<?php
namespace Adianti\Plugins\Accordion;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TStyle;

/**
 * Accordion Container
 */
class AccordionContainer extends TElement
{
    protected $elements;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->id = 'taccordion_' . uniqid();
        $this->elements = array();
    }
    
    /**
     * Add a page to the accordion
     * @param $title  Title
     * @param $object Content
     */
    public function addContent($title, $object)
    {
        $this->elements[] = array($title, $object);
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        foreach ($this->elements as $child)
        {
            $title = new TElement('button');
            $title->class = 'taccordion';
            $title->add($child[0]);
            
            $content = new TElement('div');
            $content->class = 'taccordion-content';
            $content->add($child[1]);
            
            parent::add($title);
            parent::add($content);
        }
        
        TStyle::importFromFile('vendor/adianti/plugins/src/Accordion/accordion.css');
        TScript::importFromFile('vendor/adianti/plugins/src/Accordion/accordion.js');
        
        parent::show();
    }
}
