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
}