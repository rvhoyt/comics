<div class="media">
    <a class="pull-left" href="/user/{{$message->user->id}}">
       @if ($message->user->profile && $message->user->profile->image) 
      <img src="https://cc-avatars.s3.eu-central-003.backblazeb2.com/{{$message->user->profile->image}}" style="width:64px"/>
      @else
      <img src="/images/profile.jpg" width="64px" style="opacity: 0.5;" alt="No Profile Image"/>
      @endif
    </a>
    <div class="media-body">
        <h5 class="media-heading">{{ $message->user->name }}</h5>
        <p>{{ $message->body }}</p>
        <div class="text-muted">
            <small>Posted {{ $message->created_at->diffForHumans() }}</small>
        </div>
    </div>
</div>