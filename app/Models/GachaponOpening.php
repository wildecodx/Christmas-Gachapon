<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GachaponOpening extends Model
{
    protected $table = 'gachapon_openings';

    protected $fillable = [
        'machine_id',
        'participant_id',
        'gift_id',
        'opening_number',
        'opened_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_number' => 'integer',
            'opened_at' => 'datetime',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(
            GachaponMachine::class,
            'machine_id'
        );
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(
            GachaponParticipant::class,
            'participant_id'
        );
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(
            GachaponGift::class,
            'gift_id'
        );
    }
}