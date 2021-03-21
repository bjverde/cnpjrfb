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
use Dompdf\Dompdf;

/**
 * Standard List Trait
 *
 * @version    7.3
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait AdiantiStandardListTrait
{
    protected $totalRow;
    
    use AdiantiStandardCollectionTrait;
    
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
    
    /**
     * Export to CSV
     * @param $output Output file
     */
    public function exportToCSV($output)
    {
        $this->limit = 0;
        $objects = $this->onReload();
        
        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            TTransaction::open($this->database);
            $handler = fopen($output, 'w');
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $row = [];
                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $column_name = $column->getName();
                        
                        if (isset($object->$column_name))
                        {
                            $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                        }
                        else if (method_exists($object, 'render'))
                        {
                            $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                            $row[] = $object->render($column_name);
                        }
                    }
                    
                    fputcsv($handler, $row);
                }
            }
            fclose($handler);
            TTransaction::close();
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $output);
        }
    }
    
    /**
     * Export to XML
     * @param $output Output file
     */
    public function exportToXML($output)
    {
        $this->limit = 0;
        $objects = $this->onReload();
        
        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            TTransaction::open($this->database);
            
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->{'formatOutput'} = true;
            $dataset = $dom->appendChild( $dom->createElement('dataset') );
            
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $row = $dataset->appendChild( $dom->createElement( $this->activeRecord ) );
                    
                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $column_name = $column->getName();
                        $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);
                        
                        if (isset($object->$column_name))
                        {
                            $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                            $row->appendChild($dom->createElement($column_name_raw, $value)); 
                        }
                        else if (method_exists($object, 'render'))
                        {
                            $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                            $value = $object->render($column_name);
                            $row->appendChild($dom->createElement($column_name_raw, $value));
                        }
                    }
                }
            }
            
            $dom->save($output);
            
            TTransaction::close();
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $output);
        }
    }
    
    /**
     * Export to PDF
     * @param $output Output file
     */
    public function exportToPDF($output)
    {
        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            $this->limit = 0;
            $this->datagrid->prepareForPrinting();
            $this->onReload();
            
            // string with HTML contents
            $html = clone $this->datagrid;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            $options = new \Dompdf\Options();
            $options-> setChroot (getcwd());
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf-> loadHtml ($contents);
            $dompdf-> setPaper ('A4', 'portrait');
            $dompdf-> render ();
            
            // write and open file
            file_put_contents($output, $dompdf->output());
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $output);
        }
    }
    
    /**
     * Export to CSV
     */
    public function onExportCSV($param)
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';
            $this->exportToCSV( $output );
            TPage::openFile( $output );
        }
        catch (Exception $e)
        {
            return new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Export to XML
     */
    public function onExportXML($param)
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';
            $this->exportToXML( $output );
            TPage::openFile( $output );
        }
        catch (Exception $e)
        {
            return new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Export datagrid as PDF
     */
    public function onExportPDF($param)
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';
            $this->exportToPDF($output);
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->{'data'}  = $output;
            $object->{'type'}  = 'application/pdf';
            $object->{'style'} = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
