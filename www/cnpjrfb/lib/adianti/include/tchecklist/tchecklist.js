function tchecklist_row_enable_check(table_id)
{
    // clique na linha
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
        
        tchecklist_fire_onselect($('table#'+table_id));
    });
    
    // clique no input
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
        
        tchecklist_fire_onselect($('table#'+table_id));
    });
}

function tchecklist_fire_onselect(table)
{
    var action = table.attr('onselect');
    
    if (action)
    {
        var checklist_name   = table.attr('name');
        
        var checks = table.find('tr.selected').find('input[type="checkbox"]');
        
        var data = {};
        data[checklist_name] = [];
        checks.each(function (k,v) {
            var input_name = $(v).attr('name');
            input_name = input_name.replace('check_'+checklist_name+'_', '');
            data[checklist_name].push(base64_decode(input_name));
        });
        
        __adianti_post_exec(action, data, null, undefined, '1');
    }
}

function tchecklist_toggle_select_all(generator, table_id)
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
    
    tchecklist_fire_onselect($('table#'+table_id));
}

function tchecklist_select_all_by_name(name, fire_events)
{
    var generator = $('table[widget="tchecklist"][name="'+name+'"] input[role="check-all"]');
    var table_id  = $('table[widget="tchecklist"][name="'+name+'"]').attr('id');
    
    $(generator).prop('checked', true);
    
    $('table#'+table_id+' tbody tr:visible td input[type=checkbox]').each(function(k,v){
        var tr = v.closest('tr');
        
        $(v).prop('checked', true);
        $(tr).addClass('selected');
    });
    
    if (fire_events) {
        tchecklist_fire_onselect($('table#'+table_id));
    }
}

function tchecklist_select_none_by_name(name, fire_events)
{
    var generator = $('table[widget="tchecklist"][name="'+name+'"] input[role="check-all"]');
    var table_id  = $('table[widget="tchecklist"][name="'+name+'"]').attr('id');
    
    $(generator).prop('checked', false);
    
    $('table#'+table_id+' tbody tr:visible td input[type=checkbox]').each(function(k,v){
        var tr = v.closest('tr');
        
        $(v).prop('checked', false);
        $(tr).removeClass('selected');
    });
    
    if (fire_events) {
        tchecklist_fire_onselect($('table#'+table_id));
    }
}


function tchecklist_enable_field(name) {
    try {
        if( $('table.tchecklist[name="'+name+'"]').length > 0) {
            $('table.tchecklist[name="'+name+'"]').parent().find('.tchecklist-disable').remove();
        }
    }
    catch (e) {
        console.log(e);
    }
}

function tchecklist_disable_field(name) {
    try {
        if( $('table.tchecklist[name="'+name+'"]').length > 0) {
            $('table.tchecklist[name="'+name+'"]').parent().prepend('<div class="tchecklist-disable"></div>')
        }
    }
    catch (e) {
        console.log(e);
    }
}