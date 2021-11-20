<?php
class Gen00 extends TPage
{

    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        try
        {
            $frm = new TFormDin($this,'Novo sistema');

            $html = '<h2>Seu novo sistema foi criado usando SysGenAd, Adianti e FormDin5</h2>
                     <br>
                     <br> Esse sistema foi criado de forma automatica usando:
                        <ul>
                            <li><a href="https://www.adianti.com.br/" target="_blank">Adinati FrameWork 7.2.2</a> criado por <a href="http://www.dalloglio.net/" target="_blank">Pablo Dall\'Oglio</a></li>
                            <li><a href="https://github.com/bjverde/formDin5" target="_blank">FormDin 5</a> é um meta FrameWork, conconstruido sobre o Adinati. Portando não é um FrameWork completo, ele tem total depencendia do Adinati. É um Framework de transição do FormDin 4 para o Adianti, facilitando a migração. É uma abastração das chamadas do FormDin no Adianti</li>
                            <li><a href="https://github.com/bjverde/sysgenad" target="_blank">SysGenAd</a> é um gerador de sistemas</li>
                            <li>Versão do sistema: É número da versão. É recomendável utilizar <a href="https://semver.org/lang/pt-BR/" target="_blank">o versionamento semântico</a></li>                            
                        </ul>';
            $frm->addHtmlField('aviso',$html,null,null);

            $this->form = $frm->show();

            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add( $this->form );
            parent::add($vbox);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();
    }

    /**
     * Load the data into the datagrid
     */
    function onReload()
    {

    }
    
    /**
     * Clear filters
     */
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}