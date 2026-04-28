<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallRoute extends Model
{
    protected $fillable = [
        'name',
        'pattern',
        'destination_type',
        'destination_value',
        'priority',
        'context',
        'enabled',
        'time_conditions',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'priority' => 'integer',
        'time_conditions' => 'array',
    ];
}
