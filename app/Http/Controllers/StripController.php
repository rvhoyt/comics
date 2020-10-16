<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use App\Models\Comment;

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
    public function index($id)
    {
        $strip = Strip::find($id);
        
        $comments = Comment::where('strip_id', (int)$id)->orderBy('created_at', 'DESC')->get();
        
        return view('strip', ['strip' => $strip, 'comments' => $comments]);
    }
    
    public function edit(Request $request, $id) {
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
      $this->middleware('auth');
      $strip = Strip::find($id);
      
      if ($request->user()->id === $strip->user) {
        $strip->delete();
      }
      
      return redirect('/');
    }
}
