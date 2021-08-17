<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

/**
 * @group comment
 *
 * APIs for twits comment services
 *
 * @authenticated
 */
class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'text'=>'required|string|max:147',
            'twits_id'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->first()],409);
        }
        try{
            $user = Auth::user();
            $comment = new Comment;
            $comment->text = $request->input("text");
            $comment->twits_id = $request->input("twits_id");
            $comment->user_id = $user->id;
            $comment->save();
            return response()->json(['message'=>'comment successfully posted'], 200);
        }catch (\Exception $exception){
            return response()->json(['error'=>'Error in the process'], 4001);
        }
    }
}
