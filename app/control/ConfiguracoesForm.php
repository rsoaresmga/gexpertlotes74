<?php
/**
 * ConfiguracoesForm Registration
 * @author  <your name here>
 */
class ConfiguracoesForm extends TPage
{
    protected $form; // form
    protected $empresaid;
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('gexpertlotes');              // defines the database
        $this->setActiveRecord('Configuracoes');     // defines the active record
        
        $this->empresaid = TSession::getValue('userunitid');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Configuracoes');
        $this->form->setFormTitle('Parâmetros de Configuração');
        
       // create the form fields
        $EMPRESA = new TEntry('EMPRESA');
            $EMPRESA->setEditable(FALSE);
        $REGIMETRIBUTARIO = new TCombo('REGIMETRIBUTARIO');
            $REGIMETRIBUTARIO->addItems(['0'=>'Simples Nacional','1'=>'Lucro Real', '2'=>'Lucro Presumido', '3'=>'MEI']);
        $RAMOATIVIDADE = new TCombo('RAMOATIVIDADE');
            $RAMOATIVIDADE->addItems(['0'=>'Comércio', '1'=>'Indústria', '2'=>'Serviços', '3'=>'Outros']);
        $CNAEPRINCIPAL = new TEntry('CNAEPRINCIPAL');
            $CNAEPRINCIPAL->setMask(TMascara::maskCnae);
        $CNAESECUNDARIO = new TEntry('CNAESECUNDARIO');
            $CNAESECUNDARIO->setMask(TMascara::maskCnae);
        $CONTADORRESPONSAVEL = new TDBSeekButton('CONTADORRESPONSAVEL', 
                                                 'gexpertlotes', 
                                                 $this->form->getName(), 
                                                 'Entidades', 
                                                 'RAZAO', 
                                                 'CONTADORRESPONSAVEL');
            $DS_CONTADORRESPONSAVEL = new TEntry('$DS_CONTADORRESPONSAVEL');
            $CONTADORRESPONSAVEL->setAuxiliar($DS_CONTADORRESPONSAVEL);
            $DS_CONTADORRESPONSAVEL->setEditable(FALSE); 
        $DATAABERTURA = new TDate('DATAABERTURA');
            $DATAABERTURA->setMask(TMascara::maskDate);
            $DATAABERTURA->setDatabaseMask(TMascara::maskDBDate);
        $DATAREGISTRO = new TDate('DATAREGISTRO');
            $DATAREGISTRO->setMask(TMascara::maskDate);
            $DATAREGISTRO->setDatabaseMask(TMascara::maskDBDate);
        $NUMEROREGISTRO = new TEntry('TIPOREGISTRO');
        $TIPOREGISTRO = new TCombo('NUMEROREGISTRO');
            $TIPOREGISTRO->addItems(['0'=>'Cartório', '1'=>'Junta Comercial']);
        $CODIGOEMPRESACONTABIL = new TEntry('CODIGOEMPRESACONTABIL');
            $CODIGOEMPRESACONTABIL->setMask(TMascara::maskInt);
        
        $aux1  = new TEntry('aux1');
        $aux2  = new TEntry('aux2');
        $aux3  = new TEntry('aux3');
        $aux4  = new TEntry('aux4');
        $aux5  = new TEntry('aux5');
        $aux6  = new TEntry('aux6');
        $aux7  = new TEntry('aux7');
        $aux8  = new TEntry('aux8');
        $aux9  = new TEntry('aux9');
        $aux10 = new TEntry('aux10');
        $aux11 = new TEntry('aux11');
        $aux12 = new TEntry('aux12');
        $aux13 = new TEntry('aux13');
        $aux14 = new TEntry('aux14');
        $aux15 = new TEntry('aux15');
        $aux16 = new TEntry('aux16');
        $aux17 = new TEntry('aux17');
        $aux18 = new TEntry('aux18');
        $aux19 = new TEntry('aux19');
        $aux20 = new TEntry('aux20');
        $aux21 = new TEntry('aux21');
        
        $HISTCTBAQUISICAO = new TDBSeekButton('HISTCTBAQUISICAO', 
                                             'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBAQUISICAO'); 
                                                       
        $HISTCTBDESMEMBRAMENTO = new TDBSeekButton('HISTCTBDESMEMBRAMENTO', 
                                            'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBDESMEMBRAMENTO');
                                              
        $HISTCTBVENDAVISTA = new TDBSeekButton('HISTCTBVENDAVISTA', 
                                              'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBVENDAVISTA');
                             
