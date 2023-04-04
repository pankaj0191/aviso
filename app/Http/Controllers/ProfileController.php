<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Contracts\IProfileService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private $profileService;

    public function __construct(IProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->updateDescription($user, $request);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->updateHourly($user, $request);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNotify(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->updateNotify($user, $request);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * Create new profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createProfile(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->createProfile($user, $request);
            return ApiResponse::success('New account has been created', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
    
    /**
     * Create new profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $id)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->updateProfile($user, $request, $id);
            return ApiResponse::success('Profile has been updated', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
    
    /**
     * update user info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->updateUserInfo($user, $request);
            
            return ApiResponse::success('Information has been updated', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
    
    /**
     * update user info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateNotification(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->updateNotification($user, $request);
            
            return ApiResponse::success('Notifcations has been updated', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
    
    /**
     * update user info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'password' => ['required', 'confirmed', 'min:6', function ($attribute, $value, $fail) use ($user) {
                if (\Hash::check($value, $user->password)) {
                    return $fail(__('The password must differ from old password.'));
                }
            }],
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!\Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
        ]);
        try {
            $result = $this->profileService->updatePassword($user, $request);
            
            return ApiResponse::success('Password has been updated', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * switch profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switchProfile(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->switchProfile($user, $request);
            return ApiResponse::success('Current profile has been change', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
    
    /**
     * switch profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switchTeam(Request $request)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->switchTeam($user, $request);
            return ApiResponse::success('Current team has been changed', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * switch profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->destroy($user, $id);
            return ApiResponse::success('Profile has been deleted!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
    
    /**
     * switch profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        try {
            $result = $this->profileService->deletePhoto($user);
            return ApiResponse::success('Photo has been deleted!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }
}

