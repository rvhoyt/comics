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
    public function index(Request $request, $page = 1)
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

        $followingStrips = [];
        $nextPageFollow = false;
        
        if ($request->user()) {
          $user_id = $request->user()->id;
          $followingStrips = Strip
            ::join('follows', 'strips.user', '=', 'follows.followee_id')
            ->where('follows.user_id', '=', $user_id)
            ->orderBy('strips.created_at', 'desc')
            ->offset($offset)
            ->take(12)
            ->get();
          $countFollow = DB::table('strips')
            ->join('follows', 'strips.user', '=', 'follows.followee_id')
            ->where('follows.user_id', '=', $user_id)
            ->count();
          if ($page * 12 < $countFollow) {
            $nextPageFollow = $page + 1;
          }
        }
        
        return view('home', [
          'strips' => $strips,
          'followingStrips' => $followingStrips,
          'nextPage' => $nextPage,
          'nextPageFollow' => $nextPageFollow,
          'currentPage' => $page,
          'stripCount' => $count,
          'userCount' => $userCount
        ]);
    }
}
