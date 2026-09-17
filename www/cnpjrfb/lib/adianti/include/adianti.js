function Adianti(){}
function Template(){}

Adianti.start = function() {
    Adianti.blockUIConter = 0;
    
    __adianti_process_popover();
    __adianti_process_tooltips();
    
    __adianti_connect_clicks();
    __adianti_bind_popover_release();
    
    // on each ajax request
    $(document).ajaxComplete( Adianti.onRequest );
    
    // page navigation
    window.onpopstate = function(stackstate) {
        if (stackstate.state) {
            __adianti_load_page_no_register(stackstate.state.url);
        }
    };
    
    // prevent ui dialog to focus on the first element.
    if (typeof jQuery.ui !== 'undefined') {
        $.ui.dialog.prototype._focusTabbable = $.noop;
    }
}

Adianti.configure = function(options) {
    Adianti.options = options;
    Adianti.setLanguage(options['language']);
    Adianti.setAppName(options['application']);
    Adianti.setDebug(options['debug']);
}

Adianti.getConfigureOptions = function() {
    return Adianti.options;
}

/**
 * On Ajax complete actions
 */
Adianti.onRequest = function() {
    __adianti_process_popover();
    __adianti_process_tooltips();
    __adianti_process_datatables();
}

Adianti.setLanguage = function(language) {
    __adianti_set_language(language);
}

Adianti.setAppName = function(name) {
    __adianti_set_name(name);
}

Adianti.setDebug = function(bool) {
    __adianti_set_debug(bool);
}

/**
 * Define the application name
 */
function __adianti_set_name(application)
{
    Adianti.applicationName = application;
}

function __adianti_set_language(lang)
{
    Adianti.language = lang;
}

function __adianti_set_debug(debug)
{
    Adianti.debug = debug;
}

function __adianti_connect_clicks()
{
    /**
     * Override the default page loader
     */
    $( document ).on( 'click', '[generator="adianti"]', function()
    {
        let href = $(this).attr('href') || '';
        if (href.indexOf('&adianti_request_method=post') !== -1) {
            let form_name = $(this).closest('form').attr('name');
            let action    = $(this).attr('href').replace('index.php?', '');
            
            __adianti_post_data(form_name, action, true, {wrapper_variable: 'form_data', blockui: false});
        }
        else if (href) {
           __adianti_load_page(href);
        }
       return false;
    });
}

function __adianti_bind_popover_release()
{
    /**
     * Close tooltips on click
     */
    $('body').on('click', function (e) {
        $('.tooltip.show').tooltip('hide');
        
        if ( $(e.target).parents('.popover').length == 0 && $(e.target).attr('poptrigger') !== "click" ) {
            // avoid closing dropdowns inside popover (colorpicker, datepicker) when they are outside popover DOM
            if ( (!$(e.target).parents('.dropdown-menu').length > 0) && (!$(e.target).parents('.select2-dropdown').length > 0) ) {
                //$('.popover').popover().hide();
                $('.popover:not(.note-popover)').remove();
            }
        }
        // click popover inside popover, remove the next one
        else if ( $(e.target).parents('.popover').length > 0 ) {
            $(e.target).parents('.popover').nextAll('.popover').remove();
        }
    });
}/**
 * Show standard dialog
 */
function __adianti_dialog( options )
{
    if (options.type == 'info') {
        var icon = (options.icon ? options.icon : 'fa fa-info-circle fa-4x icon-dialog text-info');
    }
    else if (options.type == 'warning') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-triangle fa-4x icon-dialog text-warning');
    }
    else if (options.type == 'error') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-circle fa-4x icon-dialog text-danger');
    }

    if (typeof bootbox == 'object')
    {
        bootbox.dialog({
          title: options.title,
          animate: false,
          backdrop: true,
          onEscape: function() {
            if (typeof options.callback != 'undefined')
            {
                options.callback();
            }
          },
          message: '<div>'+
                    '<span class="'+icon+'" style="float:left"></span>'+
                    '<span style="margin-left:70px;display:block;max-height:500px">'+options.message+'</span>'+
                    '</div>',
          buttons: {
            success: {
              label: "OK",
              className: "btn-primary",
              callback: function() {
                if (typeof options.callback != 'undefined')
                {
                    options.callback();
                }
              }
            }
          }
        });
    }
    else {
        // fallback mode
        alert(options.message);
        if (typeof options.callback != 'undefined') {
            options.callback();
        }
    }
}

