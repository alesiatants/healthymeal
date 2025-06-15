<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'register',
            'login',
            'manage profile',
            'manage users',
            'view product cost',
            'edit recipe',
            'view recipe',
            'calculate',
            'manage favorites',
            'manage system',
            'sign curse',
            'view cuses',
            'view course materials',
            'comment course',
            'comment recipe',
            'transcript',
            'edit course',
            'edit course material',
            'view students',
            'filter recipes',
            'pay',
            'show payment history'
        ];
        /*foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }*/
        $adminRole = Role::create(['name' => 'admin']);
        $dietologRole = Role::create(['name' => 'dietolog']);
        $userRole = Role::create(['name' => 'user']);
        $superadminRole = Role::create(['name' => 'superadmin']);

       /* $superadminRole->givePermissionTo(['login', 'manage profile', 'manage users', 'manage system']);
       /* $dietologRole->givePermissionTo(['register', 'login', 'manage profile', 'view recipe', 'filter recipes', 'manage favorites', 'view cuses', 'view course materials', 'comment course', 'comment recipe', 'transcript', 'show payment history', 'edit course', 'edit course material', 'view students', 'view product cost', 'calculate']);
        $userRole->givePermissionTo(['register', 'login', 'manage profile', 'view recipe', 'filter recipes', 'manage favorites', 'sign curse', 'view cuses', 'view course materials', 'comment course', 'comment recipe', 'transcript', 'pay', 'show payment history', 'view product cost', 'calculate']);*/
        
    }
}
