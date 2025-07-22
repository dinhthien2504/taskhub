<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'super admin']);

        $admin->givePermissionTo(Permission::all());

        $user = User::firstOrCreate(
            ['email' => 'laptrinh05.net@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('@Password123'),
                'active' => 1,
            ]
        );

        if (!$user->hasRole('super admin')) {
            $user->assignRole('super admin');
        }
    }
}
