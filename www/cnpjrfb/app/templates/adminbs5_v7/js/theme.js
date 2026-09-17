/**
 * Start FULL Admin Template
 */
Template.start = function() {
    $("#sidebar-toggle").click( function() {
        $("#sidebar").toggleClass("collapsed");
    });
    
    Template.connectMenu();
    
    if (Template.navbarOptions['has_menu_mode_switch'] == '1') {
        document.documentElement.setAttribute('data-menu-theme', Template.getMenuTheme());
        $('#menu_theme_switch').prop('checked', (Template.getMenuTheme() == 'dark'));
    }
    
    if (Template.navbarOptions['has_main_mode_switch'] == '1') {
        document.documentElement.setAttribute('data-bs-theme', Template.getGlobalTheme());
        $('#global_theme_switch').prop('checked', (Template.getGlobalTheme() == 'dark'));
    }
    
    $(window).resize(__adianti_debounce(function(){
        if (Template.width !== $(window).width()) {
            Template.adjustMenu();
        }
        Template.width = $(window).width();
    }, 100));
    
    Template.adjustMenu();
    
    Template.width = $(window).width();
    
    // compatibility for jquery-ui with new BS5
    var bootstrapButton = $.fn.button.noConflict()
    $.fn.bootstrapBtn = bootstrapButton;
    
    // customize edit in line colors
    $.fn.editInPlace.defaults['bg_over'] = 'var(--bs-secondary-bg-subtle)';
    
    // start master menu
    if (Template.navbarOptions['has_master_menu'] == '1') {
        Template.generateMasterMenu();
    }
    
    // enable page tabs
    if (Template.navbarOptions['allow_page_tabs'] == '1') {
        Template.restorePageTabsLocalStorage();
    }
    
    // activate item from query string
    Template.findQueryStringMenuItem( true );
    
    // set select2 language
    $.fn.select2.defaults.set('language', $.fn.select2.amd.require("select2/i18n/"+Adianti.language));
}

/**
 * Start reduced Template inside IFRAME
 */
Template.startIframe = function() {
    Template.width = $(window).width();
    
    // compatibility for jquery-ui with new BS5
    var bootstrapButton = $.fn.button.noConflict()
    $.fn.bootstrapBtn = bootstrapButton;
    
    // set themes in the root html inside iframe
    document.documentElement.setAttribute('data-menu-theme', Template.getMenuTheme());
    document.documentElement.setAttribute('data-bs-theme', Template.getGlobalTheme());
    
    // customize edit in line colors
    $.fn.editInPlace.defaults['bg_over'] = 'var(--bs-secondary-bg-subtle)';
}

/**
 * Intercept content before renderer
 */
Template.onBeforeLoadContent = function(content, partial_load = false) {
    Adianti.requestDump = '';
    
    var found = false;
    var limit = 10000;
    
    while ( (content.indexOf('<adump>') >= 0) && limit > 0) {
        Adianti.requestDump += __adianti_string_get_between(content, '<adump>', '</adump>', true);
        var dump_contents = __adianti_string_get_between(content, '<adump>', '</adump>', false);
        content = content.replace(dump_contents, '');
        
        found = true;
        limit --;
    }
    
    if (partial_load && (typeof Template.updateDebugPanel == 'function')) {
        Template.updateDebugPanel();
    }
    return content;
}

/**
 * Hook after page loading
 */
