function tslider_start(id, value, min, max, step)
{
    var value = $(id).val();
    $(id).wrap( '<div class="tslidercontainer">' );
    $(id).before( '<div class="label">'+value+'</div>' );
    
    $(id).on('input', function() {
        $(id).parent().find('.label').html(this.value);
    });
}

function tslider_enable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events', 'auto');
        $('form[name='+form_name+'] [name='+field+']').removeClass('tfield_disabled');
    },1);
}

function tslider_disable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').css('pointer-events', 'none');
        $('form[name='+form_name+'] [name='+field+']').addClass('tfield_disabled');
    },1);    
}
