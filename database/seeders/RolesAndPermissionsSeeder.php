<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view_users', 'add_users', 'edit_users', 'delete_users', 'view_loans', 'change_loans'];
        $permissions = collect($permissions)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'api'];
        });

        // create permissions
        Permission::insert($permissions->toArray());

        // create roles and assign created permissions
        $role = Role::create(['name' => 'customer']);
        $role->givePermissionTo(['view_loans']);

        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo(['view_users', 'view_loans', 'change_loans']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }
}