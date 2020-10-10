document.addEventListener("DOMContentLoaded", function(){
  document.getElementById('description').addEventListener('keyup', function(e) {
    var len = e.target.value.length;
    document.getElementById('description-length').innerText = len;
    if (len > 1000) {
      document.getElementById('description-label').style.color = 'red';
      document.getElementById('submit-button').style.display = 'none';
    } else {
      document.getElementById('description-label').style.color = 'black';
      document.getElementById('submit-button').style.display = 'inline-block';
    }
  });
  
  design = new fabric.Canvas('design', {
    containerClass: 'design',
    stopContextMenu: true,
    preserveObjectStacking: true
  });
  design.wrapperEl.addEventListener('mousedown', startPan);
  design.setWidth(document.querySelector('.design').offsetWidth-2);
  
  setCanvas(682, 270, true);
  
  /*listeners for canvas*/
  design.on('object:modified', function() {

  });

  design.on('selection:created', function(event) {
    var objs = event.selected;
    var blurValue = objs.map((a) => a.blur).reduce((a, b) => (a + b)) / objs.length;
    document.getElementById('blurSlider').value = blurValue;
    
    var opacityValue = objs.map((a) => a.opacity).reduce((a, b) => (a + b)) / objs.length;
    document.getElementById('opacitySlider').value = opacityValue;
    
    var hasText = objs.some((el) => el.type === 'textbox');
    if (hasText) {
      var fontValue = objs.map((a) => {if(a.type === 'textbox'){return a.fontSize}}).reduce((a, b) => (a + b)) / objs.length;
      document.getElementById('fontSizeSlider').value = fontValue;
      
      var fontValue = objs.map((a) => {if(a.type === 'textbox'){return a.textboxBorderSize}}).reduce((a, b) => (a + b)) / objs.length;
      document.getElementById('borderSlider').value = fontValue;
      
      var blurValue = objs.map((a) => {if(a.type === 'textbox'){return a.blur}}).reduce((a, b) => (a + b)) / objs.length / 10;
      document.getElementById('blurSlider').value = blurValue;
      
      document.getElementById('textEditor').classList.add('show');
    } else {
      document.getElementById('textEditor').classList.remove('show');
    }
    
    
    if (objs.length === 1 && objs[0].type === 'group' && !objs.isMasked) {
      document.getElementById('ungroup-btn').disabled = false;
      document.getElementById('saveGroup-btn').disabled = false;
    } else {
      document.getElementById('ungroup-btn').disabled = true;
      document.getElementById('saveGroup-btn').disabled = true;
    }
    
    if (objs.length > 1) {
      $('#group-btn').attr('disabled', false);
    } else {
      $('#group-btn').attr('disabled', true);
    }
    
    if (objs.length === 1 && objs[0].type === 'group' && objs[0].isMasked) {
      $('#unmask-btn').attr('disabled', false);
      $('#flip-mask-btn').attr('disabled', false);
    } else {
      $('#unmask-btn').attr('disabled', true);
      $('#flip-mask-btn').attr('disabled', true);
    }
    
    if (objs.length === 2) {
      $('#mask-btn').attr('disabled', false);
    } else {
      $('#mask-btn').attr('disabled', true);
    }
    
  });

  // hook up the pan and zoom
  design.on('mouse:wheel', function(opt) {
    var delta = opt.e.deltaY;
    var zoom = design.getZoom();
    zoom *= 0.999 ** delta;
    if (zoom > 20) zoom = 20;
    if (zoom < 0.01) zoom = 0.01;
    this.setZoom(zoom);
    opt.e.preventDefault();
    opt.e.stopPropagation();
  });
  design.on('mouse:down', function(opt) {
    var evt = opt.e;
    if (this.placingPoint) {
      placeTextboxPoint(this.placingPoint, evt.layerX, evt.layerY);
      this.placingPoint = false;
    }
  });
  /*design.on('mouse:move', function(opt) {
    
  });
  design.on('mouse:up', function(opt) {
    
  });*/

  document.addEventListener('keydown', function(e) {
    if ((e.path[0].type !== 'textarea' && e.path[0].tagName !== 'INPUT') && (e.which === 8 || e.which === 46)) {
      e.preventDefault();
      deleteElements();
    } else if (e.which === 67 && e.ctrlKey) {
      copyElement();
    } else if (e.which === 86 && e.ctrlKey && (e.path[0].type !== 'textarea' && e.path[0].tagName !== 'INPUT')) {
      pasteElement();
    } else if (e.which === 40) {
      /*down*/
      var obj = design.getActiveObject();
      obj.top++;
      design.renderAll();
    } else if (e.which === 39) {
      /*right*/
      var obj = design.getActiveObject();
      obj.left++;
      design.renderAll();
    } else if (e.which === 38) {
      /*up*/
      var obj = design.getActiveObject();
      obj.top--;
      design.renderAll();
    } else if (e.which === 37) {
      /*left*/
      var obj = design.getActiveObject();
      obj.left--;
      design.renderAll();
    }
  });

  /* image adding*/
  design.on('drop', function(ev) {
    var zoom = design.getZoom();
    var shiftX = design.viewportTransform[4];
    var shiftY = design.viewportTransform[5]; 
    ev.e.preventDefault();
    var src = ev.e.dataTransfer.getData("src");
    var library = ev.e.dataTransfer.getData("library");
    var offsetX = ev.e.dataTransfer.getData("x");
    var offsetY = ev.e.dataTransfer.getData("y");
    var x = ev.e.offsetX - offsetX - shiftX;
    var y = ev.e.offsetY - offsetY - shiftY;
    if (library && libraryElements[library]) {
      src = libraryElements[library].clone(function(clone) {
        console.log(clone);
        clone.top = y / zoom;
        clone.left = x / zoom;
        design.add(clone);
      }, ['blur', 'invert', 'perPixelTargetFind', 'isMasked']);
    } else {
      addImage(src, x / zoom, y / zoom);
    }
  });
});