Template.onAfterLoad = function(url, data) {
    Template.updateDebugPanel();
    
    let url_container = url.match('target_container=([0-z-]*)');
    let dom_container = data.match('adianti_target_container\\s?=\\s?"([0-z-]*)"');
    let into_right_panel = false;
    
    if (url_container || dom_container) {
        let id = '';
        if (url_container) {
            id = url_container[1];
        }
        else if (dom_container) {
            id = dom_container[1];
        }

       into_right_panel = $('#' + id).closest('#adianti_right_panel').length > 0;
    }
    
    if ((url.indexOf('target_container=adianti_right_panel') !== -1) || (data.indexOf('adianti_target_container="adianti_right_panel"') !== -1) ) {
        if (data.indexOf('override="true"') !== -1) {
            $('#adianti_right_panel').find('[page_name]').not(':last').remove();
        }
        
        if ($( window ).width() >= 800) {
            // if Desktop, uses offset and custom widths
            Template.stackRightPanels();
        }
        
        if ($('#adianti_right_panel').is(":visible") == false) {
            $('body').css("overflow", "hidden");
            $('#adianti_right_panel').show('slide',{direction:'right'}, 320);
        }
        
        if ($('#adianti_right_panel').find('[page_name]').length > 1) {
            let current_page_name = ($('#adianti_right_panel').find('[page_name]').last().attr('page_name'));
            if (data.indexOf('page_name="'+current_page_name+'"') == -1) // avoid slide again if top curtain = requested one
            {
                $('#adianti_right_panel').find('[page_name]').last().hide();
                $('#adianti_right_panel').find('[page_name]').last().show('slide',{direction:'right'}, 320);
            }
        }
        
        var warnings = $('#adianti_right_panel').clone().find('div[page_name],script').remove().end().html();
        $('#adianti_right_panel').find('[page_name]').last().prepend(warnings);
        $("#adianti_right_panel").animate({ scrollTop: 0 }, "fast");
    }
    else if ( (url.indexOf('&static=1') == -1) && (data.indexOf('widget="TWindow"') == -1) && ! into_right_panel ) {
        if ($('#adianti_right_panel').is(":visible")) {
            $('#adianti_right_panel').hide();
            $('#adianti_right_panel').html('');
            $('body').css('overflow', 'unset');
        }
    }
    
    if ($('#adianti_tab_area').is(':visible')) {
        var index_url = url.replace('engine.php', 'index.php');
        
        if (typeof Template.openPageTab == 'function') {
            var tab_found = Template.openPageTab(index_url + '&template=iframe', true);
            
            if (!tab_found) {
                // switch to normal view if not opening an iframed page.
                $('#adianti_tab_content_wrapper').hide();
                $('#adianti_div_content_wrapper').show();
    
                Template.goFirstPageTab();
                
                // get the menu item label
                let menu_link = $('a.sidebar-link[href="' + index_url + '"]').first();
                if (menu_link.length > 0 ) {
                    Template.setFirstPageTabInfo( menu_link.find('span').text(), index_url );
                }
            }
        }
    }
};

Template.onAfterPost = Template.onAfterLoad;

/**
 * Stack right panels, adjust sizes
 */
Template.stackRightPanels = function() {
    var default_offset = 20;
    var default_width  = 800;
    var max_panel_width = 0;
    var right_panels = $('#adianti_right_panel').find('[page_name]').length;
    
    $('#adianti_right_panel').find('[page_name]').each(function(k,v) {
        if ($(v).data('curtain-width') > 0) {
            // maximum width is the window width
            if ($(v).data('curtain-width') > $(window).width()) {
                $(v).data('curtain-width', $(window).width() - default_offset);
            }
            $(v).width($(v).data('curtain-width'));
        }
        
        max_panel_width = Math.max( $(v).data('curtain-width') || default_width, max_panel_width);
    });
    max_panel_width += (default_offset * (right_panels-1));
    
    var previous_page_width = default_width;
    
    $('#adianti_right_panel').find('[page_name]').each(function(k,v) {
        let this_path_width = $(v).data('curtain-width') || default_width;
        
        // variable page left offset according to previous page
        let adittional_offset = (previous_page_width > this_path_width) ? (previous_page_width - this_path_width) : 0;
        $(v).css('left', (k * 20) + adittional_offset + 'px');
        
        previous_page_width = this_path_width;
    });
    
    $('#adianti_right_panel').css('width', (max_panel_width) + 'px');
}

/**
 * Close right panel
 */
Template.closeRightPanel = function () {
    let curtain_width  = $( document ).width() >= 800 ? 780 : ($( document ).width());
    let default_offset = $( document ).width() >= 800 ? 20  : 0;
    
    if ($('.popover.trigger.click.show').length > 0) {
        $(".popover.trigger.click:last").remove();
    }
    else if ($('#adianti_right_panel > [page_name]').length > 0) {
        if ($('#adianti_right_panel > [page_name]').length == 1) {
            $('#adianti_right_panel').hide('slide',{direction:'right', complete: function() {
                $('body').css('overflow', 'unset');
                $('#adianti_right_panel').html('');
            }},320)
        }
        
        $('#adianti_right_panel > [page_name]').last().hide('slide',{direction:'right', complete: function() {
            $('#adianti_right_panel > [page_name]').last().remove();
            
            if ($( window ).width() >= 800) {
                if ($('#adianti_right_panel > [page_name]').length > 0) {
                    // if Desktop, uses offset and custom widths
                    Template.stackRightPanels();
                }
                else {
                    $('#adianti_right_panel').css('width', '');
                }
            }
        }}, 320);
    }
}

