<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * Check if user is authenticated
     */
    public static function check()
    {
        return Auth::check();
    }

    /**
     * Get current authenticated user
     */
    public static function user()
    {
        return Auth::user();
    }

    /**
     * Get current user ID
     */
    public static function id()
    {
        return Auth::id();
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($roleSlug)
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return false;
        }
        return $user->role->slug === $roleSlug;
    }
}

