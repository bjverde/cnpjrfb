var TImageCropper = (function (field, title, buttonLabel, serviceAction, fileHandling, base64, webcam, config, name, extension) {

    this.cropper;
    this.url;
    this.name = name;
    this.type = extension;

    this.fileHandling = fileHandling;
    this.serviceAction = serviceAction;
    this.field = field;
    this.title = title;
    this.buttonLabel = buttonLabel;
    this.config = config;
    this.base64 = base64;
    this.webcam = webcam;

    this.file_input_hidden = $('input[name=' + field + ']');
    this.file = $('#tfile_timagecropper_' + field);
    this.image = $('#timagecropper_' + field);
    this.actions = $('#timagecropper_' + field + '+div.timagecropper_actions');
    this.edit = this.actions.find('[action=edit]');
    this.remove = this.actions.find('[action=remove]');

    this.file.click(function(evt){
        // Not use webcan or is mobile
        if(! webcam || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return;
        }

        evt.preventDefault();

        var div = document.createElement('div');
        div.id =  'webcam_' + name.replace('.','');
        div.title = 'WebCam';

        $(div).addClass('timagecropper_webcam');

        $('#adianti_div_content').append(div);

        var width = $(window).width() * .75;
        var height = $(window).height() * .75;

        Webcam.set({
            image_format: 'png'
        });

        tjquerydialog_start('#' + div.id, true, false, false, width, height, 0, 0, 2050, [
            {
                text: 'Capturar',
                click: function() {
                    Webcam.snap( function(data_uri) {
                        var file = dataUrltoFile(data_uri);
                        onCloseWebCam(div.id);
                        onUpload(null, [file]);
                    });
                }
            }
        ], function(){ onCloseWebCam(div.id) }, true, '');
        
        setTimeout(function() { Webcam.attach('#' + div.id) }, 500);
    });

    var onCloseWebCam = function(id)  {
        Webcam.reset();
        $('#' + id).remove();
    };

    this.dropzone = $('#timagecropper_container_' + field);
    
    this.dropzone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    this.dropzone.on('dragover dragenter', function() {
        $('#timagecropper_container_' + field).addClass('highlight');
    });
    
    this.dropzone.on('dragleave dragend drop', function() {
        $('#timagecropper_container_' + field).removeClass('highlight');
    });

    var that = this;

    var onEdit = function (e) {
        __adianti_block_ui();
        that.url = that.image[0].src;
        open();
        __adianti_unblock_ui();
        e.preventDefault();
    }

    var onRemove = function (e) {
        var image = $('#timagecropper_' + that.field)[0];
        if (that.fileHandling) {

            var file_data = getData();
            
            file_data.delFile = file_data.fileName;
            file_data.fileName = '';
            file_data.newFile = '';

            // if delete recently included file (not in server already)
            if (file_data.delFile == file_data.newFile) {
                file_data = '';
            }

            setData(file_data);
            image.src = ' ';
        } else {
            var image = $('#timagecropper_' + that.field)[0];
            that.url = null;
            image.src = ' ';
            setData('');
        }
        
        that.actions.hide();
        e.preventDefault();
    }

    var onUpload = function (e, webcamimages) {
        __adianti_block_ui();
        var files = webcamimages||e.target.files||e.originalEvent.dataTransfer.files;
        
        if (files && files.length > 0) {
            var file = files[0];

            that.name = file.name;
            that.type = file.type;

            processFile(file);
            __adianti_unblock_ui();
        } else {
            __adianti_unblock_ui();
        }
    };

    var processFile = function (file) {
        if (URL) {
            that.url = URL.createObjectURL(file);
            open();
        } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function (e) {
                that.url = reader.result;
                open();
            };
            reader.readAsDataURL(file);
        }
    }

    var init = function () {
        var image = $('#timgagecropper_image_' + that.field)[0];

        var defaults = {
            viewMode: 0,
            highlight: true,
            rotatable: true,
            responsive: true,
            restore: true,
            modal: true,
            guides: true
        };

        if (that.config.aspectRatio) {
            defaults.aspectRatio = that.config.aspectRatio;
        }

        that.cropper = new Cropper(image, defaults);
    }

    var open = function () {
        __adianti_block_ui();
        var div = document.createElement('div');
        var container = document.createElement('div');
        var actionsContainer = document.createElement('div');
        var actions = document.createElement('div');
        var img = document.createElement('img');
        var width = $(window).width() * .95;
        var height = $(window).height() * .90;

        img.src = that.url;
        img.id = 'timgagecropper_image_' + that.field;

        div.title = that.title;
        div.id = 'container_timgagecropper_image_' + that.field;

        actionsContainer.appendChild(actions);
        container.appendChild(img);
        container.appendChild(actionsContainer);
        div.appendChild(container);

        if ($(window).width() < 540) {
            var cropWidth = width - 30;
            var cropHeight = height - 200;
        } else {
            var cropWidth = width - 100;
            var cropHeight = height - 130;
        }
        
        container.setAttribute('style', 'width: ' + cropWidth + 'px; height: ' + cropHeight + 'px');
        
        img.setAttribute('class', 'img_timagecrroper');
        container.setAttribute('class', 'container_timagecrroper');
        actionsContainer.setAttribute('class', 'actions_timagecrroper');
        actionsContainer.setAttribute('class', 'actions_timagecrroper');

        if (that.config.enableButtonDrag) {
            actions.appendChild(getImageCropperDragButton());
        }

        if (that.config.enableButtonScale) {
            actions.appendChild(getImageCropperScaleButton());
        }

        if (that.config.enableButtonZoom) {
            actions.appendChild(getImageCropperZoomButton());
        }

        if (that.config.enableButtonRotate) {
            actions.appendChild(getImageCropperRotateButton());
        }

        if (that.config.enableButtonRotate) {
            actions.appendChild(getImageCropperResetButton());
        }

        $('#adianti_div_content').append(div);

        tjquerydialog_start('#' + div.id, true, false, false, width, height, 0, 0, 2050, [
            {
                text: that.buttonLabel,
                click: save
            }
        ], null, true, '');

        init();

        __adianti_unblock_ui();
    };

    var dataUrltoFile = function (dataurl) {
        var arr = dataurl.split(',');
        var bstr = atob(arr[1]);
        var n = bstr.length;
        var u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }

        var name = (that.name? that.name : 'image' + Math.floor((Math.random() * 1000000) + 1) + '.png' );
        var type = (that.type? that.type : 'image/png') ;
        
        return new File([u8arr], name, { type: type});
    };

    var save = function () {
        __adianti_block_ui();
        var canvas = that.cropper.getCroppedCanvas({
            width: that.config.cropWidth,
            heigth: that.config.cropHeight
        });

        var canvasUrl = canvas.toDataURL(that.type);

        $('#timagecropper_' + that.field).attr('src', canvasUrl);

        that.actions.show();

        if (that.base64) {
            that.file_input_hidden.val(canvasUrl);
        }
        else {
            try {
                var file = dataUrltoFile(canvasUrl);
                var form_data = new FormData();
                form_data.append('fileName', file);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', that.serviceAction, true);
                xhr.addEventListener('readystatechange', onReadyStateChange, false);
                xhr.send(form_data);

                if (that.fileHandling) {
                    var file_data = getData();

                    // if delete recently included file (not in server already)
                    if (file_data.fileName && file_data.fileName != file_data.newFile ) {
                        file_data.delFile = file_data.fileName;
                    }

                    file_data.newFile = 'tmp/' + that.name;
                    file_data.fileName = 'tmp/' + that.name;
                    setData(file_data);
                } else {
                    that.file_input_hidden.val(that.name);
                }
            }
            catch (e) {
                __adianti_error('Error', e);
            }
        }
        
        $('.ui-dialog').remove();
        $('#container_timgagecropper_image_' + that.field).remove();

        that.cropper.destroy();
        __adianti_unblock_ui();
    };

    var onReadyStateChange = function(evt)
    {
        var status = null;
        
        try {
            status = evt.target.status;
        }
        catch(e) {
            return;
        }
        
        if (status == '200' && evt.target.readyState == '4' && evt.target.responseText) {
            try {
                var response = JSON.parse( evt.target.responseText );
                
                if ( response.type == 'error' ) {
                    __adianti_error('Error', response.msg);
                }
            }
            catch (e) {
                __adianti_error('Error', e);
            }
        }
    };
    
    var setData = function (data) {
        if (data) {
            $(that.file_input_hidden).val(encodeURIComponent(JSON.stringify(data)));
        }
        else {
            $(that.file_input_hidden).val('');
        }
    };

    var getData = function () {
        var file_data = decodeURIComponent(that.file_input_hidden.val());
        var file_data_json = '';

        try {
            file_data_json = file_data ? JSON.parse(file_data) : {};
        }
        catch (e) {
            file_data_json = {};
        }
        return file_data_json;
    };
    
    // Actions

    // Reset    
    var getImageCropperResetButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-sync-alt');
        button.setAttribute('title', that.config.labels.reset);
        button.addEventListener('click', function () {
            that.cropper.reset();
        });
        button.addEventListener('touchstart', function () {
            that.cropper.reset();
        });
        button.appendChild(icon);
        return button;
    }

    // Scale
    var getImageCropperScaleButton = function () {
        var h = getImageCropperScaleHorizontalButton();
        var v = getImageCropperScaleVerticalButton();

        var div = document.createElement('div');
        div.appendChild(h);
        div.appendChild(v);

        return div;
    }

    var getImageCropperScaleHorizontalButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-arrows-alt-h');
        button.setAttribute('title', that.config.labels.scalex);
        button.setAttribute('data-option', '-1');
        button.addEventListener('click', function () {
            imageCropperScaleX(this);
        });
        button.addEventListener('touchstart', function () {
            imageCropperScaleX(this);
        });
        button.appendChild(icon);
        return button;
    }

    var getImageCropperScaleVerticalButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-arrows-alt-v');
        button.setAttribute('title', that.config.labels.scaley);
        button.setAttribute('data-option', '-1');
        button.addEventListener('click', function () { imageCropperScaleY(this) });
        button.addEventListener('touchstart', function () { imageCropperScaleY(this) });
        button.appendChild(icon);
        return button;
    }


    var imageCropperScaleX = function (element) {
        var scale = element.getAttribute('data-option');
        that.cropper.scaleX(scale);
        element.setAttribute('data-option', scale * -1);
    }


    var imageCropperScaleY = function (element) {
        var scale = element.getAttribute('data-option');
        that.cropper.scaleY(scale);
        element.setAttribute('data-option', scale * -1);
    }

    // Drag
    var getImageCropperDragButton = function () {
        var m = getImageCropperDragMoveButton();
        var c = getImageCropperDragCropButton();

        var div = document.createElement('div');
        div.appendChild(m);
        div.appendChild(c);

        return div;
    }

    var getImageCropperDragMoveButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-arrows-alt');
        button.setAttribute('title', that.config.labels.move);
        button.addEventListener('click', function () {
            that.cropper.setDragMode("move");
        });
        button.addEventListener('touchstart', function () {
            that.cropper.setDragMode("move");
        });
        button.appendChild(icon);
        return button;
    }

    var getImageCropperDragCropButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-crop-alt');
        button.setAttribute('title', that.config.labels.crop);
        button.addEventListener('click', function () {
            that.cropper.setDragMode("crop");
        });
        button.addEventListener('touchstart', function () {
            that.cropper.setDragMode("crop");
        });
        button.appendChild(icon);

        return button;
    }

    // Zoom
    var getImageCropperZoomButton = function () {
        var zin = getImageCropperZoomInButton();
        var zout = getImageCropperZoomOutButton();

        var div = document.createElement('div');
        div.appendChild(zin);
        div.appendChild(zout);

        return div;
    }

    var getImageCropperZoomInButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-search-plus');
        button.setAttribute('title', that.config.labels.zoomin);
        button.addEventListener('click', function () {
            that.cropper.zoom(.1);
        });
        button.addEventListener('touchstart', function () {
            that.cropper.zoom(.1);
        });
        button.appendChild(icon);
        return button;
    }

    var getImageCropperZoomOutButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-search-minus');
        button.setAttribute('title', that.config.labels.zoomout);
        button.addEventListener('click', function () {
            that.cropper.zoom(-.1);
        });
        button.addEventListener('touchstart', function () {
            that.cropper.zoom(-.1);
        });
        button.appendChild(icon);
        return button;
    }

    // Rotate
    var getImageCropperRotateButton = function () {
        var rr = getImageCropperRotateRightButton();
        var rl = getImageCropperRotateLeftButton();

        var div = document.createElement('div');
        div.appendChild(rl);
        div.appendChild(rr);

        return div;
    }

    var getImageCropperRotateRightButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-redo-alt');
        button.setAttribute('title', that.config.labels.rotateright);
        button.addEventListener('click', function () {
            that.cropper.rotate(45);
        });
        button.addEventListener('touchstart', function () {
            that.cropper.rotate(45);
        });
        button.appendChild(icon);
        return button;
    }

    var getImageCropperRotateLeftButton = function () {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-undo-alt');
        button.setAttribute('title', that.config.labels.rotateleft);
        button.setAttribute('data-option', '-45');
        button.addEventListener('click', function () {
            that.cropper.rotate(-45);
        });
        button.addEventListener('touchstart', function () {
            that.cropper.rotate(-45);
        });
        button.appendChild(icon);
        return button;
    }

    this.file.change(onUpload);
    this.dropzone.on('drop',onUpload);
    this.edit.click(onEdit);
    this.remove.click(onRemove);

});

function timagecropper_start(field, title, buttonLabel, serviceAction, fileHandling, base64, webcam, config, name, extension) {
    new TImageCropper(field, title, buttonLabel, serviceAction, fileHandling, base64, webcam, config, name, extension);
}
