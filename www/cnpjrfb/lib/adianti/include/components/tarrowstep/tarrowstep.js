function tarrowstep_disable_field(name)
{
    $(`.arrow_steps_${name}`).addClass('disabled');
}

function tarrowstep_enable_field(name)
{
    $(`.arrow_steps_${name}`).removeClass('disabled');
}

function tarrowstep_clear(name)
{
    $(`.arrow_steps_${name} .step`).removeClass('current');
    $(`input[name='${name}']`).val('');
}

function tarrowstep_set_current(name, value)
{
    $(`.arrow_steps_${name} .step`).removeClass('current');

    $(`input[name='${name}']`).val(value);

    var selected = true;

    $(`.arrow_steps_${name} .step`).each(function(index, el){
        
        if (selected)

        {
            $(el).addClass('current');
        }
        
        if ($(el).data('key') == value) {
            selected = false;
        }
    });
}

function tarrowstep_start(name)
{
    $(`.arrow_steps_${name} .step`).on('click', function(){
        tarrowstep_set_current(name, $(this).data('key'));
    });

    $(`.arrow_steps_${name} .step`).on('mouseenter', function(){
        var value = $(this).data('key');
        var selected = true;

        $(`.arrow_steps_${name} .step`).each(function(index, el){
            
            if (selected)
    
            {
                $(el).addClass('preview-current');
            }
            
            if ($(el).data('key') == value) {
                selected = false;
            }
        });

    });

    $(`.arrow_steps_${name} .step`).on('mouseleave', function(){
        $(`.arrow_steps_${name} .step`).removeClass('preview-current');
    });
}