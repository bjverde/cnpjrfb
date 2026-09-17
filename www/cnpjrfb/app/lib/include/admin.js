function __adianti_input_fuse_search(input_search, attribute, selector)
{
    setTimeout(function() {
        var stack_search = [];
        var search_attributes = [];
        attribute.split(',').forEach( function(v,k) {
            search_attributes.push( v );
        });
        
        $(selector).each(function() {
            var row = $(this);
            var search_data = {};
            
            search_data['id'] = $(this).attr('id');
            search_attributes.forEach( function(v,k) {
                search_data[v] = $(row).attr(v);
            });
            stack_search.push(search_data);
        });
        
        var fuse = new Fuse(stack_search, {
            keys: search_attributes,
            id: 'id',
            threshold: 0.2
        });
            
        $(input_search).on('keyup', function(){
            if ($(this).val()) {
                var result = fuse.search($(this).val());
                
                search_attributes.forEach( function(v,k) {
                    $(selector + '['+v+']').hide();
                });
                
                if(result.length > 0) {
                    for (var i = 0; i < result.length; i++) {
                        var query = '#'+result[i].item.id;
                        $(query).show();
                    }
                }
            }
            else {
                search_attributes.forEach( function(v,k) {
                    $(selector + '['+v+']').show();
                });
            }
        });
    }, 10);
}

var pretty = {};
pretty.json = {
   replacer: function(match, pIndent, pKey, pVal, pEnd) {
      var key = '<span class=json-key>';
      var val = '<span class=json-value>';
      var str = '<span class=json-string>';
      var r = pIndent || '';
      if (pKey)
         r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
      if (pVal)
         r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
      return r + (pEnd || '');
      },
   print: function(obj) {
      var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
      return JSON.stringify(obj, null, 3)
         .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
         .replace(/</g, '&lt;').replace(/>/g, '&gt;')
         .replace(jsonLine, pretty.json.replacer);
      }
   };

Template.updateMessageDropdown = function() {
    $.get('engine.php?class=SystemMessageDropdown', function(data) {
        $('#envelope_messages').html(data);
    });
}

Template.updateNotificationDropdown = function() {
    $.get('engine.php?class=SystemNotificationDropdown', function(data) {
        $('#envelope_notifications').html(data);
    });
}

Template.wikiPagePicker = function(){
    __adianti_load_page('index.php?class=SystemWikiPagePicker');
};