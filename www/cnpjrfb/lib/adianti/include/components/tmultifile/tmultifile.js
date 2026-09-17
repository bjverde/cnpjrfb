/**
 * Hidden's nao podem ser criados sempre na createStatusRow(), pois na edição vem pronto do TMultiFile.php
 * Resolvido pois ele dá um remove do hidden criado pelo php, logo após dar um start
 */
function MultiFileUploader(input_id, obj_file, service_action, parent_container, complete_action, file_handling, image_gallery, popover, limitSize)
{
    this.field_name = $('#' + input_id).attr('receiver');
    this.obj_file = obj_file;
    this.limitSize = limitSize;
    this.service_action = service_action;
    this.complete_action   = complete_action;
    this.file_handling     = file_handling;
    this.image_gallery     = JSON.parse( image_gallery );
    this.popover           = JSON.parse( popover );
    this.parent_container  = $('#'+parent_container);
    this.instances = [];
    this.messages = [];
    var that = this;
    
    // Create structure of status row
    this.createStatusRow = function()
    {
        var input_file = that.parent_container.find('[widget=tmultifile]')[0];
        var input_size = $(input_file).css('width');
        var file_row_wrapper  = $('<div />',  {'class': 'tfile_row_wrapper tfile_row_wrapper-'+input_id});
        var file_row_1        = $('<div />',  {'class': 'tfile_row_1'});
        var file_row_2        = $('<div />',  {'class': 'tfile_row_2'});
        var file_link_wrapper = $('<div />',  {'class': 'tfile_link_wrapper', 'style': 'width: calc('+ input_size +' - 25px)'});
        var file_del_icon     = $('<span />', {'class': 'tfile_del_icon fa fa-minus-circle gray', 'title': 'Remover'});
        var file_progress_bar = $('<div />',  {'class': 'progress-bar progress-bar-success', 'style': 'width: 0%; height: 2px;'});
        var file_input_hidden = $('<input />',{type: 'hidden', name: that.field_name+'[]'});
        
        if (that.image_gallery.enabled == '1')
        {
            $(file_link_wrapper).css('width','unset');
            $(file_row_wrapper).css('border','1px solid gray');
            $(file_row_wrapper).css('border-radius','3px');
            $(file_row_wrapper).css('margin','2px');
            $(file_row_wrapper).css('display','inline-block');
        }
        
        that.file_row_wrapper  = file_row_wrapper;
        that.file_input_hidden = file_input_hidden;
        that.file_progress_bar = file_progress_bar;
        that.file_link_wrapper = file_link_wrapper;
        
        //that.parent_container.children('.tfile_row_wrapper').remove();
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
                that.parent_container.find('[widget=tmultifile]').css('padding-left', '5px');
                
                // check the file to be deleted
                file_data.delFile = file_data.fileName;
                
                // if delete recently included file (not in server already)
                if (file_data.delFile == file_data.newFile) {
                    file_data = '';
                    that.setData(file_data);
                    file_row_wrapper.remove();
                }
                
                that.setData(file_data);
            }
        );
        
        return file_row_wrapper;
    }

    this.createInputHidden = function(value)
    {
        var file_input_hidden = $('<input />',{
            type: 'hidden',
            name: that.field_name+'[]',
            widget: "thidden",
            value: value
        });

        that.parent_container.append(file_input_hidden);
        
        return file_input_hidden;
    };
    
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
                        file_link_wrapper.popover({
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
        if (data) {
            $(that.file_input_hidden).val(encodeURIComponent(JSON.stringify(data)));
        }
        else {
            $(that.file_input_hidden).val('');
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
        var form_data = new FormData();
        var file = that.obj_file;
        
        form_data.append('fileName', file);

        if (that.limitSize)
        {
            this.onLoadStart();
            if (file.size > that.limitSize)
            {
                throw new Error(_t('large_file') + that.limitSize/1024/1024 + 'MBs');
            }
        }

        //if (that.file_handling) {
            this.createStatusRow();
        //}
        
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
        that.parent_container.find('[widget=tmultifile]').css('padding-left', '20px');
        that.parent_container.children('i').attr({'class'   : 'fa fa-spinner fa-spin fa-fw blue file-response-icon',
                                                'data-original-title' : '...',
                                                'title' : '...'});
    };
    
    // Handle the end of the upload
    this.onLoad = function(evt)
    {
    };
    
    // Handle the progress
    this.onProgress = function(evt)
    {
        if( evt.lengthComputable ) {
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
                        var file_data      = that.getData();
                        file_data.newFile  = 'tmp/' + response.fileName;
                        file_data.fileName = 'tmp/' + response.fileName;
                        
                        that.setData(file_data);
                        that.showFile(file_data);
                    }
                    else {
                        that.file_input_hidden.val(response.fileName);
                        that.file_link_wrapper.html(response.fileName);
                    }
                    
                    that.showMessage('success', _t('success'));
                    if (this.readyState == 4 && typeof(that.complete_action) == "function") {
                        that.complete_action();
                    }
                }
                else {
                    that.file_row_wrapper.remove();
                    that.showMessage('error', response.msg);
                }
            }
            catch (e)
            {
                that.file_row_wrapper.remove();
                
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
        
        if (that.messages.length > 0)
        {
            if (type != 'success')
            {
                that.messages.push(message);
            }
            else
            {
                that.messages.push('success');
            }
            
            var icon = that.messages.filter( m => m == 'success').length > 0 ? 'fa fa-info-circle blue' : 'fa fa-exclamation-circle red';

            that.parent_container.find('[widget=tmultifile]').css('padding-left', '20px');
            that.parent_container.children('i').attr({ 'class' : icon + ' file-response-icon',
                                                       'title' : that.messages.filter( m => m != 'success').join(', ') });
        }
        else
        {
            if (type == 'success')
            {
                that.parent_container.find('[widget=tmultifile]').css('padding-left', '20px');
                that.parent_container.children('i').attr({ 'class' : 'far fa-check-circle green file-response-icon',
                                                           'title' : message });
            }
            else
            {      
                that.messages.push(message);
    
                that.parent_container.find('[widget=tmultifile]').css('padding-left', '20px');
                that.parent_container.children('i').attr({ 'class' : 'fa fa-exclamation-circle red file-response-icon',
                                                           'title' : message });
            }
        }
        
        __adianti_process_tooltips();
    };
    
    this.clear = function()
    {
        $('.tfile_row_wrapper-'+input_id).each(function(){
            $(this).find('input').each(function(){
                $(this).val('');
            });
            
            $(this).remove();
        });
        
        that.parent_container.children('i').attr({'class' : '', 'title' : ''});
        that.parent_container.find('[widget=tmultifile]').css('padding-left', '5px');
        
        $('[name="file_'+this.field_name+'[]"]').val('');
        $(this.file_input_hidden).val('');
        $('#'+this.input_id).val('');
    }
    
    this.setValue = function(values)
    {
        this.clear();
        
        if(!values) 
        {
            return;
        }
        
        try {
            values = JSON.parse(values);
        } catch (e) {
            
        }
        
        for (var index in values) 
        {
            
            var value = values[index];
            var data = {"idFile" : index, "fileName": value};
            
            
            
            if(value.indexOf('%7B%') >= 0 )
            {
                data = JSON.parse(decodeURIComponent(value));
            }
            
            if(this.file_handling) 
            {
                this.createStatusRow();
                this.setData(data);
                this.showFile();
            } 
            else 
            {
                this.createInputHidden(value);
            }
        }
    }

    this.updateValueInfo = function() {
        // Refaz o count
        var dt = new DataTransfer();
        var files = that.instances.map( (file) => file.obj_file );

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            dt.items.add(file);
        }

        $('#' + input_id)[0].files = dt.files;
    }
    
    this.addInstance = function(instance)
    {
        this.instances.push(instance);
    }
}

