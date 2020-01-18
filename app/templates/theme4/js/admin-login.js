if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}

$.AdminBSB = {};
//==========================================================================================================================

/* Input - Function ========================================================================================================
*  You can manage the inputs(also textareas) with name of class 'form-control'
*  
*/
$.AdminBSB.input = {
    activate: function () {
        //On focus event
        $('.form-control').focus(function () {
            $(this).parent().addClass('focused');
        });

        //On focusout event
        $('.form-control').focusout(function () {
            var $this = $(this);
            if ($this.parents('.form-group').hasClass('form-float')) {
                if ($this.val() == '') { $this.parents('.form-line').removeClass('focused'); }
            }
            else {
                $this.parents('.form-line').removeClass('focused');
            }
        });

        //On label click
        $('body').on('click', '.form-float .form-line .form-label', function () {
            $(this).parent().find('input').focus();
        });

    }
}
//==========================================================================================================================

/* Form - Select - Function ================================================================================================
*  You can manage the 'select' of form elements
*  
*/
$.AdminBSB.select = {
    activate: function () {
        if ($.fn.selectpicker) { $('select:not(.ms)').selectpicker(); }
    }
}

//==========================================================================================================================
$(document).ajaxComplete(function () {
    $.AdminBSB.input.activate();
});

$(function () {
    $.AdminBSB.input.activate();
    $.AdminBSB.select.activate();

    __adianti_block_ui = function () { $('.page-loader-wrapper').show(); };
    __adianti_unblock_ui = function () { $('.page-loader-wrapper').fadeOut(); };
    setTimeout(function () { $('.page-loader-wrapper').fadeOut(); }, 50);
});
