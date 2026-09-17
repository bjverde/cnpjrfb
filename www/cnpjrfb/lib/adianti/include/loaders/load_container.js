function __adianti_load_container(container, content, url)
{
    if (container == 'adianti_right_panel') {
        var page_container = content.match('page_name\\s?=\\s?"([0-z-]*)"');
        
        if (page_container) {
            var page_name = page_container[1];
        }
        
        if ($('#adianti_right_panel').is(":visible") == false) {
            $('#adianti_right_panel').html('');
        }
        
        if ($('[page_name='+page_name+']').length > 0)
        {
            $('[page_name='+page_name+']').empty();
            $('[page_name='+page_name+']').replaceWith(content);
        }
        else
        {
            $('#adianti_right_panel').append(content);
        }
    }
    else {
        var page_fragment = url.match('page_fragment=([0-z-]*)');
        
        $('#'+container).empty();
        
        if (page_fragment !== null) {
            var domstring = $(content);
            $('#'+container).replaceWith(domstring.find('#'+page_fragment[1]));
        }
        else {
            $('#'+container).html(content);
        }
    }
}