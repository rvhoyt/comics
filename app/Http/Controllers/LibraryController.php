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
        return new StreamedResponse(function () use ($request) {
            $chunkSize = 100; // Define the chunk size
            $buffer = []; // Buffer to hold chunked data
            $firstChunk = true; // Track if it's the first chunk
            
            // Start output buffering with gzip handler
            if (ob_start('ob_gzhandler')) {
                echo '['; // Start the JSON array
                
                $libraries = DB::table('libraries')
                    ->where('user_id', $request->user()->id)
                    ->cursor(); // Use cursor for efficient memory usage
                
                foreach ($libraries as $library) {
                    $buffer[] = $library; // Add library to buffer

                    // When buffer reaches chunk size, flush the chunk
                    if (count($buffer) >= $chunkSize) {
                        if (!$firstChunk) {
                            echo ','; // Add comma between JSON chunks
                        }

                        // Encode the buffer as JSON without surrounding brackets
                        echo substr(json_encode($buffer), 1, -1);
                        ob_flush();
                        flush();
                        
                        $buffer = []; // Clear buffer after flushing
                        $firstChunk = false; // Mark subsequent chunks
                    }
                }

                // Flush any remaining data in the buffer
                if (!empty($buffer)) {
                    if (!$firstChunk) {
                        echo ','; // Add comma for remaining chunk
                    }

                    // Encode the remaining buffer as JSON without surrounding brackets
                    echo substr(json_encode($buffer), 1, -1);
                    ob_flush();
                    flush();
                }
                
                echo ']'; // Close the JSON array
                ob_flush();
                flush();
                ob_end_flush(); // End the gzip compression
            }
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Encoding' => 'gzip', // Indicate that the content is gzipped
            'Cache-Control' => 'no-cache, must-revalidate'
        ]);
    }
}
