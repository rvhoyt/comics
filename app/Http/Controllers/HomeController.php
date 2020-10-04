<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;

class HomeController extends Controller
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
    public function index()
    {
        $strips = Strip::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('home', ['strips' => $strips]);
    }
}