/**
 * Close all right panels
 */
Template.closeRightPanels = function() {
    $('#adianti_right_panel').hide('slide',{direction:'right', complete: function() {
        $('body').css('overflow', 'unset');
        $('#adianti_right_panel').html('');
    }},320)
}

/**
 * Close all windows
 */
Template.closeWindows = function() {
    $('[widget="TWindow"]').remove();
}

/**
 * Connect main menu actions
 */
Template.connectMenu = function() {
    $('.sidebar-nav:visible a[generator="adianti"]').click(function(el) {
        $('.sidebar-nav:visible a[generator="adianti"]').removeClass('active');
        
        // activate current item
        $(el.target).closest('a').addClass('active');
        
        // mark parents to keep-opened
        $(el.target).parents('li').data('keep-opened', true);
        
        // unselect all links that are not parent
        $('#menu-wrapper').find('li').each(function(k,v) {
            if ($(v).data('keep-opened')!== true) {
                $(v).find('>a.sidebar-link').attr('aria-expanded', 'false');
            }
        });
        // releases keep-opened
        $('#menu-wrapper').find('li').removeData('keep-opened');
        
        if ($(window).width() < 767 || Template.navbarOptions['always_collapse'] == '1') {
            $("#sidebar").addClass("collapsed");
        }
    });
}

/**
 * Update debug panel
 */
Template.updateDebugPanel = function() {
    try {
        var url  = Adianti.requestURL;
        var body = Adianti.requestData;
        var dumps = Adianti.requestDump;
        
        url = url.replace('engine.php?', '');
        $('#request_url_panel').html( pretty.json.print(__adianti_query_to_json(urldecode(url)), undefined, 4) );
        $('#request_data_panel').html( pretty.json.print(__adianti_query_to_json(urldecode(body)), undefined, 4) );
        $('#request_dump_panel').html( dumps );
    }
    catch (e) {
        console.log(e);
    }
}

/**
 * Create master module menu
 */
Template.generateMasterMenu = function() {
    if ($('#sidebar').length == 0) {
        return;
    }
    
    // insert module menu
    $('#sidebar').prepend('<div id="module-menu" style="position:relative"> <div id="module-menu-top"></div><div id="module-menu-bottom" style="position:absolute;bottom:10px;display: flex;flex-direction: column;"></div>');
    
    // adjust size and float
    $('#menu-wrapper').css('float', 'left');
    $('#menu-wrapper').width('calc(100% - 70px)');
    
    // transfer logo to master menu
    var logo = $('#sidebar .sidebar-logo').clone();
    logo.css('margin', '5px').css('margin-bottom', '30px');
    $('#module-menu-top').append(logo);
    
    // replace menu's name with system's name, remove system info
    $('.sidebar-menu-title .name').html( $('.sidebar-system-name .name') );
    $('.sidebar-system-name').remove();
    
    // hide main menu, insert partial menu
    $('#side-menu').hide();
    $('#side-menu').after('<ul id="side-menu-part" class="sidebar-nav">');
    
    // start with first menu
    $('#side-menu-part').html( $('#side-menu>li:first>ul').children().clone() );
    // adjust tootips that were already processed in the hidden full menu
    $('#side-menu-part').html( $('#side-menu-part').html().replaceAll('data-original-title', 'title') );
    
    $('#side-menu>li>a').each(function(k, v) {
        // create one master button for each first level <li><a>
        var btn = $('<button class="master-menu-item"></button>');
        btn.attr('title', $(v).find('span').text()); // use label as title
        var icon = $(v).find('i').attr('class') || 'far fa-circle fa-fw';
        btn.append('<i class="'+icon+'"></i>');
        $('#module-menu-top').append(btn);
        
        // connect elements via bs-target random id
        btn.data('bs-target-id', $(v).data('bs-target'));
        
        if ($(v).attr('href').substring(0,4) == 'http') {
            btn.click( function(ev) {
                window.open($(v).attr('href'), "_blank");
            });
        }
        else {
            // connect master menu click
            btn.click( function(ev) {
                // toggle active
                $('#module-menu .master-menu-item').removeClass('active');
                $(this).addClass('active');
                
                // find the correct submenu
                let target_bs_id = $(this).data('bs-target-id');
                let menu_item = $('a[data-bs-target="'+target_bs_id+'"]');
                
                // collect the submenu items ant transfer to side-menu-part
                let ul = menu_item.closest('li').find('>ul');
                if (ul.length > 0) {
                    $('#side-menu-part').html(ul.children().clone());
                    
                    // adjust tootips that were already processed in the hidden full menu
                    $('#side-menu-part').html( $('#side-menu-part').html().replaceAll('data-original-title', 'title') );
                    __adianti_process_tooltips();
                }
                else {
                    // if doesn't has subitems, so trigger click
                    $(menu_item).trigger('click');
                }
                
                // connect recently created submenu actions
                Template.connectMenu();
                
                // Keep the current URL active when changing module
                Template.findQueryStringMenuItem( false );
            });
        }
    });
    
    Template.transferTopActionsToLeftSidebar();
    
    // activate first master menu entry
    $('#module-menu .master-menu-item:first').addClass('active');
    
    // connect submenu actions
    Template.connectMenu();
    
    // reprocess tooltips for recently created items
    __adianti_process_tooltips();
}

