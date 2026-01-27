<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request){
        $credentils = $request->validated();
        if (!Auth::attempt($credentils)) {
            return response([
                'message' => 'provided phone or password is incoorect'
            ],401);
        }
        $user = Auth::user();
        switch ($user->userable) {
            case 'Admin':
                $data =Admin::findOrFail($user->userable_id);
                break;
            case 'Employee':
                $data =Employee::findOrFail($user->userable_id);
                break;

            default:
                # code...
                break;
        }
        /** @var \App\Models\User $user */
        $token = $user->createToken('main')->plainTextToken;
        $data =Admin::findOrFail($user->userable_id);
        // return response(compact('user','token'));
        $data->load('user');
        return response()->json(['admin'=> $data,'token'=>$token,'user'=>$user]);
    }

}
