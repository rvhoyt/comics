<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;

class StripController extends Controller
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
    public function index($id)
    {
        $strip = Strip::find($id);
        
        return view('strip', ['strip' => $strip]);
    }
}
