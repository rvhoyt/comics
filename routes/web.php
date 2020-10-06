<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/builder', [App\Http\Controllers\BuilderController::class, 'index'])->name('builder');

Route::post('/builder', function (Request $request) {
    $data = $request->validate([
        'title' => 'required|max:255',
        'url' => 'required',
        'description' => 'required|max:255',
    ]);
    
    require_once '../app/helpers.php';
    
    $filename = $request->user()->id . time() . '.png';
    $file = $data['url'];
    $file = str_replace(' ','+',$file);
    $file =  substr($file,strpos($file,",")+1);
    $file = base64_decode($file);
    uploadToB2($file, $filename);


    $data['user'] = $request->user()->id;
    $data['url'] = $filename;

    $strip = tap(new App\Models\Strip($data))->save();

    return redirect('/');
});

Route::get('/strips/{id}', [App\Http\Controllers\StripController::class, 'index'])->name('strip');

Route::post('/strips/{id}/comment', function(Request $request, $id) {
  $data = $request->validate([
      'comment' => 'required|max:1000',
  ]);
  
  $data['user_id'] = $request->user()->id;
  $data['strip_id'] = $id;
  
  tap(new App\Models\Comment($data))->save();
  
  return redirect('/strips/' . $id);
});

Route::get('/comment/{id}/delete', [App\Http\Controllers\CommentController::class, 'delete']);

Route::post('/comment/{id}', [App\Http\Controllers\CommentController::class, 'edit']);