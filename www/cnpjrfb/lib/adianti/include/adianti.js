function Adianti(){}
function Template(){}

/**
 * Define the application name
 */
function __adianti_set_name(application)
{
    Adianti.applicationName = application;
}

/**
 * Initialize tab/mdi environment
 */
function __adianti_init_tabs(useTabs, storeTabs, mdiWindows)
{
    Adianti.tabs = {};
    Adianti.firstOpenTab = true;
    Adianti.useTabs = useTabs;
    Adianti.storeTabs = storeTabs;
    Adianti.mdiWindows = mdiWindows;
    Adianti.currentTab = null;
    
    if (Adianti.mdiWindows) {
        setTimeout(function() {
            $('#adianti_content .adianti_tabs_container').addClass('mdi-windows');
        }, 0);
    }
    
    // restore previously opened tabs/mdi's (from local storage to in memory)
    if (Adianti.useTabs && Adianti.storeTabs)
    {
        var pages   = JSON.parse(localStorage.getItem('__adianti_tabs_' + Adianti.applicationName));
        var current = localStorage.getItem('__adianti_current_tab_' + Adianti.applicationName);
        
        if (pages)
        {
            setTimeout( function() {
                $('#adianti_tab_content .adianti-tab').remove();
                
                if (current) {
                    Adianti.currentTab = current;
                }
                
                for (var page of pages) {
                    // load to in memory storage
                    Adianti.tabs[page.name] = { content: null, id: page.uniqid, name: page.name };
                    
                    // create DOM item
                    __adianti_create_tab_item(page.uniqid, page.page, page.name, (page.name == current))
                }

            }, 0);
            
            setTimeout(__adianti_scroll_to_active_tab, 250)
        }
    }
    else
    {
        localStorage.removeItem('__adianti_tabs_' + Adianti.applicationName)
        localStorage.removeItem('__adianti_current_tab_' + Adianti.applicationName)
    }
}

/**
 * Initialize tabs/mdi environment variables
 */
function __adianti_clear_tabs()
{
    Adianti.currentTab = null;
    Adianti.tabs = {};
    localStorage.removeItem('__adianti_tabs_' + Adianti.applicationName)
    localStorage.removeItem('__adianti_current_tab_' + Adianti.applicationName)
}

/**
 * Scroll tab bar using left and right arrows
 */
function __adianti_scroll_tab(direction)
{
    var offset = 150;
    
    if(direction == 'left') {
        offset *= -1;
    }

    var left = $('#adianti_tab_content').scrollLeft()
    $('#adianti_tab_content').animate({ scrollLeft: (left + offset) }, 250, 'linear')
}

/**
 * Scroll to active tab
 */
function __adianti_scroll_to_active_tab()
{
    if ($('.adianti-tab.active').length > 0) {
        $('#adianti_tab_content').scrollTo($('.adianti-tab.active'))
    }
}

/**
 * Load an tab/mdi content
 */
function __adianti_load_tab(page, content)
{
    var param    = __adianti_query_to_json(page);
    var tab_name = param.adianti_tab_name?? '*';

    var url_container = page.match('target_container=([0-z-]*)');
    var dom_container = content.match('adianti_target_container\\s?=\\s?"([0-z-]*)"');
    
    if (!Adianti.useTabs || url_container || dom_container)
    {
        return;
    }

    if (Adianti.currentTab == null)
    {
        __adianti_set_current_tab(tab_name);
    }

    if ((page.indexOf('adianti_open_tab=1') >= 0 && page.indexOf('adianti_reload_tab=1') == -1) || Adianti.mdiWindows)
    {
        __adianti_add_tab(page, tab_name);
    }

    if (Adianti.storeTabs)
    {
        page = page.replace('&adianti_reload_tab=1', '');

        if (typeof Adianti.tabs[tab_name] === 'undefined' && Adianti.useTabs && Adianti.firstOpenTab)
        {
            __adianti_set_current_tab(tab_name);
            __adianti_add_tab(page, tab_name);
        }
    }

    Adianti.firstOpenTab = false;
}

/**
 * Create a new tab item in the tab nav bar
 */
