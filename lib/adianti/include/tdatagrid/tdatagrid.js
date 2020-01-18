function tdatagrid_inlineedit( querystring )
{
    $(function() {
        $(".inlineediting").editInPlace({
            	callback: function(unused, enteredText)
            	{
            	    __adianti_load_page( $(this).attr("action") + querystring + '&key='+ $(this).attr("key")+'&'+$(this).attr("pkey")+ '='+$(this).attr("key")+"&field="+ $(this).attr("field")+"&value="+encodeURIComponent(enteredText));
            	    return enteredText;
            	},
            	show_buttons: false,
            	text_size:20,
            	params:column=name
        });
    });
}

function tdatagrid_add_serialized_row(datagrid, row)
{
    $('#'+datagrid+' > tbody:last-child').append(row);
}

function tdatagrid_enable_groups()
{
    $('[id^=tdatagrid_] tr[level]').not('[x=1]')
        .css("cursor","pointer")
        .attr("x","1")
        .click(function(){
            if (!$(this).data('child-visible')) {
                $(this).data('child-visible', false);
            }
            $(this).data('child-visible', !$(this).data('child-visible'));
            if ($(this).data('child-visible')) {
                    $(this).siblings('[childof="'+$(this).attr('level')+'"]').hide('fast');
                }
                else {
                    $(this).siblings('[childof="'+$(this).attr('level')+'"]').show('fast');
                }
        });
}