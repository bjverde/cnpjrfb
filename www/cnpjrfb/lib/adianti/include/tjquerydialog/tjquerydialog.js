function tjquerydialog_start( id, modal, draggable, resizable, width, height, top, left, zIndex, actions, close_action, close_escape, dialog_class) {
	if ($( id ).parent().attr('role') == 'window-wrapper') {
	    var window_container = '#' + $( id ).parent().attr('id');
	}
	else {
	    var window_container = 'body';
	}
	
	$( id ).dialog({
		modal: modal,
		stack: false,
		zIndex: 2000,
        draggable: draggable,
        resizable: resizable,
        closeOnEscape: close_escape,
		height: height,
		width: width,
		appendTo: window_container,
		dialogClass: dialog_class,
		beforeClose: function() {
		    if (typeof(close_action) == "function") {
		        close_action();
		        return false;
		    }
		    return true;
		},
		close: function(ev, ui) {
            $(this).remove();
            $(".tooltip.fade").remove();
            var window_name = ($(this).attr('name'));
            $('[window_name='+window_name+']').remove();
		},
		buttons: actions
	});
	
	$('.ui-dialog').last().focus();
	
	$( id ).closest('.ui-dialog').css({ zIndex: zIndex });
	
	$(".ui-widget-overlay").css({ zIndex: 100 });
	
	if (top > 0) {
	    $( id ).closest('.ui-dialog').css({ top: top+'px' });
	}
	
	if (left > 0) {
	    $( id ).closest('.ui-dialog').css({ left: left+'px' });
	}
}

function tjquerydialog_block_ui()
{
    $( document ).ready(function() {
        $('.ui-dialog').css('pointer-events', 'none');
        $('.ui-dialog-content').css('opacity', '0.5');
    });
}

function tjquerydialog_unblock_ui()
{
    $('.ui-dialog').css('pointer-events', 'all');
    $('.ui-dialog-content').css('opacity', '');
}