function tselect_enable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field+'[]');
    
    try {
        $(selector).css('pointer-events', 'auto');
        $(selector).css('user-select', 'auto');
        $(selector).removeClass('tselect_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tselect_disable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field+'[]');
    
    try {
        $(selector).attr('tabindex', '-1');
        $(selector).css('pointer-events', 'none');
        $(selector).css('user-select', 'none');
        $(selector).addClass('tselect_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tselect_clear_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name="'+field+'[]"]').val('');
    } catch (e) {
        console.log(e);
    }
}

function tselect_create_opt_group(form_name, field, label)
{
    if(typeof form_name != 'undefined' && form_name != '') {
        form_name = 'form[name="'+form_name+'"] ';
    }
    else
    {
        form_name = '';
    }

    var selector = '[name="'+field+'[]"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field
    }

    $('<optgroup label="'+label+'"></optgroup>').appendTo(form_name + selector);
}


function tselect_add_option(form_name, field, key, value) {
    var key = key.replace(/"/g, '');

    if(typeof form_name != 'undefined' && form_name != '') {
        form_name = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name = '';
    }

    var selector = 'select[name="'+field+'[]"]';

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

function tselect_clear(form_name, field, fire_events)
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

    var selector = '[name="'+field+'[]"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field
    }

    var field = $(form_name + selector);

    field.val(false);
    field.html('');

    if (fire_events) {
        if (field.attr('changeaction')) {
            tform_events_hang_exec( field.attr('changeaction') );
        }
    }
}

function tselect_enable_search(field, placeholder)
{
    $(field).removeAttr('onchange');
    
    $(field).select2({
        allowClear: true,
        multiple: true,
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
