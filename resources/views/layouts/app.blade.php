<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="/images/favicon.png"/>

    <!-- Scripts -->
    <script src="/js/jquery-3.5.1.slim.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
      <meta property="og:type" content="website" />
      <meta name="twitter:card" content="summary_large_image">
    @if (strpos(URL::current(), 'strips/') !== false)
      <meta property="og:title" content="{{$strip->title}}">
      <meta property="og:description" content="{{$strip->description}}">
      <meta property="og:image" content="https://comiccrafter.com/strip-images/{{ $strip->url }}">
      <meta property="og:url" content="{{URL::current()}}">
      
      <meta name="twitter:title" content="{{$strip->title}}">
      <meta name="twitter:description" content="{{$strip->description}}">
      <meta name="twitter:image" content="https://comiccrafter.com/strip-images/{{ $strip->url }}">
    @else
      <meta property="og:title" content="Comic Crafter">
      <meta property="og:description" content="Create and share comics with your friends.">
      <meta property="og:image" content="https://comiccrafter.com/strip-images/5-1617648166.png">
      <meta property="og:url" content="https://comiccrafter.com">
      
      <meta name="twitter:title" content="Comic Crafter">
      <meta name="twitter:description" content="Create and share comics with your friends.">
      <meta name="twitter:image" content="https://comiccrafter.com/strip-images/5-1617648166.png">
      
    @endif
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                      <li>
                      <form class="input-group" action="/search">
                        <input type="search" class="form-control" name="q" placeholder="Search Strips">
                        <div class="input-group-append">
                          <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                      </form>
                      </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                              <a id="navbarDropdownComments" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Recent Comments
                              </a>
                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownComments">
                                @include('partials.recentcomments')
                              </div>
                            </li>
                            <li class="nav-item">
                              <a href="/messages" class="nav-link {{Auth::user()->newThreadsCount() > 0 ? 'font-weight-bold' : ''}}">{{Auth::user()->newThreadsCount()}} Messages</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/user">Profile</a>
                                    <a class="dropdown-item" href="/builder">Make a Strip</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('myjsfile')
</body>
</html>
