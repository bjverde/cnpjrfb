function tgantt_start( id, day_action, minuteValue, pixelValue, update_action ) {

    tgantt_set_height(id);
    
    //Quantos minutos cada pulo vale
    $(id).data('pixelStep', pixelValue);

    //Quantos pixels representam o valor dos pulo
    $(id).data('minuteStep', minuteValue);
    
    $(id).data('lastHour', 0);

    if (update_action)
    {
        tgantt_set_update_action(id, update_action)
    }

    if (day_action)
    {
        tgantt_set_day_action(id, day_action);
    }
}

function tgantt_set_day_action(id, day_action)
{
    $(id + ' .tgantt-event').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();
        __adianti_load_page($(this).attr('href'));
    });

    $(id + ' .tgantt-cell').on('click', function(){
        var tr_data = $(this).closest('tr').data();
        __adianti_load_page( day_action+'&start_time='+$(this).data('date')+'&group_id='+tr_data['id']+'&group_title='+tr_data['title']);
    })
}

function tgantt_format_date(date)
{
    return moment(date).format('YYYY-MM-DD HH:mm:ss');
}

function tgantt_set_update_action(id, update_action)
{
    var pixelStep = $(id).data('pixelStep');

    $(id + " .tgantt-event" ).draggable({
        addClasses: false,
        delay: 50,
        axis: "x",
        grid: [pixelStep,100],
        drag: function(_, ui) {
            tgantt_update_data_title(id, ui);
        },
        stop: function(e, ui) {
            e.stopPropagation();
            // e.preventDefault();

            if( ui.position.left < 0) {
                var width = ui.helper.width();
                var left = Math.abs( ui.position.left );
                var marginLeft = parseInt( ui.helper.css('margin-left') );

                //Não deixa o evento sair da tela (lado esquerdo)
                if( width + (marginLeft - left) <= pixelStep ) {
                    var css = ( ( ( ( marginLeft + width ) * (-1) ) + parseFloat(pixelStep) ) );
                    ui.helper.css('left', css+'px');
                    ui.position.left = css;
                }
            } else {
                var widthTr = ui.helper.parent().parent().width();
                var left = Math.abs( ui.position.left );
                var width = ui.helper.width();
                var marginLeft = parseInt( ui.helper.css('margin-left') );

                if( left+marginLeft >= widthTr) {
                    var css = widthTr - marginLeft - parseFloat(pixelStep);
                    ui.helper.css('left', css+'px');
                    ui.position.left = css;
                }
            }

            var hours = tgantt_get_time_drag(id, ui);
            var event = JSON.parse(base64_decode(ui.helper.attr('data')));

            var date_start = new Date(event.start_time);
            date_start.setTime(date_start.getTime() + hours * 60 * 60 * 1000);
            
            var date_end = new Date(event.end_time);
            date_end.setTime(date_end.getTime() + hours * 60 * 60 * 1000);
            
            var event_id = event.id;
            var start = tgantt_format_date(date_start);
            var end = tgantt_format_date(date_end);
            
            // update event data
            event.start_time = start;
            event.end_time = end;
            ui.helper.attr('data', base64_encode(JSON.stringify(event))); // update event data
            
            __adianti_ajax_exec(update_action+"&id="+event_id+"&key="+event_id+'&start_time='+start+'&end_time='+end);
        }
    }).resizable({
        addClasses: false,
        delay: 100,
        handles: "e,w",
        axis: "x",
        grid: [pixelStep,10],
        /*resize: function(e, ui) {
            e.stopPropagation();
            e.preventDefault();
        },
        */
        stop: function(e, ui ) {
            e.stopPropagation();
            e.preventDefault();
            
            var handle_position = $(this).data('ui-resizable').axis;
            if (handle_position == 'e')
            {
                var move_direction = ($(this).data('ui-resizable').size.width - $(this).data('ui-resizable').originalSize.width) > 0 ? 'right' : 'left';
            }
            else
            {
                var move_direction = ($(this).data('ui-resizable').size.width - $(this).data('ui-resizable').originalSize.width) < 0 ? 'right' : 'left';
            }
            
            var hours = tgantt_get_time_resize(id, ui);
            
            var event = JSON.parse(base64_decode(ui.helper.attr('data')));
            var event_id = event.id;
            
            if (handle_position == 'w') // move start
            {
                var date = new Date(event.start_time);
                
                if (move_direction == 'left')
                {
                    date.setTime(date.getTime() - Math.abs(hours) * 60 * 60 * 1000);
                }
                else
                {
                    date.setTime(date.getTime() + Math.abs(hours) * 60 * 60 * 1000);
                }
                var start = tgantt_format_date(date);
                var end = event.end_time;
            }
            else // move end
            {
                var date = new Date(event.end_time);
                
                if (move_direction == 'left')
                {
                    date.setTime(date.getTime() - Math.abs(hours) * 60 * 60 * 1000);
                }
                else
                {
                    date.setTime(date.getTime() + Math.abs(hours) * 60 * 60 * 1000);
                }
                var start = event.start_time;
                var end = tgantt_format_date(date);
            }
            
            // update event data
            event.start_time = start;
            event.end_time = end;
            
            ui.helper.attr('data', base64_encode(JSON.stringify(event))); // update event data
            __adianti_ajax_exec(update_action+"&id="+event_id+"&key="+event_id+'&start_time='+start+'&end_time='+end);
        },
    });
}

function tgantt_update_data_title(id, ui)
{
    if($('.tooltip').length < 1)
    {
        return false;
    }

    var hourNow = tgantt_get_time_drag(ui);
    var tooltip = $('.tooltip')
    var left = tooltip.position().left;
    var lastHour = $(id).data('lastHour');
    var pixelStep = $(id).data('pixelStep');

    if (lastHour == hourNow)
    {
        return false;
    }

    if (hourNow > lastHour)
    {
        tooltip.css('left', left+parseInt(pixelStep) + 'px');
    }
    else
    {
        tooltip.css('left', (left-pixelStep) + 'px');
    }

    $(id).data('lastHour', hourNow);
}

function tgantt_set_height(id)
{
    // setTimeout(function(){
        let height = [];

        $(id + ' .table-content tr').each( function() {
            height.push( $(this).outerHeight() );
        });

        height.reverse();

        $(id + ' .table-rows tr').each( function() {
            $(this).outerHeight( height.pop() );
        });
    // }, 0);
}

function tgantt_get_steps(ui)
{
    return ui.position.left - ui.originalPosition.left;
}

function tgantt_get_time_drag(id, ui)
{
  var steps = tgantt_get_steps(ui);
  var pixelStep = $(id).data('pixelStep');
  var minuteStep = $(id).data('minuteStep');

  // Nao mexeu
  if( steps == 0)
  {
    return 0;
  }

  return ( ( ( steps / pixelStep ) * minuteStep ) / 60);
}

function tgantt_get_time_resize(id, ui)
{
    var valLeft = tgantt_get_time_drag(id, ui);
    var pixelStep = $(id).data('pixelStep');
    var minuteStep = $(id).data('minuteStep');

    if ( valLeft != 0 )
    {
        return valLeft;
    }

    var resizeWidth = ui.size.width - ui.originalSize.width;

    if ( resizeWidth == 0 )
    {
        return 0;
    }

    return ( ( ( resizeWidth / pixelStep ) * minuteStep ) /60);
}