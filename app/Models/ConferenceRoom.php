<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceRoom extends Model
{
    protected $fillable = [
        'room_number',
        'name',
        'pin',
        'max_participants',
        'recording_enabled',
        'wait_for_moderator',
        'moderator_pin',
        'mute_on_join',
        'enabled',
    ];

    protected $casts = [
        'max_participants' => 'integer',
        'recording_enabled' => 'boolean',
        'wait_for_moderator' => 'boolean',
        'mute_on_join' => 'boolean',
        'enabled' => 'boolean',
    ];
}
