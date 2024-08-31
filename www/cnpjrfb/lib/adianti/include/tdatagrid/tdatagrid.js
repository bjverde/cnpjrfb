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

function tdatagrid_update_total(datagrid_id)
{
	var datagrid_name = datagrid_id.substring(1);
	var target = document.querySelector( datagrid_id );
	
	var observer = new MutationObserver(function(mutations) {
	  mutations.forEach(function(mutation) {
		
		if (mutation.target.tagName == 'TBODY') {
			var total_fields = $(datagrid_id).find('[data-total-function=sum]');
			
			total_fields.each(function(k,v) {
				var total = 0;
				
				var column_name = $(v).data("column-name");
				var total_mask  = $(v).data("total-mask");
				var parts = total_mask.split(':');
				if (parts.length>0)
				{
				    var prefix = parts[0];
				    var nmask  = parts[1];
				}
				else
				{
				    var prefix = '';
				    var nmask  = total_mask.substring(1);
				}
				
				$('[name="'+datagrid_name+'_'+column_name+'[]"]').each(function(k,v) {
					total += parseFloat( $(v).val() );
				});
				
				$(v).html( prefix + ' ' + number_format(total, nmask.substring(0,1), nmask.substring(1,2), nmask.substring(2,3) ) );
				$(v).data('value', total);
				$(v).attr('data-value', total);
			});
		}
	  });
	});
	 
	// configuração do observador:
	var config = { attributes: true, childList: true, characterData: true, subtree: true, attributeOldValue: true, characterDataOldValue: true };
	 
	// passar o nó alvo, bem como as opções de observação
	observer.observe(target, config);	
}


function tdatagrid_mutation_action(datagrid_id, mutation_url)
{
	var datagrid_name = datagrid_id.substring(1);
	var target = document.querySelector( datagrid_id );
	
	var observer = new MutationObserver(function(mutations) {
      var results = {};
      var final_results = {};
	  mutations.forEach(function(mutation) {
		
		if (mutation.target.tagName == 'TBODY') {
			var hidden_fields = $(datagrid_id).find('[data-hidden-field=true]');
			
			hidden_fields.each(function(k,v) {
				var column_name = $(v).attr('name').replace(datagrid_name+'_', '');
				var column_name = column_name.replace('[]', '');
				
				if (column_name.substring(0,1) !== '=') {
    				if (typeof results[column_name] == 'undefined')
    				{
    				    results[column_name] = [];
    				}
    				results[column_name].push( $(v).val() );
    	        }
			});
			
    		for (prop in results)
    		{
    		    for (var i = 0; i < results[prop].length; i++) {
    		        if (typeof final_results[i] == 'undefined')
    		        {
    		            final_results[i] = {};
    		        }
    		        final_results[i][prop] = results[prop][i]
    		    }
    
    		}
    		var post_data = {};
    		
    		var parent_form = $(datagrid_id).closest('form');
    		
    		if (parent_form) {
                var form_data = $(parent_form).serializeArray();
                
                $(form_data ).each(function(index, obj){
                    var column_name = obj.name.replace('[]', '');
                    post_data[column_name] = obj.value;
                });
    		}
    		
    		post_data['list_data'] = final_results;
    		__adianti_post_exec(mutation_url, post_data, null, '1', true);
         }
	  });
	});
	 
	// configuração do observador:
	var config = { attributes: true, childList: true, characterData: true, subtree: true, attributeOldValue: true, characterDataOldValue: true };
	 
	// passar o nó alvo, bem como as opções de observação
	observer.observe(target, config);	
}