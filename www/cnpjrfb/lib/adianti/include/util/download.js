/**
 * Download a file
 */
function __adianti_download_file(file, basename)
{
    extension = file.split('.').pop();
    screenWidth  = screen.width;
    screenHeight = screen.height;
    if (extension !== 'html')
    {
        screenWidth /= 3;
        screenHeight /= 3;
    }

    if (typeof basename == 'undefined') {
        basename = '';
    }

    window.open('download.php?file='+file+'&basename='+basename, '_blank',
      'width='+screenWidth+
     ',height='+screenHeight+
     ',top=0,left=0,status=yes,scrollbars=yes,toolbar=yes,resizable=yes,maximized=yes,menubar=yes,location=yes');
}