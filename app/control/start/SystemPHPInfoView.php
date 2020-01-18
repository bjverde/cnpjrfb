<?php
/**
 * SystemPHPInfoView
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemPHPInfoView extends TPage
{
    protected $form; // formulário
    
    /**
     * método construtor
     * Cria a página e o formulário de cadastro
     */
    function __construct()
    {
        parent::__construct();
        
        ob_start();
        phpinfo();
        $content = ob_get_contents();
        $content = str_replace(',', ', ', $content);
        $content = str_replace(':/', ': /', $content);
        ob_end_clean();
        $content = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$'.'1',$content);
        
        $div = new TElement('div');
        $div->{'id'} = 'phpinfo';
        
        // Está assim (um por linha), por que o parser ignora linhas que iniciam com "#"
        $styles = '<style type="text/css">';
        $styles.= '#phpinfo pre {margin: 0px; font-family: monospace;} ';
        $styles.= '#phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;} ';
        $styles.= '#phpinfo a:hover {text-decoration: underline;} ';
        $styles.= '#phpinfo table {border-collapse: collapse;} ';
        $styles.= '#phpinfo .center {text-align: center;} ';
        $styles.= '#phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left;} ';
        $styles.= '#phpinfo .center th { text-align: center !important; } ';
        $styles.= '#phpinfo td, th { border: 1px solid gray; font-size: 75%; vertical-align: baseline; padding: 5px} ';
        $styles.= '#phpinfo h1 {font-size: 150%;} ';
        $styles.= '#phpinfo h2 {font-size: 125%;} ';
        $styles.= '#phpinfo .p {text-align: left;} ';
        $styles.= '#phpinfo .e {background-color: whiteSmoke; font-weight: bold; color: #000000;} ';
        $styles.= '#phpinfo .h {background-color: #888888; font-weight: bold; color: white;} ';
        $styles.= '#phpinfo .v {background-color: white; color: #000000;} ';
        $styles.= '#phpinfo i {color: #666666; background-color: #cccccc;} ';
        $styles.= '#phpinfo img {float: right; border: 0px;} ';
        $styles.= '#phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;} ';
        $styles.= '#phpinfo table { width: 100%;} ';
        $styles.= '#phpinfo td, th{ font-size: 90% !important;} ';
        $styles.= '#phpinfo td.e{    width: 30%;} </style>';

        $div->add($styles);
        $div->add($content);
        
        $panel = new TPanelGroup('PHP Info');
        $panel->add(new TAlert('info', 'The php.ini current location is <b>'.php_ini_loaded_file().'</b>'));
        
        $panel->add($div);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
    }
}