/**
 * Show message error dialog
 */
function __adianti_error(title, message, callback)
{
    __adianti_dialog( { type: 'error', title: title, message: message, callback: callback} );
}

/**
 * Show message info dialog
 */
function __adianti_message(title, message, callback)
{
    __adianti_dialog( { type: 'info', title: title, message: message, callback: callback} );
}

/**
 * Show message warning dialog
 */
function __adianti_warning(title, message, callback)
{
    __adianti_dialog( { type: 'warning', title: title, message: message, callback: callback} );
}

/**
 * Show question dialog
 */
function __adianti_question(title, message, callback_yes, callback_no, label_yes, label_no)
{
    if (typeof bootbox == 'object')
    {
        bootbox.dialog({
          title: title,
          animate: false,
          message: '<div>'+
                    '<span class="fa fa-question-circle fa-4x icon-dialog" style="float:left"></span>'+
                    '<span style="margin-left:70px;display:block;max-height:500px">'+message+'</span>'+
                    '</div>',
          buttons: {
            yes: {
              label: label_yes,
              className: "btn-primary",
              callback: function() {
                if (typeof callback_yes != 'undefined') {
                    callback_yes();
                }
              }
            },
            no: {
              label: label_no,
              className: "btn-secondary",
              callback: function() {
                if (typeof callback_no != 'undefined') {
                    callback_no();
                }
              }
            },
          }
        });
    }
    else
    {
        // fallback mode
        var r = confirm(message);
        if (r == true) {
            if (typeof callback_yes != 'undefined') {
                callback_yes();
            }
        } else {
            if (typeof callback_no != 'undefined') {
                callback_no();
            }
        }
    }
}

/**
 * Show input dialog
 */
function __adianti_input(question, callback)
{
    if (typeof bootbox == 'object')
    {
        bootbox.prompt(question, function(result) {
          if (result !== null) {
            callback(result);
          }
        });
    }
    else
    {
        var result = prompt(question, '');
        callback(result);
    }
}function __adianti_show_toast64(type, message64, place, icon)
{
    __adianti_show_toast(type, atob(message64), place, icon)
}

function __adianti_show_toast(type, message, place, icon)
{
    var place = place.replace(/(?:^\w|[A-Z]|\b\w|\s+)/g, function(match, index) {
            if (+match === 0) return ""; // or if (/\s+/.test(match)) for white spaces
            return index == 0 ? match.toLowerCase() : match.toUpperCase();
          });

    var options = {
        message: message,
        timeout: 3000,
        position: place
    };

    if (type == 'show') {
        options['progressBarColor'] = 'rgb(0, 255, 184)';
        options['theme'] = 'dark';
    }

    if (typeof icon !== 'undefined') {
        var icon_prefix = icon.substring(0,3);
        if (['far', 'fas', 'fal', 'fad', 'fab'].includes(icon_prefix)) {
            options['icon'] = icon_prefix + ' fa-' + icon.substring(4);
        }
        else {
            options['icon'] = 'fa ' + icon.replace(':', '-');
        }
    }

    iziToast[type]( options );
}
/**
 * Process popovers
 */
