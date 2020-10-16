<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;

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
      try {
        $title = $request->input('title');
        $url = $request->input('url');
        $description = $request->input('description');
      } catch(Exception $e) {
        return response('Missing Fields', 400);
      }
      if (!$description || ! $title || !$url) {
        return response('Missing Fields', 400);
      }
      if (strlen($title) > 255) {
        return response('Title Too Long', 400);
      }
      
      $data = [
        'title' => $title,
        'url' => $url,
        'description' => $description
      ];
      
      try {
        require_once '../app/helpers.php';
        
        $filename = $request->user()->id . '-' . time() . '.png';
        $file = $data['url'];
        $file = str_replace(' ','+',$file);
        $file =  substr($file,strpos($file,",")+1);
        $file = base64_decode($file);
        $fileId = uploadToB2($file, $filename);
      } catch(Exception $e) {
        return response('Could not save image', 500);
      }


      $data['user'] = $request->user()->id;
      $data['url'] = $filename;
      $data['fileId'] = $fileId;

      $strip = tap(new Strip($data))->save();
      
      return response($strip->id, 200);
    }
}
