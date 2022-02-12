<?php
/**
 * MunicipiosForm Form
 * @author  <your name here>
 */
class MunicipiosForm extends TPage
{
    use Adianti\Base\AdiantiStandardFormTrait;
    use Tr2FormUtilsTrait;
    
    protected $form; // form
    
     public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Municipios');
        $this->form->setFormTitle('Municipios');
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Municipios');    

        // create the form fields
        $CODIGO = new TEntry('CODIGO');
        $UF = new TDBCombo('UF', 'gexpertlotes', 'Estados', 'CODIGO', '{NOME} ({SIGLA})');
        $UF->enableSearch();        
        $NOME = new TEntry('NOME');
        $CEP = new TEntry('CEP');
        $CEP->setMask(TMascara::maskCEP);
        $RAIS = new TEntry('RAIS');
        $RAIS->setMask(TMascara::maskRais);   
        $FEDERAL = new TEntry('FEDERAL');
        $FEDERAL->setMask(TMascara::maskInt);
        $ESTADUAL = new TEntry('ESTADUAL');
        $ESTADUAL->setMask(TMascara::maskInt);


        // add the fields
        $this->form->addFields( [ new TLabel('Codigo') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $NOME ] );
        $this->form->addFields( [ new TLabel('Cód. Rais') ], [ $RAIS ] );
        $this->form->addFields( [ new TLabel('Uf') ], [ $UF ] );
        $this->form->addFields( [ new TLabel('Cep') ], [ $CEP ] );
        $this->form->addFields( [ new TLabel('Cód. Federal') ], [ $FEDERAL ] );
        $this->form->addFields( [ new TLabel('Cód. Estadual') ], [ $ESTADUAL ] );



        // set sizes
        $CODIGO->setSize(TWgtSizes::wsInt);
        $NOME->setSize(TWgtSizes::ws60);
        $RAIS->setSize(TWgtSizes::ws10);
        $UF->setSize(TWgtSizes::ws40);
        $CEP->setSize(TWgtSizes::wsCEP);
        $FEDERAL->setSize(TWgtSizes::wsInt);
        $ESTADUAL->setSize(TWgtSizes::wsInt);
        
        $CODIGO->setEditable(FALSE);
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['MunicipiosList', 'onReload']), 'far:arrow-alt-circle-left blue'); 
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Municípios', 'Cadastro'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('gexpertlotes'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new Municipios;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated CODIGO
            $data->CODIGO = $object->CODIGO;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('gexpertlotes'); // open a transaction
                $object = new Municipios($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
