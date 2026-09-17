function tdbentry_start( field_name, url, min )
{
    $('input[name="'+field_name+'"]').autocomplete( { serviceUrl: url, minChars: min } );
}