<?php
namespace Adianti\Widget\Util;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Util\TImage;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use stdClass;

/**
 * TGantt
 * 
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @author     Artur Comunello
 * @author     Lucas Tomasi
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TGantt extends TElement
{
    private $view_mode;
    private $events;
    private $rows;
    private $size;
    private $minutesStep;
    private $eventAction;
    private $start_date;
    private $end_date;
    private $interval;
    private $dates = [];
    private $headerActions = [];
    private $reloadAction;
    private $updateAction;
    private $dayClickAction;
    private $dragEvent;
    private $hours;
    private $count_hours;
    private $remove_space = FALSE;
    private $title = '';
    private $view_mode_button = FALSE;
    private $size_mode_button = FALSE;
    private $view_mode_options;
    private $size_mode_options;
    
    private $stripedMonths = FALSE;
    private $stripedRows = FALSE;
    private $transformTimeTitle = NULL;
    private $transformEventLabel = NULL;

    const MODE_DAYS            = 'MODE_DAYS';
    const MODE_MONTHS          = 'MODE_MONTHS';
    const MODE_DAYS_WITH_HOUR  = 'MODE_DAYS_WITH_HOUR';
    const MODE_MONTHS_WITH_DAY = 'MODE_MONTHS_WITH_DAY';

    const HOURS    = ['00', '01', '12', '18'];
    const HOURS_24 = ['00', '01','02','03', '04','05','06', '07', '08','09','10','11', '12', '13', '14', '15', '16', '17', '18', '19','20','21','22','23'];

    const SIZES         = ['xs', 'sm', 'md', 'lg'];
    const SIZESPX       = [30, 60, 120, 240];
    const SIZESPXHORAS  = [120, 240, 480, 960];
    const COLUMNHOURVAL = 24;

    const ADJUST_MARGIN = ['xs' => 0, 'sm' => 2, 'md' => 4, 'lg' => 10];
    
    /**
     * Constructor method
     */
    public function __construct($view_mode, $size = 'md')
    {
        $this->id     = 'tgantt' . mt_rand(1000000000, 1999999999);
        $this->events = [];
        $this->view_mode   = $view_mode;
        $this->size        = $size;
        $this->start_date  = date('Y-m-d');
        $this->hours       = self::HOURS;
        $this->count_hours = count(self::HOURS);

        if (in_array($view_mode, [self::MODE_DAYS, self::MODE_DAYS_WITH_HOUR]))
        {
            $this->setInterval('15 days');
        }
        else
        {
            $this->setInterval('2 month');
        }
    }
    
    /**
     * Change view mode
     */
    public function setViewMode($view_mode)
    {
        $this->view_mode = $view_mode;
    }
    
    /**
     * Get view mode
     */
    public function getViewMode()
    {
        return $this->view_mode;
    }
    
    /**
     * Render title popover
     * @param $title Event title
     * @param $poptitle Popover Title
     * @param $popcontent Popover Content
     */
    public static function renderPopover($title, $poptitle, $popcontent)
    {
        return "<div data-popover='true' poptitle='{$poptitle}' popcontent='{$popcontent}' style='display:flex;cursor:pointer; padding: 0px 7px'> {$title} </div>";
    }

    /**
     * Define title of header gantt
     * 
     * @param $title String title header 
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Define view mode
     * 
     * @param $mode String mode
     */
    public function setSizeMode($size)
    {
        if (! in_array($size, self::SIZES))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $size, __METHOD__));
        }

        $this->size = $size;
    }
    
    /**
     * Get size mode
     */
    public function getSizeMode()
    {
        return $this->size;
    }
    
    /**
     * Define transformer time title
     * 
     * $transformer callable (start, end, events)
     */
    public function setTransformerTimeTitle(callable $transformer)
    {
        $this->transformTimeTitle = $transformer;
    }

    /**
     * Define transformer event label
     * 
     * $transformer callable (object event, events, times)
     */
    public function setTransformerEventLabel(callable $transformEventLabel)
    {
        $this->transformEventLabel = $transformEventLabel;
    }
    
    /**
     * Define date start gantt
     * 
     * @param $date String date 
     */
    public function setStartDate($date)
    {
        $this->start_date = $date;
        $this->setInterval($this->interval);
    }

    /**
     * Return date start gantt
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Return date end gantt
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Define interval between dates
     * 
     * @param $interval String 1|2|4 number concat with type month|day|year e.g:  1 month, 10 days
     */
    public function setInterval($interval = '1 month')
    {
        $this->interval = $interval;
        $this->end_date = date('Y-m-d', strtotime("{$this->start_date} + {$interval} - 1 day"));
        
        if ($this->view_mode == self::MODE_MONTHS)
        {
            $start = new DateTime($this->start_date);
            $start->modify('first day of this month');
            $this->start_date = $start->format('Y-m-d');

            $end = new DateTime($this->end_date);
            $end->modify('last day of this month');
            $this->end_date = $end->format('Y-m-d');
        }
    }
    
    /**
     * Remove spaces between events
     */
    public function removeSpaceBetweenEvents()
    {
        $this->remove_space = TRUE;
    }

    /**
     * Add background striped on columns
     */
    public function enableStripedMonths()
    {
        $this->stripedMonths = TRUE;
    }

    /**
     * Add background striped on rows
     */
    public function enableStripedRows()
    {
        $this->stripedRows = TRUE;
    }

    /**
     * Enable hour when MODE_DAYS_WITH_HOUR 24h
     */
    public function enableFullHours()
    {
        $this->hours       = self::HOURS_24;
        $this->count_hours = count(self::HOURS_24);
    }

    /**
     * Define reload events action
     * 
     * @param $reloadAction TAction
     */
    public function setReloadAction(TAction $reloadAction)
    {
        $this->reloadAction = $reloadAction;
    }

    /**
     * Define the day click action
     * @param $action day click action
     */
    public function setDayClickAction(TAction $action)
    {
        $this->dayClickAction = $action;
        $this->dayClickAction->setParameter('register_state', 'false');
    }

    /**
     * Define reload events action
     * 
     * @param $reloadAction TAction Button action click
     * @param $label String Button label
     * @param $icon TImage Button icon
     */
    public function addHeaderAction(TAction $action, $label = '', TImage $icon = null)
    {
        $button = new TElement('button');

        if ($icon)
        {
            $button->add($icon);    
        }
        
        $button->add($label);
        $button->{'generator'} = 'adianti';
        $button->{'class'} = 'btn btn-sm ';

        $this->headerActions[] = [$button, $action];

        return $button;
    }
    
    /**
     * Add a form header widget
     * @param $widget Widget
     */
    public function addHeaderWidget($widget)
    {
        $this->headerActions[] = [$widget];
        return $widget;
    }
    
    /**
     * Add new row 
     * 
     * @param $id any Key of row
     * @param $title any Label of row
     */
    public function addRow( $id, $label )
    {
        $row = new stdClass;
        $row->{'id'}    = $id;
        $row->{'title'} = $label;

        $this->rows[] = $row;
    }
    
    /**
     *
     */
    public function clearEvents()
    {
        $this->events = [];
    }
    
    /**
     * Add new event on Gantt
     * 
     * @param $id any Key of event
     * @param $rowId any Key of row
     * @param $title String title
     * @param $start_time String date start
     * @param $end_time String date end
     * @param $color String color background
     * @param $percent float percent color
     */
    public function addEvent($id, $rowId, $title, $start_time, $end_time, $color = NULL, $percent = null)
    {
        $event = new stdClass;
        $event->{'id'}    = $id;
        $event->{'rowId'} = $rowId;
        $event->{'title'} = $title;
        $event->{'start_time'} = $start_time;
        $event->{'end_time'}   = $end_time;
        $event->{'color'} = $color;
        $event->{'percent'} = $percent;

        if (empty($this->events[$rowId]))
        {
            $this->events[$rowId] = [];
        }

        $this->events[$rowId][] = $event;
    }

    /**
     * Define click event action
     * 
     * @param $action TAction
     */
    public function setEventClickAction( $action )
    {
        $this->eventAction = $action;
        $this->eventAction->setParameter('register_state', 'false');
    }
    
    /**
     * Define drag event action
     * 
     * @param $action TAction
     */
    public function enableDragEvent( TAction $updateAction, $minutesStep = 1440)
    {
        $this->dragEvent    = true;
        $this->updateAction = $updateAction;
        $this->minutesStep  = $minutesStep;
    }

    /**
     * Return dates into interval
     */
    private function getDates()
    {
        if ( !empty( $this->dates ) )
        {
            return $this->dates;
        }
        
        $begin = new DateTime( $this->start_date );
        $end   = new DateTime( $this->end_date   );
        $end = $end->modify( '+1 day' );

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $date)
        {
            $this->dates[] = $date;
        }

        return $this->dates;
    }

    /**
     * Return pixel value step minutes
     */
    private function getPixelValue()
    {
        $columnSzie = $this->getColumnSize();
        #Tamanho da Coluna / ( 24 horas (representacao do minuteStep em horas) ) )
        return $columnSzie / ( 24 / ( $this->minutesStep / 60 ) );
    }

    /**
     * Rerturn size column based into a zoom level
     */
    private function getColumnSize()
    {
        $key = array_search($this->size, self::SIZES);

        switch ($this->view_mode)
        {
            case self::MODE_DAYS:
            case self::MODE_MONTHS_WITH_DAY:
            case self::MODE_MONTHS:
                return self::SIZESPX[$key];
                break;
            case self::MODE_DAYS_WITH_HOUR:
                return self::SIZESPX[$key] * $this->count_hours;
                break;
        }
    }

    /**
     * Return title gantt
     */
    private function geTimeTitle()
    {
        $months = [];
        $start  = new DateTime($this->start_date);
        $end    = new DateTime($this->end_date);

        if ($this->transformTimeTitle)
        {
            return call_user_func_array($this->transformTimeTitle, [$start->format('Y-m-d H:i'), $end->format('Y-m-d H:i'), $this->events]);
        }

        $s = mb_substr(AdiantiCoreTranslator::translate($start->format('F')), 0, 3);
        $e = mb_substr(AdiantiCoreTranslator::translate($end->format('F')), 0, 3);

        $months[$s] = $s;
        $months[$e] = $e;

        return implode(' - ', $months);
    }

    private function renderHeader()
    {
        $title = new TElement( 'div' );
        $title->class = 'panel-heading tgantt-title';
        $title->add( $this->title );
        
        $todayAction = clone $this->reloadAction;
        $todayAction->setParameter('start_time', date('Y-m-d'));
        $todayAction->setParameter('end_time', date('Y-m-d', strtotime("now +{$this->interval}")));

        $todayButton = new TElement('button');
        $todayButton->add(AdiantiCoreTranslator::translate('Today'));
        $todayButton->{'id'} = 'now';
        $todayButton->{'generator'} = 'adianti';
        $todayButton->{'style'} = 'margin: 0 20px 3px 20px;';
        $todayButton->{'class'} = 'btn btn-sm btn-primary';
        $todayButton->{'href'} = $todayAction->serialize(TRUE);
        
        $previusWeekAction = clone $this->reloadAction;
        $previusWeekAction->setParameter('start_time', date('Y-m-d', strtotime("{$this->start_date} -{$this->interval}")));
        $previusWeekAction->setParameter('end_time', date('Y-m-d', strtotime("{$this->start_date} - 1 day")));

        $previusWeek = new TElement('button');
        $previusWeek->add(new TImage('fa:chevron-left'));
        $previusWeek->{'id'} = 'previous_week';
        $previusWeek->{'generator'} = 'adianti';
        $previusWeek->{'class'} = 'btn btn-default';
        $previusWeek->{'href'} = $previusWeekAction->serialize(TRUE);

        $next_end = new DateTime($this->end_date);
        $next_end->modify('+'.$this->interval);
        $next_end->modify('last day of this month');
        
        $nextWeekAction = clone $this->reloadAction;
        $nextWeekAction->setParameter('start_time', date('Y-m-d', strtotime("{$this->end_date} + 1 day")));
        $nextWeekAction->setParameter('end_time', $next_end->format('Y-m-d'));
        
        $nextWeek = new TElement('button');
        $nextWeek->add(new TImage('fa:chevron-right'));
        $nextWeek->{'id'} = 'previous_week';
        $nextWeek->{'generator'} = 'adianti';
        $nextWeek->{'class'} = 'btn btn-default';
        $nextWeek->{'href'} = $nextWeekAction->serialize(TRUE);

        $title->add( $todayButton );
        $title->add( $previusWeek );
        $title->add( $nextWeek );

        $month = new TElement( 'span' );
        $month->add($this->geTimeTitle());
        $month->{'style'} = 'margin-left: 25px;margin-right: auto;';

        $title->add($month);

        if ($this->headerActions)
        {
            foreach($this->headerActions as $headerAction)
            {
                $event_ids = array_map(function($e) { return $e->{'id'}; }, array_column($this->events, 0));
                $widget = $headerAction[0];
                
                if (!empty($headerAction[1]))
                {
                    $action = $headerAction[1];
                    
                    $action->setParameter('start_time', $this->start_date);
                    $action->setParameter('end_time', $this->end_date);
                    $action->setParameter('interval', $this->interval);
                    $action->setParameter('event_ids', $event_ids);
                    $action->setParameter('view_mode', $this->view_mode);
                    $action->setParameter('size_mode', $this->size);
                    
                    $widget->{'href'} = $action->serialize(TRUE);
                }
                
                // $action->setParameter('register_state', 'false');
                // $action->setParameter('static', '1');
                
                $title->add($widget);
            }
        }

        return $title;
    }

    /**
     *  Render rows
     */
    private function renderAside()
    {
        $tableAside = new TTable;
        $tableAside->{'class'} = 'table-rows';

        //Somente se for no modo de horas
        switch ( $this->view_mode)
        {
            case self::MODE_DAYS_WITH_HOUR:
            case self::MODE_MONTHS_WITH_DAY:
                $tableRow = $tableAside->addRow();
                $cell = $tableRow->addCell('');
                $cell->{'style'} = 'border-bottom: unset';
                break;
        }

        $tableRow = $tableAside->addRow();
        $tableRow->{'style'} = 'height: 30px !important;';

        $cell = $tableRow->addCell( '' );
        $cell->{'style'} = 'height: 30px !important;border-top:unset;';

        if( !empty( $this->dayInterval) )
        {
            $row = $tableAside->addRow();
            $row->{'class'} = 'tgantt-head-hour';
            $cell = $row->addCell( '&nbsp;' );
        }
        
        if (!empty($this->rows))
        {
            foreach ($this->rows as $row)
            {
                $tableRow = $tableAside->addRow();
                $cell = $tableRow->addCell( $row->{'title'} );
    
                if (strip_tags($row->{'title'}) == $row->{'title'})
                {
                    $cell->{'style'} = 'padding: 15px';
                }
            }
        }
        
        $aside = new TElement( 'aside' );
        $aside->{'class'} = 'fixedTable-sidebar';
        $aside->add( $tableAside );

        return $aside;
    }

    /**
     * Render header table month
     * 
     * @param $time_table Table
     */
    private function renderMonthHeader($time_table)
    {
        $monthsColspan    = [];
        $table_row        = $time_table->addRow();
        $table_row->{'class'} = 'tgantt-head tgantt-head-day';

        foreach ($this->getDates() as $date)
        {
            $month = AdiantiCoreTranslator::translate($date->format('F'));
            if(!isset($monthsColspan[$month]))
            {
                $monthsColspan[$month]  = 1;
            }
            else
            {
                $monthsColspan[$month]++;
            }
        }

        $months = [];

        foreach ($this->getDates() as $date)
        {
            $month = AdiantiCoreTranslator::translate($date->format('F'));

            if(!isset($months[$month]))
            {
                $months[$month]  = 1;

                $dayLabel        = new TElement( 'div' );
                $dayLabel->{'class'} = 'tgantt-weekly-header-day-label';
                $dayLabel->add($month);

                $h4 = new TElement( 'h4' );
                $h4->{'class'} = 'tgantt-weekly-header-info-' . $this->size;
                $h4->add( $dayLabel );

                $cell = $table_row->addCell( $h4 );
                $cell->{'colspan'} = $monthsColspan[$month];
            }
        }
    }

    /**
     * Render header table month day
     * 
     * @param $time_table Table
     */
    private function renderMonthDayHeader($time_table)
    {
        $monthsColspan = [];
        $table_row = $time_table->addRow();
        $table_row->{'class'} = 'tgantt-head tgantt-head-day';

        $hours_row = $time_table->addRow();

        foreach ($this->getDates() as $date)
        {
            $month = AdiantiCoreTranslator::translate($date->format('F'));

            if(!isset($monthsColspan[$month]))
            {
                $monthsColspan[$month]  = 1;
            }
            else
            {
                $monthsColspan[$month]++;
            }
        }

        $months = [];

        $pintar = TRUE;

        foreach ($this->getDates() as $date)
        {
            $month = AdiantiCoreTranslator::translate($date->format('F'));

            if(!isset($months[$month]))
            {
                $months[$month]  = 1;

                $dayLabel        = new TElement( 'div' );
                $dayLabel->{'class'} = 'tgantt-weekly-header-day-label';
                $dayLabel->add($month);

                $h4 = new TElement( 'h4' );
                $h4->{'class'} = 'tgantt-weekly-header-info-' . $this->size;
                $h4->add( $dayLabel );

                // Name month
                $cell = $table_row->addCell( $h4 );
                $cell->{'colspan'} = $monthsColspan[$month];
                
                if ($pintar AND $this->stripedMonths)
                {
                    $cell->{'class'} = 'tgannt-cell-opacity';
                    $pintar = FALSE;
                }
                else
                {
                    $pintar = TRUE;
                }
            }

            // Day of month
            $cell = $hours_row->addCell($date->format('d'));
            $cell->{'class'} = 'hour-cell';

            if (!$pintar AND $this->stripedMonths)
            {
                $cell->{'class'} .= ' tgannt-cell-opacity';
            }
        }
    }

    /**
     * Render header table days
     * 
     * @param $time_table Table
     */
    private function renderDailyHeader( $time_table )
    {
        $table_row = $time_table->addRow();
        $table_row->{'class'} = 'tgantt-head';

        foreach ($this->getDates() as $date)
        {
            $dayLabel = new TElement( 'div' );
            $dayLabel->{'class'} = 'tgantt-weekly-header-day-label';
            $dayLabel->add( mb_substr(AdiantiCoreTranslator::translate($date->format( 'l' )), 0,3 ) );
            
            $dayNumber = new TElement( 'div' );
            $dayNumber->{'class'} = 'tgantt-weekly-header-day-number-' . $this->size;
            $dayNumber->add( $date->format( 'd' ) );

            $h4 = new TElement( 'h4' );
            $h4->{'class'} = 'tgantt-weekly-header-info-' . $this->size;
            $h4->add( $dayLabel );
            $h4->add( $dayNumber );

            $table_row->addCell( $h4 );
        }
    }

    /**
     * Render header table hours
     * 
     * @param $time_table Table
     */
    private function renderDailyHourHeader( $time_table )
    {
        $table_row = $time_table->addRow();
        $table_row->{'class'} = 'tgantt-head tgantt-head-day';

        $hours_row = $time_table->addRow();

        foreach ($this->getDates() as $date)
        {
            $dayLabel = new TElement( 'div' );
            $dayLabel->{'class'} = 'tgantt-weekly-header-day-label';
            $dayLabel->add( mb_substr(AdiantiCoreTranslator::translate($date->format( 'l' )), 0,3 ) );

            $dayNumber = new TElement( 'div' );
            $dayNumber->{'class'} = 'tgantt-weekly-header-day-number-' . $this->size;
            $dayNumber->add( $date->format( 'd' ) );

            $h4 = new TElement( 'h4' );
            $h4->{'class'} = 'tgantt-weekly-header-info-' . $this->size;
            $h4->add( $dayLabel );
            $h4->add( $dayNumber );

            $cell = $table_row->addCell( $h4 );
            $cell->{'colspan'} = $this->count_hours;

            foreach ($this->hours as $hour)
            {
                $cell = $hours_row->addCell($hour);
                $cell->{'class'} = 'hour-cell';
            }
        }
    }

    /**
     * Render header table
     * 
     * @param $table
     */
    private function renderTimeTableHeader($table)
    {
        switch ($this->view_mode)
        {
            case self::MODE_DAYS:
                $this->renderDailyHeader($table);
                break;
            case self::MODE_DAYS_WITH_HOUR:
                $this->renderDailyHourHeader($table);
                break;
            case self::MODE_MONTHS_WITH_DAY:
                $this->renderMonthDayHeader($table);
                break;
            case self::MODE_MONTHS:
                $this->renderMonthHeader($table);
                break;
            default:
                throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', 'mode', '__construct'));
                break;
        }
    }

    /**
     * Render time table
     * 
     * @param $table
     */
    private function renderTimeTable()
    {
        $time_table = new TTable;
        $time_table->{'class'} = 'table-content';

        $this->renderTimeTableHeader( $time_table );
        
        $pintarRow = TRUE;
        
        if (!empty($this->rows))
        {
            foreach ($this->rows as $index => $row)
            {
                $table_row = $time_table->addRow();
    
                $pintarColumn = TRUE;
                $mes_anterior = NULL;
    
                $pintarRow = $this->stripedRows ? $index % 2 != 0 : FALSE;
    
                foreach ($this->getDates() as $date)
                {
                    $month = AdiantiCoreTranslator::translate($date->format('F'));
    
                    if (is_null($mes_anterior))
                    {
                        $mes_anterior = $month;
                    }
    
                    if ($month != $mes_anterior)
                    {
                        $pintarColumn = $this->stripedMonths ? (!$pintarColumn) : FALSE;
                    }
    
                    switch ( $this->view_mode)
                    {
                        case self::MODE_DAYS:
                        case self::MODE_MONTHS_WITH_DAY:
                        case self::MODE_MONTHS:
                            $cell = $table_row->addCell( '' );
                            $cell->{'data-date'} = $date->format('Y-m-d H:i');
                            $cell->{'class'} = 'tgantt-cell';
                            $cell->{'style'} = '';
    
                            if ($this->remove_space)
                            {
                                $cell->{'style'} .= 'padding:unset;';
                            }
    
                            if (($pintarColumn || $pintarRow) && ! ($pintarColumn && $pintarRow) && ($this->stripedMonths || $this->stripedRows))
                            {
                                $cell->{'class'} .= ' tgannt-cell-opacity';
                            }
                           
                            break;
                        default:
                            foreach ($this->hours as $hour)
                            {
                                $cell = $table_row->addCell('');
                                
                                $cell->{'data-date'} = $date->format("Y-m-d {$hour}:i");
                                $cell->{'class'} = 'tgantt-cell';
                                $cell->{'style'} = '';
    
                                if ($this->remove_space)
                                {
                                    $cell->{'style'} .= 'padding:unset;';
                                }
                            }
                            break;
                    }
    
                    $mes_anterior = AdiantiCoreTranslator::translate($date->format('F'));
                }
    
                if( !empty( $this->events[ $row->{'id'} ] ) )
                {
                    $cell = $table_row->getChildren()[0]; //First cell
    
                    foreach ($this->events[ $row->{'id'} ] as $event )
                    {
                        $cell->add( $this->renderEvent( $event) );
                    }
                }
                
                $table_row->{'data-id'} = $row->{'id'};
                $table_row->{'data-title'} = $row->{'title'};
            }
        }
        
        $divFixedContent = new TElement( 'div' );
        $divFixedContent->{'class'} = 'fixedTable-body';
        $divFixedContent->add( $time_table);

        return $divFixedContent;
    }

    /**
     * Render event
     * 
     * @param $event
     */
    private function renderEvent($event)
    {
        $div                = new TElement('div');
        $div->{'id'}        = $event->{'id'};
        $div->{'class'}     = 'tgantt-event';
        $div->{'data'}      = base64_encode(json_encode($event));

        if ($this->transformEventLabel) {
            $div->add( call_user_func_array($this->transformEventLabel, [$event, $this->events, [$this->start_date, $this->end_date]]) );
        } else {
            $div->add( $event->{'title'} );
        }

        if ($this->eventAction)
        {
            $this->eventAction->setParameter('id', $event->{'id'});
            $this->eventAction->setParameter('key', $event->{'id'});
            $this->eventAction->setParameter('view_mode', $this->view_mode);
            $this->eventAction->setParameter('size_mode', $this->size);
            
            $div->{'generator'} = 'adianti';
            $div->{'href'}      = $this->eventAction->serialize(TRUE);
        }

        //Gantt begin and end dates
        $strScheduleStart = strtotime( $this->start_date );

        //Event begin and end dates
        $strEventStart = strtotime( $event->{'start_time'} );
        $strEventEnd   = strtotime( $event->{'end_time'}   );

        // Event duration in hours
        $eventHourDuration = ($strEventEnd - $strEventStart) /(3600);

        // Total hours of event divide by quatity of hours step column multipled for size of column
        $width = ( round($eventHourDuration) / self::COLUMNHOURVAL ) * $this->getColumnSize();

        $marginBegin = ( $strEventStart - $strScheduleStart ) / (3600);
        $marginLeft = (( $marginBegin / self::COLUMNHOURVAL ) * $this->getColumnSize()) - self::ADJUST_MARGIN[$this->size];

        $div->{'style'} = "width:{$width}px;margin-left:{$marginLeft}px;";

        if (!empty($event->{'color'}))
        {
            if ($event->{'percent'})
            {
                $div->{'style'} .= "background: linear-gradient(90deg, {$event->{'color'}} {$event->{'percent'}}%, {$event->{'color'}}40 {$event->{'percent'}}%)";
            }
            else
            {
                $div->{'style'} .= "background:" . $event->{'color'};
            }
        }
        else if ($event->{'percent'})
        {
            $div->{'style'} .= "background: linear-gradient(90deg, #9e9e9e {$event->{'percent'}}%, #9e9e9e40 {$event->{'percent'}}%)";
        }

        if ($this->remove_space)
        {
            $div->{'style'} .= ';display:block;';
        }

        return $div;
    }

    /**
     * Render gantt
     */
    private function renderGantt()
    {
        $schedule = new TElement( 'div' );
        $schedule->{'class'} = "fixed-table-" . $this->size;
        $schedule->add( $this->renderAside() );
        $schedule->add( $this->renderTimeTable() );

        return $schedule;
    }
    
    /**
     * Enable view_mode button
     */
    function enableViewModeButton($with_label = TRUE, $with_icon = TRUE, $label = NULL, $icon = NULL)
    {
        $this->view_mode_button = TRUE;
        $this->view_mode_options = [$with_label, $with_icon, $label, $icon];
    }
    
    /**
     * Enable size_mode button
     */
    function enableSizeModeButton($with_label = TRUE, $with_icon = TRUE, $label = NULL, $icon = NULL)
    {
        $this->size_mode_button = TRUE;
        $this->size_mode_options = [$with_label, $with_icon, $label, $icon];
    }
    
    /**
     *
     */
    public function show()
    {
        if ($this->view_mode_button)
        {
            $current_view_mode = $this->getViewMode();
            
            $reloadAction = clone $this->reloadAction;
            $reloadAction->setParameter('register_state', 'false');
            
            // header actions (change view mode)
            $dropdown1 = new TDropDown( $this->view_mode_options[0] ? ($this->view_mode_options[2] ?? AdiantiCoreTranslator::translate('View mode')) : '',
                                        $this->view_mode_options[1] ? ($this->view_mode_options[3] ?? 'fa:eye' ): '');
            $dropdown1->setButtonClass('btn btn-default waves-effect dropdown-toggle');
            $dropdown1->addAction( AdiantiCoreTranslator::translate('Months'),           $reloadAction->cloneWithParameters(['view_mode' => 'MODE_MONTHS']), ($current_view_mode == 'MODE_MONTHS') ? 'fas:circle' : 'far:circle' );
            $dropdown1->addAction( AdiantiCoreTranslator::translate('Months with days'), $reloadAction->cloneWithParameters(['view_mode' => 'MODE_MONTHS_WITH_DAY']), ($current_view_mode == 'MODE_MONTHS_WITH_DAY') ? 'fas:circle' : 'far:circle' );
            $dropdown1->addAction( AdiantiCoreTranslator::translate('Days'),             $reloadAction->cloneWithParameters(['view_mode' => 'MODE_DAYS']), ($current_view_mode == 'MODE_DAYS') ? 'fas:circle' : 'far:circle' );
            $dropdown1->addAction( AdiantiCoreTranslator::translate('Days with hours'),  $reloadAction->cloneWithParameters(['view_mode' => 'MODE_DAYS_WITH_HOUR']), ($current_view_mode == 'MODE_DAYS_WITH_HOUR') ? 'fas:circle' : 'far:circle' );
            
            $this->addHeaderWidget( $dropdown1 );
        }
        
        if ($this->size_mode_button)
        {
            $current_size_mode = $this->getSizeMode();
            
            $reloadAction = clone $this->reloadAction;
            $reloadAction->setParameter('register_state', 'false');
            
            // header actions (change zoom mode)
            $dropdown2 = new TDropDown( $this->size_mode_options[0] ? ($this->size_mode_options[2] ?? AdiantiCoreTranslator::translate('Zoom mode') ): '',
                                        $this->size_mode_options[1] ? ($this->size_mode_options[3] ?? 'fa:search') : '');
            $dropdown2->setButtonClass('btn btn-default waves-effect dropdown-toggle');
            $dropdown2->addAction( AdiantiCoreTranslator::translate('Large'),     $reloadAction->cloneWithParameters(['size_mode' => 'lg']), ($current_size_mode == 'lg') ? 'fas:circle' : 'far:circle' );
            $dropdown2->addAction( AdiantiCoreTranslator::translate('Medium'),    $reloadAction->cloneWithParameters(['size_mode' => 'md']), ($current_size_mode == 'md') ? 'fas:circle' : 'far:circle' );
            $dropdown2->addAction( AdiantiCoreTranslator::translate('Small'),     $reloadAction->cloneWithParameters(['size_mode' => 'sm']), ($current_size_mode == 'sm') ? 'fas:circle' : 'far:circle' );
            $dropdown2->addAction( AdiantiCoreTranslator::translate('Condensed'), $reloadAction->cloneWithParameters(['size_mode' => 'xs']), ($current_size_mode == 'xs') ? 'fas:circle' : 'far:circle' );
            
            $this->addHeaderWidget( $dropdown2 );
        }
        
        $panel = new TElement( 'div' );
        $panel->{'id'} = $this->id;
        $panel->{'class'} = 'panel panel-default tgantt';
        $panel->add( $this->renderHeader() );
        $panel->add( $this->renderGantt() );
        $panel->show();

        $minutesStep = '0';
        $pixelValue = '0';
        $update_action_string = '';
        $day_click_action_string = '';

        if ($this->dragEvent)
        {
            $minutesStep = $this->minutesStep;
            $pixelValue  = $this->getPixelValue();
            $this->updateAction->setParameter('view_mode', $this->view_mode);
            $this->updateAction->setParameter('size_mode', $this->size);
            $update_action_string = $this->updateAction->serialize(FALSE);
        }

        if ($this->dayClickAction)
        {
            $this->dayClickAction->setParameter('view_mode', $this->view_mode);
            $this->dayClickAction->setParameter('size_mode', $this->size);
            $day_click_action_string = $this->dayClickAction->serialize(TRUE);
        }
        
        TScript::create("tgantt_start( '#{$this->id}', '{$day_click_action_string}', '{$minutesStep}','{$pixelValue}', '{$update_action_string}');");
    }
}