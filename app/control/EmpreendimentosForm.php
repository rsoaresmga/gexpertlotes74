<?php

use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Util\TActionLink;
use Adianti\Widget\Wrapper\TQuickForm;
use Adianti\Widget\Wrapper\TQuickGrid;

/**
 * EmpreendimentosForm Master/Detail
 * @author  <your name here>
 */
class EmpreendimentosForm extends TTr2Page
{
    protected $form; // form
    protected $detail_list;
    protected $criteria;
    
    use Tr2CollectionUtilsTrait;
    
    /**
     * Page constructor
     */
    public function __construct($param)
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('frm'.__CLASS__);
        $this->form->setFormTitle('Empreendimentos');
        
        $this->setDefaultPageAction();
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Empreendimentos');
                
        $this->criteria = new TCriteria;
        $this->criteria->add(new TFilter('EMPRESA','=', TSession::getValue('userunitid')));
                
        $sum = function($values)
               {
                   return 'R$ '.number_format(array_sum($values), 5,',','.');
               };
        
        $sum2 = function($values)
               {
                   return number_format(array_sum($values), 5,',','.');
               };
               
        $count = function($values)
                 {
                     return count($values);
                 };
        $asCur = function($value){
     
                         if(is_numeric($value))
                         {
                             return 'R$ '.number_format($value, 5, ',', '.');
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
                  
        $asSituacao = function($value, $object, $row)
                       {
                           $span = new TElement('span');
                           
                           
                           switch ($value)
                           {
                               case 0: $span->class='label label-primary'; // Aberto
                                       $value = 'Aberto';
                                   break;
                               case 1: $span->class='label label-success'; //  Vendido
                                       $value = 'Vendido';
                                   break;
                               case 2: $span->class='label label-danger'; //  Devolvido
                                       $value = 'Devolvido';
                                   break;
                               case 3: $span->class='label label-secondary'; // Renegociado
                                       $value = 'Renegociado';
                                   break;
                               case 4: $span->class='label label-info'; //  Revendido
                                       $value = 'Revendido';
                                   break;                
                           }
                           
                           $span->add($value);
                           
                           return $span;                               
                       };                       
        
        // master fields
        $CODIGO = new TEntry('CODIGO');  
        $EMPRESA = new TEntry('EMPRESA');
        $EMPRESA->setEditable(FALSE);
        $TIPO = new TCombo('TIPO');
        $TIPO->addItems(['0'=>'Urbano','1'=>'Rural']);
        $DESCRICAO = new TEntry('DESCRICAO');
        $DESCRICAO->forceUpperCase();
        $DATAAQUISICAO = new TDate('DATAAQUISICAO');
        $DATAAQUISICAO->setMask(TMascara::maskDate);
        $DATAAQUISICAO->setDatabaseMask(TMascara::maskDBDate);
        $VLRAQUISICAO = new TNumeric('VLRAQUISICAO', 2, ',', '.', true, true);
        $AREATOTAL = new TNumeric('AREATOTAL', 2, ',', '.', true, true);
        $QUADRAS = new TEntry('QUADRAS');
        $QUADRAS->setMask(TMascara::maskInt);
        $LOTES = new TEntry('LOTES');
        $LOTES->setMask(TMascara::maskInt);
        $OBS = new TText('OBS');
        $ATIVO = new TCombo('ATIVO');
        $ATIVO->addItems(['S'=>'Sim', 'N'=>'Não']);
        $ENDERECO = new TEntry('ENDERECO');
        $ENDERECO->forceUpperCase();
        $NUMERO = new TEntry('NUMERO');
        $NUMERO->setMask(TMascara::maskInt);
        $BAIRRO = new TEntry('BAIRRO');
        $BAIRRO->forceUpperCase();
        $COMPLEMENTO = new TEntry('COMPLEMENTO');
        $CEP = new TEntry('CEP');
        $CEP->setMask(TMascara::maskCEP);
        $UF = new TDBCombo('UF', 'gexpertlotes', 'Estados', 'CODIGO', '{SIGLA} / {NOME}');
        $UF->setChangeAction(new TAction([$this, 'onChangeUF']));
             $UF->enableSearch();
        $CIDADE = new TDBCombo('CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', 'NOME');
        $CIDADE->enableSearch();
        $AREALOTE = new TNumeric('AREALOTE', 3, ',', '.', true, true);
        $CUSTOLOTE = new TNumeric('CUSTOLOTE', 3, ',', '.', true, true);
        
        $filterPlano = new TCriteria();
        $filterPlano->add(new TFilter('EMPRESA', '=', TSession::getValue('userunitid')));   
         
        $CONTACTB = new TTr2DBSeekButton('CONTACTB', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTB', NULL , $this->criteria);
        $CONTACTB->setModelKey('CODIGO');
        $CONTACTB->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBRECEITA = new TTr2DBSeekButton('CONTACTBRECEITA', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBRECEITA', NULL , $this->criteria);
        $CONTACTBRECEITA->setModelKey('CODIGO');
        $CONTACTBRECEITA->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBCUSTO = new TTr2DBSeekButton('CONTACTBCUSTO', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBCUSTO', NULL , $this->criteria);
        $CONTACTBCUSTO->setModelKey('CODIGO');
        $CONTACTBCUSTO->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBDEVOLUCAO = new TTr2DBSeekButton('CONTACTBDEVOLUCAO', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBDEVOLUCAO', NULL , $this->criteria);
        $CONTACTBDEVOLUCAO->setModelKey('CODIGO');
        $CONTACTBDEVOLUCAO->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBINFRAESTRUTURA = new TTr2DBSeekButton('CONTACTBINFRAESTRUTURA', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBINFRAESTRUTURA', NULL , $this->criteria);
        $CONTACTBINFRAESTRUTURA->setModelKey('CODIGO');
        $CONTACTBINFRAESTRUTURA->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBPAGTO = new TTr2DBSeekButton('CONTACTBPAGTO', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBPAGTO', NULL , $this->criteria);
        $CONTACTBPAGTO->setModelKey('CODIGO');
        $CONTACTBPAGTO->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBATUALIZACAO = new TTr2DBSeekButton('CONTACTBATUALIZACAO', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBATUALIZACAO', NULL , $this->criteria);
        $CONTACTBATUALIZACAO->setModelKey('CODIGO');
        $CONTACTBATUALIZACAO->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBJUROS = new TTr2DBSeekButton('CONTACTBJUROS', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBJUROS', NULL , $this->criteria);
        $CONTACTBJUROS->setModelKey('CODIGO');
        $CONTACTBJUROS->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBRECEITAEVENTUAL = new TTr2DBSeekButton('CONTACTBRECEITAEVENTUAL', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBRECEITAEVENTUAL', NULL , $this->criteria);
        $CONTACTBRECEITAEVENTUAL->setModelKey('CODIGO');
        $CONTACTBRECEITAEVENTUAL->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBDEVOLUCAOPAGAR = new TTr2DBSeekButton('CONTACTBDEVOLUCAOPAGAR', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBDEVOLUCAOPAGAR', NULL , $this->criteria);
        $CONTACTBDEVOLUCAOPAGAR->setModelKey('CODIGO');
        $CONTACTBDEVOLUCAOPAGAR->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTBDEVOLUCAODRE= new TTr2DBSeekButton('CONTACTBDEVOLUCAODRE', 'gexpertlotes', 'frm'.__CLASS__, 'Plano', 'concat(CODIGO, CLASSIFICACAO, DESCRICAO)', 'CONTACTBDEVOLUCAODRE', NULL , $this->criteria);
        $CONTACTBDEVOLUCAODRE->setModelKey('CODIGO');
        $CONTACTBDEVOLUCAODRE->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');

        $INAUGURACAO    = new TDate('INAUGURACAO');
        $INAUGURACAO->setMask(TMascara::maskDate);
        $INAUGURACAO->setDatabaseMask(TMascara::maskDBDate);
        $TIPOCALC       = new TCombo('TIPOCALC');
        $TIPOCALC->addItems(['0'=> '[0] Tradicional - Infraestrutura x % recebido', 
                             '1'=> '[1] POC - Infraestrutura x custo orçado',
                             '2'=> '[2] POC - Medição da obra ou laudo de avaliação',
                             '3'=> '[3] POC - 100% Entrega das chaves']);
        $CUSTOORC       = new TNumeric('CUSTOORC', 2, ',', '.', true, true);
        $CUSTOINC       = new TNumeric('CUSTOINC', 2, ',', '.', true, true);
        $CUSTOINC->setEditable(FALSE);
        $PERANDAMENTO   = new TNumeric('PERANDAMENTO', 4, ',', '.', true, true);
        $PERANDAMENTO->setEditable(FALSE);
        $PERMEDICAO     = new TNumeric('PERMEDICAO', 4, ',', '.', true, true);
        $PERSUSPENSAO   = new TNumeric('PERSUSPENSAO', 4, ',', '.', true, true);
        $DIASSUSPENSAO  = new TEntry('DIASSUSPENSAO');
        $SUSPENSO       = new TCombo('SUSPENSO');   
        $SUSPENSO->addItems(['S'=>'Sim', 'N'=>'Nao']); 

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
        
        $CONTACTB->setAuxiliar($aux1);
        $CONTACTBRECEITA->setAuxiliar($aux2);
        $CONTACTBCUSTO->setAuxiliar($aux3);
        $CONTACTBDEVOLUCAO->setAuxiliar($aux4);
        $CONTACTBINFRAESTRUTURA->setAuxiliar($aux5);
        $CONTACTBPAGTO->setAuxiliar($aux6);
        $CONTACTBATUALIZACAO->setAuxiliar($aux7);
        $CONTACTBJUROS->setAuxiliar($aux8);
        $CONTACTBRECEITAEVENTUAL->setAuxiliar($aux9);
        $CONTACTBDEVOLUCAOPAGAR->setAuxiliar($aux10);
        $CONTACTBDEVOLUCAODRE->setAuxiliar($aux11);
         
         $CODIGO->setSize(TWgtSizes::wsInt);
         $EMPRESA->setSize(TWgtSizes::wsInt);
         $TIPO->setSize(TWgtSizes::wsDouble);
         $TIPOCALC->setSize(TWgtSizes::ws60);
         $DESCRICAO->setSize(TWgtSizes::ws60);
         $DATAAQUISICAO->setSize(TWgtSizes::wsDate);
         $INAUGURACAO->setSize(TWgtSizes::wsDate);
         $VLRAQUISICAO->setSize(TWgtSizes::wsDouble);
         $CUSTOORC->setSize(TWgtSizes::wsDouble);
         $CUSTOINC->setSize(TWgtSizes::wsDouble);
         $AREATOTAL->setSize(TWgtSizes::wsDouble);
         $AREALOTE->setSize(TWgtSizes::wsDouble);
         $CUSTOLOTE->setSize(TWgtSizes::wsDouble);
         $QUADRAS->setSize(TWgtSizes::wsInt);
         $LOTES->setSize(TWgtSizes::wsInt);
         $DIASSUSPENSAO->setSize(TWgtSizes::wsInt);
         $PERSUSPENSAO->setSize(TWgtSizes::wsInt);
         $PERANDAMENTO->setSize(TWgtSizes::wsInt);
         $PERMEDICAO->setSize(TWgtSizes::wsInt);
         $OBS->setSize(TWgtSizes::wsBlob);
         $ATIVO->setSize(TWgtSizes::wsBol);
         $SUSPENSO->setSize(TWgtSizes::wsBol);
         $ENDERECO->setSize(TWgtSizes::ws60);
         $NUMERO->setSize(TWgtSizes::wsInt);
         $BAIRRO->setSize(TWgtSizes::ws35);
         $COMPLEMENTO->setSize(TWgtSizes::ws30);
         $CEP->setSize(TWgtSizes::wsCEP);
         $UF->setSize(TWgtSizes::ws35);
         $CIDADE->setSize(TWgtSizes::ws50);
         
         
         $CONTACTB->setSize(TWgtSizes::wsInt);
         $CONTACTBRECEITA->setSize(TWgtSizes::wsInt);
         $CONTACTBCUSTO->setSize(TWgtSizes::wsInt);
         $CONTACTBDEVOLUCAO->setSize(TWgtSizes::wsInt);
         $CONTACTBINFRAESTRUTURA->setSize(TWgtSizes::wsInt);
         $CONTACTBPAGTO->setSize(TWgtSizes::wsInt);
         $CONTACTBATUALIZACAO->setSize(TWgtSizes::wsInt);
         $CONTACTBJUROS->setSize(TWgtSizes::wsInt);
         $CONTACTBRECEITAEVENTUAL->setSize(TWgtSizes::wsInt);
         $CONTACTBDEVOLUCAOPAGAR->setSize(TWgtSizes::wsInt);
         $CONTACTBDEVOLUCAODRE->setSize(TWgtSizes::wsInt);
         
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
         
        if (!empty($CODIGO))
        {
            $CODIGO->setEditable(FALSE);
        }
        
        // actions fields
        $calcLote = new TAction([$this, 'calcLote']);
        $AREATOTAL->setExitAction($calcLote);
        $VLRAQUISICAO->setExitAction($calcLote);
        $LOTES->setExitAction($calcLote);
        $QUADRAS->setExitAction($calcLote);

        $btAqsParc = new TButton('btAqsParc');
        $btAqsParc->{'style'} = "margin-left: -11px; border-radius: 0";
        $btAqsParc->{'title'} = 'Parcelamento da entrada';
        $btAqsParc->setAction(new TAction([$this, 'onParcelasAquisicao'], ['static'=>'1']), "<i class='fa fa-search-dollar'></i>");
        
        // master fields
        $this->form->appendPage('Geral');
        $this->form->addFields( [new TLabel('Codigo')], [$CODIGO] );
        $this->form->addFields( [new TLabel('Empresa')], [$EMPRESA] );
        $this->form->addFields( [new TLabel('Tipo')], [$TIPO] );
        $this->form->addFields( [new TLabel('Descricao')], [$DESCRICAO] );
        $this->form->addFields( [new TLabel('Data aquisição*', 'red')], [$DATAAQUISICAO] );
        $this->form->addFields( [new TLabel('Vlr aquisição*', 'red')], [$VLRAQUISICAO, $btAqsParc] );
        $this->form->addFields( [new TLabel('Area total*', 'red')], [$AREATOTAL] );
        $this->form->addFields( [new TLabel('Tipo apuração*', 'red')], [$TIPOCALC] );
        $this->form->addFields( [new TLabel('Data inauguração*', 'red')], [$INAUGURACAO] );
        $this->form->addFields( [new TLabel('Vlr custo orçado*', 'red')], [$CUSTOORC] );
        $this->form->addFields( [new TLabel('Vlr custo incorrido')], [$CUSTOINC] );
        $this->form->addFields( [new TLabel('% Andamento da obra')], [$PERANDAMENTO] );
        $this->form->addFields( [new TLabel('% Medição da obra*', 'red')], [$PERMEDICAO] );
        $this->form->addFields( [new TLabel('% Clausula suspensiva')], [$PERSUSPENSAO] );
        $this->form->addFields( [new TLabel('Nr dias suspensão')], [$DIASSUSPENSAO] );
        $this->form->addFields( [new TLabel('Suspenso')], [$SUSPENSO] );
        $this->form->addFields( [new TLabel('Quadras/Andares*', 'red')], [$QUADRAS] );
        $this->form->addFields( [new TLabel('Lotes/Aptos*', 'red')], [$LOTES] );
        $this->form->addFields( [new TLabel('Area lote/apto*', 'red')], [$AREALOTE] );
        $this->form->addFields( [new TLabel('Custo lote/apto*', 'red')], [$CUSTOLOTE] );
        $this->form->addFields( [new TLabel('Obs')], [$OBS] );
        $this->form->addFields( [new TLabel('Ativo')], [$ATIVO] );
        
        $VLRAQUISICAO->addValidation('Vlr aquisicao', new TMinValueValidator, array(1));
        $AREATOTAL->addValidation('Area total', new TMinValueValidator, array(1));
        $QUADRAS->addValidation('Quadras/Andares', new TMinValueValidator, array(1));
        $LOTES->addValidation('Lotes/Aptos', new TMinValueValidator, array(1));
        $AREALOTE->addValidation('Area lote/apto', new TMinValueValidator, array(1));
        $CUSTOLOTE->addValidation('Custo lote/apto', new TMinValueValidator, array(1));
        $TIPOCALC->addValidation('Tipo calculo', new TRequiredValidator);

        $this->form->appendPage('Endereco'); 
        $this->form->addFields( [new TLabel('Endereco')], [$ENDERECO] );
        $this->form->addFields( [new TLabel('Numero')], [$NUMERO] );
        $this->form->addFields( [new TLabel('Bairro')], [$BAIRRO] );
        $this->form->addFields( [new TLabel('Complemento')], [$COMPLEMENTO] );
        $this->form->addFields( [new TLabel('Cep')], [$CEP] );
        $this->form->addFields( [new TLabel('Uf')], [$UF] );
        $this->form->addFields( [new TLabel('Cidade')], [$CIDADE] );
        
        $this->form->appendPage('Contábil');         
        $this->form->addFields( [new TLabel('Conta ctb')], [$CONTACTB] );
        $this->form->addFields( [new TLabel('Conta ctb receita')], [$CONTACTBRECEITA] );
        $this->form->addFields( [new TLabel('Conta ctb custo')], [$CONTACTBCUSTO] );
        $this->form->addFields( [new TLabel('Conta ctb devolucao')], [$CONTACTBDEVOLUCAO] );
        $this->form->addFields( [new TLabel('Conta ctb infraestrutura')], [$CONTACTBINFRAESTRUTURA] );
        $this->form->addFields( [new TLabel('Conta ctb pagto')], [$CONTACTBPAGTO] );
        $this->form->addFields( [new TLabel('Conta ctb atualizacao')], [$CONTACTBATUALIZACAO] );
        $this->form->addFields( [new TLabel('Conta ctb juros')], [$CONTACTBJUROS] );
        $this->form->addFields( [new TLabel('Conta ctb receita eventual')], [$CONTACTBRECEITAEVENTUAL] );
        $this->form->addFields( [new TLabel('Conta ctb devolucao a pagar')], [$CONTACTBDEVOLUCAOPAGAR] );
        $this->form->addFields( [new TLabel('Conta ctb devolucao DRE')], [$CONTACTBDEVOLUCAODRE] );
        
        
        $this->form->appendPage('Lotes / Apartamentos');
        // detail fields
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGridScroll);
        $this->detail_list->setId('Empreendimentos_list');
        $this->detail_list->style ="width: 100%"; 
        $this->detail_list->setHeight(480);

        $criteria = new TCriteria;
        $this->addSubClass('Lotes', 'Empreendimentos', 'CODIGO', 'EMPREENDIMENTO', $criteria);
        $this->addSubDatagrid('Lotes', $this->detail_list);
        
        // items
        $EMPRESA = $this->detail_list->addQuickColumn('Empresa', 'EMPRESA', 'left', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'ATIVO']));
        $this->detail_list->addQuickColumn('Quadra/Andar', 'QUADRA', 'left', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'QUADRA']));
        $this->detail_list->addQuickColumn('Codigo/Apto', 'CODIGO', 'left', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'CODIGO']));
        $DESMEMBRAMENTO = $this->detail_list->addQuickColumn('Desmemb', 'DESMEMBRAMENTO', 'left', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'DESMEMBRAMENTO']));
        $AREA  = $this->detail_list->addQuickColumn('Area(MTS)', 'AREA', 'left', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'AREA']));
        $CUSTO = $this->detail_list->addQuickColumn('Vlr custo(R$)', 'VLRCUSTO', 'left', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'VLRCUSTO']));
        $SITUACAO = $this->detail_list->addQuickColumn('Situacao', 'SITUACAO', 'center', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'SITUACAO']));
        $ATIVO = $this->detail_list->addQuickColumn('Ativo', 'ATIVO', 'center', 180, new TAction([$this, 'onSubReorder'],['static'=>'1', 'reset'=>1, 'status'=>'browse', 'subclass'=>'Lotes', 'order'=>'ATIVO']));
        
        $DESMEMBRAMENTO->setTransformer($asDate);
        $AREA->setTransformer($asCur);
        $CUSTO->setTransformer($asCur);
        $ATIVO->setTransformer($asBol);
        $SITUACAO->setTransformer($asSituacao);
        
        $AREA->setTotalFunction($sum2);         
        $CUSTO->setTotalFunction($sum);         
        $EMPRESA->setTotalFunction($count);
 
        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction(['LotesForm', 'onEdit']),   'ID', 'far:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'ID', 'far:trash-alt red');

        $this->detail_list->createModel();
        
        $searchQuadra          = new TEntry('searchQuadra');
        $searchLote            = new TEntry('searchLote');
        $searchDesmembramento  = new TDate('searchDesmembramento');
        $searchArea            = new TEntry('searchArea');
        $searchCusto           = new TEntry('searcCusto');
        $searchSituacao        = new TCombo('searchSituacao');
        
        $searchArea->style='width:100%';
        $searchCusto->style='width:100%';
        $searchDesmembramento->style='width:100%';
        $searchLote->style='width:100%';
        $searchQuadra->style='width:100%';
        $searchSituacao->style='width:100%';

        $searchArea->placeholder = 'Area em metros...';
        $searchCusto->placeholder = 'Vlr Custo...';
        $searchDesmembramento->placeholder = 'Dt desmembramento...';
        $searchLote->placeholder = 'Nr lote...';
        $searchQuadra->placeholder = 'Nr quadra...';
        $searchSituacao->placeholder = 'Situacao...';
        
        $searchArea->tabindex=-1;
        $searchCusto->tabindex=-1;
        $searchLote->tabindex=-1;
        $searchDesmembramento->tabindex=-1;
        $searchQuadra->tabindex=-1;
        $searchSituacao->tabindex=-1;
        
        $this->form->addField($searchArea);
        $this->form->addField($searchCusto);
        $this->form->addField($searchDesmembramento);
        $this->form->addField($searchLote);
        $this->form->addField($searchQuadra);
        $this->form->addField($searchSituacao);
                
        $searchSituacao->addItems(['0'=>'Aberto', '1'=>'Vendido', '2'=>'Devolvido', '3'=>'Renegociado', '4'=>'Revendido']); 
        $searchDesmembramento->setMask('dd/mm/yyyy');
        
        $this->addSubFilter('Lotes', 'QUADRA', '=', 'searchQuadra');
        $this->addSubFilter('Lotes', 'CODIGO', '=', 'searchLote');
        $this->addSubFilter('Lotes', 'DESMEMBRAMENTO', '=', 'searchDesmembramento', $asDateSQL);
        $this->addSubFilter('Lotes', 'AREA', 'like', 'searchArea', $asCurSQL);
        $this->addSubFilter('Lotes', 'VLRCUSTO', 'like', 'searchCusto', $asCurSQL);
        $this->addSubFilter('Lotes', 'SITUACAO', '=', 'searchSituacao');
        
        $searchQuadra->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>1, 'subclass'=>'Lotes']));
        $searchLote->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>1, 'subclass'=>'Lotes']));
        $searchDesmembramento->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>1, 'subclass'=>'Lotes']));
        $searchArea->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>1, 'subclass'=>'Lotes']));
        $searchCusto->setExitAction(new TAction([$this, 'onSubSearch'], ['static'=>1, 'subclass'=>'Lotes']));
        $searchSituacao->setChangeAction(new TAction([$this, 'onSubSearch'], ['static'=>1, 'subclass'=>'Lotes']));
        $TIPOCALC->setChangeAction(new TAction([$this, 'onChangeTipoCalc']));

        $tr = new TElement('tr');
        $tr->{'style'} = 'display: inline-table; width: calc(100% - 20px);';
        $tr->add($td = TElement::tag('td', ''));
        $td->style='width:28px';
        $tr->add($td = TElement::tag('td', ''));
        $td->style='width:28px'; 
        $tr->add($td = TElement::tag('td', ''));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', $searchQuadra));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', $searchLote));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', $searchDesmembramento));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', $searchArea));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', $searchCusto));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', $searchSituacao));
        $td->style='width:200px';
        $tr->add($td = TElement::tag('td', ''));
        $td->style='width:200px';
        
        $this->detail_list->getHead()->add($tr);

        $panel = new TPanelGroup;
        
        $panel->add($this->detail_list);
       
        $addLote = new TActionLink('Novo Lote', new TAction(['LotesForm', 'onEdit']), null, null, null, 'fa:plus blue');
        $addLote->class='btn btn-default btn-sm';
        
        $addLotes = new TActionLink('Inserir Lotes em Massa', new TAction([$this, 'onGeraLotes'], ['static'=>'1']), null, null, null, 'fa:play green');
        $addLotes->class='btn btn-default btn-sm';
        
        $delLotes = new TActionLink('Excluir Todos os Lotes', new TAction([$this, 'onExcluirLotes'], ['static'=>'1']), null, null, null, 'fas:trash-alt red');
        $delLotes->class='btn btn-default btn-sm';
       
     //   $dropdown_lotes = new TDropDown(_t('Export'), 'fa:list');
    //    $dropdown_lotes->addAction(_('Save as CSV'), new TAction([$this, 'onExportSubCSV'],['static'=>'1', 'subclass'=>'Lotes']), 'fa:table fa-fw blue'); 
        
    //    $panel->addHeaderWidget($dropdown_lotes);
        $panel->addHeaderActionLink(_t('Clear Filters'), new TAction([$this, 'clearSubFilters'],['static'=>'1', 'subclass'=>'Lotes', 'reset'=>1]), 'fa:eraser red');
        
        $panel->addFooter('');
        $panel->getFooter()->add($addLote);
        $panel->getFooter()->add($addLotes);
        $panel->getFooter()->add($delLotes);       
        $panel->getBody()->style = 'width:100%;overflow-x:auto';
        
        $this->form->addContent( [$panel] );
        

        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction( _t('Back'), new TAction(['EmpreendimentosList', 'onReload']), 'far:arrow-alt-circle-left blue');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'overflow-x: auto';
        $container->add(TXMLBreadCrumb::create(['Empreendimentos', 'Cadastro de Empreendimentos']));
        $container->add($this->form);
        
        parent::add($container);

        $record = Empreendimentos::findInTransaction($this->database, isset($param['key'])? $param['key']: null);
        
        self::onChangeTipoCalc($record? $record->toArray(): ['TIPOCALC'=>'']); 
    }

    public static function onChangeTipoCalc($param)
    {
        switch( $param['TIPOCALC'] ) {
            case '0': {
                TQuickForm::hideField('frm'.__CLASS__, 'INAUGURACAO');
                TQuickForm::hideField('frm'.__CLASS__, 'CUSTOORC');
                TQuickForm::hideField('frm'.__CLASS__, 'CUSTOINC');
                TQuickForm::hideField('frm'.__CLASS__, 'PERANDAMENTO');
                TQuickForm::hideField('frm'.__CLASS__, 'PERMEDICAO');                
            };
            break;
            case '1': {
                TQuickForm::showField('frm'.__CLASS__, 'INAUGURACAO');
                TQuickForm::showField('frm'.__CLASS__, 'CUSTOORC');
                TQuickForm::showField('frm'.__CLASS__, 'CUSTOINC');
                TQuickForm::showField('frm'.__CLASS__, 'PERANDAMENTO');
                TQuickForm::hideField('frm'.__CLASS__, 'PERMEDICAO'); 
            };
            break;
            case '2': {
                TQuickForm::showField('frm'.__CLASS__, 'INAUGURACAO');
                TQuickForm::showField('frm'.__CLASS__, 'CUSTOORC');
                TQuickForm::hideField('frm'.__CLASS__, 'CUSTOINC');
                TQuickForm::hideField('frm'.__CLASS__, 'PERANDAMENTO');
                TQuickForm::showField('frm'.__CLASS__, 'PERMEDICAO'); 
            };
            break;
            default: {
                TQuickForm::showField('frm'.__CLASS__, 'INAUGURACAO');
                TQuickForm::showField('frm'.__CLASS__, 'CUSTOORC');
                TQuickForm::showField('frm'.__CLASS__, 'CUSTOINC');
                TQuickForm::showField('frm'.__CLASS__, 'PERANDAMENTO');
                TQuickForm::showField('frm'.__CLASS__, 'PERMEDICAO'); 
            } 
            break;
        }
    }

    public function onParcelasAquisicao($param) 
    {
        TScript::create('alert("clicou")');
    }   
        
    public function onDeleteDetail( $param )
    {      
        if (isset($param['key']))
        {
            TTransaction::open('gexpertlotes');
            $key = $param['key'];
            $obj = new Lotes($key);
            $obj->delete();
            TTransaction::close();
        
            $detail_id = $param['key'];
        
         // delete item from screen
         TScript::create("ttable_remove_row_by_id('Empreendimentos_list', '{$detail_id}')");
       } 
    }        

    function onBeforeSave($object)
    {
        if(($object->TIPOCALC == '1' or $object->TIPOCALC == '2') && ( $object->CUSTOORC<=0 or empty($object->INAUGURACAO)))
        {
            throw new Exception("A T E N Ç Ã O ! <br><br> Para cálculo do tipo <b>POC</b> os campos abaixo devem ser informados: <br><hr> - Data inauguração<br> - Custo orçado<br>");

            exit;
        } 
        
        return $object;
    }

    public function fireEvents($object)
    {        
        $object->DATAAQUISICAO = TDateTime::convertToMask($object->DATAAQUISICAO, 'yyyy-mm-dd', 'dd/mm/yyyy');
        return $object;
    }
      
    public static function onChangeUF($param)
    {
        if(isset($param['UF']))
        {
            $filter = new TCriteria();
                $filter->add(new TFilter('UF', '=', $param['UF']));
            
            TDBCombo::reloadFromModel('form_Empreendimentos', 
                                      'CIDADE',
                                      'gexpertlotes', 
                                      'Municipios', 
                                      'CODIGO', 
                                      'NOME', 
                                      'NOME', 
                                      $filter
                      ); 
        }
    }
    
    public static function calcLote($param)
    {   
        $lotes     = TConversion::asInt($param['LOTES']);
        $quadras   = TConversion::asInt($param['QUADRAS']);
        $areatotal = TConversion::asDouble($param['AREATOTAL'],2);
        $vlraqs    = TConversion::asDouble($param['VLRAQUISICAO'],2);
        
        if (($lotes>0) & ($quadras>0) & ($areatotal>0) & ($vlraqs>0))
        {
            $arealote  = TConversion::asDoubleBR($areatotal/$quadras/$lotes, 2);
            $custolote = TConversion::asDoubleBR($vlraqs/$quadras/$lotes, 2);         
           
            $obj = new StdClass();
            $obj->AREALOTE  = $arealote;
            $obj->CUSTOLOTE = $custolote;       
           
            TForm::sendData('frm'.__CLASS__, $obj);  
        }
                
    }
    
    public function onGeraLotes($param)
    {
        $fmGeraLotes = new TQuickForm('fmGeraLotes');
        $fmGeraLotes->setFormTitle('Gerar Lotes / Aptos');
        $fmGeraLotes->style = 'padding:30px';
                
        $DESMEMB  = new TDate('desmemb');
        $QUADRA   = new TEntry('quadra');
        $NUMLOTES = new TEntry('numlotes');
        $AREA     = new TEntry('area');
        $CUSTO    = new TEntry('custo');
        
        $DESMEMB->setSize(TWgtSizes::wsDate);
        $QUADRA->setSize(TWgtSizes::wsInt);
        $NUMLOTES->setSize(TWgtSizes::wsInt);
        $AREA->setSize(TWgtSizes::wsDouble);
        $CUSTO->setSize(TWgtSizes::wsDouble);
        
        $DESMEMB->setMask(TMascara::maskDate);
        $DESMEMB->setDatabaseMask(TMascara::maskDBDate);
        $QUADRA->setMask(TMascara::maskInt);
        $NUMLOTES->setMask(TMascara::maskInt);
        $AREA->setNumericMask(2,',','.', TRUE);
        $CUSTO->setNumericMask(2,',','.', TRUE);
        
        $fmGeraLotes->addQuickField(new TLabel('<b>Dt. Desmembramento</b>'), $DESMEMB, TWgtSizes::wsDate);
        $fmGeraLotes->addQuickField(new TLabel('<b>Núm Quadra / Andar</b>'), $QUADRA, TWgtSizes::wsInt);
        $fmGeraLotes->addQuickField(new TLabel('<b>Qtd Lotes / Aptos</b>'), $NUMLOTES, TWgtSizes::wsInt);
        $fmGeraLotes->addQuickField(new TLabel('<b>Área (Mts)</b>'), $AREA, TWgtSizes::wsDouble);
        $fmGeraLotes->addQuickField(new TLabel('<b>Vlr. Custo Un (R$)</b>'), $CUSTO, TWgtSizes::wsDouble);
        
        $fmGeraLotes->addQuickAction('Executar / Gerar',  new TAction([$this, 'GeraLotes'], ['static'=>'1']), 'fa:play green');  
        
        new TInputDialog('', $fmGeraLotes);    
    }
    
    public function GeraLotes($param)
    {
        if(isset($param['desmemb'])&isset($param['quadra'])&isset($param['numlotes'])&isset($param['area'])&isset($param['custo']))
        {
            TTransaction::open($this->database);
            
            $con = TTransaction::get();
            
            $sql = $con->prepare('call sp_gera_lotes(?,?,?,?,?,?,?)');
            
            $fmtArea   = TConversion::asDouble($param['area']);
            $fmtCusto  = TConversion::asDouble($param['custo']);          
            
            $sql->execute([ 
                           $this->getSubMasterId(),
                           TConversion::asSQLDate(($param['desmemb'])),
                           $param['quadra'],
                           $param['numlotes'],
                           TSession::getValue('userid'),
                           $fmtArea,
                           $fmtCusto] );
            
            TTransaction::close();
            
            new TMessage('info', 'Comando Executado com Sucesso!', new TAction([$this, 'onEdit'],['status'=>'browse', 'reset'=>1]));    
        }
               
    }
    
    public function onExcluirLotes($param = NULL)
    {
        new TQuestion('Deseja Realmente Excluir todos os Lotes?', new TAction([$this, 'ExcluirLotes'],['static'=>'1']));
    }
    
    function ExcluirLotes($param = NULL)
    {
        TTransaction::open($this->database);
        
        $con = TTransaction::get();
        
        $sql = $con->prepare('delete from lotes where empreendimento = ?');
        $sql->execute([$this->getSubMasterId()]);
        
        TTransaction::close();
        
        new TMessage('info', 'Comando Executado com Sucesso!', new TAction([$this, 'onEdit'],['status'=>'browse', 'reset'=>1])); 
    } 
    

}


