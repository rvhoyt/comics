<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuilderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('builder');
    }
    
    public function save(Request $request) {
      $data = $request->validate([
          'title' => 'required|max:255',
          'url' => 'required',
          'description' => 'required',
      ]);
      
      require_once '../app/helpers.php';
      
      $filename = $request->user()->id . time() . '.png';
      $file = $data['url'];
      $file = str_replace(' ','+',$file);
      $file =  substr($file,strpos($file,",")+1);
      $file = base64_decode($file);
      uploadToB2($file, $filename);


      $data['user'] = $request->user()->id;
      $data['url'] = $filename;

      $strip = tap(new App\Models\Strip($data))->save();

      return redirect('/');
    }
}
