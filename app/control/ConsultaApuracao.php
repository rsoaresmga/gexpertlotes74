<?php

    class ConsultaApuracao extends TPage
    {
        use Adianti\Base\AdiantiStandardListTrait;
        use Tr2FormUtilsTrait;
        
        protected $form;
        protected $pageNavigation;
        protected $datagrid; 
        
        public function __construct()
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder('form_ConsultaApuracao');
            $this->form->setFormTitle('Consulta Apuração');
            $this->setDatabase('gexpertlotes');
            $this->setActiveRecord('Apuracao');
            $this->setDefaultOrder('MES desc, EMPREENDIMENTO, QUADRA, LOTE', 'asc');
                      
            $criteria = new TCriteria();
            $criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
            
            $this->setCriteria($criteria);
            
            $this->addFilterField('EMPREENDIMENTO', '>=', 'empto1');
            $this->addFilterField('EMPREENDIMENTO', '<=', 'empto2');
            $this->addFilterField('QUADRA', '>=', 'qda1');
            $this->addFilterField('QUADRA', '<=', 'qda2');
            $this->addFilterField('LOTE', '>=', 'lte1');
            $this->addFilterField('LOTE', '<=', 'lte2');
            $this->addFilterField('MES', '>=', 'mes1');
            $this->addFilterField('MES', '<=', 'mes2');
            $this->addFilterField('DATA', '>=', 'dh1');
            $this->addFilterField('DATA', '<=', 'dh2');
            $this->addFilterField('USUARIO', '=', 'usuario');
            
            
            $empto1 = new TDBSeekButton('empto1', $this->database, $this->form->getName(), 'Empreendimentos', 'DESCRICAO', null, null, $this->criteria);  
            $empto1aux = new THidden('empto1aux');
            $empto1->setAuxiliar($empto1aux);
            $empto2 = new TDBSeekButton('empto2', $this->database, $this->form->getName(), 'Empreendimentos', 'DESCRICAO', null, null, $this->criteria);            
            $empto2aux = new THidden('empto2aux');
            $empto2->setAuxiliar($empto2aux);
            $qda1 = new TEntry('qda1');
            $qda1->setMask(TMascara::maskInt);
            $qda2 = new TEntry('qda2');
            $qda2->setMask(TMascara::maskInt);
            $lte1 = new TEntry('lte1');
            $lte1->setMask(TMascara::maskInt);
            $lte2 = new TEntry('lte2');
            $lte2->setMask(TMascara::maskInt); 
            $mes1 = new TDate('mes1');
            $mes1->setMask(TMascara::maskDate);
            $mes1->setDatabaseMask(TMascara::maskDBDate);
            $mes2 = new TDate('mes2');
            $mes2->setMask(TMascara::maskDate);
            $mes2->setDatabaseMask(TMascara::maskDBDate);
            
            $dh1 = new TDateTime('dh1');
            $dh1->setMask(TMascara::maskDateTime);
            $dh1->setDatabaseMask(TMascara::maskDBDateTime);
            $dh2 = new TDateTime('dh2');
            $dh2->setMask(TMascara::maskDateTime);
            $dh2->setDatabaseMask(TMascara::maskDBDateTime);
            
            $usuario = new TDBCombo('usuario', $this->database, 'SystemUser', 'login', '{login}');  
            
            $empto1->setSize(TWgtSizes::wsInt);
            $empto2->setSize(TWgtSizes::wsInt);
            $qda1->setSize(TWgtSizes::wsInt);
            $qda2->setSize(TWgtSizes::wsInt);
            $lte1->setSize(TWgtSizes::wsInt);
            $lte2->setSize(TWgtSizes::wsInt);  
            $mes1->setSize(TWgtSizes::wsDate);
            $mes2->setSize(TWgtSizes::wsDate); 
            $dh1->setSize(TWgtSizes::wsDateTime);
            $dh2->setSize(TWgtSizes::wsDateTime); 
            $usuario->setSize(TWgtSizes::ws40);        
            
            $this->form->addFields([new TLabel('Empreendimentos de')], [$empto1, '&nbsp&nbspa', $empto2]);
            $this->form->addFields([new TLabel('Quadras de')], [$qda1, '&nbspa', $qda2]);
            $this->form->addFields([new TLabel('Lotes de')], [$lte1, '&nbspa', $lte2]);
            $this->form->addFields([new TLabel('Competencia de')],[$mes1, '&nbspa', $mes2]);
            $this->form->addFields([new TLabel('Data/Hora Apurado de')],[$dh1, '&nbspa', $dh2]);
            $this->form->addFields([new TLabel('Usuário')],[$usuario]);
            
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagrid->style = 'width: 100%';
            
            $column_MES               = $this->datagrid->addQuickColumn('Compet', 'MES', 'left', 0, new TAction([$this, 'onReload'],['order'=>'MES']));
            $column_EMPREENDIMENTO    = $this->datagrid->addQuickColumn('Emp', 'EMPREENDIMENTO', 'left', 0, new TAction([$this, 'onReload'],['order'=>'EMRPEENDIMENTO']));
            $column_QUADRA            = $this->datagrid->addQuickColumn('Qda', 'QUADRA', 'left', 0, new TAction([$this, 'onReload'],['order'=>'QUADRA']));
            $column_LOTE              = $this->datagrid->addQuickColumn('Lte', 'LOTE', 'left', 0, new TAction([$this, 'onReload'],['order'=>'LOTE']));
            $column_VLRCUSTOLOTE      = $this->datagrid->addQuickColumn('Custo Lote', 'VLRCUSTOLOTE', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRCUSTOLOTE']));  
            $column_PERRECEBACUMUL    = $this->datagrid->addQuickColumn('% Rec Acumul', 'PERRECEBACUMUL', 'left', 0, new TAction([$this, 'onReload'],['order'=>'PERRECEBACUMUL']));
            $column_VLRINFRAACUMULADA = $this->datagrid->addQuickColumn('Infra Acumul', 'VLRINFRAACUMULADA', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRINFRAACUMULADA']));
            $column_VLRRECEBACUMULADO = $this->datagrid->addQuickColumn('Receb Acumul', 'VLRRECEBACUMULADO', 'left', 0, new TAction([$this, 'onReload'],['order'=>'PERRECEBACUMULADO']));
            $column_PERRECEBMES       = $this->datagrid->addQuickColumn('% Rec Mes', 'PERRECEBMES', 'left', 0, new TAction([$this, 'onReload'],['order'=>'PERRECEBMES']));  
            $column_VLRINFRAMES       = $this->datagrid->addQuickColumn('Infra Mês', 'VLRINFRAMES', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRINFRAMES'])); 
            $column_VLRCUSTOPROP      = $this->datagrid->addQuickColumn('Custo Prop', 'VLRCUSTOPROP', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRCUSTOPROP']));   
            $column_VLRCUSTOAPROP     = $this->datagrid->addQuickColumn('Custo Aprop', 'VLRCUSTOAPROP', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRCUSTOAPROP']));  
            $column_VLRCUSTOLP        = $this->datagrid->addQuickColumn('Custo Lp', 'VLRCUSTOLP', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRCUSTOLP'])); 
            $column_VLRCUSTOCP        = $this->datagrid->addQuickColumn('Custo Cp', 'VLRCUSTOCP', 'left', 0, new TAction([$this, 'onReload'],['order'=>'VLRCUSTOCP'])); 
            $column_USUARIO           = $this->datagrid->addQuickColumn('Usuario', 'USUARIO', 'left', 0, new TAction([$this, 'onReload'],['order'=>'USUARIO']));
            $column_DATA              = $this->datagrid->addQuickColumn('Data', 'DATA', 'left', 0, new TAction([$this, 'onReload'],['order'=>'DATA']));           
            
            $column_DATA->setTransformer([$this, 'asDateTime']);            
            $column_MES->setTransformer([$this, 'asDate']);
            
            $column_VLRCUSTOLOTE->setTransformer([$this, 'asCurBR']);
            $column_VLRCUSTOAPROP->setTransformer([$this, 'asCurBR']);
            $column_VLRCUSTOCP->setTransformer([$this, 'asCurBR']);
            $column_VLRCUSTOLP->setTransformer([$this, 'asCurBR']);
            $column_VLRCUSTOPROP->setTransformer([$this, 'asCurBR']);
            $column_VLRINFRAACUMULADA->setTransformer([$this, 'asCurBR']);
            $column_VLRINFRAMES->setTransformer([$this, 'asCurBR']);
            $column_VLRRECEBACUMULADO->setTransformer([$this, 'asCurBR']);
            
            $column_PERRECEBACUMUL->setTransformer([$this, 'asDoubleBR']);
            $column_PERRECEBMES->setTransformer([$this, 'asDoubleBR']);
            
            $this->pageNavigation = new TPageNavigation;
            $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
            $this->pageNavigation->setWidth($this->datagrid->getWidth());
            $this->pageNavigation->enableCounters();
            
            $this->datagrid->createModel();
            
            $button = $this->form->addAction(_t('Search'), new TAction([$this, 'onSearch']), 'fa:search');
            $button->class = 'btn btn-success';
            $this->form->addAction(_t('Clear Filters'), new TAction([$this,'clearFilters']),'fa:eraser red'); 
            
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
