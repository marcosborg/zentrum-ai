<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'assistant_create',
            ],
            [
                'id'    => 18,
                'title' => 'assistant_edit',
            ],
            [
                'id'    => 19,
                'title' => 'assistant_show',
            ],
            [
                'id'    => 20,
                'title' => 'assistant_delete',
            ],
            [
                'id'    => 21,
                'title' => 'assistant_access',
            ],
            [
                'id'    => 22,
                'title' => 'openai_create',
            ],
            [
                'id'    => 23,
                'title' => 'openai_edit',
            ],
            [
                'id'    => 24,
                'title' => 'openai_show',
            ],
            [
                'id'    => 25,
                'title' => 'openai_delete',
            ],
            [
                'id'    => 26,
                'title' => 'openai_access',
            ],
            [
                'id'    => 27,
                'title' => 'instruction_create',
            ],
            [
                'id'    => 28,
                'title' => 'instruction_edit',
            ],
            [
                'id'    => 29,
                'title' => 'instruction_show',
            ],
            [
                'id'    => 30,
                'title' => 'instruction_delete',
            ],
            [
                'id'    => 31,
                'title' => 'instruction_access',
            ],
            [
                'id'    => 32,
                'title' => 'training_create',
            ],
            [
                'id'    => 33,
                'title' => 'training_edit',
            ],
            [
                'id'    => 34,
                'title' => 'training_show',
            ],
            [
                'id'    => 35,
                'title' => 'training_delete',
            ],
            [
                'id'    => 36,
                'title' => 'training_access',
            ],
            [
                'id'    => 37,
                'title' => 'project_create',
            ],
            [
                'id'    => 38,
                'title' => 'project_edit',
            ],
            [
                'id'    => 39,
                'title' => 'project_show',
            ],
            [
                'id'    => 40,
                'title' => 'project_delete',
            ],
            [
                'id'    => 41,
                'title' => 'project_access',
            ],
            [
                'id'    => 42,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
