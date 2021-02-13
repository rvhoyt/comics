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
          @else
            <span>Please Login to make strips</span>
          @endif
          <br/><br/>
          <hr/>
          <br/><br/>
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
                  <a href="/strips/{{ $strip->id }}?source=following" class="text-center">
                    <img class="thumbnail-image" src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}" alt="{{ $strip->title}}"/>
                  </a>
                  <div class="row">
                    <div class="col-sm-3">
                      <div class="float-left badge badge-light" style="padding:10px;margin:5px;margin-top:0">
                      {{count($strip->likes)}}
                      </div>
                    </div>
                    <div class="col-sm-8">
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
