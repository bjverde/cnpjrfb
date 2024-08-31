function tfieldlist_reset_fields(row, clear_fields)
{
    var uniqid = parseInt(Math.random() * 100000000);
    $(row).attr('id', uniqid);
    var fields = $(row).find('input,select');
    var newids = [];
    
    $.each(fields, function(index, field)
    {
        var field_id = $(field).attr('id');
        var field_component = $(field).attr('widget');
        var field_role = $(field).attr('role');
        
        if (typeof field_id !== "undefined")
        {
            var field_id_parts = field_id.split('_');
            field_id_parts.pop();
            var field_prefix = field_id_parts.join('_');
            var new_id = field_prefix + '_' + uniqid;
            var parent = $(field).parent();
            
            if (newids.indexOf(new_id) >= 0 )
            {
                var new_id = field_prefix + parseInt(Math.random() * 100) + '_' + uniqid;
            }
            newids.push(new_id);
            
            if (field_component == 'tdate' || field_component == 'ttime' || field_component == 'tdatetime')
            {
                // realocate in dom
                $(field).attr('id', new_id);
                if (clear_fields) {
                    $(field).val('');
                }
                
                if (typeof $(field).attr('exitaction') !== 'undefined') {
                    $(field).attr('exitaction', $(field).attr('exitaction').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onblur') !== 'undefined') {
                    $(field).attr('onblur', $(field).attr('onblur').replace(field_id, new_id));
                }
                if (typeof $(field).attr('changection') !== 'undefined') {
                    $(field).attr('changection', $(field).attr('changection').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onchange') !== 'undefined') {
                    $(field).attr('onchange', $(field).attr('onchange').replace(field_id, new_id));
                }
                
                grandparent = $(parent).parent();
                field = $(field).detach()
                $(parent).remove();
                grandparent.append(field);
                
                var script_filter = field_component;
                if (field_component == 'ttime') {
                  var script_filter = 'tdatetime';
                }
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(grandparent, script_filter, function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
            }
            else if (field_component =='tentry')
            {
                parent = $(field).closest('td');
                $(field).attr('id', new_id);
                if (clear_fields) {
                    $(field).val('');
                }
                
                if (typeof $(field).attr('exitaction') !== 'undefined') {
                    $(field).attr('exitaction', $(field).attr('exitaction').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onblur') !== 'undefined') {
                    $(field).attr('onblur', $(field).attr('onblur').replace(field_id, new_id));
                }
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(parent, 'tentry', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
            }
            else if (field_component =='tspinner')
            {
                $(field).attr('id', new_id);
                if (clear_fields) {
                    $(field).val('');
                }
                
                if (typeof $(field).attr('exitaction') !== 'undefined') {
                    $(field).attr('exitaction', $(field).attr('exitaction').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onblur') !== 'undefined') {
                    $(field).attr('onblur', $(field).attr('onblur').replace(field_id, new_id));
                }
                
                grandparent = $(parent).parent();
                field = $(field).detach()
                $(parent).remove();
                grandparent.append(field);
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(grandparent, 'tspinner', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
            }
            else if (field_component =='tcolor')
            {
                // realocate in dom
                $(field).attr('id', new_id);
                if (clear_fields) {
                    $(field).val('');
                }
                
                if (typeof $(field).attr('exitaction') !== 'undefined') {
                    $(field).attr('exitaction', $(field).attr('exitaction').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onblur') !== 'undefined') {
                    $(field).attr('onblur', $(field).attr('onblur').replace(field_id, new_id));
                }
                
                grandparent = $(parent).parent();
                grandparent.find('.pickr').replaceWith('<span class="input-group-addon tcolor"><i class="tcolor-icon"></i></span>');
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(grandparent, 'tcolor', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
            }
            else if (field_component =='thidden')
            {
                $(field).attr('id', new_id);
                if ($(field).attr('uniqid') == 'true') {
                    $(field).val(parseInt(Math.random() * 10000000000));
                }
                else if (clear_fields) {
                    $(field).val('');
                }
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(parent, 'thidden', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
            }
            else if (field_component =='tdbmultisearch' || field_component =='tdbuniquesearch')
            {
                $(field).attr('id', new_id);
                $(field).removeData('select2-id').removeAttr('data-select2-id').find('option').removeAttr('data-select2-id');
                $(row).removeData('select2-id').removeAttr('data-select2-id');
                
                // remove select2 container previously processed
                $(parent).find('.select2-container').remove();
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(parent, 'tdbmultisearch', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
                
                if (clear_fields) {
                    setTimeout(function() { $(field).val('').trigger('change.select2'); }, 10 );
                }
            }
            else if (field_component =='tmultisearch' || field_component =='tuniquesearch')
            {
                $(field).attr('id', new_id);
                $(field).removeData('select2-id').removeAttr('data-select2-id').find('option').removeAttr('data-select2-id');
                $(row).removeData('select2-id').removeAttr('data-select2-id');
                
                $(parent).find('.select2-container').remove();
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(parent, 'tmultisearch', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
                
                if (clear_fields) {
                    setTimeout(function() { $(field).val('').trigger('change.select2'); }, 10 );
                }
            }
            else if (field_component =='tcombo')
            {
                $(field).attr('id', new_id);
                
                if (clear_fields) {
                    $(field).val('');
                }
                if (field_role == 'tcombosearch') {
                    // clear data attributes
                    $(field).removeData('select2-id').removeAttr('data-select2-id').find('optgroup').removeAttr('data-select2-id');
                    $(field).removeData('select2-id').removeAttr('data-select2-id').find('option').removeAttr('data-select2-id');
                    $(field).removeData('select2-id').removeAttr('data-select2-id').find('optgroup').find('option').removeAttr('data-select2-id');
                    $(row).removeData('select2-id').removeAttr('data-select2-id');
                    $(parent).find('.select2-container').remove();
                }
                
                if (typeof $(field).attr('changeaction') !== 'undefined') {
                    $(field).attr('changeaction', $(field).attr('changeaction').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onchange') !== 'undefined') {
                    $(field).attr('onchange', $(field).attr('onchange').replace(field_id, new_id));
                }
                
                var re = new RegExp(field_id, 'g');
                tfieldlist_execute_scripts(parent, 'tcombo', function(script_content) {
                    script_content = script_content.replace(re, new_id);
                    return script_content;
                });
            }
            else if (field_component =='tfile')
            {
                $(field).attr('id', new_id);
                
                if (clear_fields) {
                    $(field).val('');
                }
                
                var parent = $(field).closest('td');
                var file_wrapper_id = parent.find('.div_file').attr('id');
                var new_file_wrapper_id = 'div_file_' + uniqid;
                
                parent.find('.tfile_row_wrapper').remove();
                parent.find('.file-response-icon').remove();
                parent.find('.div_file').attr('id', new_file_wrapper_id);
                $(field).css('padding-left', '5px');
                
                if (typeof $(field).attr('changeaction') !== 'undefined') {
                    $(field).attr('changeaction', $(field).attr('changeaction').replace(field_id, new_id));
                }
                if (typeof $(field).attr('onchange') !== 'undefined') {
                    $(field).attr('onchange', $(field).attr('onchange').replace(field_id, new_id));
                }
                
                var re  = new RegExp(field_id, 'g');
                var re2 = new RegExp(file_wrapper_id, 'g');
                tfieldlist_execute_scripts(parent, 'tfile', function(script_content) {
                    script_content = script_content.replace(re,  new_id);
                    script_content = script_content.replace(re2, new_file_wrapper_id);
                    return script_content;
                });
            }
        }
        
        // fora do if pois não troca ID (não possui ID), só reinicia value
        if (field_component =='tradiobutton')
        {
            $(field).parent().removeClass('active');
        }
        else if (field_component =='tcheckbutton')
        {
            $(field).parent().removeClass('active');
        }
    });
}

function tfieldlist_execute_scripts(container, filter, callback)
{
    var scripts = $(container).find('script');
    $.each(scripts, function(sindex, script)
    {
        var text = $(script).text();
        text = callback(text);
        if (text.trim().split('_')[0] == filter) {
            //$(script).text(text);
            //setTimeout(function() {new Function(text)(); }, 10 );
            $(script).replaceWith('<script>'+text+'</script>');
        }
    });
}

function tfieldlist_clear(name)
{
    ttable_clone_previous_row( $('[name='+name+'] tbody') );
    var rows = $('[name='+name+'] tbody').find('tr').length - 1;
    $('[name='+name+'] tbody').find('tr').each(function(i,e){
        if(i<rows) {
            ttable_remove_row(e);
        }
    });
    $('[name='+name+'] tfoot').find('input[type="text"]').each(function(i,e){
        tfieldlist_update_sum(name, $(e).attr('field_name'));
    });
}

function tfieldlist_add_rows(name, rows, timeout)
{
    timeout = timeout ? timeout : 50;

    for (n=1; n<=rows; n++)
    {
        setTimeout(function() {
            ttable_clone_previous_row( $('[name='+name+'] tbody') );
        }, timeout * n);
    }
}

function tfieldlist_clear_rows(name, start, length)
{
    var rows = $('[name='+name+'] tbody').find('tr').length;
    length = ( (length == 0) ? rows : length ) + start;
    if(length>=rows)
    {
        ttable_clone_previous_row( $('[name='+name+'] tbody').find('tr:last') );
        length = rows;
    }

    $('[name='+name+'] tbody').find('tr').each(function(i,e){
        if(i>=start && i<length){
            $(e).remove();
        }
    });
}

function tfieldlist_get_last_row_data(generator)
{
    var values = {};
    values.index = $(generator).closest('table').find('tbody tr:last').index();
    
    $(generator).closest('table').find('tbody tr:last').find('[name]').each(function(k,v) {
        var attribute_name  = $(v).attr('name');
        attribute_name = attribute_name.replace('[]', '');
        values[ attribute_name ] = $(v).val();
    });
    
    return values;
}

function tfieldlist_get_row_data(generator)
{
    var values = {};
    values.index = $(generator).closest('tr').index();
    
    values[ '_row_id' ] = $(generator).closest('tr').attr('id');
    
    $(generator).closest('tr').find('[name]').each(function(k,v) {
        var attribute_name  = $(v).attr('name');
        attribute_name = attribute_name.replace('[]', '');
        values[ attribute_name ] = $(v).val();
    });
    
    return values;
}

function tfieldlist_column_sum(field_name)
{
    var total = 0;
    $('[name="'+field_name+'[]"]').each(function (k,v) {
        var total_raw = tentry_get_data_by_id( $(v).attr('id') );
        if (!isNaN(parseFloat(total_raw))) {
            total += parseFloat(total_raw);
        }
    });
    return total;
}

function tfieldlist_update_sum(fieldlist_name, field_name, callback)
{
    setTimeout( function() {
        var total = tfieldlist_column_sum(field_name);
        var mask  = $('[name^='+field_name+']:first').data('nmask');
        var maskParts = [];
        
        if (mask) {
            maskParts = mask.split('');
        }
        
        $('[name=grandtotal_'+field_name+']').val( number_format( total, maskParts[0]??2, maskParts[1]??',', maskParts[2]??'.') );
        
        if (callback && typeof(callback) === "function")
        {
            callback();
        }
        
        var total_update_action = $('table[name='+fieldlist_name+']').attr('total-update-action');
        
        if (typeof total_update_action !== 'undefined' && total_update_action.length > 0)
        {
            var parent_form = $('table[name='+fieldlist_name+']').closest('form');
    		
    		if (parent_form) {
                var data = {};
                var final_results = {};
                
                var form_data = $(parent_form).serializeArray();
                
                $(form_data ).each(function(index, obj) {
                    var column_name = obj.name;
                    var column_name = column_name.replace('[]', '');
                    
                    if(data[column_name] === undefined) {
                        data[column_name] = [];
                    }
                    
                    data[column_name].push(obj.value);
                });
                
                for (prop in data) {
                    if (data[prop].length >= 1) {
                        for (var i = 0; i < data[prop].length; i++) {
                            if (typeof final_results[i] == 'undefined') {
                                final_results[i] = {};
                            }
                            final_results[i][prop] = data[prop][i]
                        }
                    }
                }
                
                data['list_data'] = final_results;
    		}
    		
    		__adianti_post_exec(total_update_action, data, null, '1', true);
        }
    }, 50);
}

function tfieldlist_enable_field(name) {
    try {
        if( $('table.tfieldlist[name="'+name+'"]').length > 0) {
            $('table.tfieldlist[name="'+name+'"]').parent().find('.tfieldlist-disable').remove();
        }
    }
    catch (e) {
        console.log(e);
    }
}

function tfieldlist_disable_field(name) {
    try {
        if( $('table.tfieldlist[name="'+name+'"]').length > 0) {
            $('table.tfieldlist[name="'+name+'"]').parent().prepend('<div class="tfieldlist-disable"></div>')
        }
    }
    catch (e) {
        console.log(e);
    }
}