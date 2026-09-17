/**
 * Start blockUI dialog
 */
function __adianti_block_ui(wait_message)
{
    if (typeof $.blockUI == 'function')
    {
        if (typeof Adianti.blockUIConter == 'undefined')
        {
            Adianti.blockUIConter = 0;
        }
        Adianti.blockUIConter = Adianti.blockUIConter + 1;
        if (typeof wait_message == 'undefined')
        {
            wait_message = Adianti.waitMessage;
        }

        $.blockUI({
           message: '<h1 style="font-size:25pt"><i class="fa fa-spinner fa-pulse"></i> '+wait_message+'</h1>',
           fadeIn: 0,
           fadeOut: 0,
           css: {
               border: 'none',
               top: '100px',
               left: 0,
               maxWidth: '300px',
               width: 'inherit',
               padding: '15px',
               backgroundColor: 'none',
               'border-radius': '5px 5px 5px 5px',
               opacity: 1,
               color: '#fff'
           }
        });

        $('.blockUI.blockMsg').mycenter();
    }
}

/**
 * Closes blockUI dialog
 */
function __adianti_unblock_ui(force)
{
    if (typeof $.blockUI == 'function') {
        if (typeof force == 'undefined') {
            Adianti.blockUIConter = Adianti.blockUIConter -1;
            if (Adianti.blockUIConter <= 0) {
                $.unblockUI( { fadeIn: 0, fadeOut: 0 } );
                Adianti.blockUIConter = 0;
            }
        }
        else if (force == true) {
            $.unblockUI( { fadeIn: 0, fadeOut: 0 } );
            Adianti.blockUIConter = 0;
        }
    }
}