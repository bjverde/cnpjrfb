function tcolor_enable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    try {
        setTimeout(function(){
            $(selector).trigger('enable');
            $(selector).removeClass('tfield_disabled');
            $(selector).attr('readonly', false);
            $(selector).removeAttr('tabindex');
            $(selector).css('pointer-events', 'auto');
        },1);
    } catch (e) {
        console.log(e);
    }
    setTimeout(function() {
        $(selector).next('.pickr').show();
    },1);
}

function tcolor_disable_field(form_name, field) {
    var selector = tfield_get_selector(form_name, field);
    
    try {
        setTimeout(function(){
            $(selector).trigger('disable');
            $(selector).addClass('tfield_disabled');
            $(selector).attr('readonly', true);
            $(selector).attr('tabindex', "-1");
            $(selector).css('pointer-events', 'none');
        },1);
    } catch (e) {
        console.log(e);
    }
    setTimeout(function(){
        $(selector).next('.pickr').hide();
    },1);
}

function tcolor_start(id, size, theme, change_function, options) {
    var lables = {
        pt : { clear: 'Limpar', save: 'Salvar' },
        es : { clear: 'Limpiar', save: 'Guardar' },
        en : { clear: 'Clear', save: 'Save' },
        it : { clear: 'Cancella', save: 'Salva' },
        de : { clear: 'Löschen', save: 'Speichern' },
        fr : { clear: 'Effacer', save: 'Enregistrer' } 
    };
    
    var tcolor = $(`#${id}`);
    
    options.el = `#${id}+.tcolor`;
    options.theme = theme;
    options.default = tcolor.val() ? tcolor.val() : null;
    options.i18n = {
        'btn:clear': lables[Adianti.language]['clear'],
        'btn:save': lables[Adianti.language]['save'],
    };

    var pickr = Pickr.create(options);
    
    $(`#${id}`).on('focusin', function(){ pickr.show() })
    $(`#${id}`).data('picker', pickr);
    
    tcolor.on('disable', function(){ pickr.disable(); });
    tcolor.on('enable', function(){ pickr.enable(); });

    pickr.on('save', function(color, instance) {
        const oldValue = tcolor.val();
        const newValue = color?.toHEXA().toString();
        tcolor.val(newValue);
        instance.hide();

        if (oldValue != newValue)
        {
            if(typeof change_function != 'undefined') {
                setTimeout(function() {
                    change_function(newValue);
                } , 100);
            }
        }
    });

    pickr.on('swatchselect', function(color, instance) {
        instance.applyColor()
    });

    tcolor.on('change', function() {
        var comp = this.value.indexOf('#') == -1 ? '#' : '';
        if (this.value) {
            pickr.setColor(comp + this.value, false);
        } else {
            pickr.setColor(null)
        }
    });
}
