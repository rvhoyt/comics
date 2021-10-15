<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Comment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        view()->composer('partials.recentcomments', function($view) {
          $request = app(\Illuminate\Http\Request::class);
          $id = $request->user()->id;
          $comments = Comment::select('comments.*', 'strips.id')
            ->join('strips', 'strips.id', '=', 'comments.strip_id')
            ->where('user', $id)
            ->orderBy('comments.created_at', 'DESC')
            ->limit(5)
            ->get();
          $view->with('comments', $comments);
        });
    }
}
