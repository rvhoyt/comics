@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h1>Create a new message</h1>
        <form action="{{ route('messages.store') }}" method="post">
            {{ csrf_field() }}
            <div class="col-md-6">
                <!-- Subject Form Input -->
                <div class="form-group">
                    <label class="control-label">Thread Name</label>
                    <input type="text" class="form-control" name="subject" placeholder="Thread Name"
                           value="{{ old('subject') }}">
                </div>

                <!-- Message Form Input -->
                <div class="form-group">
                    <label class="control-label">Message</label>
                    <textarea name="message" class="form-control">{{ old('message') }}</textarea>
                </div>

                @if($users->count() > 0)
                  <label class="control-label">Thread Members</label>
                  <select name="recipients[]" class="form-control" multiple>
                    @foreach($users as $user)
                      <option value="{{$user->id}}">{!!$user->name!!}</option>
                    @endforeach
                    </select>
                @endif
        
                <!-- Submit Form Input -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary form-control">Submit</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
@stop
