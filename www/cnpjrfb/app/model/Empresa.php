<?php
/**
 * Empresas Active Record
 * @author  <your-name-here>
 */
class Empresa extends TRecord
{
    const TABLENAME = 'empresas';
    const PRIMARYKEY= 'cnpj';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $cnaes_secundarios;
    private $socios;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('matriz_filial');
        parent::addAttribute('razao_social');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('situacao');
        parent::addAttribute('data_situacao');
        parent::addAttribute('motivo_situacao');
        parent::addAttribute('nm_cidade_exterior');
        parent::addAttribute('cod_pais');
        parent::addAttribute('nome_pais');
        parent::addAttribute('cod_nat_juridica');
        parent::addAttribute('data_inicio_ativ');
        parent::addAttribute('cnae_fiscal');
        parent::addAttribute('tipo_logradouro');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cep');
        parent::addAttribute('uf');
        parent::addAttribute('cod_municipio');
        parent::addAttribute('municipio');
        parent::addAttribute('ddd_1');
        parent::addAttribute('telefone_1');
        parent::addAttribute('ddd_2');
        parent::addAttribute('telefone_2');
        parent::addAttribute('ddd_fax');
        parent::addAttribute('num_fax');
        parent::addAttribute('email');
        parent::addAttribute('qualif_resp');
        parent::addAttribute('capital_social');
        parent::addAttribute('porte');
        parent::addAttribute('opc_simples');
        parent::addAttribute('data_opc_simples');
        parent::addAttribute('data_exc_simples');
        parent::addAttribute('opc_mei');
        parent::addAttribute('sit_especial');
        parent::addAttribute('data_sit_especial');
    }

    
    /**
     * Method addCnaesSecundario
     * Add a CnaesSecundario to the Empresas
     * @param $object Instance of CnaesSecundario
     */
    public function addCnaesSecundario(CnaesSecundario $object)
    {
        $this->cnaes_secundarios[] = $object;
    }
    
    /**
     * Method getCnaesSecundarios
     * Return the Empresas' CnaesSecundario's
     * @return Collection of CnaesSecundario
     */
    public function getCnaesSecundarios()
    {
        return $this->cnaes_secundarios;
    }
    
    /**
     * Method addSocio
     * Add a Socio to the Empresas
     * @param $object Instance of Socio
     */
    public function addSocio(Socio $object)
    {
        $this->socios[] = $object;
    }
    
    /**
     * Method getSocios
     * Return the Empresas' Socio's
     * @return Collection of Socio
     */
    public function getSocios()
    {
        return $this->socios;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->cnaes_secundarios = array();
        $this->socios = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->cnaes_secundarios = parent::loadComposite('CnaesSecundario', 'cnpj', $id);
        $this->socios = parent::loadComposite('Socio', 'cnpj', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveComposite('CnaesSecundario', 'cnpj', $this->id, $this->cnaes_secundarios);
        parent::saveComposite('Socio', 'cnpj', $this->id, $this->socios);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('CnaesSecundario', 'cnpj', $id);
        parent::deleteComposite('Socio', 'cnpj', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
