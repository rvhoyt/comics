@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
          <div class="alert alert-success float-right">
            <div>{{$userCount}} Artists</div>
            <div>{{$stripCount}} Strips</div>
          </div>
          <h1>Welcome to a Comic Strip builder!</h1>
          @auth
            <a href="{{ url('/builder') }}">Go to strip builder</a>
          @else
            <span>Please Login to make strips</span>
          @endif
          <br/><br/>
          <hr/>
          <br/><br/>
        </div>
        
        <div class="col-sm-12">
          <div class="alert alert-info">
            <h2>The Future</h2>
            <p>Hello fellow Strip Generator users! As we all know with the imminent death of Flash and the seeming abandoned state of Strip Generator, I've started this project to attempt to keep the community alive and active.</p>
            <p>In order for this to be a success, it will need your assistance and I hope to make the platform as open as possible to input and development. As such, here's a link to a Google Sheet for suggestions and reporting bugs: <a rel="noopener" target="_blank" href="https://docs.google.com/spreadsheets/d/1e6KPjDp23X6TRmrnBpYFUdHA9fG_x1JzWXC19vmIiUI/edit?usp=sharing">Suggestions</a></p>
            <p>In the sheet, please also suggest names for this project. Then I can get a domain and figure out proper hosting.</p>
            <p>LMake your mark on this new community and help provide SVG images to include in our library. You can create them using <a target="_blank" rel="noopener" href="https://inkscape.org/">Inkscape<a/>.
            You may upload SVG files via <a target="_blank" rel="noopener" href="https://www.dropbox.com/request/bBleQ5huRKFXg6jrfSwq">this link</a>.
            <p>Thank you, and hopefully we can build a great community together!</a>
          </div>
        </div>
        
        <div class="col-sm-12 row">
          <div class="col-sm-12">
            <h2>Recent Strips</h2>
          </div>
          @foreach ($strips as $strip)
              <div class="col-sm-2 mb-3">
                <div class="card" title="{{$strip->title}}">
                  <a href="/strips/{{ $strip->id }}">
                    <img class="thumbnail-image" src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}" alt="{{ $strip->title}}"/>
                    <strong>{{ $strip->title }}</strong>
                  </a>
                  <a href="/user/{{$strip->owner->id}}">{{$strip->owner->name}}</a>
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