function tmultifile_start( input_id, parent_container, service_action, complete_action, file_handling, image_gallery, popover, limitSize)
{
    var masterFile = new  MultiFileUploader(input_id, null, service_action, parent_container, complete_action, file_handling, image_gallery, popover, limitSize);

    $(function() {
        $('#' + input_id).change( function() {
            masterFile.messages = [];

            // iterate selected files in upload dialog
            $.each( $(this).prop('files'), function(index, upload_file) {
                var file = new  MultiFileUploader(input_id, upload_file, service_action, parent_container, complete_action, file_handling, image_gallery, popover, limitSize);                    
                file.messages = masterFile.messages;

                try {
                    file.initFileUpload();
                    masterFile.addInstance(file);
                } catch (e) {
                    file.showMessage('error', file.obj_file.name + ': ' + e.message);
                }
            });

            masterFile.updateValueInfo();
        });
        
        if (file_handling) {
            $('#'+input_id).parent().children('input[type=hidden]').each(function(index, input_hidden) {
                var file = new  MultiFileUploader(input_id, null, service_action, parent_container, complete_action, file_handling, image_gallery, popover, limitSize);
                file.createStatusRow();

                if ($(input_hidden).val())
                {
                    file.setData( JSON.parse(decodeURIComponent($(input_hidden).val())) );
                }
                file.showFile();
                $(input_hidden).remove();
                masterFile.addInstance(file);
            });
        }
        
        $('#' + input_id)[0].tmultifile = masterFile;

    });
}


function tmultifile_enable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name=\'file_'+field+'[]\']').removeAttr('disabled');
        $('form[name='+form_name+'] [name=\'file_'+field+'[]\']').removeClass('tfield_disabled').addClass('tfield');
    } catch (e) {
        console.log(e);
    }
}

function tmultifile_disable_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name=\'file_'+field+'[]\']').attr('disabled', true);
        $('form[name='+form_name+'] [name=\'file_'+field+'[]\']').removeClass('tfield').addClass('tfield_disabled');
    } catch (e) {
        console.log(e);
    }
}

function tmultifile_clear_field(form_name, field) {
    try {
        $('form[name='+form_name+'] [name=\''+field+'[]\']').val('');
        $('form[name='+form_name+'] [name=\'file_'+field+'[]\']').val('');
    } catch (e) {
        console.log(e);
    }
}
