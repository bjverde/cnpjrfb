function tradiogroup_enable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').attr('onclick', null);
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events',   'auto');
        $('form[name='+form_name+'] [name='+field+']').parent().css('pointer-events', 'auto');
        $('form[name='+form_name+'] [name='+field+']').parent().removeAttr('disabled');
        $('form[name='+form_name+'] [name='+field+']').parent().css('color', '');
    },1);
}

function tradiogroup_disable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').attr('onclick', 'return false');
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events', 'none');
        $('form[name='+form_name+'] [name='+field+']').parent().css('pointer-events', 'none');
        $('form[name='+form_name+'] [name='+field+']').parent().attr('disabled', '');
        $('form[name='+form_name+'] [name='+field+']').parent().css('color', 'gray');
    },1);
}

function tradiogroup_clear_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').attr('checked', false) },1);    
}
