Adianti.translation = {
    'en' : {
        'large_file' : 'The file is too large. The upload limit is ',
        'success': 'Success'
    },
    'pt' : {
        'large_file' : 'O arquivo é muito grande. O limite de carregamento é ',
        'success': 'Sucesso'
    },
    'es' : {
        'large_file' : 'El archivo es demasiado grande. El límite de carga es ',
        'success': 'Éxito'
    }
}

function _t(message)
{
    return Adianti.translation[Adianti.language][message] ?? message;
}