<?php
namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Form\TMultiCombo;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TCriteria;
use Adianti\Widget\Base\TScript;

use Exception;

/**
 * DBMultiCombo Widget
 *
 * @version    8.6
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TDBMultiCombo extends TDBSelect
{
    protected $id;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name, $database, $model, $key, $value, $ordercolumn = NULL, ?TCriteria $criteria = NULL)
    {
        // executes the parent class constructor
        parent::__construct($name, $database, $model, $key, $value, $ordercolumn, $criteria);
        
        $this->id   = 'tdbmulticombo_' . mt_rand(1000000000, 1999999999);
        
        $this->tag->{'class'} = 'tdbmulticombo'; // CSS
        $this->tag->{'widget'} = 'tdbmulticombo';
        
        parent::setDefaultOption(false);
        parent::disableTitles();
    }
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " tmulticombo_enable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " tmulticombo_disable_field('{$form_name}', '{$field}'); " );
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        parent::clearField($form_name, $field);
        TScript::create("tmulticombo_reload('{$form_name}', '{$field}')", true, 100);
    }
    
    /**
     * Reload combo from model data
     * @param  $formname    form name
     * @param  $field       field name
     * @param  $database    database name
     * @param  $model       model class name
     * @param  $key         table field to be used as key in the combo
     * @param  $value       table field to be listed in the combo
     * @param  $ordercolumn column to order the fields (optional)
     * @param  $criteria    criteria (TCriteria object) to filter the model (optional)
     * @param  $startEmpty  if the combo will have an empty first item
     * @param  $fire_events  if change action will be fired
     */
    public static function reloadFromModel($formname, $field, $database, $model, $key, $value, $ordercolumn = NULL, $criteria = NULL, $startEmpty = FALSE, $fire_events = TRUE)
    {
        // load items
        $items = self::getItemsFromModel($database, $model, $key, $value, $ordercolumn, $criteria);
        
        // reload combo
        TMultiCombo::reload($formname, $field, $items, $startEmpty, $fire_events);
    }
    
    /**
     * Redirects reload
     */
    public static function reload($formname, $name, $items, $startEmpty = false)
    {
        return TMultiCombo::reload($formname, $name, $items, $startEmpty);
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        parent::show();
        
        $labels = [
            'placeholder'     => AdiantiCoreTranslator::translate('Click to search'),
            'search'          => AdiantiCoreTranslator::translate('Search'),
            'searchNoResult'  => AdiantiCoreTranslator::translate('No results'),
            'selectedOptions' => ' ' . AdiantiCoreTranslator::translate('selected'),
            'selectAll'       => AdiantiCoreTranslator::translate('Select all'),
            'unselectAll'     => AdiantiCoreTranslator::translate('Clear the selection'),
            'noneSelected'    => AdiantiCoreTranslator::translate('None selected')
        ];
        $labels_json = json_encode($labels);
        TScript::create("tmulticombo_start('{$this->id}', '{$this->size}', $labels_json);");
        
        if (!parent::getEditable())
        {
            TScript::create( " tmulticombo_disable_field( '{$this->formName}', '{$this->id}' ); " );
        }
    }
}