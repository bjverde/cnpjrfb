function tcolor_enable_field(form_name, field) {
    try {
        setTimeout(function(){
            $('form[name='+form_name+'] [name='+field+']').closest('.color-div').colorpicker('enable');
            $('form[name='+form_name+'] [name='+field+']').removeClass('tfield_disabled');
        },1);
    } catch (e) {
        console.log(e);
    }
}

function tcolor_disable_field(form_name, field) {
    try {
        setTimeout(function(){
            $('form[name='+form_name+'] [name='+field+']').closest('.color-div').colorpicker('disable');
            $('form[name='+form_name+'] [name='+field+']').addClass('tfield_disabled');
        },1);
    } catch (e) {
        console.log(e);
    }
}

function tcolor_start(id, size, change_function) {
    var last_tcolor_change_time = 0;
    if(typeof change_function != 'undefined')
    {
        $('#'+id).closest('.color-div').colorpicker().on('changeColor', function(e) {
            if ((e.timeStamp - last_tcolor_change_time) > 500) {
                change_function(e.color);
                last_tcolor_change_time = e.timeStamp;
            }
        });
        $('#'+id).click( function() {
            $('#'+id).closest('.color-div').colorpicker('show');
        });
    }
    else
    {
        $('#'+id).closest('.color-div').colorpicker();
    }
    
    if (size !== 'undefined')
    {
        $('#'+id).closest('.color-div').width(size);
    }
    
    // to allow colorpicker open over popover
    $('.colorpicker').css('z-index', 1000000);
}
