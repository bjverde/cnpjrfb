function tdate_enable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field);
    
    try {
        $(selector).removeClass('tfield_disabled');
        $(selector).css('pointer-events', 'auto');
        $(selector).removeAttr('tabindex');
        $(selector).attr('readonly', false);
        
    } catch (e) {
        console.log(e);
    }
    setTimeout(function() {
        $(selector).next().show();
    },1);
} 

function tdate_disable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field);
    
    try {
        $(selector).addClass('tfield_disabled');
        $(selector).css('pointer-events', 'none');
        $(selector).attr('tabindex', "-1");
        $(selector).attr('readonly', true);
    } catch (e) {
        console.log(e);
    }
    setTimeout(function(){
        $(selector).next().hide();
    },1);
}

function tdate_set_value(id, value)
{
    $(id).val(value);
    $(id).closest('.tdate-group').datepicker('update');
}

function tdate_start( id, mask, language, size, options) {
    $( id ).wrap( '<div class="tdate-group date">' );
    $( id ).after( '<span class="btn btn-default tdate-group-addon"><i class="far fa-calendar"></i></span>' );
    
    var attributes = {
        format: mask,
        todayBtn: "linked",
        language: language,
        calendarWeeks: false,
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    };
    
    options = Object.assign(attributes, JSON.parse( options) );
    
    $( id ).closest('.tdate-group').datepicker(options).on('changeDate', function(e){
        if ( $( id ).attr('exitaction')) {
            new Function( $( id ).attr('exitaction'))();
        }
    }).on('show', function() {
        // to avoid fire $('body').on('click') when selecting date inside popover
        // without this, it would close the popover, because the click event bound to body
        $('.datepicker').on('click', function (e) {
            e.stopPropagation();
        });
    });
    
    if (size !== 'undefined')
    {
        $( id ).closest('.tdate-group').width(size);
    }
}
