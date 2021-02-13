<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Models\Follow;

class StripController extends Controller
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
    public function index(Request $request, $id)
    {
        if (!is_numeric($id)) {
          return redirect('/');
        }
        
        $strip = Strip::find($id);
        
        $author = User::find($strip->user);
        
        
        
        $source = 'none';
        $validSources = ['following', 'user'];
        if (isset($request->source) && in_array($request->source, $validSources)) {
          $source = $request->source;
        }
        
        if ($source === 'following') {
          if (!$request->user()) {
            $source = 'none';
          } else {
            $user_id = $request->user()->id;
            $followees = Follow::where('user_id', '=', $user_id)->select('followee_id')->get();
            $followee_ids = [];
            foreach($followees as $f) {
              $followee_ids[] = $f->followee_id;
            }
            
            $strips = Strip
              ::whereIn('user', $followee_ids)
              ->orderBy('strips.created_at', 'desc')
              ->get();
          }
        }
        
        if ($source === 'user') {
          $strips = Strip::where('user', $author->id)->orderBy('created_at', 'DESC')->get();
        }
        
        if ($source === 'none') {
          $strips = Strip::orderBy('created_at', 'desc')->get();
        }
        
        $prev = '';
        $next = '';
        $found = false;
        foreach($strips as $s) {
          if ($found) {
            if ($prev !== '') {
              continue;
            }
            $prev = $s->id;
            continue;
          }
          if ($s->id == $id) {
            $found = true;
            continue;
          }
          $next = $s->id;
        }
        
        $sourceParams = [
          'none' => '',
          'following' => '?source=following',
          'user' => '?source=user'
        ];
        $sourceParam = $sourceParams[$source];
        
        
        $comments = Comment::where('strip_id', (int)$id)->orderBy('created_at', 'DESC')->get();
        
        $likes = Like::where('strip_id', (int)$id)->get();
        
        if ($request->user()) {
          $user_id = $request->user()->id;
          $alreadyLiked = Like::where('user_id', $user_id)->where('strip_id', $id)->count();
        } else {
          $alreadyLiked = false;
        }
        return view('strip', [
          'author' => $author,
          'strip' => $strip,
          'comments' => $comments,
          'likes' => $likes,
          'alreadyLiked' => $alreadyLiked,
          'next' => $next,
          'prev' => $prev,
          'source' => $sourceParam
        ]);
    }
    
    public function edit(Request $request, $id) {
      if (!is_numeric($id)) {
        return redirect('/');
      }
      $this->middleware('auth');
      $strip = Strip::find($id);
      
      if ($request->user()->id === $strip->user) {
        $strip->description = $request->input('description');
        $strip->title = $request->input('title');
        $strip->save();
      }
      
      return redirect('/strips/' . $id);
    }
    
    public function delete(Request $request, $id) {
      if (!is_numeric($id)) {
        return redirect('/');
      }
      $this->middleware('auth');
      $strip = Strip::find($id);
      
      require_once '../app/helpers.php';
      
      deleteFromB2($strip->fileId, $strip->url);
      
      if ($request->user()->id === $strip->user) {
        $strip->delete();
      }
      
      return redirect('/');
    }
}