function __adianti_create_tab_item(uniqid, page, name, active)
{
    active = active ? 'active' : '';
    $('#adianti_tab_content').append("<div onclick='__adianti_open_tab(\""+page+"\",\""+name+"\")' id='"+uniqid+"' class='adianti-tab "+active+"'><span class='adianti-tab-name'>"+name+"</span> <i onclick='__adianti_close_tab(\""+name+"\", event); return false;' class='fas fa-times adianti-close-tab'></i> </div>");
}

/**
 * Set the current opened tab
 */
function __adianti_set_current_tab(tab_name) {
    Adianti.currentTab = tab_name;

    if (Adianti.storeTabs) {
        localStorage.setItem('__adianti_current_tab_' + Adianti.applicationName, tab_name);
    }
}

/**
 * Store a tab content in memory or local storage (according to configuration)
 */
function __adianti_store_tab_content(page, uniqid, content, name) 
{
    Adianti.tabs[name] = {
        content: content,
        id: uniqid,
        name: name
    };

    if (Adianti.storeTabs) {
        var pages = JSON.parse(localStorage.getItem('__adianti_tabs_' + Adianti.applicationName));
        if ( ! pages) {
            pages = [];
        }

        var hasPage = pages.filter((e) => e.name == name).length > 0;

        if (! hasPage ) {
            pages.push({page, name, uniqid});
            localStorage.setItem('__adianti_tabs_' + Adianti.applicationName, JSON.stringify(pages));
        }
    }
}

function __adianti_open_tab(page, name)
{
    if (Adianti.mdiWindows)
    {
        if (Adianti.currentTab != name) {
            $('#adianti_tab_content').find('.adianti-tab').removeClass('active');
            $('#'+Adianti.tabs[name].id).addClass('active');
            
            if (! Adianti.tabs[name].content && ! Adianti.firstOpenTab)
            {
                __adianti_load_page(page+'&adianti_reload_tab=1');
            }
            else if (Adianti.tabs[name].content && ! Adianti.tabs[name].content.is(':visible'))
            {
                __adianti_show_iframe(name);
            }
            __adianti_register_state(page);
        }
        
        __adianti_set_active_iframe(name);
    }
    else if (Adianti.currentTab != name)
    {
        if (typeof Adianti.tabs[Adianti.currentTab] != 'undefined')
        {
            Adianti.tabs[Adianti.currentTab].content = $('#adianti_div_content').children().detach();
            //$('#adianti_content').append("<div id='adianti_div_content'></div>");
        }
        
        $('#adianti_tab_content').find('.adianti-tab').removeClass('active');
        $('#'+Adianti.tabs[name].id).addClass('active');

        if (! Adianti.tabs[name].content)
        {
            __adianti_load_page(page+'&adianti_reload_tab=1');
        }
        else
        {
            //Adianti.tabs[name].content.appendTo();
            $('#adianti_div_content').html(Adianti.tabs[name].content);
            __adianti_register_state(page);
        }
    }

    __adianti_set_current_tab(name);
}

function __adianti_close_tab(name, event)
{
    if(typeof event != 'undefined')
    {
        event.preventDefault();
        event.stopPropagation();
    }

    $('#'+Adianti.tabs[name].id).remove();

    if(Adianti.currentTab == name)
    {
        $('#adianti_div_content').empty();
        let tabs = $('#adianti_tab_content').find('.adianti-tab-name');
        if(tabs.length > 0)
        {
            $(tabs[0]).click();
        }
    }

    if (Adianti.storeTabs) {
        var pages = JSON.parse(localStorage.getItem('__adianti_tabs_' + Adianti.applicationName));
        pages = pages.filter( (e) => e.name != name);
        localStorage.setItem('__adianti_tabs_' + Adianti.applicationName, JSON.stringify(pages));
    }

    delete Adianti.tabs[name];

    if (Adianti.mdiWindows) {
        __adianti_close_iframe(name);
    }
}

