<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'id' => 1,
                'name' => 'Developers',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Graphic Designer',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'Digital Marketing',
                'status' => 'active',
            ],
        ];

        foreach ($departments as $departmentData) {
            $department = Department::where('name', $departmentData['name'])->first();
            if ($department) {
                $department->update([
                    'status' => $departmentData['status'],
                ]);
                if ($department->id != $departmentData['id']) {
                    DB::table('departments')->where('id', $department->id)->update(['id' => $departmentData['id']]);
                }
            } else {
                DB::table('departments')->insert([
                    'id' => $departmentData['id'],
                    'name' => $departmentData['name'],
                    'status' => $departmentData['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
