<?php
/**
 * PlanoList Listing
 * @author  <your name here>
 */
class PlanoList extends TPage
{
    use Adianti\Base\AdiantiStandardListTrait;
   
    private $form; 
    private $datagrid;
    private $pageNavigation;
        
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Plano');
        $this->form->setFormTitle('Plano');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Plano');
        $this->setLimit(20);
        $this->setDefaultOrder('CLASSIFICACAO', 'asc');
        
        $criteria = new TCriteria;
        $criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
        $this->setCriteria($criteria);
        
        $this->addFilterField('CODIGO');
        $this->addFilterField('GRUPO');
        $this->addFilterField('CLASSIFICACAO', 'like');
        $this->addFilterField('DESCRICAO', 'like');

        // create the form fields
        $CODIGO = new TEntry('CODIGO');
        $GRUPO = new TEntry('GRUPO');
        $CLASSIFICACAO = new TEntry('CLASSIFICACAO');
        $DESCRICAO = new TEntry('DESCRICAO');

        // add the fields
        $row = $this->form->addFields( [ new TLabel('Codigo')       , $CODIGO ], 
                                       [ new TLabel('Grupo')        , $GRUPO ], 
                                       [ new TLabel('Classificacao'), $CLASSIFICACAO ] );
        $row->layout = ['col-md-2', 'col-md-2', 'col-md-8'];                                
                                       
        $row = $this->form->addFields( [ new TLabel('Descricao')    , $DESCRICAO ] );
        $row->layout = ['col-md-12'];

        // set sizes
        $CODIGO->setSize('100%');
        $GRUPO->setSize('100%');
        $CLASSIFICACAO->setSize('100%');
        $DESCRICAO->setSize('100%');
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addActionLink(_t('New'), new TAction(['PlanoForm', 'onEdit']), 'fa:plus green');
        $this->form->addActionLink(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
        // add the columns to the DataGrid
        $this->datagrid->addQuickColumn('Codigo','CODIGO', 'right', 80, new TAction([$this, 'onReload'], ['order'=>'CODIGO']));
        $this->datagrid->addQuickColumn('Grupo','GRUPO', 'right', 60, new TAction([$this, 'onReload'], ['order'=>'GRUPO']));
        $this->datagrid->addQuickColumn('Classificação','CLASSIFICACAO', 'left', 100, new TAction([$this, 'onReload'], ['order'=>'CLASSIFICACAO']));
        $col_natureza = $this->datagrid->addQuickColumn('Natureza','NATUREZA', 'left', 60, new TAction([$this, 'onReload'], ['order'=>'NATUREZA']));
        $this->datagrid->addQuickColumn('Descrição','DESCRICAO', 'left', 500, new TAction([$this, 'onReload'], ['order'=>'DESCRICAO']));
        
        $col_natureza->setTransformer(function($value){ if($value==1) { return 'Debito'; } else { return 'Credito'; } });
        
        // create EDIT action
        $this->datagrid->addQuickAction(_t('Edit'),   new TDataGridAction(['PlanoForm', 'onEdit']), 'ID', 'far:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction([$this, 'onDelete'])    , 'ID', 'far:trash-alt red');   
      
        $this->form->addAction('Importar um Arquivo CSV', new TAction(['ImportarPlanoCsv', 'onLoad']), 'fas:cloud-upload-alt blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state'=>'false', 'static'=>'1']), 'far:file-excel green');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state'=>'false', 'static'=>'1']), 'far:file-pdf red');
        $dropdown->addAction(_t('Save as XML'), new TAction([$this, 'onExportXML'], ['register_state'=>'false', 'static'=>'1']), 'far:file-code blue');
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->enableCounters();
        
        $panel = new TPanelGroup;
        $panel->style='width:100%; overflow-x:auto';
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $panel->addHeaderWidget($dropdown); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
        
        $this->form->setData(TSession::getValue($this->activeRecord.'_filter_data') );
    }    
}
