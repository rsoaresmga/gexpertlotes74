<?php

/************************************************************************************
 * Autor     : Rodrigo Soares
 * Criado em : 19/11/2020
 * Versão    : 1.0.0.0
 * Framework : 7.2.0 
 * 
 * Objetivo:
 *  Gerar dinamica as Grid Filhas, adicionando Filtragem e Ordenação   
 *
 * Obs:
 *  -Para executar essa Trait é obrigatório 
 *  passar os valores no __construct do Formulario para
 *  "setDatabase(database a ser usada)" e "setActiveRecord(Classe Master do formulario)".
 *  
 *  -É obrigatorio declarar no método de Reload da tela(ou por padrao onEdit)
 *   a funcao "setActiveRecordID" pois ela armazena o ID do Master no caso de Edicao e 
 *   visualizacao.
 *
 *  -É obrigatório declarar em todas as funcoes com $param o item 
 * detail_class(Nome da classe Filha)  
 *    
 ************************************************************************************/
    
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TRecord;
use Adianti\Database\TFilter;
use Adianti\Database\TExpression;
use Adianti\Database\TCriteria;
use Adianti\Registry\TSession;
use Adianti\Widget\Datagrid\TDataGrid;

//use DomDocument;
use Dompdf\Dompdf;
    
trait Tr2CollectionUtilsTrait
{
    protected $subMasterClass;
    protected $subMasterKey;
    protected $subClass;
    protected $subForeignkey;
    protected $subFilterFields;
    protected $subFilterControls;
    protected $subFilterOperators;
    protected $subFilterLogicOperators;
    protected $subFilterTransformers;
    protected $subLimit;
    protected $subOffset;
    protected $subOrderDefault;
    protected $subDirectionDefault;
    protected $subOrderCommands;
    protected $subCriteria;
    protected $subCriteriaFilter;
    protected $subFieldKey;
    protected $subDatagrid;  

    use Adianti\Base\AdiantiStandardFormTrait;
    use Tr2FormUtilsTrait; 
    
    /* Método usado para registrar a Classe pai e filha bem como suas chaves de relacionamento 
     *
     * @param $class       String    : Nome da classe Filha a ser carregada nos detalhes
     * @param $master      String    : Nome da classe Master que servirá de base
     * @param $master_key  String    : Campo chave da tabela Master
     * @param $foreign_key String    : Campo referente a tabela Master dentro da tabela Filha
     * @param $criteria    TCriteria : Variavel com criterios SQL adicionais 
     */
    function addSubClass($class, $master, $master_field, $foreign_key, $criteria = null)
    {        
        $this->subClass[]              = $class;
        $this->subMasterClass[$class]  = $master;
        $this->subMasterKey[$class]    = $master_field;
        $this->subForeignkey[$class]   = $foreign_key;
        $this->setSubCriteria($class, $criteria);      
    }
    
    /* Define o datagrid usado pela classe Filha
     * @param $class    String    : Nome da classe Filha
     * @param $datagrid TDataGrid : Datagrid que exibe a classe Filha.
     */
    function addSubDataGrid($class, $datagrid)
    {
        $this->subDatagrid[$class] = $datagrid;
    }
    
    /* Método usado para vincular os campos do formulario com os campos da tabela filha
     *
     * @param $class           String : Nome da classe filha a ser carregada nos detalhes
     * @param $field           String : Nome do campo no banco de dados
     * @param $operador        String : Operador de atribuicao SQL
     * @param $control         String : Nome do campo do formulario que enviara os dados para busca
     * @param $logic_operator  String : Operador de juncao SQL;
     */
    function addSubFilter($class, $field, $operator = 'like', $control, $transformer = null, $logic_operator = TExpression::AND_OPERATOR)
    {
         if (!isset($this->subClass))
         {
             $this->subClass = [];
         }
         
         if(!array_key_exists($class, $this->subClass))
         {
             new Exception("A classe '{$class}' não definida! \n Utilize addClassDetail para definir.");
         }
         
         $this->subFilterFields[$class][]                = $field;
         $this->subFilterControls[$class][$field]        = $control;
         $this->subFilterOperators[$class][$field]       = $operator;  
         $this->subFilterLogicOperators[$class][$field]  = $logic_operator; 
         $this->subFilterTransformers[$class][$field]    = $transformer;                  
    }
    
