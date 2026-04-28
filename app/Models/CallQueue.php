<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CallQueue extends Model
{
    protected $table = 'queues';

    protected $fillable = [
        'queue_name',
        'strategy',
        'timeout',
        'wrapuptime',
        'maxlen',
        'announce',
        'context',
        'enabled',
        'servicelevel',
        'musicclass',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'timeout' => 'integer',
        'wrapuptime' => 'integer',
        'maxlen' => 'integer',
        'servicelevel' => 'integer',
    ];

    public function extensions(): BelongsToMany
    {
        return $this->belongsToMany(Extension::class, 'queue_member', 'queue_id', 'extension_id');
    }
}
