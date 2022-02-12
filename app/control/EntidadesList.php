<?php
/**
 * EntidadesList Listing
 * @author  <your name here>
 */
class EntidadesList extends TPage
{
    protected $form;     
    protected $datagrid; 
    protected $pageNavigation;

    use Adianti\base\AdiantiStandardListTrait;
    use Tr2FormUtilsTrait;
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('gexpertlotes');          
        $this->setActiveRecord('Entidades');  
        $this->setLimit(20);
        $this->setDefaultOrder('RAZAO', 'asc');    

        $this->addFilterField('CODIGO', '=', 'CODIGO'); 
        $this->addFilterField('RAZAO', 'like', 'RAZAO'); 
        $this->addFilterField('FANTASIA', 'like', 'FANTASIA'); 
        $this->addFilterField('CNPJ', 'like', 'CNPJ'); 
        $this->addFilterField('CPF', 'like', 'CPF'); 
        $this->addFilterField('IE', 'like', 'IE'); 
        $this->addFilterField('CIDADE', 'like', 'CIDADE'); 
        $this->addFilterField('UF', '=', 'UF'); 
        
        $this->form = new BootstrapFormBuilder('form_Entidades');
        $this->form->setFormTitle('Entidades');

        $CODIGO     = new TEntry('CODIGO');
        $RAZAO      = new TEntry('RAZAO');
        $FANTASIA   = new TEntry('FANTASIA');
        $CNPJ       = new TEntry('CNPJ');
        $CPF        = new TEntry('CPF');
        $IE         = new TEntry('IE');
        $UF         = new TDBCombo('UF', 'gexpertlotes', 'Estados', 'CODIGO', '{NOME} ({SIGLA})');
        $CIDADE     = new TDBCombo('CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', '{RAIS}. {NOME}');
        
        $UF->setChangeAction(new TAction([$this, 'onChangeUF'],['static'=>'1']));
        $UF->enableSearch();
        $CIDADE->enableSearch();

        $row = $this->form->addFields( [ new TLabel('Codigo'), $CODIGO ], [ new TLabel('Razao'), $RAZAO ], [ new TLabel('Fantasia'), $FANTASIA ]  );
        $row->layout = ['col-md-2', 'col-md-5', 'col-md-5'];
        
        $row = $this->form->addFields( [ new TLabel('Cnpj'), $CNPJ ], [ new TLabel('Cpf'), $CPF ], [ new TLabel('Ie'), $IE ] );
        $row->layout = ['col-md-4', 'col-md-4', 'col-md-4'];

        $row = $this->form->addFields( [ new TLabel('Uf'), $UF ], [ new TLabel('Cidade'), $CIDADE ]  );
        $row->layout = ['col-md-4', 'col-md-8'];
        
        $CNPJ->setMask(TMascara::maskCNPJ);
        $CPF->setMask(TMascara::maskCPF);    

        $CODIGO->setSize('100%');
        $RAZAO->setSize('100%');
        $FANTASIA->setSize('100%');
        $CNPJ->setSize('100%');
        $CPF->setSize('100%');
        $IE->setSize('100%');
        $CIDADE->setSize('100%');
        $UF->setSize('100%');
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'), new TAction(['EntidadesForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $column_CODIGO     = new TDataGridColumn('CODIGO', 'Codigo', 'left', 1);
        $column_RAZAO      = new TDataGridColumn('RAZAO', 'Razao', 'left');
        $column_FANTASIA   = new TDataGridColumn('FANTASIA', 'Fantasia', 'left');
        $column_TIPO       = new TDataGridColumn('TIPO', 'Tipo', 'left', 1);
        $column_CNPJ       = new TDataGridColumn('CNPJ', 'Cnpj', 'left', 1);
        $column_CPF        = new TDataGridColumn('CPF', 'Cpf', 'left', 1);
        $column_IE         = new TDataGridColumn('IE', 'Ie', 'left', 1);
        $column_CEP        = new TDataGridColumn('CEP', 'Cep', 'left', 1);
        $column_CIDADE     = new TDataGridColumn('cidades->NOME', 'Cidade', 'left',1);
        $column_UF         = new TDataGridColumn('estados->SIGLA', 'Uf', 'left', 1);
        $column_ATIVO      = new TDataGridColumn('ATIVO', 'Ativo', 'left', 1);

        $column_CODIGO->setAction(new TAction([$this, 'onReload'],['order'=>'CODIGO'])); 
        $column_RAZAO->setAction(new TAction([$this, 'onReload'],['order'=>'RAZAO']));
        $column_FANTASIA->setAction(new TAction([$this, 'onReload'],['order'=>'FANTASIA']));
        $column_TIPO->setAction(new TAction([$this, 'onReload'],['order'=>'TIPO']));
        $column_CNPJ->setAction(new TAction([$this, 'onReload'],['order'=>'CNPJ']));
        $column_CPF->setAction(new TAction([$this, 'onReload'],['order'=>'CPF']));
        $column_IE->setAction(new TAction([$this, 'onReload'],['order'=>'IE']));
        $column_CEP->setAction(new TAction([$this, 'onReload'],['order'=>'CEP']));
        $column_CIDADE->setAction(new TAction([$this, 'onReload'],['order'=>'CIDADE']));
        $column_UF->setAction(new TAction([$this, 'onReload'],['order'=>'UF']));
        $column_ATIVO->setAction(new TAction([$this, 'onReload'],['order'=>'ATIVO']));

        $this->datagrid->addColumn($column_CODIGO);
        $this->datagrid->addColumn($column_RAZAO);
        $this->datagrid->addColumn($column_FANTASIA);
        $this->datagrid->addColumn($column_TIPO);
        $this->datagrid->addColumn($column_CNPJ);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_IE);
        $this->datagrid->addColumn($column_CEP);
        $this->datagrid->addColumn($column_CIDADE);
        $this->datagrid->addColumn($column_UF);
        $this->datagrid->addColumn($column_ATIVO);
        
        $column_ATIVO->setTransformer([$this, 'asBooleanBR']);
        $column_TIPO->setTransformer([$this, 'asTipoPessoa']);
        
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['EntidadesForm', 'onEdit']), 'CODIGO', 'far:edit blue');    
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction([$this, 'onDelete']), 'CODIGO', 'far:trash-alt red');
        
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
    
    public function onChangeUF($param)
    {
        if(isset($param['UF']))
        {
            $criteria = new TCriteria();
            $criteria->add(new TFilter('UF', '=', $param['UF']));
            
            TDBCombo::reloadFromModel($this->form->getName(), 'CIDADE', $this->database, 'Municipios', 'CODIGO', '{RAIS}. {NOME}', 'NOME', $criteria, TRUE);
        }
    }
    

}
