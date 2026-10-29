<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GachaponMachine extends Model
{
    protected $fillable = [
        'name',
        'godparent_name',
        'welcome_message',
        'management_token_hash',
        'public_token',
        'access_code_hash',
        'status',
        'opens_at',
        'closes_at',
        'godparent_role',
        'number_of_godchildren',
        'openings_per_godchild',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'number_of_godchildren' => 'integer',
            'openings_per_godchild' => 'integer',
        ];
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(GachaponGift::class, 'machine_id');
    }

        public function openings()
    {
        return $this->hasMany(GachaponOpening::class, 'machine_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(GachaponClaim::class, 'machine_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(GachaponParticipant::class, 'machine_id');
    }

    
}
