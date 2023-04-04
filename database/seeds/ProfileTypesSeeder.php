<?php

use App\ProfileType;
use Illuminate\Database\Seeder;

class ProfileTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProfileType::updateOrCreate(['value' => 'client'],[
            'name' => 'Client',
        ]);
        ProfileType::updateOrCreate(['value' => 'freelancer'],[
            'name' => 'Freelancer',
        ]);
        ProfileType::updateOrCreate(['value' => 'agency'],[
            'name' => 'Agency',
            'value' => 'agency',
        ]);
        ProfileType::updateOrCreate(['value' => 'collaborator'], [
            'name' => 'Collaborator',
        ]);
    }
}
