<?php
/**
 * System generated by SysGen (System Generator with Formdin Framework) 
 * Download SysGenAd: https://github.com/bjverde/sysgenad
 * Download Formdin5 Framework: https://github.com/bjverde/formDin5
 * 
 * SysGen  Version: 0.6.0
 * FormDin Version: 5.0.0
 * 
 * System cnpjrfb created in: 2021-11-19 22:41:14
 */
class simples extends TRecord
{
    const TABLENAME = DATABASE_SCHEMA.'simples';
    const PRIMARYKEY= 'cnpj_basico';
    const IDPOLICY  = 'serial'; //{max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('opcao_pelo_simples');
        parent::addAttribute('data_opcao_simples');
        parent::addAttribute('data_exclusao_simples');
        parent::addAttribute('opcao_mei');
        parent::addAttribute('data_opcao_mei');
        parent::addAttribute('data_exclusao_mei');
    }

}
?>