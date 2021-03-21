function tfullcalendar_start(id, editable, defaultView, currentDate, language, events, day_click_action, event_click_action, event_update_action, min_time, max_time, hidden_days, movable, resizable, options)
{
    var drag_status   = 0;
    var resize_status = 0;
    
    var attributes = {
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek'
        },
        hiddenDays: hidden_days,
        defaultDate: currentDate,
        defaultView: defaultView,
        allDaySlot: false,
        viewRender: function(view,element){setTimeout( function() {__adianti_process_popover()},100)},
        minTime: min_time,
        maxTime: max_time,
        slotLabelFormat : 'HH:mm',
        lang: language, 
        editable: editable,
        eventLimit: true, // allow "more" link when too many events
        events: events,
        eventDurationEditable: resizable,
        eventStartEditable: movable,
        eventRender: function (event, element) {
            element.find('.fc-title').html(event.title);
            element.find('.fc-list-item-title').html(event.title);
        },
        dayClick: function(date, jsEvent, view) {
            if (day_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(day_click_action+"&date="+date.format()+"&view="+view.name);
            }
        },
        eventClick: function(calEvent, jsEvent, view) {
            if (event_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(event_click_action+"&id="+calEvent.id+"&key="+calEvent.id+"&view="+view.name);
            }
        },
        eventDragStart : function(calEvent, jsEvent, ui, view) {
            drag_status = 1;
        },
        eventDragStop : function(calEvent, jsEvent, ui, view) {
            drag_status = 0;
        },
        eventDrop : function(calEvent, jsEvent, ui, view) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+calEvent.id+"&key="+calEvent.id+"&start_time="+calEvent.start.format()+"&end_time="+calEvent.end.format());
            }
        },
        eventResizeStart : function(calEvent, jsEvent, ui, view) {
            resize_status = 1; 
        },
        eventResizeStop : function(calEvent, jsEvent, ui, view) {
            resize_status = 0;
        },
        eventResize : function(calEvent, jsEvent, ui, view) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+calEvent.id+"&key="+calEvent.id+"&start_time="+calEvent.start.format()+"&end_time="+calEvent.end.format());
            }
        },
        eventAfterAllRender: function() {
            __adianti_process_popover();
        }
    };
    
    options = Object.assign(attributes, JSON.parse( options) );
    
    $('#'+id).fullCalendar(
        options
    );
}
