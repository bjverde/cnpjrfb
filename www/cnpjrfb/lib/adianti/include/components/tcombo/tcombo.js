function tcombo_enable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field);
    
    try {
        if ($(selector).attr('role') == 'tcombosearch') {
            tmultisearch_enable_field(form_name, field);
        }
        else {
            $(selector).attr('onclick', null);
            $(selector).removeAttr('tabindex');
            $(selector).css('pointer-events',   'auto');
            $(selector).removeClass('tcombo_disabled');
        }
    } catch (e) {
        console.log(e);
    }
}

function tcombo_disable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field);
    
    try {
        if ($(selector).attr('role') == 'tcombosearch') {
            setTimeout( function(){
                tmultisearch_disable_field(form_name, field);
            });
        }
        else {
            $(selector).attr('onclick', 'return false');
            $(selector).attr('tabindex', '-1');
            $(selector).css('pointer-events', 'none');
            $(selector).addClass('tcombo_disabled');
        }
    } catch (e) {
        console.log(e);
    }
}

function tcombo_add_option(form_name, field, key, value)
{
    var key = key.replace(/"/g, '');
    
    let selector = tfield_get_selector(form_name, field);
    
    var optgroups =  $(selector).find('optgroup');
    
    if( optgroups.length > 0 ) {
        $('<option value="'+key+'">'+value+'</option>').appendTo(optgroups.last());
    }
    else {
        $('<option value="'+key+'">'+value+'</option>').appendTo(selector);
    }
    
}

function tcombo_create_opt_group(form_name, field, label)
{
    let selector = tfield_get_selector(form_name, field);
    
    $('<optgroup label="'+label+'"></optgroup>').appendTo(selector);
}

function tcombo_clear(form_name, field, fire_events)
{
    let selector = tfield_get_selector(form_name, field);
    
    if (typeof fire_events == 'undefined') {
        fire_events = true;
    }
    
    var field = $(selector);
    
    if (field.attr('role') == 'tcombosearch') {
        if (field.find('option:not(:disabled)').length>0) {
            // scoped version of change to avoid indirectly fire events
            field.val('').empty().trigger('change.select2');
        }
    }
    else {
        field.val(false);
        field.html('');
    }
    
    if (fire_events) { 
        if (field.attr('changeaction')) {
            tform_events_hang_exec( field.attr('changeaction') );
        }
    }
}

function tcombo_enable_search(field, placeholder)
{
    $(field).removeAttr('onchange');
    
    var options = {
        allowClear: true,
        placeholder: placeholder,
        templateResult: function (d) {
            if (/<[a-z][\s\S]*>/i.test(d.text)) {
                return $("<span>"+d.text+"</span>");
            }
            else {
                return d.text;
            }
        },
        templateSelection: function (d) {
            if (/<[a-z][\s\S]*>/i.test(d.text)) {
                return $("<span>"+d.text+"</span>");
            }
            else {
                return d.text;
            }
        }
    };
    
    $(field).select2(options).on('change', function (e) {
        new Function( $( field ).attr('changeaction'))();
    });
    
    $(field).data('select2-options', options);
    $(field).addClass('byselect2');
}