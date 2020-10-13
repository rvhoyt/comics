let design = undefined;
let mainCanvas = undefined;
let frames = [];
const Builder = {
  data() {
    return {
      activeSelectionCount: 0,
      activeSelectionMasked: false,
      activeSelectionType: undefined,
      borderSizeValue: 0,
      blurValue: 1,
      canvas: undefined,
      canvasHeight: undefined,
      canvasWidth: undefined,
      customProperties: ['active', 'blur', 'invert', 'isMasked', 'textboxBorderSize', 'textboxBorderColor', 'radius', 'pointX', 'pointY'],
      description: '',
      fontFamilyValue: 'Verdana',
      fontSizeValue: 12,
      frames: [],
      frameView: undefined,
      imgSrcs: [],
      libraryElements: [],
      libaryFolders: ['Items', 'Shapes', 'Characters', 'Pieces', 'Library'],
      mainCanvas: undefined,
      mainView: true,
      opacityValue: 1,
      radiusValue: 0,
      selectedFolder: 'Items',
      title: '',
      url: '',
      zoomValue: 1
    }
  },
  watch: {

  },
  computed: {
    pageWidth: () => document.body.clientWidth - 250
  },
  methods: {
    addFrame: function(width, height, x, y) {
      if (!x) {
        x = 105;
        y = 105;
      }
      var ctrl = this;
      var id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
      var rect = new fabric.Rect({
        top: x,
        left: y,
        width: width,
        height: height,
        stroke: 'black',
        fill: 'white',
        frameId: id,
        isFrame: true,
        blur:0,
        opacity: 1,
        inverted: 0
      });
      var frame = {
        id: id,
        placeholder: rect,
        canvas: undefined
      };
      frames.push(frame);
      var newCanvas = document.createElement('canvas');
      frame.el = newCanvas;
      this.$refs.framesHolder.appendChild(newCanvas);
      newCanvas.width = this.pageWidth;
      newCanvas.height = 600;
      
      frame.canvas = new fabric.Canvas(newCanvas, {
        controlsAboveOverlay: true,
        containerClass: 'design',
        stopContextMenu: true,
        preserveObjectStacking: true
      });
      
      newCanvas.parentElement.style.zIndex = 0;
      ctrl.initCanvas(frame.canvas);
      ctrl.setCanvas(width, height, true, frame.canvas);
      design.add(rect);
    },
    addImage: function(src, x, y) {
      var ctrl = this;
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
    },
    addTextbox: function() {
      var textbox = new fabric.Textbox('double click to write...', {
        left: 100,
        top: 100,
        fontSize: 12,
        textboxBorderSize: 1,
        textboxBorderColor: 'black',
        backgroundColor: 'white',
        pointX: 50,
        pointY: 50,
        blur: 0,
        invert: 0,
        radius: 0,
        textAlign: 'center',
        fontFamily: 'Verdana'
      });
      textbox.setControlsVisibility({
        mt: false,
        mb: false,
        bl: false,
        br: false,
        tl: false,
        tr: false,
      });
      design.add(textbox);
    },
    blurElement: function(value, obj) {
      var ctrl = this;
      var one = false;
      if (!obj) {
        one = true;
        obj = design.getActiveObject();
      }
      if (!obj) {
        return;
      }
      if (obj.type === 'activeSelection') {
        obj.forEachObject(function(el){
          ctrl.blurElement(value, el);
        });
      } else {
        obj.blur = value;
        obj.dirty = true;
      }
      if (one) {
        design.renderAll();
      }
    },
    bringForward: function() {
      var activeObject = design.getActiveObject();
      if (activeObject) {
        design.bringForward(activeObject);
        design.renderAll();
      }
    },
    bringToFront: function() {
      var activeObject = design.getActiveObject();
      if (activeObject) {
        design.bringToFront(activeObject);
        design.renderAll();
      }
    },
    copyElement: function() {
      var ctrl = this;
      var active = design.getActiveObject();
      if (!active) {
        return;
      }
      return design.getActiveObject().clone(function(cloned) {
        ctrl._clipboard = cloned;
      }, ctrl.customProperties);
    },
    deleteElements: function() {
      var ctrl = this;
      var activeObject = design.getActiveObjects();
      activeObject.forEach(function(obj) {
        if (obj.isFrame) {
          frames.some(function(frame, i, a) {
            if (obj.frameId === frame.placeholder.frameId) {
              frame.canvas.clear();
              frame.canvas.dispose();
              ctrl.$refs.framesHolder.removeChild(frame.el);
              return true;
            }
          });
        }
      });
      design.discardActiveObject();
      design.remove(...activeObject);
      design.renderAll();
    },
    deleteLibrary: function(id) {
      var check = confirm('Are you sure you want to delete this library element?');
      if (!check) {
        return;
      }
      fetch('/library/' + id + '/delete').then(function (response) {
        return response.ok ? response.json() : Promise.reject(response);
      }).then(this.updateLibrary).catch(function (err) {
        console.warn('Something went wrong.', err);
      });
    },
    drag: function(ev) {
      var x = ev.offsetX;
      var y = ev.offsetY;
      ev.dataTransfer.setData("src", ev.target.src);
      ev.dataTransfer.setData("library", ev.target.dataset.library);
      ev.dataTransfer.setData("x", x);
      ev.dataTransfer.setData("y", y);
    },
    drop: function(ev) {
      var ctrl = this;
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
      if (library && this.libraryElements[library]) {
        src = this.libraryElements[library].clone(function(clone) {
          clone.top = y / zoom;
          clone.left = x / zoom;
          design.add(clone);
          design.setActiveObject(clone);
          ctrl.ungroupElements();
          ctrl.groupElements();
        }, ctrl.customProperties);
      } else {
        ctrl.addImage(src, x / zoom, y / zoom);
      }
    },
    duplicateElement: function() {
      this.copyElement();
      setTimeout(this.pasteElement, 50);
    },
    enterFrame: function(id) {
      var ctrl = this;
      var frame = frames.find((frame) => frame.id === id);
      design.lowerCanvasEl.parentElement.style.zIndex = '1';
      design = frame.canvas;
      design.lowerCanvasEl.parentElement.style.zIndex = '2';
      ctrl.frameView = id;
      ctrl.mainView = false;
      var zoom = mainCanvas.getZoom();
      var x = 100 - frame.placeholder.left - (mainCanvas.viewportTransform[4] / zoom);
      var y = 100 - frame.placeholder.top - (mainCanvas.viewportTransform[5] / zoom);
      x = x * zoom;
      y = y * zoom;
      
      window.f = frame.canvas;
      
      frame.canvas.setZoom(zoom);
      frame.canvas.absolutePan({x:x, y:y});
    },
    exitFrame: function() {
      var ctrl = this;
      var frame = frames.find((frame) => frame.id === ctrl.frameView);
      frame.canvas.setZoom(1);
      frame.canvas.absolutePan({x:0, y:0});
      var img = design.toCanvasElement(1, {
        left: 100,
        top: 100,
        width: frame.placeholder.width,
        height: frame.placeholder.height
      });
      frame.placeholder.fill = new fabric.Pattern({
        source: img,
        repeat: 'no-repeat'
      });
      frame.placeholder.dirty = true;
      design.lowerCanvasEl.parentElement.style.zIndex = '0';
      design = mainCanvas;
      design.lowerCanvasEl.parentElement.style.zIndex = '2';
      ctrl.mainView = true;
      design.renderAll();
    },
    flipMask: function() {
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
    },
    flipX: function() {
      var obj = design.getActiveObject();
      obj.flipX = !obj.flipX;
      design.renderAll();
    },
    flipY: function() {
      var obj = design.getActiveObject();
      obj.flipY = !obj.flipY;
      design.renderAll();
    },
    groupElements: function() {
      var active = design.getActiveObject();
      if (!active) {
        return;
      }
      if (active.type !== 'activeSelection') {
        return;
      }
      var g = active.toGroup();
      g.perPixelTargetFind = true;
      g.blur = 0;
      g.invert = 0;
      function handleMasks (obj) {
        if (obj.isMasked) {
          obj.shouldCache = function() {return true};
        }
        if (obj.type === 'group') {
          obj.forEachObject(function(o) {
            handleMasks(o);
          });
        }
        return obj;
      }
      g = handleMasks(g);
      design.requestRenderAll();
      this.updateActiveSelectionType();
    },
    handleShortcuts: function(e) {
      if (e.path[0].type === 'textarea' || e.path[0].tagName === 'INPUT') {
        return;
      } else if ((e.path[0].type !== 'textarea' && e.path[0].tagName !== 'INPUT') && (e.which === 8 || e.which === 46)) {
        e.preventDefault();
        this.deleteElements();
      } else if (e.which === 67 && e.ctrlKey) {
        this.copyElement();
        e.preventDefault();
      } else if (e.which === 86 && e.ctrlKey && (e.path[0].type !== 'textarea' && e.path[0].tagName !== 'INPUT')) {
        this.pasteElement();
        e.preventDefault();
      } else if (e.which === 40) {
        e.preventDefault();
        /*down*/
        var obj = design.getActiveObject();
        obj.top++;
        if (e.shiftKey) {
          obj.top += 9;
        }
        design.renderAll();
      } else if (e.which === 39) {
        e.preventDefault();
        /*right*/
        var obj = design.getActiveObject();
        obj.left++;
        if (e.shiftKey) {
          obj.left += 9;
        }
        design.renderAll();
      } else if (e.which === 38) {
        e.preventDefault();
        /*up*/
        var obj = design.getActiveObject();
        obj.top--;
        if (e.shiftKey) {
          obj.top -= 9;
        }
        design.renderAll();
      } else if (e.which === 37) {
        e.preventDefault();
        /*left*/
        var obj = design.getActiveObject();
        obj.left--;
        if (e.shiftKey) {
          obj.left -= 9;
        }
        design.renderAll();
      }
    },
    initCanvas: function(canvas) {
      var ctrl = this;
      function startPan(event) {
        if (event.button != 2) {
            return;
        }
        var x0 = event.screenX,
            y0 = event.screenY;

        function continuePan(event) {
            var x = event.screenX,
                y = event.screenY;
            canvas.relativePan({
              x: x - x0,
              y: y - y0
            });
            if (!ctrl.mainView) {
              mainCanvas.relativePan({
                x: x - x0,
                y: y - y0
              });
            }
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
      
      canvas.wrapperEl.addEventListener('mousedown', startPan);
      // hook up the pan and zoom
      canvas.on('mouse:wheel', function(opt) {
        var delta = opt.e.deltaY;
        var zoom = design.getZoom();
        zoom *= 0.999 ** delta;
        if (zoom > 20) zoom = 20;
        if (zoom < 0.01) zoom = 0.01;
        if (zoom > 3) {
          zoom = 3;
        }
        this.setZoom(zoom);
        if (!ctrl.mainView) {
          mainCanvas.setZoom(zoom);
        }
        ctrl.zoomValue = zoom;
        opt.e.preventDefault();
        opt.e.stopPropagation();
      });
      
      canvas.on('mouse:down', function(opt) {
        var evt = opt.e;
        if (this.placingPoint) {
          ctrl.placeTextboxPoint(this.placingPoint, evt.layerX, evt.layerY);
          this.placingPoint = false;
        }
      });
      
      canvas.on('mouse:dblclick', function(ev) {
        var target = ev.target;
        if (ev.target && ev.target.isFrame) {
          ctrl.enterFrame(ev.target.frameId);
        }
      });

      /* image adding*/
      canvas.on('drop', this.drop);
      
      canvas.on('selection:updated', this.updateActiveSelectionType);
      canvas.on('selection:created', this.updateActiveSelectionType);
      canvas.on('selection:cleared', this.updateActiveSelectionType);
      
    },
    invertElement: function(obj) {
      var ctrl = this;
      var one = false;
      if (!obj) {
        one = true;
        obj = design.getActiveObject();
      }
      if (obj.type === 'activeSelection') {
        obj.forEachObject(function(el){
          ctrl.invertElement(el);
        });
      } else {
        obj.invert = obj.invert ? 0 : 1;
        obj.dirty = true;
      }
      if (one) {
        design.renderAll();
      }
    },
    maskElements: function() {
      var active = design.getActiveObject();
      if (active._objects.length !== 2) {
        return;
      }
      this.groupElements();
      active = design.getActiveObject();
      active.isMasked = true;
      active._objects[1].globalCompositeOperation = 'source-out';
      design.discardActiveObject();
      design.renderAll();
      this.updateActiveSelectionType();
    },
    opacityElement: function(value, obj) {
      var ctrl = this;
      if (!obj) {
        obj = design.getActiveObject();
      }
      if (obj.type === 'activeSelection') {
        obj._objects.forEach(function(el){
          ctrl.opacityElement(value, el);
        });
      } else {
        obj.opacity = value;
      }
      design.renderAll();
    },
    pasteElement: function() {
      var ctrl = this;
      if (!this._clipboard) {
        return;
      }
      // clone again, so you can do multiple copies.
      this._clipboard.clone(function(clonedObj) {
        var left = clonedObj.left + 10;
        var top = clonedObj.top + 10;
        clonedObj.set({
          left: clonedObj.left + 10,
          top: clonedObj.top + 10,
          evented: true
        });
        var objs = [];
        function recursiveSet (obj, old) {
          if (obj.isMasked) {
            obj.shouldCache = function() {return true};
          }
          if (obj.type === 'group') {
            obj.forEachObject(function(o, i) {
              recursiveSet(o, old._objects[i]);
            });
          }
          return obj;
        }
        if (clonedObj.type === 'activeSelection') {
          // active selection needs a reference to the canvas.
          clonedObj.canvas = design;
          clonedObj = recursiveSet(clonedObj, ctrl._clipboard);
          clonedObj.forEachObject(function(obj, i) {
            objs.push(obj);
            design.add(obj);
          });
        } else {
          clonedObj = recursiveSet(clonedObj, ctrl._clipboard);
          objs.push(clonedObj);
          design.add(clonedObj);
        }
        ctrl._clipboard.top += 10;
        ctrl._clipboard.left += 10;
        var sel = new fabric.ActiveSelection(objs, {
          canvas: design,
        });
        design.discardActiveObject();
        design.setActiveObject(sel);
        function recursiveDirty(obj) {
          obj.dirty = true;
          if (obj.type === 'group' || obj.type === 'activeSelection') {
            obj.forEachObject(recursiveDirty);
          }
          return obj;
        }
        setTimeout(function() {
          recursiveDirty(sel);
          design.renderAll();
        }, 30);
      }, ctrl.customProperties);
    },
    placeTextboxPoint: function(obj, x, y) {
      if (!obj || obj.type !== 'textbox') {
        return;
      }
      obj.pointX = x - obj.left;
      obj.pointY = y - obj.top;
      obj.dirty = true;
      design.renderAll();
      design.setActiveObject(obj);
    },
    saveGroupElements: function() {
      var ctrl = this;
      if (!design.getActiveObject()) {
        return;
      }
      if (design.getActiveObject().type !== 'group') {
        return;
      }
      var group = design.getActiveObject();
      var data = group.toDatalessObject(this.customProperties);
      function replaceSrc(data) {
        data.objects.map(function(obj) {
          if (obj.type === 'image') {
            var img = ctrl.imgSrcs.find(src => src.src === obj.src);
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
        body: JSON.stringify(payload)
      }).then(function (response) {
        return response.ok ? response.json() : Promise.reject(response);
      }).then(this.updateLibrary).catch(function (err) {
        console.warn('Something went wrong.', err);
      });
    },
    saveImage: function() {
      design.setZoom(1);
      design.absolutePan({x:0, y:0});
      design.discardActiveObject().renderAll();
      var hiddenCanvas = document.getElementById('hiddenCanvas');
      hiddenCanvas.style.display = 'block';
      hiddenCanvas.width = this.canvasWidth;
      hiddenCanvas.height = this.canvasHeight;
      var copy = design.toCanvasElement(1, {
        left: 100,
        top: 100,
        width: this.canvasWidth,
        height: this.canvasHeight
      });
      var ctx = hiddenCanvas.getContext('2d');
      ctx.drawImage(copy, 0, 0);
      var dataUrl = hiddenCanvas.toDataURL("image/png");
      this.url = dataUrl;
    },
    sendBackwards: function() {
      var activeObject = design.getActiveObject();
      if (activeObject) {
        var background = design._objects[0];
        design.sendBackwards(activeObject);
        background.sendToBack();
        design.renderAll();
      }
    },
    sendToBack: function() {
      var activeObject = design.getActiveObject();
      if (activeObject) {
        var background = design._objects[0];
        design.sendToBack(activeObject);
        background.sendToBack();
        design.renderAll();
      }
    },
    setCanvas: function(width, height, skip = false, canvas = undefined) {
      var ctrl = this;
      if (!skip) {
        var check = confirm('Resizing the canvas will erase the contents.');
        if (!check) {
          return false;
        }
        frames.forEach(function(frame) {
          frame.canvas.dispose();
          ctrl.$refs.framesHolder.removeChild(frame.el);
        });
        frames = [];
      }
      var transparency = false;
      if (!canvas) {
        this.canvasWidth = width;
        this.canvasHeight = height;
        canvas = design;
        transparency = true;
        canvas.lowerCanvasEl.parentElement.style.zIndex = '2';
      }
      canvas.clear();
      canvas.backgroundColor = 'rgb(211,211,211, 0.5)';
      if (transparency) {
        canvas.backgroundColor = 'lightgrey';
      }
      var rect = new fabric.Rect({
        width: width,
        height: height,
        fill: 'white',
        top: 100,
        left: 100,
        selectable: false,
        hoverCursor: 'cursor',
      });
      canvas.add(rect);
    },
    startPlaceTextboxPoint: function() {
      var obj = design.getActiveObject();
      if (obj.type === 'textbox') {
        design.placingPoint = obj;
      }
    },
    textboxProperty: function(prop, value, obj) {
      var ctrl = this;
      if (!obj) {
        obj = design.getActiveObject();
      }
      if (obj.type === 'activeSelection' || obj.type === 'group') {
        obj._objects.forEach(function(el){
          ctrl.textboxProperty(prop, value, el);
        });
      } else if (obj.type === 'textbox') {
        obj[prop] = value;
        obj.dirty = true;
      }
      design.renderAll();
    },
    ungroupElements: function() {
      if (!design.getActiveObject()) {
        return;
      }
      if (design.getActiveObject().type !== 'group' || design.getActiveObject().isMasked) {
        return;
      }
      design.getActiveObject().toActiveSelection();
      design.requestRenderAll();
      this.updateActiveSelectionType();
    },
    unmaskElements: function() {
      var active = design.getActiveObject();
      if (!active || !active.isMasked) {
        return;
      }
      active._objects[1].globalCompositeOperation = 'source-over';
      active.isMasked = false;
      this.ungroupElements();
      design.renderAll();
    },
    updateActiveSelectionType: function() {
      var active = design.getActiveObject();
      this.activeSelectionType = active ? active.type : undefined;
      if (active && active.type === 'activeSelection' && active._objects) {
        this.activeSelectionCount = active._objects.length;
      } else if (active) {
        this.activeSelectionCount = 1;
      } else {
        this.activeSelectionCount = 0;
      }
      if (active && active.isMasked) {
        this.activeSelectionMasked = true;
      } else {
        this.activeSelectionMasked = false;
      }
      var objs = design.getActiveObjects();
      this.opacityValue = objs.length ? objs.map((a) => parseFloat(a.opacity)).reduce((a, b) => a + b) / objs.length : 1;
      this.blurValue = objs.length ? objs.map((a) => parseFloat(a.blur)).reduce((a, b) => a + b) / objs.length : 0;
      this.fontFamilyValue = objs[0] ? objs[0].fontFamilyValue : 'Verdana';
      this.fontSizeValue = objs[0] ? objs[0].fontSize : 12;
      this.radius = objs[0] ? objs[0].radius : 0;
      this.borderSizeValue = objs[0] ? objs[0].textboxBorderSize : 1;
    },
    updateLibrary: function(data) {
      var ctrl = this;
      function replaceSrc(data) {
        data.objects.map(function(obj) {
          if (obj.type === 'image') {
            var img = ctrl.imgSrcs.find(src => src.file === obj.src);
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
        objects.forEach(function(obj, i) {
          obj.libraryId = ids[i];
        });
        ctrl.libraryElements = objects;
      });
    }
  },
  mounted() {
    var ctrl = this;
    
    /*convert library svg/xml into images*/
    var images = document.querySelectorAll('.draggableImage');
    var imgSrcs = [];
    /*convert all svg xml into images*/
    [].forEach.call(images, function(div) {
      var src = div.dataset.src;
      var file = div.dataset.file;
      imgSrcs.push({src: src, file: file});
      div.innerHTML = '<img src="' + src + '"/>';
    });
    this.imgSrcs = imgSrcs;
    
    /*start listening to keys*/
    document.addEventListener('keydown', this.handleShortcuts);
    
    
    design = new fabric.Canvas('design', {
      controlsAboveOverlay: true,
      containerClass: 'design',
      stopContextMenu: true,
      preserveObjectStacking: true
    });
    mainCanvas = design;
    window.c = design;
    
    this.setCanvas(682, 270, true);
    this.initCanvas(design);
  },
  created() {
    var ctrl = this;
    fabric.Image.prototype.needsItsOwnCache = function() {return true};

    fabric.Object.prototype.stateProperties = fabric.Object.prototype.stateProperties.concat(ctrl.customProperties);
    fabric.Object.prototype.cacheProperties = fabric.Object.prototype.cacheProperties.concat(ctrl.customProperties);
    
    fetch('/library').then(function (response) {
      return response.ok ? response.json() : Promise.reject(response);
    })
    .then(this.updateLibrary)
    .catch(function (err) {
      console.warn('Something went wrong.', err);
    });
  }
}

Vue.createApp(Builder).mount('#builder')

/*customize textbox*/
var originalTextboxRender = fabric.Textbox.prototype._render;
fabric.Textbox.prototype._render = function(ctx) {
  
  ctx.filter = 'blur(' + this.blur * 10 + 'px)';
  
  if (this.invert) {
    this.fill = 'white';
    this.backgroundColor = 'black';
    this.textboxBorderColor = 'white';
  } else {
    this.fill = 'black';
    this.backgroundColor = 'white';
    this.textboxBorderColor = 'black';
  }
  
  if (this.textboxBorderSize > 0) {
    var w = this.width,
      h = this.height,
      x = -this.width / 2,
      y = -this.height / 2;

    ctx.fillStyle = this.backgroundColor;
    ctx.lineWidth = this.textboxBorderSize;
    ctx.strokeStyle = this.textboxBorderColor;
    
    /*textbox line*/
    /*start in center of box*/
    var startX = x + w/2;
    var startY = y + h/2;
    ctx.beginPath();
    ctx.moveTo(startX, startY);
    
    /*calc offset of canvas*/
    var subX = Math.abs(x);
    if (subX === 0) {
      subX = w/2
    }
    var subY = Math.abs(y);
    if (subY === 0) {
      subY = h/2
    }
    ctx.lineTo(this.pointX - subX, this.pointY - subY);
    ctx.stroke();
    
    /*draw textbox rectangle*/
    roundRect(ctx, x, y, w, h, this.radius);
  }
  
  this.backgroundColor = 'transparent';
  originalTextboxRender.call(this, ctx);
}

function roundRect(ctx, x, y, width, height, radius) {
  if (radius > width /2 || radius > height/2) {
    radius = Math.min(width / 2, height / 2);
  }
  radius = {tl: radius, tr: radius, br: radius, bl: radius};
  
  ctx.beginPath();
  ctx.moveTo(x + radius.tl, y);
  ctx.lineTo(x + width - radius.tr, y);
  ctx.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
  ctx.lineTo(x + width, y + height - radius.br);
  ctx.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
  ctx.lineTo(x + radius.bl, y + height);
  ctx.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
  ctx.lineTo(x, y + radius.tl);
  ctx.quadraticCurveTo(x, y, x + radius.tl, y);
  ctx.closePath();
  ctx.fill();
  ctx.stroke();

}

var originalRenderCache = fabric.Object.prototype.renderCache;
fabric.Image.prototype.renderCache = function() {
  var filter = 'blur(' + (this.blur * 10) + 'px)' +
    'invert(' + this.invert + ')';
  if (this._cacheCanvas && this.dirty) {
    this._cacheContext.filter = filter;
  }
  
  originalRenderCache.call(this);
}

var _updateCacheCanvas = fabric.Image.prototype._updateCacheCanvas;
fabric.Image.prototype._updateCacheCanvas = function() {
  var filter = 'blur(' + (this.blur * 10) + 'px)' +
    'invert(' + this.invert + ')';
  var oldVal = this.zoomX ;
  var returnVal = _updateCacheCanvas.call(this);
  var newVal = this.zoomX;
  if (oldVal !== newVal) {
    this._cacheContext.filter = filter;
  }
  if (this._cacheContext.filter !== filter) {
    this._cacheContext.filter = filter;
  }
  return returnVal;
};

var originalGroupRenderCache = fabric.Group.prototype.renderCache;
fabric.Group.prototype.renderCache = function() {
  if (this._cacheCanvas && this.dirty) {
    this._cacheContext.filter = 'blur(' + (this.blur * 10) + 'px)';
    this._cacheContext.filter += 'invert(' + this.invert + ')';
  }
  
  originalGroupRenderCache.call(this);
}