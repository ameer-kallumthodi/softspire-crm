<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = UserRole::where('slug', 'admin')->first();

        if ($adminRole) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@crm.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]);
        }
    }
}
