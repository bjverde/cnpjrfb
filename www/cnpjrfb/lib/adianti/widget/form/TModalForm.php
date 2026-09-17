<?php
namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\AdiantiFormInterface;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Util\TActionLink;

use stdClass;
use Exception;

/**
 * Modal form constructor for Adianti Framework
 *
 * @version    8.6
 * @package    wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TModalForm implements AdiantiFormInterface
{
    private $id;
    private $title;
    private $decorated;
    private $modal;
    private $rows;
    private $actions;
    private $modal_class;
    private $footer_actions;
    
    /**
     * Constructor method
     * @param $name form name
     */
    public function __construct($name = 'my_form')
    {
        $this->decorated = new TForm($name);
        $this->id        = 'mform_' . mt_rand(1000000000, 1999999999);
    }
    
    /**
     * Returns form id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Add a form title
     * @param $title Form title
     */
    public function setFormTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Return the form title
     */
    public function getFormTitle()
    {
        return $this->title;
    }
    
    /**
     * Redirect calls to decorated object
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    
    /**
     * Redirect assigns to decorated object
     */
    public function __set($property, $value)
    {
        return $this->decorated->$property = $value;
    }
    
    /**
     * Set form name
     * @param $name Form name
     */
    public function setName($name)
    {
        return $this->decorated->setName($name);
    }
    
    /**
     * Get form name
     */
    public function getName()
    {
        return $this->decorated->getName();
    }
    
    /**
     * Add form field
     * @param $field Form field
     */
    public function addField(AdiantiWidgetInterface $field)
    {
        return $this->decorated->addField($field);
    }
    
    /**
     * Del form field
     * @param $field Form field
     */
    public function delField(AdiantiWidgetInterface $field)
    {
        return $this->decorated->delField($field);
    }
    
    /**
     * Set form fields
     * @param $fields Array of Form fields
     */
    public function setFields($fields)
    {
        return $this->decorated->setFields($fields);
    }
    
    /**
     * Return form field
     * @param $name Field name
     */
    public function getField($name)
    {
        return $this->decorated->getField($name);
    }
    
    /**
     * Return form fields
     */
    public function getFields()
    {
        return $this->decorated->getFields();
    }
    
    /**
     * Clear form
     */
    public function clear( $keepDefaults = FALSE )
    {
        return $this->decorated->clear( $keepDefaults );
    }
    
    /**
     * Set form data
     * @param $object Data object
     */
    public function setData($object)
    {
        return $this->decorated->setData($object);
    }
    
    /**
     * Get form data
     * @param $class Object type of return data
     */
    public function getData($class = 'StdClass')
    {
        return $this->decorated->getData($class);
    }
    
    /**
     * Return form actions
     */
    public function getActions()
    {
        return $this->actions;
    }
    
    /**
     * Validate form data
     */
    public function validate()
    {
        return $this->decorated->validate();
    }
    
    /**
     * Add row
     * @param $label_string Field label
     * @param $field Field object
     * @param $floating turn on/off floating labels
     */
    public function addRowField($label_string, AdiantiWidgetInterface $field, $floating = false)
    {
        $this->decorated->addField($field);
        
        $label = new TLabel($label_string);
        $label->{'for'} = $field->getId();
        $field->{'class'} .= ' form-control rounded-3';
        
        $this->rows[] = ['type' => 'field',
                         'data' => [ 'label' => $label, 'field' => $field, 'floating' => $floating ]];
    }
    
    /**
     * Add a row content
     */
    public function addRowContent($content)
    {
        $this->rows[] = ['type' => 'content', 'data' => $content];
    }
    
    /**
     * Add a form action
     * @param $label Button label
     * @param $action Button action
     * @param $icon Button icon
     */
    public function addAction($label, TAction $action, $icon = 'fa:save', $name = null)
    {
        $label_info = ($label instanceof TLabel) ? $label->getValue() : $label;
        $name   = $name ?? 'btn_'.strtolower(str_replace(' ', '_', $label_info));
        
        $button = new TButton($name);
        $button->{'class'} = 'w-100 mb-2 btn btn-lg rounded-3 btn-primary';
        $this->decorated->addField($button);
        
        // define the button action
        $button->setAction($action, $label);
        $button->setImage($icon);
        
        $this->actions[] = $button;
        return $button;
    }
    
    /**
     * Add a form action
     * @param $label Button label
     * @param $action Button action
     * @param $icon Button icon
     */
    public function addFooterAction($label, TAction $action, $icon = 'fa:save', $name = null)
    {
        $label_info = ($label instanceof TLabel) ? $label->getValue() : $label;
        $name   = $name ?? 'btn_'.strtolower(str_replace(' ', '_', $label_info));
        
        $button = new TButton($name);
        $button->{'class'} = 'w-100 py-2 mb-2 btn btn-outline-secondary rounded-3';
        $this->decorated->addField($button);
        
        // define the button action
        $button->setAction($action, $label);
        $button->setImage($icon);
        
        $this->footer_actions[] = $button;
        return $button;
    }
    
    /**
     * Add a form action
     * @param $label Button label
     * @param $action Button action
     * @param $icon Button icon
     */
    public function addFooterActionLink($label, TAction $action, $icon = 'fa:save')
    {
        $label_info = ($label instanceof TLabel) ? $label->getValue() : $label;
        $button = new TActionLink($label_info, $action, null, null, null, $icon);
        $button->{'class'} = 'w-100 py-2 mb-2 btn btn-outline-secondary rounded-3';
        //$this->decorated->addField($button);
        
        // define the button action
        //$button->setAction($action, $label);
        //$button->setImage($icon);
        
        $this->footer_actions[] = $button;
        return $button;
    }
    
    /**
     * Set modal class
     */
    public function setModalClass($class)
    {
        $this->modal_class = $class;
    }
    
    /**
     * Render DOM
     */
    public function render()
    {
        $this->modal = new TElement('div');
        $this->modal->{'id'} = $this->id;
        $this->modal->{'class'} = $this->modal_class ?? 'modal modal-sheet position-static d-block p-1 py-md-5';
        $this->modal->{'role'} = 'dialog';
        $this->modal->{'tabindex'} = '-1';
        
        $dialog = new TElement('div');
        $dialog->{'class'} = 'modal-dialog';
        $dialog->{'role'} = 'document';
        $this->modal->add($dialog);
        
        $content = new TElement('div');
        $content->{'class'} = 'modal-content rounded-4 shadow';
        $dialog->add($content);
        
        $header = new TElement('div');
        $header->{'class'} = 'modal-header p-5 pb-4 border-bottom-0';
        $content->add($header);
        
        if (is_object($this->title))
        {
            $header->add($this->title);
        }
        else
        {
            $title = new TElement('h1');
            $title->{'class'} = 'fw-bold mb-0 fs-2';
            $header->add($title);
            $title->add($this->title);
        }
        
        $body = new TElement('div');
        $body->{'class'} = 'modal-body p-5 pt-0';
        $content->add($body);
        
        $body->add($this->decorated);
        
        if ($this->rows)
        {
            foreach ($this->rows as $row)
            {
                if ($row['type'] == 'field')
                {
                    $wrapper = new TElement('div');
                    $wrapper->{'style'} = 'position:relative';
                    
                    if ($row['data']['floating'])
                    {
                        $wrapper->{'class'} = 'form-floating mb-3';
                        
                        $wrapper->add($row['data']['field']);
                        $wrapper->add($row['data']['label']);
                    }
                    else
                    {
                        $wrapper->{'class'} = 'mb-3';
                        
                        $wrapper->add($row['data']['label']);
                        $wrapper->add($row['data']['field']);
                    }
                }
                else if ($row['type'] == 'content')
                {
                    $wrapper = new TElement('div');
                    $wrapper->{'class'} = 'mb-3';
                    $wrapper->add($row['data']);
                }
                
                $this->decorated->add($wrapper);
            }
        }
        
        if ($this->actions)
        {
            foreach ($this->actions as $action)
            {
                $this->decorated->add($action);
            }
        }
        
        if ($this->footer_actions)
        {
            $hr = new TElement('hr');
            $hr->{'class'} = 'my-4';
            $this->decorated->add($hr);
            
            foreach ($this->footer_actions as $action)
            {
                $this->decorated->add($action);
            }
        }
    }
    
    /**
     * Show form
     */
    public function show()
    {
        if (empty($this->modal))
        {
            $this->render();
        }
        
        $this->modal->show();
    }
}
