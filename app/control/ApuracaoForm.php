<?php

use Adianti\Widget\Form\TDate;
use Adianti\Widget\Wrapper\TDBCombo;

class ApuracaoForm extends TPage 
    {
        protected $form;
        protected $empresaid;
        protected $username;
        protected $filtroempresa;
        protected $params;
         
        public function __construct()
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder('form_Apuracao');
            $this->form->setFormTitle('Totalizar Custos e Receitas');
            
            $this->empresaid = TSession::getValue('userunitid');
            $this->username  = TSession::getValue('username');
            
            $this->params = new StdClass();  
            
            $this->filtroempresa = new TCriteria();
            $this->filtroempresa->add(new TFilter('EMPRESA','=', $this->empresaid));
            
            $t1 = new TDBSeekButton('t1', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', null, null, $this->filtroempresa);
            $t1aux = new THidden('t1aux');
                $t1->setAuxiliar($t1aux);
            $t2 = new TDBSeekButton('t2', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', null, null, $this->filtroempresa);
            $t2aux = new THidden('t2aux');
                $t2->setAuxiliar($t2aux);
            $q1 = new TEntry('q1');
            $q2 = new TEntry('q2');
            $l1 = new TEntry('l1');
            $l2 = new TEntry('l2');
            
            $mes = new TCombo('mes');
            $mes->addItems(
                [
                    '1'=>'Janeiro',
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
                    '12'=>'Dezembro',                    
                ]
                );
          
            $ano = new TCombo('ano');
            $ano->clear();
            $anos = [];
            for($i=date('Y');$i>1899;$i--) 
            {
                $anos[$i] = $i;
            }
            
            $ano->addItems($anos);

            $t1->setSize(TWgtSizes::wsInt);
            $t2->setSize(TWgtSizes::wsInt);
            $q1->setSize(TWgtSizes::wsInt);
            $q2->setSize(TWgtSizes::wsInt);
            $l1->setSize(TWgtSizes::wsInt);
            $l2->setSize(TWgtSizes::wsInt);
            $mes->setSize(TWgtSizes::ws20);
            $ano->setSize(TWgtSizes::ws10);
            
            $this->form->addFields([new TLabel('Empreendimentos de')],[$t1, new TLabel('a'), $t2]);
            $this->form->addFields([new TLabel('Quadras de')],[$q1, new TLabel('a') ,$q2]);
            $this->form->addFields([new TLabel('Lotes de')],[$l1, new TLabel('a') ,$l2]);
            $this->form->addFields([new TLabel('Competencia')],[$mes, $ano]);
            
            $this->form->addAction('Executar', new TAction([$this, 'onExecutarClick'],['static'=>'1']), 'fa:play green'); 
            
            $container = new TVBox();
            $container->style = 'overflow-x: auto; width: 90%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($this->form);
            
            parent::add($container);                        
        }
        
        public function onReload($param)
        {
            $this->loaded = TRUE;
        }
        
        public function onExecutarClick($param)
        {
            $param['params'] = $this->form->getData(); //Armazenar dados do form
            
            new TQuestion("Essa rotina irá recalcular todos os valores dos filtros selecionados, isso poderá demorar um pouco e não poderá ser desfeito.<br><br>"."Deseja continuar mesmo assim?", 
                new TAction([$this, 'Executar'],$param)
            );            
        }
        
        public function Executar($param)
        {  
           $inicio = new DateTime(date('Y-m-d H:i:s'));
           
           $data = $param;

           try
           {
                $e   = TSession::getValue('userunitid');
                $u   = TSession::getValue('login');
                $pt1 = !empty($param['t1'])?$param['t1']:'1';
                $pt2 = !empty($param['t2'])?$param['t2']:'9999';
                $pq1 = !empty($param['q1'])?$param['q1']:'1';
                $pq2 = !empty($param['q2'])?$param['q2']:'9999';
                $pl1 = !empty($param['l1'])?$param['l1']:'1';
                $pl2 = !empty($param['l2'])?$param['l2']:'9999';
                $mes = !empty($param['mes'])?$param['mes']:'12';
                $ano = !empty($param['ano'])?$param['ano']:'1899';
         
                $dti = new DateTime("{$ano}-{$mes}-01");
                $dtf = new DateTime("{$ano}-".str_pad($mes, 2, '0', STR_PAD_LEFT)."-01");
                $dtf = new DateTime($dtf->format('Y-m-t'));

                $period = new DatePeriod($dti, DateInterval::createFromDateString('1 month'), $dtf);

                $apurador = new Apurador();

                $apurador->apurar($e, $dtf, $pt1, $pt2, $pq1, $pq2, $pl1, $pl2);
                //$apurador->poc();
                exit;
               /* foreach($period as $mes)
                {
                   $strmes  = $mes->format('Y-m-d'); 
                   
                   TTransaction::open('gexpertlotes');
                   $link    = TTransaction::get();
                   $cmd     = $link->query("call sp_apurar({$e},{$e},{$pt1},{$pt2},{$pq1},{$pq2},{$pl1},{$pl2},'{$strmes}','{$u}')");
                   $cmd->execute();
                   TTransaction::close();
                                                         
                }*/
                
                // Novo comando a partir de 15/03/2021 criado para evitar o erro de timeout em grandes quantidades de registros
                
                TTransaction::open('gexpertlotes');
                
              
                   $link = TTransaction::get();
                  
                   $strmes  = $dti->format('Y-m-d'); 
                   
                   $query = $link->query("SELECT EMPREENDIMENTO, QUADRA, LOTE FROM vendas WHERE EMPRESA={$e} AND EMPREENDIMENTO BETWEEN {$pt1} AND {$pt2} AND QUADRA between {$pq1} and {$pq2} AND get_firstDayOfMonth(EMISSAO)<=get_firstDayOfMonth('{$strmes}') AND CANCELADO = 'N' GROUP BY 1,2,3 HAVING COUNT(1) > 1");
                   
                   $duplicado = $query->fetchAll();
                  
                   if(count($duplicado)>0)
                   {
                        //$duplicado = $query->fetchAll();
                        
                        $listaDuplicados = '<br><br>Foram encontradas vendas ativas em duplicidade!<br>';
                        
                        foreach($duplicado as $vda)
                        {
                             $listaDuplicados .= " - Empreendimento: {$vda['EMPREENDIMENTO']} &#9 Quadra: {$vda['QUADRA']} &#9 Lote: {$vda['LOTE']}<br>";
                        }
                        
                         throw new Exception("{$listaDuplicados}<br> Por favor verifique!");
                         
                         Exit; 
                   }
                   
                   $query = $link->query("select * from empreendimentos where EMPRESA={$e} and CODIGO between {$pt1} and {$pt2}");
                   
                   $empreendimentos = $query->fetchAll();
                   
                   foreach($empreendimentos as $t)
                   {
                       
                       $query = $link->query("select distinct QUADRA from lotes where EMPRESA={$e} and EMPREENDIMENTO={$t[0]} and QUADRA between {$pq1} and {$pq2} order by QUADRA");

                       $quadras = $query->fetchAll(); 
                       
                       foreach($quadras as $q)
                       {
                           
                           $query = $link->query("select CODIGO from lotes where EMPRESA={$e} and EMPREENDIMENTO={$t[0]} and QUADRA={$q[0]} and SITUACAO in (1,2)");
                          
                           $lotes = $query->fetchAll();
                          
                           foreach($lotes as $l)
                           {
                                // echo "call sp_apurar({$e},{$e},{$t[0]},{$t[0]},{$q[0]},{$q[0]},{$l[0]},{$l[0]},'{$strmes}','{$u}')<br>";
                                echo "<br>";
                                
                                $sth = $link->prepare("call sp_apurar({$e},{$e},{$t[0]},{$t[0]},{$q[0]},{$q[0]},{$l[0]},{$l[0]},'{$strmes}','{$u}')");
                                 
                                $sth->execute();                                                         
                           }                          
                       }                       
                   }
                   
                   TTransaction::close(); 
                 
                $this->form->setData((object) $data); //Devolver dados ao form
                
                $fim = new DateTime(date('Y-m-d H:i:s'));
                
                $decorrido = $inicio->diff($fim);
                
                new TMessage('info', "Apuração Gerada com Sucesso! <br> Tempo decorrido {$decorrido->format('%H:%I:%S')}" );                
           } 
           catch(Exception $e) 
           {
               new TMessage('error', "Ocorreu um erro: \n".$e->getMessage());    
           }
                    
        } 
    }
    
    

?>
