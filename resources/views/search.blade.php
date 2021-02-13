@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2>Search Strips</h2>
        <form class="input-group mb-3">
          <input type="search" class="form-control" value="{{$query}}" name="q">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
          </div>
        </form>
      </div>
        
        @if ($query)
        <div class="col-sm-12 row">
          <div class="col-sm-12">
            <h2>{{$stripCount}} Strips Matching "{{$query}}"</h2>
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
            <a href="/search/{{$currentPage - 1}}?q={{$query}}" class="float-left">Previous Page</a>
          @endif
          @if($nextPage)
            <a href="/search/{{$nextPage}}?q={{$query}}" class="float-right">Next Page</a>
          @endif
        </div>
        @endif
        
    </div>
</div>
@endsection