/**
 * Transfer top menu actions to left sidebar
 */
Template.transferTopActionsToLeftSidebar = function() {
    // switch class to move to left master sidebar
    $('#navbar-top-right-area').find('.superlight').removeClass('superlight').addClass('master-menu-item');
    $('#navbar-top-right-area button>span.username').remove(); // Remove "user name" span
    
    // move to left master sidebar
    $('#module-menu-bottom').append( $('#navbar-top-right-area').detach() );
    $('#alert_notifications').removeClass('border-light');
    $('#alert_notifications').removeClass('start-100');
    $('#alert_notifications').css('left', '90%');
    $('#alert_messages').removeClass('border-light');
    $('#alert_messages').removeClass('start-100');
    $('#alert_messages').css('left', '90%');
}

/**
 * Find right menu item from URL
 */
Template.findQueryStringMenuItem = function( activate ) {
    // test if has a query string (F5)
    var query_string = __adianti_query_string();
    
    if (Template.navbarOptions['has_master_menu'] == '1') {
        if (typeof query_string['class'] !== 'undefined') {
            // find the item in full menu
            let found = $('#side-menu').find('a[href="index.php?class='+query_string['class']+'"]');
            
            // first first <li> parent of menu item
            var master_item = found.parentsUntil('.sidebar-nav').closest('li.sidebar-item');
            $('#module-menu-top>.master-menu-item').each(function(k,v) {
                if ($(v).data('bs-target-id') == master_item.find('a').data('bs-target')) {
                    if (activate) {
                        // master item found, click
                        $(v).trigger('click');
                    }
                    
                    // collapse all <ul>'s
                    $('#menu-wrapper').find('ul').removeClass('show');
                    $('#menu-wrapper').find('li').find('>a.sidebar-link').attr('aria-expanded', 'false');
                    
                    // find menu item in side-menu-part
                    let found_sub = $('#side-menu-part').find('a[href="index.php?class='+query_string['class']+'"]');
                    if (found_sub.length>0) {
                        // activate the menu item
                        found_sub.addClass('active');
                        
                        // collapse the parent <ul>'s
                        found_sub.parents('li').find('ul').addClass('show');
                        found_sub.parents('li').find('>a.sidebar-link').not(found_sub).attr('aria-expanded', 'true');
                    }
                }
            });
        }
    }
    else {
        if (typeof query_string['class'] !== 'undefined') {
            // unactivate every element
            $('.sidebar-link').removeClass('active');
            
            // collapse all <ul>'s
            $('#menu-wrapper').find('ul').removeClass('show');
            $('#menu-wrapper').find('li').find('>a.sidebar-link').attr('aria-expanded', 'false');
            
            // find the item in full menu
            let found = $('#side-menu').find('a[href="index.php?class='+query_string['class']+'"]');
            
            if (found.length>0) {
                // activate the menu item
                found.addClass('active');
                
                // collapse the parent <ul>'s
                found.parents('li').find('>ul').addClass('show');
                found.parents('li').find('>a.sidebar-link').attr('aria-expanded', 'true');
            }
        }
    }
}

/**
 * Create page tab from menu click
 */
