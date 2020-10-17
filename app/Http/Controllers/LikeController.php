<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Like;

class LikeController extends Controller
{
    
    public function like(Request $request, $id) {
      $this->middleware('auth');
      $data = [];
      if (!is_numeric($id)) {
        return response('Bad Request', 400);
      }
      
      $user_id = $request->user()->id;
      
      $like = Like::where('user_id', $user_id)->where('strip_id', $id)->count();
      
      if ($like) {
        return response('already liked', 400);
      }
      
      $data['user_id'] = $user_id;
      $data['strip_id'] = $id;
      
      tap(new Like($data))->save();
      
      return response('liked', 200);
    }
    
    public function unlike(Request $request, $id) {
      if (!is_numeric($id)) {
        return response('Bad Request', 400);
      }
      $this->middleware('auth');
      $user_id = $request->user()->id;
      $like = Like::where('user_id', $user_id)->where('strip_id', $id);
      
      $like->delete();
      
      return response('deleted', 200);
    }
}
