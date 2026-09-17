var TSignaturePad = (function (field, title, buttonLabel, serviceAction, fileHandling, base64, config, name, extension) {

    var that = this;

    this.pad = null;
    this.url = null;
    this.name = 'signature_' + Math.floor((Math.random() * 1000000) + 1) + '.png';
    this.type = extension || 'image/png';

    this.fileHandling   = fileHandling;
    this.serviceAction  = serviceAction;
    this.field          = field;
    this.title          = title;
    this.buttonLabel    = buttonLabel;
    this.config         = config || {};
    this.base64         = base64;

    this.file_input_hidden = $('input[id=' + field + ']');
    this.actions           = $('#' + field).closest('.label_tsignaturepad').find('.tsignaturepad_actions');
    this.container         = $('#tsignaturepad_container_' + name);
    this.placeholder       = $('#tsignaturepad_container_' + name + ' .image-placeholder');
    this.edit              = this.actions.find('[action=edit]');
    this.remove            = this.actions.find('[action=remove]');

    // utilitários
    function ensurePngName(fname) {
        return fname && fname.toLowerCase().endsWith('.png') ? fname : (fname + '.png');
    }

    function dataURLtoBlob(dataUrl) {
        var parts = dataUrl.split(',');
        var mime  = parts[0].match(/:(.*?);/)[1];
        var bstr  = atob(parts[1]);
        var n     = bstr.length;
        var u8arr = new Uint8Array(n);
        while (n--) u8arr[n] = bstr.charCodeAt(n);
        return new Blob([u8arr], { type: mime });
    }

    // callbacks de XHR
    function onReadyStateChange(evt) {
        var status = null;
        try { status = evt.target.status; } catch(e) { return; }

        if (status == 200 && evt.target.readyState == 4 && evt.target.responseText) {
            try {
                var response = JSON.parse(evt.target.responseText);
                if (response.type == 'error') {
                    __adianti_error('Error', response.msg);
                }
            } catch (e) {
                __adianti_error('Error', e);
            }
        }
    }

    // gestão de dados do hidden
    function setData(data) {
        if (data) {
            that.file_input_hidden.val(encodeURIComponent(JSON.stringify(data)));
        } else {
            that.file_input_hidden.val('');
        }
    }
    that.sendData = setData;

    function getData() {
        var raw = decodeURIComponent(that.file_input_hidden.val());
        try {
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            return {};
        }
    }

    // salvar assinatura
    function save() {
        __adianti_block_ui();

        var dataUrl = that.pad.toDataURL(that.type);

        $('#tsignaturepad_image_' + that.field).attr('src', dataUrl).show();
        that.actions.show();
        if (that.placeholder && that.placeholder.length) that.placeholder.hide();

        if (that.base64) {
            that.file_input_hidden.val(dataUrl);
        } else {
            try {
                var blob  = dataURLtoBlob(dataUrl);
                var fname = ensurePngName(that.name);
                var file  = new File([blob], fname, { type: that.type });

                var form_data = new FormData();
                form_data.append('fileName', file);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', that.serviceAction, true);
                xhr.addEventListener('readystatechange', onReadyStateChange, false);
                xhr.send(form_data);

                if (that.fileHandling) {
                    var file_data = getData();
                    if (file_data.fileName && file_data.fileName !== file_data.newFile) {
                        file_data.delFile = file_data.fileName;
                    }
                    file_data.newFile  = 'tmp/' + fname;
                    file_data.fileName = 'tmp/' + fname;
                    setData(file_data);
                } else {
                    that.file_input_hidden.val(fname);
                }
            } catch (e) {
                __adianti_error('Error', e);
            }
        }

        that.container.css('background', '#ffffff');
        $('#container_tsignaturepad_' + that.field).remove(); // close dialog
        __adianti_unblock_ui();
    }

    // abrir modal
    function open(existingDataUrl) {
        __adianti_block_ui();

        var div    = document.createElement('div');
        var canvas = document.createElement('canvas');
        var actions = document.createElement('div');

        canvas.id = 'tsignaturepad_canvas_' + that.field;
        canvas.width  = that.config.drawWidth;
        canvas.height = that.config.drawHeight;
        canvas.style.display = 'block';
        canvas.style.margin = 'auto';
        canvas.className = 'tsignaturepad_canvas';
        
        actions.className = 'actions_tsignaturepad';
        actions.width = 80;
        actions.appendChild(that.getUndoButton());
        actions.appendChild(that.getRedoButton());
        
        div.title = that.title;
        div.id    = 'container_tsignaturepad_' + that.field;
        div.style.background = '#fff';
        div.style.padding    = '10px';
        div.appendChild(canvas);
        div.appendChild(actions);
        
        $('#adianti_div_content').append(div);

        tjquerydialog_start('#' + div.id, true, true, true, 'auto', 'auto', 0, 0, 2050, [
            { text: that.buttonLabel, click: save }
        ], function() {
            // garante remoção do container ao fechar/salvar
            $('#' + div.id).remove();
        }, true, '', true);

        that.pad = new SignaturePad(canvas, {
            penColor: that.config.penColor || '#000000',
            minWidth: that.config.thickness || 1.5,
            maxWidth: (that.config.thickness || 1.5) * 2
        });
        
        that.undoData = [];

        if (existingDataUrl) {
            var img = new Image();
            img.onload = function() {
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.src = existingDataUrl;
        }

        __adianti_unblock_ui();
    }

    // API pública
    this.start = function(existingDataUrl) {
        open(existingDataUrl || null);
    };

    this.clear = function () {
        if (this.pad) this.pad.clear();
        this.actions.hide();
        $('#tsignaturepad_image_' + this.field).hide();
        that.container.css('background', '#e9e9e959');
        this.file_input_hidden.val('');
        if (this.placeholder && this.placeholder.length) this.placeholder.show();
    };

    this.getUndoButton = function() {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-undo');
        //button.setAttribute('title', that.config.labels.move);
        button.addEventListener('click', function () {
            var data = that.pad.toData();
            if (data) {
                const removed = data.pop(); // remove the last dot or line
                that.undoData.push(removed);
                that.pad.fromData(data);
            }
        });
        button.appendChild(icon);
        return button;
    }
    
    this.getRedoButton = function() {
        var icon = document.createElement('i');
        var button = document.createElement('button');
        button.setAttribute('class', 'btn btn-default');
        icon.setAttribute('class', 'fa fa-redo');
        //button.setAttribute('title', that.config.labels.move);
        button.addEventListener('click', function () {
            var data = that.pad.toData();
            data.push(that.undoData.pop())
            that.pad.fromData(data);
        });
        button.appendChild(icon);
        return button;
    }

    // listeners (reaproveitando start/clear)
    this.edit.click(function() {
        var currentSrc = $('#tsignaturepad_image_' + that.field).attr('src');
        that.start(currentSrc);
    });

    this.remove.click(function() {
        that.clear();
    });

    var placeholderEl = document.querySelector('#tsignaturepad_container_' + name + ' .image-placeholder');
    var imageEl       = document.getElementById('tsignaturepad_image_' + field);

    if (placeholderEl) {
        placeholderEl.addEventListener('click', function() { that.start(); });
    }
    
    if (imageEl && imageEl.dataset.editable !== 'false') {
        imageEl.addEventListener('click', function() {
            var currentSrc = imageEl.getAttribute('src');
            that.start(currentSrc);
        });
    }
});

function tsignaturepad_start(options) {
    var tsignaturepad = new TSignaturePad(
        options.field,
        options.title,
        options.buttonLabel,
        options.serviceAction,
        options.fileHandling,
        options.base64,
        options.config,
        options.name,
        options.extension
    );
    document.getElementById(options.field).tsignaturepad = tsignaturepad;
}
