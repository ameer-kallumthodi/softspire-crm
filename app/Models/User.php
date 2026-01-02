<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'country_code',
        'phone',
        'joining_date',
        'dob',
        'employee_id',
        'department_id',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'date',
            'dob' => 'date',
        ];
    }

    /**
     * Get the user's role
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /**
     * Get the user's department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
