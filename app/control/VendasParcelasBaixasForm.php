<?php
/**
 * VendasParcelasBaixasForm Form
 * @author  Rodrigo Soares
 */
class VendasParcelasBaixasForm extends TTr2Page
{
    protected $form; // form
    protected $empresaid;
    protected $vendaid;
    protected $parcelaid;
    protected $criteria;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('frm'.__CLASS__);
        $this->form->setFormTitle('Baixa de Parcelas');
        
        $this->empresaid     = TSession::getValue('userunitid');
        $this->vendaid       = TSession::getValue('vendaid');
        $this->parcelaid     = TSession::getValue('parcelaid');
        
        $this->criteria = new TCriteria();
        $this->criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
        
        // create the form fields
        $ID = new TEntry('ID');
        $PARCELA = new TEntry('PARCELA');
        $RECEBIMENTO = new TDate('RECEBIMENTO');
        $VALOR = new TEntry('VALOR');
        $JUROS = new TEntry('JUROS');
        $MULTA = new TEntry('MULTA');
        $DESCONTO = new TEntry('DESCONTO');
        $ATUALIZACAO = new TEntry('ATUALIZACAO');
        $TOTAL = new TEntry('TOTAL');
       // $CONTACTBPAGTO = new TDBSeekButton('CONTACTBPAGTO', 'gexpertlotes', 'frm'.__CLASS__ ,'SelPlano', 'DISPLAY', 'CONTACTBPAGTO', null , $this->criteria) ;
        $aux1 = new TEntry('aux1');
        $aux1->setEditable(FALSE);
        $CONTACTBPAGTO = new TTr2DBSeekButton('CONTACTBPAGTO', 'gexpertlotes', 'frm'.__CLASS__ ,'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTBPAGTO', NULL, $this->criteria);
        $CONTACTBPAGTO->setDisplayMask('{CLASSIFICACAO} {DESCRICAO}');
        $CONTACTBPAGTO->setModelKey('CODIGO');
        
        $CONTACTBPAGTO->setAuxiliar($aux1);
        
        $VALOR->setExitAction(new TAction([$this, 'setTotal'], ['static'=>1]));
        $JUROS->setExitAction(new TAction([$this, 'setTotal'], ['static'=>1]));
        $MULTA->setExitAction(new TAction([$this, 'setTotal'], ['static'=>1]));
        $DESCONTO->setExitAction(new TAction([$this, 'setTotal'], ['static'=>1]));
        $ATUALIZACAO->setExitAction(new TAction([$this, 'setTotal'], ['static'=>1]));
                   

        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $ID ] );
        $this->form->addFields( [ new TLabel('Parcela') ], [ $PARCELA ] );
        $this->form->addFields( [ new TLabel('Recebimento') ], [ $RECEBIMENTO ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $VALOR ] );
        $this->form->addFields( [ new TLabel('Juros') ], [ $JUROS ] );
        $this->form->addFields( [ new TLabel('Multa') ], [ $MULTA ] );
        $this->form->addFields( [ new TLabel('Desconto') ], [ $DESCONTO ] );
        $this->form->addFields( [ new TLabel('Atualizacao') ], [ $ATUALIZACAO ] );
        $this->form->addFields( [ new TLabel('Total') ], [ $TOTAL ] );
        $this->form->addFields( [ new TLabel('Cta Ctb Recebto (Ativo)') ], [ $CONTACTBPAGTO ] );

        // set sizes
        $ID->setSize(TWgtSizes::wsInt);
        $PARCELA->setSize(TWgtSizes::wsInt);
        $RECEBIMENTO->setSize(TWgtSizes::wsDate);
        $VALOR->setSize(TWgtSizes::wsDouble);
        $JUROS->setSize(TWgtSizes::wsDouble);
        $MULTA->setSize(TWgtSizes::wsDouble);
        $DESCONTO->setSize(TWgtSizes::wsDouble);
        $ATUALIZACAO->setSize(TWgtSizes::wsDouble);
        $TOTAL->setSize(TWgtSizes::wsDouble);
        $CONTACTBPAGTO->setSize(TWgtSizes::wsInt);
        $aux1->setSize(TWgtSizes::wsAux);
        
        $PARCELA->setEditable(FALSE);
        $RECEBIMENTO->setMask(TMascara::maskDate);
        $RECEBIMENTO->setDatabaseMask(TMascara::maskDBDate);
        $VALOR->setNumericMask(2,',','.',TRUE);
        $JUROS->setNumericMask(2,',','.',TRUE);
        $MULTA->setNumericMask(2,',','.',TRUE);
        $DESCONTO->setNumericMask(2,',','.',TRUE);
        $ATUALIZACAO->setNumericMask(2,',','.',TRUE);
        $TOTAL->setNumericMask(2,',','.',TRUE);
        $TOTAL->setEditable(FALSE);



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
        //$this->form->addAction(_t('Back'),  new TAction(['VendasParcelasForm', 'onEdit'], ['key'=>TSession::getValue('parcelaid')]), 'far:arrow-alt-circle-left blue');
        
        
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
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
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
                $data->PARCELA = $this->parcelaid;
            }
            
            $object = new VendasParcelasBaixas;  // create an empty object
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
                
                $object = new VendasParcelasBaixas($key);
                
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
    
    public function setTotal($param)
        {
            $obj = new StdClass();
            
            $valorparcela =  TSession::getValue('parcelavalor'); 
            $valor        =  TConversion::asDouble($param['VALOR']);
            $juros        =  TConversion::asDouble($param['JUROS']);
            $multa        =  TConversion::asDouble($param['MULTA']);
            $atualizacao  =  TConversion::asDouble($param['ATUALIZACAO']);  
            $desconto     =  TConversion::asDouble($param['DESCONTO']);
            
            if($valor>$valorparcela)
            {
                $atualizacao      = $valor-$valorparcela;
                $obj->ATUALIZACAO = TConversion::asDoubleBR($atualizacao,2); 
                $valor            = $valorparcela;
                $obj->VALOR       = TConversion::asDoubleBR($valor,2);    
            }   
              
            $total        =  $valor+$juros+$multa+$atualizacao-$desconto;
            
          
            $obj->TOTAL = TConversion::asDoubleBR($total,2);
            
           TForm::sendData('frm'.__CLASS__, $obj, FALSE, FALSE);
        }
    
}
