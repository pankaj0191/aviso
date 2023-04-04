<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $collection = collect([
            'users',
            'roles',
            'permissions',
            'announcements',
            'plans',
            'invoices',
            'categories',
            'sub_categories',
            'category_files',
            // 'teams', 
            // ... // List all your Models you want to have Permissions for.
        ]);
        Permission::create(['group' => 'Nova', 'name' => 'Browes Nova']);

        $collection->each(function ($item, $key) {
            // create permissions for each collection item
            Permission::create(['group' => $item, 'name' => 'Browes ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Read ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Read own ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Create ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Update ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Update own ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Restore ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Restore own ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Delete ' . $item]);
            Permission::create(['group' => $item, 'name' => 'Delete own ' . $item]);
            Permission::create(['group' => $item, 'name' => 'ForceDelete ' . $item]);
            Permission::create(['group' => $item, 'name' => 'ForceDelete own ' . $item]);
        });

        // Create a Super-Admin Role and assign all Permissions
        // Create a Super-Admin Role and assign all Permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
        $role = Role::create(['name' => 'editor']);

        // Give User Super-Admin Role
        $user = User::whereEmail('admin@admin.com')->count(); // Change this to your email.

        if ($user > 0) {
            $user = User::whereEmail('admin@admin.com')->first(); // Change this to your email.
            $user->assignRole('admin');
        }
    }
}
