<?php
class EmpresaController
{
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
}
