<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use \Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        try {
            $this->validate($request, [
                'name'     => 'required|max:55',
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed'
            ]);
            
            $user           = new User;
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->password = app('hash')->make($request->password);
            $user->save();

            // $token = $user->createToken('AuthToken')->accessToken;
              // Add Generated token to user column
            // User::where('email', $request['email'])->update(array('api_token' => $token));

            return response([
                'user'  => $user,
                // 'token' => $token
            ],200);
        } catch (ValidationException $e) {

            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {

            return response()->json(['error' => $e], 500);
        }
    }

}
