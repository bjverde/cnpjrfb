<?php
/**
 * CnaesSecundarios Active Record
 * @author  <your-name-here>
 */
class CnaesSecundario extends TRecord
{
    const TABLENAME = 'cnaes_secundarios';
    const PRIMARYKEY= 'cnpj';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cnae_ordem');
        parent::addAttribute('cnae');
    }


}
