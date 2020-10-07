<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use Illuminate\Support\Facades\DB;

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
    public function index($page = 1)
    {
      
        $userCount = DB::table('users')->count();
        $count = DB::table('strips')->count();
        
        $offset = ((int)$page - 1) * 12;
        
        $nextPage = false;
        if ($page * 12 < $count) {
          $nextPage = $page + 1;
        }
        
        $strips = Strip::orderBy('created_at', 'desc')
            ->offset($offset)
            ->take(12)
            ->get();
        
        return view('home', [
          'strips' => $strips,
          'nextPage' => $nextPage,
          'currentPage' => $page,
          'stripCount' => $count,
          'userCount' => $userCount
        ]);
    }
}
