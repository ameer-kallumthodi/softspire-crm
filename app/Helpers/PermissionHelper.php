<?php

namespace App\Helpers;

use App\Helpers\AuthHelper;

class PermissionHelper
{
    /**
     * Check if user has permission
     */
    public static function has($permission)
    {
        $user = AuthHelper::user();
        if (!$user || !$user->role) {
            return false;
        }

        $permissions = $user->role->permissions ?? [];
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Check if user has any of the permissions
     */
    public static function hasAny(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (self::has($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all permissions
     */
    public static function hasAll(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!self::has($permission)) {
                return false;
            }
        }
        return true;
    }
}

