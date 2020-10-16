@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
      <div class="card">
        <h1 class="card-header">
          {{ $strip->title }}
          @if (auth()->user() && auth()->user()->id === $strip->user)
            <button type="button" id="delete-strip" class="btn btn-danger btn-sm float-right">Delete</button>
            <button type="button" id="edit-strip" class="btn btn-primary btn-sm float-right">Edit</button>
          @endif
        </h1>
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
            alt="{{ $strip->title}}"/>
          <div class="col-sm-12">
            <p>{{$strip->description}}</p>
          </div>
        </div>
      </div>
    </div>
    
    <br/>
    
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
              {{$comment->user->name}}
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
  </script>
@stop