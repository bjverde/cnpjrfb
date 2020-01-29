function tjquerydialog_start( id, modal, draggable, resizable, width, height, top, left, zIndex, actions, close_action, close_escape, dialog_class) {
	$( id ).dialog({
		modal: modal,
		stack: false,
		zIndex: 2000,
        draggable: draggable,
        resizable: resizable,
        closeOnEscape: close_escape,
		height: height,
		width: width,
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