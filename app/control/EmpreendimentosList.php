<?php
/**
 * EmpreendimentosList Listing
 * @author  <your name here>
 */
class EmpreendimentosList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $deleteButton;
    
    use Adianti\Base\AdiantiStandardListTrait; 
    use Tr2FormUtilsTrait;
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Empreendimentos');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
        $this->setCriteria($criteria);
        
        $asDateBR = function($value)
        {
            return TConversion::asDate($value);           
        };
        
        $asDoubleBR = function($value)
        {
            return TConversion::asDoubleBR($value,2);
        };
        
        $this->addFilterField('CODIGO');
        $this->addFilterField('TIPO');
        $this->addFilterField('DESCRICAO','like');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('frm_'.__CLASS__);
        $this->form->setFormTitle('Listagem de Empreendimentos');

        // create the form fields
        $CODIGO     = new TEntry('CODIGO');
        $TIPO       = new TCombo('TIPO');
        $DESCRICAO  = new TEntry('DESCRICAO');
        $CIDADE     = new TDBCombo('CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', '{RAIS}. {NOME}', 'concat(RAIS,NOME)');
        $CIDADE->enableSearch();
        $UF         = new TDBCombo('UF', 'gexpertlotes', 'Estados', 'CODIGO', '{NOME} ({SIGLA})', 'SIGLA');
        $UF->setChangeAction(new TAction([$this, 'onChangeUF']));
        $UF->enableSearch();

        $TIPO->addItems(['0'=>'0-Urbano','1'=>'1-Rural']);
        
        // add the fields
        $row = $this->form->addFields( [ new TLabel('Codigo'), $CODIGO ], 
                                       [ new TLabel('Tipo'), $TIPO ],
                                       [ new TLabel('Descricao'), $DESCRICAO ] );
        
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-7'];
        
        $row = $this->form->addFields( [ new TLabel('Uf'), $UF ],
                                       [ new TLabel('Cidade'), $CIDADE ] );
        
        $row->layout = ['col-sm-4', 'col-sm-8'];
        
        // set sizes
        $CODIGO->setSize(TWgtSizes::wsDef);
        $TIPO->setSize(TWgtSizes::wsDef);
        $DESCRICAO->setSize(TWgtSizes::wsDef);
        $CIDADE->setSize(TWgtSizes::wsDef);
        $UF->setSize(TWgtSizes::wsDef);

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Empreendimentos_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fas:eraser red');
        $this->form->addAction(_t('New'), new TAction(['EmpreendimentosForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        $column_CODIGO                 = $this->datagrid->addQuickcolumn( 'Codigo','CODIGO', 'right',200, new TAction([$this, 'onReload'],['order'=>'CODIGO']) );
        $column_TIPO                   = $this->datagrid->addQuickcolumn( 'Tipo','TIPO', 'left',200, new TAction([$this, 'onReload'],['order'=>'TIPO']) );
        $column_DESCRICAO              = $this->datagrid->addQuickcolumn( 'Descricao','DESCRICAO', 'left',200, new TAction([$this, 'onReload'],['order'=>'DESCRICAO']) );
        $column_DATAAQUISICAO          = $this->datagrid->addQuickcolumn( 'Aquisicao','DATAAQUISICAO', 'left',200, new TAction([$this, 'onReload'],['order'=>'DATAAQUISICAO']) );
        $column_VLRAQUISICAO           = $this->datagrid->addQuickcolumn( 'Vlr Aquisicao','VLRAQUISICAO', 'right',200, new TAction([$this, 'onReload'],['order'=>'VLRAQUISICAO']) );
        $column_AREATOTAL              = $this->datagrid->addQuickcolumn( 'Area total','AREATOTAL', 'right',200, new TAction([$this, 'onReload'],['order'=>'AREATOTAL']) );
        $column_QUADRAS                = $this->datagrid->addQuickcolumn( 'Quadras','QUADRAS', 'right',200, new TAction([$this, 'onReload'],['order'=>'QUADRAS']) );
        $column_LOTES                  = $this->datagrid->addQuickcolumn( 'Lotes','LOTES', 'right',200, new TAction([$this, 'onReload'],['order'=>'LOTES']) );
        $column_CIDADE                 = $this->datagrid->addQuickcolumn( 'Cidade','{municipio->NOME}', 'right',200, new TAction([$this, 'onReload'],['order'=>'CIDADE']) );
        $column_UF                     = $this->datagrid->addQuickcolumn( 'Uf','{estado->SIGLA}', 'right',200, new TAction([$this, 'onReload'],['order'=>'UF']) );
        $column_ATIVO                  = $this->datagrid->addQuickcolumn( 'Ativo','ATIVO', 'left',200, new TAction([$this, 'onReload'],['order'=>'ATIVO']) );
        $column_CONTACTB               = $this->datagrid->addQuickcolumn( 'Conta ctb','CONTACTB', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTB']) );
        $column_CONTACTBRECEITA        = $this->datagrid->addQuickcolumn( 'Conta ctb receita','CONTACTBRECEITA', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBRECEITA']) );
        $column_CONTACTBCUSTO          = $this->datagrid->addQuickcolumn( 'Conta ctb custo','CONTACTBCUSTO', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBCUSTO']) );
        $column_CONTACTBDEVOLUCAO      = $this->datagrid->addQuickcolumn( 'Conta ctb devolucao','CONTACTBDEVOLUCAO', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBDEVOLUCAO']) );
        $column_CONTACTBINFRAESTRUTURA = $this->datagrid->addQuickcolumn( 'Conta ctb infraestrutura','CONTACTBINFRAESTRUTURA', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBINFRAESTRUTURA']) );
        $column_CONTACTBPAGTO          = $this->datagrid->addQuickcolumn( 'Conta ctb pagto','CONTACTBPAGTO', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBPAGTO']) );
        $column_CONTACTBATUALIZACAO    = $this->datagrid->addQuickcolumn( 'Conta ctb atualizacao','CONTACTBATUALIZACAO', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBATUALIZACAO']) );
        $column_CONTACTBJUROS          = $this->datagrid->addQuickcolumn( 'Conta ctb juros','CONTACTBJUROS', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBJUROS']) );
        $column_CONTACTBRECEITAEVENTUAL= $this->datagrid->addQuickcolumn( 'Conta ctb receita eventual','CONTACTBRECEITAEVENTUAL', 'right',200, new TAction([$this, 'onReload'],['order'=>'CONTACTBRECEITAEVENTUAL']) );
        
        $column_DATAAQUISICAO->setTransformer([$this, 'asDate']);
        $column_VLRAQUISICAO->setTransformer([$this, 'asCurBR']);
        $column_AREATOTAL->setTransformer([$this, 'asDoubleBR']);
        $column_ATIVO->setTransformer([$this, 'asBooleanBR']);
        
        $column_TIPO->setTransformer(
            function($value)
            {
                return ($value==0)? '0-Urbano' : '1-Rural';
            }
        );
        
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['EmpreendimentosForm', 'onEdit'], ['reset'=>1, 'status'=>'edit', 'page'=>0]), 'CODIGO', 'far:edit blue');
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
        
        $panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation);
        $panel->addHeaderWidget($dropdown);
                
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
        
        $this->form->setData(TSession::getValue($this->activeRecord.'_filter_data'));
    }
    
    static function onChangeUF($param)
    {
        if(isset($param['UF']))
        {
            $uf = $param['UF'];
            
            $criteria = new TCriteria;
            $criteria->add(new TFilter('UF', '=', $uf));
            
            TDBCombo::reloadFromModel('frm_'.__CLASS__, 'CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', '{RAIS}. {NOME}', 'concat(RAIS, NOME)', $criteria);              
        }
    }
}
