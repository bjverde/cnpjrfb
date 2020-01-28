function tdatetime_start( id, mask, language, size, options) {
    $( id ).wrap( '<div class="tdate-group tdatetimepicker input-append date">' );
    $( id ).after( '<span class="add-on btn btn-default tdate-group-addon"><i class="far fa-clock icon-th"></i></span>' );
    
    atributes = {
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        format: mask,
        language: language
    };
    
    options = Object.assign(atributes, JSON.parse( options) );
    
    $( id ).closest('.tdate-group').datetimepicker(options).on('change.dp', function(e){
        if ( $( id ).attr('exitaction')) {
            new Function( $ ( id ).attr('exitaction'))();
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