Template.createPageTabFromMenu = function(element) {
    var link  = $(element).closest('a.sidebar-link')
    var href  = link.attr('href') + '&template=iframe';
    
    var found = false;
    
    // switch to tab view
    $('#adianti_div_content_wrapper').hide();
    $('#adianti_tab_content_wrapper').show();
    
    // try to find iframe
    $('#adianti_tab_content_wrapper iframe').hide();
    $('#adianti_tab_content_wrapper iframe').each(function(k,v) {
        if (href == $(v).attr('src')) {
            $(v).show();
            found = true;
        }
    });
    
    // create item and iframe
    if (!found) {
        var label = $(element).closest('a.sidebar-link').find('span').text();
        
        // create tab and iframe
        Template.createPageTab(label, href);
        __adianti_process_tooltips();
    }
    
    // activate the item in tab menu
    $('#adianti_tab_area .nav-link').removeClass('active');
    $('#adianti_tab_area .nav-link[data-href="'+href+'"]').addClass('active');
    
    // activate the item in the side menu
    $('.sidebar-nav:visible a[generator="adianti"]').removeClass('active');
    link.addClass('active');
    
    // localstorage control
    Template.updatePageTabsLocalStorage();
}

/**
 * Create page tab content
 */
Template.createPageTab = function(label, href) {
    
    var close_label = Application.translation[Adianti.language]['close'];
    // create new item
    var new_tab = $('<li class="nav-item"><a data-iframed=true data-href="'+href+'" class="nav-link" style="float:left" onclick="Template.openPageTab(\''+href+'\')">' + label + '</a><a style="float:left;padding-top:0.5rem;padding-left:0.5rem" title="'+close_label+'" onclick="Template.closePageTab(this)"><i class="fa-solid fa-xmark red"></i></a></li>');
    $('#adianti_tab_area').append(new_tab);
    
    // create new iframe
    $('#adianti_tab_content_wrapper iframe').hide();
    var iframe = $('<iframe src="' + href + '" style="width:100%;height:calc(100vh - 200px)"></iframe>');
    $('#adianti_tab_content_wrapper .container-fluid').append(iframe);
    
    // clear main div and first page tab, if equals to the newly created tab 
    if (href.replace('&template=iframe', '') == $('#adianti_tab_area .nav-link:first').data('href') ) {
        Template.setFirstPageTabInfo('', '');
        $('#adianti_div_content').html('');
    }
}

/**
 * Open page tab
 */
Template.openPageTab = function(href, from_side_bar_click) {
    var found = false;
    
    // hide all iframes
    $('#adianti_tab_content_wrapper iframe').hide();
    $('#adianti_tab_content_wrapper iframe').each(function(k,v) {
        if (href == $(v).attr('src')) {
            // show the found iframe
            $(v).show();
            
            // switch to tab view
            $('#adianti_div_content_wrapper').hide();
            $('#adianti_tab_content_wrapper').show();
            
            // activate the item in tab menu
            $('#adianti_tab_area .nav-link').removeClass('active');
            $('#adianti_tab_area .nav-link[data-href="'+href+'"]').addClass('active');
            
            found = true;
            
            // when from sidebar click, it means the page was already loaded in main <div> (admin.js).
            // and if we are here, it means that the page is already tab-iframed.
            // so to avoid duplicate it, the main <div> is cleaned.
            if (from_side_bar_click) {
                Template.setFirstPageTabInfo('', '');
                $('#adianti_div_content').html('');
            }
        }
    });
    
    return found;
}

/**
 * Close page tab
 */
Template.closePageTab = function(generator) {
    var page = $(generator).parent().find('a.nav-link');
    if (page.length > 0) {
        var href = page.data('href');
        // remove just iframed tabs
        if (page.data('iframed')) {
            page.closest('li').remove();
            $('iframe[src="'+href+'"]').remove();
            
            Template.goLastPageTab();
        }
    }
    
    Template.updatePageTabsLocalStorage();
}

/**
 * Go to first page tab
 */
Template.goFirstPageTab = function() {
    // activate only first tab
    $('#adianti_tab_area .nav-link').removeClass('active');
    $('#adianti_tab_area .nav-link:first').addClass('active');
    
    // activate main view
    $('#adianti_div_content_wrapper').show();
    $('#adianti_tab_content_wrapper').hide();
}

/**
 * Go to last page tab
 */
Template.goLastPageTab = function() {
    // activate only first tab
    $('#adianti_tab_area .nav-link').removeClass('active');
    $('#adianti_tab_area .nav-link:not(.close):last').addClass('active');
    
    var iframed = $('#adianti_tab_area .nav-link:not(.close):last').data('iframed');
    var href = $('#adianti_tab_area .nav-link:not(.close):last').data('href');
    
    if (iframed)
    {
        // try to find iframe
        $('#adianti_tab_content_wrapper iframe').hide();
        $('#adianti_tab_content_wrapper iframe').each(function(k,v) {
            if (href == $(v).attr('src')) {
                $(v).show();
                found = true;
            }
        });
    }
    else
    {
        // activate main view
        $('#adianti_div_content_wrapper').show();
        $('#adianti_tab_content_wrapper').hide();
    }
}

