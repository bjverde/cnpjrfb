function tdbmultisearch_start( id, custom_options_json, callback ) {
    
    var custom_options = JSON.parse( custom_options_json );
    
    var options = {
        minimumInputLength: custom_options['minlen'],
        maximumSelectionLength: custom_options['maxsize'],
        allowClear: true,
        selectionTitleAttribute: custom_options['with_titles'],
        allowClear: custom_options['allowclear'],
        placeholder: custom_options['placeholder'],
        multiple: custom_options['multiple'],
        hash: custom_options['hash'],
        id: function(e) { return e.id+"::"+e.text; },
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
        },
        ajax: {
            url: custom_options['url'],
            dataType: 'json',
            delay: 250,
            
            // prepare query params before send to server
            data: function(value, page) {
                return {
                    value: value.term,
                    hash: custom_options['hash']
                };
            },
            
            // process results received from server
            processResults: function(data, page ) 
            {
                var aa = [];
                $(data.result).each(function(i) {
                    var item = this.split('::');
                    aa.push({
                        id: item[0],
                        text: item[1]
                    });
                });               

                return {                             
                    results: aa 
                }
            }
        },             
    };
    
    if (typeof custom_options['dropdownParent'] !== 'undefined') {
        options['dropdownParent'] = custom_options['dropdownParent'];
    }
    
    if (custom_options['multiple'] !== '1')
    {
        delete options.maximumSelectionLength;
    }
    
    // the second part is to prevent results keep showing even when
    // change action change the current page
    $('#'+id).select2( options ).on('select2:unselecting', function() {
        $(this).data('unselecting', true);
    }).on('select2:opening', function(e) {
        if ($(this).data('unselecting')) {
            $(this).removeData('unselecting');
            e.preventDefault();
        }
    });
    
    if (typeof callback != 'undefined')
    {
        $('#'+id).on("change", function (e) {
            callback();
        });
    }
    
    if (parseInt(custom_options['maxsize']) !== 1)
    {
        $('#'+id).parent().find('.select2-selection').height(custom_options['height']);
        $('#'+id).parent().find('.select2-selection').find('.select2-selection__rendered').height(custom_options['height']);
        $('#'+id).parent().find('.select2-selection').find('.select2-selection__rendered').css('overflow-y', 'auto');
    }
    
    $('#'+id).data('select2-options', options);
    $('#'+id).addClass('byselect2');
}

function tdbmultisearch_set_value( form_name, field, value, callback )
{
    setTimeout(function() {
        if (field.substr(0,1) == '#')
        {
            var select = $(field);
        }
        else
        {
            var select = $('form[name='+form_name+'] [name="'+field+'[]"]');
        }

        var hash = select.data('select2').options.options.hash;
        
        if (Array.isArray(value))
        {
            var url = select.data('select2').options.options.ajax.url + "&hash=" + hash + "&values=" + JSON.stringify(value) + '&operator_idsearch=in&onlyidsearch=1&jsonvalue=1';
        }
        else {
            var url = select.data('select2').options.options.ajax.url + "&hash=" + hash + "&value=" + value + '&operator_idsearch=in&onlyidsearch=1&jsonvalue=1';
        }
        
        $.ajax({
          url: url,
          dataType: "json",
          }).done(function( data ) {
              if (Array.isArray(data.result)) {
                  if (data.result.length > 0) {
                    var results = [];

                    for (const result of data.result) {
                        var item = result.split('::');
                        results.push(item[0]);

                        if (!select.find("option[value='" + item[0] + "']").length) {
                            select.append(new Option(item[1], item[0], true, true));
                        }
                    }

                    if (value == '' || value == '[]')
                    {
                        select.val('').trigger('change.select2');
                    }
                    else
                    {
                        select.val(results).trigger('change.select2');
                    }

                    if (typeof callback == 'function')
                    {
                        callback();
                    }
                  }
                  else {
                      select.val('').trigger('change.select2');
                  }
              }
              else {
                  select.val('').trigger('change.select2');
              }
          }).fail(function(jqxhr, textStatus, exception) {
             __adianti_error('Error', textStatus + ': ' + 'connection failed');
          });
      }, 1);
}