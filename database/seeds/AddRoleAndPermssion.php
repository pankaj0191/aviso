<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddRoleAndPermssion extends Seeder
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
            'category_files'
            // 'teams', 
            // ... // List all your Models you want to have Permissions for.
        ]);
        Permission::firstOrCreate(['group' => 'Nova', 'name' => 'Browes Nova']);

        $collection->each(function ($item, $key) {
            // create permissions for each collection item
            Permission::firstOrCreate(['group' => $item, 'name' => 'Browes ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Read ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Read own ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Create ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Update ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Update own ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Restore ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Restore own ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Delete ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'Delete own ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'ForceDelete ' . $item]);
            Permission::firstOrCreate(['group' => $item, 'name' => 'ForceDelete own ' . $item]);
        });
    }
}
