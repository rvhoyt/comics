@extends('layouts.app')
@section('content')

<div id="builder">
  <div class="tabbed-area">
    <button class="btn"
      :class="{active: selectedFolder === folder}"
      v-for="folder in libaryFolders"
      @click="selectedFolder = folder">@{{folder}}</button>
  </div>
  <div class="drawer">
    <?php
      $folders = ['items', 'shapes', 'characters', 'pieces'];
      
      foreach($folders as $folder) {
        $files = scandir('shapes/' . $folder);
        ?>
        <div v-show="selectedFolder === '<?php echo ucfirst($folder);?>'" class="drawer-container" ref="selectedFolder">
        <?php
        
        foreach($files as $file) {
          if ($file === '.' || $file === '..') {
            continue;
          }
          $filename = 'shapes/' . $folder . '/' . $file;
          $img = file_get_contents('shapes/' . $folder . '/' . $file);
          $img = 'data:image/svg+xml;base64,' . base64_encode($img);
          ?>
          <div draggable="true" @dragstart="drag" data-file="<?php echo $filename;?>" data-src="<?php echo $img;?>" class="draggableImage">
          </div>
          <?php
        }
        ?>
        </div>
        <?php
      }
    ?>
    <div class="drawer-container" v-show="selectedFolder === 'Library'">
      <div class="draggableImage" draggable="true" @dragstart="drag" v-for="(obj, i) in libraryElements">
        <img :data-library="i" :src="obj.thumbnail"/>
        <button type="button" class="library-del btn btn-sm btn-danger" @click="deleteLibrary(obj.libraryId)">X</button>
      </div>
    </div>
  </div>
  
  <button style="position:absolute;z-index:10" v-if="!mainView" @click="exitFrame">Back to Main Canvas</button>
  <div class="canvas-container">
    <canvas id="design" :width="pageWidth" height="600"></canvas>
    <div ref="framesHolder">
    
    </div>
    
    <div class="controls">
        <label>Zoom:
          <input v-model="zoomValue" min="0.5" max="3" step="0.01" type="range" @change="zoomCanvas($event.target.value)"/>
        </label>
        <span>@{{(zoomValue * 100).toFixed(0)}}%</span>
        <br/>
        <button type="button" @click="setCanvas(682, 270)" :disabled="!mainView">Canvas 1 Line</button>
        <button type="button" @click="setCanvas(682, 530)" :disabled="!mainView">Canvas Half Page</button>
        <button type="button" @click="setCanvas(682, 1050)" :disabled="!mainView">Canvas Full Page</button>
        <br/>
        <button type="button" @click="addFrame(220, 260)" :disabled="!mainView">Frame 1x</button>
        <button type="button" @click="addFrame(446, 260)" :disabled="!mainView">Frame 2x</button>
        <button type="button" @click="addFrame(672, 260)" :disabled="!mainView">Frame 3x</button>
        <br/><br/>
        <div class="swatches" v-if="activeSelectionType === 'image'">
          <div class="swatch" v-for="color in colors" :style="'background:#' + color" @click="colorElement(color)"></div>
        </div>
        <button type="button" @click="groupElements" :disabled="activeSelectionCount < 2">Group</button>
        <button type="button" @click="ungroupElements" :disabled="activeSelectionType !== 'group'">Ungroup</button>
        <br/>
        <button type="button" @click="maskElements" :disabled="activeSelectionCount !== 2">Mask</button>
        <button type="button" @click="flipMask" :disabled="!activeSelectionMasked">Flip Mask</button>
        <button type="button" @click="unmaskElements" :disabled="!activeSelectionMasked">Unmask</button>
        <br/>
        <button type="button" @click="saveGroupElements" :disabled="activeSelectionType !== 'group'">Save to Library</button>
        <br/>
        <label>Blur:
          <input v-model="blurValue" min="0" max="3" step="0.01" type="range" @change="blurElement($event.target.value)"/>
        </label>
        <span>@{{(blurValue * 100).toFixed(0)}}%</span>
        <br/>
        <label>Opacity:
          <input v-model="opacityValue" min="0" max="1" step="0.01" type="range" @change="opacityElement($event.target.value)"/>
        </label>
        <span>@{{(opacityValue * 100).toFixed(0)}}%</span>
        <br/>
        <button type="button" @click="duplicateElement()" :disabled="!activeSelectionCount">Duplicate</button>
        <button type="button" @click="deleteElements()" :disabled="!activeSelectionCount">Delete</button>
        <button type="button" @click="invertElement()" :disabled="!activeSelectionCount">Invert</button>
        <br/><br/>
        <button type="button" @click="flipX()" :disabled="!activeSelectionCount">Flip Horizontally</button>
        <button type="button" @click="flipY()" :disabled="!activeSelectionCount">Flip Vertically</button>
        <br/><br/> 
        <button type="button" @click="sendBackwards()" :disabled="!activeSelectionCount">Send Backwards</button>
        <button type="button" @click="sendToBack()" :disabled="!activeSelectionCount">Send to Back</button>
        <br/><br/>
        <button type="button" @click="bringForward()" :disabled="!activeSelectionCount">Bring Forwards</button>
        <button type="button" @click="bringToFront()" :disabled="!activeSelectionCount">Bring to Front</button>
        <br/><br/>
        <button type="button" @click="addTextbox()">Add Textbox</button>
        <br/>
        <label class="custom-file-upload">Load Image
          <input type="file" class="btn" @change="loadSVG" accept="image/png, image/gif, image/jpeg, image/svg+xml"/>
        </label>
        
        <div v-if="activeSelectionType === 'textbox'">
          <label>Font Size:
            <input v-model="fontSizeValue" min="1" max="81" step="1" type="range" @change="textboxProperty('fontSize', $event.target.value)"/>
          </label>
          <span>@{{fontSizeValue}}</span>
          <br/>
          <label>Border Size:
            <input v-model="borderSizeValue" min="0" max="10" step="1" type="range" @change="textboxProperty('textboxBorderSize', $event.target.value)"/>
          </label>
          <span>@{{borderSizeValue}}</span>
          <br/>
          <label>Roundness:
            <input v-model="radiusValue" min="0" max="100" step="1" type="range" @change="textboxProperty('radius', $event.target.value)"/>
          </label>
          <span>@{{radiusValue}}</span>
          
          <br/>
          <button @click="textboxProperty('textAlign', 'left')" type="button">Left</button>
          <button @click="textboxProperty('textAlign', 'center')" type="button">Center</button>
          <button @click="textboxProperty('textAlign', 'justify')" type="button">Justify</button>
          <button @click="textboxProperty('textAlign', 'right')" type="button">Right</button>
          
          <select :value="fontFamilyValue" @change="textboxProperty('fontFamily', $event.target.value)">
            <option>Arial</option>
            <option>Comic Sans MS</option>
            <option>Times New Roman</option>
            <option selected>Verdana</option>
          </select>
          
        </div>
        <br/><br/>
        <button type="button" @click="saveImage()">Save Image</button>
      </div>
      <div class="canvas-spacer"></div>
    </div>
    
  <div class="container">
    <br/>
    <div class="row card">
      <form class="card-body" @submit="handleSubmission">
        <p v-if="error" class="alert alert-danger">@{{error}}</p>
        <p>Press save image on right panel to enable submit button</p>
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" placeholder="Title" required v-model="title">
        </div>
          
          <textarea style="display:none" class="form-control" v-model="url" name="url" placeholder="URL" ></textarea>
          
        <div class="form-group">
            <label id="description-label" for="description" :class="{'text-danger':description.length > 1000}">Description  <span id="description-length">@{{description.length}}</span>/1000</label>
            <textarea class="form-control" name="description" placeholder="description" required v-model="description">{{ old('description') }}</textarea>
        </div>
        <button :disabled="submitting || !description.length || description.length > 1000 || !url" type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
    
    <div class="col-sm-12 card">
      <div class="card-body" v-if="url">
        <label>Apply post-effects <select v-model="postFx" @change="applyPostFx">
          <option value="">None</option>
          <option value="halftone">Color Halftone</option>
          <option value="denoise">Denoise</option>
          <option value="dot">Dot Screen</option>
          <option value="hex">Hexaganate</option>
          <option value="hue">Hue / Saturation</option>
          <option value="sepia">Sepia</option>
        </select>
        <div>
          <label v-if="postFx === 'halftone' || postFx === 'dot'">Dot Size:
            <input type="range" min="1" max="20" v-model.number="fxOptions.dotSize" @change="applyPostFx"/> @{{fxOptions.dotSize}}
          </label>
          <label v-if="postFx === 'hex'">Hex Size:
            <input type="range" min="1" max="40" v-model.number="fxOptions.hexSize" @change="applyPostFx"/> @{{fxOptions.hexSize}}
          </label>
          <label v-if="postFx === 'denoise'">Strength:
            <input type="range" min="1" max="30" v-model.number="fxOptions.denoiseAmount" @change="applyPostFx"/> @{{fxOptions.denoiseAmount}}
          </label>
          <div v-if="postFx === 'hue'">
            <label>Hue:
              <input type="range" min="-1" max="1" step="0.01" v-model.number="fxOptions.hue" @change="applyPostFx"/> @{{fxOptions.hue}}
            </label>
            <br/>
            <label>Saturation:
              <input type="range" min="-1" max="1" step="0.01" v-model.number="fxOptions.saturation" @change="applyPostFx"/> @{{fxOptions.saturation}}
            </label>
          </div>
        </div>
        <br/>
        <img :src="url" alt="preview" style="width:682px" ref="finalImage"/>
      </div>
    </div>
    <canvas style="display:none;" id="library-temp"></canvas>
  </div>
</div>


@endsection

@section('myjsfile')
  <script src="js/vue.min.js"></script>
  <script src="js/fabric.min.js"></script>
  <script src="js/glfx.js"></script>
  <script src="js/stripBuilder.js"></script>
@stop
