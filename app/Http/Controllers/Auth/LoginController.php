<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        AuthenticatesUsers::login as traitLogin;
    }

    /**
     * Create a new login controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->redirectTo = 'dashboard';
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('spa-modules.spa-projects.auth.login');
    }

    /**
     * {@inheritdoc}
     */
    public function login(Request $request)
    {
        if ($request->filled('remember')) {
            $request->session()->put('auth-remember', $request->remember);
        }

        return $this->traitLogin($request);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard()->logout();

        session()->flush();

        return redirect(
            property_exists($this, 'redirectAfterLogout')
                ? $this->redirectAfterLogout : '/'
        );
    }
}
