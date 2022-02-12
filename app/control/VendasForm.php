<?php
/**
 * VendasForm Master/Detail
 * @author  <your name here>
 */
class VendasForm extends TTr2Page
{
    use Tr2CollectionUtilsTrait;
    
    protected $form; // form
    protected $detail_list;
    protected $empresaid;
    protected $empreendimentoid;
    protected $vendaid;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
       
        parent::__construct();
       
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Vendas');
        $this->form->setFormTitle('Vendas');
        
        $this->setDefaultPageAction();
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Vendas');
                
        $this->empresaid         = TSession::getValue('userunitid');
        $this->empreendimentoid  = TSession::getValue('empreendimentoid');
        
        $this->filtroEmpresa = new TCriteria();
        $this->filtroEmpresa->add(new TFilter('EMPRESA','=',$this->empresaid));    
        $this->filtroEmpreendimento = new TCriteria();
        $this->filtroEmpreendimento->add(new TFilter('EMPRESA','=',$this->empresaid));
        $this->filtroEmpreendimento->add(new TFilter('EMPREENDIMENTO', '=', $this->empreendimentoid));
            
        $asCur = function($value){
     
                         if(is_numeric($value))
                         {
                             return 'R$ '.number_format($value, 2, ',', '.');
                         }                         
                         return $value;
        };
        
        $asCurSQL = function($value){
                       
                       if ($value)
                       {
                           if((strpos($value, ',')>0) || (strpos($value, '.')>0)) 
                           {
                               $value = rtrim(rtrim($value,'0'),'.');    
                           }                                                                 

                           $value = rtrim(str_replace(',','.', str_replace('.', '', $value)), '.');
                           
                           return $value;                            
                       }    
                        
                       return 0;    
        };            
        
        $asBol = function($value){
                          if($value=='S')
                          {
                              return 'Sim';
                           }
                                      
                             return 'Não';
                     };                 
                     
        $asDate = function($value){
                  
                  return TDate::date2br($value);                                
        };
        
        $asDateSQL = function($value){ return TDate::date2us($value); };
        
        $asBolSQL  = function($value){ return substr($value,1); };
                                  
        $sum = function($values){return 'R$ '.number_format(array_sum($values), 2, ',', '.');};  
        
        $count = function($values){return count($values);}; 
        
        $bold = function($quitado, $object, $row){ 
                                    if($quitado=='N')
                                          {
                                            $row->{'style'} .= ';font-weight: bold';                                              
                                          };
                                          if($quitado=='S')
                                          {
                                              return 'Sim';
                                          };
                                      
                                          return 'Não'; 
                                  };                                                  
        
        $LANCAMENTO = new TEntry('LANCAMENTO');
            $LANCAMENTO->setEditable(FALSE);
        $EMPRESA = new TEntry('EMPRESA');
            $EMPRESA->setEditable(FALSE);
        $DS_EMPREENDIMENTO = new TEntry('DS_EMPREENDIMENTO');
        $DS_EMPREENDIMENTO->setEditable(FALSE);     
        $EMPREENDIMENTO = new TDBSeekButton('EMPREENDIMENTO', 
                                            'gexpertlotes', 
                                            $this->form->getName(),
                                            'Empreendimentos',
                                            'DESCRICAO',
                                            'EMPREENDIMENTO',
                                            $DS_EMPREENDIMENTO,
                                            $this->filtroEmpresa);  
             $EMPREENDIMENTO->setAuxiliar($DS_EMPREENDIMENTO); 
             $EMPREENDIMENTO->addValidation('EMPREENDIMENTO', new TRequiredValidator);                                          
       $QUADRA = new TEntry('QUADRA');
           $QUADRA->setMask(TMascara::maskInt);
           $QUADRA->addValidation('QUADRA', new TRequiredValidator);
       $LOTE = new TEntry('LOTE');
           $LOTE->setMask(TMascara::maskInt);
           $LOTE->addValidation('LOTE', new TRequiredValidator);     
       $DS_ENTIDADE = new TEntry('DS_ENTIDADE');
       $DS_ENTIDADE->setEditable(FALSE);     
       $ENTIDADE = new TDBSeekButton('ENTIDADE', 
                                     'gexpertlotes', 
                                     $this->form->getName(),
                                     'Entidades',
                                     'RAZAO',
                                     'ENTIDADE',
                                     $DS_ENTIDADE);  
            $ENTIDADE->setAuxiliar($DS_ENTIDADE); 
            $ENTIDADE->addValidation('ENTIDADE', new TRequiredValidator);    
       $EMISSAO = new TDate('EMISSAO');
           $EMISSAO->setMask(TMascara::maskDate);
           $EMISSAO->setDatabaseMask(TMascara::maskDBDate);
       $VALOR = new TEntry('VALOR');
           $VALOR->setNumericMask(2, ',', '.', TRUE); 
           $VALOR->setExitAction(new TAction([$this, 'onExitPARCELAS']));              
       $ENTRADA = new TEntry('ENTRADA');
           $ENTRADA->setNumericMask(2, ',', '.', TRUE); 
               $ENTRADA->setExitAction(new TAction([$this, 'onExitPARCELAS']));
       $CONTRATO = new TEntry('CONTRATO');
       $PARCELAS = new TEntry('PARCELAS');
           $PARCELAS->setMask(TMascara::maskInt);
           $PARCELAS->setExitAction(new TAction([$this, 'onExitPARCELAS']));
       $VALORPARCELA = new TEntry('VALORPARCELA');
           $VALORPARCELA->setNumericMask(2, ',', '.', TRUE);
       $REAJUSTE = new TCombo('REAJUSTE');
           $REAJUSTE->addItems(['1'=>'Janeiro',
                                '2'=>'Fevereiro',
                                '3'=>'Março',
                                '4'=>'Abril',
                                '5'=>'Maio',
                                '6'=>'Junho',
                                '7'=>'Julho',
                                '8'=>'Agosto',
                                '9'=>'Setembro',
                                '10'=>'Outubro',
                                '11'=>'Novembro',
                                '12'=>'Dezembro',]);
       $OBSERVACAO = new TText('OBSERVACAO');
       $CANCELADO = new TCombo('CANCELADO');
           $CANCELADO->addItems(['N'=>'Não', 'S'=>'Sim']);
       $CANCELAMENTO = new TDate('CANCELAMENTO');
           $CANCELAMENTO->setMask(TMascara::maskDate);
           $CANCELAMENTO->setDatabaseMask(TMascara::maskDBDate); 
       
