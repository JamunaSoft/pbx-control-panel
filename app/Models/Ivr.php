<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ivr extends Model
{
    protected $fillable = [
        'name',
        'greeting_audio',
        'timeout_action',
        'timeout_seconds',
        'menu_options',
        'invalid_input_action',
        'max_attempts',
        'enabled',
    ];

    protected $casts = [
        'menu_options' => 'array',
        'enabled' => 'boolean',
        'timeout_seconds' => 'integer',
        'max_attempts' => 'integer',
    ];
}
