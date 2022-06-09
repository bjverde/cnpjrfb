function tradiogroup_enable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').attr('onclick', null);
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events',   'auto');
        //$('form[name='+form_name+'] [name='+field+']').parent().css('pointer-events', 'auto');
        //$('form[name='+form_name+'] [name='+field+']').parent().removeAttr('disabled');
        //$('form[name='+form_name+'] [name='+field+']').parent().css('color', '');
        $('form[name='+form_name+'] [name='+field+']').closest('.toggle-wrapper').removeClass('tfield_block_events');
    },1);
}

function tradiogroup_disable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').attr('onclick', 'return false');
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events', 'none');
        //$('form[name='+form_name+'] [name='+field+']').parent().css('pointer-events', 'none');
        //$('form[name='+form_name+'] [name='+field+']').parent().attr('disabled', '');
        //$('form[name='+form_name+'] [name='+field+']').parent().css('color', 'gray');
        $('form[name='+form_name+'] [name='+field+']').closest('.toggle-wrapper').addClass('tfield_block_events');
    },1);
}

function tradiogroup_clear_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').attr('checked', false) },1);    
}

function tradiogroup_reload(form_name, field, content) {
    $('form[name='+form_name+'] [tradiogroup='+field+']').html(content);
}