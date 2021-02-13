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

Route::get('page/{id}', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

Route::get('search/{id}', [App\Http\Controllers\SearchController::class, 'index'])->name('search');

Route::get('builder', [App\Http\Controllers\BuilderController::class, 'index'])->name('builder');

Route::post('builder', [App\Http\Controllers\BuilderController::class, 'save']);

Route::get('strips/{id}', [App\Http\Controllers\StripController::class, 'index'])->name('strip');

Route::post('strips/{id}', [App\Http\Controllers\StripController::class, 'edit']);

Route::get('strips/{id}/delete', [App\Http\Controllers\StripController::class, 'delete']);

Route::get('strips/{id}/like', [App\Http\Controllers\LikeController::class, 'like']);

Route::get('strips/{id}/unlike', [App\Http\Controllers\LikeController::class, 'unlike']);

Route::post('strips/{id}/comment', [App\Http\Controllers\CommentController::class, 'save']);

Route::get('comment/{id}/delete', [App\Http\Controllers\CommentController::class, 'delete']);

Route::post('comment/{id}', [App\Http\Controllers\CommentController::class, 'edit']);

Route::get('user', [App\Http\Controllers\ProfileController::class, 'index']);
Route::get('user/{id}/follow', [App\Http\Controllers\ProfileController::class, 'follow']);
Route::get('user/{id}/unfollow', [App\Http\Controllers\ProfileController::class, 'unfollow']);
Route::get('user/{id}/{page?}', [App\Http\Controllers\ProfileController::class, 'index']);

Route::post('user', [App\Http\Controllers\ProfileController::class, 'update']);

Route::get('library',  [App\Http\Controllers\LibraryController::class, 'index']);
Route::post('library', [App\Http\Controllers\LibraryController::class, 'save']);
Route::get('library/{id}/delete', [App\Http\Controllers\LibraryController::class, 'delete']);

Route::group(['prefix' => 'messages'], function () {
    Route::get('/', ['as' => 'messages', 'uses' => 'App\Http\Controllers\MessagesController@index']);
    Route::get('create', ['as' => 'messages.create', 'uses' => 'App\Http\Controllers\MessagesController@create']);
    Route::post('/', ['as' => 'messages.store', 'uses' => 'App\Http\Controllers\MessagesController@store']);
    Route::get('{id}', ['as' => 'messages.show', 'uses' => 'App\Http\Controllers\MessagesController@show']);
    Route::put('{id}', ['as' => 'messages.update', 'uses' => 'App\Http\Controllers\MessagesController@update']);
});