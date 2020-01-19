function thtmleditor_enable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').next().find('.note-editable').attr('contenteditable', true); },1);
}

function thtmleditor_disable_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').next().find('.note-editable').attr('contenteditable', false); },1);
}

function thtmleditor_clear_field(form_name, field) {
    setTimeout(function(){ $('form[name='+form_name+'] [name='+field+']').code(''); },1);    
}

function thtmleditor_start(objectId, width, height, lang, options) {
    
    var attributes = {
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
    
    $('#'+objectId).summernote(options);
    
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