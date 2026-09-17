/**
 * Ajax lookup
 */
function __adianti_ajax_lookup(action, field)
{
    var value = field.value;
    __adianti_ajax_exec(action +'&key='+value+'&ajax_lookup=1', null);
}

/**
 * Execute an Ajax action
 */
function __adianti_ajax_exec(action, callback, automatic_output)
{
    if (action.substring(0,5) == 'class') {
        var uri = 'engine.php?' + action +'&static=1';
    }
    else {
        //var uri = 'xhr-' + action +'&static=1';
        var uri = 'xhr-' + action + ( (action.indexOf('?') == -1) ? '?' : '&') +'static=1';
    }
    
    var automatic_output = (typeof automatic_output === "undefined") ? true : automatic_output;

    $.ajax({url: uri})
    .done(function( result ) {
        if (automatic_output) {
            __adianti_parse_html(result, callback, uri);
        }
        else {
            callback(result);
        }
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_failure_request(jqxhr, textStatus, exception);
    });
}