<?php
namespace Adianti\Widget\Util;

use Adianti\Widget\Util\TBreadCrumb;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Menu\TMenuParser;
use SimpleXMLElement;
use Exception;

/**
 * XMLBreadCrumb
 *
 * @version    7.1
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TXMLBreadCrumb extends TBreadCrumb
{
    private $parser;
    
    /**
     * Handle paths from a XML file
     * @param $xml_file path for the file
     */
    public function __construct($xml_file, $controller)
    {
        parent::__construct();
        
        $this->parser = new TMenuParser($xml_file);
        $paths = $this->parser->getPath($controller);
        if (!empty($paths))
        {
            parent::addHome();
            
            $count = 1;
            foreach ($paths as $path)
            {
                if (!empty($path))
                {
                    parent::addItem($path, $count == count($paths));
                    $count++;
                }
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Class ^1 not found in ^2', $controller, $xml_file));
        }
    }
    
    /**
     * Return the controller path
     */
    public function getPath($controller)
    {
        return $this->parser->getPath($controller);
    }
}