/*window.addEventListener("beforeunload", function(e) {
  e.preventDefault();
  e.returnValue = '';
});*/

var images = document.querySelectorAll('.draggableImage');

var imgSrcs = [];
/*convert all svg xml into images*/
[].forEach.call(images, function(div) {
  var src = div.dataset.src;
  var file = div.dataset.file;
  imgSrcs.push({src: src, file: file});
  div.innerHTML = '<img src="' + src + '"/>';
});

var libraryElements;

function updateLibrary (data) {
  function replaceSrc(data) {
    data.objects.map(function(obj) {
      if (obj.type === 'image') {
        var img = imgSrcs.find(src => src.file === obj.src);
        obj.src = img.src;
      } else if (obj.type === 'group') {
        obj = replaceSrc(obj);
      }
    });
    return data;
  }
  var ids = [];
  data = data.map(function(obj) {
    ids.push(obj.id);
    var obj = JSON.parse(obj.data);
    obj = replaceSrc(obj);
    return obj;
  });
  fabric.util.enlivenObjects(data, function(objects) {
    libraryElements = objects;
    var drawer = document.getElementById('drawer-library');
    var html = '';
    objects.forEach(function(obj, i) {
      var img = obj.toDataURL();
      html += '<div class="draggableImage" draggable="true" ondragstart="drag(event)"><img data-library="' + i + '" src="' + img + '"/><button type="button" class="library-del btn btn-sm btn-danger" onclick="deleteLibrary(' + ids[i] + ')">X</button></div>';
    })
    drawer.innerHTML = html;
  });
}

fetch('/library').then(function (response) {
  return response.ok ? response.json() : Promise.reject(response);
})
.then(updateLibrary)
.catch(function (err) {
  console.warn('Something went wrong.', err);
});

var design;
var canvasWidth;
var canvasHeight;

function setCanvas(width, height, skip = false) {
  if (!skip) {
    var check = confirm('Resizing the canvas will erase the contents.');
    if (!check) {
      return false;
    }
  }
  design.clear();
  design.backgroundColor = 'lightgrey';
  canvasWidth = width;
  canvasHeight = height;
  var rect = new fabric.Rect({
    width: canvasWidth,
    height: canvasHeight,
    fill: 'white',
    top: 100,
    left: 100,
    selectable: false,
    hoverCursor: 'cursor',
  });
  design.add(rect);
}

function saveImage() {
  design.setZoom(1);
  design.absolutePan({x:0, y:0});
  design.discardActiveObject().renderAll();
  var hiddenCanvas = document.getElementById('hiddenCanvas');
  hiddenCanvas.style.display = 'block';
  hiddenCanvas.width = canvasWidth;
  hiddenCanvas.height = canvasHeight;
  var copy = design.toCanvasElement(1, {
    left: 100,
    top: 100,
    width: canvasWidth,
    height: canvasHeight
  });
  var ctx = hiddenCanvas.getContext('2d');
  ctx.drawImage(copy, 0, 0);
  var dataUrl = hiddenCanvas.toDataURL("image/png");
  document.getElementById('url').value = dataUrl;
  document.getElementById('submit-button').disabled = false;
}


