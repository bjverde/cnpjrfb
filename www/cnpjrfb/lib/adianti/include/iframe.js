function IFrame(){}

function importFunction(fn, win) {
    var code = fn.toString();
    var params = code.match(/\(([^)]*)\)/);
    if (typeof(params[1]) !== 'undefined') {
      params = params[1].split(/\s*,\s*/);
    } else {
      params = null;
    }
    code = code.replace(/^[^{]*{/, '');
    code = code.replace(/}$/, '');
    if (params) {
      return new win.Function(params, code);
    } 
    return new win.Function(code);
}

function __adianti_load_page(page, callback)
{
    if (typeof page !== 'undefined')
    {
        var load_page_func = importFunction(window.top.__adianti_load_page, this);
            
        if (page.indexOf('inside_iframe') == -1) {
            if (page.indexOf('?') == -1) {
                load_page_func(page+'?inside_iframe=1', callback);
            }
            else {
                load_page_func(page+'&inside_iframe=1', callback);
            }
        }
        else {
            load_page_func(page, callback);
        }
    }
}

function __adianti_post_data(form, action)
{
    var post_data_func = importFunction(window.top.__adianti_post_data, this);
    
    if (action.indexOf('inside_iframe') == -1) {
        if (action.indexOf('&') == -1) {
            post_data_func(form, action+'?inside_iframe=1');
        }
        else {
            post_data_func(form, action+'&inside_iframe=1');
        }
    }
    else {
        post_data_func(form, action);
    }
}