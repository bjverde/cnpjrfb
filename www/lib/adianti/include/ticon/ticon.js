function ticon_enable_field(form_name, field) {
    try { 
        setTimeout(function() {
            $('form[name='+form_name+'] div[name="block_'+field+'"]').remove();
        }, 1);
    } catch (e) {
        console.log(e);
    }
}

function ticon_disable_field(form_name, field) {
    try {
        input = $('form[name='+form_name+'] [name='+field+']').closest('.input-group');
        setTimeout(function() {$('form[name='+form_name+'] div[name="block_'+field+'"]').remove();}, 19);
        setTimeout(function() {input.prepend('<div name="block_'+field+'" style="position:absolute; width:'+input.width()+'px; height:'+input.height()+'px; background: #c0c0c0; opacity:0.5;"></div>')}, 20);
    } catch (e) {
        console.log(e);
    }
}

function ticon_start(id, func) {
    if(func) {
        $('#'+id).iconpicker().on('iconpickerUpdated', function(e) {
            func($(this).val());
        });
    }
    else {
        $('#'+id).iconpicker();
    }
}
