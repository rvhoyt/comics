@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
          <h1>Welcome to a Comic Strip builder test.</h1>
          @auth
            <a href="{{ url('/builder') }}">Go to strip builder</a>
          @else
            <span>Please Register</span>
          @endif
          <br/><br/>
          <hr/>
          <br/><br/>
        </div>
        <div class="col-sm-12 row">
          <div class="col-sm-12">
            <h2>Recent Strips</h2>
          </div>
          @foreach ($strips as $strip)
              <div class="col-sm-2">
                <a href="/strips/{{ $strip->id }}" class="card">
                  <img class="thumbnail-image" src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}" alt="{{ $strip->title}}"/>
                  <strong>{{ $strip->title }}</strong>
                </a>
              </div>
          @endforeach
        </div>
    </div>
</div>
@endsection
