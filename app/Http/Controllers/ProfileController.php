<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use App\Models\Profile;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $id = 'missing', $page = 1)
    {
        if ($id === 'missing') {
          if (!$request->user()) {
            return redirect('/');
          }
          $id = $request->user()->id;
        }
        if (!is_numeric($id)) {
          return redirect('/');
        }
        
        if ($request->user()) {
          $user_id = $request->user()->id;
          $isFollowing = Follow::where('user_id', $user_id)->where('followee_id', $id)->count() > 0;
        } else {
          $isFollowing = false;
        }
        
        $user = User::find($id);
        
        $count = DB::table('strips')->where('user', 1)->count();
        
        $offset = ((int)$page - 1) * 12;
        
        $nextPage = false;
        if ($page * 12 < $count) {
          $nextPage = $page + 1;
        }
        
        $strips = Strip::where('user', (int)$id)->orderBy('created_at', 'DESC')->offset($offset)->limit(12)->get();
        
        $profile = Profile::where('user_id', $id)->first();
        
        $followees = Follow::where('user_id', $id)->get();
        
        return view('profile', [
          'user' => $user,
          'profile' => $profile,
          'strips' => $strips,
          'nextPage' => $nextPage,
          'currentPage' => $page,
          'followees' => $followees,
          'isFollowing' => $isFollowing
          ]);
    }
    
    public function follow(Request $request, $id) {
      $this->middleware('auth');
      $user_id = $request->user()->id;
      
      if ($id == $user_id) {
        return response('{"error":"cannot follow self"}', 400);
      }
      
      $profile = User::find($id);
      if (!$profile) {
        return response('{"error":"user not found"}', 400);
      }
      
      $check = Follow::where('user_id', $user_id)->where('followee_id', $id)->get();
      if (count($check) === 0) {
        $data = [
          'user_id' => $user_id,
          'followee_id' => $id
        ];
        $resp = tap(new Follow($data))->save();
      }
      
      return response('{"success":"user followed"}', 200);
    }
    
    public function unfollow(Request $request, $id) {
      $this->middleware('auth');
      $user_id = $request->user()->id;
      
      $follow = Follow::where('user_id', $user_id)->where('followee_id', $id)->first();
      if ($follow) {
        $follow->delete();
      }
      
      return response('{"success":"user unfollowed"}', 200);
    }
    
    public function update(Request $request){
      $this->middleware('auth');
      $user_id = $request->user()->id;
      $profile = Profile::where('user_id', $user_id)->first();
      $image = $request->file('image');
      require_once '../app/helpers.php';
      
      function processImage($image, $user_id) {
        if ($image) {
          $fileId = uploadToB2($image->get(), $user_id . '.jpg', 'cee8b7ec89db39707e51081e');
          return $fileId;
        }
        return '';
      }
      
      if (!$profile) {
        $profile = new \stdClass();
        $profile->description = $request->input('description');
        $profile->user_id = $user_id;
        $profile->image = '';
        $profile->fileId = '';
        $fileId = processImage($image, $user_id);
        if ($fileId) {
          $profile->fileId = $fileId;
          $profile->image = $user_id . '.jpg';
        }
        tap(new Profile((array)$profile))->save();
      } else {
        if ($image) {
          if ($profile->fileId) {
            deleteFromB2($profile->fileId, $user_id . '.jpg');
          }
          $fileId = processImage($image, $user_id);
          if ($fileId) {
            $profile->fileId = $fileId;
            $profile->image = $user_id . '.jpg';
          }
        }
        $profile->description = $request->input('description');
        $profile->save();
      }
    }
}
