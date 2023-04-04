<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProfileTypesSeeder::class);
        $this->call(ProofloCategoriesSeeder::class);
        $this->call(ProfileDescriptionSeeder::class);
        $this->call(ProfileTypesEditSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
