<?php

use App\User;
use App\Profile;
use App\ProfileType;
use App\ProfileDescription;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'admin@admin.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Prooflo Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin'),
                'verified' => '1'
            ]);
            $profile = ProfileType::where('value', 'agency')->first();
            $profile = Profile::create([
                'user_id' => $user->id,
                'profile_type_id' => $profile->id,
                'name' => 'Prooflo Admin'
            ]);

            ProfileDescription::create([
                'profle_name' => 'General Profile',
                'title' => '',
                'body' => '',
                'profile_id' => $profile->id
            ]);

            $user->current_profile_id = $profile->id;
            $user->save();
        }
    }
}
