function tlikertscale_enable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').attr('onclick', null);
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events',   'auto');
        $('form[name='+form_name+'] [name='+field+']').closest('.likert-wrapper').removeClass('tfield_block_events');
    },1);
}

function tlikertscale_disable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').attr('onclick', 'return false');
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events', 'none');
        $('form[name='+form_name+'] [name='+field+']').closest('.likert-wrapper').addClass('tfield_block_events');
    },1);
}

function tlikertscale_clear_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').attr('checked', false) },1);    
}

function tlikertscale_reload(form_name, field, content) {
    $('form[name='+form_name+'] [tlikertscale='+field+']').replaceWith(content);
}