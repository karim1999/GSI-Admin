<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator, Hash;
use Illuminate\Support\Facades\Password;

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
     public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if(is_numeric($request->email)){

            $credentials = array('phone'=>$request->email,'password'=>$request->password);

            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);          
        }else{ 
               
            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token);          
        }    

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
            'phone' => 'required|unique:users',
            'civilIDNumber' => 'required',
            'gender'=> 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['type'=> 'validation', 'success'=> false, 'msg'=> $validator->messages()],400);
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
        return response()->json( auth()->user()->load([ 'lecture', 'jointLectures']) );
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

    public function userToken(Request $request){
        $user = auth()->user();
        $user->token = $request->token;
        $user->save();

        return $user;
    }

    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email'=> [$error_message]]], 401);
        }
        try {
            Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject('Your Password Reset Link')->action('Reset Password', url('password/reset', $this->token));
                
            });
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }
        return response()->json([
            'success' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->middleName = $request->middleName;
        $user->lastName = $request->lastName;
        $user->gender = $request->gender;
        if($request->email == $user->email){
            $user->email = $request->email;
        }else{
            $validator = Validator::make($request->all(),[
                'email' => 'email|max:255|unique:users'
            ]);
            if($validator->fails()) {
                return response()->json(['type'=> 'validation', 'success'=> false, 'msg'=> $validator->messages()],400);
            }
        }
        $user->email = $request->email;
        if($request->phone == $user->phone){
            $user->phone = $request->phone;
        }else{
            $validator = Validator::make($request->all(),[
                'phone' => 'unique:users'
            ]);
            if($validator->fails()) {
                return response()->json(['type'=> 'validation', 'success'=> false, 'msg'=> $validator->messages()],400);
            }
        }
        $user->phone = $request->phone;
        $user->civilIDNumber = $request->civilIDNumber;
        $user->save();
        return $user;
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        /*
        * Validate all input fields
        */
        // $this->validate($request, [
        //     'password' => $user->password,
        //     'new_password' => 'confirmed|max:8|different:password',
        // ]);

        if (Hash::check($request->oldPassword, $user->password)) {
            $user->password = Hash::make($request->newPassword);
            $user->save();
            return 'success';

        } else {
            return 'error';
        }

    }
    
    public function privacy(){
        return view('Privacy Policy of gsi - FreePrivacyPolicy');
    }
    
    public function userImage(Request $request)
    {
        $user = auth()->user();
        $user->img = $request->img;
        $user->save();
        
        return response()->json(auth()->user()->load('lecture','jointLectures'));
    }
}
