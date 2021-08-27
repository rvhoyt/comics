@extends('layouts.app')

@section('content')
<div class="container">
  <h1>
    @if (auth()->user() && $user->id === auth()->user()->id)
    <button type="button" class="btn btn-primary edit-profile float-right" style="margin-top:15px;margin-bottom: 15px">Edit Profile</button>
    @endif
    
    @if (!$isFollowing && auth()->user() && $user->id !== auth()->user()->id)
    <button type="button" class="btn btn-primary follow-user float-right" style="margin-top:15px;margin-bottom: 15px">Follow</button>
    @elseif ($isFollowing && auth()->user() && $user->id !== auth()->user()->id)
    <button type="button" class="btn btn-primary unfollow-user float-right" style="margin-top:15px;margin-bottom: 15px">Unfollow</button>
    @endif
    
    {{$user->name}}
  </h1>
  <div class="row">
    <div class="col-sm-2">
      @if ($profile && $profile->image) 
      <img src="https://cc-avatars.s3.eu-central-003.backblazeb2.com/{{$profile->image}}" style="width:150px"/>
      @else
      <img src="/images/profile.jpg" width="150px" style="opacity: 0.5;" alt="No Profile Image"/>
      @endif
    </div>
    <div class="col-sm-6">
      @if ($profile && $profile->description)
      {{$profile->description}}
      @else
        {missing description}
        <br/>
        Such a mysterious person...
      @endif
    </div>
    <div class="col-sm-4">
      <div class="card">
       @if($stripCount)
        <div class="card-body">
          Strips: {{$stripCount}}
          <br/>
          Most Liked Strip: <a href="/strips/{{$popularStrip->id}}">{{$popularStrip->title}}</a>
        </div>
        @else
        <div class="card-body">
          No strips made.
        </div>
        @endif
      </div>
    </div>
  </div>
  <br/>
  @if (auth()->user() && $user->id === auth()->user()->id)
  <div class="row">
    <form class="form profile-editor col-sm-12" style="display:none">
      @csrf
      <div class="form-group">
        <label for="profile-description">Profile Description</label>
        <textarea class="form-control" id="profile-description" name="description" required>{{$profile ? $profile->description : ""}}</textarea>
      </div>
      <div class="custom-file">
        <input type="file" accept="image/jpeg" name="image" id="image-upload" class="custom-file-input">
        <label class="custom-file-label" for="image-upload">Choose avatar image, jpeg only, 150x150, max 200kb</label>
        <p style="display:none" class="alert alert-danger file-error">File is too large</p>
      </div>
      <br/><br/>
      <button class="btn btn-primary save-profile profile-submit">Save</button>
      <hr/>
    </form>
  </div>
  @endif
  <div class="row">
    @foreach ($strips as $strip)
        <div class="col-md-2 mb-3">
          <div class="card" title="{{$strip->title}}" style="height:200px;">
            <div class="badge badge-light" style="padding:10px;margin:5px;margin-top:0;position:absolute;bottom:0" title="Likes">
              {{count($strip->likes)}}
            </div>
            <a href="/strips/{{ $strip->id }}?source=user" class="text-center">
              <img class="thumbnail-image" src="/strip-images/{{ $strip->url }}" alt="{{ $strip->title}}"/>
            </a>
            <div class="row" style="padding:10px;">
              <div class="col-sm-12">
                <a href="/strips/{{ $strip->id }}?source=user"><strong>{{ $strip->title }}</strong></a>
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
      <a href="/user/{{$user->id}}/{{$currentPage - 1}}" class="float-left">Previous Page</a>
    @endif
    @if($nextPage)
      <a href="/user/{{$user->id}}/{{$nextPage}}" class="float-right">Next Page</a>
    @endif
  </div>
  
  <div class="col-sm-12" style="clear:both">
    <h3>Following</h3>
    <div class="row">
      @foreach ($followees as $followee)
      <div class="col-sm-2">
        <a href="/user/{{$followee->followee_id}}">
        @if ($followee->followee->profile && $followee->followee->profile->image)
          <img style="max-height:150px;max-width:150px" src="https://cc-avatars.s3.eu-central-003.backblazeb2.com/{{$followee->followee->profile->image}}"/>
        @else
          <img src="/images/profile.jpg" width="150px" style="opacity: 0.5;" alt="No Profile Image"/>
        @endif
        <br/>
        {{$followee->followee->name}}
        </a>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

@section('myjsfile')
<script>
  $('body').on('click', '.edit-profile', function() {
    $('.edit-profile').hide();
    $('.profile-editor').show();
  });
  
  $('body').on('click', '.follow-user', function() {
    $.ajax(window.location.pathname + '/follow')
      .done(function(r) {
        $('.follow-user').hide();
      })
      .fail(function(r) {
        
      });
  });
  
  $('body').on('click', '.unfollow-user', function() {
    $.ajax(window.location.pathname + '/unfollow')
      .done(function(r) {
        $('.unfollow-user').hide();
      })
      .fail(function(r) {
        
      });
  });
  
  $("#image-upload").on("change", function (e) {
    var count=1;
    $('.file-error').hide();
    $('.profile-submit').attr('disabled',false);
    var files = e.currentTarget.files;
    if (files[0] && files[0].size > 204800) {
      $('.file-error').show();
      $('.profile-submit').attr('disabled','disabled');
    }
  });
  
  $('.profile-editor').submit(function(e) {
    e.preventDefault();
    var data = new FormData(document.querySelector('.profile-editor'));
    $.ajax({
      method: 'POST',
      url: '/user',
      data: data,
      processData: false,
      contentType: false
    })
    .done(function() {
      window.location.reload();
    }).fail(function() {
      
    });
  });
</script>
@stop