<?php
class SocioController
{
    public function selectBySocio($cnpj_cpf_socio,$nome_socio)
    {
        try {
            TTransaction::open('cnpj_full'); // abre uma transação
            $criteria = new TCriteria;
            $criteria->add(new TFilter('cnpj_cpf_socio', '=', $cnpj_cpf_socio));
            $criteria->add(new TFilter('nome_socio', '=', $nome_socio));

            $repository = new TRepository('Socio');
            $socio = $repository->load($criteria);

            TTransaction::close(); // fecha a transação.
            return  $socio;
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
}
