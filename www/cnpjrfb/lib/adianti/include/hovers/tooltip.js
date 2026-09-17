function __adianti_process_tooltips()
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
}