if (window.location === window.parent.location) {
    $(function () {
        $.AdminBSB.browser.activate();
        $.AdminBSB.leftSideBar.activate();
        $.AdminBSB.rightSideBar.activate();
        $.AdminBSB.navbar.activate();
        $.AdminBSB.dropdownMenu.activate();
        $.AdminBSB.input.activate();
        $.AdminBSB.select.activate();
        $.AdminBSB.search.activate();
    
        __adianti_block_ui = function (wait_message) {
            if (typeof wait_message !== 'undefined') {
                $('#page-loader-message').html( wait_message );
            }
            $('.page-loader-wrapper').show();
        };
        __adianti_unblock_ui = function () { $('.page-loader-wrapper').fadeOut(); };
        setTimeout(function () { $('.page-loader-wrapper').fadeOut(); }, 50);
        
        setTimeout( function() {
            $('#envelope_messages a').click(function() { $(this).closest('.dropdown.open').removeClass('open'); });
            $('#envelope_notifications a').click(function() { $(this).closest('.dropdown.open').removeClass('open'); });
        }, 500);
        
        $('.menu i.fa, .menu i.fas, .menu i.far, .menu i.fab').css('zoom', '120%');
        $('.menu i.fa, .menu i.fas, .menu i.far, .menu i.fab').css('margin-top', '8px');
        $('.menu ul li ul li i.fa, .menu ul li ul li i.far, .menu ul li ul li i.fas, .menu ul li ul li i.fab').css('margin-top', '5px');
        
        $('#leftsidebar a[generator="adianti"]').click(function() {
            $('body').scrollTop(0);
            $('body').removeClass('overlay-open');
            $('.overlay').hide();
        });
    });
}

/**
 * Show message info dialog
 */
function __adianti_message(title, message, callback)
{
    __adianti_dialog( { type: 'success', title: title, message: message, callback: callback} );
}

/**
 * Show standard dialog
 */
function __adianti_dialog( options )
{
    setTimeout( function() {
        swal({
          html: true,
          title: options.title,
          text: options.message,
          type: options.type,
          allowEscapeKey: (typeof options.callback == 'undefined'),
          allowOutsideClick: (typeof options.callback == 'undefined')
        },
        function(){
            if (typeof options.callback != 'undefined') {
                options.callback();
            }
        });
    }, 100);
}

/**
 * Show question dialog
 */
function __adianti_question(title, message, callback_yes, callback_no, label_yes, label_no)
{
    setTimeout( function() {
        swal({
          html: true,
          title: title,
          text: message,
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: label_yes,
          cancelButtonText: label_no
        },
        function(isConfirm){
          if (isConfirm) {
            if (typeof callback_yes != 'undefined') {
                callback_yes();
            }
          } else {
            if (typeof callback_no != 'undefined') {
                callback_no();
            }
          }
        });
    }, 100);
}

function tdate_start( id, mask, language, size, options) {
    $( id ).attr("onblur", "");
    $( id ).wrap( '<div class="tdate-group date">' );
    $( id ).after( '<span class="btn btn-default tdate-group-addon"><i class="far fa-calendar"></i></span>' );
    
    mask = mask.replace('yyyy', 'YYYY', mask);
    mask = mask.replace('mm', 'MM', mask);
    mask = mask.replace('dd', 'DD', mask);
    
    atributes = {
        lang: language,
        weekStart : 0,
        time: false,
        format : mask,
        switchOnClick: true,
        clearButton: true,
    };
    
    switch (language){
        case 'es':
        	atributes.cancelText = 'Cancelar';
        	atributes.okText = 'Listo';
        	atributes.clearText = 'Limpiar';
        	break;
        case 'pt':
        	atributes.cancelText = 'Cancelar';
        	atributes.okText = 'Ok';
        	atributes.clearText = 'Limpar';
        	break;
    }
    
    options = Object.assign(atributes, JSON.parse( options) );
    
    $( id ).bootstrapMaterialDatePicker(options).on('change', function(e, date) {
        if ( $( id ).attr('exitaction')) {
            new Function( $ ( id ).attr('exitaction'))();
        }
    });
    
    if (size !== 'undefined')
    {
        $( id ).closest('.tdate-group').width(size);
    }
}

function tdatetime_start( id, mask, language, size, options) {
    $( id ).wrap( '<div class="tdate-group tdatetimepicker input-append date">' );
    $( id ).after( '<span class="add-on btn btn-default tdate-group-addon"><i class="far fa-clock icon-th"></i></span>' );
    
    mask = mask.replace('yyyy', 'YYYY', mask);
    mask = mask.replace('mm', 'MM', mask);
    mask = mask.replace('dd', 'DD', mask);
    mask = mask.replace('hh', 'HH', mask);
    mask = mask.replace('ii', 'mm', mask);
    
    atributes = {
        lang: language,
        weekStart : 0,
        format : mask,
        switchOnClick: true,
        clearButton: true,
    };
    
    switch (language){
        case 'es':
        	atributes.cancelText = 'Cancelar';
        	atributes.okText = 'Listo';
        	atributes.clearText = 'Limpiar';
        	break;
        case 'pt':
        	atributes.cancelText = 'Cancelar';
        	atributes.okText = 'Ok';
        	atributes.clearText = 'Limpar';
        	break;
    }
    
    options = Object.assign(atributes, JSON.parse( options) );
    if (options.pickDate == false)
    {
        options.date = false;
    }
    
    $( id ).bootstrapMaterialDatePicker(options).on('change', function(e, date) {
        if ( $( id ).attr('exitaction')) {
            new Function( $ ( id ).attr('exitaction'))();
        }
    });
    
    if (size !== 'undefined')
    {
        $( id ).closest('.tdate-group').width(size);
    }
}

function tdate_set_value(id, value)
{
    $(id).val(value);
}

$( document ).on( 'click', 'ul.dropdown-menu a[generator="adianti"]', function() {
    $(this).parents(".dropdown.show").removeClass("show");
    $(this).parents(".dropdown-menu.show").removeClass("show");
});
