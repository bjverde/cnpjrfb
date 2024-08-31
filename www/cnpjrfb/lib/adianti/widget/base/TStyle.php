<?php
namespace Adianti\Widget\Base;

use Adianti\Control\TPage;
use Adianti\Widget\Base\TElement;

/**
 * StyleSheet Manager
 *
 * @version    7.6
 * @package    widget
 * @subpackage base
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TStyle
{
    private $name;           // stylesheet name
    private $properties;     // properties
    static  private $loaded; // array of loaded styles
    static  private $styles;
    
    /**
     * Class Constructor
     * @param $mame Name of the style
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->properties = array();
    }
    
    /**
     * Import style
     * @param $style Style file name
     */
    public static function importFromFile($filename)
    {
        $style = new TElement('style');
        $style->add( file_get_contents( $filename ) );
        $style->show();
    }
    
    /**
     * Returns the style name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Find a style by its properties
     * @object style object
     */
    public static function findStyle($object)
    {
        if (self::$styles)
        {
            foreach (self::$styles as $stylename => $style)
            {
                if ((array)$style->properties === (array)$object->properties)
                {
                    return $stylename;
                }
            }
        }
    }
    
    /**
     * Executed whenever a property is assigned
     * @param  $name    = property's name
     * @param  $value   = property's value
     */
    public function __set($name, $value)
    {
        // replaces "_" by "-" in the property's name
        $name = str_replace('_', '-', $name);
        
        // store the assigned tag property
        $this->properties[$name] = $value;
    }
    
    /**
     * Executed whenever a property is read
     * @param  $name    = property's name
     */
    public function __get($name)
    {
        // replaces "_" by "-" in the property's name
        $name = str_replace('_', '-', $name);
        
        return $this->properties[$name];
    }
    
    /**
     * Return if the style has any content
     */
    public function hasContent()
    {
        return count($this->properties) > 0;
    }
    
    /**
     * Returns the style content
     */
    public function getContent()
    {
        // open the style
        $style = '';
        $style.= "    .{$this->name}\n";
        $style.= "    {\n";
        if ($this->properties)
        {
            // iterate the style properties
            foreach ($this->properties as $name=>$value)
            {
                $style.= "        {$name}: {$value};\n";
            }
        }
        $style.= "    }\n";
        return $style;
    }
    
    /**
     * Return the style inline code
     */ 
    public function getInline()
    {
        $style = '';
        if ($this->properties)
        {
            // iterate the style properties
            foreach ($this->properties as $name=>$value)
            {
                $name = str_replace('_', '-', $name);
                $style.= "{$name}: {$value};";
            }
        }
        
        return $style;
    }
    
    /**
     * Show the style
     */
    public function show( $inline = FALSE)
    {
        // check if the style is already loaded
        if (!isset(self::$loaded[$this->name]))
        {
            if ($inline)
            {
                echo "    <style type='text/css' media='screen'>\n";
                echo $this->getContent();
                echo "    </style>\n";
            }
            else
            {
                $style = $this->getContent();
                TPage::register_css($this->name, $style);
                // mark the style as loaded
                self::$loaded[$this->name] = TRUE;
                self::$styles[$this->name] = $this;
            }
        }
    }
}
