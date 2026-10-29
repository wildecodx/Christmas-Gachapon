<?php

namespace App\Http\Controllers;

use App\Models\GachaponMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GachaponSetupController extends Controller
{
    public function create()
    {
        return view('gachapon.setup');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'godparent_role' => [
                'required',
                'in:godfather,godmother',
            ],

        'gifts.*.gift_type' => ['required', 'in:money,custom'],
            'number_of_gifts' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],

            'number_of_godchildren' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'openings_per_godchild' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],

            'gifts' => [
                'required',
                'array',
            ],

            'gifts.*.name' => [
                'nullable',
                'string',
                'max:191',
            ],

            'gifts.*.description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'gifts.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],

            'gifts.*.amount' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:99999999.99',
            ],
        ]);

        // Ensure the number of submitted gift items matches the form.
        if (count($data['gifts']) !== (int) $data['number_of_gifts']) {
            throw ValidationException::withMessages([
                'number_of_gifts' =>
                    'The number of gift items does not match the gift details.',
            ]);
        }

        // Calculate the total number of allowed gift openings.
        $totalOpenings =
            (int) $data['number_of_godchildren']
            * (int) $data['openings_per_godchild'];

        // All godchildren share the same gift inventory.
        $totalStock = collect($data['gifts'])
            ->sum(fn ($gift) => (int) $gift['quantity']);

        if ($totalStock < $totalOpenings) {
            throw ValidationException::withMessages([
                'gifts' => "You need at least {$totalOpenings} gifts in total. "
                    . "Your current stock is {$totalStock}.",
            ]);
        }

        // Validate gift-specific fields.
        foreach ($data['gifts'] as $index => $gift) {
            if ($gift['gift_type'] === 'money') {
                if (
                    !isset($gift['amount'])
                    || !is_numeric($gift['amount'])
                    || (float) $gift['amount'] <= 0
                ) {
                    throw ValidationException::withMessages([
                        "gifts.{$index}.amount" =>
                            'Enter a valid amount for each money gift.',
                    ]);
                }
            } elseif (empty(trim($gift['name'] ?? ''))) {
                throw ValidationException::withMessages([
                    "gifts.{$index}.name" =>
                        'Enter a name for each custom gift.',
                ]);
            }
        }

        // Generate secret management and public machine tokens.
        $managementToken = Str::random(64);
        $publicToken = Str::random(48);

        $machine = DB::transaction(function () use (
            $data,
            $managementToken,
            $publicToken
        ) {
            $machine = GachaponMachine::create([
                'name' => 'Christmas Gachapon',
                'godparent_role' => $data['godparent_role'],
                'number_of_godchildren' =>
                    (int) $data['number_of_godchildren'],
                'openings_per_godchild' =>
                    (int) $data['openings_per_godchild'],

                // Store only the hash of the private management token.
                'management_token_hash' =>
                    hash('sha256', $managementToken),

                'public_token' => $publicToken,

                // Preserves compatibility with the existing required column.
                // Actual godchild codes will use participant records.
                'access_code_hash' => Hash::make(Str::random(64)),

                'status' => 'active',
            ]);

            foreach ($data['gifts'] as $index => $gift) {
                $machine->gifts()->create([
                    'name' => $gift['gift_type'] === 'money'
                        ? 'Money Gift ' . ($index + 1)
                        : trim($gift['name']),

                    'description' => $gift['description'] ?? null,

                    'gift_type' => $gift['gift_type'],

                    'amount' => $gift['gift_type'] === 'money'
                        ? $gift['amount']
                        : null,

                    'quantity' => (int) $gift['quantity'],

                    'remaining_quantity' => (int) $gift['quantity'],

                    'is_active' => true,
                ]);
            }

            return $machine;
        });

        return response()->json([
            'message' => 'Your Christmas Gachapon has been created!',

            'management_url' => url('/manage/' . $managementToken),

            'public_url' => url('/play/' . $machine->public_token),

            'number_of_godchildren' =>
                $machine->number_of_godchildren,

            'openings_per_godchild' =>
                $machine->openings_per_godchild,

            'total_openings' => $totalOpenings,
        ], 201);
    }
}