    /* Método usado para alimentar as variaveis de sessao com os valores dos filtros 
     * e também recarregar os campos após o post
     * @return TRepository
     *
     * @param $param Array:
     *        -subclass         String : Classe filha que será filtrada (Obrigatório);
     *        -masterkey       Integer : Valor do ID da tabela Pai (Opcional);
     *        -trigger          String : Método onde está sendo chamada a function onReloadDetail() caso nao informado o 'onEdit' é assumido (Opcional);          
     */
    function onSubSearch($param)
    {
        TTransaction::open($this->database);

        $class            = $param['subclass']; 
        $masterkey        = isset($param['masterkey'])? $param['masterkey']: $this->getSubMasterID($class);
        $trigger          = isset($param['trigger'])? $param['trigger']: 'onEdit';   
        $master           = $this->subMasterClass[$class];
        $masterfield      = $this->subMasterKey[$class];    
        $foreignkey       = $this->subForeignkey[$class];
        $criteria         = !is_null($this->getSubCriteria($class))? clone $this->getSubCriteria($class): $this->setSubCriteriaFilter($class, new TCriteria);

        $data = $this->form->getData();

        $criteria->add(new TFilter($foreignkey, '=', $masterkey)); 
        
        foreach($this->subFilterFields[$class] as $i=>$field)
        {
            $operator              = isset($this->subFilterOperators[$class][$field])? $this->subFilterOperators[$class][$field]: null;
            $control               = isset($this->subFilterControls[$class][$field])? $this->subFilterControls[$class][$field]: null;
            $logic_operator        = isset($this->subFilterLogicOperators[$class][$field])? $this->subFilterLogicOperators[$class][$field]: null; 
            $value                 = (isset($data->{$control}))? $data->{$control}: null;
            $transformer_function  = (isset($this->subFilterTransformers[$class][$field]))? $this->subFilterTransformers[$class][$field]: null; 

            if (!empty($value) or $value=='0')
            {
                if(isset($transformer_function))
                {
                    $value = $transformer_function($value);
                }
               
                if(stristr($operator, 'like'))
                {
                    $value = "%{$value}%";
                }
                

                $criteria->add($filter = new TFilter($field, $operator, $value));
                
                TSession::setValue("sub_{$class}_{$control}", $data->{$control});
            }
            else 
            {
                 TSession::setValue("sub_{$class}_{$control}", null);  
            }                 
        } 
        
        $this->setSubCriteriaFilter($class, $criteria);
        
        $repository = new TRepository($class);
        
        $objects = $repository->load($criteria, false);              
        
        TTransaction::close();

        AdiantiCoreApplication::loadPage(__CLASS__, $trigger, ['status'=>'browse', 'key'=>$masterkey]); 

        return $objects; 
        
    }
    
    /* Gera TCriteria em Sessao para ordenar as tabelas Filhas no onReloadDetail
     * return TCriteria
     *
     * @param $param Array 
     *       -subclass       String  : Nome da classe filha(Obrigatorio)  
     *       -trigger        String  : Método que chama onReloadDetail no form caso vazio assume onEdit(Opcional)
     *       -masterkey      Integer : Valor do ID da tabela Pai (Opcional);
     *       -offset         Integer : Mostrar linhas a partir de OffSet (opcional) 
     *       -direction      String  : Asc ou Desc (Opcional)  
     *       -order          String  : Campo da tabela Filha a ser ordenado (Obrigatorio)                      
     */
    
    function onSubReorder($param)
    {
        TTransaction::open($this->database);

        $class            = $param['subclass']; 
        $masterkey        = isset($param['masterkey'])                   ? $param['masterkey']                      : $this->getSubMasterId($class);
        $trigger          = isset($param['trigger'])                     ? $param['trigger']                        : 'onEdit';   
        $limit            = isset($this->subLimit[$class])               ? $this->subLimit[$class]                  : null;         
        $offset           = isset($param['offset'])                      ? $param['offset']                         : null;
        $order            = isset($param['order'])                       ? $param['order']                          : null;
        $criteria         = !is_null($this->getSubCriteriaFilter($class))? clone $this->getSubCriteriaFilter($class): $this->getSubCriteria($class);
        $direction        = ($criteria->getProperty('direction'))        ? $criteria->getProperty('direction')      : 'asc';

        if(!empty($order))
        {
            if($limit)
            {
                $criteria->setProperty('limit', $this->subLimit[$class]);
            }
            
            if($offset)
            {
                $criteria->setProperty('offset', $offset);
            }       
            
            if ( isset($this->subOrderCommands[$class]) && ($order) && ($this->detail_order_commands[$class][$order]) )
            {
                $order = $this->subOrderCommands[$class][$order];        
            }
            
            if($order)
            {
                if(isset($this->subFilterTransformers[$class])&&isset($this->subFilterTransformers[$class][$order]))
                { 
                     $transformer_function = $this->subFilterTransformers[$class][$order];
                     
                     $order = $transformer_function($order);   
                }
                
                $criteria->setProperty('order', $order);     
            } 
            
            if($direction)
            {            
                if ($criteria->getProperty('direction')=='asc')
                {
                   $direction = 'desc';  
                } 
                else
                {
                    $direction = 'asc';
                }
                
                $criteria->setProperty('direction', $direction);
            }       
   
            $this->setSubCriteriaFilter($class, $criteria);
                        
            TTransaction::close();
    
            AdiantiCoreApplication::loadPage(__CLASS__, $trigger, ['status'=>'browse', 'key'=>$masterkey]);
        }
        
        return $criteria;
                    
    }
    
