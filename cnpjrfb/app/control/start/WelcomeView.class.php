<?php
/**
 * WelcomeView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class WelcomeView extends TPage
{
    private $html;
    
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        TPage::include_css('app/resources/styles.css');
        $this->html = new THtmlRenderer('app/resources/welcome.html');

        // define replacements for the main section
        $replace = array();
        
        // replace the main section variables
        $this->html->enableSection('main', $replace);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->html);
        
        // add the build to the page
        parent::add($container);
    }
}
