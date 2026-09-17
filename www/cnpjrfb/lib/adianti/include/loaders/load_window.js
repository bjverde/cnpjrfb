function __adianti_load_window_content(content)
{
    var win_container = content.match('window_name\\s?=\\s?"([0-z-]*)"');
    var window_name = win_container[1];
    var window_count = $('[window_name]').filter(function() { return ($(this).html().length > 1); }).length;
    
    // no window opened or reload the current window
    if ( window_count == 0 || (window_count == 1 && $('[window_name='+window_name+']').length ==1) )
    {
        $('#adianti_online_content').empty();
    }
    
    if ($('[window_name='+window_name+']').length > 0)
    {
        $('[window_name='+window_name+']').empty();
        $('[window_name='+window_name+']').replaceWith(content);
    }
    else
    {
        $('#adianti_online_content').append(content);
    }
}