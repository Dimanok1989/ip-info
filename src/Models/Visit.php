<?php

namespace Kolgaev\IpInfo\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'is_blocked',
        'page',
        'method',
        'referer',
        'user_agent',
        'request_data',
        'created_at',
    ];

    /**
     * Attributes to be converted.
     *
     * @var array
     */
    protected $casts = [
        'is_blocked' => 'boolean',
        'request_data' => 'array',
    ];
}
