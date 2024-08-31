loading = true;

Application = {};
Application.translation = {
    'en' : {
        'loading' : 'Loading',
        'close'   : 'Close',
        'insert'  : 'Insert',
        'open_new_tab' : 'Open on a new tab'
    },
    'pt' : {
        'loading' : 'Carregando',
        'close'   : 'Fechar',
        'insert'  : 'Inserir',
        'open_new_tab' : 'Abrir em uma nova aba'
    },
    'es' : {
        'loading' : 'Cargando',
        'close'   : 'Cerrar',
        'insert'  : 'Insertar',
        'open_new_tab' : 'Abrir en una nueva pestaña'
    }
};

Adianti.onClearDOM = function(){
	/* $(".select2-hidden-accessible").remove(); */
	/* $(".colorpicker-hidden").remove(); */
	$(".pcr-app").remove();
	$(".select2-display-none").remove();
	$(".tooltip.fade").remove();
	$(".select2-drop-mask").remove();
	/* $(".autocomplete-suggestions").remove(); */
	$(".datetimepicker").remove();
	$(".note-popover").remove();
	$(".dtp").remove();
	$("#window-resizer-tooltip").remove();
};


function showLoading() 
{ 
    if(loading)
    {
        __adianti_block_ui(Application.translation[Adianti.language]['loading']);
    }
}

Adianti.onBeforeLoad = function(url) 
{ 
    loading = true; 
    setTimeout(function(){showLoading()}, 400);
    if (url.indexOf('&static=1') == -1 && url.indexOf('&noscroll=1') == -1) {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    }
};

Adianti.onAfterLoad = function(url, data)
{ 
    loading = false; 
    __adianti_unblock_ui( true );
    
    // Fill page tab title with breadcrumb
    // window.document.title  = $('#div_breadcrumbs').text();
};

// set select2 language
$.fn.select2.defaults.set('language', $.fn.select2.amd.require("select2/i18n/pt"));
