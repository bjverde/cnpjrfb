function tdialog_start(id, callback)
{
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
    $( id ).modal({backdrop:true, keyboard:true});
    
    if (typeof callback != 'undefined')
    {
        $( id ).on("hidden.bs.modal", function(){ callback(); tdialog_close(id.substring(1));  } );
    }
    else
    {
        $( id ).on("hidden.bs.modal", function(){ tdialog_close(id.substring(1));  } );

    }
}

function tdialog_close(id)
{
    $( '.modal-backdrop' ).last().remove();
    $('#'+id).modal('hide');
    $('body').removeClass('modal-open');
    setTimeout(function(){ $('#'+id).remove(); }, 300);
}