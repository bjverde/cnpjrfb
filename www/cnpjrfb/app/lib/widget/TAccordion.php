<?php
/**
 * TAccordion Container
 * Copyright (c) 2006-2010 Pablo Dall'Oglio
 * @author  Pablo Dall'Oglio <pablo [at] adianti.com.br>
 * @version 2.0, 2007-08-01
 */
class TAccordion extends TElement
{
    protected $elements;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->id = 'taccordion_' . uniqid();
        $this->class = 'accordion';
        $this->elements = [];
    }
    
    /**
     * Add an object to the accordion
     * @param $title  Title
     * @param $objeto Content
     */
    public function appendPage($title, $object)
    {
        $this->elements[] = array($title, $object);
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        foreach ($this->elements as $key => $child)
        {
            $id = uniqid();
            
            $item = new TElement('div');
            $item->class = 'accordion-item';
            
            $header = new TElement('h2');
            $header->class = 'accordion-header';
            $item->add($header);
            
            $button = new TElement('button');
            $button->class = 'accordion-button collapsed';
            $button->type  = 'button';
            $button->{'data-bs-toggle'} ="collapse";
            $button->{'data-bs-target'} = "#".$id;
            $button->add($child[0]);
            $header->add($button);
            
            $content = new TElement('div');
            $content->id = $id;
            $content->class = 'accordion-collapse collapse';
            
            $body = new TElement('div');
            $body->class="accordion-body";
            $body->add($child[1]);
            $content->add($body);
            
            $item->add($content);
            
            parent::add($item);
        }
        
        parent::show();
    }
}
