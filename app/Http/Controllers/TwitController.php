<?php

namespace App\Http\Controllers;

use App\Models\Twit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
/**
 * @group Twit
 *
 * APIs for twits services
 *
 * @authenticated
 */
class TwitController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'text'=>'required|string|max:147',
        ]);
        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        try{
            $user = Auth::user();
            $twits = new Twit;
            $twits->text = $request->input("text");
            $twits->user_id = $user->id;
            $twits->save();
            return response()->json(['message'=>'Twits successfully created'], 200);
        }catch (\Exception $exception){
            return response()->json(['error'=>'Error in the process'], 4001);
        }
    }

    public function read(){
        $twits = Twit::with('user', 'comment', 'comment.user','likes')->orderBy('id','desc')->get();
        return response()->json(['message'=>'fetch twits', 'data'=>$twits],200);
    }

    public function readOne(Request $request, $id){
        $twits = Twit::with('user', 'comment')->where('id', $id)->get();
        return response()->json(['message'=>'fetch twits', 'data'=>$twits,],200);
    }


    public function delete($id){
        $twit = Twit::with('user', 'comment')->where('id', $id)->first();
        if (!$twit){
            return response()->json(['error'=>"Twit is not available",],404);
        }

        if (Auth::user()->can('delete', $twit)){
            $twit->delete();
            return response()->json(['message'=>'your twit successfully deleted', 'data'=>$twit,],200);
        }else{
            return response()->json(['error'=>"You don't have Authority to delete this post",],400);
        }
    }

    public function readByUser(){
        $user = Auth::user();
        $twits = Twit::with('user', 'comment')->where('user_id', $user->id)->get();
        return response()->json(['message'=>'fetch twits', 'data'=>$twits,],200);
    }


}
