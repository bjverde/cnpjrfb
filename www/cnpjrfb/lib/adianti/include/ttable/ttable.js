function ttable_clone_previous_row(element)
{
    var table    = $(element).closest('table');
    var last_row = table.find('tbody tr:last');
    var insert   = last_row.clone();
    tfieldlist_reset_fields(insert, true);
    table.find('tbody').append(insert);
    ttable_reset_counter(table);
}

function ttable_clone_row(element)
{
    var current = $(element).closest('tr');
    var table = $(element).closest('table');
    var previous = current.prev();
    var insert = current.clone();
    tfieldlist_reset_fields(insert, false);
    current.after($(insert));
    
    ttable_reset_counter(table);
}

function ttable_reset_counter(table)
{
    var count = 0;
    var rows = $(table).find('tr').each(function(index, row) {
        var fields = $(row).find('input,select');
        $(row).data('row', count);
        
        if (fields.length > 0)
        {
            $.each(fields, function(findex, field)
            {
                $(field).data('row', count);
            });
            count ++;
        }
    });
}

function ttable_remove_row(element)
{
    // timeout to avoid close parent when inside popover
    setTimeout( function() {
        var table = $(element).closest('table');
        var rows  = table.find('tbody').find('tr:visible').length;
        tr = $(element).closest('tr');
        
        if (rows == 1) // last row
        {
            var insert = tr.clone();
            tfieldlist_reset_fields(insert, true);
            tr.after($(insert));
            tr.remove();
        }
        else
        {
            tr.remove();
        }
        ttable_reset_counter(table);
    }, 10);
}

function ttable_remove_row_by_id(table_id, row_id)
{
	$('#'+table_id).find('tr[id='+row_id+']').remove();
}

function ttable_show_row(table, rowid)
{
    $('#'+table).find('tr#'+rowid).show();
}

function ttable_hide_row(table, rowid)
{
    $('#'+table).find('tr#'+rowid).hide();
}

function ttable_sortable_rows(id, handle, callback)
{
    $(document).ready( function() {
        $('#'+id+' tbody').sortable({
            handle: handle,
            deactivate: callback
        });
    });
}

function ttable_get_column_values(id, column)
{
    return $('#'+id+' tbody').find('tr').find('td:nth-child('+column+')').map(function () {
               return this.innerText;
           }).get().join(',');
}

function ttable_add_row(table_id, section, row)
{
    $('#' + table_id + ' tbody').append(base64_decode(row));
}

function ttable_replace_row_by_id(table_id, id, row)
{
    if ($('#' + table_id + ' tbody').find('#'+id).length > 0) {
        $('#' + table_id + ' tbody').find('#'+id).replaceWith(base64_decode(row));
    }
    else {
        $('#' + table_id + ' tbody').append(base64_decode(row));
    }
}