function tlabel_toggle_visibility(field_id)
{
    var tlabel = $('#'+field_id);

    tlabel.find('i').on('click', function(e) {
        var span = $(this).prev();
        if (span.css('filter') === 'none') {
            span.css('filter', 'blur(5px)');
            $(this).switchClass('fa-eye', 'fa-eye-slash')
        } else {
            span.css('filter', '');
            $(this).switchClass('fa-eye-slash', 'fa-eye')
        }
   });
}