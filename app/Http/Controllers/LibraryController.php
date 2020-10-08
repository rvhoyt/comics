<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Library;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $this->middleware('auth');
        $library = DB::table('libraries')->where('user_id', $request->user()->id)->get();
        
        return response()->json($library);
    }
    
    public function save(Request $request) {
      $this->middleware('auth');
      $data = $request->json()->all();
      $rules = ['data' => 'required'];
      $validator = Validator::make($data, $rules);
      if (!$validator->passes()) {
        dd($validator->errors()->all());
      }
      
      $data['user_id'] = $request->user()->id;
      
      tap(new Library($data))->save();
      
      $library = DB::table('libraries')->where('user_id', $request->user()->id)->get();
      
      return response()->json($library);
    }
    
    public function delete(Request $request, $id) {
      $this->middleware('auth');
      $library = Library::find($id);
      
      if ($request->user()->id === $library->user_id) {
        $library->delete();
      }
      
      $library = DB::table('libraries')->where('user_id', $request->user()->id)->get();
      
      return response()->json($library);
    }
}