/**
 * Update local storage with page tabs
 */
Template.updatePageTabsLocalStorage = function() {
    var list = [];
    $('#adianti_tab_area .nav-link:not(.close)[data-iframed]').each(function(k,v) {
        list.push( { href: $(v).data('href'), label: $(v).text() } );
    });
    let appname = Adianti.applicationName || '';
    localStorage.setItem(appname+".page-tabs", JSON.stringify(list));
}

/**
 * Recreate page tabs from local storage
 */
Template.restorePageTabsLocalStorage = function() {
    let appname = Adianti.applicationName || '';
    var local_tabs_json = localStorage.getItem(appname+".page-tabs");
    if (local_tabs_json) {
        var local_tabs = JSON.parse(local_tabs_json);
        for (tab of local_tabs)
        {
            Template.createPageTab( tab['label'], tab['href']);
        }
    }
}

/**
 * Set first page tab information
 */
Template.setFirstPageTabInfo = function(label, url) {
    $('#adianti_tab_area .nav-link:first span').text(label);
    $('#adianti_tab_area .nav-link:first').data('href', url);
}

/**
 * Adjust menu size according screen size
 */
Template.adjustMenu = function() {
  if ($(window).width() > 767) {
      $("#sidebar").removeClass("collapsed");
      $("body").removeClass("responsive");
  }
  else {
      $("#sidebar").addClass("collapsed");
      $("body").addClass("responsive");
  }
}

/**
 * Toggle global theme
 */
Template.toggleGlobalTheme = function(generator) {
    let next = ($(generator).is(":checked")) ? 'dark' : 'light';
    let appname = Adianti.applicationName || '';
    
    document.documentElement.setAttribute('data-bs-theme', next);
    localStorage.setItem(appname+".bs-theme", next);
    
    Template.setIFrameGlobalTheme(next);
    Template.updateChartsMode();
}

/**
 * Toggle menu theme
 */
Template.toggleMenuTheme = function(generator) {
    let next = ($(generator).is(":checked")) ? 'dark' : 'light';
    let appname = Adianti.applicationName || '';
    
    document.documentElement.setAttribute('data-menu-theme', next);
    localStorage.setItem(appname+".menu-theme", next);
    
    Template.setIFrameMenuTheme(next);
}

/**
 * Returns global theme
 */
Template.getGlobalTheme = function() {
    let appname = Adianti.applicationName || '';
    return ( localStorage.getItem(appname+".bs-theme") !== null ) ? localStorage.getItem(appname+".bs-theme") : $(document.documentElement).data('bs-theme');
}

/**
 * Returns menu theme
 */
Template.getMenuTheme = function() {
    let appname = Adianti.applicationName || '';
    return ( localStorage.getItem(appname+".menu-theme") !== null ) ? localStorage.getItem(appname+".menu-theme") : $(document.documentElement).data('menu-theme');
}

/**
 * Change global theme in all IFRAMEs
 */
Template.setIFrameGlobalTheme = function(theme) {
    // select all iframes
    const iframes = document.querySelectorAll("iframe");
    
    iframes.forEach(iframe => {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            iframeDoc.documentElement.dataset.bsTheme = theme; // Exemplo: altera a cor de fundo
        } catch (error) {
            console.warn(error);
        }
    });
}

/**
 * Change menu theme in all IFRAMEs
 */
Template.setIFrameMenuTheme = function(theme) {
    // select all iframes
    const iframes = document.querySelectorAll("iframe");
    
    iframes.forEach(iframe => {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            iframeDoc.documentElement.dataset.menuTheme = theme; // Exemplo: altera a cor de fundo
        } catch (error) {
            console.warn(error);
        }
    });
}

/**
 * Set NAVBAR options
 */
