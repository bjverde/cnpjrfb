function tdialog_start(id, callback)
{
    let modal = new bootstrap.Modal(id, {keyboard:true});
    modal.show();
    
    if (typeof callback != 'undefined')
    {
        $( id ).on("hidden.bs.modal", function(){ callback(); tdialog_close(id.substring(1));  } );
    }
    else
    {
        $( id ).on("hidden.bs.modal", function(){ tdialog_close(id.substring(1));  } );
    }
    
    // reparent of select2 objects (otherwise, can't focus on input search)
    $(id+' .byselect2:not([multiple])').each(function () {
        var obj, parent;
        obj = $(this).data('select2-options');
        parent = $(this).closest('.modal'); // if there is a modal that select is inside it
        if (parent.length) {
            obj['dropdownParent'] = parent;
        }
        $(this).select2(obj);
    });
    
    // release focus to avoid "Blocked aria-hidden on an element because its descendant retained focus"
    $( id ).on("hide.bs.modal", function(){   
            document.activeElement.blur();
    });
}

function tdialog_close(id)
{
    $('#'+id).modal('hide');
    setTimeout(function(){ $('#'+id).remove(); }, 300);
}