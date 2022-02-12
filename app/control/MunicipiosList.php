<?php
/**
 * MunicipiosList Listing
 * @author  <your name here>
 */
class MunicipiosList extends TPage
{
    use Adianti\Base\AdiantiStandardListTrait;
    use Tr2FormUtilsTrait;
    
    protected $form; 
    protected $datagrid; 
    protected $pageNavigation;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Municipios');
        $this->form->setFormTitle('Municipios');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Municipios');
        $this->setDefaultOrder('(select SIGLA from estados where CODIGO=municipios.UF), RAIS', 'asc');
        
        $this->setOrderCommand('estado->SIGLA', '(select SIGLA from estados where CODIGO=Municipios.UF)');
        
        $this->addFilterField('CODIGO');
        $this->addFilterField('UF');
        $this->addFilterField('NOME');
        $this->addFilterField('CEP');
        $this->addFilterField('RAIS');

        // create the form fields
        $CODIGO = new TEntry('CODIGO');
        $UF = new TDBCombo('UF', $this->database, 'Estados', 'CODIGO', '{NOME} ({SIGLA})');
        $UF->enableSearch();        
        $NOME = new TEntry('NOME');
        $CEP = new TEntry('CEP');
        $CEP->setMask(TMascara::maskCEP);
        $RAIS = new TEntry('RAIS');
        $RAIS->setMask(TMascara::maskRais);        
        

        // add the fields
        $row = $this->form->addFields( [ new TLabel('Codigo'), $CODIGO ], [ new TLabel('Nome'), $NOME ] );
        $row->layout = ['col-md-2', 'col-md-10'];
        $row = $this->form->addFields( [ new TLabel('Uf'), $UF ], [ new TLabel('Cód. Rais'), $RAIS ], [ new TLabel('Cep'), $CEP ] );
        $row->layout = ['col-md-6', 'col-md-3', 'col-md-3'];
        
        // set sizes
        $CODIGO->setSize('100%');
        $NOME->setSize('100%');
        $RAIS->setSize('100%');
        $UF->setSize('100%');
        $CEP->setSize('100%');

        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addActionLink(_t('New'), new TAction(['MunicipiosForm', 'onEdit']), 'fa:plus green');
        $this->form->addActionLink(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
                
        $this->datagrid->addQuickColumn('Codigo', 'CODIGO', 'right', 200, new TAction([$this, 'onReload'], ['order'=>'CODIGO']));
        $this->datagrid->addQuickColumn('Nome', 'NOME', 'left', 200, new TAction([$this, 'onReload'], ['order'=>'NOME']));
        $this->datagrid->addQuickColumn('Cód. Rais', 'RAIS', 'right', 200, new TAction([$this, 'onReload'], ['order'=>'RAIS']));
        $this->datagrid->addQuickColumn('Uf', '{estado->SIGLA}', 'right', 200, new TAction([$this, 'onReload'], ['order'=>'estado->SIGLA']));
        $this->datagrid->addQuickColumn('Cep', 'CEP', 'left', 200, new TAction([$this, 'onReload'], ['order'=>'CEP']));
        $this->datagrid->addQuickColumn('Cód. Federal', 'FEDERAL', 'right', 200, new TAction([$this, 'onReload'], ['order'=>'FEDERAL']));
        $this->datagrid->addQuickColumn('Cód. Estadual', 'ESTADUAL', 'right', 200, new TAction([$this, 'onReload'], ['order'=>'ESTADUAL']));
        
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['MunicipiosForm', 'onEdit']), 'CODIGO', 'far:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction([$this, 'onDelete']), 'CODIGO', 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
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
