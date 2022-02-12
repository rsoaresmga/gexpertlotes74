<?php
   
    class ImportarHistoricosCsv extends TPage
    { 
        protected $form;
        public $state;
        
        public function __construct($param)
        {
            parent::__construct();
            
            $this->state = 0;
            
            $this->form = new BootstrapFormBuilder('form_implCSV2');
               
            $this->form->setFormTitle('Importar Históricos Contábeis CSV' ); 
            
            $csv = new TFile('csv');
            $csv->setAllowedExtensions(['csv']);
            $header = new TCombo('h');
            $header->addItems(['S'=>'Sim', 'N'=>'Não']);
            $header->setValue('S');
            
            $csv->setSize(TWgtSizes::wsDef);
            $header->setSize(TWgtSizes::wsBol);
            
            $this->form->addFields(['Arquivo'], [$csv]);
            $this->form->addFields(['Primeira linha contém cabeçalho'], [$header]);
            
            $this->form->addAction('Importar', new TAction([$this, 'onExecutarClick']), 'fas:cloud-upload-alt red');
            $this->form->addAction('Download Layout', new TAction([$this, 'onDownloadClick']), 'fas:cloud-download-alt green');
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($this->form);
            
            parent::add($container);   
        }
        
        public function onExecutarClick($param)
        {
            $csv = $param['csv'];
            $header = $param['h'];
            
            $frmDlg = new TQuickForm('frmDlg');
            $frmDlg->setFormTitle('Já existem históricos cadastrados no sistema!');
            $frmDlg->style = 'padding: 30px';
            $oper = new TCombo('o');
            $oper->addItems([2=>'Excluir e importar novamente', 3=>'Importar Somente não importados']);
            $vheader = new THidden('h');
            $vheader->setValue($header);
            $vcsv = new THidden('csv');
            $vcsv->setValue($csv);
            $frmDlg->addQuickField(new TLabel('<b>O que fazer?</b>'), $oper, TWgtSizes::ws80);
            $frmDlg->addQuickField('', $vcsv, 0);
            $frmDlg->addQuickField('', $vheader, 0);
            $frmDlg->addQuickAction('Iniciar', new TAction([$this, 'Executar']), 'fa:play green');
            
            TTransaction::open('gexpertlotes');
            $historicos = Historicos::where('CODIGO', '>', '0');
            $count = $historicos->count();
            TTransaction::close();
            
            
            if($count>0)
            {  
                new TInputDialog('Importar', $frmDlg);
                
            } else {
                       self::Executar(['csv'=>$csv, 'h'=>$header, 'o'=>1]);
                   }                  
        }
        
        public function Executar($param)
        {                         
            $i = 0;
            isset($param['h'])?$header=$param['h']:$header='N';
            isset($param['csv'])?$csv='tmp/'.$param['csv']:$csv='';
            isset($param['o'])?$o=$param['o']:$o=0;
            
            if(!empty($csv)&$csv!='tmp/')
            {
                $reader = new TReadCsv($csv);
                $reader->set_delim(",");                                 
            } else {
                       new TMessage('error', 'Nenhum arquivo selecionado.');                       
                   }
            
            if(isset($reader))
            {
                TTransaction::open('gexpertlotes');
                    
                   switch ($o) 
                    {
                        case 0: 
                            {
                                 new TMessage('erro','Opção selecionada é inválida!');   
                            }
                        case 1:
                            {   $i = 0;
                                
                                foreach($reader->abre() as $row)
                                {    if(($header=='S'&$i>0)||$header=='N')
                                    {
                                         $id = str_replace('.','',$row[0]); 
                                         $obj = new Historicos;
                                         $obj->CODIGO = $id;
                                         $obj->DESCRICAO = utf8_encode($row[1]);
                                         $obj->store();
                                     }                             
                                   $i++;                             
                                 }                                                                                                           
                            } 
                         case 2:
                             { $i = 0;
                                                       
                               $historicos = Historicos::where('CODIGO','>=',-1);
                               $historicos->delete();
                               
                               foreach($reader->abre() as $row)
                                {    
                                    if(($header=='S'&$i>0)||$header=='N')
                                    {
                                         $id = str_replace('.','',$row[0]); 
                                         $obj = new Historicos;
                                         $obj->CODIGO = $id;
                                         $obj->DESCRICAO = utf8_encode($row[1]);
                                         $obj->store();
                                     }                             
                                   $i++;                             
                                 } 
                             }
                           
                           case 3:
                             {   $i = 0;
                                 foreach($reader->abre() as $row)
                                 {
                                    $id = str_replace('.','',$row[0]);
                                    $historicos = Historicos::where('CODIGO','=', $id);
                               
                                    if(($header=='S'&$i>0)||$header=='N')
                                    {   
                                       if($historicos->count()<=0)
                                       {
                                          $id = $id; 
                                          $obj = new Historicos;
                                          $obj->CODIGO = $id;
                                          $obj->DESCRICAO = utf8_encode($row[1]);
                                          $obj->store(); 
                                       }
                                         
                                     }                             
                                   $i++;                             
                                 } 
                             }          
   
                    
                    }  
                                                  
                TTransaction::close(); 
                                
                new TMessage('info','Históricos importados com sucesso!');  
             }                 
         }               
        
        public function onLoad($param = NULL)
        {
            $this->loaded = TRUE;
        }
        
        public function onDownloadClick($param)
        {
            TPage::openFile('layouts/layout-historicos.csv');
        }             
        
    }
    
?>
