<?php
/**
 * SystemDataBrowser
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemDataBrowser extends TPage
{
    private $datagrid;
    private $pageNavigation;
    private $pageAction;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        // Define the id of target container
        $this->adianti_target_container = 'data_browser_container';
        
        // creates the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        // creates the pagination
        $this->pageAction = new TAction(array($this, 'onLoad'));
        $this->pageAction->setParameter('register_state', 'false');
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction($this->pageAction);
    }
    
    /**
     * Load data
     */
    public function onLoad($param)
    {
        try
        {
            $limit    = 10;
            $database = isset($param['database']) ? $param['database'] : TSession::getValue(__CLASS__ . '_database');
            $table    = isset($param['table'])    ? $param['table']    : TSession::getValue(__CLASS__ . '_table');
            
            // store database and table into session
            TSession::setValue(__CLASS__ . '_database', $database);
            TSession::setValue(__CLASS__ . '_table',    $table);
            
            // creates the select criteria
            $criteria = new TCriteria;
            if (isset($param['order']))
            {
                $criteria->setProperty('order',     $param['order']);
            }
            if (isset($param['direction']))
            {
                $criteria->setProperty('direction', $param['direction']);
            }
            if (isset($param['offset']))
            {
                $criteria->setProperty('offset',    (int) $param['offset']);
            }
            $criteria->setProperty('limit',     $limit);
            
            if (!empty($param['filter_value']))
            {
                $this->pageAction->setParameter('filter_name',  $param['filter_name']);
                $this->pageAction->setParameter('filter_value', $param['filter_value']);
                $this->pageAction->setParameter('filter_type',  $param['filter_type']);
                
                if ($param['filter_type'] == '=')
                {
                    $criteria->add(new TFilter($param['filter_name'], $param['filter_type'], $param['filter_value']));
                }
                else
                {
                    $criteria->add(new TFilter($param['filter_name'], $param['filter_type'], '%'.$param['filter_value'].'%'));
                }
            }
            
            // open transaction
            TTransaction::open( $database );
            $conn = TTransaction::get();
            
            $info = TTransaction::getDatabaseInfo();
            // count records
            $where_string = $criteria->dump() ? 'WHERE '.$criteria->dump() : '';
            $count_row = $conn->query( "SELECT count(*) as COUNT FROM {$table} ". $where_string)->fetchObject();
            $count = isset($count_row->COUNT) ? $count_row->COUNT : $count_row->count;
            
            // run the main query
            $sql = new TSqlSelect;
            $sql->setCriteria($criteria);
            $sql->addColumn('*');
            $sql->setEntity($table);
            $result = $conn->query( $sql->getInstruction() );
            $first_row = $result->fetch();
            
            $i = 0;
            if ($first_row)
            {
                // define datagrid columns based on the first row
                foreach ($first_row as $key => $value)
                {
                    if (is_string($key) && $key !== '__ROWNUMBER__')
                    {
                        // create column
                        $col = new TDataGridColumn($key, $key, 'left');
                        $this->datagrid->addColumn($col);
                        
                        // create order action
                        $action = new TAction( [$this, 'onLoad'] );
                        $action->setParameters($param); // keep other parameters (pagination)
                        $action->setParameter('order', $key);
                        $col->setAction($action);
                    }
                }
                
                // create the datagrid model
                $this->datagrid->createModel();
                
                // create filter row
                $body = $this->datagrid->getBody();
                $tr = new TElement('tr');
                $tr->style = 'background: #e8e8e8';
                $body->add($tr);
                
                // add first data row
                $this->datagrid->addItem( (object) $first_row );
                
                // add other rows
                while ($row = $result->fetch())
                {
                    $this->datagrid->addItem( (object) $row );
                }
                
                // use the first row to create input filters
                foreach ($first_row as $key => $value)
                {
                    if (is_string($key) && $key !== '__ROWNUMBER__')
                    {
                        $type = (is_numeric($value)) ? '=' : 'like';
                        
                        // create the input filter
                        $entry = new TEntry($key);
                        $entry->exitOnEnter();
                        $entry->setSize('100%');
                        $entry->class = 'input-data-search';
                        
                        // define the filter action
                        $request = $param;
                        $request['class']  = 'SystemDataBrowser';
                        $request['method'] = 'onLoad';
                        $request['offset'] = '0';
                        $request['page']   = '1';
                        $request['first_page']   = '1';
                        $request['register_state'] = 'false';
                        $request['filter_type'] = $type;
                        $request['filter_name'] = $key;
                        
                        $url = http_build_query($request);
                        $entry->onblur = "__adianti_load_page('index.php?{$url}&filter_value='+$(this).val())";
                        
                        // keep the field filled
                        if (isset($param['filter_name']) && $param['filter_name'] == $key)
                        {
                            $entry->setValue($param['filter_value']);
                        }
                        
                        $cell = new TElement('td');
                        $tr->add($cell);
                        $cell->add($entry);
                    }
                }
            }
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // create panel group around datagrid
            $panel = new TPanelGroup($database . ' > ' . $table);
            $button = $panel->addHeaderActionLink("SQL", new TAction(["SystemSQLPanel", "onLoad"], ["database" => $database, "table" => $table]), "fa:code");
            $button->class = "btn btn-primary";
            
            $panel->add($this->datagrid);
            $panel->addFooter($this->pageNavigation);
            $panel->getBody()->style = 'overflow-x:auto';
            parent::add($panel);
            
            TTransaction::close();
            
            // fix the field height
            TScript::create("$('#data_browser_container .panel').css('min-height', $(window).height()-133);");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
