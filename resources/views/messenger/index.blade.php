@extends('layouts.app')
  @section('content')
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <a class="btn btn-primary" href="/messages/create">New Message</a>
          <hr/>
        </div>
        <div class="col-sm-12">
          <h3>Threads</h3>
            @include('messenger.partials.flash')

            @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
        </div>
      </div>
    </div>
  @stop