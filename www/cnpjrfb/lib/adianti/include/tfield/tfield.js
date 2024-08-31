function tfield_enable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name='+field+']').attr('readonly', false);
        $('form[name='+form_name+'] [name='+field+']').removeClass('tfield_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tfield_disable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name='+field+']').attr('readonly', true);
        $('form[name='+form_name+'] [name='+field+']').addClass('tfield_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tfield_clear_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name='+field+']').val('');
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