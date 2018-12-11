<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator, Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $credentials = $request->only('name', 'middleName', 'lastName','email', 'password', 'type', 'phone', 
        'civilIDNumber', 'gender');
        $rules = [
            'name' => 'required|max:255',
            'middleName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'type'=> 'required',
            'phone' => 'required',
            'civilIDNumber' => 'required',
            'gender'=> 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['type'=> 'validation', 'success'=> false, 'error'=> $validator->messages()],400);
        }
        $name = $request->name;
        $middleName = $request->middleName;
        $lastName = $request->lastName;
        $email = $request->email;
        $password = $request->password;
        $type = $request->type;
        $phone = $request->phone;
        $civilIDNumber = $request->civilIDNumber;
        $gender = $request->gender;
        User::create(['name' => $name, 'middleName' => $middleName, 'lastName' => $lastName, 'email' => $email,
         'password' => Hash::make($password), 'type' => $type, 'phone' => $phone,
          'civilIDNumber' => $civilIDNumber, 'gender' => $gender]);
        return $this->login($request);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
