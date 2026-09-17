function tmultientry_start( id, maxsize, width, height, callback) {
    var options = {
        tags: true,
        maximumSelectionLength: maxsize,
        width: width,
        height: height,
        tokenSeparators: [',', ';']
    };
    
    if (typeof callback != 'undefined') {
        $('#'+id).on("change", function (e) {
            callback();
        });
    }
    
    var element = $('#'+id).select2(options);
    
    $('#'+id).parent().find('.select2-selection').height(height);
    $('#'+id).parent().find('.select2-selection').find('.select2-selection__rendered').height(height);
    $('#'+id).parent().find('.select2-selection').find('.select2-selection__rendered').css('overflow-y', 'auto');
}
