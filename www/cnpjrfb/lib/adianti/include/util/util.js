/**
 * Debounce actions
 */
function __adianti_debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

function __adianti_copy_to_clipboard64(content64)
{
    __adianti_copy_to_clipboard(base64_decode(content64));
}

function __adianti_copy_to_clipboard(content)
{
    var clipboard = navigator.clipboard;
    if (clipboard == undefined) {
        __adianti_show_toast64('error', base64_encode('Clipboard is unavailable'), 'bottomCenter', '');
    } else {
        clipboard.writeText(content).then(function() {
            __adianti_show_toast64('success', base64_encode('Copied to clipboard successfully!'), 'bottomCenter', '');
        }, function() {
            __adianti_show_toast64('error', base64_encode('Unable to write to clipboard'), 'bottomCenter', '');
        });
    }
}

function __adianti_toggle_fullscreen()
{
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else if (document.exitFullscreen) {
        document.exitFullscreen();
    }
}

function __adianti_string_get_between(context_string, needle_start, needle_end, only_content = true)
{
    var pos_ini = context_string.indexOf(needle_start);
    if (pos_ini < 0)
    {
        return '';
    }
    
    var start = pos_ini + (only_content ? needle_start.length : 0);
    
    var pos_fim = context_string.indexOf(needle_end);//, $start);
    if (pos_fim < 0)
    {
        return '';
    }
    
    var end = (only_content ? pos_fim : pos_fim + needle_end.length);
    
    return context_string.substring(start, end);
}

$.fn.mycenter = function () {
    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.outerHeight() ) / 2+$(window).scrollTop() + "px");
    this.css("left", ( $(window).width() - this.outerWidth() ) / 2+$(window).scrollLeft() + "px");
    return this;
}

