function __adianti_show_toast64(type, message64, place, icon)
{
    __adianti_show_toast(type, atob(message64), place, icon)
}

function __adianti_show_toast(type, message, place, icon)
{
    var place = place.replace(/(?:^\w|[A-Z]|\b\w|\s+)/g, function(match, index) {
            if (+match === 0) return ""; // or if (/\s+/.test(match)) for white spaces
            return index == 0 ? match.toLowerCase() : match.toUpperCase();
          });

    var options = {
        message: message,
        timeout: 3000,
        position: place
    };

    if (type == 'show') {
        options['progressBarColor'] = 'rgb(0, 255, 184)';
        options['theme'] = 'dark';
    }

    if (typeof icon !== 'undefined') {
        var icon_prefix = icon.substring(0,3);
        if (['far', 'fas', 'fal', 'fad', 'fab'].includes(icon_prefix)) {
            options['icon'] = icon_prefix + ' fa-' + icon.substring(4);
        }
        else {
            options['icon'] = 'fa ' + icon.replace(':', '-');
        }
    }

    iziToast[type]( options );
}
