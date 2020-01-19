<?php
/**
 * Socios Active Record
 * @author  <your-name-here>
 */
class Socio extends TRecord
{
    const TABLENAME = 'socios';
    const PRIMARYKEY= 'cnpj_cpf_socio';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cnpj');
        parent::addAttribute('tipo_socio');
        parent::addAttribute('nome_socio');
        parent::addAttribute('cod_qualificacao');
        parent::addAttribute('perc_capital');
        parent::addAttribute('data_entrada');
        parent::addAttribute('cod_pais_ext');
        parent::addAttribute('nome_pais_ext');
        parent::addAttribute('cpf_repres');
        parent::addAttribute('nome_repres');
        parent::addAttribute('cod_qualif_repres');
    }


}
