function tcolor_enable_field(form_name, field) {
    try {
        setTimeout(function(){
            $('form[name='+form_name+'] [name='+field+']').trigger('enable');
            $('form[name='+form_name+'] [name='+field+']').removeClass('tfield_disabled');
        },1);
    } catch (e) {
        console.log(e);
    }
}

function tcolor_disable_field(form_name, field) {
    try {
        setTimeout(function(){
            $('form[name='+form_name+'] [name='+field+']').trigger('disable');
            $('form[name='+form_name+'] [name='+field+']').addClass('tfield_disabled');
        },1);
    } catch (e) {
        console.log(e);
    }
}

function tcolor_start(id, size, theme, change_function, options) {
    var lables = {
        pt : { clear: 'Limpar', save: 'Salvar' },
        es : { clear: 'Limpiar', save: 'Guardar' },
        en : { clear: 'Clear', save: 'Save' }
    };

    var tcolor = $(`#${id}`);
    tcolor.css('width', `calc( ${size} - 30px )`);

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
        tcolor.val(color?.toHEXA().toString());
        instance.hide();
        if(typeof change_function != 'undefined') {
            change_function(color?.toHEXA().toString());
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
            console.log('clear');
            pickr.setColor(null)
        }
    });
}