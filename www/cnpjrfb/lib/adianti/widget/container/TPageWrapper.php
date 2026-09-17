<?php
namespace Adianti\Widget\Container;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

/**
 * Page Wrapper
 *
 * @version    8.6
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TPageWrapper extends TElement
{
    protected $embed_class;
    protected $embed_method;
    protected $frame_width;
    protected $frame_height;
    protected $parameters;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->id = 'tpagewrapper_' . mt_rand(1000000000, 1999999999);
        $this->{'widget'} = 'tpagewrapper';
        $this->parameters = [];
        $this->frame_width  = '100%';
        $this->frame_height = '400';
    }
    
    /**
     * Define the wrapper id
     * @param $id Wrapper id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Define the class
     * @param $class The class name for embed
     */
    public function setClass($class)
    {
        $this->embed_class = $class;
    }
    
    /**
     * Define the method
     * @param $method The method name for embed
     */
    public function setMethod($method)
    {
        $this->embed_method = $method;
    }
    
    /**
     * Define the frame size
     * @param $width frame width
     * @param $height frame height
     */
    public function setSize($width, $height)
    {
        $this->frame_width = $width;
        $this->frame_height = $height;
    }
    
    /**
     * Adds a parameter to the action
     * @param  $parameter = parameter name
     * @param  $value = parameter value
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
    }
    
    /**
     * Keep REQUEST parameters
     */
    public function preserveRequestParameters($request_parameters = [])
    {
        if ($request_parameters)
        {
            foreach ($request_parameters as $request_parameter)
            {
                if (isset($_REQUEST[$request_parameter]))
                {
                    $this->setParameter($request_parameter, $_REQUEST[$request_parameter]);
                }
            }
        }
    }
    
    /**
     * Show element
     */
    public function show()
    {
        $this->parameters['register_state']   = 'false';
        $this->parameters['target_container'] = $this->id;
        
        if (!empty($this->embed_class))
        {
            $url = ['class' => $this->embed_class, 'method' => $this->embed_method];
            $url = array_merge($url, $this->parameters);
            
            $query = 'index.php?' . http_build_query($url);
            
            parent::add(TScript::create("__adianti_load_page('{$query}');", false, 1));
        }
        
        $this->{'style'} .= (strstr($this->frame_width, '%') !== FALSE) ? ";width:{$this->frame_width}" : ";width:{$this->frame_width}px";
        $this->{'style'} .= (strstr($this->frame_height, '%') !== FALSE) ? ";min-height:{$this->frame_height}" : ";min-height:{$this->frame_height}px";
        
        parent::show();
    }
}
