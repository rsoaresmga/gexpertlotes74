<?php
/**
 * HistoricosForm Form
 * @author  <your name here>
 */
class HistoricosForm extends TPage
{
    use Adianti\Base\AdiantiStandardFormTrait;
    
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Historicos');
        $this->form->setFormTitle('Historicos');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Historicos');
        
        // create the form fields
        $ID = new TEntry('ID');
        $CODIGO = new TEntry('CODIGO');
        $DESCRICAO = new TEntry('DESCRICAO');

        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $ID ] );
        $this->form->addFields( [ new TLabel('Codigo') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Descricao') ], [ $DESCRICAO ] );

        $CODIGO->addValidation('Codigo', new TRequiredValidator);
        $DESCRICAO->addValidation('Descricao', new TRequiredValidator);

        // set sizes
        $ID->setSize(TWgtSizes::wsInt);
        $CODIGO->setSize(TWgtSizes::wsInt);
        $DESCRICAO->setSize(TWgtSizes::ws60);      
        $ID->setEditable(FALSE);
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'),  new TAction(['HistoricosList', 'onReload']), 'far:arrow-alt-circle-left blue'); 
          
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Históricos', 'Cadastro'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }
}