function __adianti_add_tab(page, name)
{
    if (typeof Adianti.tabs[name] == 'undefined')
    {
        let uniqid = parseInt(Math.random() * 100000000);

        $('#adianti_tab_content').find('.adianti-tab').removeClass('active');

        __adianti_create_tab_item(uniqid, page, name, true);

        __adianti_store_tab_content(page, uniqid, null, name);

        if (typeof Adianti.tabs[Adianti.currentTab] !== 'undefined')
        {
            if (Adianti.mdiWindows)
            {
                Adianti.tabs[Adianti.currentTab].content = __adianti_get_iframe(Adianti.currentTab);
            }
            else
            {
                // guarda conteúdo atual, antes de carregar o próximo.
                Adianti.tabs[Adianti.currentTab].content = $('#adianti_div_content').children().detach();
            }
        }
        
        if (Adianti.mdiWindows) {
            Adianti.tabs[name].content = __adianti_get_iframe(name);
        }

        //$('#adianti_content').append("<div id='adianti_div_content'></div>");
    }
    else if (Adianti.currentTab != name)
    {
        __adianti_open_tab(page, name);
    }

    setTimeout(__adianti_scroll_to_active_tab, 0);

    __adianti_set_current_tab(name)
}

function __adianti_set_language(lang)
{
    Adianti.language = lang;
}

function __adianti_set_debug(debug)
{
    Adianti.debug = debug;
}

function __adianti_run_after_loads(url, result)
{
    if (typeof Adianti.onAfterLoad == "function") {
        Adianti.onAfterLoad(url, result);
    }

    if (typeof Template.onAfterLoad == "function") {
        Template.onAfterLoad(url, result);
    }
}

function __adianti_run_after_posts(url, result)
{
    if (typeof Adianti.onAfterPost == "function") {
        Adianti.onAfterPost(url, result);
    }

    if (typeof Template.onAfterPost == "function") {
        Template.onAfterPost(url, result);
    }
}

function __adianti_run_before_loads(url)
{
    if (typeof Adianti.onBeforeLoad == "function") {
        Adianti.onBeforeLoad(url);
    }

    if (typeof Template.onBeforeLoad == "function") {
        Template.onBeforeLoad(url);
    }
}

function __adianti_run_before_posts(url)
{
    if (typeof Adianti.onBeforePost == "function") {
        Adianti.onBeforePost(url);
    }

    if (typeof Template.onBeforePost == "function") {
        Template.onBeforePost(url);
    }
}

function __adianti_failure_message()
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
}

/**
 * Goto a given page
 */
function __adianti_goto_page(page)
{
    window.location = page;
}

/**
 * Returns the URL Base
 */
