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
}