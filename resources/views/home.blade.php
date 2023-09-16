@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
          <div class="alert alert-success float-right">
            <div>{{$userCount}} Artists</div>
            <div>{{$stripCount}} Strips</div>
          </div>
          <h1>Comic Crafter</h1>
          @auth
            <a href="{{ url('/builder') }}" class="btn btn-primary">Make a Strip!</a>
            <a href="https://discord.gg/9BunZDKY3b" target="_blank" class="btn btn-secondary">Join Our Discord</a>
          @else
            <span>Please Login to make strips</span>
          @endif
          <br/><br/>
          <hr/>
          <br/><br/>
        </div>
        
        <div class="col-sm-12 row">
          <div class="col-sm-12">
            <h2>Popular in the Last Week</h2>
          </div>
          @foreach ($popularStrips as $strip)
              <div class="col-md-2 mb-3">
                <div class="card" title="{{$strip->title}}" style="height:200px;">
                  <div class="badge badge-light" style="padding:10px;margin:5px;margin-top:0;position:absolute;bottom:0" title="Likes">
                    {{count($strip->likes)}}
                  </div>
                  <a href="/strips/{{ $strip->id }}?source=following" class="text-center">
                    <img class="thumbnail-image" src="/strip-images/{{ $strip->url }}" alt="{{ $strip->title}}"/>
                  </a>
                  <div class="row" style="padding:10px;">
                    <div class="col-sm-12">
                      <a href="/strips/{{ $strip->id }}?source=following"><strong>{{ $strip->title }}</strong></a>
                      <br/>
                      <a href="/user/{{$strip->owner->id}}">{{$strip->owner->name}}</a>
                    </div>
                  </div>
                </div>
              </div>
          @endforeach
        </div>
        
        <div class="col-sm-12 row">
          <div class="col-sm-12">
            <h2>Recent Followed Strips</h2>
          </div>
          @if (count($followingStrips) === 0) 
            <div class="col-sm-12 alert alert-info">Login and follow some artists!</div>
          @endif
          @foreach ($followingStrips as $strip)
              <div class="col-md-2 mb-3">
                <div class="card" title="{{$strip->title}}" style="height:200px;">
                  <div class="badge badge-light" style="padding:10px;margin:5px;margin-top:0;position:absolute;bottom:0" title="Likes">
                    {{count($strip->likes)}}
                  </div>
                  <a href="/strips/{{ $strip->id }}?source=following" class="text-center">
                    <img class="thumbnail-image" src="/strip-images/{{ $strip->url }}" alt="{{ $strip->title}}"/>
                  </a>
                  <div class="row" style="padding:10px;">
                    <div class="col-sm-12">
                      <a href="/strips/{{ $strip->id }}?source=following"><strong>{{ $strip->title }}</strong></a>
                      <br/>
                      <a href="/user/{{$strip->owner->id}}">{{$strip->owner->name}}</a>
                    </div>
                  </div>
                </div>
              </div>
          @endforeach
        </div>
        <div class="col-sm-12">
          @if($currentPage > 1)
            <a href="/page/{{$currentPage - 1}}" class="float-left">Previous Page</a>
          @endif
          @if($nextPageFollow)
            <a href="/page/{{$nextPageFollow}}" class="float-right">Next Page</a>
          @endif
        </div>
        
        
        <div class="col-sm-12 row">
          <hr/>
        </div>
        
        <div class="col-sm-12 row">
          <div class="col-sm-12">
            <h2>Recent Strips</h2>
          </div>
          @foreach ($strips as $strip)
              <div class="col-md-2 mb-3">
                <div class="card" title="{{$strip->title}}" style="height:200px;">
                  <div class="badge badge-light" style="padding:10px;margin:5px;margin-top:0;position:absolute;bottom:0" title="Likes">
                    {{count($strip->likes)}}
                  </div>
                  <a href="/strips/{{ $strip->id }}" class="text-center">
                    <img class="thumbnail-image" src="/strip-images/{{ $strip->url }}" alt="{{ $strip->title}}"/>
                  </a>
                  <div class="row" style="padding:10px;">
                    <div class="col-sm-12">
                      <a href="/strips/{{ $strip->id }}"><strong>{{ $strip->title }}</strong></a>
                      <br/>
                      <a href="/user/{{$strip->owner->id}}">{{$strip->owner->name}}</a>
                    </div>
                  </div>
                </div>
              </div>
          @endforeach
        </div>
        <div class="col-sm-12">
          @if($currentPage > 1)
            <a href="/page/{{$currentPage - 1}}" class="float-left">Previous Page</a>
          @endif
          @if($nextPage)
            <a href="/page/{{$nextPage}}" class="float-right">Next Page</a>
          @endif
        </div>
        
    </div>
</div>
@endsection
