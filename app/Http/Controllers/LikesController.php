<?php

namespace App\Http\Controllers;

use App\Models\Likes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

/**
 * @group Likes
 *
 * APIs for like services
 *
 * @authenticated
 */
class LikesController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'twits_id'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        try{
            $user = Auth::user();
            $liked = Likes::where(['twits_id'=>$request->input("twits_id"),'user_id'=>$user->id])->first();
            if ($liked){
                return response()->json(['message'=>'already liked'], 402);
            }
            $like = new Likes;
            $like->twits_id = $request->input("twits_id");
            $like->user_id = $user->id;
            $like->save();
            return response()->json(['message'=>'liked'], 200);
        }catch (\Exception $exception){
            return response()->json(['error'=>'Error in the process'], 401);
        }
    }
}
