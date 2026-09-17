function __adianti_failure_message()
{
    if (Adianti.debug == 1) {
        if (Adianti.language == 'pt') {
            return 'Requisição falhou. Verifique a conexão com internet e os logs do servidor de aplicação';
        }
        return 'Request failed. Check the internet connection and the application server logs';
    }
    else
    {
        if (Adianti.language == 'pt') {
            return 'Requisição falhou';
        }
        return 'Request failed';
    }
}

/**
 * Show message failure
 */
 function __adianti_failure_request(jqxhr, textStatus, exception) {
    if(! jqxhr.responseText) {
        __adianti_error('Error', textStatus + ': ' + __adianti_failure_message());
    } else {
        $('#adianti_online_content').append(jqxhr.responseText);
    }
}