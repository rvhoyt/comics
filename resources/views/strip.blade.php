@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-1">
              <div class="card text-center" style="width:50px;margin-right: 15px;height:100%;">
              <strong style="font-size:150%" class="likes">{{count($likes)}}</strong>
              LIKES
              </div>
            </div>
            <div class="col-sm-11">
              <h1>
              {{ $strip->title }}
              </h1>
              @if (auth()->user() && auth()->user()->id === $strip->user)
                <button type="button" id="delete-strip" class="btn btn-danger btn-sm float-right">Delete</button>
                <button type="button" id="edit-strip" class="btn btn-primary btn-sm float-right">Edit</button>
              @endif
              <div>
                <a href="/user/{{$author->id}}">{{$author->name}}</a> &nbsp;&nbsp;&nbsp;{{$strip->created_at->format('j F, Y')}}
              </div>
            </div>
          </div>
        </div>
        <div class="card-body text-center">
          @if (auth()->user() && auth()->user()->id === $strip->user)
            <form id="edit-form" class="form" method="POST" style="display:none;">
              @csrf
              <div class="form-group">
                <label for="title">Title</label>
                <input name="title" class="form-control" value="{{$strip->title}}"/>
              </div>
              <div class="form-group">
              <label for="description">Description</label>
                <textarea name="description" class="form-control">{{$strip->description}}</textarea>
              </div>
              <button class="btn btn-primary">Submit</button>
            </form>
          @endif
          <img
            src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}"
            alt="{{ $strip->title}}" width="682px"/>
          <div class="col-sm-12">
            <p>{{$strip->description}}</p>
          </div>
          <div class="col-sm-12">
            <button type="button" class="@if(!$alreadyLiked) like @else disabled @endif btn btn-primary float-right">
              Like
              <span class="badge badge-light likes">{{count($likes)}}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <br/>
    
    @auth
    <div class="row">
      <div class="col-sm-6">
        <form class="card form" method="POST" action="{{$strip->id}}/comment">
          <div class="card-header">Add Comment</div>
          <div class="card-body">
            @csrf
            <textarea name="comment" class="form-control" required></textarea>
            <br/>
            <button class="btn btn-primary">Comment</button>
          </div>
        </form>
      </div>
    </div>
    @else
    <div class="row">
      <div class="col-sm-12">Please Login to comment</div>
    </div>
    @endif
    
    <div class="row">
      <div class="col-sm-6">
      @foreach ($comments as $comment)
        <div class="comment">
          <div class="card">
            <div class="card-header">
              <span class="float-right">
                {{$comment->created_at->format('d/m/Y')}}
              </span>
              @if ($comment->created_ad !== $comment->updated_at)
                <span class="float-right">Edited&nbsp;&nbsp;</span>
              @endif
              @if($comment->user->profile && $comment->user->profile->image)
                <img src="https://cc-avatars.s3.eu-central-003.backblazeb2.com/{{$comment->user->profile->image}}" alt="{{$comment->user->name}}" height="30px"/>
              @else
                <img src="/images/profile.jpg" height="30px" style="opacity: 0.5;" alt="No Profile Image"/>
              @endif
              <a href="/user/{{$comment->user->id}}">{{$comment->user->name}}</a>
            </div>
            <div class="card-body">
              <span class="comment-text">{{$comment->comment}}</span>
              <form style="display:none" method="POST" action="/comment/{{$comment->id}}">
              @csrf
                <textarea class="form-control" name="comment">{{$comment->comment}}</textarea>
                <button class="btn btn-primary">Save</button>
              </form>
              @if (auth()->user() && auth()->user()->id === $comment->user_id)
                <div>
                  <a href="/comment/{{$comment->id}}/delete" class="float-right btn btn-danger btn-small">Delete</a>
                  <button type="button" class="float-right btn btn-secondary btn-small edit-comment">Edit</button>
                </div>
              @endif
            </div>
          </div>
        </div>
        <br/>
      @endforeach
      </div>
    </div>
</div>
@endsection

@section('myjsfile')
  <script>
  document.addEventListener("DOMContentLoaded", function(){
    var edits = document.querySelectorAll('.comment');
    [].forEach.call(edits, function(el) {
      var btn = el.querySelector('.edit-comment');
      window.btn = btn;
      if (btn) {
        btn.addEventListener('click', function() {
          console.log(2);
          el.querySelector('form').style.display = 'block';
          el.querySelector('.comment-text').style.display = 'none';
        });
      }
    });
  });
  $('body').on('click', '#edit-strip', function() {
    $('#edit-form').show();
  });
  $('body').on('click', '#delete-strip', function() {
    var check = confirm('Are you sure you want to delete this strip?');
    if (check) {
      window.location = window.location + '/delete';
    }
  });
  
  $('body').on('click', '.like', function() {
    $('.like').removeClass('like').addClass('disabled');
    $.get(window.location.pathname + '/like').done(function() {
      var likes = parseInt($('.likes').eq(0).text());
      likes++;
      $('.likes').text(likes);
    });
  });
  </script>
@stop