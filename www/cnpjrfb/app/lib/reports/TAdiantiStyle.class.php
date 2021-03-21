<?php
/**
 * Style
 *
 * @version    7.0
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAdiantiStyle
{
    private $name;           // stylesheet name
    private $properties;     // properties
    static  private $loaded; // array of loaded styles
    
    /**
     * Class Constructor
     * @param $mame Name of the style
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * Executed whenever a property is assigned
     * @param  $name    = property's name
     * @param  $value   = property's value
     */
    public function __set($name, $value)
    {
        // store the assigned tag property
        $this->properties[$name] = $value;
    }
    
    /**
     * Check if a property is assigned
     * @param  $name    = property's name
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }
    
    /**
     * Executed whenever a property is required
     * @param  $name    = property's name
     */
    public function __get($name)
    {
        if (isset($this->properties[$name]))
        {
            return $this->properties[$name];
        }
    }
    
    /**
     * Show the style
     */
    public function show()
    {
        // check if the style is already loaded
        if (!isset(self::$loaded[$this->name]))
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
                    $name = str_replace('_', '-', $name);
                    $style.= "        {$name}: {$value};\n";
                }
            }
            $style.= "    }\n";
            
            echo $style;
            
            // mark the style as loaded
            // comentado por que no cliente ele sรณ exibe o estilo uma vez
            // self::$loaded[$this->name] = TRUE;
        }
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
}
?>