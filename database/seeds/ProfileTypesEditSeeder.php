<?php

use App\ProfileType;
use Illuminate\Database\Seeder;

class ProfileTypesEditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProfileType::updateOrCreate(['value' => 'collaborator'], [
            'name' => 'Collaborator',
        ]);
    }
}
