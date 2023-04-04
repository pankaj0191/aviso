<?php

use App\User;
use App\Category;
use Illuminate\Database\Seeder;

class ProofloCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::firstOrCreate([
            'name' => 'Website Design',
            'group' => 'Website Design',
            'slug' => 'website',
            'active' => 1,
            'image' => 'categories/image/YeWXJiYbQj2DHDDInKI2elExKyFNUlksRce0bdBq.svg',
        ]);

        Category::firstOrCreate([
            'name' => 'Graphic Design',
            'group' => 'Graphic Design',
            'slug' => 'design',
            'active' => 1,
            'image' => 'categories/image/JlYs0ayFukjvTwYdX3zRMFdayGfnAvuMumhe3T1r.svg',
        ]);

        Category::firstOrCreate([
            'name' => 'Video Production',
            'group' => 'Video Production',
            'slug' => 'video',
            'active' => 1,
            'image' => 'categories/image/7CwR9OuILZEFC9jMHXDLsp55cLgP5jVRis0K86ub.svg',
        ]);

        Category::firstOrCreate([
            'name' => 'Audio',
            'group' => 'Audio',
            'slug' => 'audio',
            'active' => 1,
            'image' => 'categories/image/4bWnotEt1GWNsn6YJsqD5B2YtrZ45a7taTgXtWgA.svg',
        ]);

        $users = User::with(['profiles' => function($q) {
            return $q->where('profile_type_id', 3)->orWhere('profile_type_id', 2);
        }])->whereHas('profiles.profileType', function($q) {
            return $q->where('value', 'agency')->orWhere('value', 'freelancer');
        })->get();

        $categories = Category::all();
        $cats = [];
        foreach($categories as $cat) {
            $cats[$cat->id] = ['active' => 1];
        }
        foreach($users as $user) {
            foreach($user->profiles as $profile) {
                $profile->categories()->detach();
                $profile->categories()->attach($cats);
            }
        }
    }
}
