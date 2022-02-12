<?php
/**
 * EmpresasList Listing
 * @author  <your name here>
 */
class EmpresasList extends TPage
{
    use Adianti\Base\AdiantiStandardListTrait;
    
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Empresas');
        $this->form->setFormTitle('Consulta Empresas');
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Empresas');        
        
        TTransaction::open($this->database);
        $user = new SystemUser(TSession::getValue('userid'));  
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CODIGO', 'IN', $user->getSystemUserUnitIds()));        
        $this->setCriteria($criteria);
        TTransaction::close();        
        
        $this->addFilterField('CODIGO');
        $this->addFilterField('RAZAO', 'like');
        $this->addFilterField('FANTASIA', 'like');
        $this->addFilterField('CNPJ', 'like');
        $this->addFilterField('CIDADE');
        $this->addFilterField('UF');
        
        $this->setOrderCommand('{municipio->NOME}', '(select NOME from Municipios where CODIGO=Empresas.CIDADE)');
        $this->setOrderCommand('{estado->NOME}', '(select NOME from Estados where CODIGO=Empresas.UF)');
        
        // create the form fields
        $CODIGO = new TEntry('CODIGO');
        $RAZAO = new TEntry('RAZAO');
        $FANTASIA = new TEntry('FANTASIA');
        $CNPJ = new TEntry('CNPJ');
        $CPF = new TEntry('CPF');
        $CIDADE = new TDBCombo('CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', '{RAIS}. {NOME}');
        $CIDADE = new TCombo('CIDADE');
        $CIDADE->enableSearch();
        $UF = new TDBCombo('UF', 'gexpertlotes', 'Estados', 'CODIGO', '{NOME} ({SIGLA})');
        $UF->setChangeAction(new TAction([$this, 'onChangeUF'],['static'=>'1']));
        $UF->enableSearch();


        // add the fields
        $row = $this->form->addFields( [ new TLabel('Codigo'), $CODIGO ], [ new TLabel('Razao'), $RAZAO ] );
        $row->layout = ['col-md-2', 'col-md-10'];
        $row = $this->form->addFields( [ new TLabel('Fantasia'), $FANTASIA ], [ new TLabel('Cnpj'), $CNPJ ], [ new TLabel('Cpf'), $CPF ]  );
        $row->layout = ['col-md-6', 'col-md-3', 'col-md-3'];
       
        $row = $this->form->addFields( [ new TLabel('Uf'), $UF ], [ new TLabel('Cidade'), $CIDADE ] );
        $row->layout = ['col-md-5', 'col-md-7'];


        // set sizes and masks
        $CODIGO->setSize(TWgtSizes::wsDef);
        $RAZAO->setSize(TWgtSizes::wsDef);
        $FANTASIA->setSize(TWgtSizes::wsDef);
        $CNPJ->setSize(TWgtSizes::wsDef);
        $CNPJ->setMask(TMascara::maskCNPJ);
        $CPF->setSize(TWgtSizes::wsDef);
        $CPF->setMask(TMascara::maskCPF);
        $CIDADE->setSize(TWgtSizes::wsDef);
        $UF->setSize(TWgtSizes::wsDef);

        
        // add the search form actions
        $actOnSearch = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search fa-lg');
        $actOnSearch->class = 'btn btn-sm btn-success';
        $this->form->addActionLink(_t('New'), new TAction(['EmpresasForm', 'onEdit']), 'fa:plus green fa-lg');
        $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fas:eraser red fa-lg');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // creates the datagrid columns
        $column_CODIGO = new TDataGridColumn('CODIGO', 'Codigo', 'right');
        $column_RAZAO = new TDataGridColumn('RAZAO', 'Razao', 'left');
        $column_FANTASIA = new TDataGridColumn('FANTASIA', 'Fantasia', 'left');
        $column_TIPO = new TDataGridColumn('TIPO', 'Tipo', 'left');
        $column_CNPJ = new TDataGridColumn('CNPJ', 'Cnpj', 'left');
        $column_CPF = new TDataGridColumn('CPF', 'Cpf', 'left');
        $column_IE = new TDataGridColumn('IE', 'Ie', 'left');
        $column_ENDERECO = new TDataGridColumn('ENDERECO', 'Endereco', 'left');
        $column_NUMERO = new TDataGridColumn('NUMERO', 'Numero', 'right');
        $column_BAIRRO = new TDataGridColumn('BAIRRO', 'Bairro', 'left');
        $column_COMPLEMENTO = new TDataGridColumn('COMPLEMENTO', 'Complemento', 'left');
        $column_CEP = new TDataGridColumn('CEP', 'Cep', 'left');
        $column_CIDADE = new TDataGridColumn('{municipio->NOME}', 'Cidade', 'right');
        $column_UF = new TDataGridColumn('UF', 'Uf', 'right');
        $column_DDD = new TDataGridColumn('DDD', 'Ddd', 'left');
        $column_FONE = new TDataGridColumn('FONE', 'Fone', 'left');
        $column_CELULAR = new TDataGridColumn('CELULAR', 'Celular', 'left');
        $column_FAX = new TDataGridColumn('FAX', 'Fax', 'left');
        $column_ATIVO = new TDataGridColumn('ATIVO', 'Ativo', 'left');
        $column_DATACAD = new TDataGridColumn('DATACAD', 'Datacad', 'left');
        $column_USUARIOCAD = new TDataGridColumn('USUARIOCAD', 'Usuariocad', 'right');
        $column_DATAALT = new TDataGridColumn('DATAALT', 'Dataalt', 'left');
        $column_USUARIOALT = new TDataGridColumn('USUARIOALT', 'Usuarioalt', 'right');
        $column_LOGO = new TDataGridColumn('LOGO', 'Logo', 'left');
        $column_OBSERVACAO = new TDataGridColumn('OBSERVACAO', 'Observacao', 'left');
        $column_OBSERVACAONF = new TDataGridColumn('OBSERVACAONF', 'Observacaonf', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_CODIGO);
        $this->datagrid->addColumn($column_RAZAO);
        $this->datagrid->addColumn($column_FANTASIA);
        $this->datagrid->addColumn($column_TIPO);
        $this->datagrid->addColumn($column_CNPJ);
        $this->datagrid->addColumn($column_CPF);
        $this->datagrid->addColumn($column_IE);
        $this->datagrid->addColumn($column_ENDERECO);
        $this->datagrid->addColumn($column_NUMERO);
        $this->datagrid->addColumn($column_BAIRRO);
        $this->datagrid->addColumn($column_COMPLEMENTO);
        $this->datagrid->addColumn($column_CEP);
        $this->datagrid->addColumn($column_CIDADE);
        $this->datagrid->addColumn($column_UF);
        $this->datagrid->addColumn($column_DDD);
        $this->datagrid->addColumn($column_FONE);
        $this->datagrid->addColumn($column_CELULAR);
        $this->datagrid->addColumn($column_FAX);
        $this->datagrid->addColumn($column_ATIVO);
        $this->datagrid->addColumn($column_DATACAD);
        $this->datagrid->addColumn($column_USUARIOCAD);
        $this->datagrid->addColumn($column_DATAALT);
        $this->datagrid->addColumn($column_USUARIOALT);
        $this->datagrid->addColumn($column_LOGO);
        $this->datagrid->addColumn($column_OBSERVACAO);
        $this->datagrid->addColumn($column_OBSERVACAONF);

