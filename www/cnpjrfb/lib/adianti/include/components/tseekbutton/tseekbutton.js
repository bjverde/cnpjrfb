function tseekbutton_enable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    try {
        $(selector).attr('disabled', false);
        $(selector).removeClass('tfield_disabled').addClass('tseekentry');
    } catch (e) {
        console.log(e);
    }
    setTimeout(function(){ $('form[name='+form_name+'] [for='+field+']').show() },1);
} 
                            
function tseekbutton_disable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    try {
        $(selector).attr('disabled', true);
        $(selector).addClass('tfield_disabled').removeClass('tseekentry');
    } catch (e) {
        console.log(e);
    }
    setTimeout(function(){ $('form[name='+form_name+'] [for='+field+']').hide() },1);
}