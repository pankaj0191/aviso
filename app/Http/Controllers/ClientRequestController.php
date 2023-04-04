<?php

namespace App\Http\Controllers;

use App\CategoryFile;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProjectRequest;
use Illuminate\Support\Facades\Storage;
use App\Contracts\IClientRequestService;

class ClientRequestController extends Controller
{
    private $clientRequestService;

    public function __construct(IClientRequestService $clientRequestService)
    {
        $this->clientRequestService = $clientRequestService;
    }

    public function freelancers()
    {
        $user = Auth::user();

        try {
            return $this->clientRequestService->getFreelancersByUser($user);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function workers()
    {
        $user = Auth::user();

        try {
            return $this->clientRequestService->getWrokers($user);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function create(Request $request)
    {
        try {
            return $this->clientRequestService->fetchRequest($request->all());
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function getFiles($projectID)
    {
        try {
            $result =  $this->clientRequestService->getFiles($projectID);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error getting project final files');
        }
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $result = $this->clientRequestService->createProject($data);
            DB::commit();
            if ($result) {
                $data = [
                    'project_id' => $result['project']->id,
                    'revision_id' => $result['project']->getActiveRevision($result['project']->id)->id,
                    'user_id' => Auth::user()->id,
                    'newClient' => $result['newClient'],
                    'client' => $result['client']
                ];
                return ApiResponse::success('Project saved successfully', $data);
            } else {
                return ApiResponse::error('001', 'This email address is associated with your freelancer account in this project. Please use a different client email address');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    public function instruction(Request $request)
    {
        try {
            $result = $this->clientRequestService->createInstruction($request);
            return ApiResponse::success('Instruction saved successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    public function updateName(Request $request)
    {
        try {
            $result = $this->clientRequestService->updateName($request);
            return ApiResponse::success('Instruction saved successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    public function dimension(Request $request)
    {
        // try {
            $result = $this->clientRequestService->createDimension($request);
            return ApiResponse::success('Dimension saved successfully', $result);
        // } catch (\Exception $e) {
        //     report($e);
        //     return ApiResponse::error('001', 'Error occurred. Please try again later');
        // }
    }
    public function description(Request $request)
    {
        // try {
            $result = $this->clientRequestService->createDescription($request);
            return ApiResponse::success('Description saved successfully', $result);
        // } catch (\Exception $e) {
        //     report($e);
        //     return ApiResponse::error('001', 'Error occurred. Please try again later');
        // }
    }
    public function summary(Request $request)
    {
        try {
            $result = $this->clientRequestService->createSummary($request);
            return ApiResponse::success('Summary saved successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }
    public function draft(Request $request)
    {
        // try {
            $result = $this->clientRequestService->draft($request);
            return ApiResponse::success('Draft saved successfully', $result);
        // } catch (\Exception $e) {
        //     report($e);
        //     return ApiResponse::error('001', 'Error occurred. Please try again later');
        // }
    }

    public function downloadZipFile($id)
    {
        $subCategories = CategoryFile::where('id', $id)->first();

        return Storage::disk('spaces')->download(
            $subCategories->file,
            $subCategories->name
        );
    }
}
