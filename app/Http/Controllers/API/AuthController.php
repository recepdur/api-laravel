<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Resources\User as UserResource;

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

        $userControlEmail = User::where('email', $request->input('email'))->get();
        if(!$userControlEmail->isEmpty()) {
            return $this->sendError('Email already exist.');
        }

        $newUser = new User;
        $newUser->email = $request->input('email');
        $newUser->password = bcrypt($request->input('password'));

        if ($request->has(['name'])) 
            $newUser->name = $request->input('name');

        if ($request->has(['status'])) 
            $newUser->status = $request->input('status');
        else
            $newUser->status = false;

        if ($request->has(['phone'])) 
            $newUser->phone = $request->input('phone');

        if ($request->has(['role'])) 
            $newUser->role = $request->input('role');
        
        $newUser->save();

        //$newUser['access_token'] = $newUser->createToken('auth_token')->plainTextToken; 

        return $this->sendResponse(new UserResource($newUser), 'User inserted.');
    }

    public function getUsers(Request $request)
    {
        $userRole = Auth::user()->role;
        if($userRole == "standart")
        {
            $users = User::all(); 
            return $this->sendResponse(UserResource::collection($users), 'Users fetched.');
        }
        else
            return $this->sendError("Not allowed!");
    }
}