        // define the transformer method over image
        $column_RAZAO->setTransformer( function($value, $object, $row) {
            return strtoupper($value);
        });
        // define the transformer method over image
        $column_FANTASIA->setTransformer( function($value, $object, $row) {
            return strtoupper($value);
        });
        // define the transformer method over image
        $column_LOGO->setTransformer( function($value, $object, $row) {
            if (file_exists($value)) {
                return new TImage($value);
            }
        });

        //Order columns
        $column_CODIGO->setAction(new TAction([$this, 'onReload'], ['order'=>'CODIGO']));
        $column_RAZAO->setAction(new TAction([$this, 'onReload'], ['order'=>'RAZAO']));
        $column_FANTASIA->setAction(new TAction([$this, 'onReload'], ['order'=>'FANTASIA']));
        $column_CNPJ->setAction(new TAction([$this, 'onReload'], ['order'=>'CNPJ']));
        $column_CPF->setAction(new TAction([$this, 'onReload'], ['order'=>'CPF']));
        $column_CIDADE->setAction(new TAction([$this, 'onReload'], ['order'=>'CIDADE']));
        $column_UF->setAction(new TAction([$this, 'onReload'], ['order'=>'UF']));
        
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['EmpresasForm', 'onEdit'], ['status'=>'edit','reset'=>1]), 'CODIGO', 'far:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction([$this, 'onDelete']), 'CODIGO', 'far:trash-alt red');
        $this->datagrid->addQuickAction('Trocar Empresa', new TDataGridAction([$this, 'onSelectUnit']), 'CODIGO', 'fas:share-square fa-fw');  
                
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->enableCounters();
        
        $dropdown_exportar = new TDropDown(_t('Export'), 'fa:list fa-lg');
        $dropdown_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_exportar->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table fa-fw blue' );
        $dropdown_exportar->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf fa-fw red' );
        $dropdown_exportar->addAction( _t('Save as XML'), new TAction([$this, 'onExportXML'], ['register_state' => 'false', 'static'=>'1']), 'fa:code fa-fw green' );
         
        $panel = new TPanelGroup;
        $panel->style = 'width:100%';
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        $panel->addHeaderWidget($dropdown_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
        
        $this->form->setData(TSession::getValue($this->activeRecord.'_filter_data'));
    }
    
    function onSelectUnit($param)
    {
        $data = (object) $param;
            
        ApplicationAuthenticationService::setUnit( $data->CODIGO ?? null );
        SystemAccessLogService::registerLogin();
        AdiantiCoreApplication::gotoPage('EmptyPage'); // reload     
    }    

    function onChangeUF($param) 
    {
        $filter = new TCriteria();
        $filter->add(new TFilter('UF','=', $param['UF']));
        
        TDBCombo::reloadFromModel($this->form->getName(), 'CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', "{RAIS}. {NOME}", "concat(RAIS, '. ', NOME)", $filter);
    }    
}