    /* Método usado para executar/carregar a filtragem da classe filha
     * return TRepository
     *
     * @param $param Array:
     *        -subclass String : Nome da classe filha (Obrigatório).
     *        -reset    String : Se esse valor existir o método clearSubFilters será executo (Opcional)
     *
     */
    function onSubReload($param)
    {
        $class = $param['subclass'];
        
        if ( !isset($param['reset']) )
          { 
               TTransaction::open($this->database);
        
               $data = new stdClass;
                
               if(isset($this->subFilterControls[$class]))
               {
                   foreach($this->subFilterControls[$class] as $control)
                   {
                       $data->$control = TSession::getValue("sub_{$class}_{$control}");
                   }    
               }
                              
               TForm::sendData($this->form->getName(), $data, null, false);
               
               $repository = new TRepository($class);
               
               $criteria = !is_null($this->getSubCriteriaFilter($class))? clone $this->getSubCriteriaFilter($class): $this->getSubCriteria($class);

               $objects = $repository->load($criteria);
               
               TTransaction::close();            
        
               return $objects;
           }
           else
           {
               return $this->clearSubFilters($param);                  
           }  
    } 
    
    /* Método usado para limpar a filtragem da classe filha deixando apenas a 
     * foreign_key com valor da classe pai. 
     * return TRepository
     *
     * @param $param Array 
     *        -class        String : Nome da classe filha (Obrigatório).
     *        -clear_detail String : Valor que define se o método será executado ou não (Opcional).
     */
    function clearSubFilters($param)
    {
        $class   = $param['subclass'];
        $trigger = (isset($param['trigger']))? $param['trigger']: 'onEdit'; 
       
        TTransaction::open($this->database);
        
        $masterkey        = isset($param['masterkey'])? $param['masterkey']: $this->getSubMasterID($class);
        $foreignkey       = $this->subForeignkey[$class];
        $masterfield      = $this->subMasterKey[$class];                                   

        $data = (object) $param;        

        $criteria = (is_a($this->getSubCriteria($class), 'TCriteria') )? clone $this->getSubCriteria($class): new TCriteria;
        
        $criteria->add(new TFilter($foreignkey, '=', $masterkey));

        $data = new stdClass;
         
        if(isset($this->subFilterControls[$class]))
        {
            foreach($this->subFilterControls[$class] as $control)
            {
               $data->$control = "";
               
               TSession::setValue("sub_{$class}_{$control}", null);
            }    
        }
                        
        TForm::sendData($this->form->getName(), $data, false, false);
        
        if( is_array($this->subOrderDefault) && isset($this->subOrderDefault[$class]) )
        {
            $order = $this->subOrderDefault[$class];
        }
        else
        {
            $order = '0';
        } 
        
        if( is_array($this->subDirectionDefault) && isset($this->subDirectionDefault[$class]) )
        {
            $direction = $this->subDirectionDefault[$class];
        } 
        else
        {
            $direction = 'asc';
        } 
        
        if(isset($this->subLimit[$class]))
        {
            $limit = $this->subLimit[$class];
        }
        
        if(isset($this->subOffset[$class]))
        {
            $offset = $this->subOffset[$class];
        }
                    
        if (isset($order_default)) { $criteria->setProperty('order', $order); }
        if (isset($direction_default)) { $criteria->setProperty('direction', $direction); }
        if (isset($limit)) { $criteria->setProperty('limit', $limit); }
        if (isset($offset)) { $criteria->setProperty('offset', $offset); }
        
        $this->setSubCriteriaFilter($class, $criteria);
 
        $repository = new TRepository($class);
        $objects = $repository->load($criteria, false);              
        
        TTransaction::close();            
        
        if (isset($param['static']) and $param['static']=='1')
        {
            AdiantiCoreApplication::loadPage(__CLASS__, $trigger, ['status'=>'edit', $masterfield=>$masterkey]);
        }
        
        return $objects;     

   } 
   
