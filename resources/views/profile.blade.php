@extends('layouts.app')

@section('content')
<div class="container">
  <h1>{{$user->name}}</h1>
  <div class="col-sm-12 row">
    @foreach ($strips as $strip)
        <div class="col-sm-2 mb-3">
          <div class="card" title="{{$strip->title}}" style="height:150px;">
            <a href="/strips/{{ $strip->id }}" class="text-center">
              <img class="thumbnail-image" src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}" alt="{{ $strip->title}}"/>
            </a>
            <div class="row">
              <div class="col-sm-3">
                <div class="float-left badge badge-light" style="padding:10px;margin:5px;margin-top:0">
                {{count($strip->likes)}}
                </div>
              </div>
              <div class="col-sm-8">
                <a href="/strips/{{ $strip->id }}"><strong>{{ $strip->title }}</strong></a>
                <a href="/user/{{$strip->owner->id}}">{{$strip->owner->name}}</a>
              </div>
            </div>
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