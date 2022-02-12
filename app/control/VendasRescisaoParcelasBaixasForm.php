<?php
/**
 * VendasRescisaoParcelasBaixasForm Form
 * @author  <your name here>
 */
class VendasRescisaoParcelasBaixasForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_VendasRescisaoParcelasBaixas');
        $this->form->setFormTitle('Baixa Parcelas Distrato');
        
        $this->empresaid         = TSession::getValue('userunitid');
        $this->vendaid           = TSession::getValue('vendaid');
        $this->parcelarescisaoid = TSession::getValue('parcelarescisaoid');
        
        $filterEmpresa = new TCriteria();
        $filterEmpresa->add(new TFilter('EMPRESA', '=', $this->empresaid));        

        // create the form fields
        $ID = new TEntry('ID');
        $RECEBIMENTO = new TDate('RECEBIMENTO');
        $VALOR = new TEntry('VALOR');
        $JUROS = new TEntry('JUROS');
        $MULTA = new TEntry('MULTA');
        $DESCONTO = new TEntry('DESCONTO');
        $CONTACTBPAGTO = new TDBSeekButton('CONTACTBPAGTO', 'gexpertlotes', $this->form->getName(), 'SelPlano', 'DISPLAY', 'CONTACTBPAGTO', null, $filterEmpresa);
        $aux1 = new TEntry('aux1');
        $aux1->setEditable(FALSE);
        $CONTACTBPAGTO->setAuxiliar($aux1);
        
        $RECEBIMENTO->setMask(TMascara::maskDate);
        $RECEBIMENTO->setDatabaseMask(TMascara::maskDBDate);
        $VALOR->setNumericMask(2,',','.', TRUE);
        $JUROS->setNumericMask(2,',','.', TRUE);
        $MULTA->setNumericMask(2,',','.', TRUE);
        $DESCONTO->setNumericMask(2,',','.', TRUE);

        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $ID ] );
        $this->form->addFields( [ new TLabel('Recebimento') ], [ $RECEBIMENTO ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $VALOR ] );
        $this->form->addFields( [ new TLabel('Juros') ], [ $JUROS ] );
        $this->form->addFields( [ new TLabel('Multa') ], [ $MULTA ] );
        $this->form->addFields( [ new TLabel('Desconto') ], [ $DESCONTO ] );
        $this->form->addFields( [ new TLabel('Contactbpagto') ], [ $CONTACTBPAGTO ] );

        // set sizes
        $ID->setSize(TWgtSizes::wsInt);
        $RECEBIMENTO->setSize(TWgtSizes::wsDate);
        $VALOR->setSize(TWgtSizes::wsDouble);
        $JUROS->setSize(TWgtSizes::wsDouble);
        $MULTA->setSize(TWgtSizes::wsDouble);
        $DESCONTO->setSize(TWgtSizes::wsDouble);
        $CONTACTBPAGTO->setSize(TWgtSizes::wsInt);
        $aux1->setSize(TWgtSizes::wsAux);

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
        $this->form->addButton(_t('Back'),  'history.back()', 'far:arrow-alt-circle-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            if(empty($data->EMPRESA))
            {
                $data->EMPRESA = $this->empresaid;       
            }
            
            if(empty($data->VENDA))
            {
                $data->VENDA = $this->vendaid;        
            }
            
            if(empty($data->PARCELA))
            {
                $data->PARCELA = $this->parcelarescisaoid;          
            }
            
            $object = new VendasRescisaoParcelasBaixas;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated ID
            $data->ID = $object->ID;
            
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
                $object = new VendasRescisaoParcelasBaixas($key); // instantiates the Active Record
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
