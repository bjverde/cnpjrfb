var tbarcodeinputreader;
var tbarcodeinputreader_receive;

function tbarcodeinputreader_open_reader(id) {
    var id_reader = id + '_reader';
    var id_container = id + '_container';

    var width = $(window).width() * 0.9;
    var height =  $(window).height() * 0.8;

    var container = document.createElement("div");
    var cameras   = document.createElement("div");
    var reader    = document.createElement("video");
    
    container.title = 'Barcode';

    container.id =  id_container;
    reader.id =  id_reader;
    cameras.id = id_reader + '-cameras';

    container.setAttribute('class', "tbarcodeinputreader_container");
    reader.setAttribute('class', "tbarcodeinputreader_reader");
    cameras.setAttribute('class', "tbarcodeinputreader-cameras");

    container.appendChild(reader);
    container.appendChild(cameras);

    $('#adianti_div_content').append(container);
    
    tbarcodeinputreader_start(id_reader,
        function() {
            tjquerydialog_start( '#' + id_container, true, false, false, width, height, 0, 0, 99999, [], function() { tbarcodeinputreader.closing = true; tbarcodeinputreader_stop(); }, true, '' );
        },
        function(code) {
            __adianti_block_ui();
            $('#' + id).val(code);
            tbarcodeinputreader_stop();

            if (typeof $('#' + id).attr('changeaction') !== 'undefined') {
                Function($('#' + id).attr('changeaction'))();
            }

            __adianti_unblock_ui();
        });
}

function tbarcodeinputreader_start(elementId, onStart, onData) {
    tbarcodeinputreader = new ZXing.BrowserBarcodeReader();
    tbarcodeinputreader.closing = false; 
    tbarcodeinputreader._stopContinuousDecode = false;
    tbarcodeinputreader_receive = onData;

    tbarcodeinputreader.listVideoInputDevices().then( function(devices) {
        if (! devices || devices.length == 0) {
            return;
        }
        
        onStart();
        
        if(devices.length > 1) {
            trbarcodeinputreader_make_radio_camera(elementId, devices);
        }

        var cameraId = devices[devices.length - 1].deviceId;

        tbarcodeinputreader_read(cameraId, elementId);
    }).catch(function(err) {
        __adianti_error('Error', err);
    });
}

function trbarcodeinputreader_make_radio_camera(elementId, devices) {
    for (var index = 0; index < devices.length; index++) {
        
        var device = devices[index];
        
        var input = document.createElement("input");
        var label = document.createElement("label");

        input.id       = device.deviceId;
        input.type     = 'radio';
        input.name     = elementId + '-camera';

        input.setAttribute('onchange', "tbarcodeinputreader_restart('"+device.deviceId+"','"+elementId+"');");

        label.for = device.deviceId;
        label.appendChild(input);
        label.appendChild(document.createTextNode(device.label));

        if(index == devices.length - 1) {
            input.checked = true;
        }

        $('#'+elementId+'-cameras').append(label);
    }
}

function tbarcodeinputreader_stop() {
    tbarcodeinputreader_close();
}

function tbarcodeinputreader_close() {
    tbarcodeinputreader.reset();
    $('.tbarcodeinputreader_container').closest('.ui-dialog').remove();
    $('.tbarcodeinputreader_container').remove();
}

function tbarcodeinputreader_restart(cameraId, elementId) {
    tbarcodeinputreader.closing = true;
    tbarcodeinputreader.reset()
    tbarcodeinputreader_read(cameraId, elementId);    
}

function tbarcodeinputreader_read(cameraId, elementId) {
    tbarcodeinputreader.decodeOnceFromVideoDevice(cameraId, elementId).then( function(result) {
        tbarcodeinputreader_receive(result)
    }).catch(function(err) {
        if(tbarcodeinputreader && tbarcodeinputreader.closing) {
            return;
        }
        
        tbarcodeinputreader_close();
        __adianti_error('Error', err);
    })
}
