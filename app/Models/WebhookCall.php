<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookCall extends Model
{
    protected $table = 'webhook_calls';

    protected $casts = [
        'headers'            => 'array',
        'payload'            => 'array',
        'processed_payload'  => 'array',
        'attachments'        => 'array',
        'processed_at'       => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    protected $fillable = [
        'name',
        'url',
        'headers',
        'payload',
        'processed_payload',
        'processed_at',
        'status',
        'attachments',
        'exception',
    ];
}
