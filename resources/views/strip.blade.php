@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h1 class="col-sm-12">{{ $strip->title }}</h1>
        <div class="col-sm-12 text-center">
          <img
            src="https://strips.s3.eu-central-003.backblazeb2.com/{{ $strip->url }}"
            alt="{{ $strip->title}}"/>
        </div>
        <div class="col-sm-12">
          <p>{{$strip->description}}</p>
        </div>
    </div>
</div>
@endsection