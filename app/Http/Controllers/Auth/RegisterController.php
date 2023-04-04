<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Profile;
use App\Category;
use Carbon\Carbon;
use App\ProfileType;
use App\ProfileDescription;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Foundation\Auth\RedirectsUsers;
use App\Mail\UserRegistered as NewUserRegistered;

class RegisterController extends Controller
{
    use RedirectsUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        $this->redirectTo = '/dashboard';
    }

    /**
     * Show the application registration form.
     *
     * @param  Request  $request
     * @return Response
     */
    public function showRegistrationForm(Request $request)
    {
        return view('spa-modules.spa-projects.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest  $request
     * @return Response
     */
    public function register(RegisterRequest $request)
    {

        if ($request->type) {
            $user = new User();

            $user->forceFill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'last_read_announcements_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays(5),
                'activation_code' => str_random(50)
            ])->save();
            
            $user->emailNotifications()->create();

            $profile = ProfileType::where('value', $request->type)->first();

            $profile = Profile::create([
                'user_id' => $user->id,
                'profile_type_id' => $profile->id,
                'name' => $user->name
            ]);

            ProfileDescription::create([
                'profle_name' => 'General Profile',
                'title' => '',
                'body' => '',
                'profile_id' => $profile->id
            ]);

            $user->current_profile_id = $profile->id;
            $user->save();
    
            if ($request->type == 'freelancer' || $request->type == 'agency') {
                $categories = Category::all();
                $cats = [];
                foreach($categories as $cat) {
                    $cats[$cat->id] = ['active' => 1];
                }
    
                foreach($user->profiles as $profile) {
                    $profile->categories()->attach($cats);
                }
            }

            event(new UserRegistered($user));
            Mail::to($user->email)->queue(new NewUserRegistered($user));

            return response()->json([
                'user' => $user,
            ]);
        } else {
            return response()->json([]);
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  RegisterRequest  $request
     * @return Response
     */
    public function resend(Request $request)
    {
        if ($request->email) { 
            $user = User::where('email', $request->email)->first();

            event(new UserRegistered($user));
            Mail::to($user->email)->queue(new NewUserRegistered($user));

            return response()->json([
                'user' => $user,
            ]);
        }
    }
}
