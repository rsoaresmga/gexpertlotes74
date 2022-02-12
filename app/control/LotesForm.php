<?php
/**
 * LotesForm Form
 * @author  <your name here>
 */
class LotesForm extends TTr2Page
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
        $this->form = new BootstrapFormBuilder('form_Lotes');
        $this->form->setFormTitle('Lotes');
        

        // create the form fields
        $ID = new TEntry('ID');
        $EMPRESA = new TEntry('EMPRESA');
        $EMPRESA->setEditable(FALSE);
        $EMPREENDIMENTO = new TEntry('EMPREENDIMENTO');
        $EMPREENDIMENTO->setEditable(FALSE);
        $QUADRA = new TEntry('QUADRA');
        $QUADRA->setMask(TMascara::maskInt);
        $CODIGO = new TEntry('CODIGO');
        $QUADRA->setMask(TMascara::maskInt);
        $DESMEMBRAMENTO = new TDate('DESMEMBRAMENTO');
        $DESMEMBRAMENTO->setMask(TMascara::maskDate);
            $DESMEMBRAMENTO->setDatabaseMask(TMascara::maskDBDate);
        $AREA = new TEntry('AREA');
        $AREA->setNumericMask(5, ',', '.', TRUE);
        $VLRCUSTO = new TEntry('VLRCUSTO');
        $VLRCUSTO->setNumericMask(5, ',', '.', TRUE);
        $SITUACAO = new TCombo('SITUACAO');
        $SITUACAO->addItems(['0'=>'Aberto','1'=>'Vendido','2'=>'Devolvido','3'=>'Renegociado', '4'=>'Revendido']);
        $ATIVO = new TCombo('ATIVO');
        $ATIVO->addItems(['S'=>'Sim', 'N'=>'Não']);
        $OBS = new TText('OBS');
        
        $filterPlano = new TCriteria();
        $filterPlano->add(new TFilter('EMPRESA', '=', TSession::getValue('userunitid')));
        
        $CONTACTBESTOQUE        = new TTr2DBSeekButton('CONTACTBESTOQUE', 'gexpertlotes', 'form_Lotes', 'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTBESTOQUE', NULL , $filterPlano);
        $CONTACTBESTOQUE->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBESTOQUE->setModelKey('CODIGO'); 
        $CONTACTBRECEITADIFER   = new TTr2DBSeekButton('CONTACTBRECEITADIFER', 'gexpertlotes', 'form_Lotes', 'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTBRECEITADIFER', NULL , $filterPlano);
        $CONTACTBRECEITADIFER->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBRECEITADIFER->setModelKey('CODIGO');
        $CONTACTBRECEITADIFERLP = new TTr2DBSeekButton('CONTACTBRECEITADIFERLP', 'gexpertlotes', 'form_Lotes', 'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTBRECEITADIFERLP', NULL , $filterPlano);
        $CONTACTBRECEITADIFERLP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBRECEITADIFERLP->setModelKey('CODIGO');
        $CONTACTBDESPESADIFER   = new TTr2DBSeekButton('CONTACTBDESPESADIFER', 'gexpertlotes', 'form_Lotes', 'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTBDESPESADIFER', NULL , $filterPlano);
        $CONTACTBDESPESADIFER->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBDESPESADIFER->setModelKey('CODIGO');
        $CONTACTBDESPESADIFERLP = new TTr2DBSeekButton('CONTACTBDESPESADIFERLP', 'gexpertlotes', 'form_Lotes', 'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTBDESPESADIFERLP', NULL , $filterPlano);
        $CONTACTBDESPESADIFERLP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBDESPESADIFERLP->setModelKey('CODIGO');
        
        $aux1 = new TEntry('aux1');
        $aux2 = new TEntry('aux2');
        $aux3 = new TEntry('aux3');
        $aux4 = new TEntry('aux4');
        $aux5 = new TEntry('aux5');
        
        $aux1->setEditable(FALSE);
        $aux2->setEditable(FALSE);
        $aux3->setEditable(FALSE);
        $aux4->setEditable(FALSE);
        $aux5->setEditable(FALSE);
        
        $CONTACTBESTOQUE->setAuxiliar($aux1);
        $CONTACTBRECEITADIFER->setAuxiliar($aux2);
        $CONTACTBRECEITADIFERLP->setAuxiliar($aux3);
        $CONTACTBDESPESADIFER->setAuxiliar($aux4);
        $CONTACTBDESPESADIFERLP->setAuxiliar($aux5);
        
        
            
        // add the fields
        $this->form->appendPage('Geral');
        $this->form->addFields( [ new TLabel('Id') ], [ $ID ] );
        $this->form->addFields( [ new TLabel('Empresa') ], [ $EMPRESA ] );
        $this->form->addFields( [ new TLabel('Empreendimento') ], [ $EMPREENDIMENTO ] );
        $this->form->addFields( [ new TLabel('Quadra/Andar') ], [ $QUADRA ] );
        $this->form->addFields( [ new TLabel('Codigo/Apto') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Desmembramento') ], [ $DESMEMBRAMENTO ] );
        $this->form->addFields( [ new TLabel('Area') ], [ $AREA ] );
        $this->form->addFields( [ new TLabel('Vlr custo') ], [ $VLRCUSTO ] );
        $this->form->addFields( [ new TLabel('Situacao') ], [ $SITUACAO ] );
        $this->form->addFields( [ new TLabel('Ativo') ], [ $ATIVO ] );
        $this->form->addFields( [ new TLabel('Obs') ], [ $OBS ] );
        
        $this->form->appendPage('Contábil');
        $this->form->addFields( [ new TLabel('Conta ctb estoque') ], [ $CONTACTBESTOQUE ] );        
        $this->form->addFields( [ new TLabel('Conta ctb receita difer (cp)') ], [ $CONTACTBRECEITADIFER ] );
        $this->form->addFields( [ new TLabel('Conta ctb despesa difer (cp)') ], [ $CONTACTBDESPESADIFER ] );        
        $this->form->addFields( [ new TLabel('Conta ctb receita difer (lp)') ], [ $CONTACTBRECEITADIFERLP ] );
        $this->form->addFields( [ new TLabel('Conta ctb despesa difer (lp)') ], [ $CONTACTBDESPESADIFERLP ] );
        
        $QUADRA->addValidation('Quadra', new TRequiredValidator);
        $CODIGO->addValidation('Codigo', new TRequiredValidator);
        $AREA->addValidation('Area', new TRequiredValidator);
        $VLRCUSTO->addValidation('Vlrcusto', new TRequiredValidator);


        // set sizes
        $ID->setSize(TWgtSizes::wsInt);
        $EMPRESA->setSize(TWgtSizes::wsInt);
        $EMPREENDIMENTO->setSize(TWgtSizes::wsInt);
        $QUADRA->setSize(TWgtSizes::wsInt);
        $CODIGO->setSize(TWgtSizes::wsInt);
        $DESMEMBRAMENTO->setSize(TWgtSizes::wsDate);
        $AREA->setSize(TWgtSizes::wsDouble);
        $VLRCUSTO->setSize(TWgtSizes::wsDouble);
        $SITUACAO->setSize(TWgtSizes::ws10);
        $ATIVO->setSize(TWgtSizes::wsBol);
        $OBS->setSize(TWgtSizes::wsBlob);
        
        $CONTACTBESTOQUE->setSize(TWgtSizes::wsInt);
        $CONTACTBRECEITADIFER->setSize(TWgtSizes::wsInt);
        $CONTACTBRECEITADIFERLP->setSize(TWgtSizes::wsInt);
        $CONTACTBDESPESADIFER->setSize(TWgtSizes::wsInt);
        $CONTACTBDESPESADIFERLP->setSize(TWgtSizes::wsInt);
        
        $aux1->setSize(TWgtSizes::wsAux);
        $aux2->setSize(TWgtSizes::wsAux);
        $aux3->setSize(TWgtSizes::wsAux);
        $aux4->setSize(TWgtSizes::wsAux);
        $aux5->setSize(TWgtSizes::wsAux);    

        if (!empty($ID))
        {
            $ID->setEditable(FALSE);
        }
        
        $AREA->setExitAction(new TAction([$this, 'calcCusto'],['static'=>'1']));
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $back = new TAction(['EmpreendimentosForm', 'onEdit'], ['status'=>'browse']);
        $this->form->addAction(_t('Back'), $back, 'far:arrow-alt-circle-left blue');
        //$this->form->addButton(_t('Back'),  "history.back()", 'far:arrow-alt-circle-left blue'); 
        
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
                $data->EMPRESA = TSession::getValue('userunitid');
            }
            
            if(empty($data->EMPREENDIMENTO))
            {
                $data->EMPREENDIMENTO = TSession::getValue('Empreendimentos_id');
            }
            
            $object = new Lotes;  // create an empty object
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
                $object = new Lotes($key); // instantiates the Active Record
                
                $object->DESMEMBRAMENTO = TDate::date2br($object->DESMEMBRAMENTO);
                
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
    
    public function calcCusto($param)
    {
        TTransaction::open('gexpertlotes');
        
        $data = $this->form->getData();
        
        $empreendimento = new Empreendimentos(TSession::getValue('Empreendimentos_id'));

        $vlraqs  = TConversion::asDouble($empreendimento->VLRAQUISICAO, 2);
        $areattl = TConversion::asDouble($empreendimento->AREATOTAL, 2);
        $area    = TConversion::asDouble($param['AREA'], 5);

        $obj = new StdClass();
        $obj->VLRCUSTO = TConversion::asDoubleBR($vlraqs/$areattl*$area,5);
        
        TForm::sendData('form_Lotes', $obj);
        
        TTransaction::close();
        
    }
}
