<?php
/**
 * VendasList Listing
 * @author  <your name here>
 */
class VendasList extends TTr2Page
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    
    use Adianti\Base\AdiantiStandardListTrait;
    use Tr2FormUtilsTrait;
    
    public function __construct()
    {     
        parent::__construct();    
          
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Vendas');
        $this->setCriteria(new TCriteria);
        $this->criteria->add(new TFilter('EMPRESA','=',TSession::getValue('userunitid')));
        
        $this->addFilterField('LANCAMENTO', '=');
        $this->addFilterField('EMPREENDIMENTO', '=');
        $this->addFilterField('ENTIDADE', '=');
        $this->addFilterField('EMISSAO', '=', 'EMISSAO', [$this, 'asDateSQL']);
        $this->addFilterField('QUADRA', '=');
        $this->addFilterField('LOTE', '=');
        $this->addFilterField('CONTRATO', 'like');
        $this->addFilterField('CANCELADO', '=');
        
        $this->setOrderCommand('entidades->RAZAO', '(select RAZAO from entidades where CODIGO=vendas.ENTIDADE)');
        $this->setOrderCommand('empreendimentos->DESCRICAO', '(select DESCRICAO from empreendimentos where CODIGO=vendas.ENTIDADE)');
        
        $this->form = new BootstrapFormBuilder('form_Vendas');
        $this->form->setFormTitle('Vendas');
        
        $LANCAMENTO = new TEntry('LANCAMENTO');
        $EMPREENDIMENTO = new TDBSeekButton('EMPREENDIMENTO', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 'EMPREENDIMENTO', '', $this->criteria);
        $DS_EMPRENDIMENTO = new TEntry('DS_EMPREENDIMENTO');
        $DS_EMPRENDIMENTO->setEditable(FALSE);
        $EMPREENDIMENTO->setAuxiliar($DS_EMPRENDIMENTO);
        $ENTIDADE = new TDBSeekButton('ENTIDADE', 'gexpertlotes', $this->form->getName(), 'Entidades', 'RAZAO', 'ENTIDADE');
        $DS_ENTIDADE = new TEntry('DS_ENTIDADE');
        $DS_ENTIDADE->setEditable(FALSE);
        $ENTIDADE->setAuxiliar($DS_ENTIDADE);
        $EMISSAO = new TDate('EMISSAO');
        $QUADRA = new TEntry('QUADRA');
        $LOTE = new TEntry('LOTE');
        $CONTRATO = new TEntry('CONTRATO');
        $CANCELADO = new TCombo('CANCELADO');
        $CANCELADO->addItems(['S'=>'Sim','N'=>'Não']);
        $EMISSAO->setMask('dd/mm/yyyy');
        
        $LANCAMENTO->setSize(TWgtSizes::wsDef);
        $EMPREENDIMENTO->setSize(TWgtSizes::wsInt);
        $DS_EMPRENDIMENTO->setSize(TWgtSizes::wsAux);
        $ENTIDADE->setSize(TWgtSizes::wsInt);
        $DS_ENTIDADE->setSize(TWgtSizes::wsAux);
        $EMISSAO->setSize(TWgtSizes::wsDef);
        $QUADRA->setSize(TWgtSizes::wsDef);
        $LOTE->setSize(TWgtSizes::wsDef);
        $CONTRATO->setSize(TWgtSizes::wsDef);
        $CANCELADO->setSize(TWgtSizes::wsDef);

        $row = $this->form->addFields( [ new TLabel('Lancamento'),  $LANCAMENTO ], [ new TLabel('Empreendimento') , $EMPREENDIMENTO ] );
        
        $row->layout = ['col-sm-2', 'col-sm-10'];
        
        $row = $this->form->addFields( [ new TLabel('Entidade'), $ENTIDADE ], [ new TLabel('Emissao') ,  $EMISSAO ]  );
        
        $row->layout = ['col-sm-9', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Quadra'), $QUADRA ], 
                                       [ new TLabel('Lote'), $LOTE ], 
                                       [ new TLabel('Contrato'), $CONTRATO ], 
                                       [ new TLabel('Cancelado'), $CANCELADO ]  );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-5', 'col-sm-3'];                                        
         
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class='btn btn-success btn-sm';
        $this->form->addAction(_t('New'), new TAction(['VendasForm', 'onEdit'],['status'=>'insert','page'=>0, 'reset'=>1]), 'fa:plus blue');
        $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width:100%';
        //$this->datagrid->datatable = 'true';
        
        $column_EMPRESA =$this->datagrid->addQuickColumn('Empresa', 'EMPRESA', 'left', 0, new TAction([$this, 'onReload'],['order'=>'EMPRESA']));
        $column_LANCAMENTO = $this->datagrid->addQuickColumn('Lancamento', 'LANCAMENTO', 'left', 0, new TAction([$this, 'onReload'],['order'=>'LANCAMENTO']));
        $column_EMPREENDIMENTO = $this->datagrid->addQuickColumn('Empreendimento','{EMPREENDIMENTO}. {empreendimentos->DESCRICAO}', 'left', 300, new TAction([$this, 'onReload'],['order'=>'empreendimentos->DESCRICAO']));
        $column_ENTIDADE = $this->datagrid->addQuickColumn('Entidade', '{ENTIDADE}. {entidades->RAZAO}', 'left', 300, new TAction([$this, 'onReload'],['order'=>'entidades->RAZAO']));
        $column_EMISSAO = $this->datagrid->addQuickColumn('Emissão', 'EMISSAO', 'left', 1, new TAction([$this, 'onReload'],['order'=>'EMISSAO']));
        $column_QUADRA = $this->datagrid->addQuickColumn('Quadra', 'QUADRA', 'left', 1, new TAction([$this, 'onReload'],['order'=>'QUADRA']));
        $column_LOTE = $this->datagrid->addQuickColumn('Lote', 'LOTE', 'left', 1, new TAction([$this, 'onReload'],['order'=>'LOTE']));
        $column_VALOR = $this->datagrid->addQuickColumn('Valor', 'VALOR', 'left', 1, new TAction([$this, 'onReload'],['order'=>'VALOR']));
        $column_ENTRADA = $this->datagrid->addQuickColumn('Entrada', 'ENTRADA', 'left', 1, new TAction([$this, 'onReload'],['order'=>'VALOR']));
        $column_CONTRATO = $this->datagrid->addQuickColumn('Contrato', 'CONTRATO', 'left', 200, new TAction([$this, 'onReload'],['order'=>'CONTRATO']));
        $column_CANCELADO = $this->datagrid->addQuickColumn('Cancelado','CANCELADO', 'left', 200, new TAction([$this, 'onReload'],['order'=>'CANCELADO']));
        $column_CANCELAMENTO = $this->datagrid->addQuickColumn('Cancelamento', 'CANCELAMENTO', 'left', 200, new TAction([$this, 'onReload'],['order'=>'CANCELAMENTO']));

        $column_EMISSAO->setTransformer([$this, 'asDate']);
        $column_CANCELAMENTO->setTransformer([$this, 'asDate']);
        $column_VALOR->setTransformer([$this, 'asCurBr']);
        $column_ENTRADA->setTransformer([$this, 'asCurBr']);  
        $column_CANCELADO->setTransformer([$this, 'asBooleanBR']);  
        
        $this->datagrid->addQuickAction(_t('Edit'),new TDataGridAction(['VendasForm', 'onEdit'], ['status'=>'edit', 'page'=>0, 'reset'=>1]), 'LANCAMENTO', 'far:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'),new TDataGridAction([$this, 'onDelete']), 'LANCAMENTO', 'far:trash-alt red');              
        $this->datagrid->createModel();
                
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->enableCounters();

        $dropdow = new TDropDown(_t('Export'), 'fa:list');
        $dropdow->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV']), 'fa:table fa-fw blue');
        $dropdow->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF']), 'far:file-pdf fa-fw red');
        $dropdow->addAction(_t('Save as XML'), new TAction([$this, 'onExportXML']), 'fa:code fa-fw green');
        
        $panel = new TPanelGroup('Resultados');
        $panel->style='width:100%';
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        $panel->addHeaderWidget($dropdow);
                 
        $container = new TVBox;
        $container->style = 'width: 100%; overflow-x:auto;';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
        
        $this->form->setData(TSession::getValue($this->activeRecord.'_filter_data'));           
    }
    
    
}