function __adianti_base_url()
{
   return window.location.protocol +'//'+ window.location.host + window.location.pathname.split( '/' ).slice(0,-1).join('/');
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

/**
 * Returns the query string
 */
function __adianti_query_string()
{
    var query_string = {};
    var query = window.location.search.substring(1);
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

/**
 * Loads an HTML content
 */
function __adianti_load_html(content, afterCallback, url)
{
    var url_container = url.match('target_container=([0-z-]*)');
    var dom_container = content.match('adianti_target_container\\s?=\\s?"([0-z-]*)"');

    if ( dom_container !== null)
    {
        var target_container = dom_container[1];
        __adianti_load_side_content(target_container, content)
    }
    else if (url_container !== null)
    {
        var target_container = url_container[1];
        __adianti_load_side_content(target_container, content)
    }
    else if (content.indexOf('widget="TWindow"') > 0)
    {
        __adianti_load_window_content(content);
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

function __adianti_load_window_content(content)
{
    var win_container = content.match('window_name\\s?=\\s?"([0-z-]*)"');
    var window_name = win_container[1];

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

function __adianti_load_side_content(container, content)
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
        $('#'+container).empty();
        $('#'+container).html(content);
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

function __adianti_get_iframe(name, dialog) {
    var iframe = $("#iframe_wrapper"+btoa(name).replaceAll('=',''));
    if (typeof dialog === 'undefined') {
        return iframe.closest('.ui-dialog');
    }

    return iframe;
}

function __adianti_close_iframe(name, event) {
    if(typeof event != 'undefined')
    {
        event.preventDefault();
        event.stopPropagation();
    }

    __adianti_get_iframe(name, true).remove();
    __adianti_get_iframe(name).remove();
}

function __adianti_adjust_iframe(name) {
    var topIframe = null;
    var topZIndex = null;

    for ( var nameTab of Object.keys(Adianti.tabs) ) {
        if (name == nameTab)  {
            continue;
        }

        var tab = Adianti.tabs[nameTab];
        if (! tab.content || ! tab.content.is(':visible')) {
            continue;
        }

        var zIndex = tab.content.css('z-index');

        if (! topZIndex || zIndex >= topZIndex ) {
            topZIndex = zIndex;
            topIframe = nameTab;
        }
    }

    $('#adianti_tab_content').find('.adianti-tab').removeClass('active');

    if (topIframe) {
        __adianti_set_current_tab(topIframe);
        $('#'+Adianti.tabs[topIframe].id).addClass('active');
    } else {
        __adianti_set_current_tab(null);
    }
}

function __adianti_show_iframe(name) {
    var iframe =  __adianti_get_iframe(name);
    var iframeDialog =  __adianti_get_iframe(name, true);

    var iframeWidth = iframeDialog.dialog( "option", 'width');
    var iframeHeight = iframeDialog.dialog( "option", 'height');
    var iframeLeft = iframe.css('left');
    var iframeTop = iframe.css('top');

    iframe.css({
        height: 0,
        width: '20px',
        top: $(window).height() - 50,
        left: '50%',
        display: 'block'
    });

    iframe.animate({
        height: iframeHeight,
        width: iframeWidth,
        top: iframeTop,
        left: iframeLeft
    }, 300, function(){});
}

function __adianti_minimize_iframe(name, event) {
    if(typeof event != 'undefined') {
        event.preventDefault();
        event.stopPropagation();
    }

    if(Adianti.currentTab == name) {
        __adianti_adjust_iframe(name)
    }

    var iframe =  __adianti_get_iframe(name);
    var iframeDialog =  __adianti_get_iframe(name, true);
    Adianti.tabs[name].content = iframe;

    var iframeWidth = iframeDialog.dialog( "option", 'width');
    var iframeHeight = iframeDialog.dialog( "option", 'height');
    var iframeLeft = iframeDialog.dialog( "option", 'left');
    var iframeTop = iframeDialog.dialog( "option", 'top');

    iframe.animate({
        height: 0,
        width: '20px',
        top: $(window).height() - 50,
        left: '50%'
    }, 300, function(){
        iframe.hide();
        iframeDialog.dialog( "option", 'width', iframeWidth);
        iframeDialog.dialog( "option", 'height', iframeHeight);
        iframeDialog.dialog( "option", 'top', iframeTop);
        iframeDialog.dialog( "option", 'left', iframeLeft);
    });
}

function __adianti_restore_iframe(name, event) {
    if(typeof event != 'undefined') {
        event.preventDefault();
        event.stopPropagation();
    }

    var iframe =  __adianti_get_iframe(name, true);

    var sizes = Adianti.tabs[name].sizes;

    var width =  sizes.width ?? $(window).width() * 0.75;
    var height =  sizes.height ?? $(window).height() * 0.75;

    iframe.dialog('option', 'height', height);
    iframe.dialog('option', 'width', width);
    iframe.parent().css({ top: (sizes.top ?? '15%'), left: (sizes.left ?? '15%') });

    Adianti.tabs[name].sizes = null;

    iframe.closest('.ui-dialog').find('button[name=maximize]').show();
    iframe.closest('.ui-dialog').find('button[name=restore]').hide();
}

function __adianti_maximize_iframe(name, event) {
    if(typeof event != 'undefined')
    {
        event.preventDefault();
        event.stopPropagation();
    }

    var iframe = __adianti_get_iframe(name, true);
    var windowIframe = __adianti_get_iframe(name);

    Adianti.tabs[name].sizes = {
        width : iframe.dialog( "option", 'width'),
        height : iframe.dialog( "option", 'height'),
        left : windowIframe.offset().left,
        top : windowIframe.offset().top,
    };

    var width =  $(window).width() - 1;
    var height =  $(window).height() - 1;

    iframe.dialog('option', 'height', height);
    iframe.dialog('option', 'width', width);
    iframe.parent().css({top: '0', left: '0'});

    iframe.closest('.ui-dialog').find('button[name=maximize]').hide();
    iframe.closest('.ui-dialog').find('button[name=restore]').show();
}

/**
 * Move iframe to TOP level
 */
function __adianti_set_active_iframe(name)
{
    var iframe =  __adianti_get_iframe(name, true);
    iframe.dialog( "moveToTop" );

    if (name == Adianti.currentTab) {
        return;
    }

    __adianti_set_current_tab(name);

    $('#adianti_tab_content').find('.adianti-tab').removeClass('active');
    $('#'+Adianti.tabs[name].id).addClass('active');
}

function __adianti_goto_iframe(url) {
    var param = __adianti_query_to_json(url);
    var name = param.adianti_tab_name?? '*';
    var url = url + '&template=iframe';

    if (typeof Adianti.tabs[name] !== 'undefined' && Adianti.tabs[name].content) {
        if (name === '*') {
            var iframe = __adianti_get_iframe(name);
            var urlActive = iframe.find('#iframe_container').attr('src');

            if (urlActive != url) {
                iframe.find('#iframe_container').attr('src', url);
            } else {
                __adianti_show_iframe(name);
            }
        }

        return;
    }

    var iframe = $('<iframe id="iframe_container" src="'+url+'" frameborder="0"  width="100%" height="98%" allowfullscreen></iframe>');

    window_width =  $(window).width() * 0.75;
    window_height =  $(window).height() * 0.75;

    var id = "iframe_wrapper"+btoa(name).replaceAll('=','');

    $("<div></div>").append(iframe).appendTo("body").dialog({
        autoOpen: true,
        modal: false,
        draggable: true,
        resizable: true,
        width: window_width,
        height: window_height,
        position: { my: 'center center', at: 'center'},
        title: name,
        zIndex: 200000,
    }).attr('id', id);

    $(`#${id}`).parent().addClass('mdi_window');
    $(`#${id}`).parent().find('.ui-dialog-titlebar').find('button').remove();

    $(`#${id}`).parent().find('.ui-dialog-titlebar').append(
        $(`<div class="d-flex" style="gap: 5px">
            <button name="minimize" class="btn btn-default" type="button"><i class="fas fa-window-minimize"></i></button>
            <button name="maximize" class="btn btn-default" type="button"><i class="far fa-window-maximize"></i></button>
            <button name="restore"  class="btn btn-default" type="button" style="display: none"><i class="far fa-window-restore"></i></button>
            <button name="close"    class="btn btn-danger"  type="button"><i class="fas fa-times"></i></button>
        </div>`)
    )

    $(`#${id}`).parent().find('.ui-dialog-titlebar').parent().on(
        'click', function(){ __adianti_set_active_iframe(name);}
    );

    $(`#${id}`).parent().find('.ui-dialog-titlebar').find('button[name=minimize]').on(
        'click', function(evt){ __adianti_minimize_iframe(name, evt);}
    );

    $(`#${id}`).parent().find('.ui-dialog-titlebar').find('button[name=maximize]').on(
        'click', function(evt){ __adianti_maximize_iframe(name, evt);}
    );

    $(`#${id}`).parent().find('.ui-dialog-titlebar').find('button[name=restore]').on(
        'click', function(evt){ __adianti_restore_iframe(name, evt);}
    );

    $(`#${id}`).parent().find('.ui-dialog-titlebar').find('button[name=close]').on(
        'click', function(){ __adianti_close_tab(name);}
    );

    if (Adianti.tabs[name]) {
        Adianti.tabs[name].content = __adianti_get_iframe(name)
    }
}

/**
 * Check if the URL can be opened using Iframe
 */
function __adianti_can_open_iframe(url)
{
    if (Adianti.mdiWindows) {
        // Case logout system | reload permission
        if ( url.indexOf('LoginForm&method=onLogout') != -1 || url.indexOf('LoginForm&method=reloadPermissions') != -1) {
            return false;
        }

        return true;
    }
    return false;
}


/**
 * Open a page using ajax
 */
function __adianti_load_page(page, callback)
{
    if (typeof page !== 'undefined')
    {
        $( '.modal-backdrop' ).remove();
        var url = page;
        url = url.replace('index.php', 'engine.php');

        if (url.indexOf('engine.php') == -1)
        {
            url = 'xhr-'+url;
        }

        __adianti_run_before_loads(url);

        if ( __adianti_can_open_iframe(url) )
        {
            var iframe_page = url.replace('engine.php', 'index.php');
            
            Adianti.requestURL  = iframe_page;
            Adianti.requestData = null;
        
            __adianti_goto_iframe(iframe_page);
            __adianti_load_tab(iframe_page, '');
            __adianti_run_after_loads(iframe_page, '');
        }
        else
        {
            if ( (url.indexOf('&static=1') > 0) || (url.indexOf('?static=1') > 0) )
            {
                $.get(url)
                .done(function(result) {
                    Adianti.requestURL  = url;
                    Adianti.requestData = null;
                    
                    __adianti_load_tab(page, result);
                    __adianti_parse_html(result);
                    
                    if (typeof callback == "function")
                    {
                        callback();
                    }
    
                    __adianti_run_after_loads(url, result);
    
                }).fail(function(jqxhr, textStatus, exception) {
                   __adianti_failure_request(jqxhr, textStatus, exception);
                   loading = false;
                });
            }
            else
            {
                $.get(url)
                .done(function(result) {
                    Adianti.requestURL  = url;
                    Adianti.requestData = null;
                    
                    __adianti_load_tab(page, result);
                    __adianti_load_html(result, __adianti_run_after_loads, url);
                    
                    if (typeof callback == "function")
                    {
                        callback();
                    }
                    
                    if ( url.indexOf('register_state=false') < 0 && history.pushState && (result.indexOf('widget="TWindow"') < 0) )
                    {
                        if (! Adianti.useTabs || ! result.match('adianti_target_container\\s?=\\s?"([0-z-]*)"')) {
                            //aquip
                            __adianti_register_state(url, 'adianti');
                            Adianti.currentURL = url;
                        }
                    }
                }).fail(function(jqxhr, textStatus, exception) {
                   __adianti_failure_request(jqxhr, textStatus, exception);
                   loading = false;
                });
            }
        }
    }
}

/**
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
           message: '<h1><i class="fa fa-spinner fa-pulse"></i> '+wait_message+'</h1>',
           fadeIn: 0,
           fadeOut: 0,
           css: {
               border: 'none',
               top: '100px',
               left: 0,
               maxWidth: '300px',
               width: 'inherit',
               padding: '15px',
               backgroundColor: '#000',
               'border-radius': '5px 5px 5px 5px',
               opacity: .5,
               color: '#fff'
           }
        });

        $('.blockUI.blockMsg').mycenter();
    }
}

/**
 * Open a window
 */
function __adianti_window(title, width, height, content)
{
    $('<div />').html(content).dialog({
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

/**
 * Show standard dialog
 */
function __adianti_dialog( options )
{
    if (options.type == 'info') {
        var icon = (options.icon ? options.icon : 'fa fa-info-circle fa-4x blue');
    }
    else if (options.type == 'warning') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-triangle fa-4x orange');
    }
    else if (options.type == 'error') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-circle fa-4x red');
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
              className: "btn-default",
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
                    '<span class="fa fa-question-circle fa-4x blue" style="float:left"></span>'+
                    '<span style="margin-left:70px;display:block;max-height:500px">'+message+'</span>'+
                    '</div>',
          buttons: {
            yes: {
              label: label_yes,
              className: "btn-default",
              callback: function() {
                if (typeof callback_yes != 'undefined') {
                    callback_yes();
                }
              }
            },
            no: {
              label: label_no,
              className: "btn-default",
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
}

function __adianti_show_toast64(type, message64, place, icon)
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
}

/**
 * Post form data
 */
function __adianti_post_data(form, action)
{
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

    if (document.querySelector('#'+form) instanceof Node)
    {
        if (!document.querySelector('#'+form).hasAttribute('novalidate') && document.querySelector('#'+form).checkValidity() == false)
        {
            document.querySelector('#'+form).reportValidity();
            return;
        }
    }

    __adianti_block_ui();

    data = $('#'+form).serialize();

    __adianti_run_before_posts(url);

    if ( (url.indexOf('&static=1') > 0) || (url.indexOf('?static=1') > 0) || (action.substring(0,4) == 'xhr-'))
    {
        $.post(url, data)
        .done(function(result) {
            __adianti_parse_html(result);
            __adianti_unblock_ui();

            Adianti.requestURL  = url;
            Adianti.requestData = data;
            
            __adianti_run_after_posts(url, result);
            
        }).fail(function(jqxhr, textStatus, exception) {
            __adianti_unblock_ui();
            __adianti_failure_request(jqxhr, textStatus, exception);
            loading = false;
        });
    }
    else
    {
        $.post(url, data)
        .done(function(result) {
            Adianti.currentURL  = url;
            Adianti.requestURL  = url;
            Adianti.requestData = data;

            __adianti_load_html(result, __adianti_run_after_posts, url);
            __adianti_unblock_ui();

        }).fail(function(jqxhr, textStatus, exception) {
            __adianti_unblock_ui();
            __adianti_failure_request(jqxhr, textStatus, exception);
            loading = false;
        });
    }
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
            history.pushState(stateObj, "", url.replace('engine.php', 'index.php').replace('xhr-', ''));
        }
    }
}

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
    var uri = 'engine.php?' + action +'&static=1';
    var automatic_output = (typeof automatic_output === "undefined") ? true : automatic_output;

    $.ajax({url: uri})
    .done(function( result ) {
        if (automatic_output) {
            __adianti_parse_html(result, callback);
        }
        else {
            callback(result);
        }
    }).fail(function(jqxhr, textStatus, exception) {
       __adianti_failure_request(jqxhr, textStatus, exception);
    });
}

function __adianti_post_exec(action, data, callback, static_call, automatic_output)
{
    var uri = 'engine.php?' + action;
    var automatic_output = (typeof automatic_output === "undefined") ? false : automatic_output;

    if (typeof static_call !== "undefined") {
        var uri = 'engine.php?' + action +'&static='+static_call;
    }

    $.ajax({
      type: 'POST',
      url: uri,
      data: data,
      }).done(function( result ) {
        if (automatic_output) {
            __adianti_parse_html(result, callback);
            __adianti_run_after_loads(uri, result);
        }
        else if (callback && typeof(callback) === "function") {
            return callback(result);
        }
      }).fail(function(jqxhr, textStatus, exception) {
         __adianti_failure_request(jqxhr, textStatus, exception);
      });
}

/**
 * Get remote content
 */
function __adianti_get_page(action, callback, postdata)
{
    var uri = 'engine.php?' + action +'&static=1';

    if (typeof postdata !== "undefined") {
        if (typeof postdata.static !== "undefined") {
            var uri = 'engine.php?' + action +'&static='+postdata.static;
        }
    }

    $.ajax({
      url: uri,
      data: postdata
      }).done(function( result ) {
          return callback(result);
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
        var uri = 'xhr-' + action +'&static=1';
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
          __adianti_parse_html(result, callback);
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

/**
 * Parse returning HTML
 */
function __adianti_parse_html(content, callback)
{
    tmp = content;
    tmp = new String(tmp.replace(/window\.opener\./g, ''));
    tmp = new String(tmp.replace(/window\.close\(\)\;/g, ''));
    tmp = new String(tmp.replace(/^\s+|\s+$/g,""));

    try {
        // permite código estático também escolher o target
        var dom_container = content.match('adianti_target_container\\s?=\\s?"([0-z]*)"');
        
        if ( dom_container !== null)
        {
            var target_container = dom_container[1];
            $('#'+target_container).empty();
            $('#'+target_container).html(tmp);
        }
        else
        {
            // target default
            $('#adianti_online_content').find('script').remove();
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

/**
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

    var get_content = function (tip, element) {
        if (typeof $(this).attr('popaction') === "undefined") {
            if (typeof $(this).attr("popcontent64") !== "undefined") {
                return base64_decode($(this).attr("popcontent64"));
            }
            else {
                return $(this).attr("popcontent") || '';
            }
        }
        else {
            var inst = $(this);
            __adianti_get_page($(this).attr('popaction'), function(result) {
                var popover = inst.attr('data-content',result).data('bs.popover');
                popover.setContent();
                popover.show();
            }, {'static': '0'});
            return '<i class="fa fa-spinner fa-spin fa-5x fa-fw"></i>';
        }
    };

    var get_title = function () {
        return $(this).attr("poptitle") || '';
    };

    var pop_template = '<div class="popover" role="tooltip" style="max-width:800px"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>';

    $('[popover="true"]:not([poptrigger]):not([processed="true"])').popover({
        placement: get_placement,
        trigger: 'hover',
        container: 'body',
        template: pop_template,
        delay: { show: 10, hide: 10 },
        content: get_content,
        html: true,
        title: get_title,
        sanitizeFn : function(d) { return d },
    }).attr('processed', true);

    $('[popover="true"][poptrigger="click"]:not([processed="true"])').popover({
        placement: get_placement,
        trigger: 'click',
        container: 'body',
        template: pop_template,
        delay: { show: 10, hide: 10 },
        content: get_content,
        sanitizeFn : function(d) { return d },
        html: true,
        title: get_title
    }).on('shown.bs.popover', function (e) {
        if (typeof $(this).attr('popaction') !== "undefined") {
            var inst = $(this);
            __adianti_get_page($(this).attr('popaction'), function(result) {
                var popover = inst.attr('data-content',result).data('bs.popover');
                popover.setContent();
                // popover.$tip.addClass( $(e.target).attr('popside') );
            }, {'static': '0'});
        }
    }).attr('processed', true);
}

/**
 * Show popover nearby element
 */
function __adianti_show_popover(element, title, message, placement, custom_options)
{
    var standard_options = {trigger:"manual", title:title || '', html: true, content:message, placement:placement, sanitizeFn : function(d) { return d }};
    var options = standard_options;

    if (typeof custom_options !== undefined)
    {
        var options = Object.assign(standard_options, custom_options);
    }
    if ($(element).length>0 && $(element).css("visibility") == "visible") {
        $(element).popover(options).popover("show");
    }
}

/**
 * Start actions
 */
$(function() {
    Adianti.blockUIConter = 0;

    if (typeof $().tooltip == 'function')
    {
        $(document.body).tooltip({
            selector: "[title]",
            placement: function (tip, element) {
                    $element = $(element);

                    var valid_placements = [
                        "auto",
                        "top",
                        "right",
                        "bottom",
                        "left",
                    ];

                    if (typeof $element.attr('titside') === "undefined" || valid_placements.indexOf($element.attr('titside')) === -1) {
                        return 'top';
                    }
                    else {
                        return $(element).attr("titside");
                    }
                },
            trigger: 'hover',
            cssClass: 'tooltip',
            container: 'body',
            content: function () {
                return $(this).attr("title");
            },
            html: true
        });
    }

    if (typeof $().popover == 'function')
    {
        $( document ).on( "dialogopen", function(){
            __adianti_process_popover();
        });
    }

    if (typeof jQuery.ui !== 'undefined')
    {
        $.ui.dialog.prototype._focusTabbable = $.noop;
    }
});

/**
 * On Ajax complete actions
 */
$(document).ajaxComplete(function ()
{
    if (typeof $().popover == 'function')
    {
        __adianti_process_popover();
    }

    if (typeof $().DataTable == 'function')
    {
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
});

$( document ).ready(function() {
    /**
     * Override the default page loader
     */
    $( document ).on( 'click', '[generator="adianti"]', function()
    {
       __adianti_load_page($(this).attr('href'));
       return false;
    });

    /**
     * Close tooltips on click
     */
    $('body').on('click', function (e) {
        $('.tooltip.show').tooltip('hide');

        if (!$(e.target).is('[popover="true"]') && !$(e.target).parents('.popover').length > 0) {
            // avoid closing dropdowns inside popover (colorpicker, datepicker) when they are outside popover DOM
            if (!$(e.target).parents('.dropdown-menu').length > 0) {
                $('.popover').popover('hide');
            }
        }
    });
});

/**
 * Register page navigation
 */
window.onpopstate = function(stackstate)
{
    if (stackstate.state)
    {
        __adianti_load_page_no_register(stackstate.state.url);
    }
};

$.fn.mycenter = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.outerHeight() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.outerWidth() ) / 2+$(window).scrollLeft() + "px");
    return this;
}
