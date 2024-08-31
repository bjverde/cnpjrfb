function tcombo_enable_field(form_name, field) {
    
    if(typeof form_name != 'undefined' && form_name != '') {
        form_name_sel = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name_sel = '';
    }

    var selector = '[name="'+field+'"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field;
    }
    
    try {
        if ($(form_name_sel + selector).attr('role') == 'tcombosearch') {
            tmultisearch_enable_field(form_name, field);
        }
        else {
            $(form_name_sel + selector).attr('onclick', null);
            $(form_name_sel + selector).css('pointer-events',   'auto');
            $(form_name_sel + selector).removeClass('tcombo_disabled');
        }
    } catch (e) {
        console.log(e);
    }
}

function tcombo_disable_field(form_name, field) {
    
    if(typeof form_name != 'undefined' && form_name != '') {
        form_name_sel = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name_sel = '';
    }

    var selector = '[name="'+field+'"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field
    }
    
    try {
        if ($(form_name_sel + selector).attr('role') == 'tcombosearch') {
            tmultisearch_disable_field(form_name, field);
        }
        else {
            $(form_name_sel + selector).attr('onclick', 'return false');
            $(form_name_sel + selector).attr('tabindex', '-1');
            $(form_name_sel + selector).css('pointer-events', 'none');
            $(form_name_sel + selector).addClass('tcombo_disabled');
        }
    } catch (e) {
        console.log(e);
    }
}

function tcombo_add_option(form_name, field, key, value)
{
    var key = key.replace(/"/g, '');
    
    if(typeof form_name != 'undefined' && form_name != '') {
        form_name = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name = '';
    }

    var selector = 'select[name="'+field+'"]';
    
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field
    }
    
    var optgroups =  $(form_name + selector).find('optgroup');
    
    if( optgroups.length > 0 ) {
        $('<option value="'+key+'">'+value+'</option>').appendTo(optgroups.last());
    }
    else {
        $('<option value="'+key+'">'+value+'</option>').appendTo(form_name + selector);
    }
    
}

function tcombo_create_opt_group(form_name, field, label)
{
    if(typeof form_name != 'undefined' && form_name != '') {
        form_name = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name = '';
    }
    
    var selector = '[name="'+field+'"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field
    }
    
    $('<optgroup label="'+label+'"></optgroup>').appendTo(form_name + selector);
}

function tcombo_clear(form_name, field, fire_events)
{
    if (typeof fire_events == 'undefined') {
        fire_events = true;
    }
    
    if (typeof form_name != 'undefined' && form_name != '') {
        var form_name = 'form[name="'+form_name+'"] ';
    }
    else {
        var form_name = '';
    }
    
    var selector = '[name="'+field+'"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field
    }
    
    var field = $(form_name + selector);
    
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
    
    $(field).select2({
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
    }).on('change', function (e) {
        new Function( $( field ).attr('changeaction'))();
    });
}