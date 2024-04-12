<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Seeder;

class UpdateUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = Permission::where('type', User::TYPE_ADMIN)->first();
        if (!empty($permission)) {
            $permission->children()->saveMany([
                new Permission([
                    'type' => User::TYPE_USER,
                    'name' => 'admin.to-do',
                    'description' => 'View To Do List',
                ])
            ]);
        }
    }
}
