<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::where('email', $request['email'])->firstOrFail();
            $response['access_token']  = $user->createToken('auth_token')->plainTextToken; 
            $response['name'] =  $user->name;
            $response['email'] =  $user->email;

            return $this->sendResponse($response, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $response['access_token'] =  $user->createToken('auth_token')->plainTextToken; 
        $response['name'] =  $user->name;
        $response['email'] =  $user->email;
        
        return $this->sendResponse($response, 'User created successfully.');
    }
}
