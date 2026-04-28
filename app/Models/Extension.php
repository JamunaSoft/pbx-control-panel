<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Extension extends Model
{
    protected $fillable = [
        'extension_number',
        'password',
        'display_name',
        'email',
        'status',
        'device_type',
        'context',
        'call_forwarding_enabled',
        'call_forwarding_number',
        'dnd_enabled',
        'voicemail_enabled',
        'voicemail_box',
        'ring_group',
        'follow_me_numbers',
    ];

    protected $casts = [
        'call_forwarding_enabled' => 'boolean',
        'dnd_enabled' => 'boolean',
        'voicemail_enabled' => 'boolean',
        'follow_me_numbers' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_extension');
    }

    public function queues(): BelongsToMany
    {
        return $this->belongsToMany(CallQueue::class, 'queue_member');
    }

    public function cdrs(): HasMany
    {
        return $this->hasMany(Cdr::class, 'src', 'extension_number');
    }

    public function voicemail(): HasOne
    {
        return $this->hasOne(Voicemail::class, 'mailbox', 'voicemail_box');
    }
}
