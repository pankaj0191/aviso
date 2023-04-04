<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the settings dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('spark::settings');
    }
}
