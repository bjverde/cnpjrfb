function tdbuniquesearch_set_value( form_name, field, value, callback )
{
    setTimeout(function() {
        if (field.substr(0,1) == '#')
        {
            var select = $(field);
        }
        else
        {
            var select = $('form[name='+form_name+'] [name='+field+']');
        }
        
        var hash   = select.data('select2').options.options.hash;
        var url    = select.data('select2').options.options.ajax.url + "&hash=" + hash + "&value=" + value + "&onlyidsearch=1";
        
        $.ajax({
          url: url,
          dataType: "json",
          }).done(function( data ) {
              if (Array.isArray(data.result)) {
                  if (data.result.length > 0) {
                      var result = data.result[0];
                      var item   = result.split('::');
                      
                      if (!select.find("option[value='" + item[0] + "']").length) {
                          select.append(new Option(item[1], item[0], true, true));
                      }
                      
                      if (value == '')
                      {
                          select.val('').trigger('change.select2');
                      }
                      else
                      {
                          var data = [];
                          data.push(item[0]);
                          select.val(data).trigger('change.select2');
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