        $HISTCTBCUSTOVENDAVISTA = new TDBSeekButton('HISTCTBCUSTOVENDAVISTA', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBCUSTOVENDAVISTA');
                                  
        $HISTCTBVENDAPRAZO = new TDBSeekButton('HISTCTBVENDAPRAZO', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBVENDAPRAZO');
                            
        $HISTCTBCUSTOVENDAPRAZO = new TDBSeekButton('HISTCTBCUSTOVENDAPRAZO', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBCUSTOVENDAPRAZO');
                        
        $HISTCTBRECDIFER = new TDBSeekButton('HISTCTBRECDIFER', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBRECDIFER');
                          
        $HISTCTBDESPDIFER = new TDBSeekButton('HISTCTBDESPDIFER', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBDESPDIFER');
                            
        $HISTCTBRECEBPARC = new TDBSeekButton('HISTCTBRECEBPARC', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'CODIGO');
                                
        $HISTCTBCUSTORECEBPARC = new TDBSeekButton('HISTCTBCUSTORECEBPARC', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBCUSTORECEBPARC');
                                     
        $HISTCTBRECEITARECEBPARC = new TDBSeekButton('HISTCTBRECEITARECEBPARC', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBRECEITARECEBPARC');
            $HISTCTBRECEITARECEBPARC->setAuxiliar('aux11');                                      
        $HISTCTBCANCELAMENTO = new TDBSeekButton('HISTCTBCANCELAMENTO', 
                                             'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBCANCELAMENTO');
                                             
        $HISTCTBRECDIFERCANCELAMENTO = new TDBSeekButton('HISTCTBRECDIFERCANCELAMENTO', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBRECDIFERCANCELAMENTO');
                                                
        $HISTCTBDESPDIFERCANCELAMENTO = new TDBSeekButton('HISTCTBDESPDIFERCANCELAMENTO', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBDESPDIFERCANCELAMENTO');
                                              
        $HISTCTBCUSTOCANCELAMENTO = new TDBSeekButton('HISTCTBCUSTOCANCELAMENTO', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBCUSTOCANCELAMENTO');
                                                
        $HISTCTBINFRAESTRUTURA = new TDBSeekButton('HISTCTBINFRAESTRUTURA', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBINFRAESTRUTURA');
                                                
        $HISTCTBINFRAVENDIDOS = new TDBSeekButton('HISTCTBINFRAVENDIDOS', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBINFRAVENDIDOS');
                                            
        $HISTCTBINFRAAVENDER = new TDBSeekButton('HISTCTBINFRAAVENDER', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'CODIGO');
                                
        $HISTCTBRECJUROS = new TDBSeekButton('HISTCTBRECJUROS', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBRECJUROS');
                                   
        $HISTCTBATUALIZACAO = new TDBSeekButton('HISTCTBATUALIZACAO', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBATUALIZACAO');
                                 
        $HISTCTBRECEITAEVENT = new TDBSeekButton('HISTCTBRECEITAEVENT', 
                                         'gexpertlotes',
                                             $this->form->getName(), 
                                             'SelHistoricos',
                                             'DESCRICAO',                                              
                                             'HISTCTBRECEITAEVENT');       
        
         $HISTCTBAQUISICAO->setAuxiliar($aux1);     
         $HISTCTBDESMEMBRAMENTO->setAuxiliar($aux2);  
         $HISTCTBVENDAVISTA->setAuxiliar($aux3);        
         $HISTCTBCUSTOVENDAVISTA->setAuxiliar($aux4);    
         $HISTCTBVENDAPRAZO->setAuxiliar($aux5);    
         $HISTCTBCUSTOVENDAPRAZO->setAuxiliar($aux6);        
         $HISTCTBRECDIFER->setAuxiliar($aux7);     
         $HISTCTBDESPDIFER->setAuxiliar($aux8);       
         $HISTCTBRECEBPARC->setAuxiliar($aux9);  
         $HISTCTBCUSTORECEBPARC->setAuxiliar($aux10);    
         $HISTCTBRECEITARECEBPARC->setAuxiliar($aux11);     
         $HISTCTBCANCELAMENTO->setAuxiliar($aux12); 
         $HISTCTBRECDIFERCANCELAMENTO->setAuxiliar($aux13);    
         $HISTCTBDESPDIFERCANCELAMENTO->setAuxiliar($aux14);    
         $HISTCTBCUSTOCANCELAMENTO->setAuxiliar($aux15);      
         $HISTCTBINFRAESTRUTURA->setAuxiliar($aux16);      
         $HISTCTBINFRAVENDIDOS->setAuxiliar($aux17);  
         $HISTCTBINFRAAVENDER->setAuxiliar($aux18);
         $HISTCTBRECJUROS->setAuxiliar($aux19);   
         $HISTCTBATUALIZACAO->setAuxiliar($aux20);   
         $HISTCTBRECEITAEVENT->setAuxiliar($aux21); 

        
        $aux1->setEditable(FALSE);
        $aux2->setEditable(FALSE);
        $aux3->setEditable(FALSE);
        $aux4->setEditable(FALSE);
        $aux5->setEditable(FALSE);
        $aux6->setEditable(FALSE);
        $aux7->setEditable(FALSE);
        $aux8->setEditable(FALSE);
        $aux9->setEditable(FALSE);
        $aux10->setEditable(FALSE);
        $aux11->setEditable(FALSE);
        $aux12->setEditable(FALSE);
        $aux13->setEditable(FALSE);
        $aux14->setEditable(FALSE);
        $aux15->setEditable(FALSE);
        $aux16->setEditable(FALSE);
        $aux17->setEditable(FALSE);
        $aux18->setEditable(FALSE);
        $aux19->setEditable(FALSE);
        $aux20->setEditable(FALSE);
        $aux21->setEditable(FALSE);

            
                                         
        $ALIQPIS = new TEntry('ALIQPIS');    
            $ALIQPIS->setNumericMask(2,',','.',TRUE);
        $ALIQCOFINS = new TEntry('ALIQCOFINS');
            $ALIQCOFINS->setNumericMask(2,',','.',TRUE);
        $ALIQIRPJ = new TEntry('ALIQIRPJ');
            $ALIQIRPJ->setNumericMask(2,',','.',TRUE);
        $ALIQCSLL = new TEntry('ALIQCSLL');
            $ALIQCSLL->setNumericMask(2,',','.',TRUE);
        $CSTPIS = new TEntry('CSTPIS');
            $CSTPIS->setMask('99');
        $CSTCOFINS = new TEntry('CSTCOFINS');
            $CSTCOFINS->setMask('99');
        $SOCIORESPONSAVEL = new TDBSeekButton('SOCIORESPONSAVEL', 
                                                 'gexpertlotes', 
                                                 $this->form->getName(), 
                                                 'Entidades', 
                                                 'RAZAO', 
                                                 'SOCIORESPONSAVEL');
            $DS_SOCIORESPONSAVEL = new TEntry('$DS_SOCIORESPONSAVEL');
            $SOCIORESPONSAVEL->setAuxiliar($DS_SOCIORESPONSAVEL);
            $DS_SOCIORESPONSAVEL->setEditable(FALSE);
        

        // add the fields
        $this->form->appendPage('Gerais');
        $this->form->addFields( [ new TLabel('Empresa') ], [ $EMPRESA ] );
        $this->form->addFields( [ new TLabel('Codigo Empresa Contabil') ], [ $CODIGOEMPRESACONTABIL ] );
        $this->form->addFields( [ new TLabel('Socio responsavel') ], [ $SOCIORESPONSAVEL ] );
        $this->form->addFields( [ new TLabel('Contador responsavel') ], [ $CONTADORRESPONSAVEL ] );
        $this->form->addFields( [ new TLabel('Cnae principal') ], [ $CNAEPRINCIPAL ] );
        $this->form->addFields( [ new TLabel('Cnae secundario') ], [ $CNAESECUNDARIO ] );
        $this->form->addFields( [ new TLabel('Data abertura') ], [ $DATAABERTURA ] );
        $this->form->addFields( [ new TLabel('Data registro') ], [ $DATAREGISTRO ] );
        $this->form->addFields( [ new TLabel('Tipo registro') ], [ $TIPOREGISTRO ] );
        $this->form->addFields( [ new TLabel('Numero registro') ], [ $NUMEROREGISTRO ] );
        $this->form->addFields( [ new TLabel('Regime tributario') ], [ $REGIMETRIBUTARIO ] );
        $this->form->addFields( [ new TLabel('Ramo atividade') ], [ $RAMOATIVIDADE ] );
        $this->form->addFields( [ new TLabel('Aliq pis') ], [ $ALIQPIS ] );
        $this->form->addFields( [ new TLabel('Aliq cofins') ], [ $ALIQCOFINS ] );
        $this->form->addFields( [ new TLabel('Aliq irpj') ], [ $ALIQIRPJ ] );
        $this->form->addFields( [ new TLabel('Aliq csll') ], [ $ALIQCSLL ] );
        $this->form->addFields( [ new TLabel('Cst pis') ], [ $CSTPIS ] );
        $this->form->addFields( [ new TLabel('Cst cofins') ], [ $CSTCOFINS ] );
        
        
        $this->form->appendPage('Contábil');
        $this->form->addFields( [ new TLabel('Hist ctb aquisicao') ], [ $HISTCTBAQUISICAO ] );
        $this->form->addFields( [ new TLabel('Hist ctb desmembramento') ], [ $HISTCTBDESMEMBRAMENTO ] );
        $this->form->addFields( [ new TLabel('Hist ctb venda vista') ], [ $HISTCTBVENDAVISTA ] );
        $this->form->addFields( [ new TLabel('Hist ctb custo vendavista') ], [ $HISTCTBCUSTOVENDAVISTA ] );
        $this->form->addFields( [ new TLabel('Hist ctb venda prazo') ], [ $HISTCTBVENDAPRAZO ] );
        $this->form->addFields( [ new TLabel('Hist ctb custo vendaprazo') ], [ $HISTCTBCUSTOVENDAPRAZO ] );
        $this->form->addFields( [ new TLabel('Hist ctb rec difer') ], [ $HISTCTBRECDIFER ] );
        $this->form->addFields( [ new TLabel('Hist ctb desp difer') ], [ $HISTCTBDESPDIFER ] );
        $this->form->addFields( [ new TLabel('Hist ctb receb parc') ], [ $HISTCTBRECEBPARC ] );
        $this->form->addFields( [ new TLabel('Hist ctb custo receb parc') ], [ $HISTCTBCUSTORECEBPARC ] );
        $this->form->addFields( [ new TLabel('Hist ctb receita receb parc') ], [ $HISTCTBRECEITARECEBPARC ] );
        $this->form->addFields( [ new TLabel('Hist ctb cancelamento') ], [ $HISTCTBCANCELAMENTO ] );
        $this->form->addFields( [ new TLabel('Hist ctb rec difer cancelamento') ], [ $HISTCTBRECDIFERCANCELAMENTO ] );
        $this->form->addFields( [ new TLabel('Hist ctb desp difer cancelamento') ], [ $HISTCTBDESPDIFERCANCELAMENTO ] );
        $this->form->addFields( [ new TLabel('Hist ctb custo cancelamento') ], [ $HISTCTBCUSTOCANCELAMENTO ] );
        $this->form->addFields( [ new TLabel('Hist ctb infraestrutura') ], [ $HISTCTBINFRAESTRUTURA ] );
        $this->form->addFields( [ new TLabel('Hist ctb infra vendidos') ], [ $HISTCTBINFRAVENDIDOS ] );
        $this->form->addFields( [ new TLabel('Hist ctb infra a vender') ], [ $HISTCTBINFRAAVENDER ] );
        $this->form->addFields( [ new TLabel('Hist ctb rec juros') ], [ $HISTCTBRECJUROS ] );
        $this->form->addFields( [ new TLabel('Hist ctb atualizacao') ], [ $HISTCTBATUALIZACAO ] );
        $this->form->addFields( [ new TLabel('Hist ctb receita event') ], [ $HISTCTBRECEITAEVENT ] );
        



        // set sizes
        $EMPRESA->setSize(TWgtSizes::wsInt);
        $REGIMETRIBUTARIO->setSize(TWgtSizes::ws30);
        $RAMOATIVIDADE->setSize(TWgtSizes::ws30);
        $CNAEPRINCIPAL->setSize(TWgtSizes::ws10);
        $CNAESECUNDARIO->setSize(TWgtSizes::ws10);
        $CONTADORRESPONSAVEL->setSize(TWgtSizes::wsInt);
            $DS_CONTADORRESPONSAVEL->setSize(TWgtSizes::wsAux);
        $DATAABERTURA->setSize(TWgtSizes::wsDate);
        $DATAREGISTRO->setSize(TWgtSizes::wsDate);
        $TIPOREGISTRO->setSize(TWgtSizes::ws30);
        $NUMEROREGISTRO->setSize(TWgtSizes::wsDouble);
        $CODIGOEMPRESACONTABIL->setSize(TWgtSizes::wsInt);
	    $HISTCTBAQUISICAO->setSize(TWgtSizes::wsInt);
        $HISTCTBDESMEMBRAMENTO->setSize(TWgtSizes::wsInt);
        $HISTCTBVENDAVISTA->setSize(TWgtSizes::wsInt);
        $HISTCTBCUSTOVENDAVISTA->setSize(TWgtSizes::wsInt);
        $HISTCTBVENDAPRAZO->setSize(TWgtSizes::wsInt);
        $HISTCTBCUSTOVENDAPRAZO->setSize(TWgtSizes::wsInt);
        $HISTCTBRECDIFER->setSize(TWgtSizes::wsInt);
        $HISTCTBDESPDIFER->setSize(TWgtSizes::wsInt);
        $HISTCTBRECEBPARC->setSize(TWgtSizes::wsInt);
        $HISTCTBCUSTORECEBPARC->setSize(TWgtSizes::wsInt);
        $HISTCTBRECEITARECEBPARC->setSize(TWgtSizes::wsInt);
        $HISTCTBCANCELAMENTO->setSize(TWgtSizes::wsInt);
        $HISTCTBRECDIFERCANCELAMENTO->setSize(TWgtSizes::wsInt);
        $HISTCTBDESPDIFERCANCELAMENTO->setSize(TWgtSizes::wsInt);
        $HISTCTBCUSTOCANCELAMENTO->setSize(TWgtSizes::wsInt);
        $HISTCTBINFRAESTRUTURA->setSize(TWgtSizes::wsInt);
        $HISTCTBINFRAVENDIDOS->setSize(TWgtSizes::wsInt);
        $HISTCTBINFRAAVENDER->setSize(TWgtSizes::wsInt);
        $HISTCTBRECJUROS->setSize(TWgtSizes::wsInt);
        $HISTCTBATUALIZACAO->setSize(TWgtSizes::wsInt);
        $HISTCTBRECEITAEVENT->setSize(TWgtSizes::wsInt);        
        $ALIQPIS->setSize(TWgtSizes::wsDouble);
        $ALIQCOFINS->setSize(TWgtSizes::wsDouble);
        $ALIQIRPJ->setSize(TWgtSizes::wsDouble);
        $ALIQCSLL->setSize(TWgtSizes::wsDouble);
        $CSTPIS->setSize(TWgtSizes::wsInt);
        $CSTCOFINS->setSize(TWgtSizes::wsInt);
        $SOCIORESPONSAVEL->setSize(TWgtSizes::wsInt);
            $DS_SOCIORESPONSAVEL->setSize(TWgtSizes::wsAux);

        $aux1->setSize(TWgtSizes::wsAux);
        $aux2->setSize(TWgtSizes::wsAux);
        $aux3->setSize(TWgtSizes::wsAux);
        $aux4->setSize(TWgtSizes::wsAux);
        $aux5->setSize(TWgtSizes::wsAux);
        $aux6->setSize(TWgtSizes::wsAux);
        $aux7->setSize(TWgtSizes::wsAux);
        $aux8->setSize(TWgtSizes::wsAux);
        $aux9->setSize(TWgtSizes::wsAux);
        $aux10->setSize(TWgtSizes::wsAux);
        $aux11->setSize(TWgtSizes::wsAux);
        $aux12->setSize(TWgtSizes::wsAux);
        $aux13->setSize(TWgtSizes::wsAux);
        $aux14->setSize(TWgtSizes::wsAux);
        $aux15->setSize(TWgtSizes::wsAux);
        $aux16->setSize(TWgtSizes::wsAux);
        $aux17->setSize(TWgtSizes::wsAux);
        $aux18->setSize(TWgtSizes::wsAux);
        $aux19->setSize(TWgtSizes::wsAux);
        $aux20->setSize(TWgtSizes::wsAux);
        $aux21->setSize(TWgtSizes::wsAux);
        
        
        if (!empty($EMPRESA))
        {
            $EMPRESA->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        //$this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    
    public function onEdit($param)
    {
        if(isset($this->empresaid))
        {
            TTransaction::open('gexpertlotes');
            if(Configuracoes::where('EMPRESA','=', $this->empresaid)->count()>0)
            {                 
                 $obj = new Configuracoes($this->empresaid);
                 $this->form->setData($obj);                
            } 
            else
            {
                $this->form->clear();
            }
            TTransaction::close();         
        }        
    }
    
    public function onSave($param)
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
            $object = new Configuracoes;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated CODIGO
            $data->EMPRESA = $object->EMPRESA;
            
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
}
