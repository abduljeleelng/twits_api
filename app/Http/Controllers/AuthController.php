<?php
/**
 * Created by PhpStorm.
 * User: AbduljeleelNG
 * Date: 8/9/2021
 * Time: 7:26 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Ramsey\Uuid\Uuid;
use App\Mail\ActCreate;
use App\mail\Activated;
use App\mail\ForgetPass;
use App\mail\ResetPass;
use Illuminate\Support\Facades\Mail;


/**
 * @group Auth User management
 *
 * APIs for authenticating users
 */

class AuthController extends Controller
{
    public function __construct()
    {

    }

    public function signUp(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:6',
            'password_confirmation'=>'string|required',
        ]);
        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        $name = $parts=explode('@',$request->input("email"));
         try{
            $user = new User;
            $uuid = Uuid::uuid4();
            $user->name = $name[0];
            $user->email = $request->input("email");
            $user->password = app('hash')->make($request->input("password"));
            $user->activate = $uuid->toString();
            $user->reset = "";
            $user->active = false;
            $user->save();
            if ($user){
                Mail::to($user->email)->send(new ActCreate($user));
                return response()->json(["data"=>$user,"message"=>"registration is successful",],200);
            }
            return response()->json(["error"=>"registration fail"],404);
        }catch (\Exception $exception){
            return response()->json(["error"=>"errors", "exce"=>$exception], 400);
        }
    }

    public function activate(Request $request){
        $validator = Validator::make($request->all(),[
            'activationCode'=>'required|string',
        ]);
        if ($validator->fails()){
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        try {
            $user = User::where('activate', $request->input('activationCode') )->first();
            if(!$user){
                return response()->json(['error'=>'wrong activation code or Invalid link'],404);
            }
            User::where('id', $user->id)->update(['activate' => "",'active'=>true]);
            Mail::to($user->email)->send(new Activated($user));
            return response()->json(['data'=>$user,'message'=>'your Account is successfully activated'],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message'=>'error in processing your request'],400);
        }

    }

    public function signIn(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|string',
            'password'=>'required|string'
        ]);
        if ($validator->fails()){
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        $user = User::where('email', $request->input('email'))->first();
        if (!$user){
            return response()->json(["error"=>"email not exist, sign up for new account"],408);
        }
        if(!$user->active){
            return response()->json(['error'=>'your account is not activated, \n Please check your email to activate your account \n '],407);
        }
        $match = Hash::check($request->input('password'), $user->password);
        if(!$match){
            return response()->json(['error'=>'email and password not match'], 404);
        }
        $credentials = $request->only(['email', 'password']);
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token,$user);
    }

    public function forgetPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|string',
        ]);
        if ($validator->fails()){
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        try {
            $user= User::where('email', $request->input('email') )->first();
            if(!$user){
                return response()->json(['error'=>'email is not exist, you can sign up for new account'],408);
            }
            if(!$user->active){
                return response()->json(['error'=>'your account is not activated, \n Please check your email to activate your account'], 407);
            }
            $uuid = Uuid::uuid4();
            User::where('id', $user->id)->update(['reset' =>$uuid->toString()]);

            $user= User::where('id',$user->id)->first();
            Mail::to($user->email)->send(new ForgetPass($user));
            return response()->json(['data'=>$user,'message'=>'Password reset token has send to your email, \n Please, check your email and continue the process'],200);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'There is an error while process your request'],400);
        }

    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'resetToken'=>'required',
            'password'=>'required|confirmed',
            'password_confirmation'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        try {
            $user= User::where('reset', $request->input('resetToken'))->first();
            if(!$user){
                return response()->json(['error'=>'Invalid link,\n Please contact support team'],408);
            }
            if(!$user->active){
                return response()->json(['error'=>'your account is not activated, \n Please check your email to activate your account'],407);
            }
            $reset = app('hash')->make($request->input('password'));
            User::where('id', $user->id)->update(['password' =>$reset,'reset'=>'']);
            $user= User::where('id',$user->id)->first();
            Mail::to($user->email)->send(new ResetPass($user));
            return response()->json(['data'=>$user,'message'=>'your Password is successfully reset'],200);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'There is an error while reset your password '],201);
        }
    }

    public function signOut(){
        auth()->logout(true);
        return response()->json(['message'=>'you are successfully logout'],200);
    }

}