   /* Adiciona comandos de ordenacao(subselects) a Classe instanciada 
    * 
    * @param $class    String : Nome da class Filha (Obrigatorio)
    * @param $field    String : Nome da coluna(alias) (Obrigatorio)
    * @param $command  String : Comando Select (Obrigatorio)
    */
   function addSubOrderCommand($class, $field, $command)
   {
       $this->subOrderCommands[$class][$field] = $command;
   }
   
   /* Adiciona ordenacao padrao a Classe instanciada 
    * 
    * @param $class    String : Nome da class Filha (Obrigatorio)
    * @param $field    String : Nome das colunas a serem ordenadas (Obrigatorio)
    * @param $command  String : Direcao da ordenacao (Opcional)
    */
   function addSubDefaultOrder($class, $fields, $direction = 'asc')
   {
       $this->subOrderDefault[$class]     = $fields;
       $this->subDirectionDefault[$class] = $direction;     
   }
   
   /* Adiciona Criteria adicional a Classe instanciada 
    * return TCriteria 
    *
    * @param $class     String : Nome da class Filha (Obrigatorio)
    * @param $criteria  String : Objeto TCriteria (Obrigatorio)
    * 
    */
   function setSubCriteria($class, $criteria = null)
    {
        $criteria = !is_null($criteria)? $criteria: new TCriteria;
        
        TSession::setValue("sub_{$class}_criteria", $criteria);

        return $criteria;
    }
    
    function getSubCriteria($class)
    {
        $criteria = TSession::getValue("sub_{$class}_criteria"); 
        
        return $criteria;             
    }
    
    /* Adiciona Criteria adicional a Classe instanciada 
    * return TCriteria 
    *
    * @param $class     String : Nome da class Filha (Obrigatorio)
    * @param $criteria  String : Objeto TCriteria (Obrigatorio)
    * 
    */
   function setSubCriteriaFilter($class, $criteria = null)
    {
        $criteria = !is_null($criteria)? $criteria: new TCriteria;
        
        TSession::setValue("sub_{$class}_criteria_filter", $criteria);

        return $criteria;
    }
    
    function getSubCriteriaFilter($class)
    {
        $criteria = TSession::getValue("sub_{$class}_criteria_filter");                                       
        
        return $criteria;             
    }
    
   /* Adiciona Limit a Classe instanciada 
    * 
    * @param $class    String  : Nome da class Filha (Obrigatorio)
    * @param $limit    Integer : Valor do Limit (Obrigatorio)
    */
   function setSubLimit($class, $limit)
   {
       $this->subLimit[$class] = $limit;
   }    
   
   /* Adiciona OffSet a Classe instanciada 
    * 
    * @param $class    String  : Nome da class Filha (Obrigatorio)
    * @param $offset   Integer : Valor OffSet (Obrigatorio)
    */
   function setSubOffSet($class, $offset = 0)
   {
       $this->subOffset[$class] = $offset;
   }
   
   /* Armazena ID da Master Class instanciada 
    * return Integer
    *
    * @param $param    Array 
    *        -status String['insert', 'edit', 'delete'] : Limpa o ID caso o status seja insert (Opcional)
    */
   function setSubMasterId($param = null)
   {
      $master_id         = null;
      $master_class      = isset($param['master_class'])? $param['master_class'] : $this->activeRecord;
      $master_pk         = isset($param['master_pk'])   ? $param['master_pk']    : $master_class::PRIMARYKEY; 
            
      if(isset($param['status']) && ($param['status']=='edit'))
      {
          $master_id = $param[$master_pk];
          
          TSession::setValue($master_class."_id", $master_id);         
      } 
      
      if(isset($param['status']) && ($param['status']=='browse'))
      {
          $master_id = $this->getSubMasterId();
      } 
      
      if(isset($param['status']) && ($param['status']=='insert'))
      {
          TSession::setValue($master_class."_id", $master_id);        
      }

      return $master_id;         
   }
   
    /* Obtém o ID da Master Class instanciada 
    *  return Integer 
    */
   function getSubMasterId($param = null)
   {
       $master_class = isset($param['master_class'])? $param['master_class']: $this->activeRecord;
       
       return TSession::getValue($master_class."_id");
   }
   
