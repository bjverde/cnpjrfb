function tmulticombo_enable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field+'[]');
    $(function () {
        try {
            $(selector).multiselect( 'disable', false );
        } catch (e) {
            console.log(e);
        }
    });
} 

function tmulticombo_disable_field(form_name, field) {
    let selector = tfield_get_selector(form_name, field+'[]');
    $(function () {
        try {
            $(selector).multiselect( 'disable', true );
        } catch (e) {
            console.log(e);
        }
    });
}

function tmulticombo_start(element_id, size, labels)
{
    $(function () {
        $('#'+element_id).multiselect({
            columns: 1,
            search: true,
            texts: labels,
            selectAll: true,
            maxHeight: 400,
            onControlOpen: function() {
                setTimeout( function() {
                    $('#'+element_id).parent().find('.ms-options input[type="text"]')[0].focus();
                }, 1);
            },
            optionAttributes: ['disabled']
        });
        $('#'+element_id).data('size', size);
        $('#'+element_id).next('.ms-options-wrap').width(size);
    });
}

function tmulticombo_reload(form_name, field)
{
    if(typeof form_name != 'undefined' && form_name != '') {
        form_name_sel = 'form[name="'+form_name+'"] ';
    }
    else {
        form_name_sel = '';
    }

    var selector = form_name_sel + '[name="'+field+'[]"]';
    
    // reload
    $(selector).multiselect('reload');
    
    // resize because reload lost the size
    $(selector).next('.ms-options-wrap').width($(selector).data('size'));
}
