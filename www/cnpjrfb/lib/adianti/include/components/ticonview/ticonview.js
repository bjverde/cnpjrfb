function ticonview_contextmenu_start(id)
{
    $('#'+id).contextmenu(function(event) {
        
        $('.dropdown-iconview').hide();
        var context_menu = $('#'+id).next('ul');
        
        context_menu.css('left', ticonview_mouseX(event, $('#'+id)));
        context_menu.css('top', ticonview_mouseY(event, $('#'+id)));
        context_menu.show();
        
        event.preventDefault();
    });
    
    return false;
}

function ticonview_bind_click()
{
    $(document).bind("click", function(event) {
        $('.dropdown-iconview').hide();
    });
}

function ticonview_mouseX(evt, generator)
{
    var offset_left = 0;
    if ($(generator).closest('.ui-dialog').length > 0)
    {
        offset_left = $(generator).closest('.ui-dialog').offset().left;
    }
    
    if (evt.pageX) {
        return evt.pageX - offset_left;
    } else if (evt.clientX) {
       return evt.clientX + (document.documentElement.scrollLeft ?
           document.documentElement.scrollLeft :
           document.body.scrollLeft) - offset_left;
    } else {
        return null;
    }
}

function ticonview_mouseY(evt, generator)
{
    var offset_top = 0;
    if ($(generator).closest('.ui-dialog').length > 0)
    {
        offset_top = $(generator).closest('.ui-dialog').offset().top + 50;
    }
    
    if (evt.pageY) {
        return evt.pageY - offset_top;
    } else if (evt.clientY) {
       return evt.clientY + (document.documentElement.scrollTop ?
       document.documentElement.scrollTop:
       document.body.scrollTop) - offset_top;
    } else {
        return null;
    }
}

function ticonview_move_start(id, move_action, source_selector, target_selector)
{
    $('#'+id + ' li' + source_selector).draggable(
    {
        zIndex: 100000,
        cursor: 'pointer',
        start: function(e, ui) {
            $(this).css('opacity', 0.5);
            $(this).addClass("iconview-drag");
        },
		stop: function(e, ui) {
		    $(this).css('opacity', 1);
		},
        revert : function(event, ui) {
            $(this).data("uiDraggable").originalPosition = {
                top : 0,
                left : 0
            };
            return !event;
        }
    });
    
    $("ul.ticonview li" + target_selector).droppable({
        accept: '.iconview-drag',
        drop: function(event, ui) {
            var source_data = $(ui.draggable).data();
            var target_data = $(this).data();
            
            var query_params = {};
            
            for (var property in source_data) {
              if (source_data.hasOwnProperty(property) && (typeof source_data[property] == 'string' || typeof source_data[property] == 'number')) {
                query_params['source_'+property] = source_data[property];
              }
            }
            
            for (var property in target_data) {
              if (target_data.hasOwnProperty(property) && (typeof target_data[property] == 'string' || typeof target_data[property] == 'number')) {
                query_params['target_'+property] = target_data[property];
              }
            }
            __adianti_load_page(move_action+"&" + $.param(query_params));
        },
        over: function( event, ui ) {
          $(this)
            .addClass("ui-over")
            .addClass($(this).attr('id'));
        },
        out: function( event, ui ) {
          $(this)
            .removeClass("ui-over")
          }
    });
}