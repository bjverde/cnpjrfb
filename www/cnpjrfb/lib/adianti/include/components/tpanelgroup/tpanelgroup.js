function card_toggle_collapse(generator)
{
    if ($(generator).closest('.card').find('.card-body:visible').length > 0) {
        $(generator).closest('.card').find('.card-body').hide();
        $(generator).find('i').removeClass('fa-chevron-up');
        $(generator).find('i').addClass('fa-chevron-down');
    }
    else {
        $(generator).closest('.card').find('.card-body').show();
        $(generator).find('i').addClass('fa-chevron-up');
        $(generator).find('i').removeClass('fa-chevron-down');
    }
}