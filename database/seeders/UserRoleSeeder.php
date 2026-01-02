<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full system access',
                'permissions' => ['*'],
            ],
            [
                'id' => 2,
                'name' => 'Telecaller',
                'slug' => 'telecaller',
                'description' => 'Manage assigned leads',
                'permissions' => ['leads.view', 'leads.edit'],
            ],
            [
                'id' => 3,
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manage leads and users',
                'permissions' => ['leads.*', 'users.view', 'users.edit'],
            ],
            [
                'id' => 4,
                'name' => 'Employee',
                'slug' => 'employee',
                'description' => 'Employee access',
                'permissions' => ['leads.view'],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = UserRole::where('slug', $roleData['slug'])->first();
            if ($role) {
                // Update existing role
                $role->update([
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions'],
                ]);
                // Update ID if different
                if ($role->id != $roleData['id']) {
                    DB::table('user_roles')->where('id', $role->id)->update(['id' => $roleData['id']]);
                }
            } else {
                // Create new role with specific ID
                DB::table('user_roles')->insert([
                    'id' => $roleData['id'],
                    'name' => $roleData['name'],
                    'slug' => $roleData['slug'],
                    'description' => $roleData['description'],
                    'permissions' => json_encode($roleData['permissions']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
