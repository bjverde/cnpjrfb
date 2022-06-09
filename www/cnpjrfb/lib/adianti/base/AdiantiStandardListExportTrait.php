<?php
namespace Adianti\Base;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;

use Exception;

/**
 * List Export Trait
 *
 * @version    7.4
 * @package    base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait AdiantiStandardListExportTrait
{
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
     * Export to XLS
     */
    public function onExportXLS($param)
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';
            $this->exportToXLS( $output );
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
            TTransaction::openFake($this->database);
            $handler = fopen($output, 'w');
            
            $row = [];
            foreach ($this->datagrid->getColumns() as $column)
            {
                $row[] = $column->getLabel();
            }
            fputcsv($handler, $row);
            
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
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': ' . $output);
        }
    }
    
    /**
     * Export to CSV
     * @param $output Output file
     */
    public function exportToXLS($output)
    {
        $widths = [];
        $titles = [];
        
        foreach ($this->datagrid->getColumns() as $column)
        {
            $titles[] = $column->getLabel();
            $width    = 100;
            
            if (is_null($column->getWidth()))
            {
                $width = 100;
            }
            else if (strpos($column->getWidth(), '%') !== false)
            {
                $width = ((int) $column->getWidth()) * 5;
            }
            else if (is_numeric($column->getWidth()))
            {
                $width = $column->getWidth();
            }
            
            $widths[] = $width;
        }
        
        $table = new \TTableWriterXLS($widths);
        $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
        $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');
        
        $table->addRow();
        
        foreach ($titles as $title)
        {
            $table->addCell($title, 'center', 'title');
        }
        
        $this->limit = 0;
        $objects = $this->onReload();
        
        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            TTransaction::openFake($this->database);
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $table->addRow();
                    foreach ($this->datagrid->getColumns() as $column)
                    {
                        $column_name = $column->getName();
                        $value = '';
                        if (isset($object->$column_name))
                        {
                            $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                        }
                        else if (method_exists($object, 'render'))
                        {
                            $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                            $value = $object->render($column_name);
                        }
                        
                        $table->addCell($value, 'center', 'data');
                    }
                }
            }
            $table->save($output);
            TTransaction::close();
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': ' . $output);
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
            TTransaction::openFake($this->database);
            
            $dom = new \DOMDocument('1.0', 'UTF-8');
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
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': ' . $output);
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
            throw new Exception(AdiantiCoreTranslator::translate('Permission denied') . ': ' . $output);
        }
    }
}
