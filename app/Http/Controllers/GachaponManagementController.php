<?php

namespace App\Http\Controllers;

use App\Models\GachaponGift;
use App\Models\GachaponMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GachaponManagementController extends Controller
{
    /**
     * Find a machine using its private management token.
     */
private function findMachine(string $token)
{
    $tokenValid = preg_match('/\A[A-Za-z0-9]{64}\z/', $token) === 1;

    $machine = \App\Models\GachaponMachine::where(
        'management_token_hash',
        hash('sha256', $token)
    )->first();


    return $machine;
}

    /**
     * Display the private management page.
     */
    public function show(string $token)
    {
        $machine = $this->findMachine($token);

        return response()->view('gachapon.manage', [
            'managementToken' => $token,
        ])
        ->header('Referrer-Policy', 'no-referrer')
        ->header('Cache-Control', 'no-store, private');
    }

    /**
     * Return machine details and gift inventory.
     */
    public function data(string $token)
    {
        $machine = $this->findMachine($token);

        $machine->load([
            'gifts' => fn ($query) => $query
                ->orderBy('id'),
        ]);

        return response()->json([
            'machine' => [
                'id' => $machine->id,
                'name' => $machine->name,
                'godparent_role' => $machine->godparent_role,
                'godparent_name' => $machine->godparent_name,
                'welcome_message' => $machine->welcome_message,
                'status' => $machine->status,
                'number_of_godchildren' =>
                    $machine->number_of_godchildren,
                'openings_per_godchild' =>
                    $machine->openings_per_godchild,
                'opens_at' => $machine->opens_at,
                'closes_at' => $machine->closes_at,
            ],

            'gifts' => $machine->gifts->map(fn ($gift) => [
                'id' => $gift->id,
                'name' => $gift->name,
                'description' => $gift->description,
                'gift_type' => $gift->gift_type,
                'amount' => $gift->amount,
                'quantity' => $gift->quantity,
                'remaining_quantity' => $gift->remaining_quantity,
                'is_active' => (bool) $gift->is_active,
            ])->values(),

            'history' => $machine->participants()
                ->with(['openings.gift'])
                ->orderBy('id')
                ->get()
                ->map(function ($participant) {
                    $participant->ensure_verification_token();

                    return [
                        'id'        => $participant->id,
                        'name'      => $participant->name,
                        'code'      => $participant->code,
                        'prize_url' => $participant->prize_url(),
                        'prizes'    => $participant->prize_rows(),
                    ];
                })
                ->values(),
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * Add new stock without restoring already-used gifts.
     */
    public function addStock(
        Request $request,
        string $token,
        int $gift
    ) {
        $machine = $this->findMachine($token);

        $validated = $request->validate([
            'add_quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $updatedGift = DB::transaction(function () use (
            $machine,
            $gift,
            $validated
        ) {
            $giftItem = GachaponGift::where('machine_id', $machine->id)
                ->whereKey($gift)
                ->lockForUpdate()
                ->firstOrFail();

            $addition = (int) $validated['add_quantity'];

            if ((int) $giftItem->quantity + $addition > 10000) {
                abort(422, 'Total gift quantity cannot exceed 10,000.');
            }

            $giftItem->quantity =
                (int) $giftItem->quantity + $addition;

            $giftItem->remaining_quantity =
                (int) $giftItem->remaining_quantity + $addition;

            $giftItem->save();

            return $giftItem;
        });

        return response()->json([
            'message' => 'Gift stock updated successfully.',
            'gift' => [
                'id' => $updatedGift->id,
                'quantity' => $updatedGift->quantity,
                'remaining_quantity' => $updatedGift->remaining_quantity,
            ],
        ]);
    }
}
