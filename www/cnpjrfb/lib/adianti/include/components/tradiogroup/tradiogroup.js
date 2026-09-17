function tradiogroup_enable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){
        $(selector).attr('onclick', null);
        $(selector).css('pointer-events',   'auto');
        $(selector).closest('.toggle-wrapper').removeClass('tfield_block_events');
    },1);
}

function tradiogroup_disable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){
        $(selector).attr('onclick', 'return false');
        $(selector).css('pointer-events', 'none');
        $(selector).closest('.toggle-wrapper').addClass('tfield_block_events');
    },1);
}

function tradiogroup_clear_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){ $(selector).prop('checked', false) },1);    
}

function tradiogroup_reload(form_name, field, content) {
    $('form[name='+form_name+'] [tradiogroup='+field+']').replaceWith(content);
}