Template.setNavbarOptions = function(options) {
    Template.navbarOptions = options;
    
    if ((options['has_program_search'] == '1') && ($("#navbar-wrapper").length > 0) && ($('html').data('public') !== 'yes')) {
        $.get("engine.php?class=SearchBox", function(data) {
            $("#search-box").append(data);
        });
    }
    
    if (options['has_notifications'] == '1') {
        $('#has_notifications').css('display', 'inline-block');
    }
    
    if (options['has_messages'] == '1') {
        $('#has_messages').css('display', 'inline-block');
    }
    
    if (options['has_docs'] == '1') {
        $('#has_docs').css('display', 'inline-block');
    }
    
    if (options['has_contacts'] == '1') {
        $('#has_contacts').css('display', 'inline-block');
    }
    
    if (options['has_support_form'] == '1') {
        $('#has_support_form').css('display', 'inline-block');
    }
    
    if (options['has_news'] == '1') {
        $('#has_news').css('display', 'inline-block');
    }
    
    if (options['has_wiki'] == '1') {
        $('#has_wiki').css('display', 'inline-block');
    }
    
    if (options['has_menu_mode_switch'] == '1') {
        $('#has_menu_mode_switch').css('display', 'inline');
    }
    
    if (options['has_main_mode_switch'] == '1') {
        $('#has_main_mode_switch').css('display', 'inline-block');
    }
    
    if (options['allow_page_tabs'] == '1') {
        $('#adianti_tab_area').show();
    }
    
    if (options['only_top_menu'] == '1') {
        $('#sidebar').remove();
        $('#sidebar-toggle').remove();
    }
    
    if (options['header_display'] == 'hidden') {
        $('nav.navbar').addClass('display-mobile');
    }
    
    if (options['footer_display'] == 'hidden') {
        $('footer').hide();
    }
}

/**
 * Set theme options
 */
Template.setThemeOptions = function(options) {
    Template.themeOptions = options;
    
    if (options['box_layout'] == '1') {
        $('body').addClass('boxed-layout');
    }
    
    if (options['menu_dark_color']) {
        $('head').append('<style id="customCSS">[data-menu-theme=dark] #sidebar { background: '+options['menu_dark_color']+'; }</style>');
    }
    
    if (options['menu_light_color']) {
        $('head').append('<style id="customCSS">[data-menu-theme=light] #sidebar { background: '+options['menu_light_color']+'; }</style>');
    }
    
    if (options['menu_mode']) {
        let appname = Adianti.applicationName || '';
        if (localStorage.getItem(appname+".menu-theme") == null || Template.navbarOptions['has_menu_mode_switch'] == '0') {
            document.documentElement.setAttribute('data-menu-theme', options['menu_mode']);
            $('#menu_theme_switch').prop('checked', (options['menu_mode'] == 'dark'));
        }
    }
    
    if (options['main_mode']) {
        let appname = Adianti.applicationName || '';
        if (localStorage.getItem(appname+".bs-theme") == null || Template.navbarOptions['has_main_mode_switch'] == '0') {
            document.documentElement.setAttribute('data-bs-theme', options['main_mode']);
            $('#global_theme_switch').prop('checked', (options['main_mode'] == 'dark'));
        }
        
        Template.setIFrameGlobalTheme(options['main_mode']);
        Template.setIFrameMenuTheme(options['menu_mode']);
        Template.updateChartsMode();
    }
}

/**
 * Set logintheme options
 */
Template.setLoginThemeOptions = function(options) {
    if (options['login_mode']) {
        document.documentElement.setAttribute('data-bs-theme', options['login_mode']);
    }
}

Template.updateChartsMode = function() {
    var mode = Template.getGlobalTheme();
    
    if (mode == 'light') {
        Chart.defaults.color = '#717171';
        Chart.defaults.borderColor = 'rgba(0,0,0,0.1)';
        Chart.defaults.tickColor = 'rgba(0,0,0,0.6)';
    }
    else {
        Chart.defaults.color = '#E0E0E0';
        Chart.defaults.borderColor = 'rgba(255,255,255,0.2)';
        Chart.defaults.tickColor = 'rgba(255,255,255,0.8)';
    }
    
    // update all Charts in DOM
    const canvasElements = document.querySelectorAll('canvas');
    
    canvasElements.forEach(canvas => {
        const chartInstance = Chart.getChart(canvas); // Chart.js v3+
        if (chartInstance) {
            // Atualiza cor da grade no eixo X
            if (chartInstance.options.scales?.x?.grid) {
                chartInstance.options.scales.x.grid.color = Chart.defaults.borderColor; // cor clara para dark mode
            }
    
            // Atualiza cor da grade no eixo Y
            if (chartInstance.options.scales?.y?.grid) {
                chartInstance.options.scales.y.grid.color = Chart.defaults.borderColor;
            }
            
            // Eixo X
            if (chartInstance.options.scales?.x?.ticks) {
                chartInstance.options.scales.x.ticks.color = Chart.defaults.tickColor;
            }
    
            // Eixo Y
            if (chartInstance.options.scales?.y?.ticks) {
                chartInstance.options.scales.y.ticks.color = Chart.defaults.tickColor;
            }
            chartInstance.update();
        }
    });
}

