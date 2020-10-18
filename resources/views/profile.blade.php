@extends('layouts.app')

@section('content')
<div class="container">
  <h1>
    @if (auth()->user() && $user->id === auth()->user()->id)
    <button type="button" class="btn btn-primary edit-profile float-right" style="margin-top:15px;margin-bottom: 15px">Edit Profile</button>
    @endif
    {{$user->name}}
  </h1>
  <div class="row">
    @if ($profile)
    <div class="col-sm-2">
      @if ($profile->image) 
      <img src="https://cc-avatars.s3.eu-central-003.backblazeb2.com/{{$profile->image}}" style="width:150px"/>
      @endif
    </div>
    <div class="col-sm-10">
      {{$profile->description}}
    </div>
    @endif
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

@section('myjsfile')
<script>
  $('body').on('click', '.edit-profile', function() {
    $('.edit-profile').hide();
    $('.profile-editor').show();
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