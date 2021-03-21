var tqrcodeinputreader;
var tqrcodeinputreader_receive;

function tqrcodeinputreader_open_reader(id) {
    var id_reader = id + '_reader';
    var id_container = id + '_container';

    var width = $(window).width() * 0.9;
    var height =  $(window).height() * 0.8;

    var container = document.createElement("div");
    var cameras   = document.createElement("div");
    var reader    = document.createElement("div");
    
    container.title = 'QRCode';

    container.id =  id_container;
    reader.id =  id_reader;
    cameras.id = id_reader + '-cameras';

    container.setAttribute('class', "tqrcodeinputreader_container");
    reader.setAttribute('class', "tqrcodeinputreader_reader");
    cameras.setAttribute('class', "tqrcodeinputreader-cameras");

    container.appendChild(reader);
    container.appendChild(cameras);

    $('#adianti_div_content').append(container);
    
    tqrcodeinputreader_start(id_reader,
        function() {
            tjquerydialog_start('#' + id_container, true, false, false, width, height, 0, 0, 99999, [], tqrcodeinputreader_stop, true, '');
        },
        function(code) {
            __adianti_block_ui();
            $('#' + id).val(code);
            tqrcodeinputreader_stop();

            if (typeof $('#' + id).attr('changeaction') !== 'undefined') {
                Function($('#' + id).attr('changeaction'))();
            }
            
            __adianti_unblock_ui();
        });
}

function tqrcodeinputreader_start(elementId, onStart, onData) {
    tqrcodeinputreader = new Html5Qrcode(elementId);
    tqrcodeinputreader_receive = onData;

    Html5Qrcode.getCameras().then(function(devices) {
        if (! devices || devices.length == 0) {
            return;
        }
        
        onStart();
        
        if(devices.length > 1) {
            trqcodeinputreader_make_radio_camera(elementId, devices);
        }

        var cameraId = devices[devices.length - 1].id;

        tqrcodeinputreader_read(cameraId);
    }).catch(function(err) {
        __adianti_error('Error', err);
    });
}

function trqcodeinputreader_make_radio_camera(elementId, devices) {
    for (var index = 0; index < devices.length; index++) {

        var device = devices[index];

        var input = document.createElement("input");
        var label = document.createElement("label");

        input.id       = device.id;
        input.type     = 'radio';
        input.name     = elementId + '-camera';

        input.setAttribute('onchange', "tqrcodeinputreader_restart('" + device.id + "');");

        label.for = device.id;
        label.appendChild(input);
        label.appendChild(document.createTextNode(device.label));

        if(index == devices.length - 1) {
            input.checked = true;
        }

        $('#' + elementId + '-cameras').append(label);
    }
}

function tqrcodeinputreader_stop() {
    if(! tqrcodeinputreader || ! tqrcodeinputreader._isScanning ) {
        tqrcodeinputreader_close();
        return;
    }

    tqrcodeinputreader.stop()
        .then(function() {
            tqrcodeinputreader_close();
        })
        .catch(function(err) {
            __adianti_error('Error', err)
        }
    );
}

function tqrcodeinputreader_close() {
    $('.tqrcodeinputreader_container').closest('.ui-dialog').remove();
    $('.tqrcodeinputreader_container').remove();
    tqrcodeinputreader = null;
}

function tqrcodeinputreader_restart(cameraId) {
    tqrcodeinputreader.stop()
        .then( function() {
            tqrcodeinputreader_read(cameraId);
        })
        .catch(function(err) {
            __adianti_error('Error', err);
        }
    );
}

function tqrcodeinputreader_read(cameraId) {
    tqrcodeinputreader.start(
        cameraId,
        {
            qrbox : $('#'+tqrcodeinputreader._elementId).width() * .75
        },
        function(code) {
            tqrcodeinputreader_receive(code)
        },
        function(error) { }).
        catch( function(err) {
            __adianti_error('Error', err);
        }
    );
}