/**
 * Set dialog options
 */
Template.setDialogOptions = function(options) {
    Template.dialogOptions = options;
    
    if (options['use_swal'] == '1') {
        window.__adianti_dialog_std = __adianti_dialog;
        
        window.__adianti_dialog = function( options ) {
            setTimeout( function() {
                // override if there's no dialog at all or a question dialog.
                if ($('.swal2-container').length == 0 || $('.swal2-container>div.swal2-icon-question').length == 1) {
                    Swal.fire({
                      title: options.title,
                      html: options.message,
                      icon: options.type,
                      allowEscapeKey: true, /**(typeof options.callback == 'undefined'),*/
                      allowOutsideClick: true /** (typeof options.callback == 'undefined') **/
                    }).then((result) => {
                      if (result.isConfirmed || typeof (result.dismiss !== 'undefined')) {
                        if (typeof options.callback != 'undefined') {
                            options.callback();
                        }
                      }
                    });
                }
                else if ( ($('#swal2-title').html() !== options.title) || $('#swal2-html-container').html() !== options.message) {
                    window.__adianti_dialog_std( options );
                }
            }, 100);
        }
        
        window.__adianti_message = function(title, message, callback) {
            __adianti_dialog( { type: 'success', title: title, message: message, callback: callback} );
        }
        
        window.__adianti_question = function(title, message, callback_yes, callback_no, label_yes, label_no) {
            setTimeout( function() {
                Swal.fire({
                  title: title,
                  html: message,
                  icon: 'question',
                  showDenyButton: true,
                  confirmButtonText: label_yes,
                  denyButtonText: label_no
                }).then((result) => {
                  if (result.isConfirmed) {
                    if (typeof callback_yes != 'undefined') {
                        callback_yes();
                    }
                  } else if (result.isDenied) {
                    if (typeof callback_no != 'undefined') {
                        callback_no();
                    }
                  }
                });
            }, 100);
        }
    }
}

/**
 * Configure template
 */
Template.configure = function(options, context = 'main') {
    Template.options = options;
    if (context !== 'login') {
        Template.setNavbarOptions(options['navbar'] ?? {});
        Template.setThemeOptions(options['theme'] ?? {});
    }
    else {
        Template.setLoginThemeOptions(options['theme'] ?? {});
        Template.navbarOptions = {};
        Template.themeOptions = {};
    }
    
    Template.setDialogOptions(options['dialogs'] ?? {});
}

/**
 * Return configured options
 */
Template.getConfigureOptions = function() {
    return Template.options;
}

/**
 * Trigger click after timeout
 */
Template.triggerClick = function(selector, timeout) {
    $(document).ready(function() {
        function tryTriggerClick() {
            if ($.active === 0) {
                if (Template.timeoutId) {
                    clearTimeout(Template.timeoutId);
                }

                Template.timeoutId = setTimeout(function() {
                    $(selector).click();
                    Template.timeoutId = null;
                }, timeout);
            } else {
                // Try again if there are pending ajax requests
                setTimeout(tryTrigger, 100);
            }
        }

        tryTriggerClick();
    });
};

/**
 * Collapse left search panel
 */
Template.collapseLeftSearchPanel = function() {
    $('.card-left').removeClass('col-lg-3');
    $('.card-right').removeClass('col-lg-9');
    $('.card-right').addClass('col-lg-12');
    $('.card-left').children().hide();
    $('.card-left').append(`<div id="tbutton_unfold_button" name="collapse_button" class="btn btn-default btn-sm collapse-button" onclick="Template.unfoldLeftSearchPanel();" aria-label="Recolher"><span>
                                <i class="fa fa-filter" style=";padding-right:4px"></i>`+Application.translation[Adianti.language]['filters']+`</span> </div>`);

}

/**
 * Unfold left search panel
 */
Template.unfoldLeftSearchPanel = function () {
    $('.card-left').addClass('col-lg-3');
    $('.card-right').addClass('col-lg-9');
    $('.card-right').removeClass('col-lg-12');
    $('.card-left').children().show();    
    $('#tbutton_unfold_button').remove();
}
