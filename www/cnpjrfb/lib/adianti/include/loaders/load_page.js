/**
 * Open a page using ajax
 */
function __adianti_load_page(page, callback, run_events)
{
    if (typeof run_events == 'undefined')
    {
        run_events = true;
    }
    
    if (typeof page !== 'undefined')
    {
        $( '.modal-backdrop' ).remove();
        var url = page;
        url = url.replace('index.php', 'engine.php');

        if (url.indexOf('engine.php') == -1)
        {
            url = 'xhr-'+url;
        }
        
        if (run_events) {
            __adianti_run_before_loads(url);
        }
        
        if ( (url.indexOf('&static=1') > 0) || (url.indexOf('?static=1') > 0) )
        {
            $.get(url)
            .done(function(data) {
                __adianti_parse_html(data, null, url);
                
                Adianti.requestURL  = url;
                Adianti.requestData = null;
                
                if (typeof callback == "function")
                {
                    callback(data);
                }
                
                if (run_events) {
                    __adianti_run_after_loads(url, data);
                }
                
            }).fail(function(jqxhr, textStatus, exception) {
               __adianti_error('Error', textStatus + ': ' + __adianti_failure_message());
               Adianti.loading = false;
            });
        }
        else
        {
            $.get(url)
            .done(function(data) {
                Adianti.requestURL  = url;
                Adianti.requestData = null;
                
                __adianti_load_html(data, __adianti_run_after_loads, url);
                
                if (typeof callback == "function")
                {
                    callback(data);
                }
                
                if ( url.indexOf('register_state=false') < 0 && history.pushState && (data.indexOf('widget="TWindow"') < 0) )
                {
                    __adianti_register_state(url, 'adianti');
                    Adianti.currentURL = url;
                }
            }).fail(function(jqxhr, textStatus, exception) {
               __adianti_failure_request(jqxhr, textStatus, exception);
               Adianti.loading = false;
            });
        }
    }
}

function __adianti_load_page_no_register(page)
{
    $.get(page)
    .done(function(result) {
        __adianti_load_html(result, null, page);
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_failure_request(jqxhr, textStatus, exception);
    });
}

/**
 * Get remote content
 */
function __adianti_get_page(action, callback, postdata, method)
{
    if (typeof method == 'undefined') {
        method = 'GET';
    }
    
    if (action.substring(0,5) == 'class') {
        var uri = 'engine.php?' + action +'&static=1';
    }
    else {
        //var uri = 'xhr-' + action +'&static=1';
        var uri = 'xhr-' + action + ( (action.indexOf('?') == -1) ? '?' : '&') + 'static=1';
    }
    
    if (typeof postdata !== "undefined") {
        if (typeof postdata.static !== "undefined") {
            uri = uri.replace('&static=1', '&static='+postdata.static);
            uri = uri.replace('?static=1', '?static='+postdata.static);
        }
    }
    
    $.ajax({
      url: uri,
      type: method,
      data: postdata
      }).done(function( result ) {
          return callback(result);
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_failure_request(jqxhr, textStatus, exception);
      });
}

/**
 * Called by Seekbutton. Add the page content.
 */
function __adianti_append_page(page, callback)
{
    page = page.replace('engine.php?','');
    params_json = __adianti_query_to_json(page);

    uri = 'engine.php?'
        + 'class=' + params_json.class
        + '&method=' + params_json.method
        + '&static=' + (params_json.static == '1' ? '1' : '0');

    $.post(uri, params_json)
    .done(function(content){
        if (content.indexOf('widget="TWindow"') > 0) {
            __adianti_load_window_content(content);
        }
        else {
            $('#adianti_online_content').after('<div></div>').html(content);
        }

        if (typeof callback == "function")
        {
            callback();
        }
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_failure_request(jqxhr, textStatus, exception);
    });
}
