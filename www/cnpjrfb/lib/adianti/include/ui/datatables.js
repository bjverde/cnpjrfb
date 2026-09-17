function __adianti_process_datatables()
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
}