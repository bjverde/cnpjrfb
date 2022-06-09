function tchecklist_row_enable_check(table_id)
{
    $('table#'+table_id+' tbody tr').click(function(ev) {
        ev.stopImmediatePropagation();
        ev.stopPropagation();
        ev.preventDefault();

        var check   = $(this).find('[type=checkbox]');
        var current = $(check).is(':checked');
        $(check).prop('checked', !current);

        if (!current) {
            $(this).addClass('selected')
        }
        else {
            $(this).removeClass('selected');
        }
    });

    $('table#'+table_id+' tbody tr input[type=checkbox]').click(function(ev) {
        ev.stopPropagation();

        var current = $(this).is(':checked');
        var tr = $(this).closest('tr');
        
        if (current) {
            $(tr).addClass('selected')
        }
        else {
            $(tr).removeClass('selected');
        }
    });
}

function tchecklist_select_all(generator, table_id)
{
    $('table#'+table_id+' tbody tr:visible td input[type=checkbox]').each(function(k,v){
        var tr = v.closest('tr');
        if (!generator.checked && v.checked ) {
            $(v).prop('checked', false);
            $(tr).removeClass('selected');
        }
        else if (generator.checked && !v.checked ) {
            $(v).prop('checked', true);
            $(tr).addClass('selected');
        }
    });
}