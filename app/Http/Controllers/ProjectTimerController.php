<?php

namespace App\Http\Controllers;

use App\ProjectTimer;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Contracts\IProjectTimerService;

class ProjectTimerController extends Controller
{
    private $projectTimerService;

    public function __construct(IProjectTimerService $projectTimerService)
    {
        $this->projectTimerService = $projectTimerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        try {
            return $this->projectTimerService->getProjectTimersByUser($user);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();

        try {
            $exisitTimer =  ProjectTimer::where('project_id', $request->project_id)->whereNull('end')->count();
            if ($exisitTimer == 0) {
                $data = $this->projectTimerService->storeTimer($user, $request);
                return ApiResponse::success('Timer saved successfully', $data);
            } else {
                return ApiResponse::error('001', 'Error you already started this project!');
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProjectTimer  $projectTimer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this->projectTimerService->getOneProjectTimer($id);
            return ApiResponse::success('Timer saved successfully', $data);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProjectTimer  $projectTimer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->projectTimerService->updateTimer($request, $id);
            return ApiResponse::success('Timer saved successfully', $data);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProjectTimer  $projectTimer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \Auth::user();

        try {
            $data = $this->projectTimerService->deleteTimer($id);
            return ApiResponse::success('Timer saved successfully', $data);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }
}