/* pull images from library to canvas*/
function drag(ev) {
  var x = ev.offsetX;
  var y = ev.offsetY;
  ev.dataTransfer.setData("src", ev.target.src);
  ev.dataTransfer.setData("library", ev.target.dataset.library);
  ev.dataTransfer.setData("x", x);
  ev.dataTransfer.setData("y", y);
} 

function addImage(src, x, y) {
  x = x ? x : 100;
  y = y ? y : 100;
  fabric.Image.fromURL(src, function(img) {
    img.set({
      left: x,
      top: y,
      blur: 0,
      invert: 0
    });

    img.perPixelTargetFind = true;

    design.add(img);
    design.discardActiveObject();
    design.setActiveObject(design._objects[design._objects.length - 1]);
  });
}

function addTextbox() {
  var textbox = new fabric.Textbox('double click to write...', {
    left: 100,
    top: 100,
    fontSize: 12,
    textboxBorderSize: 1,
    textboxBorderColor: 'black',
    showTextBoxBorder: true,
    lockScalingY: true,
    lockScalingX: true,
    backgroundColor: 'white',
    pointX: 50,
    pointY: 50,
    blur: 0
  });
  design.add(textbox);
}

/*image manipulation*/
function clipElements() {
  if (!design.getActiveObject()) {
    return;
  }
  if (design.getActiveObject().type !== 'activeSelection') {
    return;
  }
  var g = design.getActiveObject()._objects;
  g[1].clipPath = g[0];
  design.requestRenderAll();
}

function deleteElements() {
  var activeObject = design.getActiveObjects();
  design.discardActiveObject();
  design.remove(...activeObject);
  design.renderAll();
}

function groupElements() {
  if (!design.getActiveObject()) {
    return;
  }
  if (design.getActiveObject().type !== 'activeSelection') {
    return;
  }
  var g = design.getActiveObject().toGroup();
  g.perPixelTargetFind = true;
  g.blur = 0;
  g.invert = 0;
  design.requestRenderAll();
  document.getElementById('ungroup-btn').disabled = false;
  document.getElementById('saveGroup-btn').disabled = false;
}

function ungroupElements() {
  if (!design.getActiveObject()) {
    return;
  }
  if (design.getActiveObject().type !== 'group' || design.getActiveObject().isMasked) {
    return;
  }
  design.getActiveObject().toActiveSelection();
  design.requestRenderAll();
  document.getElementById('ungroup-btn').disabled = true;
  document.getElementById('saveGroup-btn').disabled = true;
}

function saveGroupElements() {
  if (!design.getActiveObject()) {
    return;
  }
  if (design.getActiveObject().type !== 'group') {
    return;
  }
  var group = design.getActiveObject();
  var data = group.toDatalessObject(['blur','invert', 'isMasked', 'perPixelTargetFind']);
  function replaceSrc(data) {
    data.objects.map(function(obj) {
      if (obj.type === 'image') {
        var img = imgSrcs.find(src => src.src === obj.src);
        obj.src = img.file;
      } else if (obj.type === 'group') {
        obj = replaceSrc(obj);
      }
    });
    return data;
  }
  data = replaceSrc(data);
  data = JSON.stringify(data);
  var payload = {
    data: data
  }
  
  fetch('/library', {
    method: 'POST',
    body: JSON.stringify(payload),
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').attributes.content.value
    }
  }).then(function (response) {
    return response.ok ? response.json() : Promise.reject(response);
  }).then(updateLibrary).catch(function (err) {
    console.warn('Something went wrong.', err);
  });
}

function deleteLibrary(id) {
  var check = confirm('Are you sure you want to delete this library element?');
  if (!check) {
    return;
  }
  fetch('/library/' + id + '/delete').then(function (response) {
    return response.ok ? response.json() : Promise.reject(response);
  }).then(updateLibrary).catch(function (err) {
    console.warn('Something went wrong.', err);
  });
}

