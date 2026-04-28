<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trunk extends Model
{
    protected $fillable = [
        'trunk_name',
        'provider',
        'host',
        'username',
        'secret',
        'context',
        'status',
        'type',
        'port',
        'failover_enabled',
        'failover_trunks',
        'cost_per_minute',
    ];

    protected $casts = [
        'failover_enabled' => 'boolean',
        'failover_trunks' => 'array',
        'cost_per_minute' => 'decimal:4',
    ];

    public function cdrs(): HasMany
    {
        return $this->hasMany(Cdr::class, 'channel')->where('channel', 'like', '%'.$this->trunk_name.'%');
    }
}