function __adianti_process_popover()
{
    var get_placement = function (tip, element) {
        $element = $(element);

        var valid_placements = [
            "auto",
            "top",
            "right",
            "bottom",
            "left",
        ];

        if (typeof $element.attr('popside') === "undefined" || valid_placements.indexOf($element.attr('popside')) === -1) {
            return 'auto';
        }
        else {
            return $(element).attr("popside");
        }
    };

    var get_content = function (element) {
        if (typeof $(element).attr('popaction') === "undefined") {
            if (typeof $(element).attr("popcontent64") !== "undefined") {
                return base64_decode($(element).attr("popcontent64"));
            }
            else {
                return $(element).attr("popcontent") || '';
            }
        }
    };
    
    $('[popover="true"]').removeAttr('popover').attr('data-popover', 'true');
    
    $('[data-popover="true"]:not([poptrigger]):not([data-popover-processed="true"])').popover({
        placement: get_placement,
        trigger: 'hover focus',
        container: 'body',
        template: '<div class="popover" role="tooltip" style="max-width:800px"><div class="popover-arrow"></div><h5 class="popover-header"></h5><div class="popover-body"></div></div>',
        delay: { show: 10, hide: 10 },
        content: get_content,
        html: true,
        title: $(this).attr("poptitle") || '',
        sanitizeFn : function(d) { return d },
    }).attr('data-popover-processed', true);
    
    $('[data-popover="true"][poptrigger="click"]:not([data-popover-processed="true"])').click(function() {
        var element = this;
        var pop_title = $(this).attr("poptitle") || '';
        var custom_options = {
            template: '<div class="popover trigger click" role="tooltip" style="max-width:800px"><div class="popover-arrow"></div><h5 class="popover-header"></h5><div class="popover-body"></div></div>'
        }
        
        if ($(this).attr("popcontent")) {
            __adianti_show_popover(element, pop_title, $(this).attr("popcontent"), 'auto', custom_options);
        }
        else if ($(this).attr("popaction")) {
            __adianti_get_page($(this).attr('popaction'), function(result) {
                // keep parent popover (ex. edit detail inside popover)
                if ($(element).parents('.popover').length == 0) {
                    __adianti_clear_click_popovers();
                }
                
                var query = __adianti_query_to_json($(element).attr('popaction'));
                
                // remove popovers for the same class+method
                //$('.popover[data-class="'+query['class']+'"][data-method="'+query['method']+'"]').remove();
                
                // create the new popover
                var popover = __adianti_show_popover(element, pop_title, result, 'auto', custom_options);
                $(popover).attr('data-class', query['class'] || '' );
                $(popover).attr('data-method', query['method'] || '' );
                
            }, {'static': '0'});
        }
    }).attr('data-popover-processed', true);
}

/**
 * Show popover nearby element
 */
function __adianti_show_popover64(element, title, message, placement, custom_options)
{
    __adianti_show_popover(element, title, base64_decode(message), placement, custom_options);
}

/**
 * Show popover nearby element
 */
function __adianti_show_popover(element, title, message, placement, custom_options)
{
    var standard_options = {trigger:"manual", title:title || '', html: true, content:message, placement:placement, sanitizeFn : function(d) { return d }};
    var options = standard_options;
    
    $(element).popover('dispose');
    
    if (typeof custom_options !== undefined)
    {
        var options = Object.assign(standard_options, custom_options);
    }
    var old_title = $(element).data('original-title');
    
    // troca o title temporariamente, por que a popover dá prioridade para o title do dom, no lugar do title passado nas options
    if (typeof title !== 'undefined') {
        $(element).attr('title', title);
        $(element).attr('data-original-title', title);
    }
    
    if ($(element).length>0 && $(element).css("visibility") == "visible") {
        $(element).popover(options).popover("show");
    }
    
    // extract and execute the nested scripts inside popover
    let popover_id = $(element).attr('aria-describedby');
    let scripts = $('#' + popover_id).clone().find('script').map(function() { return $(this).html(); }).get().join(';');
    
    if (scripts.length> 0) {
        new Function(scripts)();
    }
    
    // restaura o title
    setTimeout( function() {
        if (typeof old_title !== 'undefined') {
            $(element).attr('title', old_title);
            $(element).attr('data-original-title', old_title);
        }
    },100);
    
    return $('#' + popover_id);
}

/**
 *
 */
