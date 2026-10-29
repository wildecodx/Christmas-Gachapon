<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GachaponClaim extends Model
{
    protected $fillable = [
        'machine_id',
        'gift_id',
        'participant_id',
        'claim_reference',
        'revealed_at',
    ];

    protected function casts(): array
    {
        return [
            'revealed_at' => 'datetime',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(GachaponMachine::class, 'machine_id');
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(GachaponGift::class, 'gift_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(GachaponParticipant::class, 'participant_id');
    }
}
