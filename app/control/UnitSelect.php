<?php
    class UnitSelect extends TWindow
    {
        use Adianti\Base\AdiantiStandardListTrait;
        
        public $form;
        public $datagrid;
        public $pageNavigation;
                
        function __construct()
        {
            parent::__construct();
            parent::setSize(0.6, null);
            parent::setMinWidth(0.9,null);
            parent::setModal('window_modal');
            parent::setTitle('Selecionar Empresa');
            $this->setDatabase('gexpertlotes');
            $this->setActiveRecord('Empresas');
            $this->setDefaultOrder('CODIGO', 'asc');
            $this->addFilterField('CODIGO', '=', 'searchCodigo');
            $this->addFilterField('RAZAO', 'like', 'searchRazao');
            $this->addFilterField('CNPJ', 'like', 'searchCnpj');
            $this->setLimit(10);
             
                                    
            $this->form = new TForm('frm_'.__CLASS__);
            $this->form->style='width:100%';
            
            $searchCodigo = new TEntry('searchCodigo');
            $searchRazao  = new TEntry('searchRazao');
            $searchCnpj   = new TEntry('searchCnpj');
            
            $searchCodigo->{'type'} = 'search';
            $searchRazao->{'type'} = 'search';
            $searchCnpj->{'type'} = 'search';
            
            $searchCodigo->setSize(TWgtSizes::wsDef);
            $searchRazao->setSize(TWgtSizes::wsDef);
            $searchCnpj->setSize(TWgtSizes::wsDef);
            
            $searchCodigo->exitOnEnter();
            $searchRazao->exitOnEnter();
            $searchCnpj->exitOnEnter();
            
            $searchCodigo->setExitAction(new TAction([$this, 'onSearch'], ['static'=>1]));
            $searchRazao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>1]));
            $searchCnpj->setExitAction(new TAction([$this, 'onSearch'], ['static'=>1]));
            
            $this->form->addField($searchCodigo);
            $this->form->addField($searchRazao);
            $this->form->addField($searchCnpj);
            
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGridScroll);
            //$this->datagrid->style='width:100%';
            $colCodigo = $this->datagrid->addQuickColumn('Código', 'CODIGO', 'left');
            $colRazao  = $this->datagrid->addQuickColumn('Razão', 'RAZAO');
            $colCnpj   = $this->datagrid->addQuickColumn('CNPJ', 'CNPJ');
            
            $actSelect = $this->datagrid->addQuickAction(_t('Open'), new TDataGridAction([$this, 'onSelect'], ['static'=>1]), 'CODIGO', 'fas:folder-open lightyellow');
            $actSelect->setDisplayCondition([$this, 'onDisplay']);
            
            $this->datagrid->createModel();
            
            $tr = new TElement('tr');
            $tr->add(TElement::tag('td', ''));
            $tr->add(TElement::tag('td', $searchCodigo));
            $tr->add(TElement::tag('td', $searchRazao));
            $tr->add(TElement::tag('td', $searchCnpj));
            
            $this->datagrid->getHead()->add($tr);
            
            $this->form->add($this->datagrid);
            
            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
            $this->pageNavigation->setWidth($this->datagrid->getWidth());
            $this->datagrid->setPageNavigation($this->pageNavigation);            
            
            $panel = new TPanelGroup();
            $panel->style='width:100%';
            $panel->add($this->form);
            $panel->addHeaderActionLink(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fas:eraser red');
            $panel->addFooter($this->pageNavigation);
            
            $container = new TVBox;            
            $container->style='width:100%; overflow-x:auto';
            $container->add($panel);
            
            parent::add($container); 
            
            TForm::sendData('frm_'.__CLASS__, (object) TSession::getValue(__CLASS__."_filter_data"), null, FALSE);                
        }        
        
        function onSelect($param)
        {
            $data = (object) $param;
            
            ApplicationAuthenticationService::setUnit( $data->CODIGO ?? null );
            SystemAccessLogService::registerLogin();
            AdiantiCoreApplication::gotoPage('EmptyPage'); // reload                    
        }
        
        function onDisplay($object)
        {
            $user = new SystemUser(TSession::getValue('userid'));
            
            $user_units = $user->getSystemUserUnitIds();

            return (in_array($object->CODIGO, $user_units)); 
        }        
    }
?>