/**
 * Open a window
 */
function __adianti_window(title, width, height, content)
{
    return $('<div />').html(content).dialog({
        modal: true,
        title: title,
        width : width,
        height : height,
        resizable: true,
        closeOnEscape:true,
        close: function(ev, ui) { $(this).remove(); },
        focus:true
    });
}

function __adianti_window_page(title, width, height, page)
{
    if (width<2)
    {
        width = $(window).width() * width;
    }
    if (height<2)
    {
        height = $(window).height() * height;
    }

    $('<div />').append($("<iframe style='width:100%;height:97%' />").attr("src", page)).dialog({
        modal: true,
        title: title,
        width : width,
        height : height,
        resizable: false,
        closeOnEscape:true,
        close: function(ev, ui) { $(this).remove(); },
        focus:true
    });
}