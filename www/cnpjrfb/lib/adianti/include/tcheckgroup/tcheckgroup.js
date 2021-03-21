function tcheckgroup_enable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [checkgroup='+field+']').attr('onclick', null);
        $('form[name='+form_name+'] [checkgroup='+field+']').css('pointer-events',   'auto');
        $('form[name='+form_name+'] [checkgroup='+field+']').parent().css('pointer-events', 'auto');
        $('form[name='+form_name+'] [checkgroup='+field+']').parent().removeAttr('disabled');
        $('form[name='+form_name+'] [checkgroup='+field+']').parent().css('color', '');
    },1);
}

function tcheckgroup_disable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [checkgroup='+field+']').attr('onclick', 'return false');
        $('form[name='+form_name+'] [checkgroup='+field+']').css('pointer-events', 'none');
        $('form[name='+form_name+'] [checkgroup='+field+']').parent().css('pointer-events', 'none');
        $('form[name='+form_name+'] [checkgroup='+field+']').parent().attr('disabled', '');
        $('form[name='+form_name+'] [checkgroup='+field+']').parent().css('color', 'gray');
    },1);
}

function tcheckgroup_clear_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [checkgroup='+field+']').prop('checked', false)
    },1);
}