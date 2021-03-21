<?php
namespace Adianti\Widget\Datagrid;

use Adianti\Control\TAction;

/**
 * Represents a group of Actions for datagrids
 *
 * @version    7.3
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGridActionGroup
{
    private $actions;
    private $headers;
    private $separators;
    private $label;
    private $icon;
    private $index;
    
    /**
     * Constructor
     * @param $label Action Group label
     * @param $icon  Action Group icon
     */
    public function __construct( $label, $icon = NULL)
    {
        $this->index = 0;
        $this->actions = array();
        $this->label = $label;
        $this->icon = $icon;
    }
    
    /**
     * Returns the Action Group label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Returns the Action Group icon
     */
    public function getIcon()
    {
        return $this->icon;
    }
    
    /**
     * Add an action to the actions group
     * @param $action TAction object
     */
    public function addAction(TAction $action)
    {
        $this->actions[ $this->index ] = $action;
        $this->index ++;
    }
    
    /**
     * Add a separator
     */
    public function addSeparator()
    {
        $this->separators[ $this->index ] = TRUE;
        $this->index ++;
    }
    
    /**
     * Add a header
     * @param $header Options header
     */
    public function addHeader($header)
    {
        $this->headers[ $this->index ] = $header;
        $this->index ++;
    }
    
    /**
     * Returns the actions
     */
    public function getActions()
    {
        return $this->actions;
    }
    
    /**
     * Returns the headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Returns the separators
     */
    public function getSeparators()
    {
        return $this->separators;
    }
}
