function tfullcalendar_start(id, editable, defaultView, currentDate, language, events, day_click_action, event_click_action, event_update_action, min_time, max_time, hidden_days, movable, resizable, options, full_height)
{
    var drag_status   = 0;
    var resize_status = 0;

    events.failure = function(error) {
        __adianti_failure_request(error.xhr, error.xhr.status, error.xhr.statusText);
    };

    var attributes = {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth,listWeek'
        },
        locale: language,
        themeSystem: 'bootstrap',
        hiddenDays: hidden_days,
        initialDate: currentDate,
        initialView: defaultView,
        allDaySlot: false,
        datesSet: function(view,element){setTimeout( function() {__adianti_process_popover()},100)},
        slotMinTime: min_time,
        slotMaxTime: max_time,
        slotLabelFormat : { hour12: false, hour: '2-digit', minute: '2-digit' },
        eventTimeFormat: { hour12: false, hour: '2-digit', minute: '2-digit' },
        editable: editable,
        dayMaxEventRows: true, // allow "more" link when too many events
        events: events,
        eventDurationEditable: resizable,
        eventStartEditable: movable,
        eventDisplay: 'block',
        eventSourceSuccess: function(content) {
            setTimeout(__adianti_process_popover, 1500);
            return content.eventArray;
        },
        eventContent: function (arg) {
            return {html: arg.event.title};
        },
        dateClick: function(info) {
            if (day_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                var date;
                if(info.allDay) {
                    date = info.dateStr;
                } else {
                    date = moment(info.date).format('YYYY-MM-DD HH:mm:ss');
                }
                __adianti_load_page(day_click_action+"&date="+ date +"&view="+info.view.type);
            }
        },
        eventClick: function(info) {
            if (event_click_action !== '' && drag_status == 0 && resize_status == 0 ) {
                __adianti_load_page(event_click_action+"&id="+info.event.id+"&key="+info.event.id+"&view="+info.view.type);
            }
        },
        eventDragStart : function() {
            drag_status = 1;
        },
        eventDragStop : function() {
            drag_status = 0;
        },
        eventDrop : function(info) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(
                    event_update_action+"&id="+info.event.id+"&key="+info.event.id+"&start_time="+moment(info.event.start).format('YYYY-MM-DD HH:mm:ss')+"&end_time="+moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'));
            }
        },
        eventResizeStart : function() {
            resize_status = 1; 
        },
        eventResizeStop : function() {
            resize_status = 0;
        },
        eventResize : function(info) {
            if (event_update_action !== '') {
                __adianti_ajax_exec(event_update_action+"&id="+info.event.id+"&key="+info.event.id+"&start_time="+moment(info.event.start).format('YYYY-MM-DD HH:mm:ss')+"&end_time="+moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'));
            }
        }
    };
    
    if (full_height) {
        var fullHeight =
            // Total size page
            ($(document).innerHeight() ?? 0) -
            // - Navbar size
            ($('html nav.navbar').innerHeight() ?? 0) -
            // - Tabs size
            ( $('.adianti_tabs_container').innerHeight() ?? 0) -
            // - Content page (forms, lists, bradcrumbs) size
            $('#adianti_div_content').innerHeight() -
            // - Footer size
            ($('footer.main-footer').innerHeight() ?? 0)
            // - Margin size
            - 100;
        
        // Min for not scroll
        fullHeight = Math.max(fullHeight, 500);

        attributes.expandRows = true;
        attributes.height = `${fullHeight}px`;
    }

    options = Object.assign(attributes, JSON.parse( options) );
    

    var calendarEl = $('#'+id)[0];
    var calendar = new FullCalendar.Calendar(calendarEl, options);
    calendar.render();

    var resizer = new ResizeObserver(function() {
        calendar.render();
    });
    resizer.observe(calendarEl);

    $('#'+id).data('fullcalendar', calendar);
}