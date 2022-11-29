<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|string',
            'c-password'=>'required|same:password',
            'address'=>'required',
            'cellphone'=>'required',
            'postal_code'=>'required',
            'province_id'=>'required',
            'city_id'=>'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(),422);
        }

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'address'=>$request->address,
            'cellphone'=>$request->cellphone,
            'postal_code'=>$request->postal_code,
            'province_id'=>$request->province_id,
            'city_id'=>$request->city_id,
        ]);

        $token=$user->createToken('myApp2')->plainTextToken;
        return $this->successResponse([
            'user'=>$user,
            'token'=>$token
        ],200);
        
    }

    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if ($validator->fails()) {
            return $this->successResponse($validator->messages(),422);
        }

        $user=User::where('email',$request->email)->first();

        if (!$user) {
            return $this->errorResponse('user not found',422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse('password is incorrect',422);
        }

        $token=$user->createToken('myApp3')->plainTextToken;

        return $this->successResponse([
            'user'=>$user,
            'token'=>$token
        ],200);
    }

    public function logout()
    {
        $user=Auth::user();
        dd($user);
    }
}
