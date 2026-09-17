function tfield_get_selector(form_name, field) {
    if (typeof form_name != 'undefined' && form_name != '') {
        form_name_sel = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name_sel = '';
    }
    
    var selector = '[name="'+field+'"]';
    if (field.indexOf('[') == -1 && $('#'+field).length >0) {
        var selector = '#'+field;
    }
    
    return form_name_sel + selector;
}

function tfield_enable_field(form_name, field) {
    try {
        let selector = tfield_get_selector(form_name, field);
        $(selector).attr('readonly', false);
        $(selector).removeAttr('tabindex');
        $(selector).removeClass('tfield_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tfield_disable_field(form_name, field) {
    try {
        let selector = tfield_get_selector(form_name, field);
        $(selector).attr('readonly', true);
        $(selector).attr('tabindex', "-1");
        $(selector).addClass('tfield_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tfield_clear_field(form_name, field) {
    try {
        let selector = tfield_get_selector(form_name, field);
        $(selector).val('');
    } catch (e) {
        console.log(e);
    }
}

function tfield_transfer_value(source, target, delimiter) {
    if ($(source).attr('type') == 'checkbox')
    {
        var value = $(source).attr('value');
        var current_val = $(source).closest(delimiter).find(target).val();
        
        if ($(source).is(':checked'))
        {
            current_val = current_val + value + ',';
        }
        else
        {
            current_val = current_val.replace(value+',', '');
        }
        $(source).closest(delimiter).find(target).val(current_val);
    }
    else
    {
        var current_val = $(source).val();
        $(source).closest(delimiter).find(target).val(current_val);
    }
}