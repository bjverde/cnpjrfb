<?php
namespace Adianti\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TStyle;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Exception;

/**
 * Arrow Step
 *
 * @version    7.6
 * @package    widget
 * @subpackage util
 * @author     Lucas Tomasi
 * @author     Matheus Agnes Dias
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TArrowStep extends TField implements AdiantiWidgetInterface
{
    protected $container;
    protected $items;
    protected $colorItems;
    protected $action;
    protected $selected;
    protected $width;
    protected $height;
    protected $name;
    protected $id;
    protected $color;
    protected $fontColor;
    protected $disableColor;
    protected $disableFontColor;
    protected $hideText;
    protected $fontSize;
    protected $formName;
    protected $className;
    protected $editable;

    /**
     * Constructor
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->tag = new TElement('div');

        $this->id = 'tarrowstep_' . mt_rand(1000000000, 1999999999);

        $this->className = "arrow_steps_{$name}";
        $this->class = $this->className;
        
        $this->container = new TElement('div');
        $this->container->{'class'} = 'arrow_steps';
        
        $this->colorItems = [];

        $this->name = $name;
        $this->editable = true;
        $this->hideText = false;
        $this->height = 50;
        $this->width = '100%';
        $this->fontSize = '14px';
        $this->color = "#6c757d";
        $this->fontColor = "#ffffff";
        $this->disableColor = "#e8e8e8";
        $this->disableFontColor = "#333";

        parent::add( $this->container );
    }

    /**
     * Disable field
     * 
     * @param $name name of arrow steps
     */
    public static function disableField($formName, $name)
    {
        TScript::create("tarrowstep_disable_field('{$name}');");
    }

    /**
     * Enable field
     * 
     * @param $name name of arrow steps
     */
    public static function enableField($formName, $name)
    {
        TScript::create("tarrowstep_enable_field('{$name}');");
    }


    /**
     * Clear currents item on steps
     * 
     * @param $name name of arrow steps
     */
    public static function clearField($formName, $name)
    {
        TScript::create("tarrowstep_clear('{$name}');");
    }

    /**
     * Define current item on steps
     * 
     * @param $name name of arrow steps
     * @param $value value current
     */
    public static function defineCurrent($name, $value)
    {
        TScript::create("tarrowstep_set_current('{$name}', '{$value}');");
    }
    
    /**
     * Define if the field is editable
     * @param $editable A boolean
     */
    public function setEditable($editable)
    {
        $this->editable= $editable;
    }

    /**
     * Returns if the field is editable
     * @return A boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            return $_POST[$this->name] ? $_POST[$this->name] : null;
        }

        return null;
    }

    /**
     * Set form name
     */
    public function setFormName($name)
    {
        $this->formName = $name;
    }

    /**
     * Set name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value current step
     */
    public function setValue($value)
    {
        $this->setCurrentKey($value);
    }

    /**
     * Get value current step
     */
    public function getValue()
    {
        return $this->getCurrent();
    }

    /**
     * Set hide text
     * @param $hide bool
     */
    public function setHideText(bool $hide = true)
    {
        $this->hideText = $hide;
    }

    /**
     * Set font size
     * @param $size string to color 
     */
    public function setFontSize($fontSize)
    {
        $fontSize = (strstr($fontSize, '%') !== FALSE) ? $fontSize : "{$fontSize}px";

        $this->fontSize = $fontSize;
    }

    /**
     * Set color arrows
     * @param $color string to color 
     * @param $fontColor string to color font
     */
    public function setFilledColor(string $color, $fontColor = null)
    {
        $this->color = $color;

        if ($fontColor)
        {
            $this->fontColor = $fontColor;
        }
    }

    /**
     * Set font color arrows
     * @param $color string to color 
     */
    public function setFilledFontColor(string $fontColor)
    {
        $this->fontColor = $fontColor;
    }

    /**
     * Set color arrows
     * @param $color string to color 
     * @param $fontColor string to color font
     */
    public function setUnfilledColor(string $color, $fontColor = null)
    {
        $this->disableColor = $color;

        if ($fontColor)
        {
            $this->disableFontColor = $fontColor;
        }
    }

    /**
     * Set color arrows
     * @param $fontColor string to color font
     */
    public function setUnfilledFontColor(string $color)
    {
        $this->disableFontColor = $color;
    }
    
    /**
     * Set width
     * @param $width int|float to width
     */
    public function setWidth($width)
    {
        if (is_numeric($width))
        {
            $this->width = $width . 'px';
        }
        else
        {
            $this->width = $width;
        }
    }

    /**
     * Get width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set sizes
     * @param $width
     * @param $height
     */
    public function setSize($width, $height = null)
    {
        if ($height)
        {
            $this->setHeight($height);
        }

        $this->setWidth($width);
    }

    /**
     * Set height arrows
     * @param $height int|float to height 
     */
    public function setHeight($height)
    {
        if (! is_numeric($height))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $height, __METHOD__));
        }

        $this->height = $height;
    }

    /**
     * Get heigth
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get size
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Add an item
     * @param $title    Item title
     * @param $id       Item id
     * @param $color    Item color
     */
    public function addItem($title, $id = null, $color = null)
    {
        if ($id)
        {
            $this->items[$id] = $title;
            $this->colorItems[$id] = $color;
        }
        else
        {
            $this->items[] = $title;
            $this->colorItems[] = $color;
        }
    }

    /**
     * Set color items
     * @param $colorItems  Items
     */
    public function setColorItems($colorItems)
    {
        $this->colorItems = $colorItems;
    }
    
    /**
     * Set items
     * @param $item  Items
     */
    public function setItems($items)
    {
        if ($items)
        {
            $this->items = [];

            foreach($items as $key => $title)
            {
                $this->items[$key] = $title;
            }
        }
    }

    /**
     * Add items
     * @param $item  Items
     */
    public function addItems($items)
    {
        if ($items)
        {
            foreach($items as $key => $title)
            {
                $this->items[$key] = $title;
            }
        }
    }
    
    /**
     * Get items
     * 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get item
     * 
     */
    public function getItem($key)
    {
        return ! empty($this->items[$key]) ? $this->items[$key] : NULL;
    }

    /**
     * Set action
     * 
     * @param $action Action
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     * 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Select current item
     */
    public function setCurrentKey($key)
    {
        $this->selected = $key;
    }

    /**
     * Get current item
     */
    public function getCurrent()
    {
        return $this->selected;
    }

    /**
     * Select current item
     */
    public function setCurrent($title)
    {
        if (in_array($title, $this->items))
        {
            $this->selected = array_search($title, $this->items);
        }
    }
    
    /**
     * Get action in serialized way
     */
    private function getSerializedAction($key, $value, $selected = false)
    {
        $this->action->setParameter('value', $value);
        $this->action->setParameter('__selected', $selected);

        if (!TForm::getFormByName($this->formName) instanceof TForm)
        {
            return "__adianti_load_page('{$this->action->serialize(true)}');";
        }
        else
        {
            $string_action = $this->action->serialize(FALSE);

            $key = $this->id ."_" . str_replace([' ', '-'], ['', ''], $key);

            return "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$key}', 'callback')";
        }
    }

    /**
     * Make a html of item
     */
    private function makeItem($key, $value, $selected = false)
    {
        $div = new TElement('div');
        $div->{'class'}  = 'step';
        $div->{'data-key'} = $key;
        $div->{'class'} .= $selected && ! is_null($this->selected) ? ' current ' : '';

        $input = new TElement('input');
        $input->{'type'} = 'hidden';
        $input->{'id'} = $this->id ."_" . str_replace([' ', '-'], ['', ''], $key);
        $input->{'value'} = $key;

        $div->add( $input );
        $this->container->add( $div );
        
        $span = new TElement('span');
        $span->add($value);
        
        if ($this->action)
        {
            $div->{'onclick'} = $this->getSerializedAction($key, $value, $selected);
        }
        
        if (! $this->hideText)
        {
            $div->add($span);
            $this->style = 'overflow-x: auto;';
        }
        else
        {
            $div->title = $value;
        }
    }

    /**
     * Make component style
     */
    private function makeStyle()
    {
        $size1 = $this->height/2 . 'px';
        $size2 = $this->height/3 . 'px';

        $styles = new TElement('style');
        $styles->type = 'text/css';
        $styles->media = 'screen';

        $styleClassHeight = new TStyle($this->className.' .arrow_steps');
        $styleClassHeight->height = $this->height .'px';
        $styles->add($styleClassHeight);

        $styleClassBackground = new TStyle($this->className.'::-webkit-scrollbar-thumb,.'.$this->className.' .step.current,.'.$this->className.' .step.preview-current');
        $styleClassBackground->{"background-color"} = $this->color;
        $styleClassBackground->{"color"} = $this->fontColor;
        $styles->add($styleClassBackground);

        $styleClassBackgroundDisable = new TStyle($this->className.' .step');
        $styleClassBackgroundDisable->{"background-color"} = $this->disableColor;
        $styleClassBackgroundDisable->{"color"} = $this->disableFontColor;
        $styleClassBackgroundDisable->{"font-size"} = $this->fontSize;
        $styleClassBackgroundDisable->{"padding-left"} = "{$size2}";
        $styles->add($styleClassBackgroundDisable);
        
        $styleClassBorder = new TStyle($this->className.' .step.current:after,.'.$this->className.' .step.preview-current:after');
        $styleClassBorder->{"border-left-color"} = $this->color;
        $styleClassBorder->{"border-left-width"} = $size2;
        $styles->add($styleClassBorder);

        $styleClassBorderHeight = new TStyle($this->className.' .step:after,.'.$this->className.' .step:before');
        $styleClassBorderHeight->{"border-top-width"} =  $size1;
        $styleClassBorderHeight->{"border-bottom-width"} = $size1;
        $styleClassBorderHeight->{"right"} = "calc( -{$size2} + 0.5px)";
        $styleClassBorderHeight->{"border-left-width"} = $size2;
        $styleClassBorderHeight->{"border-left-color"} = $this->disableColor;
        $styles->add($styleClassBorderHeight);
        
        $styleClassBorderStepBefore = new TStyle($this->className.' .step:before');
        $styleClassBorderStepBefore->{'border-left-width'} = $size2;
        $styleClassBorderStepBefore->{"border-left-color"} = 'white';
        $styles->add($styleClassBorderStepBefore);

        $styleClassBorderSpanBefore = new TStyle($this->className.' span:before');
        $styleClassBorderSpanBefore->{'left'} = "-{$size2}";
        $styles->add($styleClassBorderSpanBefore);

        if (! empty($this->colorItems))
        {
            foreach($this->colorItems as $key => $color)
            {
                $styleClassBackgroundStep = new TStyle("{$this->className} .step.current[data-key=\"{$key}\"],.{$this->className} .step.preview-current[data-key=\"{$key}\"]");
                $styleClassBackgroundStep->{"background-color"} = $color;
                $styles->add($styleClassBackgroundStep);
                
                $styleClassBackgroundStepArrow = new TStyle("{$this->className} .step.current[data-key=\"{$key}\"]:after,.{$this->className} .step.preview-current[data-key=\"{$key}\"]:after");
                $styleClassBackgroundStepArrow->{"border-left-color"} = $color;
                $styles->add($styleClassBackgroundStepArrow);
            }
        }

        parent::add($styles);
    }

    /**
     * Show component
     */
    public function show()
    {
        $this->makeStyle();

        if ($this->items)
        {
            $selected = true;

            foreach($this->items as $key => $value)
            {
                $this->makeItem($key, $value, $selected);

                if ($this->selected == $key)
                {
                    $selected = false;
                }
            }
        }

        $input = new TElement('input');
        $input->{'type'} = 'hidden';
        $input->{'widget'} = 'tarrowstep';
        $input->{'id'} = $this->id;
        $input->{'name'} = $this->name;
        $input->{'value'} = $this->selected;

        parent::add($input);

        if (! $this->editable)
        {
            $this->className .= ' disabled ';
        }

        if ($this->width)
        {
            $this->style = 'width: ' . $this->width;
        }

        parent::setProperty('class', $this->className . " div_arrow_steps");
        
        TScript::create("tarrowstep_start('{$this->name}');");

        parent::show();
    }
}
