Adianti.loading = true;

Application = {};
Application.translation = {
    'en' : {
        'loading' : 'Loading',
        'close'   : 'Close',
        'insert'  : 'Insert',
        'open_new_tab' : 'Open on a new tab',
        'filters' : 'Filters'
    },
    'pt' : {
        'loading' : 'Carregando',
        'close'   : 'Fechar',
        'insert'  : 'Inserir',
        'open_new_tab' : 'Abrir em uma nova aba',
        'filters' : 'Filtros'
    },
    'es' : {
        'loading' : 'Cargando',
        'close'   : 'Cerrar',
        'insert'  : 'Insertar',
        'open_new_tab' : 'Abrir en una nueva pestaña',
        'filters' : 'Filtros'
    },
    'de' : {
        'loading' : 'Wird geladen',
        'close'   : 'Schließen',
        'insert'  : 'Einfügen',
        'open_new_tab' : 'In neuem Tab öffnen',
        'filters' : 'Filter'
    },
    'fr' : {
        'loading' : 'Chargement',
        'close'   : 'Fermer',
        'insert'  : 'Insérer',
        'open_new_tab' : 'Ouvrir dans un nouvel onglet',
        'filters' : 'Filtres'
    },
    'it' : {
        'loading' : 'Caricamento',
        'close'   : 'Chiudi',
        'insert'  : 'Inserisci',
        'open_new_tab' : 'Apri in una nuova scheda',
        'filters' : 'Filtri'
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


Adianti.showLoading = function() {
    if (Adianti.loading)
    {
        __adianti_block_ui(Application.translation[Adianti.language]['loading']);
    }
}

Adianti.onBeforeLoad = function(url) {
    setTimeout(function(){
        Adianti.showLoading()
    }, 400);
    
    if (url.indexOf('&static=1') == -1 && url.indexOf('&noscroll=1') == -1) {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    }
};

Adianti.onAfterLoad = function(url, data)
{ 
    __adianti_unblock_ui( true );
};

// set select2 language
$.fn.select2.defaults.set('language', $.fn.select2.amd.require("select2/i18n/pt"));
