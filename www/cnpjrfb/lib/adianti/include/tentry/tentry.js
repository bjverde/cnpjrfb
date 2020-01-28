function tentry_new_mask(field, mask)
{
    $(document).ready(function(){
        mask = mask.replace(/9/g, '0');
        var length = $('#'+field).attr('maxlength') > 0 ? $('#'+field).attr('maxlength') : 250;
        
        if (mask == 'A!') {
            mask = 'A'.repeat(length);
        }
        else if (mask == 'S!') {
            mask = 'S'.repeat(length);
        }
        else if (mask == '0!') {
            mask = '0'.repeat(length);
        }
        $('#'+field).mask(mask);
    });
}

/**
 * TEntry Mask
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 * @enterprise  WebSeller Sistemas Integrados
 * @author  Wellington Camargo Serra
 * @author  Tiago Fernando Shirmer
 * @author  Cristian Tales
 * 
 * @param field = Name do campo a ser aplicado a mascara.
 * @param event = Evento de disparo js
 * @param mask  = Mascara a ser aplicada
 */
function tentry_mask(field, event, mask)
{
    var value, i, character, returnString,tamCampo,maskLength;
    value = field.value;
    
    if (typeof value == 'undefined')
    {
        return true;
    }
    
    if ($(field).attr('forceupper') == '1')
    {
        value = value.toUpperCase();
    }
    if ($(field).attr('forcelower') == '1')
    {
        value = value.toLowerCase();
    }
    
    if(document.all) // IE
    {
        keyCode = event.keyCode;
    }
    else if(document.layers) // Firefox
    {
        keyCode = event.which;
    }
    else
    {
        keyCode = event.which;
    }
    if (keyCode == 8 || event.keyCode == 9 || event.keyCode == 13) // backspace e caps
    {
        return true;
    }
    
    returnString = '';
    var n = 0;
    
    /**
     * Mask Type Verification 
     * Verifica se a mascará será aplicada caracter a caracter
     * ou se será aplicada a todo o campo.
     * ! = aplicada a todo o campo + case sensitive
     * # = aplicada a todo o campo - case sensitive
     */
    if(mask.charAt(1)=='!')
    {
       maskLength = field.value.length+1;
    }
    else
    {
       maskLength = mask.length;
    }
    
    for(i=0; i<maskLength-1; i++)
    {
        maskChar  = mask.charAt(i);
        valueChar = value.charAt(n);
        
        if (i <= value.length)
        {
            if (((maskChar == "-")  || (maskChar == "_") || (maskChar == ".") || (maskChar == "/") ||
                 (maskChar == "\\") || (maskChar == ":") || (maskChar == "|") ||
                 (maskChar == "(")  || (maskChar == ")") || (maskChar == "[") || (maskChar == "]") ||
                 (maskChar == "{")  || (maskChar == "}")) & (maskChar!==valueChar))
            {
                returnString += maskChar; 
            }
            else
            {
            
                returnString += valueChar;
                n ++;
            }
        }
    }
    
    field.value = returnString;
    tamCampo    = field.value.length;


    /**
     * Mask Character Verification 
     * ! - todo campo
     *
     * Verifica o segundo campo da mascara.
     * Se ='!' aplica a mascara a todo o campo
     * Senão aplica a mascara definida para cada caractere. 
     */
    if(mask.charAt(1)=='!' )
    {
        maskChar = mask.charAt(0);
    }
    else
    {
        maskChar = mask.charAt(tamCampo);
    }

    /**
     * Mask Verification 
     * A,a - campo alfanumerico
     * S,s - alfabetico  
     * 9 - numeros
     *  
     * Verifica a mascara definida para o campo
     */
    switch(maskChar)
    {
        
        case 'A':
        case 'a':
            return (((keyCode > 47) && (keyCode < 58))||((keyCode > 64) && (keyCode < 91))||((keyCode > 96) && (keyCode < 123)));
            break;
        case 'S':
        case 's':
            return (((keyCode > 64) && (keyCode < 91))||((keyCode > 96) && (keyCode < 123)));
            break;
        case '9':
            return ((keyCode > 47) && (keyCode < 58));
            break;
    }

    return true;
}

/**
 * TEntry Upper case 
 * @enterprise  WebSeller
 * 
 * @param field = Name do campo a ser aplicado o uppercase.
 * @return retorna o conteudo do campo em maiusculo.
 */
function tentry_upper(field)
{
    if (typeof field.value !== 'undefined')
    {
        field.value = field.value.toUpperCase();
    }
}

/**
 * TEntry Lower case 
 * @enterprise  WebSeller
 * 
 * @param field = Name do campo a ser aplicado o lowercase.
 * @return retorna o conteudo do campo em minusculo.
 */
function tentry_lower(field)
{
    if (typeof field.value !== 'undefined')
    {
        field.value = field.value.toLowerCase();
    }
}

function tentry_autocomplete(field, list, options)
{
    var selecteds;
    var selector = 'input[name="'+field+'"]';
    if ($('#'+field).length >0) {
        var selector = '#'+field
    }
    
    var attributes = {
        lookup: list,
        onSelect: function() {
            var oldv = $(this).data('val');
            var newv = $(this).val();
            $(this).data('val', newv);
            $(selector).removeAttr('onblur');
            if (oldv !== newv) {
                if ($(selector).attr('exitaction')) {
                    string_callback=$(selector).attr('exitaction');
                    Function(string_callback)();
                }
            }
        }
    }
    
    options = Object.assign(attributes, JSON.parse( options) );
    
    $(selector).autocomplete(options);
}

function tentry_autocomplete_by_name(field, list, options)
{
	objectId = $('[name='+field+']').attr('id');
	tentry_autocomplete(objectId, list, options)
}

function tentry_numeric_mask(field, decimals, decimal_sep, thousand_sep)
{
    var selector = 'input[name="'+field+'"]';
    if ($('#'+field).length >0) {
        var selector = '#'+field
    }
    
    if (Adianti.numericMaskLibrary == 'iMask') {
        $(selector).iMask({
                type : 'number',
                decDigits   : decimals,
                decSymbol   : decimal_sep,
                groupSymbol : thousand_sep
        });
        
        if ($(selector).prop('readonly') == true) {
            $(selector).off('keydown');
        }
    }
    else {
        var decimals_zeros = '0'.repeat(decimals);
        $(selector).mask("#"+thousand_sep+"##0"+decimal_sep+decimals_zeros, {reverse: true});
    }
}

function tentry_get_data_by_id(field_id)
{
    var nmask =$('#'+field_id).data('nmask');
    
    if (typeof nmask !== 'undefined')
    {
        var dec_sep = nmask.substring(1,2);
        var tho_sep = nmask.substring(2,3);
        var value   = $('#'+field_id).val();
        value = value.replace(tho_sep, '');
        value = value.replace(dec_sep, '.');
        return value;
    }
    else
    {
        return $('#'+field_id).val();
    }
}

function tentry_exit_on_enter(field_id)
{
    $('#'+field_id).bind('keypress', function(e) {
         if(e.keyCode == 13) {
             document.getElementById(field_id).blur();
         }
    });
}