function __adianti_clear_click_popovers()
{
    $('.popover.trigger.click').remove();
}function __adianti_process_tooltips()
{
    $("[title]").each(function(k,v) {
        if ($(v).attr('title').length > 0 ) {
            tippy(v, {
              content: $(v).attr('title'),
              allowHTML: true,
              theme: 'light-border',
              arrow: true
            });
            $(v).attr('data-original-title', $(v).attr('title'));
            $(v).removeAttr('title');
        }
    });
}/**
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
}function __adianti_failure_message()
{
    if (Adianti.debug == 1) {
        if (Adianti.language == 'pt') {
            return 'Requisição falhou. Verifique a conexão com internet e os logs do servidor de aplicação';
        }
        return 'Request failed. Check the internet connection and the application server logs';
    }
    else
    {
        if (Adianti.language == 'pt') {
            return 'Requisição falhou';
        }
        return 'Request failed';
    }
}

/**
 * Show message failure
 */
 function __adianti_failure_request(jqxhr, textStatus, exception) {
    if(! jqxhr.responseText) {
        __adianti_error('Error', textStatus + ': ' + __adianti_failure_message());
    } else {
        $('#adianti_online_content').append(jqxhr.responseText);
    }
}function __adianti_load_container(container, content, url)
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
}/**
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
}/**
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
}function __adianti_run_after_loads(url, result)
{
    if (typeof Adianti.onAfterLoad == "function") {
        Adianti.onAfterLoad(url, result);
    }

    if (typeof Template.onAfterLoad == "function") {
        Template.onAfterLoad(url, result);
    }
    Adianti.loading = false;
}

function __adianti_run_after_posts(url, result)
{
    if (typeof Adianti.onAfterPost == "function") {
        Adianti.onAfterPost(url, result);
    }

    if (typeof Template.onAfterPost == "function") {
        Template.onAfterPost(url, result);
    }
    
    Adianti.loading = false;
}

function __adianti_run_before_loads(url)
{
    if (typeof Adianti.onBeforeLoad == "function") {
        Adianti.onBeforeLoad(url);
    }

    if (typeof Template.onBeforeLoad == "function") {
        Template.onBeforeLoad(url);
    }
    
    Adianti.loading = true;
}

function __adianti_run_before_posts(url)
{
    if (typeof Adianti.onBeforePost == "function") {
        Adianti.onBeforePost(url);
    }

    if (typeof Template.onBeforePost == "function") {
        Template.onBeforePost(url);
    }
    
    Adianti.loading = true;
}/**
 * Post form data
 */
function __adianti_post_data(form, action, run_events, options)
{
    if (typeof run_events == "undefined") {
        run_events = true;
    }
    
    if (typeof options == "undefined") {
        options = {};
    }
    
    if (action.substring(0,4) == 'xhr-')
    {
        url = action;
    }
    else
    {
        if (action.substring(0,5) == 'class') {
            url = 'index.php?'+action;
            url = url.replace('index.php', 'engine.php');
        }
        else {
            var url = 'xhr-' + action; // use routes por post
        }
    }

    if (document.querySelector('#'+form) instanceof Node && (action.indexOf('novalidate') === -1))
    {
        if (!document.querySelector('#'+form).hasAttribute('novalidate') && document.querySelector('#'+form).checkValidity() == false)
        {
            document.querySelector('#'+form).reportValidity();
            return;
        }
    }
    
    if (typeof options.blockui == "undefined" || options.blockui == true) {
        __adianti_block_ui();
    }
    
    if (typeof options.wrapper_variable !== "undefined") {
        var data = {};
        data = __adianti_query_to_json(action);
        data[options.wrapper_variable] = __adianti_query_to_json( $('#'+form).serialize() );
        url = __adianti_filter_url(url, action, ['class', 'method', 'static', 'register_state']);
    }
    else
    {
        var data = $('#'+form).serialize();
    }
    
    if (run_events) {
        __adianti_run_before_posts(url);
    }
    
    if ( (url.indexOf('&static=1') > 0) || (url.indexOf('?static=1') > 0) || (action.substring(0,4) == 'xhr-'))
    {
        $.post(url, data)
        .done(function(result) {
            __adianti_parse_html(result, null, url);
            if (typeof options.blockui == "undefined" || options.blockui == true) {
                __adianti_unblock_ui();
            }
            Adianti.requestURL  = url;
            Adianti.requestData = data;
            
            if (run_events) {
                __adianti_run_after_posts(url, result);
            }
            
        }).fail(function(jqxhr, textStatus, exception) {
            if (typeof options.blockui == "undefined" || options.blockui == true) {
                __adianti_unblock_ui();
            }
            __adianti_failure_request(jqxhr, textStatus, exception);
            Adianti.loading = false;
        });
    }
    else
    {
        $.post(url, data)
        .done(function(result) {
            Adianti.currentURL  = url;
            Adianti.requestURL  = url;
            Adianti.requestData = data;
            
            if (run_events) {
                __adianti_load_html(result, __adianti_run_after_posts, url);
            }
            else {
                __adianti_load_html(result, null, url);
            }
            if (typeof options.blockui == "undefined" || options.blockui == true) {
                __adianti_unblock_ui();
            }

        }).fail(function(jqxhr, textStatus, exception) {
            if (typeof options.blockui == "undefined" || options.blockui == true) {
                __adianti_unblock_ui();
            }
            __adianti_failure_request(jqxhr, textStatus, exception);
            Adianti.loading = false;
        });
    }
}

