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
    
    if (!useTabs && !mdiWindows)
    {
        $(function() {
            $('#adianti_content .adianti_tabs_container').hide();
            console.log(1);
        });
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
