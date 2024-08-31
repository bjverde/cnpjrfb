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
            var multifile_field  = [];
        }
        else
        {
            var single_field    = $("form[name="+form_name+"] [name='"+field+"']");
            var array_field     = $("form[name="+form_name+"] [name='"+field+"[]']");
            var multifile_field = $("form[name="+form_name+"] [widget='tmultifile'][name='file_"+field+"[]']");
        }
        
        if (single_field.length || array_field.length || multifile_field) {
            if (typeof Adianti.formEventsCounter == 'undefined' || Number.isNaN(Adianti.formEventsCounter)) {
                Adianti.formEventsCounter = 0;
            }
            
            if (Adianti.formEventsCounter == 0 ) {
                if(multifile_field.length) {
                    try {
                        multifile_field[0].tmultifile.send_data(JSON.parse(value));
                    } 
                    catch (e) {
                        multifile_field[0].tmultifile.setValue(value);
                    }
                }
                else if (single_field.length) {
                    
                    if (single_field.attr('widget') == 'thtmleditor') {
                        single_field.summernote( "code", value );
                    }
                    else if (single_field.attr('widget') == 'tcolor') {
                        //single_field.colorpicker('setValue', value);
                        if (value) {
                            single_field.data('picker').setColor(value);
                        }
                        else {
                            single_field.data('picker').setColor(null);
                        }
                    }
                    else if (single_field.attr('widget') == 'tarrowstep') {
                        if (value) {
                            tarrowstep_set_current(single_field[0].name, value);
                        } else {
                            tarrowstep_clear(single_field[0].name);
                        }
                    }
                    else if (single_field.attr('widget') == 'tdate') {
                        tdate_set_value('#'+single_field.attr('id'), value);
                    }
                    else if (single_field.attr('widget') == 'timagecropper') {
                        single_field[0].timagecropper.setValue(value);
                    }
                    else if (single_field.attr('widget') == 'tfile') {
                        single_field[0].tfile.setValue(value);
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
                        var tdbunique_fire_events = fire_events;
                        tdbuniquesearch_set_value(form_name, field, value, function() {
                            // fire events must be after fetch elements from service
                            if (tdbunique_fire_events) {
                                if (single_field.attr('exitaction')) {
                                    tform_events_hang_exec( single_field.attr('exitaction') );
                                }
                                if (single_field.attr('changeaction')) {
                                    tform_events_hang_exec( single_field.attr('changeaction') );
                                }
                            }
                        });
                        // fire events is called within callback
                        fire_events = false;
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
                        single_field.prop('checked', false);
                        if (value) {
                            var radio_input = single_field.filter('[value="'+value+'"]').prop('checked', true);
                            if (radio_input.parent().prop('tagName') == 'LABEL') {
                                radio_input.parent().parent().find('label').removeClass('active');
                                radio_input.parent().toggleClass('active');
                            }
                        } else {
                            if (single_field.parent().prop('tagName') == 'LABEL') {
                                single_field.parent().parent().find('label').removeClass('active');
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
                            var checkeds = JSON.parse(value);
                            $.each(checkeds, function(key, checkvalue) {
                                var check_input = array_field.filter('[value="'+checkvalue+'"]').prop('checked', true);
                                if (check_input.parent().prop('tagName') == 'LABEL') {
                                    check_input.parent().toggleClass('active');
                                }
                            });
                        } else {
                            array_field.parent().parent().find('label').removeClass('active');
                        }
                    }
                    else if (array_field.attr('component') == 'multientry') {
                        if (value) {
                            array_field.empty();
                            var values = JSON.parse(value);
                            $.each(values, function(key, value) {
                                array_field.append($("<option/>").val(value).text(value));
                            });
                            array_field.val(values).trigger("change.select2");
                        }
                    }
                    else if (array_field.attr('component') == 'multisearch' && array_field.attr('widget') !== 'tuniquesearch') {
                        if (value) {
                            var values = JSON.parse(value);
                            
                            var select2_template = function (d) {
                                if (/<[a-z][\s\S]*>/i.test(d.text)) {
                                    return $("<span>"+d.text+"</span>");
                                }
                                else {
                                    return d.text;
                                }
                            };
                            
                            // fi. sendData with multiple values for just one multisearch ['a','c']
                            if (array_field.attr('widget') == 'tmultisearch')
                            {
                                array_field.select2({templateResult: select2_template, templateSelection: select2_template}).val(values).trigger('change.select2');
                            }
                            else if (array_field.attr('widget') == 'tdbmultisearch')
                            {
                                var tdbmulti_fire_events = fire_events;
                                tdbmultisearch_set_value( form_name, field, value, function() {
                                    // fire events must be after fetch elements from service
                                    if (tdbmulti_fire_events) {
                                        if (array_field.attr('exitaction')) {
                                            tform_events_hang_exec( array_field.attr('exitaction') );
                                        }
                                        if (array_field.attr('changeaction')) {
                                            tform_events_hang_exec( array_field.attr('changeaction') );
                                        }
                                    }
                                });

                                fire_events = false
                            }
                            else // fi. sendData with multiple values for many uniques (inside fieldlist) ['a','c']
                            {
                                $.each(values, function(key, each_value) {
                                    var field_id = $($(array_field)[key]).attr('id');
                                    tform_send_data(form_name, '#'+field_id, each_value, fire_events);
                                } );
                                
                                // cancel fire events because it will be fired one by one inside the previous loop
                                fire_events = false;
                            }
                        }
                    }
                    else if (array_field.length) {
                        if (value) {
                            var values = JSON.parse(value);
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
                                
                                // cancel fire events because it will be fired one by one inside the previous loop
                                fire_events = false;
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

function tform_send_data_by_id(form_name, field, value, fire_events, timeout)
{
    if ($('form[name='+form_name+'] [id='+field+']').length > 0)
    {
        tform_send_data(form_name, '#'+field, value, fire_events, timeout)
    }   
}

function tform_events_hang_exec( string_callback )
{
    if (typeof Adianti.formEventsCounter == 'undefined' || Number.isNaN(Adianti.formEventsCounter)) {
        Adianti.formEventsCounter = 0;
    }
    
    Adianti.formEventsCounter ++;
    string_callback=string_callback.replace("'callback'", 'tform_decrease_events_counter');
    Function(string_callback)();
}

function tform_events_stop( callback )
{
    Adianti.formEventsStop = callback;
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
    if (typeof Adianti.formEventsCounter == 'undefined' || Number.isNaN(Adianti.formEventsCounter)) {
        Adianti.formEventsCounter = 0;
    }
    
    if (Adianti.formEventsCounter == 0 && Adianti.formEventsQueue.length > 0)
    {
        next = Adianti.formEventsQueue.shift();
        next();
    }
    
    if (Adianti.formEventsQueue.length > 0)
    {
        setTimeout( tform_process_events_queue, 100 );
    }
    else
    {
        if (typeof Adianti.formEventsStop == 'function')
        {
            Adianti.formEventsStop();
            Adianti.formEventsStop = null;
        }
    }
}

function tform_decrease_events_counter()
{
    if (typeof Adianti.formEventsCounter == 'undefined' || Number.isNaN(Adianti.formEventsCounter)) {
        Adianti.formEventsCounter = 0;
    }
    
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
