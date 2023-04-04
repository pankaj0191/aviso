<?php

namespace App\Http\Controllers;

use App\Team;
use App\User;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MyUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = [];
        $user = Auth::user();
        $isFreelancer = isFreelancer($user);
        $isAgency = isAgency($user);
        $isClient = isClient($user);
        $userProject = User::where('id', $user->id)->with('projects')->first();

        if ($isFreelancer || $isAgency) {
            foreach ($userProject->projects as $key => $project) {
                $clientRole = DB::table('project_user')->where('project_id', $project->id)->where('role', 'client')->first();

                if ($clientRole) {
                    $user = User::find($clientRole->user_id);
                    if ($user) {
                        $users[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'photo_url' => $user->photo_url,
                            // 'company' => $project->company,
                            'website' => $project->website_url,
                        ];
                    }
                }

            }
        } else if ($isClient) {
            foreach ($userProject->projects as $key => $project) {

                $user = $project->users()->where('user_id', $project->created_by)->first();
                if (isSubscribed($user)) {
                    $users[$key] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'photo_url' => $user->photo_url,
                        // 'company' => Project::where('created_by', $user->id)->first()->company,
                    ];
                }
            }
        }

        return response()->json($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function teams()
    {
        $ownedTeams = [];

        $user = Auth::user();

        foreach (Team::all() as $key => $team) {
            if ($user->onTeam($team)) {
                $ownedTeams[] = Team::with('users', 'owner')->where('id', $team->id)->first();
            }
        }

        return $ownedTeams;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
