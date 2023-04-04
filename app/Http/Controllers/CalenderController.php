<?php

namespace App\Http\Controllers;

use File;
use App\User;
use App\Proof;
use App\Project;
use Carbon\Carbon;
use App\ProjectFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        foreach ($user->onTeam as $key => $team) {
            $ownedTeams = Team::with('users', 'owner')->where('id', $team->id)->get();
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
        dd($request);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $project = Project::find($id);
            $project->start = $request->start;
            $project->end = $request->end;
            $project->save();
            return ApiResponse::success('Project saved successfully', $project);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, $id)
    {
        try {
            $project = Project::find($id);
            $project->start = null;
            $project->end = null;
            $project->save();
            return ApiResponse::success('Project saved successfully', $project);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
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
