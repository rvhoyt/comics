<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;


class CommentController extends Controller
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
    
    public function save(Request $request, $id) {
      if (!is_numeric($id)) {
        return redirect('/');
      }
      $this->middleware('auth');
      $data = $request->validate([
          'comment' => 'required|max:1000',
      ]);
      
      $data['user_id'] = $request->user()->id;
      $data['strip_id'] = $id;
      
      tap(new Comment($data))->save();
      
      return redirect('/strips/' . $id);
    }
    
    public function delete(Request $request, $id) {
      if (!is_numeric($id)) {
        return redirect('/');
      }
      $this->middleware('auth');
      $comment = Comment::find($id);
      $strip_id = $comment->strip_id;
      
      if ($request->user()->id === $comment->user_id) {
        $comment->delete();
      }
      
      return redirect('/strips/' . $strip_id);
    }
    
    public function edit(Request $request, $id) {
      if (!is_numeric($id)) {
        return redirect('/');
      }
      $this->middleware('auth');
      $comment = Comment::find($id);
      $strip_id = $comment->strip_id;
      
      if ($request->user()->id === $comment->user_id) {
        $comment->comment = $request->input('comment');
        $comment->save();
      }
      
      return redirect('/strips/' . $strip_id);
    }
}
