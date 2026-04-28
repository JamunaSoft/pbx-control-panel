<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voicemail extends Model
{
    protected $fillable = [
        'mailbox',
        'context',
        'password',
        'fullname',
        'email',
        'pager',
        'email_notification',
        'language',
        'timezone',
        'delete_after_email',
        'enabled',
    ];

    protected $casts = [
        'email_notification' => 'boolean',
        'delete_after_email' => 'boolean',
        'enabled' => 'boolean',
    ];

    public function extension(): BelongsTo
    {
        return $this->belongsTo(Extension::class, 'mailbox', 'voicemail_box');
    }
}
