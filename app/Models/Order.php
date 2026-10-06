<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Tell Laravel the correct table name
    protected $table = 'orders';

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

    protected $casts = [
        'headers'           => 'array',
        'payload'           => 'array',
        'processed_payload' => 'array',
        'attachments'       => 'array',
        'processed_at'      => 'datetime',
    ];
}

