<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use App\Models\Follow;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
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
      
      if (isset($request->q)) {
        $query = $request->q;
        $count = DB::table('strips')->where('title', 'like', '%' . $query . '%')->count();
        
        $offset = ((int)$page - 1) * 24;
        
        $nextPage = false;
        if ($page * 24 < $count) {
          $nextPage = $page + 1;
        }
        
        $strips = Strip
            ::where('title', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->take(24)
            ->get();

        $followingStrips = [];
        $nextPageFollow = false;

      } else {
        $query = '';
        $strips = [];
        $nextPage = false;
        $currentPage = false;
        $count = 0;
      }
        
        return view('search', [
          'strips' => $strips,
          'nextPage' => $nextPage,
          'currentPage' => $page,
          'stripCount' => $count,
          'query' => $query
        ]);
    }
}
