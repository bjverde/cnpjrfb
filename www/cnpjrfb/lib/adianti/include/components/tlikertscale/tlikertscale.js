function tlikertscale_enable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){
        $(selector).attr('onclick', null);
        $(selector).css('pointer-events',   'auto');
        $(selector).closest('.likert-wrapper').removeClass('tfield_block_events');
    },1);
}

function tlikertscale_disable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){
        $(selector).attr('onclick', 'return false');
        $(selector).css('pointer-events', 'none');
        $(selector).closest('.likert-wrapper').addClass('tfield_block_events');
    },1);
}

function tlikertscale_clear_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){ $(selector).attr('checked', false) },1);    
}

function tlikertscale_reload(form_name, field, content) {
    $('form[name='+form_name+'] [tlikertscale='+field+']').replaceWith(content);
}