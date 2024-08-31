function tmultisearch_start( id, minlen, maxsize, placeholder, multiple, width, height, allowclear, allowsearch, callback, with_titles ) {
    var options = {
        minimumInputLength: minlen,
        maximumSelectionLength: maxsize,
        selectionTitleAttribute: with_titles,
        allowClear: allowclear,
        placeholder: placeholder,
        multiple: multiple,
        minimumResultsForSearch: allowsearch,
        id: function(e) { return e.id + "::" + e.text; },
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

function tmultisearch_clear_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name="'+field+'[]"]').val('').trigger('change');
        $('form[name='+form_name+'] [name="'+field+'"]').val('').trigger('change');
    }
    catch (e) {
        console.log(e);
    }
}

function tmultisearch_enable_field(form_name, field) {
    try {
        if ($('form[name='+form_name+'] [name="'+field+'"]').length > 0) {
            $('form[name='+form_name+'] [name="'+field+'"]').next('.select2').find('.select2-disable').remove();
        }
        else {
            $('form[name='+form_name+'] [name="'+field+'[]"]').next('.select2').find('.select2-disable').remove();
        }
    }
    catch (e) {
        console.log(e);
    }
}

function tmultisearch_disable_field(form_name, field, title) {
    if (!title) {
        title = '';
    }

    try {
        if($('form[name='+form_name+'] [name="'+field+'"]').length > 0) {
            $('form[name='+form_name+'] [name="'+field+'"]').next('.select2').prepend('<div title="'+title+'" class="select2-disable"></div>');
        }
        else if($('[form='+form_name+'] [name="'+field+'"]').length > 0) {
            $('[form='+form_name+'] [name="'+field+'"]').next('.select2').prepend('<div title="'+title+'" class="select2-disable"></div>');
        }
        else {
            $('form[name='+form_name+'] [name="'+field+'[]"]').next('.select2').prepend('<div title="'+title+'" class="select2-disable"></div>');
        }
    }
    catch (e) {
        console.log(e);
    }
}

// Backspace remove the entire item, not only one character per time
$.fn.select2.amd.require(['select2/selection/search'], function (Search) {
    var oldRemoveChoice = Search.prototype.searchRemoveChoice;

    Search.prototype.searchRemoveChoice = function () {
        oldRemoveChoice.apply(this, arguments);
        this.$search.val('');
    };
});

// Remove wrong option title
$.fn.select2.amd.require(['select2/selection/single'], function (Single) {
    var _updateSingleSelection = Single.prototype.update;

    Single.prototype.update = function(data) {
        _updateSingleSelection.apply(this, Array.prototype.slice.apply(arguments));
        var $rendered = this.$selection.find('.select2-selection__rendered');
        $rendered.removeAttr('title');
    };
});

// Remove wrong option title
$.fn.select2.amd.require(['select2/selection/multiple'], function (MultipleSelection) {
    var _updateMultipleSelection = MultipleSelection.prototype.update;

    MultipleSelection.prototype.update = function(data) {
        _updateMultipleSelection.apply(this, Array.prototype.slice.apply(arguments));

        var _selectionTitleAttribute = this.options.get('selectionTitleAttribute');
        if (_selectionTitleAttribute === false) {
            var $rendered = this.$selection.find('.select2-selection__rendered');
            $rendered.find('.select2-selection__choice').removeAttr('title');
        }
    }
});


// Compatibility with jquery-3.6 or above (autofocusing the select2 input)
$(document).on('select2:open', () => {
    let allFound = document.querySelectorAll('.select2-container--open .select2-search__field');
    allFound[allFound.length - 1].focus();
});