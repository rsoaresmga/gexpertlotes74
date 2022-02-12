<?php
/**
 * PlanoForm Form
 * @author  <your name here>
 */
class PlanoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Plano');
        $this->form->setFormTitle('Plano');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Plano');

        // create the form fields
        $ID     = new TEntry('ID');
        $ID->setEditable(FALSE);
        $CODIGO = new TEntry('CODIGO');
        $CODIGO->setMask(TMascara::maskInt);
        $EMPRESA = new TEntry('EMPRESA');
        $EMPRESA->setEditable(FALSE);
        $GRUPO = new TEntry('GRUPO');
        $GRUPO->setMask(TMascara::maskInt);
        $CLASSIFICACAO = new TEntry('CLASSIFICACAO');
        $NATUREZA = new TCombo('NATUREZA');
        $NATUREZA->addItems(['1'=>'Débito', '-1'=>'Crédito']);
        $DESCRICAO = new TEntry('DESCRICAO');
        $ID = new TEntry('ID');


        // add the fields
        $this->form->addFields( [ new TLabel('Empresa') ], [ $EMPRESA ] );
        $this->form->addFields( [ new TLabel('Id') ], [ $ID ] );
        $this->form->addFields( [ new TLabel('Codigo') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Grupo') ], [ $GRUPO ] );
        $this->form->addFields( [ new TLabel('Classificacao') ], [ $CLASSIFICACAO ] );
        $this->form->addFields( [ new TLabel('Natureza') ], [ $NATUREZA ] );
        $this->form->addFields( [ new TLabel('Descricao') ], [ $DESCRICAO ] );
        //$this->form->addFields( [ new TLabel('Id') ], [ $ID ] );

        $GRUPO->addValidation('Grupo', new TRequiredValidator);
        $CLASSIFICACAO->addValidation('Classificacao', new TRequiredValidator);
        $NATUREZA->addValidation('Natureza', new TRequiredValidator);
        $DESCRICAO->addValidation('Descricao', new TRequiredValidator);


        // set sizes
        $CODIGO->setSize(TWgtSizes::wsInt);
        $EMPRESA->setSize(TWgtSizes::wsInt);
        $GRUPO->setSize(TWgtSizes::wsInt);
        $CLASSIFICACAO->setSize(TWgtSizes::ws20);
        $NATUREZA->setSize(TWgtSizes::ws10);
        $DESCRICAO->setSize(TWgtSizes::ws60);
        $ID->setSize(TWgtSizes::wsInt);

        if (!empty($ID))
        {
            $ID->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['PlanoList', 'onReload']), 'far:arrow-alt-circle-left blue'); 
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Plano de Contas', 'Cadastro'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }
}
