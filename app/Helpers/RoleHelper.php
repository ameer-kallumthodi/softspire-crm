<?php

namespace App\Helpers;

use App\Models\UserRole;

class RoleHelper
{
    /**
     * Get all roles
     */
    public static function getAll()
    {
        return UserRole::all();
    }

    /**
     * Get role by slug
     */
    public static function getBySlug($slug)
    {
        return UserRole::where('slug', $slug)->first();
    }

    /**
     * Get role by ID
     */
    public static function getById($id)
    {
        return UserRole::find($id);
    }

    /**
     * Check if role exists
     */
    public static function exists($slug)
    {
        return UserRole::where('slug', $slug)->exists();
    }
}