function __adianti_post_exec(action, data, callback, static_call, automatic_output)
{
    if (action.substring(0,5) == 'class') {
        var uri = 'engine.php?' + action;
    }
    else {
        var uri = 'xhr-' + action;
    }
    
    var automatic_output = (typeof automatic_output === "undefined") ? false : automatic_output;

    if (typeof static_call !== "undefined") {
        var uri = uri + ( (uri.indexOf('?') == -1) ? '?' : '&') + 'static='+static_call;
    }

    $.ajax({
      type: 'POST',
      url: uri,
      data: data,
      }).done(function( result ) {
        if (automatic_output) {
            Adianti.requestURL  = uri;
            try {
                Adianti.requestData = new URLSearchParams(data).toString();
            }
            catch (error) {
                console.log(error);
            }
            __adianti_parse_html(result, callback, uri);
            __adianti_run_after_loads(uri, result);
        }
        else if (callback && typeof(callback) === "function") {
            return callback(result);
        }
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_failure_request(jqxhr, textStatus, exception);
      });
}


function __adianti_post_lookup(form, action, field, callback) {
    if (typeof field == 'string') {
        field_obj = $('#'+field);
    }
    else if (field instanceof HTMLElement) {
        field_obj = $(field);
    }

    var formdata = $('#'+form).serializeArray();
    formdata.push({name: '_field_value', value: field_obj.val()});

    if (action.substring(0,5) == 'class') {
        var uri = 'engine.php?' + action +'&static=1';
    }
    else {
        //var uri = 'xhr-' + action +'&static=1';
        var uri = 'xhr-' + action + ( (action.indexOf('?') == -1) ? '?' : '&') + 'static=1';
    }

    formdata.push({name: 'key',         value: field_obj.val()}); // for BC
    formdata.push({name: 'ajax_lookup', value: 1});
    formdata.push({name: '_field_id',   value: field_obj.attr('id')});
    formdata.push({name: '_field_name', value: field_obj.attr('name')});
    formdata.push({name: '_form_name',  value: form});
    var formdata_scalar = [...formdata];
    formdata.push({name: '_field_data', value: $.param(field_obj.data(), true)});
    formdata.push({name: '_field_data_json', value: JSON.stringify(__adianti_query_to_json($.param(field_obj.data(), true)))});
    
    $.ajax({
      type: 'POST',
      url: uri,
      data: formdata
      }).done(function( result ) {
          Adianti.requestURL  = uri;
          try {
              Adianti.requestData = new URLSearchParams(Object.fromEntries(formdata_scalar.map(({ name, value }) => [name, value]))).toString();
          }
          catch (error) {
              console.log(error);
          }
          __adianti_parse_html(result, callback, uri);
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_failure_request(jqxhr, textStatus, exception);
      });
}

function __adianti_post_page_lookup(form, action, field, callback) {
    if (typeof field == 'string') {
        field_obj = $('#'+field);
    }
    else if (field instanceof HTMLElement) {
        field_obj = $(field);
    }

    var formdata = $('#'+form).serializeArray();
    formdata.push({name: '_field_value', value: field_obj.val()});

    if (action.substring(0,5) == 'class') {
        var uri = 'engine.php?' + action;
    }
    else {
        var uri = 'xhr-' + action;
    }

    formdata.push({name: '_field_id',   value: field_obj.attr('id')});
    formdata.push({name: '_field_name', value: field_obj.attr('name')});
    formdata.push({name: '_form_name',  value: form});
    formdata.push({name: '_field_data', value: $.param(field_obj.data(), true)});
    formdata.push({name: '_field_data_json', value: JSON.stringify(__adianti_query_to_json($.param(field_obj.data(), true)))});
    formdata.push({name: 'key',         value: field_obj.val()}); // for BC
    formdata.push({name: 'ajax_lookup', value: 1});

    $.ajax({
      type: 'POST',
      url: uri,
      data: formdata
      }).done(function( result ) {
          __adianti_load_html(result, callback, uri);
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_failure_request(jqxhr, textStatus, exception);
      });
}

