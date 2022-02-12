<?php
    class ExportarLctoCtb extends TPage
    {
        protected $form;
        protected $empresaid;
        protected $empresanome;
        protected $filtroempresa;
        protected $csvfile;
        
        public function __construct($param)
        {
            parent::__construct();
            
            $this->empresaid = TSession::getValue('userunitid');
            $this->empresanome = TSession::getValue('userunitname');
            
            $this->filtroempresa = new TCriteria();
            $this->filtroempresa->add(new TFilter('EMPRESA', '=', $this->empresaid));
            
            $this->form = new BootstrapFormBuilder('form_ExportaLctoCtb');
            $this->form->setFormTitle('Exportação de Lançamentos Contábeis');
            
            $this->sim_nao = ['S'=>'Sim', 'N'=>'Não'];
            
            $empdto1 = new TDBSeekButton('empdto1', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO',null,null,$this->filtroempresa);
            $aux1 = new THidden('aux1');
                $empdto1->setAuxiliar($aux1);
            $empdto2 = new TDBSeekButton('empdto2', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO',null,null,$this->filtroempresa);
            $aux2 = new THidden('aux2');
                $empdto2->setAuxiliar($aux2);
            $qda1   = new TEntry('qda1');
            $qda2   = new TEntry('qda2');
            $lte1   = new TEntry('lte1');
            $lte2   = new TEntry('lte2');
            $dta1   = new TDate('dta1');
                $dta1->setMask(TMascara::maskDate);
                $dta1->setDatabaseMask(TMascara::maskDBDate);
            $dta2   = new TDate('dta2');
                $dta2->setMask(TMascara::maskDate);
                $dta2->setDatabaseMask(TMascara::maskDBDate);
            
            $aqs    = new TCombo('aqs');
            $aqs->addItems($this->sim_nao);
            $ven    = new TCombo('ven');
            $ven->addItems($this->sim_nao); 
            $res    = new TCombo('res');
            $res->addItems($this->sim_nao); 
            $inf    = new TCombo('inf');
            $inf->addItems($this->sim_nao); 
            $tax    = new TCombo('tax');
            $tax->addItems($this->sim_nao); 
            $par    = new TCombo('par');
            $par->addItems($this->sim_nao); 
            $jur    = new TCombo('jur');
            $jur->addItems($this->sim_nao); 
            $att    = new TCombo('att');
            $att->addItems($this->sim_nao); 
            
            $aqs->setValue('N');
            $ven->setValue('N');
            $res->setValue('N');
            $inf->setValue('N');
            $tax->setValue('N');
            $par->setValue('N');
            $jur->setValue('N');
            $att->setValue('N');
                    
            $empdto1->setSize(TWgtSizes::wsInt);
            $empdto2->setSize(TWgtSizes::wsInt);            
            $qda1->setSize(TWgtSizes::wsInt);
            $qda2->setSize(TWgtSizes::wsInt);
            $lte1->setSize(TWgtSizes::wsInt);
            $lte2->setSize(TWgtSizes::wsInt);
            $dta1->setSize(TWgtSizes::wsDate);
            $dta2->setSize(TWgtSizes::wsDate);
            $aqs->setSize(TWgtSizes::wsBol);
            $ven->setSize(TWgtSizes::wsBol);
            $res->setSize(TWgtSizes::wsBol);
            $inf->setSize(TWgtSizes::wsBol);
            $tax->setSize(TWgtSizes::wsBol);
            $par->setSize(TWgtSizes::wsBol);
            $jur->setSize(TWgtSizes::wsBol);
            $att->setSize(TWgtSizes::wsBol);
            
            $this->form->addFields([new TLabel('Empreendimento')],[$empdto1, '&nbsp&nbsp&nbsp&nbspà&nbsp&nbsp', $empdto2]);
            $this->form->addFields([new TLabel('Quadra')],[$qda1, '&nbsp&nbspà&nbsp&nbsp', $qda2]);
            $this->form->addFields([new TLabel('Lote')],[$lte1, '&nbsp&nbspà&nbsp&nbsp', $lte2]);
            $this->form->addFields([new TLabel('Data')],[$dta1, '&nbsp&nbspà&nbsp&nbsp', $dta2]);
            $this->form->addFields([new TLabel('Aquisição')],[$aqs]);
            $this->form->addFields([new TLabel('Vendas')],[$ven]);
            $this->form->addFields([new TLabel('Rescisão')],[$res]);
            $this->form->addFields([new TLabel('Infraestrutura')],[$inf]);
            $this->form->addFields([new TLabel('Taxa')],[$tax]);
            $this->form->addFields([new TLabel('Parcelas')],[$par]);
            $this->form->addFields([new TLabel('Juros')],[$jur]);
            $this->form->addFields([new TLabel('Atualização')],[$att]);
            
            $this->form->addAction('Gerar', new TAction([$this, 'onGerarClick']), 'fa:download red');
            
            $container = new TVBox();
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));            
            $container->style = 'width: 100%';
            $container->add($this->form);
            
            parent::add($container);
        }
        
        public static function onGerarClick($param)
        {
           TTransaction::open('gexpertlotes');
           
           $razao    = TSession::getValue('userunitname');
           $empresa  = TSession::getValue('userunitid');
           
           $pEmpdto1 = !empty($param['empdto1'])?$param['empdto1']:'0'; 
           $pEmpdto2 = !empty($param['empdto2'])?$param['empdto2']:'99999';
           $pDta1    = !empty($param['dta1'])?$param['dta1']:'1899-01-01';
           $pDta2    = !empty($param['dta2'])?$param['dta2']:'2100-12-31';
           $pQda1    = !empty($param['qda1'])?$param['qda1']:'0';
           $pQda2    = !empty($param['qda2'])?$param['qda2']:'99999';
           $pLte1    = !empty($param['lte1'])?$param['lte1']:'0';
           $pLte2    = !empty($param['lte2'])?$param['lte2']:'99999';
           $pAqs     = !empty($param['aqs'])?$param['aqs']:'N';
           $pVen     = !empty($param['ven'])?$param['ven']:'N';
           $pRes     = !empty($param['res'])?$param['res']:'N';
           $pInf     = !empty($param['inf'])?$param['inf']:'N';
           $pTax     = !empty($param['tax'])?$param['tax']:'N';
           $pPar     = !empty($param['par'])?$param['par']:'N';
           $pJur     = !empty($param['jur'])?$param['jur']:'N';
           $pAtt     = !empty($param['att'])?$param['att']:'N';
           
           $pDta1 = TConversion::asSQLDate($pDta1);
           $pDta2 = TConversion::asSQLDate($pDta2);
           
           $bdlink   = TTransaction::get();
           
           $where    =  " where empresa={$empresa} and data between '{$pDta1}' and '{$pDta2}' and empreendimento between {$pEmpdto1} and {$pEmpdto2} ";
           $whereql  = $where." and quadra between {$pQda1} and {$pQda2} and lote between {$pLte1} and {$pLte2} ";
               
           $csvfile = new TCsvFile('tmp/lctoctb_'.$razao.'_'.$pDta1.'_'.$pDta2.'.csv');
           $csvfile->open(moRewrite); 
                     
           if($pAqs=='S')
           {    
               $qrAqs    = $bdlink->query("select * from sel_lctoctb_aquisicao".$where." order by data"); 
               $csvfile->writeqr($qrAqs->fetchAll()); 
           }
           if($pVen=='S')
           {
               $qrVen    = $bdlink->query("select * from sel_lctoctb_vendas".$whereql." order by data");  
               $csvfile->writeqr($qrVen->fetchAll());    
           }
           if($pRes=='S')
           {
               $qrRes    = $bdlink->query("select * from sel_lctoctb_distratos". $whereql." order by data");
               $csvfile->writeqr($qrRes->fetchAll());      
           }
           if($pInf=='S')
           {
               $qrInf    = $bdlink->query("select * from sel_lctoctb_infraestrutura". $whereql." order by data");
               $csvfile->writeqr($qrInf->fetchAll());    
           }
           if($pTax=='S')
           {
               $qrTax    = $bdlink->query("select * from sel_lctoctb_taxas". $whereql." order by data");
               $csvfile->writeqr($qrTax->fetchAll());                  
           }
           if($pPar=='S')
           {
               $qrPar    = $bdlink->query("select * from sel_lctoctb_parcelas". $whereql." order by data");
               $csvfile->writeqr($qrPar->fetchAll());                 
           }
           if($pJur=='S')
           {
              $qrJur    = $bdlink->query("select * from sel_lctoctb_juros". $whereql." order by data");  
              $csvfile->writeqr($qrJur->fetchAll());                   
           }
           if($pAtt=='S')
           {
              $qrAtt    = $bdlink->query("select * from sel_lctoctb_atualizacao". $whereql." order by data");
              $csvfile->writeqr($qrAtt->fetchAll());                  
           } 
           
           //Finaliza e faz download do arquivo                  
           $csvfile->download();
           
           $csvfile->close();
           
           TTransaction::close();
        }
    }
?>