function tform_send_data(form_name, field, value, fire_events, timeout)
{
    if (parseInt(timeout) > 0)
    {
        setTimeout( function() {
            tform_send_data(form_name, field, value, fire_events, 0);
        }, timeout);
        return;
    }

    try {
        if (field.substr(0,1) == '#')
        {
            var single_field = $(field);
            var array_field  = [];
        }
        else
        {
            var single_field = $("form[name="+form_name+"] [name='"+field+"']");
            var array_field  = $("form[name="+form_name+"] [name='"+field+"[]']");
        }
        
        if (single_field.length || array_field.length) {
            if (typeof Adianti.formEventsCounter == 'undefined' || Number.isNaN(Adianti.formEventsCounter)) {
                Adianti.formEventsCounter = 0;
            }
            
            if (Adianti.formEventsCounter == 0 ) {
                if (single_field.length) {
                    if (single_field.attr('widget') == 'thtmleditor') {
                        single_field.summernote( "code", value );
                    }
                    if (single_field.attr('widget') == 'tcolor') {
                        single_field.colorpicker('setValue', value);
                    }
                    else if (single_field.attr('role') == 'tcombosearch') {
                        // only when is different to avoid extra asynchronous event calls on hierarchical combos
                        if (single_field.val() !== value) {
                            var select2_template = function (d) {
                                if (/<[a-z][\s\S]*>/i.test(d.text)) {
                                    return $("<span>"+d.text+"</span>");
                                }
                                else {
                                    return d.text;
                                }
                            };
                            
                            single_field.select2({templateResult: select2_template, templateSelection: select2_template}).val(value).trigger('change.select2');
                        }
                    }
                    else if (single_field.attr('widget') == 'tdbuniquesearch') {
                        tdbuniquesearch_set_value(form_name, field, value);
                    }
                    else if (single_field.attr('component') == 'multisearch') {
                        if (value) {
                        
                            var select2_template = function (d) {
                                if (/<[a-z][\s\S]*>/i.test(d.text)) {
                                    return $("<span>"+d.text+"</span>");
                                }
                                else {
                                    return d.text;
                                }
                            };
                            
                            single_field.select2({templateResult: select2_template, templateSelection: select2_template}).val(value).trigger('change.select2');
                        }
                    }
                    // checkbox is tested here when used alone inside a form (no checkgroup)
                    else if (single_field.attr('type') == 'radio' || single_field.attr('type') == 'checkbox') {
                        if (value) {
                            var radio_input = single_field.filter('[value='+value+']').prop('checked', true);
                            if (radio_input.parent().prop('tagName') == 'LABEL') {
                                radio_input.parent().parent().find('label').removeClass('active');
                                radio_input.parent().toggleClass('active');
                            }
                        }
                    }
                    else {
                        single_field.val( value );
                    }
                    
                    if (fire_events) { 
                        if (single_field.attr('exitaction')) {
                            tform_events_hang_exec( single_field.attr('exitaction') );
                        }
                        if (single_field.attr('changeaction')) {
                            tform_events_hang_exec( single_field.attr('changeaction') );
                        }
                    }
                }
                else if (array_field.length)
                {
                    if (array_field.attr('type') == 'checkbox') {
                        array_field.prop('checked', false);
                        
                        if (value) {
                            array_field.parent().parent().find('label').removeClass('active');
                            var checkeds = value.split('|');
                            $.each(checkeds, function(key, checkvalue) {
                                var check_input = array_field.filter('[value='+checkvalue+']').prop('checked', true);
                                if (check_input.parent().prop('tagName') == 'LABEL') {
                                    check_input.parent().toggleClass('active');
                                }
                            });
                        }
                    }
                    else if (array_field.attr('component') == 'multientry') {
                        if (value) {
                            array_field.empty();
                            var values = value.split('|');
                            $.each(values, function(key, value) {
                                array_field.append($("<option/>").val(value).text(value));
                            });
                            array_field.val(values).trigger("change.select2");
                        }
                    }
                    else if (array_field.attr('component') == 'multisearch' && array_field.attr('widget') !== 'tuniquesearch') {
                        if (value) {
                            var values = value.split('|');
                            
                            var select2_template = function (d) {
                                if (/<[a-z][\s\S]*>/i.test(d.text)) {
                                    return $("<span>"+d.text+"</span>");
                                }
                                else {
                                    return d.text;
                                }
                            };
                            
                            array_field.select2({templateResult: select2_template, templateSelection: select2_template}).val(values).trigger('change.select2');
                        }
                    }
                    else if (array_field.length) {
                        if (value) {
                            var values = value.split('|');
                            $(array_field).find("option").prop('selected', false);
                            if (array_field.attr('widget') == 'tselect' && array_field.length == 1) // single select[]
                            {
                                $.each(values, function(key, checkvalue) {
                                    $(array_field).find("option").filter('[value="'+checkvalue+'"]').prop('selected', true);
                                } );
                            }
                            else // array of inputs (tfieldlist)
                            {
                                $.each(values, function(key, each_value) {
                                    var field_id = $($(array_field)[key]).attr('id');
                                    tform_send_data(form_name, '#'+field_id, each_value, fire_events);
                                } );
                            }
                        }
                    }
                    
                    if (fire_events) { 
                        if (array_field.attr('exitaction')) {
                            tform_events_hang_exec( array_field.attr('exitaction') );
                        }
                        if (array_field.attr('changeaction')) {
                            tform_events_hang_exec( array_field.attr('changeaction') );
                        }
                    }
                }
            }
            else {
                tform_events_queue_push( function(){
                    tform_send_data(form_name, field, value, fire_events);
                });
            }
        }
    } catch (e) {
        console.log(e);
    }
}

