<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            'restore users',
            'view user roles',
            'assign roles to users',
            'view project user',
            'export users csv',
            'download users csv template',
            'import users from csv',

            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view role permissions',
            'edit role permissions',

            // Permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Projects
            'view any project',
            'create project',
            'update project',
            'update project status',
            'delete project',
            'view project users',
            'add project user',
            'remove project user',

            // Tasks
            'view any task',
            'view task',
            'create task',
            'update task',
            'delete task',

            // Comments
            'create comment',
            'delete comment',

            //Logs Activity User
            'view user activity logs',

            // Campaign core
            'view campaigns',
            'create campaigns',
            'update campaigns',
            'delete campaigns',
            'send campaigns',
            'restore campaigns',
            'view trashed campaigns',

            // Campaign users
            'view campaign users',
            'assign campaign users',
            'remove campaign users',

            //Email Template
            'view email templates',
            'view trashed email templates',
            'create email templates',
            'update email templates',
            'delete email templates',
            'restore email templates',

            //Check in logs
            'view check in logs',

            //Working Time
            'view working time',
            'edit working time'
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
