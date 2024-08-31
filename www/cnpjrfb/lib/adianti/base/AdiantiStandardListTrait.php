<?php
namespace Adianti\Base;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Control\TAction;
use Adianti\Database\TTransaction;

use Exception;

/**
 * Standard List Trait
 *
 * @version    7.6
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
trait AdiantiStandardListTrait
{
    protected $totalRow;
    
    use AdiantiStandardCollectionTrait;
    use AdiantiStandardListExportTrait;
    
    /**
     * Enable total row
     */
    public function enableTotalRow()
    {
        $this->setAfterLoadCallback( function($datagrid, $information) {
            $tfoot = new TElement('tfoot');
            $tfoot->{'class'} = 'tdatagrid_footer';
            $row = new TElement('tr');
            $tfoot->add($row);
            $datagrid->add($tfoot);
            
            $row->{'style'} = 'height: 30px';
            $cell = new TElement('td');
            $cell->add( $information['count'] . ' ' . AdiantiCoreTranslator::translate('Records'));
            $cell->{'colspan'} = $datagrid->getTotalColumns();
            $cell->{'style'} = 'text-align:center';
            
            $row->add($cell);
        });
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            // open a transaction with database
            TTransaction::open($this->database);
            
            // instantiates object {ACTIVE_RECORD}
            $class = $this->activeRecord;
            
            // instantiates object
            $object = new $class($key);
            
            // deletes the object from the database
            $object->{$field} = $value;
            $object->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record updated'));
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
     * Ask before delete record collection
     */
    public function onDeleteCollection( $param )
    {
        $data = $this->formgrid->getData(); // get selected records from datagrid
        $this->formgrid->setData($data); // keep form filled
        
        if ($data)
        {
            $selected = array();
            
            // get the record id's
            foreach ($data as $index => $check)
            {
                if ($check == 'on')
                {
                    $selected[] = substr($index,5);
                }
            }
            
            if ($selected)
            {
                // encode record id's as json
                $param['selected'] = json_encode($selected);
                
                // define the delete action
                $action = new TAction(array($this, 'deleteCollection'));
                $action->setParameters($param); // pass the key parameter ahead
                
                // shows a dialog to the user
                new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
            }
        }
    }
    
    /**
     * method deleteCollection()
     * Delete many records
     */
    public function deleteCollection($param)
    {
        // decode json with record id's
        $selected = json_decode($param['selected']);
        
        try
        {
            TTransaction::open($this->database);
            if ($selected)
            {
                // delete each record from collection
                foreach ($selected as $id)
                {
                    $class = $this->activeRecord;
                    $object = new $class;
                    $object->delete( $id );
                }
                $posAction = new TAction(array($this, 'onReload'));
                $posAction->setParameters( $param );
                new TMessage('info', AdiantiCoreTranslator::translate('Records deleted'), $posAction);
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
