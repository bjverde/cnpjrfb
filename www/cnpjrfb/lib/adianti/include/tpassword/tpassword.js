function tpassword_start(id) {
    $(`#${id} button`).click(function() {
        var i = $(this).find('i');
        var input = $(this).prev();

        i.toggleClass('fa-eye-slash');
        i.toggleClass('fa-eye');

        if(input.attr('type') == 'text') {
            input.attr('type', 'password');
        } else {
            input.attr('type', 'text');
        }
    });
}