function __adianti_filter_url(url, action, keep_variables)
{
    var query_object = __adianti_query_to_json(action);
    var keep_list = {};
    
    for (var variable of keep_variables) {
        if (typeof query_object[variable] !== 'undefined') {
            keep_list[variable] = query_object[variable];
        }
    }
    
    if (action.substring(0,4) !== 'xhr-') {
        url = url.replace(action, '');
        url = url + $.param(keep_list);
    }
    
    return url;
}/**
 * Start blockUI dialog
 */
function __adianti_block_ui(wait_message)
{
    if (typeof $.blockUI == 'function')
    {
        if (typeof Adianti.blockUIConter == 'undefined')
        {
            Adianti.blockUIConter = 0;
        }
        Adianti.blockUIConter = Adianti.blockUIConter + 1;
        if (typeof wait_message == 'undefined')
        {
            wait_message = Adianti.waitMessage;
        }

        $.blockUI({
           message: '<h1 style="font-size:25pt"><i class="fa fa-spinner fa-pulse"></i> '+wait_message+'</h1>',
           fadeIn: 0,
           fadeOut: 0,
           css: {
               border: 'none',
               top: '100px',
               left: 0,
               maxWidth: '300px',
               width: 'inherit',
               padding: '15px',
               backgroundColor: 'none',
               'border-radius': '5px 5px 5px 5px',
               opacity: 1,
               color: '#fff'
           }
        });

        $('.blockUI.blockMsg').mycenter();
    }
}

/**
 * Closes blockUI dialog
 */
function __adianti_unblock_ui(force)
{
    if (typeof $.blockUI == 'function') {
        if (typeof force == 'undefined') {
            Adianti.blockUIConter = Adianti.blockUIConter -1;
            if (Adianti.blockUIConter <= 0) {
                $.unblockUI( { fadeIn: 0, fadeOut: 0 } );
                Adianti.blockUIConter = 0;
            }
        }
        else if (force == true) {
            $.unblockUI( { fadeIn: 0, fadeOut: 0 } );
            Adianti.blockUIConter = 0;
        }
    }
}function __adianti_process_datatables()
{
    if (typeof $().DataTable == 'function') {
        var dt_options = {
            responsive: true,
            paging: false,
            searching: false,
            ordering:  false,
            info: false
        };

        if (typeof Adianti.language !== 'undefined')
        {
            dt_options['language'] = {};
            dt_options['language']['url'] = 'lib/jquery/i18n/datatables/'+Adianti.language+'.json';
        }

        $('table[datatable="true"]:not(.dataTable)').DataTable( dt_options );
    }
}/**
 * Goto a given page
 */
function __adianti_goto_page(page)
{
    window.location = page;
}

/**
 * Open page in new tab
 */
function __adianti_open_page(page)
{
    var win = window.open(page, '_blank');
    if (win)
    {
        win.focus();
    }
    else
    {
        alert('Please allow popups for this website');
    }
}/**
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
}/**
 * Download a file
 */
function __adianti_download_file(file, basename)
{
    extension = file.split('.').pop();
    screenWidth  = screen.width;
    screenHeight = screen.height;
    if (extension !== 'html')
    {
        screenWidth /= 3;
        screenHeight /= 3;
    }

    if (typeof basename == 'undefined') {
        basename = '';
    }

    window.open('download.php?file='+file+'&basename='+basename, '_blank',
      'width='+screenWidth+
     ',height='+screenHeight+
     ',top=0,left=0,status=yes,scrollbars=yes,toolbar=yes,resizable=yes,maximized=yes,menubar=yes,location=yes');
}/**
 * Returns the URL Base
 */
function __adianti_base_url()
{
   return window.location.protocol +'//'+ window.location.host + window.location.pathname.split( '/' ).slice(0,-1).join('/');
}

/**
 * Register URL state
 */
