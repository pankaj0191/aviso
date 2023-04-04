<?php

namespace App\Http\Controllers;

use App\ProjectTimer;
use App\ProfileDescription;
use Illuminate\Http\Request;
use App\Mail\InvoiceHourlyRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProjectTimer::with(['project.usersHourlyRate' => function ($q) {
            $q->where('id', Auth::user()->id);
        }, 'user'])->where('user_id', Auth::user()->id)->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ProjectTimer::with(['project.usersHourlyRate' => function ($q) {
            $q->where('id', Auth::user()->id);
        }, 'user'])->where('id', $id)->first();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendMail($id)
    {
        $projectTimer = ProjectTimer::with('project.users', 'user')->where('id', $id)->first();
        return Mail::to(Auth::user()->email)->send(new InvoiceHourlyRate($projectTimer, Auth::user()->email));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendMailInvoices(Request $request)
    {
        $projectTimer = ProjectTimer::with('project.users', 'user')->where('user_id', Auth::user()->id)->get();
        return Mail::to(Auth::user()->email)->send(new InvoiceHourlyRate($projectTimer, Auth::user()->email));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeLogo(Request $request)
    {
        $user = $request->user();
        if ($request->hasFile('logo')) {
            $profileDesc = ProfileDescription::where('profile_id', $user->current_profile_id)->first();
            Storage::disk('spaces')->delete($profileDesc->logo);
            $profileDesc->dark_logo = $request->file('logo')->store('assets/img', 'spaces');
            $profileDesc->save();
        }
        return $profileDesc;
    }
}
