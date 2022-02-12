<?php
/**
 * VendasRescisaoParcelasForm Master/Detail
 * @author  <your name here>
 */
class VendasRescisaoParcelasForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_VendasRescisaoParcelas');
        $this->form->setFormTitle('Parcela Distrato');
        
        $this->empresaid         = TSession::getValue('userunitid');
        $this->vendaid           = TSession::getValue('vendaid');
        $this->parcelarescisaoid = TSession::getValue('parcelarescisaoid');
        
        $sum = function($values)
        {
            return array_sum($values);
        };
        
        $count = function($values)
        {
            return count($values);
        };
        
        $asCur = function($value)
        {
            return 'R$ '.TConversion::asDoubleBR($value, 2);
        };
        
        $asDate = function($value)
        {
            return TConversion::asDate($value);
        };
        
        // master fields
        $ID = new TEntry('ID');
        $VENCIMENTO = new TDate('VENCIMENTO');
        $VALOR = new TEntry('VALOR');


        if (!empty($ID))
        {
            $ID->setEditable(FALSE);
        }
        
        // master fields
        $this->form->addFields( [new TLabel('Id')], [$ID] );
        $this->form->addFields( [new TLabel('Vencimento')], [$VENCIMENTO] );
        $this->form->addFields( [new TLabel('Valor')], [$VALOR] );
        
        $ID->setSize(TWgtSizes::wsInt);
        $VENCIMENTO->setSize(TWgtSizes::wsDate);
        $VALOR->setSize(TWgtSizes::wsDouble);
        
        $VENCIMENTO->setMask(TMascara::maskDate);
        $VENCIMENTO->setDatabaseMask(TMascara::maskDBDate);
        $VALOR->setNumericMask(2,',','.',TRUE);
        
        // detail fields
        $this->form->addContent( ['<br><h5>Detalhamento das Baixas</h5><hr>'] );
        
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid);        
        //$this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->detail_list->setId('VendasRescisaoParcelas_list');
        $this->detail_list->makeScrollable();
        $this->detail_list->setHeight(100);
        
         $bxID      = $this->detail_list->addQuickColumn('Id', 'ID', 'left', 50);
         $bxRecbto  = $this->detail_list->addQuickColumn('Recbto', 'RECEBIMENTO', 'left', 200);
         $bxVlr     = $this->detail_list->addQuickColumn('Valor', 'VALOR', 'left', 200);
         $bxJur     =  $this->detail_list->addQuickColumn('Juros', 'JUROS', 'left', 200);
         $bxMul     = $this->detail_list->addQuickColumn('Multa', 'MULTA', 'left', 200);
         $bxDes     = $this->detail_list->addQuickColumn('Desconto', 'DESCONTO', 'left', 200);
         $bxCtaCtb  = $this->detail_list->addQuickColumn('Conta Ctb Pagto', 'CONTACTBPAGTO', 'left', 200);
        
         $bxID->setTotalFunction($count);
         $bxRecbto->setTransformer($asDate);
         
         $bxVlr->setTransformer($asCur);
         $bxJur->setTransformer($asCur);
         $bxMul->setTransformer($asCur);
         $bxDes->setTransformer($asCur);
         
         $bxVlr->setTotalFunction($sum); 
         $bxJur->setTotalFunction($sum);
         $bxMul->setTotalFunction($sum);
         $bxDes->setTotalFunction($sum);
          
        // items

        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction(['VendasRescisaoParcelasBaixasForm',  'onEdit']), 'ID', 'fa:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'ID', 'fas:trash-alt red');
        $this->detail_list->createModel();
        
        $btnBaixar = new TActionLink('Add Nova Baixa', new TAction(['VendasRescisaoParcelasBaixasForm', 'onEdit']), null,null,null, 'fa:plus blue');
        $btnBaixar->class='btn btn-default btn-sm';
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $panel->addFooter('');
        $panel->getFooter()->add($btnBaixar);
        $this->form->addContent( [$panel] );

        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction( _t('Back'), new TAction(['VendasForm', 'onEdit'], ['status'=>'browse', 'reset'=>1]),'far:arrow-alt-circle-left blue');
        
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Vendas', 'Parcelas Distrato']));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    public function onClear($param)
    {
        $this->onReload( $param );
    }
  
    public function onDeleteDetail( $param )
    {
        if(isset($param['key']))
        {
                TTransaction::open('gexpertlotes');
                
                $key = $param['key'];
                
                $obj = new VendasRescisaoParcelasBaixas($key);
                $obj->delete();        
                    
                TTransaction::close();
                
                self::onEdit(['key'=>$this->parcelarescisaoid]);
         }
        
    }
    
  
    public function onReload($param)
    {
        $this->loaded = TRUE;
    }
    
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('gexpertlotes');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
               
                TSession::setValue('parcelarescisaoid', $key);   
                
                $object = new VendasRescisaoParcelas($key);
                
                $items  = VendasRescisaoParcelasBaixas::where('PARCELA', '=', $key)->orderBy('RECEBIMENTO', 'asc')->load();
                
                $this->detail_list->addItems($items);
                
                $this->form->setData($object); // fill the form with the active record data
                $this->onReload( $param ); // reload items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear(TRUE);                
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open('gexpertlotes');
            
            $data = $this->form->getData();
            
            if (empty($data->EMPRESA))
            {
                $data->EMPRESA = $this->empresaid;
            }
            
            if (empty($data->VENDA))
            {
                $data->VENDA = $this->vendaid;
            }
            
            $master = new VendasRescisaoParcelas;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            
            TTransaction::close(); // close the transaction
            
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
