<?php

    class ExportarF200 extends TPage
    {
        protected $form;
        protected $empresaid;
        protected $empresanome;
        
        public function __construct($param)
        {
            $this->empresaid   = TSession::getValue('userunitid');
            $this->empresanome = TSession::getValue('userunitname');
            
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder('form_ExportarF200');
            $this->form->setFormTitle('Gerar Arquivo Sped F200');
            
            $d1 = new TDate('d1');
                $d1->setMask(TMascara::maskDate);
                $d1->setDatabaseMask(TMascara::maskDBDate);
            $d2 = new TDate('d2');
                $d2->setMask(TMascara::maskDate);
                $d2->setDatabaseMask(TMascara::maskDBDate);
                
            $d1->setSize(TWgtSizes::wsDate);    
            $d2->setSize(TWgtSizes::wsDate);
                
            $this->form->addFields([new TLabel('Período')], [$d1, $d2]);
            
            $this->form->addAction('Gerar', new TAction([$this, 'onGerarClick']), 'fa:download red');
            
            $container = new TVBox();
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));            
            $container->style = 'width: 100%';
            $container->add($this->form);
            
            parent::add($container);            
        }
        
        public function onGerarClick($param)
        {
            $vd1 = TConversion::asSQLDate($param['d1']);
            $vd2 = TConversion::asSQLDate($param['d2']);
            
            $sql    = "select 
                        REG, 
                        IND_OPER, 
                        UNID_IMOB, 
                        IDENT_EMP, 
                        DESC_UNID_IMOB, 
                        NR_CONTRATO, 
                        NR_CPF_PROP, 
                        DT_CONTRATO, 
                        VL_CONTRATO, 
                        VL_REC_ACUMUL, 
                        VL_TOT_REC, 
                        CST_PIS, 
                        VL_BASE_PIS, 
                        AL_PIS, 
                        VL_PIS, 
                        CST_COFINS, 
                        VL_BASE_COFINS, 
                        AL_COFINS, 
                        VL_COFINS, 
                        PR_REC_ACUMUL, 
                        IND_NAT_UNID_IMOB, 
                        INF_COMP
                       from sel_sped_f200 where empresa= {$this->empresaid} and competencia between '{$vd1}' and '{$vd2}'";

            TTransaction::open('gexpertlotes');
                
                $link = TTransaction::get();
                $qry = $link->query($sql);
                
                $file = new TCsvFile('tmp/spedF200_'.$this->empresanome.'_'.$vd1.'_a_'.$vd2.'.sped');
                
                $file->open(moRewrite);
                
                $file->writeqr($qry->fetchAll(),'|');
                
                $file->download();
                
                $file->close();                
            
            TTransaction::close();
            
        }
    }

?>
