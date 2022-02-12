<?php
/**
 * InfraestruturaList Listing
 * @author  <your name here>
 */
class InfraestruturaList extends TTr2Page
{
    use Adianti\Base\AdiantiStandardListTrait;
    use Tr2FormUtilsTrait;
       
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Infraestrutura');
        $this->form->setFormTitle('Infraestrutura');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Infraestrutura');
        $this->setLimit(20);
        $this->setDefaultOrder('DATA', 'desc');
        
        $this->setOrderCommand('empreendimentos->DESCRICAO', '(select DESCRICAO) from empreendimentos where CODIGO=infraestrutura.EMPREENDIMENTO');
        
        $this->addFilterField('EMPREENDIMENTO');
        $this->addFilterField('DATA', '=', 'DATA', [$this, 'asDateSQL']);
        $this->addFilterField('VALOR', 'like', 'VALOR', [$this, 'asCurrencySQL']);
        $this->addFilterField('CONTACTB');
        $this->addFilterField('HISTORICO');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
        $this->setCriteria($criteria);

        $LANCAMENTO = new TEntry('LANCAMENTO');

        $EMPREENDIMENTO = new TDBSeekButton('EMPREENDIMENTO', $this->database, $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 'EMPREENDIMENTO', 'DSEMPREENDIMENTO', $this->criteria);
        $DSEMPREENDIMENTO = new TEntry('DSEMPREENDIMENTO');
        $DSEMPREENDIMENTO->setEditable(FALSE);
        $EMPREENDIMENTO->setAuxiliar($DSEMPREENDIMENTO);
            
        $HISTORICO = new TTr2DBSeekButton('HISTORICO', $this->database, $this->form->getName(), 'Historicos', 'DESCRICAO', 'HISTORICO');
        $HISTORICOAUX = new TEntry('HISTORICOAUX');
        $HISTORICOAUX->setEditable(FALSE);
        $HISTORICO->setAuxiliar($HISTORICOAUX);
        $DATA = new TDate('DATA');
        $DATA->setMask(TMascara::maskDate);
        $DATA->setDatabaseMask(TMascara::maskDate);
        $VALOR = new TEntry('VALOR');
        
        $CONTACTB = new TTr2DBSeekButton('CONTACTB', $this->database, $this->form->getName(), 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTB', NULL, $this->criteria);
        $CONTACTB->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTB->setModelKey('CODIGO');
        $CONTACTBAUX = new TEntry('CONTACTBAUX');
        $CONTACTBAUX->setEditable(FALSE);
        $CONTACTB->setAuxiliar($CONTACTBAUX);

        $row = $this->form->addFields( [ new TLabel('Empreendimento'), $EMPREENDIMENTO ],  [ new TLabel('Data'), $DATA ], [ new TLabel('Valor'), $VALOR ]);
        $row->layout = ['col-md-6', 'col-md-3', 'col-md-3'];
 
        $row = $this->form->addFields([ new TLabel('Conta ctb'), $CONTACTB ] , [ new TLabel('Histórico'), $HISTORICO ] );
        $row->layout = ['col-md-6', 'col-md-6'];
        
        $EMPREENDIMENTO->setSize(80);
        $DSEMPREENDIMENTO->setSize('calc(100% - 100px)');
        $DATA->setSize('100%');
        $VALOR->setSize('100%');
        $CONTACTB->setSize(80);
        $CONTACTBAUX->setSize('calc(100% - 100px)');
        $HISTORICO->setSize(80);
        $HISTORICOAUX->setSize('calc(100% - 100px)');

        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'), new TAction(['InfraestruturaForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
        
        $this->datagrid            = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style     = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        $column_LANCAMENTO     = new TDataGridColumn('LANCAMENTO', 'Lancamento', 'left', 1);
        $column_EMPREENDIMENTO = new TDataGridColumn('{EMPREENDIMENTO}. {empreendimentos->DESCRICAO}', 'Empreendimento','left', 200);
        $column_DATA           = new TDataGridColumn('DATA', 'Data', 'left');
        $column_VALOR          = new TDataGridColumn('VALOR', 'Valor', 'right');
        $column_CONTACTB       = new TDataGridColumn('CONTACTB', 'Conta ctb', 'right', 1);
        $column_HISTORICO      = new TDataGridColumn('HISTORICO', 'Histórico', 'right', 1);
        $column_OBSERVACAO     = new TDataGridColumn('OBSERVACAO', 'Observacao', 'left');
        
        $column_DATA->setTransformer([$this, 'asDate']);
        $column_VALOR->setTransformer([$this, 'asCurBr']);
        
        $column_LANCAMENTO->setAction(new TAction([$this, 'onReload'], ['order'=>'LANCAMENTO']));
        $column_EMPREENDIMENTO->setAction(new TAction([$this, 'onReload'], ['order'=>'empreendimentos->DESCRICAO']));
        $column_DATA->setAction(new TAction([$this, 'onReload'], ['order'=>'DATA']));
        $column_VALOR->setAction(new TAction([$this, 'onReload'], ['order'=>'VALOR']));
        
        $this->datagrid->addColumn($column_LANCAMENTO);
        $this->datagrid->addColumn($column_EMPREENDIMENTO);
        $this->datagrid->addColumn($column_DATA);
        $this->datagrid->addColumn($column_VALOR);
        $this->datagrid->addColumn($column_CONTACTB);
        $this->datagrid->addColumn($column_HISTORICO);
        $this->datagrid->addColumn($column_OBSERVACAO);

        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['InfraestruturaForm', 'onEdit']), 'LANCAMENTO', 'far:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction([$this, 'onDelete']), 'LANCAMENTO', 'far:trash-alt red');
       
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
                   
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
