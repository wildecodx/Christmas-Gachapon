<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GachaponParticipant extends Model
{
    protected $fillable = [
        'machine_id',
        'name',
        'code',
        'verification_token',
        'code_hash',
        'max_openings',
        'openings_used',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_openings' => 'integer',
            'openings_used' => 'integer',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(GachaponMachine::class, 'machine_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(GachaponClaim::class, 'participant_id');
    }

    public function openings(): HasMany
    {
        return $this->hasMany(GachaponOpening::class, 'participant_id');
    }

    /**
     * @uses: Gives this godchild a private prize-page token when one is missing.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    public function ensure_verification_token(): self
    {
        if (
            is_string($this->verification_token)
            && $this->verification_token !== ''
        ) {
            return $this;
        }

        $attempt = 0;
        $token   = Str::random(48);

        while (
            static::where('verification_token', $token)->exists()
            && $attempt < 5
        ) {
            $token = Str::random(48);
            $attempt++;
        }

        $this->verification_token = $token;
        $this->save();

        return $this;
    }

    /**
     * @uses: Builds the official prize-page address for this godchild.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    public function prize_url(): ?string
    {
        if (
            !is_string($this->verification_token)
            || $this->verification_token === ''
        ) {
            return null;
        }

        return route('gachapon.prize', [
            'token' => $this->verification_token,
        ]);
    }

    /**
     * @uses: Lists the prizes this godchild has already opened, newest first.
     * @author: Jerico Nacario
     * Date: 2026-10-29
     */
    public function prize_rows(): array
    {
        $openings = $this->relationLoaded('openings')
            ? $this->openings->sortByDesc(function ($opening) {
                return $opening->opened_at?->getTimestamp() ?? 0;
            })->values()
            : $this->openings()
                ->with('gift')
                ->orderByDesc('opened_at')
                ->orderByDesc('id')
                ->get();

        $rows = [];

        foreach ($openings as $opening) {
            $gift   = $opening->gift;
            $amount = null;

            if ($gift && $gift->gift_type === 'money' && $gift->amount !== null) {
                $amount = (float) $gift->amount;
            }

            $rows[] = [
                'opening_number' => (int) $opening->opening_number,
                'opened_at'      => $opening->opened_at?->toIso8601String(),
                'opened_label'   => $opening->opened_at
                    ?->timezone(config('app.timezone'))
                    ->format('M j, Y g:i A'),
                'name'           => $gift?->name ?? 'A Christmas Surprise',
                'description'    => $gift?->description,
                'gift_type'      => $gift?->gift_type ?? 'custom',
                'amount'         => $amount,
            ];
        }

        return $rows;
    }
}
