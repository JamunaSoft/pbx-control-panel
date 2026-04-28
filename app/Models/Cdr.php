<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    protected $table = 'cdr';

    public $timestamps = false;

    protected $fillable = [
        'accountcode',
        'src',
        'dst',
        'dcontext',
        'clid',
        'channel',
        'dstchannel',
        'lastapp',
        'lastdata',
        'start',
        'answer',
        'end',
        'duration',
        'billsec',
        'disposition',
        'amaflags',
        'uniqueid',
        'linkedid',
        'peeraccount',
        'sequence',
    ];

    protected $casts = [
        'start' => 'datetime',
        'answer' => 'datetime',
        'end' => 'datetime',
        'duration' => 'integer',
        'billsec' => 'integer',
    ];

    public function getCallerExtensionAttribute()
    {
        return Extension::where('extension_number', $this->src)->first();
    }

    public function getCalleeExtensionAttribute()
    {
        return Extension::where('extension_number', $this->dst)->first();
    }
}
