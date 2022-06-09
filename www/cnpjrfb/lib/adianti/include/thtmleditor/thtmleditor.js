function thtmleditor_enable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').next().find('.note-editable').attr('contenteditable', true); },1);
}

function thtmleditor_disable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').next().find('.note-editable').attr('contenteditable', false); },1);
}

function thtmleditor_clear_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').code(''); },1);    
}

function thtmleditor_get_length(content) {
    content = content.replace(/(<p><br><\/p>)/ig, ' ');
    content = content.replace(/(<([^>]+)>)/ig,"");
    content = content.replace(/(&nbsp;)/ig, ' ');

    return content.length;
}

function thtmleditor_start(objectId, width, height, lang, options) {
    
    var attributes = {
        dialogsInBody: true,
        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36', '48' , '64', '82', '150'],
        width: width,
        height: height,
        lang: lang,
        toolbar: [
          ['undoredo', ['undo','redo']],
          ['style', ['style', 'bold', 'italic', 'underline','clear']],
          ['font', ['fontname', 'fontsize', 'color']],
          ['para', ['ul', 'ol', 'paragraph', 'height']],
          ['insert', ['table', 'link', 'picture', 'hr']],
          ["view", ["fullscreen", "codeview", "help"]],
       ]
    };
    
    options = Object.assign(attributes, JSON.parse( options) );
    
    if (typeof options.completion !== 'undefined') {
        if (typeof summernote_wordlist == 'undefined') {
            summernote_wordlist = {};
        }
        summernote_wordlist[objectId] = options.completion;
        
        options.hint = {
            match: /\b(\w{1,})$/,
            search: function (keyword, callback) {
              callback($.grep(summernote_wordlist[objectId], function (item) {
                return item.indexOf(keyword) === 0;
              }));
          }
        };
    }
    
    if (options.airMode == true)
    {
        setTimeout( function() {
            $('#'+objectId).parent().find('.note-editable').height(height + 'px').css('overflow', 'auto');
        }, 1);
    }

    if(!! options.maxlength && options.maxlength > 0) {
        options.callbacks = {
            onKeydown: function(e) {
                var l = thtmleditor_get_length( $(e.currentTarget).html()) + 1;

                if (l > options.maxlength) {
                    var allowedKeys = [8, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];
                    if (! allowedKeys.includes(e.keyCode))
                    e.preventDefault();
                }
            },
            onKeyup: function(e) {
                var l = thtmleditor_get_length( $(e.currentTarget).html());
                $('#'+objectId).next('.note-editor').find('.counter').html(l+'/'+options.maxlength);
            },
            onPaste: function(e) {
                e.preventDefault();
            }
          }
    }
    
    $('#'+objectId).summernote(options);
    
    if(!! options.maxlength && options.maxlength > 0) {
        var content = $('#'+objectId).parent().find('.note-editable').html();
        var length = thtmleditor_get_length(content);
        $('#'+objectId).next('.note-editor').append(
            '<small style="position:absolute;bottom:10px;right:4px;" class="counter">' + length + '/' + options.maxlength + '</small>'
        );
    }

    if (typeof $('#'+objectId).next('.note-editor')[0] !== 'undefined')
    {
        var container = $('#'+objectId).next('.note-editor')[0];
        $(container).css('margin', $('#'+objectId).css('margin'));
    }
}

function thtml_editor_reload_completion(field, options)
{
    objectId = $('[name='+field+']').attr('id');
    setTimeout( function() {
        summernote_wordlist[objectId] = options;
    }, 1);
}