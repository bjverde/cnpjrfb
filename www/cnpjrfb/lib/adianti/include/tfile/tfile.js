function FileUploader(input_id, parent_container, service_action, complete_action, error_action, file_handling, image_gallery, popover, limitSize)
{
    this.field_name       = $('#' + input_id).attr('receiver');
    this.input_id         = input_id;
    this.limitSize        = limitSize;
    this.service_action   = service_action;
    this.parent_container = $('#'+parent_container);
    this.complete_action  = complete_action;
    this.error_action     = error_action;
    this.file_handling    = file_handling;
    this.image_gallery    = JSON.parse( image_gallery );
    this.popover          = JSON.parse( popover );
    this.obj_file         = document.getElementById( this.input_id );
    
    var that = this;
    
    // Create structure of status row
    this.createStatusRow = function()
    {
        var input_file = that.parent_container.find('[widget=tfile]')[0];
        var input_size = $(input_file).css('width');
        var file_row_wrapper  = $('<div />',  {'class': 'tfile_row_wrapper', 'style': 'display:none'});
        var file_row_1        = $('<div />',  {'class': 'tfile_row_1'});
        var file_row_2        = $('<div />',  {'class': 'tfile_row_2'});
        var file_link_wrapper = $('<div />',  {'class': 'tfile_link_wrapper', 'style': 'width: calc('+ input_size +' - 25px)'});
        var file_del_icon     = $('<span />', {'class': 'tfile_del_icon fa fa-minus-circle gray', 'title': 'Remover'});
        var file_progress_bar = $('<div />',  {'class': 'progress-bar progress-bar-success', 'style': 'width: 0%; height: 2px;'});
        var file_input_hidden = $('<input />',{type: 'hidden', name: that.field_name, widget: 'tfile'});
        
        if (that.image_gallery.enabled == '1')
        {
            $(file_link_wrapper).css('width','unset');
            $(file_row_wrapper).css('border','1px solid gray');
            $(file_row_wrapper).css('border-radius','3px');
            $(file_row_wrapper).css('margin','2px');
        }
        
        that.file_row_wrapper  = file_row_wrapper;
        that.file_input_hidden = file_input_hidden;
        that.file_progress_bar = file_progress_bar;
        that.file_link_wrapper = file_link_wrapper;
        
        that.parent_container.children('.tfile_row_wrapper').remove();
        that.parent_container.append(file_row_wrapper);
        file_row_wrapper.append(file_row_1);
        file_row_wrapper.append(file_row_2);
        file_row_1.append(file_input_hidden, file_link_wrapper, file_del_icon);
        file_row_2.append(file_progress_bar);
        
        $(file_del_icon).click(
            function(){
                var file_data = that.getData();
                
                file_row_wrapper.hide();
                that.parent_container.children('i').attr({'class' : '', 'title' : ''});
                that.parent_container.find('[widget=tfile]').css('padding-left', '5px');
                
                // check the file to be deleted
                file_data.delFile = file_data.fileName;
                
                // if delete recently included file (not in server already)
                if (file_data.delFile == file_data.newFile) {
                    file_data = '';
                }
                
                that.setData(file_data);
            }
        );
        
        return file_row_wrapper;
    }
    
    this.clear = function()
    {
        if (that.file_row_wrapper)
        {
            that.file_row_wrapper.hide();
        }

        that.parent_container.children('i').attr({'class' : '', 'title' : ''});
        that.parent_container.find('[widget=tfile]').css('padding-left', '5px');
        $(that.file_input_hidden).val('');
        $('#'+this.input_id).val('');
    }
    
    // Show file
    this.showFile = function(file_data)
    {
        if (typeof file_data == 'undefined')
        {
            var file_data  = this.getData();
        }
        var file_name = file_data.fileName;
        
        if (file_name) {
            var file_row_wrapper  = that.file_row_wrapper;
            var file_link_wrapper = file_row_wrapper.find('.tfile_link_wrapper');
            file_row_wrapper.find('.progress-bar').css('width', '100%');
            
            if ($.inArray( file_name.split('.').pop().toLowerCase(), [ "png", "jpg", "jpeg", "gif", "svg", "webp", "ico", "fav" ] ) > -1) {
                var pop_template = '<div class="popover" role="tooltip" style="z-index:100000;max-width:800px"><div class="arrow"></div><h3 class="popover-title popover-header"></h3><div class="popover-content popover-body"><div class="data-content"></div></div></div>';
                var pop_content  = '<img style="max-width:460px" src="download.php?file={file_name}">';
                
                if (that.popover.content) {
                    pop_content = atob(that.popover.content);
                }
                
                pop_content = pop_content.replace('{file_name}', file_name);
                if (that.image_gallery.enabled == '1')
                {
                    var file_link = $('<a />', { 'href': 'download.php?file='+file_name, 'target' : '_blank' });
                    var image = $('<img />', { 'src': 'download.php?file='+file_name }).height(that.image_gallery.height).width(that.image_gallery.width);
                    file_link.append(image);
                    
                    if (that.popover.enabled == '1') {
                        file_link.popover({
                            trigger: "hover",
                            'content': pop_content,
                            'html' : true,
                            'container': 'body',
                            'template': pop_template,
                            title: that.popover.title || ""
                        });
                    }
                }
                else
                {
                    var file_link = $('<a />', { 'href': 'download.php?file='+file_name, 'target' : '_blank'});
                    
                    if (that.popover.enabled == '1') {
                        file_link.popover({
                            trigger: "hover",
                            'content': pop_content,
                            'html' : true,
                            'container': 'body',
                            'template': pop_template,
                            title: that.popover.title || ""
                        });
                    }
                }
            }
            else {
                var file_link = $('<a />', { 'href': 'download.php?file='+file_name, 'target' : '_blank' });
            }
            that.file_row_wrapper.show();
            if (that.image_gallery.enabled != '1')
            {
                file_link.append(file_name);
            }
            file_link_wrapper.html(file_link);
       }
    };
    
    // Get file data
    this.getData = function()
    {
        var file_data = decodeURIComponent(that.file_input_hidden.val());
        var file_data_json = '';
        
        try {
            file_data_json = file_data ? JSON.parse(file_data) : {};
        }
        catch(e) {
            file_data_json = {};
        }
        return file_data_json;
    };
    
    // Set file data
    this.setData = function(data)
    {
        that.clear();
        
        if (data) {
            $(that.file_input_hidden).val(encodeURIComponent(JSON.stringify(data)));
        }
    };
    
    // Ajax uploads are supported
    this.supportAjaxUploadWithProgress = function()
    {
        return this.supportFileAPI() && this.supportAjaxUploadProgressEvents() && this.supportFormData();
    };
    
    // File API supported?
    this.supportFileAPI = function()
    {
        var fi = document.createElement('INPUT');
        fi.type = 'file';
        return 'files' in fi;
    };
        
    // Progress events supported?
    this.supportAjaxUploadProgressEvents = function()
    {
        var xhr = new XMLHttpRequest();
        return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
    };
    
    // FormData supported
    this.supportFormData = function()
    {
        return !! window.FormData;
    };
    
    // Start upload
    this.initFileUpload = function()
    {
        if (that.file_handling) {
            //this.createStatusRow(); // do recreate row, because it would remove old data.fileName (needed for exclusion/data.delFile)
        }

        var form_data = new FormData();
        var file = that.obj_file.files[0];

        form_data.append('fileName', file);

        if (that.limitSize)
        {
            if (file.size > that.limitSize)
            {
                that.onLoadStart();
                throw new Error(_t('large_file') + that.limitSize/1024/1024 + 'MBs');
            }
        }

        if (this.supportAjaxUploadWithProgress()) {
            this.sendXHRequest(form_data, that.service_action);
        }
    };
    
    // Send request
    this.sendXHRequest = function(form_data, uri)
    {
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('loadstart', this.onLoadStart, false);
        xhr.upload.addEventListener('progress', this.onProgress, false);
        xhr.upload.addEventListener('load', this.onLoad, false);
        xhr.addEventListener('readystatechange', this.onReadyStateChange, false);
        xhr.open('POST', uri, true);
        xhr.send(form_data);
    };
    
    // Handle the start of the upload
    this.onLoadStart = function(evt)
    {
        if( that.parent_container.children('i').length == 0 ) {
            that.parent_container.prepend($('<i>'));
        }
        if (that.file_handling) {
            that.file_row_wrapper.show();
        }
        that.parent_container.find('[widget=tfile]').css('padding-left', '20px');
        that.parent_container.children('i').attr({'class'   : 'fa fa-spinner fa-spin fa-fw blue file-response-icon',
                                                  'title' : '...'});
    };
    
    // Handle the end of the upload
    this.onLoad = function(evt)
    {
    };
    
    // Handle the progress
    this.onProgress = function(evt)
    {
        if( evt.lengthComputable && that.file_handling) {
            var percent = Math.round(evt.loaded * 100 / evt.total);
            that.file_progress_bar.css('width',percent+'%').css('height', 2);
            that.file_link_wrapper.html(percent + '%');
        }
    };
    
    // Handle the response from the server
    this.onReadyStateChange = function(evt)
    {
        var status = null;
        
        try {
            status = evt.target.status;
        }
        catch(e) {
            return;
        }

        if (status == '200' && evt.target.readyState == '4' && evt.target.responseText) {
            try {
                var response = JSON.parse( evt.target.responseText );
                
                if ( response.type == 'success' ) {
                    if (that.file_handling) {
                        var file_data = that.getData();
                        
                        // if there was already a file, then tell server to delete it
                        if (file_data.fileName) {
                            file_data.delFile = file_data.fileName;
                        }
                        
                        file_data.newFile  = 'tmp/' + response.fileName;
                        file_data.fileName = 'tmp/' + response.fileName;
                        that.setData(file_data);
                        that.showFile(file_data);
                    }
                    else {
                        var file_input_hidden = $('#'+that.input_id).parent().children('input[type=hidden]');
                        $(file_input_hidden).val(response.fileName);
                    }

                    that.showMessage('success', _t('success'));
                    if (this.readyState == 4 && typeof(that.complete_action) == "function") {
                        that.complete_action();
                    }
                }
                else {
                    if (that.file_handling) {
                        that.file_row_wrapper.remove();
                    }
                    if (typeof(that.error_action) == "function") {
                        that.error_action();
                    }
                    that.showMessage('error', response.msg);
                }
            }
            catch (e) {
                if (typeof(that.error_action) == "function") {
                    that.error_action();
                }
                
                if (that.file_handling) {
                    that.file_row_wrapper.remove();
                }
                if (typeof response == "undefined") {
                    that.showMessage('error', evt.target.responseText);
                }
                else {
                    that.showMessage('error', e);
                }
            }
        }
    };
    
    // Show message
    this.showMessage = function(type, message)
    {
        that.parent_container.children('i').removeAttr('data-original-title');

        if (type == 'success') {
            that.parent_container.find('[widget=tfile]').css('padding-left', '20px');
            that.parent_container.children('i').attr({ 'class' : 'far fa-check-circle green file-response-icon',
                                                       'data-original-title' : message });
        }
        else if (type == 'error') {
            that.parent_container.find('[widget=tfile]').css('padding-left', '20px');
            that.parent_container.children('i').attr({ 'class' : 'fa fa-exclamation-circle red file-response-icon',
                                                       'data-original-title' : message });
        }
    };
    
    this.setValue = function(value)
    {
        this.clear();
        if(value)
        {
            var data = {"fileName": value};
        
            if(value.indexOf('%7B%') >= 0 )
            {
                data = JSON.parse(decodeURIComponent(value));
            }
            
            this.setData(data);    
        }
        
        this.showFile();
    }
}

