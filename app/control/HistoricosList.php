<?php
/**
 * HistoricosList Listing
 * @author  <your name here>
 */
class HistoricosList extends TPage
{
    use Adianti\Base\AdiantiStandardListTrait;
    
    public $form; 
    public $datagrid; 
    public $pageNavigation;

    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Historicos');
        $this->form->setFormTitle('Historicos');        
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Historicos');
        $this->setLimit(20);
        $this->setDefaultOrder('CODIGO', 'asc');
        
        $this->addFilterField('CODIGO');
        $this->addFilterField('DESCRICAO', 'like');
        
        $CODIGO = new TEntry('CODIGO');
        $DESCRICAO = new TEntry('DESCRICAO');
        
        $CODIGO->setSize('100%');
        $DESCRICAO->setSize('100%');

        $row = $this->form->addFields( [ new TLabel('Codigo'), $CODIGO ], [ new TLabel('Descricao'), $DESCRICAO ] );
        $row->layout = ['col-md-2', 'col-md-10']; 

        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'), new TAction(['HistoricosForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fa:eraser red');
        $this->form->addAction('Importar um arquivo CSV', new TAction(['ImportarHistoricosCsv', 'onload']), 'fas:cloud-upload-alt blue');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        $this->datagrid->addQuickColumn('Código', 'CODIGO', 'left', 50, new TAction([$this, 'onReload'],['order'=>'CODIGO']));
        $this->datagrid->addQuickColumn('Descrição', 'DESCRICAO', 'left', null, new TAction([$this, 'onReload'],['order'=>'DESCRICAO']));

        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['HistoricosForm', 'onEdit']), 'ID', 'far:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction([$this, 'onDelete']), 'ID', 'far:trash-alt red');
        
        $this->datagrid->createModel();
        
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV']), 'fas:table blue');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF']), 'far:file-pdf red');
        $dropdown->addAction(_t('Save as XML'), new TAction([$this, 'onExportXML']), 'fas:code green');
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        $this->pageNavigation->enableCounters();
        
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
