function tspinner_start(id, callback)
{
    $(id).wrap( '<div class="input-group spinner" data-trigger="spinner">' );
    $(id).after( '<div class="input-group-addon"> <a href="javascript:;" class="spin-up" data-spin="up"><i class="fa fa-caret-up"></i></a> <a href="javascript:;" class="spin-down" data-spin="down"><i class="fa fa-caret-down"></i></a> </div>' );
    
    $(id).parent().spinner('changing', function(e, newVal, oldVal) {
        if ( $( id ).attr('exitaction')) {
            new Function( $( id ).attr('exitaction'))();
        }
        
        if (typeof callback == 'function') {
            callback();
        }
    });
}

function tspinner_enable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').parent().css('pointer-events', 'auto');
        $('form[name='+form_name+'] [name='+field+']').removeClass('tfield_disabled');
        $('form[name='+form_name+'] [name='+field+']').removeAttr('readonly');
    },1);
}

function tspinner_disable_field(form_name, field) {
    setTimeout(function(){
        $('form[name='+form_name+'] [name='+field+']').parent().css('pointer-events', 'none');
        $('form[name='+form_name+'] [name='+field+']').addClass('tfield_disabled');
        $('form[name='+form_name+'] [name='+field+']').attr('readonly', true);
    },1);
}