function blurElement(value, obj) {
  var one = false;
  if (!obj) {
    one = true;
    obj = design.getActiveObject();
  }
  if (!obj) {
    return;
  }
  if (obj.type === 'textbox') {
    obj.blur = value * 10;
    obj.dirty = true;
  } else if (obj.type === 'activeSelection') {
    obj.forEachObject(function(el){
      blurElement(value, el);
    });
  } else {
    obj.blur = value;
    obj.dirty = true;
  }
  if (one) {
    design.renderAll();
  }
}

function invertElement(obj) {
  var one = false;
  if (!obj) {
    one = true;
    obj = design.getActiveObject();
  }
  if (obj.type === 'textbox') {
    return;
  }
  if (obj.type === 'activeSelection') {
    obj.forEachObject(function(el){
      invertElement(el);
    });
  } else {
    obj.invert = obj.invert ? 0 : 1;
    obj.dirty = true;
  }
  if (one) {
    design.renderAll();
  }
}

function opacityElement(value, obj) {
  if (!obj) {
    obj = design.getActiveObject();
  }
  if (obj.type === 'activeSelection') {
    obj._objects.forEach(function(el){
      opacityElement(value, el);
    });
  } else {
    obj.opacity = value;
  }
  design.renderAll();
}

function fontSizeElement(value, obj) {
  if (!obj) {
    obj = design.getActiveObject();
  }
  if (obj.type === 'activeSelection' || obj.type === 'group') {
    obj._objects.forEach(function(el){
      fontSizeElement(value, el);
    });
  } else {
    obj.fontSize = value;
  }
  design.renderAll();
}

function borderElement(value, obj) {
  if (!obj) {
    obj = design.getActiveObject();
  }
  if (obj.type === 'activeSelection' || obj.type === 'group') {
    obj._objects.forEach(function(el){
      fontSizeElement(value, el);
    });
  } else {
    obj.textboxBorderSize = value;
  }
  design.renderAll();
}


function sendBackwards() {
  var activeObject = design.getActiveObject();
  if (activeObject) {
    var background = design._objects[0];
    design.sendBackwards(activeObject);
    background.sendToBack();
    design.renderAll();
  }
};

function sendToBack() {
  var activeObject = design.getActiveObject();
  if (activeObject) {
    var background = design._objects[0];
    design.sendToBack(activeObject);
    background.sendToBack();
    design.renderAll();
  }
};

function bringForward() {
  var activeObject = design.getActiveObject();
  if (activeObject) {
    design.bringForward(activeObject);
    design.renderAll();
  }
};

function bringToFront() {
  var activeObject = design.getActiveObject();
  if (activeObject) {
    design.bringToFront(activeObject);
    design.renderAll();
  }
};

function flipX() {
  var obj = design.getActiveObject();
  obj.flipX = !obj.flipX;
  design.renderAll();
}

function flipY() {
  var obj = design.getActiveObject();
  obj.flipY = !obj.flipY;
  design.renderAll();
}

function maskElements() {
  var active = design.getActiveObject();
  if (active._objects.length !== 2) {
    return;
  }
  groupElements();
  active = design.getActiveObject();
  active.isMasked = true;
  active._objects[1].globalCompositeOperation = 'source-out';
  design.discardActiveObject();
  design.renderAll();
}

function flipMask() {
  var active = design.getActiveObject();
  if (!active || !active.isMasked) {
    return;
  }
  if (active._objects[1].globalCompositeOperation === 'source-out') {
    active._objects[1].globalCompositeOperation = 'source-in';
  } else {
    active._objects[1].globalCompositeOperation = 'source-out';
  }
  active.dirty = true;
  design.renderAll();
}

function unmaskElements() {
  var active = design.getActiveObject();
  console.log(!active.isMasked);
  if (!active || !active.isMasked) {
    return;
  }
  active._objects[1].globalCompositeOperation = 'source-over';
  active.isMasked = false;
  ungroupElements();
  design.renderAll();
}
  
function copyElement() {
  var active = design.getActiveObject();
  design.getActiveObject().clone(function(cloned) {
    _clipboard = cloned;
  }, ['invert', 'blur', 'perPixelTargetFind']);
}

