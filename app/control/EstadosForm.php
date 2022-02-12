<?php
/**
 * EstadosForm Form
 * @author  <your name here>
 */
class EstadosForm extends TPage
{
    use Adianti\Base\AdiantiStandardFormTrait;
    use Tr2FormUtilsTrait;
    
    protected $form; // form
        
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Estados');
        $this->form->setFormTitle('Estados');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Estados');
        
        $CODIGO          = new TEntry('CODIGO');
        $SIGLA           = new TEntry('SIGLA', 'gexpertlotes', 'Estados', 'CODIGO', 'SIGLA');
        $NOME            = new TEntry('NOME');
        $ALIQICMSINTERNA = new TEntry('ALIQICMSINTERNA');
        $ALIQICMSEXTERNA = new TEntry('ALIQICMSEXTERNA');

        // add the fields
        $this->form->addFields( [ new TLabel('Codigo') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Sigla') ], [ $SIGLA ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $NOME ] );
        $this->form->addFields( [ new TLabel('% ICMS Interno') ], [ $ALIQICMSINTERNA ] );
        $this->form->addFields( [ new TLabel('% ICMS Externo') ], [ $ALIQICMSEXTERNA ] );

        $SIGLA->addValidation('Sigla', new TRequiredValidator);
        $NOME->addValidation('Nome', new TRequiredValidator);

        // set sizes
        $CODIGO->setSize(TWgtSizes::wsInt);
        $SIGLA->setSize(TWgtSizes::wsInt);
        $NOME->setSize(TWgtSizes::ws60);
        $ALIQICMSINTERNA->setSize(TWgtSizes::wsDouble);
        $ALIQICMSEXTERNA->setSize(TWgtSizes::wsDouble);
        
        $ALIQICMSEXTERNA->setNumericMask(5, ',', '.', TRUE);
        $ALIQICMSINTERNA->setNumericMask(5, ',', '.', TRUE);

        if (!empty($CODIGO))
        {
            $CODIGO->setEditable(FALSE);
        }
        
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'),  new TAction(['EstadosList', 'onReload']), 'far:arrow-alt-circle-left blue'); 
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Estados', 'Cadastro'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }

}
