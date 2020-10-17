<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use App\Models\Profile;
use App\Models\User;
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
        $user = User::find($id);
        
        $count = DB::table('strips')->where('user', 1)->count();
        
        $offset = ((int)$page - 1) * 12;
        
        $nextPage = false;
        if ($page * 12 < $count) {
          $nextPage = $page + 1;
        }
        
        $strips = Strip::where('user', (int)$id)->orderBy('created_at', 'DESC')->offset($offset)->limit(12)->get();
        
        $profile = Profile::where('user_id', $id)->first();
        
        return view('profile', [
          'user' => $user,
          'profile' => $profile,
          'strips' => $strips,
          'nextPage' => $nextPage,
          'currentPage' => $page
          ]);
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
      
      $filename = $user_id;
      if (!$profile) {
        $profile = [
          'description' => $request->input('description'),
          'user_id' => $user_id,
          'image' => '',
          'fileId' => ''
        ];
        $fileId = processImage($image, $user_id);
        if ($fileId) {
          $profile->fileId = $fileId;
          $profile->image = $user_id . '.jpg';
        }
        tap(new Profile($profile))->save();
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
