@extends('layouts.app')
@section('content')
<div class="drawer">
  <?php
    $files = scandir('shapes');
    
    foreach($files as $file) {
      if ($file === '.' || $file === '..') {
        continue;
      }
      $img = file_get_contents('shapes/' . $file);
      $img = 'data:image/svg+xml;base64,' . base64_encode($img);
      ?>
      <div draggable="true" ondragstart="drag(event)" data-src="<?php echo $img;?>" class="draggableImage">
      </div>
      <?php
    }
  ?> 
</div>
    <div class="canvas-container">
      <canvas id="design" width="1000" height="600"></canvas>
      <div class="controls">
        <button type="button" onclick="setCanvas(682, 270)">Canvas 1 Line</button>
        <button type="button" onclick="setCanvas(682, 530)">Canvas Half Page</button>
        <button type="button" onclick="setCanvas(682, 1050)">Canvas Full Page</button>
        <br/><br/>
        <button type="button" onclick="groupElements()">Group</button>
        <button type="button" onclick="ungroupElements()">Ungroup</button>
        <br/><br/>
        <label>Blur: <input id="blurSlider" value="0" min="0" max="3" step="0.01" type="range" onchange="blurElement(this.value)"/></label>
        <br/><br/>
        <label>Opacity: <input id="opacitySlider" value="1" min="0" max="1" step="0.01" type="range" onchange="opacityElement(this.value)"/></label>
        <br/><br/>
        <button type="button" onclick="invertElement()">Invert</button>
        <br/><br/>
        <button type="button" onclick="flipX()">Flip Horizontally</button>
        <button type="button" onclick="flipY()">Flip Vertically</button>
        <br/><br/> 
        <button type="button" onclick="sendBackwards()">Send Backwards</button>
        <button type="button" onclick="sendToBack()">Send to Back</button>
        <br/><br/>
        <button type="button" onclick="bringForward()">Bring Forwards</button>
        <button type="button" onclick="bringToFront()">Bring to Front</button>
        <br/><br/>
        <button type="button" onclick="addTextbox()">Add Textbox</button>
        
        <div id="textEditor">
          <button type="button" onclick="startPlaceTextboxPoint()">Place text line</button>
          <label>Font Size: <input id="fontSizeSlider" value="12" min="1" max="81" step="1" type="range" onchange="fontSizeElement(this.value)"/></label>
          
          <label>Border Size: <input id="borderSlider" value="12" min="0" max="10" step="1" type="range" onchange="borderElement(this.value)"/></label>
          
        </div>
        <br/><br/>
        <button type="button" onclick="saveImage()">Save Image</button>
      </div>
    </div>
    <canvas id="hiddenCanvas"></canvas>
    
<div class="container">
    <br/>
    <div class="row card">
      <form action="/builder" method="post" class="card-body">
        <p>Press save image on right panel to enable submit button</p>
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Title" value="{{ old('title') }}" required>
        </div>
          <textarea style="display:none" class="form-control @error('url') is-invalid @enderror" id="url" name="url" placeholder="URL" value="{{ old('url') }}"></textarea>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="description" required>{{ old('description') }}</textarea>
        </div>
        <button disabled="disabled" id="submit-button" type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
</div>

@endsection

@section('myjsfile')
  <script src="https://strips-data.s3.eu-central-003.backblazeb2.com/lodash.js"></script>
  <script src="https://strips-data.s3.eu-central-003.backblazeb2.com/fabric.min.js"></script>
  <script src="js/stripBuilder.js"></script>
@stop
