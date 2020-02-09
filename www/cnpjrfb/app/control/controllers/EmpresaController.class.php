<?php
class EmpresaController
{
    public function selectBySocio(array $listSocio)
    {
        try {
            $listEmpresa = array();
            foreach ($listSocio as $key => $socio) {
                $cnpj = $socio->getCnpj();
                $empresa = new Empresa($cnpj);
                $listEmpresa = array_merge($listEmpresa, $empresa);
            }
            return $listEmpresa;
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    function getGridEmpresa(array $listEmpresa){
        // create the datagrid
        $grid = new BootstrapDatagridWrapper(new TDataGrid);
        $grid->width = '100%';    
        $grid->addColumn( new TDataGridColumn('cnpj','CNPJ','left') );
        $grid->addColumn( new TDataGridColumn('razao_social','Razão Social','left') );
        $grid->addColumn( new TDataGridColumn('nome_fantasia','Nome Fantasia','left') );

        $action1 = new TDataGridAction(['EmpresaViewForm', 'onView'],  ['key' => '{cnpj}'], ['register_state' => 'false']  );
        $grid->addAction($action1, 'Detalhar Empresa', 'fa:building #7C93CF');

        $grid->createModel();
        $grid->addItems($listEmpresa);
        $panel = TPanelGroup::pack('Lista de Empresas', $grid);
        
        return $panel;
    }
}
