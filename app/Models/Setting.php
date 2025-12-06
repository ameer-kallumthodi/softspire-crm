<?php

namespace App\Models;

use App\Models\BaseModel;

class Setting extends BaseModel
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected $casts = [
        'value' => 'string',
    ];
}