function tform_send_data_by_id(form_name, field, value, fire_events, timeout) {
    if (parseInt(timeout) > 0)
    {
        setTimeout( function() {
            tform_send_data_by_id(form_name, field, value, fire_events, 0);
        }, timeout);
        return;
    }

    try{
        if ($('form[name='+form_name+'] [id='+field+']').length) {
            if (typeof Adianti.formEventsCounter == 'undefined') {
                Adianti.formEventsCounter = 0;
            }
            
            if (Adianti.formEventsCounter == 0 ) {
                $('form[name='+form_name+'] [id='+field+']').val( value );
                if (fire_events) { 
                    if ($('form[name='+form_name+'] [id='+field+']').attr('exitaction')) {
                        tform_events_hang_exec( $('form[name='+form_name+'] [id='+field+']').attr('exitaction') );
                    }
                    if ($('form[name='+form_name+'] [id='+field+']').attr('changeaction')) {
                        tform_events_hang_exec( $('form[name='+form_name+'] [id='+field+']').attr('changeaction') );
                    }
                }
            }
            else {
                tform_events_queue_push( function(){
                    tform_send_data_by_id(form_name, field, value, fire_events);
                });
            }
        }
    } catch (e) {
        console.log(e);
    }
}

function tform_events_hang_exec( string_callback )
{
    Adianti.formEventsCounter ++;
    string_callback=string_callback.replace("'callback'", 'tform_decrease_events_counter');
    Function(string_callback)();
}

function tform_events_queue_push( callback )
{
    if (typeof Adianti.formEventsQueue == 'undefined')
    {
        Adianti.formEventsQueue = new Array;
    }
    Adianti.formEventsQueue.push( callback );
    setTimeout( tform_process_events_queue, 100 );
}

function tform_process_events_queue()
{
    if (Adianti.formEventsCounter == 0 && Adianti.formEventsQueue.length > 0)
    {
        next = Adianti.formEventsQueue.shift();
        next();
    }
    
    if (Adianti.formEventsQueue.length > 0)
    {
        setTimeout( tform_process_events_queue, 100 );
    }
}

function tform_decrease_events_counter()
{
    Adianti.formEventsCounter --;
}

function tform_send_data_aggregate(form_name, field, value, fire_events) {
    try {
        if ($('form[name='+form_name+'] [name='+field+']').val() == '')
        {
            $('form[name='+form_name+'] [name='+field+']').val( value );
        }
        else
        {
            current_value = $('form[name='+form_name+'] [name='+field+']').val();
            $('form[name='+form_name+'] [name='+field+']').val( current_value + ', '+ value );
        }
    } catch (e) {
        console.log(e);
    }
}

function tform_fire_field_actions(form_name, field) { 
    if ($('form[name='+form_name+'] [name='+field+']').attr('exitaction')) {
        tform_events_hang_exec( $('form[name='+form_name+'] [name='+field+']').attr('exitaction') );
    }
    if ($('form[name='+form_name+'] [name='+field+']').attr('changeaction')) {
        tform_events_hang_exec( $('form[name='+form_name+'] [name='+field+']').attr('changeaction') );
    }
}

function tform_hide_field(form, field, speed) {
    if (typeof speed == 'undefined') {
        $('#'+form+' [name="'+field+'"]').closest('.tformrow').hide('fast');
    }
    else
    {
        $('#'+form+' [name="'+field+'"]').closest('.tformrow').hide(speed);
    }
}

function tform_show_field(form, field, speed) {
    if (typeof speed == 'undefined') {
        $('#'+form+' [name="'+field+'"]').closest('.tformrow').show('fast');
    }
    else
    {
        $('#'+form+' [name="'+field+'"]').closest('.tformrow').show(speed);
    }
}
