<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GachaponGift extends Model
{
    protected $fillable = [
        'machine_id',
        'name',
        'description',
        'gift_type',
        'amount',
        'image_path',
        'quantity',
        'remaining_quantity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quantity' => 'integer',
            'remaining_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(GachaponMachine::class, 'machine_id');
    }

    public function openings(): HasMany
    {
        return $this->hasMany(GachaponOpening::class, 'gift_id');
    }
}