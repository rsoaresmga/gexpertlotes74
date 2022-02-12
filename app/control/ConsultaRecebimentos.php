<?php
    class ConsultaRecebimentos extends TPage
    {
        use Adianti\Base\AdiantiStandardListTrait;
        use Tr2FormUtilsTrait;
        
        protected $form;
        protected $datagrid;
        protected $pageNavigation;
        
        public function __construct()
        {
            parent::__construct();
            
            $this->setDatabase('gexpertlotes');
            $this->setActiveRecord('VendasParcelasBaixas');
            $this->setDefaultOrder('VENDA, RECEBIMENTO', 'asc');
            $this->setLimit(20);
            
            $this->setOrderCommand('EMPREENDIMENTO', '(select CODIGO from empreendimentos where CODIGO=(select EMPREENDIMENTO from vendas where LANCAMENTO=vendas_parcelas_baixas.VENDA))');
            $this->setOrderCommand('CLIENTE', '(select RAZAO from entidades where CODIGO=(select ENTIDADE from vendas where LANCAMENTO=vendas_parcelas_baixas.VENDA))');
            $this->setOrderCommand('QUADRA', '(select QUADRA from vendas where LANCAMENTO=vendas_parcelas_baixas.VENDA)');
            $this->setOrderCommand('LOTE', '(select LOTE from vendas where LANCAMENTO=vendas_parcelas_baixas.VENDA)');
            $this->setOrderCommand('PARCELA', '(select PARCELA from vendas_parcelas where ID=vendas_parcelas_baixas.PARCELA)');
            $this->setOrderCommand('EMISSAO', '(select EMISSAO from vendas where LANCAMENTO=vendas_parcelas_baixas.VENDA)');
            $this->setOrderCommand('VALORPARCELA', '(select VALOR from vendas_parcelas where ID=vendas_parcelas_baixas.PARCELA)');
            $this->setOrderCommand('VENCIMENTO', '(select VENCIMENTO from vendas_parcelas where ID=vendas_parcelas_baixas.PARCELA)');
            $this->setOrderCommand('QUITADO', '(select QUITADO from vendas_parcelas where ID=vendas_parcelas_baixas.PARCELA)');
          
            $this->addFilterField('VENDA', '=', 'vda');            
            $this->addFilterField($this->orderCommands['EMPREENDIMENTO'], '>=', 't1');
            $this->addFilterField($this->orderCommands['EMPREENDIMENTO'], '<=', 't2');
            $this->addFilterField($this->orderCommands['QUADRA'], '>=', 'q1');
            $this->addFilterField($this->orderCommands['QUADRA'], '<=', 'q2');
            $this->addFilterField($this->orderCommands['LOTE'], '>=', 'l1');
            $this->addFilterField($this->orderCommands['LOTE'], '<=', 'l2');            
            $this->addFilterField($this->orderCommands['PARCELA'], '>=', 'p1');
            $this->addFilterField($this->orderCommands['PARCELA'], '<=', 'p2');
            $this->addFilterField($this->orderCommands['VENCIMENTO'], '>=', 'v1');
            $this->addFilterField($this->orderCommands['VENCIMENTO'], '<=', 'v2');
            $this->addFilterField('RECEBIMENTO', '>=', 'r1');
            $this->addFilterField('RECEBIMENTO', '<=', 'r2');
            
            $this->form = new BootstrapFormBuilder('form_Consulta_Recebimentos');
            $this->form->setFormTitle('Consultar Recebimentos');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
            
            $this->setCriteria($criteria);
            
            $t1 = new TDBSeekButton('t1', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't1', null, $this->criteria);
            $t1aux = new THidden('t1aux');
            $t1->setAuxiliar($t1aux);
            $t2 = new TDBSeekButton('t2', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't2', null, $this->criteria);
            $t2aux = new THidden('t2aux');
            $t2->setAuxiliar($t2aux);
             
            $q1 = new TEntry('q1');
            $q1->setMask(TMascara::maskInt);
            $q2 = new TEntry('q2');
            $q2->setMask(TMascara::maskInt);
            $l1 = new TEntry('l1');
            $l1->setMask(TMascara::maskInt);
            $l2 = new TEntry('l2');
            $l2->setMask(TMascara::maskInt);
            $vda  = new TEntry('vda');
            $vda->setMask(TMascara::maskInt);           
            $p1 = new TEntry('p1');
            $p1->setMask(TMascara::maskInt);
            $p2 = new TEntry('p2');
            $p2->setMask(TMascara::maskInt);
            $v1 = new TDate('v1');
            $v1->setMask(TMascara::maskDate);
            $v1->setDatabaseMask(TMascara::maskDBDate);
            $v2 = new TDate('v2');
            $v2->setMask(TMascara::maskDate);
            $v2->setDatabaseMask(TMascara::maskDBDate);
            $r1 = new TDate('r1');
            $r1->setMask(TMascara::maskDate);
            $r1->setDatabaseMask(TMascara::maskDBDate);
            $r2 = new TDate('r2');
            $r2->setMask(TMascara::maskDate);
            $r2->setDatabaseMask(TMascara::maskDBDate);
            
            $t1->setSize(TWgtSizes::wsInt);
            $t2->setSize(TWgtSizes::wsInt);
            $q1->setSize(TWgtSizes::wsInt);
            $q2->setSize(TWgtSizes::wsInt);
            $l1->setSize(TWgtSizes::wsInt);
            $l2->setSize(TWgtSizes::wsInt);
            $vda->setSize(TWgtSizes::wsDouble);
            $p1->setSize(TWgtSizes::wsInt);
            $p2->setSize(TWgtSizes::wsInt);
            $v1->setSize(TWgtSizes::wsDate);
            $v2->setSize(TWgtSizes::wsDate);
            $r1->setSize(TWgtSizes::wsDate);
            $r2->setSize(TWgtSizes::wsDate);
            
            $this->form->addFields([new TLabel('Empreendimento de')],[$t1 ,new TLabel('&nbsp&nbspa'), $t2]);
            $this->form->addFields([new TLabel('Quadra de')],[$q1 ,new TLabel('a'), $q2]);
            $this->form->addFields([new TLabel('Lote de')],[$l1 ,new TLabel('a'), $l2]);
            $this->form->addFields([new TLabel('Venda')],[$vda]);
            $this->form->addFields([new TLabel('Parcela de')],[$p1 ,new TLabel('a'), $p2]);
            $this->form->addFields([new TLabel('Vencimento de')],[$v1 ,new TLabel('a'), $v2]);
            $this->form->addFields([new TLabel('Recebimento de')],[$r1 ,new TLabel('a'), $r2]);
            
            $button = $this->form->addAction(_t('Search'), new TAction([$this, 'onSearch']), 'fa:search');
            $button->class = 'btn btn-success';
            $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
            
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagrid->style = 'width:100%';
            $this->datagrid->disableDefaultClick();
                        
            $column_EMPREENDIMENTO = $this->datagrid->addQuickColumn('Empto', '{vendas->EMPREENDIMENTO}');
            $column_VENDA          = $this->datagrid->addQuickColumn('Venda', 'VENDA'); 
            $column_CLIENTE        = $this->datagrid->addQuickColumn('Cliente', '{vendas->entidades->RAZAO}');
            $column_QUADRA         = $this->datagrid->addQuickColumn('Qda', '{vendas->QUADRA}');
            $column_LOTE           = $this->datagrid->addQuickColumn('Lte', '{vendas->LOTE}');
            $column_PARCELA        = $this->datagrid->addQuickColumn('Parc', '{vendas_parcelas->PARCELA}');
            $column_EMISSAO        = $this->datagrid->addQuickColumn('Emissão', '{vendas->EMISSAO}');
            $column_VALOR          = $this->datagrid->addQuickColumn('Vlr Parc', '{vendas_parcelas->VALOR}');
            $column_VENCIMENTO     = $this->datagrid->addQuickColumn('Vencto', '{vendas_parcelas->VENCIMENTO}');
            $column_QUITADO        = $this->datagrid->addQuickColumn('Quit', '{vendas_parcelas->QUITADO}');
            $column_RECEBIMENTO    = $this->datagrid->addQuickColumn('Recebto', 'RECEBIMENTO');
            $column_RECEBIDO       = $this->datagrid->addQuickColumn('Valor', 'VALOR');
            $column_MULTA          = $this->datagrid->addQuickColumn('Multa', 'MULTA');
            $column_JUROS          = $this->datagrid->addQuickColumn('Juros', 'JUROS');
            $column_ATUALIZACAO    = $this->datagrid->addQuickColumn('Att', 'ATUALIZACAO');
            $column_TOTALRECEBIDO  = $this->datagrid->addQuickColumn('Total', '={VALOR}+{MULTA}+{JUROS}+{ATUALIZACAO}-{DESCONTO}');
            $column_ID             = $this->datagrid->addQuickColumn('Id', 'ID');
            $column_DATACAD        = $this->datagrid->addQuickColumn('Cad', 'DATACAD');
            $column_DATAALT        = $this->datagrid->addQuickColumn('Alt', 'DATAALT');
            
            $column_EMPREENDIMENTO->setAction(new TAction([$this, 'onReload'],['order'=>'EMPREENDIMENTO']));
            $column_VENDA->setAction(new TAction([$this, 'onReload'],['order'=>'VENDA']));
            $column_CLIENTE->setAction(new TAction([$this, 'onReload'],['order'=>'CLIENTE']));
            $column_QUADRA->setAction(new TAction([$this, 'onReload'],['order'=>'QUADRA']));
            $column_LOTE->setAction(new TAction([$this, 'onReload'],['order'=>'LOTE']));
            $column_PARCELA->setAction(new TAction([$this, 'onReload'],['order'=>'PARCELA']));
            $column_EMISSAO->setAction(new TAction([$this, 'onReload'],['order'=>'EMISSAO']));
            $column_VALOR->setAction(new TAction([$this, 'onReload'],['order'=>'VALORPARCELA']));
            $column_VENCIMENTO->setAction(new TAction([$this, 'onReload'],['order'=>'VENCIMENTO']));
            $column_QUITADO->setAction(new TAction([$this, 'onReload'],['order'=>'QUITADO']));
            $column_RECEBIMENTO->setAction(new TAction([$this, 'onReload'],['order'=>'RECEBIMENTO']));
            $column_RECEBIDO->setAction(new TAction([$this, 'onReload'],['order'=>'VALOR']));
            $column_MULTA->setAction(new TAction([$this, 'onReload'],['order'=>'MULTA']));
            $column_JUROS->setAction(new TAction([$this, 'onReload'],['order'=>'JUROS']));
            $column_ATUALIZACAO->setAction(new TAction([$this, 'onReload'],['order'=>'ATUALIZACAO']));
            $column_TOTALRECEBIDO->setAction(new TAction([$this, 'onReload'],['order'=>'TOTALRECEBIDO']));
            $column_DATAALT->setAction(new TAction([$this, 'onReload'],['order'=>'DATAALT']));
            $column_DATACAD->setAction(new TAction([$this, 'onReload'],['order'=>'DATACAD']));
            $column_ID->setVisibility(FALSE);
            $column_EMISSAO->setVisibility(FALSE);
            
            $action_group = new TDataGridActionGroup('Ações', 'fa:th');
            $action_group->addHeader('Opções Disponíveis');
            $action_group->addAction($action = new TDataGridAction(['VendasParcelasBaixasForm', 'onEdit'], ['ID'=>'{ID}']));
            $action->setLabel('Abrir Cadastro da Baixa');
            $action->setImage('far:money-bill-alt blue');
            
         /*   $action_group->addAction($action = new TDataGridAction(['VendasParcelasForm', 'onEdit'], ['ID'=>'{PARCELA}']));
            $action->setLabel('Abrir Cadastro da Parcela');
            $action->setImage('fas:money-check red');*/
            
            $action_group->addAction($action = new TDataGridAction(['VendasForm', 'onEdit'], ['LANCAMENTO'=>'{VENDA}', 'status'=>'edit', 'reset'=>'1']));
            $action->setLabel('Abrir Cadastro da Venda');
            $action->setImage('fas:cash-register green');
            
            
            $this->datagrid->addActionGroup($action_group);
            
            $column_VALOR->setTransformer([$this, 'asCurBR']);
            $column_RECEBIMENTO->setTransformer([$this, 'asDate']);
            $column_EMISSAO->setTransformer([$this, 'asDate']);
            $column_DATAALT->setTransformer([$this, 'asDateTime']);
            $column_DATACAD->setTransformer([$this, 'asDateTime']);
            $column_VENCIMENTO->setTransformer([$this, 'asDate']);
            $column_RECEBIDO->setTransformer([$this, 'asCurBR']);
            $column_MULTA->setTransformer([$this, 'asCurBR']);
            $column_JUROS->setTransformer([$this, 'asCurBR']);
            $column_ATUALIZACAO->setTransformer([$this, 'asCurBR']);
            $column_TOTALRECEBIDO->setTransformer([$this, 'asCurBR']);
            $column_QUITADO->setTransformer([$this, 'asBooleanBR']);
            
            $this->pageNavigation = new TPageNavigation;
            $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
            $this->pageNavigation->setWidth($this->datagrid->getWidth());
            $this->pageNavigation->enableCounters();
            
            $this->datagrid->datatable = 'true';
            $this->datagrid->createModel();
            
            $dropdown = new TDropDown(_t('Export'), 'fa:list');
            $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
            $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV']), 'fas:table blue');
            $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF']), 'far:file-pdf red');
            $dropdown->addAction(_t('Save as XML'), new TAction([$this, 'onExportXML']), 'fas:code green');
            
            $panel = new TPanelGroup;
            $panel->add($this->datagrid);
            $panel->addFooter($this->pageNavigation);
            $panel->addHeaderWidget($dropdown);
    
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($this->form);
            $container->add($panel);
            
            parent::add($container);
            
            $this->form->setData(TSession::getValue($this->activeRecord.'_filter_data'));
		                           
        }
        
        
    }
?>