function tfile_start( input_id, parent_container, service_action, complete_action, error_action, file_handling, image_gallery, popover, limit_size)
{
    $(function() {
        var file = new FileUploader(input_id, parent_container, service_action, complete_action, error_action, file_handling, image_gallery, popover, limit_size);
        
        $('#' + input_id).change( function() {
            try {
                file.initFileUpload();
            } catch (e) {
                file.clear()
                file.showMessage('error', e.message);
                if (typeof(this.error_action) == "function") {
                    file.error_action();
                }
            }
        });
        
        if (file_handling) {
            var input_hidden = $('#'+input_id).parent().children('input[type=hidden]');
            file.createStatusRow();
            if ($(input_hidden).val())
            {
                file.setData( JSON.parse(decodeURIComponent($(input_hidden).val())) );
            }
            file.showFile();
            $(input_hidden).remove();
        }
        
        $('[name="' + file.field_name+'"]')[0].tfile = file;
    });
}

function tfile_send_data(id, value) {
    
    if(value)
    {
        var data = {"fileName": value};

        var instance = $('#'+id).data('instance')
    
        if(typeof instance != 'undefined')
        {
            instance.setData(data);
            instance.showFile();
        } 
    }
    
}

function tfile_enable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name=file_'+field+']').removeAttr('disabled');
        $('form[name='+form_name+'] [name=file_'+field+']').removeClass('tfield_disabled').addClass('tfield');
    } catch (e) {
        console.log(e);
    }
}

function tfile_disable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name=file_'+field+']').attr('disabled', true);
        $('form[name='+form_name+'] [name=file_'+field+']').removeClass('tfield').addClass('tfield_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tfile_clear_field(form_name, field) {
    try{
        $('form[name='+form_name+'] [name='+field+']').val('');
        $('form[name='+form_name+'] [name=file_'+field+']').val('');
    } catch (e) {
        console.log(e);
    }
}

function tfile_update_download_link(name)
{
    if ($('#view_'+name).length) {
        value = $('[name='+name+']').val();
        $('#view_'+name).attr('href', 'download.php?file=tmp/' + value);
        $('#view_'+name).html('tmp/' + value);
    }
}
