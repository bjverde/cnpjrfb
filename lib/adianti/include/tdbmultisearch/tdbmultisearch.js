function tdbmultisearch_start( id, minlen, maxsize, placeholder, multiple, service, width, height, hash, callback ) {
    var options = {
        minimumInputLength: minlen,
        maximumSelectionLength: maxsize,
        allowClear: true,
        placeholder: placeholder,
        multiple: multiple,
        hash: hash,
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
            url: service,
            dataType: 'json',
            delay: 250,
            
            // prepare query params before send to server
            data: function(value, page) {
                return {
                    value: value.term,
                    hash: hash
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
    
    if (multiple !== '1')
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
    
    if (parseInt(maxsize) !== 1)
    {
        $('#'+id).parent().find('.select2-selection').height(height);
        $('#'+id).parent().find('.select2-selection').find('.select2-selection__rendered').height(height);
        $('#'+id).parent().find('.select2-selection').find('.select2-selection__rendered').css('overflow-y', 'auto');
    }
}