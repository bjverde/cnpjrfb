<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Form\TMultiSearch;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TUniqueSearch;
use Adianti\Widget\Wrapper\TDBMultiSearch;
use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;

use Exception;

/**
 * DBUnique Search Widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDBUniqueSearch extends TDBMultiSearch implements AdiantiWidgetInterface
{
    protected $database;
    protected $model;
    protected $mask;
    protected $key;
    protected $column;
    protected $items;
    protected $size;
    
    /**
     * Class Constructor
     * @param  $name Widget's name
     */
    public function __construct($name, $database, $model, $key, $value, $orderColumn = NULL, TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name, $database, $model, $key, $value, $orderColumn, $criteria);
        parent::setMaxSize(1);
        parent::setDefaultOption(TRUE);
        parent::disableMultiple();
        
        $this->tag->{'widget'} = 'tdbuniquesearch';
    }
    
    /**
     * Define the field's value
     * @param $value Current value
     */
    public function setValue($value)
    {
        if (is_scalar($value) && !empty($value))
        {
            TTransaction::open($this->database);
            $model = $this->model;
            
            $pk = constant("{$model}::PRIMARYKEY");
            
            if ($pk === $this->key) // key is the primary key (default)
            {
                // use find because it uses cache
                $object = $model::find( $value );
            }
            else // key is an alternative key (uses where->first)
            {
                $object = $model::where( $this->key, '=', $value )->first();
            }
            
            if ($object)
            {
                $description = $object->render($this->mask);
                $this->value = $value; // avoid use parent::setValue() because compat mode
                parent::addItems( [$value => $description ] );
            }
            
            TTransaction::close();
        }
        else
        {
            $this->value = $value;
        }
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        $name = str_replace(['[',']'], ['',''], $this->name);
        
        if (isset($_POST[$name]))
        {
            $val = $_POST[$name];
            
            if ($val == '') // empty option
            {
                return '';
            }
            else
            {
                return $val;
            }
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Returns the size
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Show the component
     */
    public function show()
    {
        $this->tag->{'name'}  = $this->name; // tag name
        parent::show();
    }
}