       $PARCELASRESCISAO = new TEntry('PARCELASRESCISAO');
       $PARCELASRESCISAO->setMask(TMascara::maskInt);
       $VALORPARCELARESCISAO = new TEntry('VALORPARCELARESCISAO');
       $VALORPARCELARESCISAO->setNumericMask(2,',','.', TRUE);  
       $ESTORNO = new TEntry('ESTORNO');
       $ESTORNO->setNumericMask(2,',','.',TRUE);   
       
        $CONTACTBCANCELAMENTO  = new TTr2DBSeekButton('CONTACTBCANCELAMENTO', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CONTACTBCANCELAMENTO', null, $this->filtroEmpresa);         
        $CONTACTBCANCELAMENTO->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBCANCELAMENTO->setModelKey('CODIGO');
        
        $CONTACTBENTIDADE      = new TTr2DBSeekButton('CONTACTBENTIDADE', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CONTACTBENTIDADE', null, $this->filtroEmpresa);        
        $CONTACTBENTIDADE->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBENTIDADE->setModelKey('CODIGO');
        
        $CONTACTBENTIDADELP    = new TTr2DBSeekButton('CONTACTBENTIDADELP', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CONTACTBENTIDADELP', null, $this->filtroEmpresa);
        $CONTACTBENTIDADELP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBENTIDADELP->setModelKey('CODIGO');
        
        $CTACTBRECEITADIFERCP  = new TTr2DBSeekButton('CTACTBRECEITADIFERCP', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CTACTBRECEITADIFERCP', null, $this->filtroEmpresa); 
        $CTACTBRECEITADIFERCP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CTACTBRECEITADIFERCP->setModelKey('CODIGO');
        
        $CTACTBRECEITADIFERLP  = new TTr2DBSeekButton('CTACTBRECEITADIFERLP', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CTACTBRECEITADIFERLP', null, $this->filtroEmpresa);
        $CTACTBRECEITADIFERLP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CTACTBRECEITADIFERLP->setModelKey('CODIGO');
        
        $CTACTBDESPESADIFERCP  = new TTr2DBSeekButton('CTACTBDESPESADIFERCP', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CTACTBDESPESADIFERCP', null, $this->filtroEmpresa);
        $CTACTBDESPESADIFERCP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CTACTBDESPESADIFERCP->setModelKey('CODIGO');
        
        $CTACTBDESPESADIFERLP  = new TTr2DBSeekButton('CTACTBDESPESADIFERLP', 'gexpertlotes', 'form_Vendas', 'Plano', 'concat(CODIGO,CLASSIFICACAO, DESCRICAO)', 'CTACTBDESPESADIFERLP', null, $this->filtroEmpresa);                        
        $CTACTBDESPESADIFERLP->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CTACTBDESPESADIFERLP->setModelKey('CODIGO');
        
        $aux1 = new TEntry('aux1');
        $aux2 = new TEntry('aux2');
        $aux3 = new TEntry('aux3');
        $aux4 = new TEntry('aux4');
        $aux5 = new TEntry('aux5');        
        $aux6 = new TEntry('aux6');
        $aux7 = new TEntry('aux7');
        
        $CONTACTBENTIDADE->setAuxiliar($aux1);
        $CONTACTBENTIDADELP->setAuxiliar($aux2);
        $CTACTBRECEITADIFERCP->setAuxiliar($aux3);
        $CTACTBRECEITADIFERLP->setAuxiliar($aux4);
        $CTACTBDESPESADIFERCP->setAuxiliar($aux5);
        $CTACTBDESPESADIFERLP->setAuxiliar($aux6);
        $CONTACTBCANCELAMENTO->setAuxiliar($aux7);
        
        $aux1->setEditable(FALSE);
        $aux2->setEditable(FALSE);
        $aux3->setEditable(FALSE);
        $aux4->setEditable(FALSE);
        $aux5->setEditable(FALSE);
        $aux6->setEditable(FALSE);
        $aux7->setEditable(FALSE);
        
        //Tamanho dos campos
        $LANCAMENTO->setSize(TWgtSizes::wsInt);
        $EMPRESA->setSize(TWgtSizes::wsInt);
        $EMPREENDIMENTO->setSize(TWgtSizes::wsInt);
        $DS_EMPREENDIMENTO->setSize(TWgtSizes::wsAux);
        $ENTIDADE->setSize(TWgtSizes::wsInt);
        $DS_ENTIDADE->setSize(TWgtSizes::wsAux);
        $EMISSAO->setSize(TWgtSizes::wsDate);
        $VALOR->setSize(TWgtSizes::wsDouble);
        $PARCELAS->setSize(TWgtSizes::wsInt);
        $VALORPARCELA->setSize(TWgtSizes::wsDouble);
        $PARCELASRESCISAO->setSize(TWgtSizes::wsInt);
        $VALORPARCELARESCISAO->setSize(TWgtSizes::wsDouble);
        $ENTRADA->setSize(TWgtSizes::wsDouble);
        $CONTRATO->setSize(TWgtSizes::wsDouble);
        $REAJUSTE->setSize(TWgtSizes::wsDate);
        $OBSERVACAO->setSize(TWgtSizes::wsBlob);
        $CANCELADO->setSize(TWgtSizes::wsBol);
            $CANCELADO->setChangeAction(new TAction([$this, 'onChangeCANCELADO']));  
        $CANCELAMENTO->setSize(TWgtSizes::wsDate);       
        $QUADRA->setSize(TWgtSizes::wsInt);
        $LOTE->setSize(TWgtSizes::wsInt);
        $ESTORNO->setSize(TWgtSizes::wsDouble);
        
        $CONTACTBENTIDADE->setSize(TWgtSizes::wsInt);
        $CONTACTBENTIDADELP->setSize(TWgtSizes::wsInt);
        $CTACTBRECEITADIFERCP->setSize(TWgtSizes::wsInt);
        $CTACTBRECEITADIFERLP->setSize(TWgtSizes::wsInt);
        $CTACTBDESPESADIFERCP->setSize(TWgtSizes::wsInt);
        $CTACTBDESPESADIFERLP->setSize(TWgtSizes::wsInt);
        $CONTACTBCANCELAMENTO->setSize(TWgtSizes::wsInt);
        
        $aux1->setSize(TWgtSizes::wsAux);
        $aux2->setSize(TWgtSizes::wsAux);
        $aux3->setSize(TWgtSizes::wsAux);
        $aux4->setSize(TWgtSizes::wsAux);
        $aux5->setSize(TWgtSizes::wsAux);
        $aux6->setSize(TWgtSizes::wsAux);
        $aux7->setSize(TWgtSizes::wsAux);
        
        //Adiciona campos ao formulario
        $this->form->appendPage('Geral');
        $this->form->addFields([new TLabel('Lancamento')], [$LANCAMENTO]);
        $this->form->addFields([new TLabel('Empresa')], [$EMPRESA]);
        $this->form->addFields([new TLabel('Empreendimento')],[$EMPREENDIMENTO]);
        $this->form->addFields([new TLabel('Proprietário')],[$ENTIDADE]);
        $this->form->addFields([new TLabel('Quadra/Andar')],[$QUADRA]);
        $this->form->addFields([new TLabel('Lote/Apto')],[$LOTE]);
        $this->form->addFields([new TLabel('Dt. Emissao')],[$EMISSAO]);
        $this->form->addFields([new TLabel('Valor')],[$VALOR]);
        $this->form->addFields([new TLabel('Vlr Entrada')],[$ENTRADA]);
        $this->form->addFields([new TLabel('Qtd Parc')],[$PARCELAS]);
        $this->form->addFields([new TLabel('Vlr Parc')],[$VALORPARCELA]);
        $this->form->addFields([new TLabel('Nr. Contrato')],[$CONTRATO]);
        $this->form->addFields([new TLabel('Mês Reajuste')],[$REAJUSTE]);
        $this->form->addFields([new TLabel('Observação')],[$OBSERVACAO]);  

        // detail fields
        $detail_ID = new THidden('detail_ID');
        $detail_PARCELA = new TEntry('detail_PARCELA');
        $detail_VALOR = new TEntry('detail_VALOR');
        $detail_QUITADO = new TEntry('detail_QUITADO');
        $detail_OBSERVACAO = new TEntry('detail_OBSERVACAO');
        $detail_VENCIMENTO = new TDate('detail_VENCIMENTO');
        $detail_USUARIOCAD = new TEntry('detail_USUARIOCAD');
        $detail_DATACAD = new TEntry('detail_DATACAD');
        $detail_USUARIOALT = new TEntry('detail_USUARIOALT');
        $detail_DATAALT = new TEntry('detail_DATAALT');
        $detail_SALDO = new TEntry('detail_SALDO');

        if (!empty($LANCAMENTO))
        {
            $LANCAMENTO->setEditable(FALSE);
        }
        
        # *************** PARCELAS ******************************************
        $this->form->appendPage('Parcelas');
        // detail fields    
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGridScroll);
        $this->detail_list->setId('Vendas_list');
        $this->detail_list->setHeight(280);
        $this->detail_list->class.= ' table table-bordered';
                
        // items
        $ID         = $this->detail_list->addQuickColumn('Id', 'ID', 'left', 100);
        $PARCELA    = $this->detail_list->addQuickColumn('Parcela', 'PARCELA', 'left', 150);
        $VENCIMENTO = $this->detail_list->addQuickColumn('Vencimento', 'VENCIMENTO', 'left', 150);
        $VALOR      = $this->detail_list->addQuickColumn('Valor', 'VALOR', 'left', 250);
        $RECEBIDO   = $this->detail_list->addQuickColumn('Recebido', 'recebido', 'left', 250);        
        $SALDO      = $this->detail_list->addQuickColumn('Saldo', 'SALDO', 'left', 250);
        $QUITADO    = $this->detail_list->addQuickColumn('Quitado', 'QUITADO', 'left', 80);
        
        $VENCIMENTO->setTransformer($asDate);
        $VALOR->setTransformer($asCur);
        $SALDO->setTransformer($asCur);
        $RECEBIDO->setTransformer($asCur);
        
        $QUITADO->setTransformer($asBol);
        $QUITADO->setTransformer($bold);
        
        $ID->setTotalFunction($count);
        $VALOR->setTotalFunction($sum);
        $SALDO->setTotalFunction($sum);
        $RECEBIDO->setTotalFunction($sum);
        
        $searchParcela    = new TEntry('searchParcela');
        $searchVencimento = new TDate('searchVencimento');
        $searchValor      = new TEntry('searchValor');
        $searchRecebido   = new TEntry('searchRecebido');
        $searchSaldo      = new TEntry('searchSaldo');
        $searchQuitado    = new TCombo('searchQuitado');
        
        $searchQuitado->addItems(['S'=>'Sim', 'N'=>'Não']);
        $searchVencimento->setMask('dd/mm/yyyy', FALSE);
        
        $searchParcela->tabindex=-1;
        $searchVencimento->tabindex=-1;
        $searchValor->tabindex=-1;
        $searchRecebido->tabindex=-1;
        $searchSaldo->tabindex=-1;
        $searchQuitado->tabindex=-1;
        
        $searchParcela->exitOnEnter();
        $searchVencimento->exitOnEnter();
        $searchValor->exitOnEnter();
        $searchRecebido->exitOnEnter();
        $searchSaldo->exitOnEnter();
        
        $searchParcela->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>'1', 'subclass'=>'VendasParcelas']));
        $searchVencimento->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>'1', 'subclass'=>'VendasParcelas']));
        $searchValor->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>'1', 'subclass'=>'VendasParcelas']));
        $searchRecebido->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>'1', 'subclass'=>'VendasParcelas']));
        $searchSaldo->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>'1', 'subclass'=>'VendasParcelas']));
        $searchQuitado->setChangeAction(new TAction([$this, 'onSubSearch'], ['static'=>'1', 'subclass'=>'VendasParcelas']));
        
        $this->form->addField($searchParcela);
        $this->form->addField($searchVencimento);
        $this->form->addField($searchValor);
        $this->form->addField($searchRecebido);
        $this->form->addField($searchSaldo);
        $this->form->addField($searchQuitado);
        
        $searchParcela->style    = "width:100%";
        $searchVencimento->style = "width:100%";
        $searchValor->style      = "width:100%";
        $searchRecebido->style   = "width:100%";
        $searchSaldo->style      = "width:100%";
        $searchQuitado->style    = "width:100%";
        
        
        $this->addSubClass('VendasParcelas', 'Vendas', 'LANCAMENTO', 'VENDA');
        $this->addSubDatagrid('VendasParcelas', $this->detail_list);
        $this->addSubFilter('VendasParcelas', 'PARCELA', '=', 'searchParcela'); 
        $this->addSubFilter('VendasParcelas', 'VENCIMENTO', '=', 'searchVencimento', $asDateSQL); 
        $this->addSubFilter('VendasParcelas', 'VALOR', 'like', 'searchValor', $asCurSQL); 
        $this->addSubFilter('VendasParcelas', 'VALOR-SALDO', 'like', 'searchRecebido', $asCurSQL);
        $this->addSubFilter('VendasParcelas', 'SALDO', 'like', 'searchSaldo', $asCurSQL);  
        $this->addSubFilter('VendasParcelas', 'QUITADO', '=', 'searchQuitado'); 
        
        $this->addSubDefaultOrder('VendasParcelas', 'VENCIMENTO', 'asc'); 
       
        $PARCELA->setAction(new TAction([$this, 'onSubReorder'], ['static'=>'1','subclass'=>'VendasParcelas', 'order'=>'PARCELA']));
        $VENCIMENTO->setAction(new TAction([$this, 'onSubReorder'], ['static'=>'1','subclass'=>'VendasParcelas', 'order'=>'VENCIMENTO']));
        $VALOR->setAction(new TAction([$this, 'onSubReorder'], ['static'=>'1','subclass'=>'VendasParcelas', 'order'=>'VALOR']));
        $SALDO->setAction(new TAction([$this, 'onSubReorder'], ['static'=>'1','subclass'=>'VendasParcelas', 'order'=>'SALDO']));
        $RECEBIDO->setAction(new TAction([$this, 'onSubReorder'], ['static'=>'1','subclass'=>'VendasParcelas', 'order'=>'VALOR-SALDO']));
        $QUITADO->setAction(new TAction([$this, 'onSubReorder'], ['static'=>'1','subclass'=>'VendasParcelas', 'order'=>'QUITADO']));

        $tr = new TElement('tr');
        $tr->{'style'} = "display: inline-table; width: calc(100% - 20px)";
        $tr->add(TElement::tag('td', '')); 
        $tr->add(TElement::tag('td', '')); 
        $tr->add(TElement::tag('td', '')); 
        $tr->add(TElement::tag('td', $searchParcela));
        $tr->add(TElement::tag('td', $searchVencimento));
        $tr->add(TElement::tag('td', $searchValor));
        $tr->add(TElement::tag('td', $searchRecebido));
        $tr->add(TElement::tag('td', $searchSaldo));
        $tr->add(TElement::tag('td', $searchQuitado));
        
        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction(['VendasParcelasForm', 'onEdit']),   'ID', 'far:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'ID', 'far:trash-alt red');
        $this->detail_list->createModel();
        
        $this->detail_list->getHead()->add($tr);
        
        $dropdown_Parcelas = new TDropDown(_t('Export'), 'fa:list');
        $dropdown_Parcelas->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_Parcelas->addAction(_t('Save as CSV'), new TAction([$this, 'onExportSubCSV'], ['subclass'=>'VendasParcelas', 'static'=>'1']), 'fa:table fa-fw blue' );
       
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->addHeaderActionLink(_t('Clear Filters'), new TAction([$this, 'clearSubFilters'],['static'=>'1', 'status'=>'browse', 'subclass'=>'VendasParcelas', 'reset'=>1]), 'fa:eraser red');
        $panel->addHeaderWidget($dropdown_Parcelas);
        
        $panel->getBody()->style = 'overflow-x:hidden';
        $panel->addFooter('');
        $add = new TActionLink('Incluir Parcela', new TAction(['VendasParcelasForm', 'onEdit']),null,null,null, 'fa:plus blue');
        $add->class='btn btn-default btn-sm';
        $addAll = new TActionLink('Incluir Parcelas em Massa', new TAction([$this, 'onAddParcelas'], ['static'=>'1']),null,null,null, 'fa:play green');
        $addAll->class='btn btn-default btn-sm';
        $delAll = new TActionLink('Excluir Todas as Parcelas', new TAction([$this, 'onDelParcelas'], ['static'=>'1']),null,null,null, 'fas:trash-alt red');
        $delAll->class='btn btn-default  btn-sm';
        $panel->getFooter()->add($add);
        $panel->getFooter()->add($addAll);
        $panel->getFooter()->add($delAll);
        $this->form->addContent( [$panel] );
        
        $this->form->appendPage('Contábil');
        $this->form->addFields([new TLabel('Conta Ctb Proprietário (CP)')],[$CONTACTBENTIDADE]);
        $this->form->addFields([new TLabel('Conta Ctb Proprietário (LP)')],[$CONTACTBENTIDADELP]);        
        $this->form->addFields([new TLabel('Conta Ctb Rec Difer (CP)')],[$CTACTBRECEITADIFERCP]);
        $this->form->addFields([new TLabel('Conta Ctb Rec Difer (LP)')],[$CTACTBRECEITADIFERLP]);
        $this->form->addFields([new TLabel('Conta Ctb Desp Difer (CP)')],[$CTACTBDESPESADIFERCP]);
        $this->form->addFields([new TLabel('Conta Ctb Desp Difer (LP)')],[$CTACTBDESPESADIFERLP]);
        
        
        $this->form->appendPage('Distrato');
        $this->form->addFields([new TLabel('Cancelado')],[$CANCELADO]);
        $this->form->addFields([new TLabel('Dt. Cancelamento')],[$CANCELAMENTO]);
        $this->form->addFields([new TLabel('Vl. Estorno')],[$ESTORNO]);
        $this->form->addFields([new TLabel('Qtd. Parcelas')],[$PARCELASRESCISAO]);
        $this->form->addFields([new TLabel('Vl. Parcela')],[$VALORPARCELARESCISAO]);
        
        
        # *****************DISTRATO ***********************
        $this->form->addContent( ['<br><h5>Parcelas do Distrato</h5><hr>'] );
        
        $this->distrato_details = new BootstrapDatagridWrapper(new TQuickGridScroll);
        $this->distrato_details->setId('rescisao_list');
        $this->distrato_details->setHeight(150);
        $this->{"style"} = ' width: 100%';
        
        $this->addSubClass('VendasRescisaoParcelas', 'Vendas', 'LANCAMENTO', 'VENDA');
        
        
                
        $coldist_id         = $this->distrato_details->addQuickColumn('Id', 'ID', 'left', null, new TAction([$this, 'onSubReorder'], ['order'=>'ID', 'static'=>1, 'subclass'=>'VendasRescisaoParcelas']));
        $coldist_vencimento = $this->distrato_details->addQuickColumn('Vencimento', 'VENCIMENTO', 'left', null, new TAction([$this, 'onSubReorder'], ['order'=>'VENCIMENTO', 'static'=>1, 'subclass'=>'VendasRescisaoParcelas']));
        $coldist_valor      = $this->distrato_details->addQuickColumn('Valor', 'VALOR', 'left', null, new TAction([$this, 'onSubReorder'], ['order'=>'VALOR', 'static'=>1, 'subclass'=>'VendasRescisaoParcelas']));  
        
        $coldist_valor->setTransformer($asCur);
        $coldist_vencimento->setTransformer($asDate);
        $coldist_valor->setTotalFunction($sum);
        $coldist_id->setTotalFunction($count);
        
        $this->distrato_details->addQuickAction('Editar', new TDataGridAction(['VendasRescisaoParcelasForm', 'onEdit']), 'ID', 'far:edit blue');
        $this->distrato_details->addQuickAction('Excluir', new TDataGridAction([$this, 'DeleteParcelasRescisao']), 'ID', 'far:trash-alt red'); 
        
        $this->distrato_details->createModel();
        $this->addSubDatagrid('VendasRescisaoParcelas', $this->distrato_details);
        
        $btnAdd    = new TActionLink('Incluir Parcela', new TAction(['VendasRescisaoParcelasForm', 'onEdit']), null, null, null, 'fa:plus blue');
        $btnAdd->class = 'btn btn-default btn-sm';
        $btnAddAll = new TActionLink('Incluir Parcelas em Massa', new TAction([$this, 'onAddParcelasRescisao']), null,null,null, 'fa:play green');
        $btnAddAll->class = 'btn btn-default btn-sm';
        $btnDelAll = new TActionLink('Excluir Todas as Parcelas', new TAction([$this, 'onDelParcelasRescisao']),null,null,null, 'fas:trash-alt red');
        $btnDelAll->class = 'btn btn-default btn-sm';
        
        $dropdown_RescisaoParcelas = new TDropDown(_t('Export'), 'fa:list');
        $dropdown_RescisaoParcelas->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_RescisaoParcelas->addAction(_t('Save as CSV'), new TAction([$this, 'onExportSubCSV'], ['static'=>'1', 'subclass'=>'VendasRescisaoParcelas']), 'fa:table fa-fw blue' );
        
        $paneldistrato = new TPanelGroup();
        $paneldistrato->getBody()->style = 'overflow-x:hidden';
        $paneldistrato->addFooter('');
        $paneldistrato->addHeaderWidget($dropdown_RescisaoParcelas);
        
        $paneldistrato->getFooter()->add($btnAdd);
        $paneldistrato->getFooter()->add($btnAddAll);
        $paneldistrato->getFooter()->add($btnDelAll);
        $paneldistrato->add($this->distrato_details);
        $this->form->addContent([$paneldistrato]);
        
        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        //$this->form->addButton(_t('Back'), 'history.back()', 'far:arrow-alt-circle-left blue');
        $this->form->addAction(_t('Back'), new TAction(['VendasList', 'onReload']), 'far:arrow-alt-circle-left blue');
        
        
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Vendas', 'Cadastro'], TRUE));
        $container->add($this->form);
        parent::add($container);
    }
    
    public function fireEvents($object)
    {
        $object->EMISSAO = TDateTime::convertToMask($object->EMISSAO, 'yyyy-mm-dd', 'dd/mm/yyyy');
       
        return $object;
    }
    
    public function onDeleteDetail( $param )
    { 
      try
       { 
        if(isset($param['key']))
        {
            $key = $param['key'];
            
            TTransaction::open('gexpertlotes');
            
            $obj = new VendasParcelas($key);
            $obj->delete();
        
            TTransaction::close();    
        }
       
        // get detail id
        $detail_id = $param['key'];
        
        // delete the item from session
        //unset($items[ $detail_id ] );
       
        // delete item from screen
        TScript::create("ttable_remove_row_by_id('Vendas_list', '{$detail_id}')");
       
       } 
       catch (Exception $e)
       {
           new TMessage('error', $e->getMessage());
       } 
        
        self::keep(['p'=>1]); //Volta para pagina parcelas
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
  /*  public function onEdit($param)
    {
        $this->setLastCurrentPage($param); 
        $this->setSubMasterId($param);
        
        try
        {
            TTransaction::open('gexpertlotes');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $param['subclass']='VendasParcelas';
                
                $parcelas  = $this->onSubReload($param); //VendasParcelas::where('VENDA', '=', $key)->orderBy('PARCELA', 'asc')->load();

                $param['subclass']='VendasRescisaoParcelas';
                $parcelasdistrato =  $this->onSubReload($param); //VendasRescisaoParcelas::where('VENDA', '=', $key)->orderBy('VENCIMENTO', 'asc')->load();
                
                $object = new Vendas($key);
                $this->detail_list->clear();
                $this->detail_list->addItems($parcelas); 
                $this->distrato_details->addItems($parcelasdistrato);  
                
                self::onChangeCANCELADO(['CANCELADO'=>$object->CANCELADO]);  
                       
                TSession::setValue('vendaid', $key);
                $this->vendaid = $key;                
                
                $this->form->setData($object); // fill the form with the active record data
                $this->onReload( $param ); // reload items list
                
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
    } */
    
    /**
     * Save the Master/Detail data from form/session to database
     */
 /*   public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open('gexpertlotes');
            
            
            $data = $this->form->getData();
            
            if(empty($data->EMPRESA))
            {
                $data->EMPRESA = TSession::getValue('userunitid');
            }
            
            $master = new Vendas;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            // delete details
            
            
            TTransaction::close(); // close the transaction
            
            // reload form and session items
            $this->onEdit(array('key'=>$master->LANCAMENTO));
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    } */
    
    
     
     public static function onChangeCANCELADO($param) 
    {
        if($param['CANCELADO']=='S')
        {
             TDate::enableField('form_Vendas', 'CANCELAMENTO'); 
        } else 
             {
                  TDate::disableField('form_Vendas', 'CANCELAMENTO');   
             }                    
    }
    
    public function onAddParcelas($param)
    {
        
        $frmAddParcelas = new TQuickForm('form_addparcelas');
        $frmAddParcelas->setFormTitle('Gerar Parcelas');
        $frmAddParcelas->style = 'padding:30px;';
        
        $dia        = new TEntry('dia');
        $data       = new TDate('data');    
        $inicio     = new TEntry('inicio');
        $quantidade = new TEntry('quantidade');   
        $valor      = new TEntry('valor');
        
        $frmAddParcelas->addQuickField(new TLabel('<b>Dia Vencto</b>'), $dia, TWgtSizes::wsInt);
        $frmAddParcelas->addQuickField(new TLabel('<b>Data Inicial</b>'), $data, TWgtSizes::wsDate);
        $frmAddParcelas->addQuickField(new TLabel('<b>Parc Inicial</b>'), $inicio, TWgtSizes::wsInt);
        $frmAddParcelas->addQuickField(new TLabel('<b>Qtde Parc</b>'), $quantidade, TWgtSizes::wsInt);
        $frmAddParcelas->addQuickField(new TLabel('<b>Vlr Parc</b>'), $valor, TWgtSizes::wsDouble);
        
        $dia->setMask(TMascara::maskInt);
        $data->setMask(TMascara::maskDate);
        $data->setDatabaseMask(TMascara::maskDBDate);
        $inicio->setMask(TMascara::maskInt);
        $quantidade->setMask(TMascara::maskInt);
        $valor->setNumericMask(2,',','.', TRUE);
        
        $frmAddParcelas->addQuickAction('Executar / Gerar', new TAction([$this, 'addParcelas'], ['static'=>'1']), 'fa:play green');
          
        new TInputDialog(APPLICATION_NAME, $frmAddParcelas, new TAction([$this, 'onEdit'], ['status'=>'browse']));         
    }
    
    public static function onExitPARCELAS($param)
    {
        $vvalor     = TConversion::asDouble($param['VALOR']);
        $ventrada   = TConversion::asDouble($param['ENTRADA']);
        $vparcelas  = TConversion::asInt($param['PARCELAS']);
        
        if ($vparcelas>0)
        {
            $valorparcela = ($vvalor-$ventrada)/$vparcelas;
        } else 
        {
            $valorparcela = ($vvalor-$ventrada);
        }
        
        $obj = new StdClass();
        $obj->VALORPARCELA = number_format($valorparcela,2,',','.');
        
        TForm::sendData('form_Vendas',$obj);   
    }
    
    public function addParcelas($param)
    {
        if(!empty($param['dia'])&!empty($param['inicio'])&!empty($param['quantidade'])&!empty($param['valor'])&!empty($param['data']))
        {
            TTransaction::open('gexpertlotes');
            $con = TTransaction::get();
            
            $fmtValor = TConversion::asDouble($param['valor']); 

            $script = $con->prepare('call sp_gera_parcelas(?, ?, ?, ?, ?, ?, ?, ?)');
            $script->execute([$this->getSubMasterId(),
                      $param['dia'],
                      $param['inicio'],
                      $param['quantidade'],
                      $fmtValor,
                      TSession::getValue('userid'),
                      '0',
                      TConversion::asSQLDate($param['data'])]);

           TTransaction::close();
           
           new TMessage('info', 'Comando executado com sucesso!', new TAction([$this, 'onEdit'], ['status'=>'browse']));
          }       
    }      
    
    public function onDelParcelas($param)
    {
                  
            $actSim = new TAction([$this,'delParcelas'], ['static'=>'1']);
       
            new TQuestion('Deseja realmente excluir todas as parcelas?', $actSim);        
    }
    
    public function delParcelas($param)
    {
            TTransaction::open('gexpertlotes');
            $con = TTransaction::get();
            
            $script = $con->prepare('call sp_gera_parcelas(?, ?, ?, ?, ?, ?, ?, ?)');
            $script->execute([$this->getSubMasterId(),0,0,0,0,0,'1', '1899-01-01']);
                     
           TTransaction::close();
       
           new TMessage('info', 'Comando executado com sucesso!', new TAction([$this, 'onEdit'], ['status'=>'browse', 'reset'=>'1']));
    }
    
    public function onEditParcelasRescisao($param)
    {
        
    }
    
    public function DeleteParcelasRescisao($param)
    {
        self::keep(['p'=>2]);
        
        TTransaction::open('gexpertlotes');
        
        if (isset($param['key']))
        {
           
           $key = $param['key'];
           $obj = new VendasRescisaoParcelas($key);
           $obj->delete();
        }
        
        TTransaction::close();
        
        self::keep(['p'=>2]);            
    }
    
    public function onAddParcelasRescisao($param)
    {
        self::keep(['p'=>2]);   
    }
    
    public function addParcelasRescisao($param)
    {
        self::keep(['p'=>2]);
    }
    
    public function onDelParcelasRescisao($param)
    {
        self::keep(['p'=>2]);    
    }
    
    public function delParcelasRecisao($param)
    {
        self::keep(['p'=>2]); 
        
        TTransaction::open('gexpertlotes');
        
        
        TTransaction::close();
          
    }
    
    public function newParcelaRescisao($param)
    {
        //
    }
    
    
    function onSearchParcelas($param)
    {       
        //$this->onSearchDetail(['detail_class'=>'VendasParcelas']);          
    }
    
    
    
}

