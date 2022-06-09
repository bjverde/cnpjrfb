<?php
namespace Adianti\Base;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TRecord;
use Adianti\Database\TFilter;
use Adianti\Database\TExpression;
use Adianti\Database\TCriteria;
use Adianti\Registry\TSession;

use Exception;
use DomDocument;

/**
 * Standard Collection Trait
 *
 * @version    7.4
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait AdiantiStandardCollectionTrait
{
    protected $filterFields;
    protected $formFilters;
    protected $filterTransformers;
    protected $loaded;
    protected $limit;
    protected $operators;
    protected $logic_operators;
    protected $order;
    protected $direction;
    protected $criteria;
    protected $transformCallback;
    protected $afterLoadCallback;
    protected $orderCommands;
    
    use AdiantiStandardControlTrait;
    
    /**
     * method setLimit()
     * Define the record limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    /**
     * Set list widget
     */
    public function setCollectionObject($object)
    {
        $this->datagrid = $object;
    }
    
    /**
     * Set order command
     */
    public function setOrderCommand($order_column, $order_command)
    {
        if (empty($this->orderCommands))
        {
            $this->orderCommands = [];
        }
        
        $this->orderCommands[$order_column] = $order_command;
    }
    
    /**
     * Define the default order
     * @param $order The order field
     * @param $directiont the order direction (asc, desc)
     */
    public function setDefaultOrder($order, $direction = 'asc')
    {
        $this->order = $order;
        $this->direction = $direction;
    }
    
    /**
     * method setFilterField()
     * Define wich field will be used for filtering
     * PS: Just for Backwards compatibility
     */
    public function setFilterField($filterField)
    {
        $this->addFilterField($filterField);
    }
    
    /**
     * method setOperator()
     * Define the filtering operator
     * PS: Just for Backwards compatibility
     */
    public function setOperator($operator)
    {
        $this->operators[] = $operator;
    }
    
    /**
     * method addFilterField()
     * Add a field that will be used for filtering
     * @param $filterField Field name
     * @param $operator Comparison operator
     */
    public function addFilterField($filterField, $operator = 'like', $formFilter = NULL, $filterTransformer = NULL, $logic_operator = TExpression::AND_OPERATOR)
    {
        $this->filterFields[] = $filterField;
        $this->operators[] = $operator;
        $this->logic_operators[] = $logic_operator;
        $this->formFilters[] = isset($formFilter) ? $formFilter : $filterField;
        $this->filterTransformers[] = $filterTransformer;
    }
    
    /**
     * method setCriteria()
     * Define the criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Define a callback method to transform objects
     * before load them into datagrid
     */
    public function setTransformer($callback)
    {
        $this->transformCallback = $callback;
    }
    
    /**
     * Define a callback method to transform objects
     * before load them into datagrid
     */
    public function setAfterLoadCallback($callback)
    {
        $this->afterLoadCallback = $callback;
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch( $param = null )
    {
        // get the search form data
        $data = $this->form->getData();
        
        if ($this->formFilters)
        {
            foreach ($this->formFilters as $filterKey => $formFilter)
            {
                $operator       = isset($this->operators[$filterKey]) ? $this->operators[$filterKey] : 'like';
                $filterField    = isset($this->filterFields[$filterKey]) ? $this->filterFields[$filterKey] : $formFilter;
                $filterFunction = isset($this->filterTransformers[$filterKey]) ? $this->filterTransformers[$filterKey] : null;
                
                // check if the user has filled the form
                if (!empty($data->{$formFilter}) OR (isset($data->{$formFilter}) AND $data->{$formFilter} == '0'))
                {
                    // $this->filterTransformers
                    if ($filterFunction)
                    {
                        $fieldData = $filterFunction($data->{$formFilter});
                    }
                    else
                    {
                        $fieldData = $data->{$formFilter};
                    }
                    
                    // creates a filter using what the user has typed
                    if (stristr($operator, 'like'))
                    {
                        $filter = new TFilter($filterField, $operator, "%{$fieldData}%");
                    }
                    else
                    {
                        $filter = new TFilter($filterField, $operator, $fieldData);
                    }
                    
                    // stores the filter in the session
                    TSession::setValue($this->activeRecord.'_filter', $filter); // BC compatibility
                    TSession::setValue($this->activeRecord.'_filter_'.$formFilter, $filter);
                    TSession::setValue($this->activeRecord.'_'.$formFilter, $data->{$formFilter});
                }
                else
                {
                    TSession::setValue($this->activeRecord.'_filter', NULL); // BC compatibility
                    TSession::setValue($this->activeRecord.'_filter_'.$formFilter, NULL);
                    TSession::setValue($this->activeRecord.'_'.$formFilter, '');
                }
            }
        }
        
        TSession::setValue($this->activeRecord.'_filter_data', $data);
        TSession::setValue(get_class($this).'_filter_data', $data);
        
        // fill the form with data again
        $this->form->setData($data);
        
        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            AdiantiCoreApplication::loadPage($class, 'onReload', ['offset'=>0, 'first_page'=>1] );
        }
        else
        {
            $this->onReload( ['offset'=>0, 'first_page'=>1] );
        }
    }
    
    /**
     * clear Filters
     */
    public function clearFilters()
    {
        TSession::setValue($this->activeRecord.'_filter_data', null);
        TSession::setValue(get_class($this).'_filter_data', null);
        $this->form->clear();
        
        if ($this->formFilters)
        {
            foreach ($this->formFilters as $filterKey => $formFilter)
            {
                TSession::setValue($this->activeRecord.'_filter', NULL); // BC compatibility
                TSession::setValue($this->activeRecord.'_filter_'.$formFilter, NULL);
                TSession::setValue($this->activeRecord.'_'.$formFilter, '');
            }
        }
    }
    
    /**
     * Load the datagrid with the database objects
     */
    public function onReload($param = NULL)
    {
        if (!isset($this->datagrid))
        {
            return;
        }
        
        try
        {
            if (empty($this->database))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (empty($this->activeRecord))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            $param_criteria = $param;
            
            // open a transaction with database
            TTransaction::open($this->database);
            
            // instancia um repositório
            $repository = new TRepository($this->activeRecord);
            $limit = isset($this->limit) ? ( $this->limit > 0 ? $this->limit : NULL) : 10;
            
            // creates a criteria
            $criteria = isset($this->criteria) ? clone $this->criteria : new TCriteria;
            if ($this->order)
            {
                $criteria->setProperty('order',     $this->order);
                $criteria->setProperty('direction', $this->direction);
            }
            

            if (is_array($this->orderCommands) && !empty($param['order']) && !empty($this->orderCommands[$param['order']]))
            {
                $param_criteria['order'] = $this->orderCommands[$param['order']];
            }
            
            $criteria->setProperties($param_criteria); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if ($this->formFilters)
            {
                foreach ($this->formFilters as $filterKey => $filterField)
                {
                    $logic_operator = isset($this->logic_operators[$filterKey]) ? $this->logic_operators[$filterKey] : TExpression::AND_OPERATOR;
                    
                    if (TSession::getValue($this->activeRecord.'_filter_'.$filterField))
                    {
                        // add the filter stored in the session to the criteria
                        $criteria->add(TSession::getValue($this->activeRecord.'_filter_'.$filterField), $logic_operator);
                    }
                }
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            if (isset($this->pageNavigation))
            {
                $this->pageNavigation->setCount($count); // count of records
                $this->pageNavigation->setProperties($param); // order, page
                $this->pageNavigation->setLimit($limit); // limit
            }
            
            if (is_callable($this->afterLoadCallback))
            {
                $information = ['count' => $count];
                call_user_func($this->afterLoadCallback, $this->datagrid, $information);
            }
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
            
            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database
            TTransaction::open($this->database);
            
            $class = $this->activeRecord;
            
            // instantiates object
            $object = new $class($key, FALSE);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload( $param );
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