function __adianti_register_state(url, origin)
{
    if (Adianti.registerState !== false || origin == 'user')
    {
        var stateObj = { url: url };
        if (typeof history.pushState != 'undefined') {
            history.pushState(stateObj, "", url.replace('engine.php', 'index.php').replace('xhr-', '').replace('&page_fragment=', '&_pf=').replace('&target_container=', '&_tc='));
        }
    }
}

/**
 * Returns the query string
 */
function __adianti_query_string(query_source)
{
    var query_string = {};
    var query = query_source || window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0; i<vars.length; i++)
    {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined")
        {
            query_string[pair[0]] = pair[1];
            // If second entry with this name
        }
        else if (typeof query_string[pair[0]] === "string")
        {
            var arr = [ query_string[pair[0]], pair[1] ];
            query_string[pair[0]] = arr;
        }
        else
        {
            query_string[pair[0]].push(pair[1]);
        }
    }
    return query_string;
}

/**
 * Converts query string into json object
 */
function __adianti_query_to_json(query)
{
    var pieces = query.split('&');
    var params = Object();
    var decode = function (s) {
        if (typeof s !== "undefined"){
            return urldecode(s.replace(/\+/g, " "));
        }
        return s;
    };

    for (var i=0; i < pieces.length ; i++) {
        var part = pieces[i].split('=');
        if(part[0].search("\\[\\]") !== -1) {
            part[0]=part[0].replace(/\[\]$/,'');
            if( typeof params[part[0]] === 'undefined' ) {
                params[part[0]] = [decode(part[1])];

            } else {
                params[part[0]].push(decode(part[1]));
            }


        } else {
            params[part[0]] = decode(part[1]);
        }
    }

    return params;
}

function __adianti_base64_to_blob(b64Data, contentType='', sliceSize=512)
{
  const byteCharacters = atob(b64Data);
  const byteArrays = [];

  for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    const slice = byteCharacters.slice(offset, offset + sliceSize);

    const byteNumbers = new Array(slice.length);
    for (let i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }

    const byteArray = new Uint8Array(byteNumbers);
    byteArrays.push(byteArray);
  }

  const blob = new Blob(byteArrays, {type: contentType});
  return blob;
}

function __adianti_download_blob(blob, name = 'file.txt') {
  // Convert your blob into a Blob URL (a special url that points to an object in the browser's memory)
  const blobUrl = URL.createObjectURL(blob);

  // Create a link element
  const link = document.createElement("a");

  // Set link's href to point to the Blob URL
  link.href = blobUrl;
  link.download = name;

  // Append link to the body
  document.body.appendChild(link);

  // Dispatch click event on the link
  // This is necessary as link.click() does not work on the latest firefox
  link.dispatchEvent(
    new MouseEvent('click', { 
      bubbles: true, 
      cancelable: true, 
      view: window 
    })
  );

  // Remove link from body
  document.body.removeChild(link);
}
/**
 * Debounce actions
 */
function __adianti_debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

function __adianti_copy_to_clipboard64(content64)
{
    __adianti_copy_to_clipboard(base64_decode(content64));
}

function __adianti_copy_to_clipboard(content)
{
    var clipboard = navigator.clipboard;
    if (clipboard == undefined) {
        __adianti_show_toast64('error', base64_encode('Clipboard is unavailable'), 'bottomCenter', '');
    } else {
        clipboard.writeText(content).then(function() {
            __adianti_show_toast64('success', base64_encode('Copied to clipboard successfully!'), 'bottomCenter', '');
        }, function() {
            __adianti_show_toast64('error', base64_encode('Unable to write to clipboard'), 'bottomCenter', '');
        });
    }
}

function __adianti_toggle_fullscreen()
{
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else if (document.exitFullscreen) {
        document.exitFullscreen();
    }
}

function __adianti_string_get_between(context_string, needle_start, needle_end, only_content = true)
{
    var pos_ini = context_string.indexOf(needle_start);
    if (pos_ini < 0)
    {
        return '';
    }
    
    var start = pos_ini + (only_content ? needle_start.length : 0);
    
    var pos_fim = context_string.indexOf(needle_end);//, $start);
    if (pos_fim < 0)
    {
        return '';
    }
    
    var end = (only_content ? pos_fim : pos_fim + needle_end.length);
    
    return context_string.substring(start, end);
}

$.fn.mycenter = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.outerHeight() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.outerWidth() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

