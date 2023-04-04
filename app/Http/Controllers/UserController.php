<?php

namespace App\Http\Controllers;


use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Contracts\IUserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    /**
     * UserController constructor.
     * @param IUserService $userService
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Verify User
     * @param $id
     * @param $activationCode
     * @return array
     */
    public function verifyUser($id, $activationCode)
    {
        if ($id && $activationCode) {
            try {
                $result = $this->userService->verifyUser($id, $activationCode);
                if ($result) {
                    return redirect('/login')->with('success', 'Your account has been verified successfully! Please use your email and password to login to your dashboard');
                }

                return redirect('/login')->with('warning', 'Invalid verification link!');
            } catch (\Exception $e) {
                report($e);

                session()->flush();
                return redirect('/login')->with('warning', 'Invalid verification link!');
            }
        }
    }

    public function impersonateCheck(Request $request) {
        return response()->json(session('nova:impersonator'));
    }

}