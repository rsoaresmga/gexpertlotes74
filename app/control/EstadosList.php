<?php
/**
 * EstadosList Listing
 * @author  <your name here>
 */
class EstadosList extends TPage
{
    use Adianti\Base\AdiantiStandardListTrait;
    use Tr2FormUtilsTrait;
       
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Estados');
        $this->form->setFormTitle('Estados');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Estados');
        $this->setLimit(20);
        $this->setDefaultOrder('CODIGO');
        
        $this->addFilterField('SIGLA');
        $this->addFilterField('NOME');
                
        $NOME = new TEntry('NOME');
        $SIGLA = new TEntry('SIGLA');

        $row = $this->form->addFields( [ new TLabel('Sigla'), $SIGLA ], [ new TLabel('Nome'), $NOME ]  );
        $row->layout = ['col-md-2', 'col-md-10'];
        
        $NOME->setSize('100%');
        $SIGLA->setSize('100%');

        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addActionLink(_t('New'), new TAction(['EstadosForm', 'onEdit']), 'fa:plus green');
        $this->form->addActionLink(_t('Clear Filters'), new TAction([$this, 'clearFilters']), 'fas:eraser red');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
                
        $this->datagrid->addQuickColumn('Código', 'CODIGO', 'right', 30, new TAction([$this, 'onReload'],['order'=>'CODIGO']));
        $this->datagrid->addQuickColumn('Sigla', 'SIGLA', 'center', 60, new TAction([$this, 'onReload'],['order'=>'SIGLA']));
        $this->datagrid->addQuickColumn('Nome', 'NOME', 'left', null, new TAction([$this, 'onReload'],['order'=>'NOME']));
        $ali = $this->datagrid->addQuickColumn('% ICMS Int', 'ALIQICMSINTERNA', 'right', 60, new TAction([$this, 'onReload'],['order'=>'ALIQICMSINTERNA']));
        $ale = $this->datagrid->addQuickColumn('% ICMS Ext', 'ALIQICMSEXTERNA', 'right', 60, new TAction([$this, 'onReload'],['order'=>'ALIQICMSEXTERNA']));
        
        $ali->setTransformer([$this, 'asDoubleBR']);
        $ale->setTransformer([$this, 'asDoubleBR']);
        
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(['EstadosForm', 'onEdit']), 'CODIGO', 'far:edit blue');
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
}
