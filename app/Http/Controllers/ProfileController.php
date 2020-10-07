<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
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
    public function index($id, $page = 1)
    {
        $user = User::find($id);
        
        $count = DB::table('strips')->where('user', 1)->count();
        
        $offset = ((int)$page - 1) * 12;
        
        $nextPage = false;
        if ($page * 12 < $count) {
          $nextPage = $page + 1;
        }
        
        $strips = DB::table('strips')->where('user', (int)$id)->offset($offset)->limit(12)->get();
        
        return view('profile', [
          'user' => $user,
          'strips' => $strips,
          'nextPage' => $nextPage,
          'currentPage' => $page
          ]);
    }
}
