<?php
class EmpresaController
{
    const LINK_IBGE   = 'https://cnae.ibge.gov.br/?option=com_cnae&view=atividades&Itemid=6160&tipo=cnae&versao_classe=7.0.0&versao_subclasse=10.1.0&chave=';
    const LINK_CONUBE = 'http://cnae-simples.com.br/?search=';

    public function selectBySocio(array $listSocio)
    {
        try {
            $listEmpresa = array();
            foreach ($listSocio as $socio) {
                TTransaction::open('cnpj_full'); // abre uma transação
                $listEmpresa[] = new Empresa($socio->cnpj);
                TTransaction::close(); // fecha a transação.
            }
            return $listEmpresa;
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function getLink ($value,$ibge=true) {
        if ($value)
        {
            $link = $ibge?self::LINK_IBGE:self::LINK_CONUBE;
            $icon  = "<i class='fas fa-link' aria-hidden='true'></i>";
            return "{$icon} <a target='newwindow' href='{$link}{$value}'> {$value} </a>";
        }
        return $value;
    }
}
