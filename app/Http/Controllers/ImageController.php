<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ImageController extends Controller
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
    public function index(Request $request, $image)
    {

      if (!isset($image)) {
        abort(404);
        $data['title'] = '404';
        $data['name'] = 'Page not found';
        return response()
            ->view('errors.404',$data,404); 
      }
      
      $file = '/public/strip-images/' . $image;
      
      if (!file_exists($file)) {
        file_put_contents($file, fopen('https://strips.s3.eu-central-003.backblazeb2.com/' . $image, 'r'));
      }
      
      return response()->file($file);
    }
}
