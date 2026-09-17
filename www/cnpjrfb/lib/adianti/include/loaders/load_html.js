/**
 * Loads an HTML content
 */
function __adianti_load_html(content, afterCallback, url)
{
    var url_container = url.match('target_container=([0-z-]*)');
    var dom_container = content.match('adianti_target_container\\s?=\\s?"([0-z-]*)"');
    var page_name = content.match('page-name\\s?=\\s?"([0-z-]*)"');
    
    if (typeof Template.onBeforeLoadContent == "function") {
        content = Template.onBeforeLoadContent(content);
    }
    
    if (content.indexOf('widget="TWindow"') > 0)
    {
        __adianti_load_window_content(content);
    }
    else if (dom_container !== null)
    {
        var target_container = dom_container[1];
        __adianti_load_container(target_container, content, url);
    }
    else if (url_container !== null)
    {
        var target_container = url_container[1];
        __adianti_load_container(target_container, content, url);
    }
    // found a page wrapper in dom with the requested page inside
    else if (page_name !== null && $('[widget="tpagewrapper"]>div[page-name="'+page_name[1]+'"]').length > 0)
    {
        var target_container = $('[widget="tpagewrapper"]>div[page-name="'+page_name[1]+'"]').parent().attr('id');
        __adianti_load_container(target_container, content, url);
    }
    else
    {
        if (typeof Adianti.onClearDOM == "function")
        {
            Adianti.onClearDOM();
        }

        $('[widget="TWindow"]').remove();
        $('#adianti_div_content').html(content);
    }

    if (typeof afterCallback == "function")
    {
        afterCallback(url, content);
    }
}



/**
 * Parse returning HTML
 */
function __adianti_parse_html(content, callback, url = '')
{
    if (typeof Template.onBeforeLoadContent == "function") {
        content = Template.onBeforeLoadContent(content, true);
    }
    
    tmp = content;
    tmp = new String(tmp.replace(/window\.opener\./g, ''));
    tmp = new String(tmp.replace(/window\.close\(\)\;/g, ''));
    tmp = new String(tmp.replace(/^\s+|\s+$/g,""));
    
    try {
        // permite código estático também escolher o target
        var url_container = url.match('target_container=([0-z-]*)');
        var dom_container = content.match('adianti_target_container\\s?=\\s?"([0-z]*)"');
        
        if ( dom_container !== null || url_container !== null )
        {
            var target_container = (dom_container !== null) ? dom_container[1] : url_container[1];
            
            if (target_container == 'adianti_right_panel')
            {
                $('#'+target_container).append(tmp);
            }
            else
            {
                var page_fragment = url.match('page_fragment=([0-z-]*)');
                
                if (page_fragment !== null) {
                    var domstring = $(tmp.toString());
                    $('#'+target_container).replaceWith(domstring.find('#'+page_fragment[1]));
                }
                else {
                    if ( (url.indexOf('&static=1') > 0) || (url.indexOf('?static=1') > 0) ) {
                        $('#'+target_container).append(tmp);
                    }
                    else {
                        $('#'+target_container).empty();
                        $('#'+target_container).html(tmp);
                    }
                }
            }
        }
        else
        {
            // target default
            $('#adianti_online_content > script').remove();
            $('#adianti_online_content').append(tmp);
        }

        if (callback && typeof(callback) === "function")
        {
            callback(content);
        }

    } catch (e) {
        if (e instanceof Error) {
            $('<div />').html(e.message + ': ' + tmp).dialog({modal: true, title: 'Error', width : '80%', height : 'auto', resizable: true, closeOnEscape:true, focus:true});
        }
    }
}