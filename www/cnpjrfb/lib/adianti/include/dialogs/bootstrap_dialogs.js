/**
 * Show standard dialog
 */
function __adianti_dialog( options )
{
    if (options.type == 'info') {
        var icon = (options.icon ? options.icon : 'fa fa-info-circle fa-4x icon-dialog text-info');
    }
    else if (options.type == 'warning') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-triangle fa-4x icon-dialog text-warning');
    }
    else if (options.type == 'error') {
        var icon = (options.icon ? options.icon : 'fa fa-exclamation-circle fa-4x icon-dialog text-danger');
    }

    if (typeof bootbox == 'object')
    {
        bootbox.dialog({
          title: options.title,
          animate: false,
          backdrop: true,
          onEscape: function() {
            if (typeof options.callback != 'undefined')
            {
                options.callback();
            }
          },
          message: '<div>'+
                    '<span class="'+icon+'" style="float:left"></span>'+
                    '<span style="margin-left:70px;display:block;max-height:500px">'+options.message+'</span>'+
                    '</div>',
          buttons: {
            success: {
              label: "OK",
              className: "btn-primary",
              callback: function() {
                if (typeof options.callback != 'undefined')
                {
                    options.callback();
                }
              }
            }
          }
        });
    }
    else {
        // fallback mode
        alert(options.message);
        if (typeof options.callback != 'undefined') {
            options.callback();
        }
    }
}

/**
 * Show message error dialog
 */
function __adianti_error(title, message, callback)
{
    __adianti_dialog( { type: 'error', title: title, message: message, callback: callback} );
}

/**
 * Show message info dialog
 */
function __adianti_message(title, message, callback)
{
    __adianti_dialog( { type: 'info', title: title, message: message, callback: callback} );
}

/**
 * Show message warning dialog
 */
function __adianti_warning(title, message, callback)
{
    __adianti_dialog( { type: 'warning', title: title, message: message, callback: callback} );
}

/**
 * Show question dialog
 */
function __adianti_question(title, message, callback_yes, callback_no, label_yes, label_no)
{
    if (typeof bootbox == 'object')
    {
        bootbox.dialog({
          title: title,
          animate: false,
          message: '<div>'+
                    '<span class="fa fa-question-circle fa-4x icon-dialog" style="float:left"></span>'+
                    '<span style="margin-left:70px;display:block;max-height:500px">'+message+'</span>'+
                    '</div>',
          buttons: {
            yes: {
              label: label_yes,
              className: "btn-primary",
              callback: function() {
                if (typeof callback_yes != 'undefined') {
                    callback_yes();
                }
              }
            },
            no: {
              label: label_no,
              className: "btn-secondary",
              callback: function() {
                if (typeof callback_no != 'undefined') {
                    callback_no();
                }
              }
            },
          }
        });
    }
    else
    {
        // fallback mode
        var r = confirm(message);
        if (r == true) {
            if (typeof callback_yes != 'undefined') {
                callback_yes();
            }
        } else {
            if (typeof callback_no != 'undefined') {
                callback_no();
            }
        }
    }
}

/**
 * Show input dialog
 */
function __adianti_input(question, callback)
{
    if (typeof bootbox == 'object')
    {
        bootbox.prompt(question, function(result) {
          if (result !== null) {
            callback(result);
          }
        });
    }
    else
    {
        var result = prompt(question, '');
        callback(result);
    }
}