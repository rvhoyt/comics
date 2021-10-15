@foreach($comments as $comment)
    <a class="dropdown-item" href="/strips/{{$comment->strip_id}}">{{$comment->created_at->format('d/m/Y')}} - {{$comment->comment}}</a>
@endforeach