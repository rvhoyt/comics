<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Library;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $this->middleware('auth');
        return $this->streamLibraryData($request);
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
        
        return $this->streamLibraryData($request);
    }
    
    public function delete(Request $request, $id) {
        $this->middleware('auth');
        $library = Library::find($id);
        
        if ($library && $request->user()->id === $library->user_id) {
            $library->delete();
        }
        
        return $this->streamLibraryData($request);
    }
    
    private function streamLibraryData(Request $request)
    {
        return new StreamedResponse(function() use ($request) {
            DB::table('libraries')
                ->where('user_id', $request->user()->id)
                ->chunk(25, function($libraries) {
                    echo json_encode($libraries);
                    ob_flush();
                    flush();
                });
        }, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache, must-revalidate'
        ]);
    }
}
