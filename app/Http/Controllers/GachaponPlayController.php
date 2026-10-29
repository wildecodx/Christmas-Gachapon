<?php

namespace App\Http\Controllers;

use App\Models\GachaponGift;
use App\Models\GachaponMachine;
use App\Models\GachaponOpening;
use App\Models\GachaponParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GachaponPlayController extends Controller
{
    public function show(string $token): View
    {
        $machine = GachaponMachine::where('public_token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        $godchild_code = $this->issue_godchild_code($machine);

        return view('gachapon.play', [
            'machine'       => $machine,
            'godchild_code' => $godchild_code,
        ]);
    }

    public function machine(string $token): View
    {
        $machine = GachaponMachine::where('public_token', $token)
            ->where('status', 'active')
            ->firstOrFail();

        $participant        = $this->verified_participant($machine);
        $openingsRemaining  = null;
        $prize_history      = [];
        $godchild_code      = null;
        $prize_url          = null;

        if ($participant) {
            $participant->ensure_verification_token();
            $participant->load(['openings.gift']);

            $openingsRemaining = $participant->max_openings === null
                ? null
                : max(0, (int) $participant->max_openings - (int) $participant->openings_used);
            $prize_history = $participant->prize_rows();
            $godchild_code = $participant->code;
            $prize_url     = $participant->prize_url();
        }

        return view('gachapon.machine', compact(
            'machine',
            'participant',
            'openingsRemaining',
            'prize_history',
            'godchild_code',
            'prize_url'
        ));
    }

    /**
     * @uses: Shows the official prize record for one godchild.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    public function prize(string $token)
    {
        $participant = GachaponParticipant::where('verification_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $participant->load(['machine', 'openings.gift']);

        $prize_history = $participant->prize_rows();
        $machine       = $participant->machine;

        return response()
            ->view('gachapon.prize', compact(
                'participant',
                'machine',
                'prize_history'
            ))
            ->header('Cache-Control', 'no-store, private');
    }

    /**
     * @uses: Checks the special code and unlocks the gachapon machine for this browser.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    public function verifyCode(Request $request, string $token): JsonResponse
    {
        $rs = [
            'code'  => 0,
            'title' => 'Ooops!',
            'msg'   => 'Something went wrong.',
        ];

        $machine = GachaponMachine::where('public_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$machine) {
            $rs['msg'] = 'This Christmas surprise could not be found.';

            return response()->json($rs, 404);
        }

        $raw_code = $request->input('code', '');

        if (!is_string($raw_code)) {
            $rs['msg'] = 'Enter the special code from your letter.';

            return response()->json($rs, 422);
        }

        $normalized_code = $this->normalize_code($raw_code);

        if ($normalized_code === '') {
            $rs['msg'] = 'Enter the special code from your letter.';

            return response()->json($rs, 422);
        }

        $participant = GachaponParticipant::where('machine_id', $machine->id)
            ->where('code_hash', hash('sha256', $normalized_code))
            ->where('is_active', true)
            ->first();

        if (!$participant) {
            $rs['msg'] = 'That code does not match this Christmas surprise.';

            return response()->json($rs, 422);
        }

        session([
            'gachapon_participant_id'    => $participant->id,
            'gachapon_machine_id'        => $machine->id,
            'gachapon_display_code'       => $normalized_code,
            'gachapon_display_machine_id' => $machine->id,
        ]);

        $openings_remaining = $participant->max_openings === null
            ? null
            : max(0, (int) $participant->max_openings - (int) $participant->openings_used);

        $rs['code']  = 1;
        $rs['title'] = 'Code accepted';
        $rs['msg']   = 'Your code is ready. You can open a capsule.';
        $rs['data']  = [
            'participant_name'   => $participant->name,
            'openings_remaining' => $openings_remaining,
        ];

        return response()->json($rs);
    }

    /**
     * @uses: Draws one remaining gift at random for the verified godchild.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    public function open(string $token): JsonResponse
    {
        $rs = [
            'code'  => 0,
            'title' => 'Ooops!',
            'msg'   => 'Something went wrong.',
        ];

        $machine = GachaponMachine::where('public_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$machine) {
            $rs['msg'] = 'This Christmas surprise could not be found.';

            return response()->json($rs, 404);
        }

        $participant = $this->verified_participant($machine);

        if (!$participant) {
            $rs['msg'] = 'Enter your special code before opening a capsule.';

            return response()->json($rs, 403);
        }

        $draw = $this->draw_random_gift($machine, $participant);

        if ($draw['code'] !== 1) {
            $rs['title'] = $draw['title'];
            $rs['msg']   = $draw['msg'];

            return response()->json($rs, 422);
        }

        $rs['code']  = 1;
        $rs['title'] = 'Your gift!';
        $rs['msg']   = 'Congratulations! Your surprise has been revealed!';
        $rs['data']  = $draw['data'];

        return response()->json($rs);
    }

    /**
     * @uses: Issues one special code for this browser, or reuses the code already given to it.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function issue_godchild_code(GachaponMachine $machine): ?string
    {
        $existing_code = $this->current_display_code($machine);

        if ($existing_code !== null) {
            return $existing_code;
        }

        return DB::transaction(function () use ($machine) {
            $locked_machine = GachaponMachine::whereKey($machine->id)
                ->lockForUpdate()
                ->first();

            if (!$locked_machine || $locked_machine->status !== 'active') {
                return null;
            }

            $godchild_count = GachaponParticipant::where('machine_id', $locked_machine->id)
                ->count();
            $allowed_count  = (int) $locked_machine->number_of_godchildren;

            if ($godchild_count >= $allowed_count) {
                return null;
            }

            $plain_code = $this->make_godchild_code();
            $code_hash  = hash('sha256', $plain_code);
            $attempt    = 0;

            while (
                GachaponParticipant::where('code_hash', $code_hash)->exists()
                && $attempt < 8
            ) {
                $plain_code = $this->make_godchild_code();
                $code_hash  = hash('sha256', $plain_code);
                $attempt++;
            }

            if (GachaponParticipant::where('code_hash', $code_hash)->exists()) {
                return null;
            }

            GachaponParticipant::create([
                'machine_id'          => $locked_machine->id,
                'name'                => 'Godchild ' . ($godchild_count + 1),
                'code'                => $plain_code,
                'verification_token'  => Str::random(48),
                'code_hash'           => $code_hash,
                'max_openings'        => (int) $locked_machine->openings_per_godchild,
                'openings_used'       => 0,
                'is_active'           => true,
            ]);

            session([
                'gachapon_display_code'       => $plain_code,
                'gachapon_display_machine_id' => $locked_machine->id,
            ]);

            return $plain_code;
        });
    }

    /**
     * @uses: Returns the special code already stored for this browser and machine.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function current_display_code(GachaponMachine $machine): ?string
    {
        $stored_code       = session('gachapon_display_code');
        $stored_machine_id = session('gachapon_display_machine_id');

        if ((int) $stored_machine_id !== (int) $machine->id) {
            return null;
        }

        if (!is_string($stored_code) || $stored_code === '') {
            return null;
        }

        $participant = GachaponParticipant::where('machine_id', $machine->id)
            ->where('code_hash', hash('sha256', $stored_code))
            ->where('is_active', true)
            ->first();

        if (!$participant) {
            return null;
        }

        return $stored_code;
    }

    /**
     * @uses: Returns the godchild unlocked in this browser for the given machine.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function verified_participant(GachaponMachine $machine): ?GachaponParticipant
    {
        $participant_id     = session('gachapon_participant_id');
        $session_machine_id = session('gachapon_machine_id');

        if (!$participant_id || (int) $session_machine_id !== (int) $machine->id) {
            return null;
        }

        $participant = GachaponParticipant::where('id', $participant_id)
            ->where('machine_id', $machine->id)
            ->where('is_active', true)
            ->first();

        if (!$participant) {
            session()->forget([
                'gachapon_participant_id',
                'gachapon_machine_id',
            ]);

            return null;
        }

        return $participant;
    }

    /**
     * @uses: Picks one in-stock gift, with larger remaining stock more likely to be drawn.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function draw_random_gift(
        GachaponMachine $machine,
        GachaponParticipant $participant
    ): array {
        $rs = [
            'code'  => 0,
            'title' => 'Ooops!',
            'msg'   => 'Something went wrong.',
        ];

        $draw = DB::transaction(function () use ($machine, $participant) {
            $locked_participant = GachaponParticipant::where('id', $participant->id)
                ->where('machine_id', $machine->id)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$locked_participant) {
                return [
                    'code'  => 0,
                    'title' => 'Ooops!',
                    'msg'   => 'Enter your special code before opening a capsule.',
                ];
            }

            $max_openings  = $locked_participant->max_openings;
            $openings_used = (int) $locked_participant->openings_used;

            if ($max_openings !== null && $openings_used >= (int) $max_openings) {
                return [
                    'code'  => 0,
                    'title' => 'All openings used',
                    'msg'   => 'You have used all your openings.',
                ];
            }

            $gifts = GachaponGift::where('machine_id', $machine->id)
                ->where('is_active', true)
                ->where('remaining_quantity', '>', 0)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $total_stock = (int) $gifts->sum('remaining_quantity');

            if ($total_stock < 1) {
                return [
                    'code'  => 0,
                    'title' => 'No gifts left',
                    'msg'   => 'Every capsule has already been opened.',
                ];
            }

            $roll          = random_int(1, $total_stock);
            $cursor        = 0;
            $selected_gift = null;

            foreach ($gifts as $gift) {
                $cursor += (int) $gift->remaining_quantity;

                if ($roll <= $cursor) {
                    $selected_gift = $gift;
                    break;
                }
            }

            if (!$selected_gift) {
                return [
                    'code'  => 0,
                    'title' => 'Ooops!',
                    'msg'   => 'A gift could not be chosen. Please try again.',
                ];
            }

            $opening_number = $openings_used + 1;
            $opened_at      = now();

            $selected_gift->remaining_quantity = (int) $selected_gift->remaining_quantity - 1;
            $selected_gift->save();

            $locked_participant->openings_used = $opening_number;
            $locked_participant->save();

            GachaponOpening::create([
                'machine_id'      => $machine->id,
                'participant_id'  => $locked_participant->id,
                'gift_id'         => $selected_gift->id,
                'opening_number'  => $opening_number,
                'opened_at'       => $opened_at,
            ]);

            $openings_remaining = $max_openings === null
                ? null
                : max(0, (int) $max_openings - $opening_number);

            $amount = null;

            if ($selected_gift->gift_type === 'money' && $selected_gift->amount !== null) {
                $amount = (float) $selected_gift->amount;
            }

            return [
                'code'  => 1,
                'title' => 'Your gift!',
                'msg'   => 'Congratulations! Your surprise has been revealed!',
                'data'  => [
                    'gift' => [
                        'name'        => $selected_gift->name,
                        'description' => $selected_gift->description,
                        'gift_type'   => $selected_gift->gift_type,
                        'amount'      => $amount,
                        'image_url'   => $this->gift_image_url($selected_gift->image_path),
                    ],
                    'openings_remaining' => $openings_remaining,
                    'opening_number'     => $opening_number,
                    'opened_label'       => $opened_at
                        ->timezone(config('app.timezone'))
                        ->format('M j, Y g:i A'),
                ],
            ];
        });

        if (!is_array($draw)) {
            return $rs;
        }

        return $draw;
    }

    /**
     * @uses: Builds a short special code the godchild can read and type.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function make_godchild_code(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $chars    = '';
        $length   = strlen($alphabet) - 1;

        for ($index = 0; $index < 4; $index++) {
            $chars .= $alphabet[random_int(0, $length)];
        }

        return 'GIFT-' . $chars;
    }

    /**
     * @uses: Trims and uppercases a special code before it is checked.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function normalize_code(string $code): string
    {
        $normalized = strtoupper(trim($code));
        $normalized = preg_replace('/\s+/', '', $normalized);

        if (!is_string($normalized)) {
            return '';
        }

        return $normalized;
    }

    /**
     * @uses: Turns a stored gift image path into a public URL.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    private function gift_image_url(?string $image_path): ?string
    {
        if ($image_path === null || $image_path === '') {
            return null;
        }

        if (
            str_starts_with($image_path, 'http://')
            || str_starts_with($image_path, 'https://')
        ) {
            return $image_path;
        }

        return asset('storage/' . ltrim($image_path, '/'));
    }
}
