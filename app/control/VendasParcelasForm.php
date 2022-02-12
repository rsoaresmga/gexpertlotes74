<?php
/**
 * VendasParcelasForm Master/Detail
 * @author  <your name here>
 */
class VendasParcelasForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    protected $empresaid;
    protected $vendaid;
   
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_VendasParcelas');
        $this->form->setFormTitle('Lançamento de Parcelas');
        
        $this->empresaid = TSession::getValue('userunitid');
        $this->vendaid   = TSession::getValue('vendaid');
        
        $asCur = function($value)
                 {
                     if (is_numeric($value))
                     {
                         return 'R$ '.number_format($value,2,',', '.');
                     }
                 };
        $asDate = function($value)
                  {
                      return TDate::date2br($value);
                  };            
        $asBol = function($value)
                 {
                     if($value=='S')
                     {
                         return 'Sim';
                     } 
                     return 'Não';
                 }; 
                 
        $sum = function($values){return array_sum($values);};  
        
        $count = function($values){return count($values);}; 
        
        // master fields
        $ID = new TEntry('ID');
        $PARCELA = new TEntry('PARCELA');
        $VENCIMENTO = new TDate('VENCIMENTO');
        $VALOR = new TEntry('VALOR');
        $SALDO = new TEntry('SALDO');
        $QUITADO = new TCombo('QUITADO');
        $OBSERVACAO = new TText('OBSERVACAO');
        
        $ID->setSize(TWgtSizes::wsInt);
        $PARCELA->setSize(TWgtSizes::wsInt);
        $VENCIMENTO->setSize(TWgtSizes::wsDate);
        $VALOR->setSize(TWgtSizes::wsDouble);
        $SALDO->setSize(TWgtSizes::wsDouble);
        $QUITADO->setSize(TWgtSizes::wsBol);
        $OBSERVACAO->setSize(TWgtSizes::wsBlob);
        
        $PARCELA->setMask(TMascara::maskInt);
        $VENCIMENTO->setMask(TMascara::maskDate);
        $VENCIMENTO->setDatabaseMask(TMascara::maskDBDate);
        $VALOR->setNumericMask(2,',', '.', TRUE);
        $SALDO->setNumericMask(2,',','.',TRUE);
        $SALDO->setEditable(FALSE);
        $QUITADO->addItems(['S'=>'Sim', 'N'=>'Não']);
        
       
        // detail fields
        $detail_ID = new THidden('detail_ID');
        
        if (!empty($ID))
        {
            $ID->setEditable(FALSE);
        }
        
        // master fields
        $this->form->addFields( [new TLabel('Id')], [$ID] );
        $this->form->addFields( [new TLabel('Nr. Parcela')], [$PARCELA] );
        $this->form->addFields( [new TLabel('Vencimento')], [$VENCIMENTO] );
        $this->form->addFields( [new TLabel('Valor')], [$VALOR] );
        $this->form->addFields( [new TLabel('Saldo')], [$SALDO] );
        $this->form->addFields( [new TLabel('Quitado')], [$QUITADO] );
        $this->form->addFields( [new TLabel('Observacao')], [$OBSERVACAO] );
       
        
        
        // detail fields
        $this->form->addContent( ['<h5>Detalhamento de Baixas</h5><hr>'] );
        $this->form->addFields( [$detail_ID] );
       
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGridScroll);
        $this->detail_list->setId('VendasParcelas_list');
        
        // items
        $ID            = $this->detail_list->addQuickColumn('Id', 'ID', 'left', 50);
        $RECEBIMENTO   = $this->detail_list->addQuickColumn('Recebimento', 'RECEBIMENTO', 'left', 200);
        $VALOR         = $this->detail_list->addQuickColumn('Valor', 'VALOR', 'left', 200);
        $JUROS         = $this->detail_list->addQuickColumn('Juros', 'JUROS', 'left', 200);
        $MULTA         = $this->detail_list->addQuickColumn('Multa', 'MULTA', 'left', 200);
        $ATUALIZACAO   = $this->detail_list->addQuickColumn('Atualizacao', 'ATUALIZACAO', 'left', 200);
        $DESCONTO      = $this->detail_list->addQuickColumn('Desconto', 'DESCONTO', 'left', 200);
        $TOTAL         = $this->detail_list->addQuickColumn('Total', 'TOTAL', 'left', 200);
                         $this->detail_list->addQuickColumn('Conta Ctb Recebto', 'CONTACTBPAGTO', 'left', 180);
        
        $VALOR->setTransformer($asCur);
        $JUROS->setTransformer($asCur);
        $MULTA->setTransformer($asCur);
        $ATUALIZACAO->setTransformer($asCur);
        $DESCONTO->setTransformer($asCur);
        $TOTAL->setTransformer($asCur);
        $RECEBIMENTO->setTransformer($asDate);
        
        $VALOR->setTotalFunction($sum);
        $VALOR->setTotalFunction($sum);
        $JUROS->setTotalFunction($sum);
        $MULTA->setTotalFunction($sum);
        $ATUALIZACAO->setTotalFunction($sum);
        $DESCONTO->setTotalFunction($sum);
        $TOTAL->setTotalFunction($sum);
        $ID->setTotalFunction($count);
        
        $this->detail_list->setHeight(100);
                    
        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction(['VendasParcelasBaixasForm', 'onEdit']),   'ID', 'far:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'ID', 'far:trash-alt red');
        $this->detail_list->createModel();
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $panel->addFooter('');
        
        $add_baixas = new TActionLink('Add Nova Baixa', new TAction(['VendasParcelasBaixasForm', 'onEdit']), null, null, null, 'fa:plus green');
        $add_baixas->class='btn btn-default btn-sm';
        $panel->getFooter()->add($add_baixas);
        
        $this->form->addContent( [$panel] );

        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-success';
        
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['VendasForm', 'onEdit'],['status'=>'browse']), 'far:arrow-alt-circle-left blue');
         
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Vendas', 'Cadastro', 'Parcelas'], TRUE));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
        $this->onReload( $param );
    }
   
     /**
     * Delete an item from session list
     * @param $param URL parameters
     */
    public function onDeleteDetail( $param )
    {
        // get detail id
        $detail_id = $param['key'];
        
        TTransaction::open('gexpertlotes');
        $obj = new VendasParcelasBaixas($detail_id); 
        $obj->delete();
        TTransaction::close();
        
        self::onEdit(['key'=>TSession::getValue('parcelaid')]);
        
        // delete item from screen
        //TScript::create("ttable_remove_row_by_id('VendasParcelas_list', '{$detail_id}')");
    }
    
    /**
     * Load the items list from session
     * @param $param URL parameters
     */
    public function onReload($param)
    {
        $this->loaded = TRUE;
    }
    
    /**
     * Load Master/Detail data from database to form/session
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('gexpertlotes');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                TSession::setValue('parcelaid', $key);
                $object = new VendasParcelas($key);
                
                // passa o ID da Venda
                TSession::setValue('vendaid', $object->VENDA);
                
                $items  = VendasParcelasBaixas::where('PARCELA', '=', $key)->load();
                
                $this->detail_list->addItems($items);
                
                if($object->VALOR)
                {
                    TSession::setValue('parcelavalor', $object->VALOR);
                } 
                else
                {
                    TSession::clear('parcelavalor');
                }
                
                $this->form->setData($object); // fill the form with the active record data
                //$this->onReload( $param ); // reload items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear(TRUE);
                TSession::setValue(__CLASS__.'_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form/session to database
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open('gexpertlotes');
            
            $data = $this->form->getData();
            
            if(empty($data->EMPRESA))
            {
                $data->EMPRESA = $this->empresaid;
            }
            
            if(empty($data->VENDA))
            {
                $data->VENDA = $this->vendaid;
            }
            
            $master = new VendasParcelas;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            
            $details = VendasParcelasBaixas::where('PARCELA', '=', $master->ID);
            
            //$this->detail_list->addItems($details->load()); 
            
            TTransaction::close(); // close the transaction
            
            // reload form and session items
            $this->onEdit(array('key'=>$master->ID));
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     * Show the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
