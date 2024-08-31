function tseekbutton_enable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name='+field+']').attr('disabled', false);
        $('form[name='+form_name+'] [name='+field+']').removeClass('tfield_disabled').addClass('tseekentry');
    } catch (e) {
        console.log(e);
    }
    setTimeout(function(){ $('form[name='+form_name+'] [for='+field+']').show() },1);
} 
                            
function tseekbutton_disable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name='+field+']').attr('disabled', true);
        $('form[name='+form_name+'] [name='+field+']').addClass('tfield_disabled').removeClass('tseekentry');
    } catch (e) {
        console.log(e);
    }
    setTimeout(function(){ $('form[name='+form_name+'] [for='+field+']').hide() },1);
}