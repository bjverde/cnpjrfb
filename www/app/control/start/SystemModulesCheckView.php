<?php
class SystemModulesCheckView extends TPage
{
    function __construct()
    {
        parent::__construct();
        
        try 
        {
            $extensions = ['general' =>
                            ['mbstring' => 'MBString',
                             'curl' => 'CURL',
                             'dom' => 'DOM',
                             'xml' => 'XML',
                             'zip' => 'ZIP',
                             'json' => 'JSON',
                             'libxml' => 'LibXML',
                             'openssl' => 'OpenSSL',
                             'zip' => 'ZIP',
                             'SimpleXML' => 'SimpleXML',
                             'fileinfo' => 'FileInfo'],
                          'database' =>
                            ['PDO' => 'PDO',
                             'pdo_sqlite' => 'PDO SQLite',
                             'pdo_mysql' => 'PDO MySql',
                             'pdo_pgsql' => 'PDO PostgreSQL',
                             'pdo_oci' => 'PDO Oracle',
                             'pdo_dblib' => 'PDO Sql Server via dblib',
                             'pdo_sqlsrv' => 'PDO Sql Server via sqlsrv',
                             'firebird' => 'PDO Firebird',
                             'odbc' => 'PDO ODBC']];
            
            $framework_extensions = array_keys( array_merge( $extensions['general'], $extensions['database'] ));
            
            $panel1 = new TPanelGroup('PHP Directives');
            
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagrid->width = '100%';
            $this->datagrid->disableHtmlConversion();
            
            // add the columns
            $this->datagrid->addQuickColumn('DIRECTIVE',    'directive',   'center', '20%');
            $this->datagrid->addQuickColumn('CURRENT',      'current',     'center', '25%');
            $this->datagrid->addQuickColumn('DEVELOPMENT',  'development', 'center', '25%');
            $this->datagrid->addQuickColumn('PRODUCTION',   'production',  'center', '30%');
            $this->datagrid->createModel();
            
            $warning = '&nbsp;<i class="fa fa-exclamation-triangle red" aria-hidden="true"></i>';
            $success = '&nbsp;<i class="far fa-check-circle green" aria-hidden="true"></i>';
            
            $item = new stdClass;
            $item->directive   = 'error_reporting';
            $item->current     = ini_get($item->directive) == E_ALL ?
                                '<span><b>E_ALL</b></span>' :
                                '<span><b>'.ini_get($item->directive).'</b></span>';
            $item->development = ini_get($item->directive) == E_ALL ?
                                '<span class="green"><b>E_ALL</b></span>' . $success:
                                '<span class="red"><b>E_ALL</b></span>' . $warning;
            $item->production  = ini_get($item->directive) == E_ALL - E_DEPRECATED - E_STRICT ?
                                '<span class="green"><b>E_ALL & ~E_DEPRECATED & ~E_STRICT</b></span>' . $success:
                                '<span class="red"><b> E_ALL & ~E_DEPRECATED & ~E_STRICT </b></span>' . $warning;
            $this->datagrid->addItem($item);
            
            $item = new stdClass;
            $item->directive   = 'display_errors';
            $item->current     = '<span><b>' . (ini_get($item->directive) ? 'On' : 'Off' ) . '</b></span>';
            $item->development = ini_get($item->directive) ?
                                 '<span class="green"><b>On</b></span>' . $success : '<span class="red"><b>On</b></span>' . $warning;
            $item->production  = !ini_get($item->directive) ?
                                 '<span class="green"><b>Off</b></span>' . $success: '<span class="red"><b>Off</b></span>' . $warning;
            $this->datagrid->addItem($item);
            
            $item = new stdClass;
            $item->directive   = 'log_errors';
            $item->current     = '<span><b>' . (ini_get($item->directive) ? 'On' : 'Off' ) . '</b></span>';
            $item->development = ini_get($item->directive) ? '<span class="green"><b>On</b></span>' . $success: '<span class="red"><b>On</b></span>' . $warning;
            $item->production  = ini_get($item->directive) ? '<span class="green"><b>On</b></span>' . $success: '<span class="red"><b>On</b></span>' . $warning;
            $this->datagrid->addItem($item);
            
            $item = new stdClass;
            $item->directive   = 'output_buffering';
            $item->current     = '<span><b>' . ini_get($item->directive) . '</b></span>';
            $item->development = ini_get($item->directive) == '4096' ? '<span class="green"><b>4096</b></span>' . $success : '<span class="red"><b>4096</b></span>' . $warning;
            $item->production  = ini_get($item->directive) == '4096' ? '<span class="green"><b>4096</b></span>' . $success : '<span class="red"><b>4096</b></span>' . $warning;
            $this->datagrid->addItem($item);
            
            $item = new stdClass;
            $item->directive   = 'opcache.enable';
            $item->current     = ini_get($item->directive) ? '<span><b>On</b></span>': '<span><b>Off</b></span>';
            $item->development = ini_get($item->directive) ? '<span class="green"><b>On</b></span>' . $success: '<span class="red"><b>On</b></span>' . $warning;
            $item->production  = ini_get($item->directive) ? '<span class="green"><b>On</b></span>' . $success: '<span class="red"><b>On</b></span>' . $warning;
            $this->datagrid->addItem($item);
            
            $panel1->add($this->datagrid);
            $panel1->addFooter(new TAlert('info', 'The php.ini current location is <b>'.php_ini_loaded_file().'</b>') .
                               new TAlert('warning', '<b>Note</b>: error_reporting and display_errors are automatic enabled when debug=1 in application.ini'));
            
            $panel2 = new TPanelGroup('PHP Modules');
            
            $row = new TElement('div');
            $row->class = 'row';
            $row->style = 'margin:0';
            
            foreach ($extensions as $type => $modules)
            {
                $module_block = new TElement('div');
                $module_block->style = 'font-size:17px; padding-left: 20px';
                $module_block->class = 'col-sm-6';
                $module_block->add( '<b>' . strtoupper($type) . '</b>');
                
                foreach ($modules as $extension => $name) 
                {
                    if (extension_loaded($extension))
                    {
                        $element = new TElement('div');
                        $element->style = 'font-size:17px; padding: 5px';
                        $element->add( TElement::tag('i', '', ['class' => 'fa fa-check green fa-fw']) );
                        $element->add("{$name} ({$extension})");
                    }
                    else
                    {
                        $element = new TElement('div');
                        $element->style = 'font-size:17px; padding: 5px';
                        $element->add( TElement::tag('i', '', ['class' => 'fa fa-times red fa-fw']) );
                        $element->add("{$name} ({$extension})");
                    }
                    
                    $module_block->add($element);
                }
                $row->add($module_block);
            }
            
            $panel2->add($row);
            $panel2->addFooter(new TAlert('info', 'The php.ini current location is <b>'.php_ini_loaded_file().'</b>'));
            
            $panel3 = new TPanelGroup('Another Modules');
            
            $extensions = get_loaded_extensions();
            $another_ext = array_diff($extensions, $framework_extensions);
            $another_ext = array_unique(array_merge($another_ext, ['session', 'date', 'zlib', 'gd', 'Phar']));
            natcasesort($another_ext);
            
            $row = new TElement('div');
            $row->class = 'row';
            $row->style = 'margin:0';
            
            foreach ($another_ext as $extension)
            {
                if (extension_loaded($extension))
                {
                    $element = new TElement('div');
                    $element->style = 'font-size:17px; padding: 5px';
                    $element->class = 'col-sm-3';
                    $element->add( TElement::tag('i', '', ['class' => 'fa fa-check green fa-fw']) );
                    $element->add("{$extension}");
                }
                else
                {
                    $element = new TElement('div');
                    $element->style = 'font-size:17px; padding: 5px';
                    $element->class = 'col-sm-3';
                    $element->add( TElement::tag('i', '', ['class' => 'fa fa-times red fa-fw']) );
                    $element->add("{$extension}");
                }
                
                $row->add($element);
            }
            $panel3->add($row);
            $panel3->addFooter(new TAlert('info', 'The php.ini current location is <b>' . php_ini_loaded_file() . '</b>'));
            
            $panel1->getBody()->style = "overflow-x:auto;";
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($panel1);
            $container->add($panel2);
            $container->add($panel3);
            
            parent::add($container);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