function pasteElement() {
  // clone again, so you can do multiple copies.
  _clipboard.clone(function(clonedObj) {
    var left = clonedObj.left + 10;
    var top = clonedObj.top + 10;
    clonedObj.set({
      left: clonedObj.left + 10,
      top: clonedObj.top + 10,
      evented: true,
      perPixelTargetFind: true,
      blur: _clipboard.blur,
      invert: _clipboard.invert
    });
    var objs = [];
    if (clonedObj.type === 'activeSelection') {
      // active selection needs a reference to the canvas.
      clonedObj.design = design;
      clonedObj.forEachObject(function(obj, i) {
        obj.set({
          top: obj.top + top,
          left: obj.left + left,
          blur: _clipboard._objects[i].blur,
          invert: _clipboard._objects[i].invert
        });
        objs.push(obj);
        design.add(obj);
      });
    } else {
      objs.push(clonedObj);
      design.add(clonedObj);
    }
    _clipboard.top += 10;
    _clipboard.left += 10;
    var sel = new fabric.ActiveSelection(objs, {
      canvas: design,
    });
    design.discardActiveObject();
    design.setActiveObject(sel);
    design.renderAll();
  }, ['blur', 'invert', 'perPixelTargetFind']);
}

function startPlaceTextboxPoint() {
  var obj = design.getActiveObject();
  if (obj.type === 'textbox') {
    design.placingPoint = obj;
  }
}

function placeTextboxPoint(obj, x, y) {
  if (!obj || obj.type !== 'textbox') {
    return;
  }
  obj.pointX = x - obj.left;
  obj.pointY = y - obj.top;
  design.renderAll();
}

function startPan(event) {
    if (event.button != 2) {
        return;
    }
    var x0 = event.screenX,
        y0 = event.screenY;

    function continuePan(event) {
        var x = event.screenX,
            y = event.screenY;
        design.relativePan({
            x: x - x0,
            y: y - y0
        });
        x0 = x;
        y0 = y;
    }

    function stopPan(event) {
      window.removeEventListener('mousemove', continuePan);
      window.removeEventListener('mouseup', stopPan);
    };
    window.addEventListener('mousemove', continuePan);
    window.addEventListener('mouseup', stopPan);
};


/*customize textbox*/
var originalTextboxRender = fabric.Textbox.prototype._render;
fabric.Textbox.prototype._render = function(ctx) {
  
  ctx.filter = 'blur(' + this.blur + 'px)';
  
  var w = this.width,
    h = this.height,
    x = -this.width / 2,
    y = -this.height / 2;
  ctx.beginPath();
  ctx.moveTo(x, y);
  ctx.lineTo(x + w, y);
  ctx.lineTo(x + w, y + h);
  ctx.lineTo(x, y + h);
  ctx.lineTo(x, y);
  ctx.closePath();
  
  var stroke = ctx.strokeStyle;
  ctx.lineWidth = this.textboxBorderSize;
  ctx.strokeStyle = this.textboxBorderColor;
  ctx.stroke();
  
  /*text line*/
  ctx.beginPath();
  var startX;
  if (this.pointX < 0) {
    startX = x;
  } else if (this.pointX < w) {
    startX = x + w/2;
  } else {
    startX = x + w;
  }
  var startY;
  if (this.pointY < 0) {
    startY = y;
  } else if (this.pointY < h) {
    startY = y + h/2;
  } else {
    startY = y + h;
  }
  ctx.moveTo(startX, startY);
  ctx.lineTo(this.pointX, this.pointY);
  ctx.closePath();
  ctx.stroke();
  originalTextboxRender.call(this, ctx);
  
  ctx.strokeStyle = stroke;
  ctx.filter = 'none';
}

var originalRenderCache = fabric.Object.prototype.renderCache;
fabric.Image.prototype.renderCache = function() {
  if (this._cacheCanvas && this.dirty) {
    this._cacheContext.filter = 'blur(' + (this.blur * 10) + 'px)';
    this._cacheContext.filter += 'invert(' + this.invert + ')';
  }
  
  originalRenderCache.call(this);
}

var originalGroupRenderCache = fabric.Group.prototype.renderCache;
fabric.Group.prototype.renderCache = function() {
  if (this._cacheCanvas && this.dirty) {
    this._cacheContext.filter = 'blur(' + (this.blur * 10) + 'px)';
    this._cacheContext.filter += 'invert(' + this.invert + ')';
  }
  
  originalGroupRenderCache.call(this);
}

fabric.Image.prototype.needsItsOwnCache = function() {return true};

fabric.Object.prototype.stateProperties = fabric.Object.prototype.stateProperties.concat(['active', 'blur', 'invert']);
fabric.Object.prototype.cacheProperties = fabric.Object.prototype.cacheProperties.concat(['active', 'blur', 'invert']);