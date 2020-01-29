<?php
namespace Adianti\Widget\Util;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TElement;
use Adianti\Util\AdiantiTemplateHandler;

use stdClass;

/**
 * FullCalendar Widget
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFullCalendar extends TElement
{
    protected $current_date;
    protected $event_action;
    protected $day_action;
    protected $update_action;
    protected $reload_action;
    protected $default_view;
    protected $min_time;
    protected $max_time;
    protected $events;
    protected $enabled_days;
    protected $popover;
    protected $poptitle;
    protected $popcontent;
    protected $resizable;
    protected $movable;
    protected $options;


    /**
     * Class Constructor
     * @param $current_date Current date of calendar
     * @param $default_view Default view (month, agendaWeek, agendaDay)
     */
    public function __construct($current_date = NULL, $default_view = 'month')
    {
        parent::__construct('div');
        $this->current_date = $current_date ? $current_date : date('Y-m-d');
        $this->default_view = $default_view;
        $this->{'class'} = 'tfullcalendar';
        $this->{'id'}    = 'tfullcalendar_' . mt_rand(1000000000, 1999999999);
        $this->min_time  = '00:00:00';
        $this->max_time  = '24:00:00';
        $this->enabled_days = [0,1,2,3,4,5,6];
        $this->popover = FALSE;
        $this->resizable = TRUE;
        $this->movable = TRUE;
        $this->options = [];
    }
    
    /**
     * Set extra datepicker options (ex: autoclose, startDate, daysOfWeekDisabled, datesDisabled)
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }
    
    /**
     * Define the time range
     */
    public function setTimeRange($min_time, $max_time)
    {
        $this->min_time = $min_time;
        $this->max_time = $max_time;
    }
    
    /**
     * Enable these days
     */
    public function enableDays($days)
    {
        $this->enabled_days = $days;
    }
    
    /**
     * Set the current date of calendar
     * @param $date Current date of calendar
     */
    public function setCurrentDate($date)
    {
        $this->current_date = $date;
    }
    
    /**
     * Set the current view of calendar
     * @param $view Current view of calendar (month, agendaWeek, agendaDay)
     */
    public function setCurrentView($view)
    {
        $this->default_view = $view;
    }
    
    /**
     * Define the reload action
     * @param $action reload action
     */
    public function setReloadAction(TAction $action)
    {
        $this->reload_action = $action;
    }
    
    /**
     * Define the event click action
     * @param $action event click action
     */
    public function setEventClickAction(TAction $action)
    {
        $this->event_action = $action;
    }
    
    /**
     * Define the day click action
     * @param $action day click action
     */
    public function setDayClickAction(TAction $action)
    {
        $this->day_action = $action;
    }
    
    /**
     * Define the event update action
     * @param $action event updaet action
     */
    public function setEventUpdateAction(TAction $action)
    {
        $this->update_action = $action;
    }
    
    /**
     * Enable popover
     * @param $title Title
     * @param $content Content
     */
    public function enablePopover($title, $content)
    {
        $this->popover = TRUE;
        $this->poptitle = $title;
        $this->popcontent = $content;
    }
    
    /**
     * Disable event resize
     */
    public function disableResizing()
    {
        $this->resizable = FALSE;
    }
    
    /**
     * Disable event dragging
     */
    public function disableDragging()
    {
        $this->movable = FALSE;
    }
    
    /**
     * Add an event
     * @param $id Event id
     * @param $title Event title
     * @param $start Event start time
     * @param $end Event end time
     * @param $url Event url
     * @param $color Event color
     */
    public function addEvent($id, $title, $start, $end = NULL, $url = NULL, $color = NULL, $object = NULL)
    {
        $event = new stdClass;
        $event->{'id'} = $id;
        
        if ($this->popover and !empty($object))
        {
            $poptitle   = AdiantiTemplateHandler::replace($this->poptitle, $object);
            $popcontent = AdiantiTemplateHandler::replace($this->popcontent, $object);
            $event->{'title'} = self::renderPopover($title, $poptitle, $popcontent);
        }
        else
        {
            $event->{'title'} = $title;
        }
        $event->{'start'} = $start;
        $event->{'end'} = $end;
        $event->{'url'} = $url;
        $event->{'color'} = $color;
        
        $this->events[] = $event;
    }
    
    /**
     * Render title popover
     * @param $title Event title
     * @param $poptitle Popover Title
     * @param $popcontent Popover Content
     */
    public static function renderPopover($title, $poptitle, $popcontent)
    {
        return "<div popover='true' poptitle='{$poptitle}' popcontent='{$popcontent}' style='display:inline;cursor:pointer'> {$title} </div>";
    }
    
    /**
     * Show the callendar and execute required scripts
     */
    public function show()
    {
        $id = $this->{'id'};
        
        $language = strtolower( AdiantiCoreTranslator::getLanguage() );
        $reload_action_string = '';
        $event_action_string  = '';
        $day_action_string    = '';
        $update_action_string = '';
        $options = json_encode($this->options);
        
        if ($this->event_action)
        {
            if ($this->event_action->isStatic())
            {
                $this->event_action->setParameter('static', '1');
            }
            $event_action_string = $this->event_action->serialize();
        }
        
        if ($this->day_action)
        {
            if ($this->day_action->isStatic())
            {
                $this->day_action->setParameter('static', '1');
            }
            $day_action_string = $this->day_action->serialize();
        }
        
        if ($this->update_action)
        {
            $update_action_string = $this->update_action->serialize(FALSE);
        }
        if ($this->reload_action)
        {
            $reload_action_string = $this->reload_action->serialize(FALSE);
            $this->events = array('url' => 'engine.php?' . $reload_action_string . '&static=1');
        }
        
        $events = json_encode($this->events);
        $editable = ($this->update_action) ? 'true' : 'false';
        $movable = ($this->movable) ? 'true' : 'false';
        $resizable = ($this->resizable) ? 'true' : 'false';
        $hidden_days = json_encode(array_values(array_diff([0,1,2,3,4,5,6], $this->enabled_days)));
        
        TScript::create("tfullcalendar_start( '{$id}', {$editable}, '{$this->default_view}', '{$this->current_date}', '$language', $events, '{$day_action_string}', '{$event_action_string}', '{$update_action_string}', '{$this->min_time}', '{$this->max_time}', $hidden_days, {$movable}, {$resizable}, '{$options}');");
        parent::show();
    }
}