   /**
     * Exportar Detalhe para CSV
     * @param $param Array
     *         -subclass     String : Nome da classe a ser exportada
     *         -output       String : Nome do arquivo de destino 
     */
   function exportSubToCSV($param)
   {
        $class     = $param['subclass'];
        $output    = $param['output'];
        $datagrid  = $this->subDatagrid[$class];
             
        $this->subLimit[$class] = 0;
        
        $objects = $this->onSubReload($param);
        
        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            TTransaction::open($this->database);
            $handler = fopen($output, 'w');
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $row        = [];
                    $row_object = new stdClass;
                    $row_line   = new stdClass;
                    
                    foreach ($datagrid->getColumns() as $column)
                    {
                        $column_name = $column->getName();
                        $col_value   = '';
                        
                        if($column->getTransformer())
                        {
                           $transformer = $column->getTransformer();
                                
                           $col_value = $transformer($object->{$column_name}, $row_object, $row_line);
                        }
                        else
                        {
                            $col_value = $object->$column_name;    
                        }
                         
                        if (isset($object->$column_name))
                        {
                            $row[] = is_scalar($col_value) ? $col_value : '';
                        }
                        else if (method_exists($object, 'render'))
                        {
                            $row[] = $object->render($column_name);
                        }
                    }
                    
                    fputcsv($handler, $row);
                }
            }
            fclose($handler);
            TTransaction::close();
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $output);
        }
   }

   /**
    * Action Exportar Detalhe para CSV
    * @param $param Array
    *         -subclass     String : Nome da classe a ser exportada
    *         -output       String : Nome do arquivo de destino 
    */
    public function onExportSubCSV($param)
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';
            $param['output'] = $output;
            $this->exportSubToCSV( $param );
            TPage::openFile( $output );
        }
        catch (Exception $e)
        {
            return new TMessage('error', $e->getMessage());
        }
    }
    
     
    /**
     * Exportar Detalhe para PDF
     * @param $param Array
     *         -subclass     String : Nome da classe a ser exportada
     *         -output       String : Nome do arquivo de destino 
     * Não funciona em tabelas Scrollable
     */
    public function exportSubToPDF($param)
    {
        $class      = $param['subclass']; 
        $output     = $param['output'];
        $datagrid   = $this->subDatagrid[$class];
        
        if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
        {
            $datagrid->prepareForPrinting();
             $datagrid->addItems($this->onSubReload($param));
             
            // string with HTML contents
            $html = clone $datagrid;
           
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new Dompdf;
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            
            $dompdf->render();
            
            // write and open file
            file_put_contents($output, $dompdf->output());
        }
        else
        {
            throw new Exception(_t('Permission denied') . ': ' . $output);
        }
    }
    
    /**
    * Action Exportar Detalhe para PDF
    * @param $param Array
    *         -detail_class String : Nome da classe a ser exportada
    *         -output       String : Nome do arquivo de destino
    * Não funciona em tabelas Scrollable 
    */
    public function onExportSubPDF($param)
    {
        try
        {
            $output          = 'app/output/'.uniqid().'.pdf';
            $param['output'] = $output;
            
            $this->exportSubToPDF($param);
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $output;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function fireEvents($object)
    {
        TForm::sendData($this->form->getName(), $object);
    }
    
    public function onEdit($param)
    {
        $this->setLastCurrentPage($param);
        $key = $this->setSubMasterId($param);
        
        try
        {
            if (empty($this->database))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (empty($this->activeRecord))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (!is_null($key))
            {
                TTransaction::open($this->database);
                
                $class = $this->activeRecord;
                
                $object = new $class($key);
                
                $this->form->setData($object);
                
                if(is_array($this->subClass))
                {
                    foreach($this->subClass as $position=>$subclass)
                    {
                        if($this->subDatagrid[$subclass])
                        {
                            $param['subclass']= $subclass;
                            
                            $repository = $this->onSubReload($param);
                     
                            $this->subDatagrid[$subclass]->addItems($repository);
                            
                            unset($param['subclass']);
                        }
                    }
                }
                
                $this->fireEvents($object);
                
                TTransaction::close();
                
                return $object;
            }
            else
            {
                $this->form->clear( true );
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }
    
    public function onSave()
    {
        try
        {
            TTransaction::open($this->database);
            
            $data = $this->form->getData();
            
            $class = $this->activeRecord;
                        
            $object = new $class();
            $object->fromArray( (array) $data);

            $this->form->validate(); 
            
            if(method_exists($this, 'onBeforeSave'))
            {
                $object = $this->onBeforeSave($object);                
            }

            $object->store(); 

            if(method_exists($this, 'onAfterSave'))
            {
                $object = $this->onAfterSave($object);                
            }
                        
            TTransaction::close();
                        
            $this->onEdit(['status'=> 'edit', 'reset'=>1, 'page'=>0, 'key'=> $object->{$class::PRIMARYKEY}, $class::PRIMARYKEY=>$object->{$class::PRIMARYKEY}]);
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            
            $this->form->setData( $this->form->getData() );
            
            TTransaction::rollback();
        }
    }
      
}
    
    
?>
