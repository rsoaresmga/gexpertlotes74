<?php

    class ConsultaLotes extends TPage
    {
        use Adianti\Base\AdiantiStandardListTrait;
        use Tr2FormUtilsTrait;
        
        public $form;
        public $datagrid;
        public $pageNavigation;
        
        public function __construct($param = null)
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder('form_ConsultaLotes');
            $this->form->setFormTitle('Consulta Lotes');
            $this->form->style = 'width: 100%';
            
            $this->setDatabase('gexpertlotes');
            $this->setActiveRecord('Lotes');
            $this->setDefaultOrder('EMPREENDIMENTO, QUADRA, CODIGO', 'asc');
            $this->setLimit(20);
            
            $this->addFilterField('EMPREENDIMENTO', '>=', 't1');
            $this->addFilterField('EMPREENDIMENTO', '<=', 't2');
            $this->addFilterField('QUADRA', '>=', 'q1');
            $this->addFilterField('QUADRA', '<=', 'q2');
            $this->addFilterField('CODIGO', '>=', 'l1');
            $this->addFilterField('CODIGO', '<=', 'l2');
            $this->addFilterField('SITUACAO', '=', 'sit');
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('EMPRESA', '=', TSession::getValue('userunitid')));
            
            $this->setCriteria($criteria);
            
            $transformer_SITUACAO = function($value)
                                    {       
                                        $span        = new TElement('span');
                                        $span->class = 'label label-default'; 
                                        $situacao    = 'Nenhum';
                                        
                                        switch($value)
                                        {
                                            case 0: 
                                                    $situacao = 'Aberto';
                                                    $span->class = 'label label-primary';
                                                break;
                                            case 1: $situacao = 'Vendido';
                                                    $span->class = 'label label-success';
                                                 break;
                                            case 2: $situacao = 'Devolvido';
                                                    $span->class = 'label label-danger';
                                                 break;                                  
                                         }
                                        
                                        $span->add($situacao);
                                        
                                      return $span;
                                    };                       
            $h = new THidden('h');
            
            $t1 = new TDBSeekButton('t1', $this->database, $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't1', null, $this->criteria);
            $t1->setSize(TWgtSizes::wsInt);
            $t1->setAuxiliar($h);
            $t2 = new TDBSeekButton('t2', $this->database, $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't2', null, $this->criteria);
            $t2->setSize(TWgtSizes::wsInt);    
            $t2->setAuxiliar($h);
            $q1 = new TEntry('q1');
            $q1->setSize(TWgtSizes::wsInt);
            $q2 = new TEntry('q2');
            $q2->setSize(TWgtSizes::wsInt);
            $l1 = new TEntry('l1');
            $l1->setSize(TWgtSizes::wsInt);
            $l2 = new TEntry('l2');
            $l2->setSize(TWgtSizes::wsInt);
            $sit = new TCombo('sit');
            $sit->addItems(['0'=>'0-Aberto', '1'=>'1-Vendido', '2'=>'2-Devolvido']);
            
            $this->form->addFields([new TLabel('Empreendimentos de')],[$t1, '&nbsp&nbsp&nbspa', $t2]);
            $this->form->addFields([new TLabel('Quadras de')],[$q1, '&nbspa', $q2]);
            $this->form->addFields([new TLabel('Lotes de')],[$l1, '&nbspa', $l2]);
            $this->form->addFields([new TLabel('Situação')], [$sit]);
            $sit->setSize(TWgtSizes::wsDouble);
            
            $button = $this->form->addAction(_t('Search'), new TAction([$this, 'onSearch']), 'fa:search');
            $button->class = 'btn btn-success';
            $this->form->addAction(_t('Clear Filters'), new TAction([$this,'clearFilters']),'fa:eraser red'); 
                        
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagrid->style = 'width:100%';
                       
            $column_EMPREENDIMENTO = $this->datagrid->addQuickColumn('Empreendimento', '{empreendimentos->DESCRICAO}', 'left', 500);
            $column_QUADRA = $this->datagrid->addQuickColumn('Quadra','QUADRA');
            $column_CODIGO = $this->datagrid->addQuickColumn('Lote','CODIGO');
            $column_AREA = $this->datagrid->addQuickColumn('Área','AREA');
            $column_VLRCUSTO = $this->datagrid->addQuickColumn('Vlr. Custo','VLRCUSTO');
            $column_SITUACAO = $this->datagrid->addQuickColumn('Situação','SITUACAO');
            $this->datagrid->addQuickColumn('Cta Ctb Est','CONTACTBESTOQUE');
            $this->datagrid->addQuickColumn('Cta Ctb Rec Dif(CP)','CONTACTBRECEITADIFER');
            $this->datagrid->addQuickColumn('Cta Ctb Desp Dif(CP)','CIBTACTBDESPESADIFER');
            $this->datagrid->addQuickColumn('Cta Ctb Rec Dif(LP)','CONTACTBRECEITADIFERLP');
            $this->datagrid->addQuickColumn('Cta Ctb Desp Dif(LP)','CONTACTBDESPESADIFERLP');
            
            $column_SITUACAO->setTransformer($transformer_SITUACAO);
            $column_AREA->setTransformer([$this, 'asDoubleBR']);
            $column_VLRCUSTO->setTransformer([$this, 'asCurBR']);
            
            $column_AREA->setTotalFunction([$this, 'sumDouble']);
            $column_VLRCUSTO->setTotalFunction([$this, 'sumCurrency']);
            
                                                            
            $this->datagrid->createModel();
            
            $this->pageNavigation = new TPageNavigation;
            $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
            $this->pageNavigation->setWidth($this->datagrid->getWidth());
            $this->pageNavigation->enableCounters();
            
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
