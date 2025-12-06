<?php

namespace App\Models;

use App\Models\BaseModel;

class UserRole extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Get users with this role
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}

