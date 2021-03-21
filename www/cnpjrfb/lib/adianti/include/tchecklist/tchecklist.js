function tchecklist_row_enable_check(table_id)
{
    $('table#'+table_id+' tbody tr').click(function(ev) {
        var check   = $(this).find('[type=checkbox]');
        var current = $(check).prop('checked');
        $(check).prop('checked', !current);
        
        if (!current) {
            $(this).addClass('selected')
        }
        else {
            $(this).removeClass('selected');
        }
    });

    $('table#'+table_id+' tbody tr input[type=checkbox]').click(function(ev) {
        var current = $(this).prop('checked');
        var tr = $(this).closest('tr');
        
        // material check
        if ($(this).offset().left < 0) {
            $(this).prop('checked', !current);
            
            if (!current) {
                $(tr).addClass('selected')
            }
            else {
                $(tr).removeClass('selected');
            }
        }
        else
        {
            if (current) {
                $(tr).addClass('selected')
            }
            else {
                $(tr).removeClass('selected');
            }
        }
        ev.stopPropagation();
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