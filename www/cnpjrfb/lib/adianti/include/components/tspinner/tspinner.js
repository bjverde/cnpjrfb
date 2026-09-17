function tspinner_start(id, callback, stepper)
{
    var classStepper = stepper ? 'tstepper' :  '' ;
    $(id).wrap( '<div class="input-group spinner ' + classStepper + '" data-trigger="spinner">' );
    if (stepper) {
        $(id).before('<a tabindex="-1" href="javascript:;" class="spin-minus" data-spin="down"><i class="fa fa-minus"></i></a>');
        $(id).after( '<a tabindex="-1" href="javascript:;" class="spin-plus" data-spin="up"><i class="fa fa-plus"></i></a>' );
    } else {
        $(id).after( '<div class="input-group-addon"> <a tabindex="-1" href="javascript:;" class="spin-up" data-spin="up"><i class="fa fa-caret-up"></i></a> <a tabindex="-1" href="javascript:;" class="spin-down" data-spin="down"><i class="fa fa-caret-down"></i></a> </div>' );
    }
    
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
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){
        $(selector).parent().css('pointer-events', 'auto');
        $(selector).removeClass('tfield_disabled');
        $(selector).removeAttr('readonly');
    },1);
}

function tspinner_disable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    setTimeout(function(){
        $(selector).parent().css('pointer-events', 'none');
        $(selector).addClass('tfield_disabled');
        $(selector).attr('readonly', true);
    },1);
}