<?php

use App\User;
use App\Profile;
use App\ProfileType;
use App\ProfileDescription;
use Illuminate\Database\Seeder;

class ProfileDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profiles = Profile::all();
        foreach($profiles as $profile) {
            if (ProfileDescription::where('profile_id', $profile->id)->count() == 0) {
                ProfileDescription::create([
                    'profle_name' => 'General Profile',
                    'title' => '',
                    'body' => '',
                    'profile_id' => $profile->id
                ]);
            }
        }
    }
}
