@extends('layouts.app')

@section('content')
<div class="container">
  <h1>{{$user->name}}</h1>
  <div class="col-sm-12 row">
    @foreach ($strips as $strip)
        <div class="col-sm-2">
          <div class="card" title="{{$strip->title}}">
            <a href="/strips/{{ $strip->id }}">
              <img class="thumbnail-image" src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}" alt="{{ $strip->title}}"/>
              <strong>{{ $strip->title }}</strong>
            </a>
          </div>
        </div>
    @endforeach
  </div>
  <div class="col-sm-12">
    @if($currentPage > 1)
      <a href="/user/{{$user->id}}/{{$currentPage - 1}}" class="float-left">Previous Page</a>
    @endif
    @if($nextPage)
      <a href="/user/{{$user->id}}/{{$nextPage}}" class="float-right">Next Page</a>
    @endif
  </div>
</div>
@endsection