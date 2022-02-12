<?php
   
    class ImportarPlanoCsv extends TPage
    { 
        protected $form;
        
        public function __construct($param)
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder('form_implCSV');
               
            $this->form->setFormTitle('Importar Plano de Contas CSV' ); 
            
            $csv = new TFile('csv');
            $csv->setAllowedExtensions(['csv']);
            $header = new TCombo('header');
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
           
            $empresaid = TSession::getValue('userunitid');
            (isset($param['csv']))   ?$csv    = 'tmp/'.$param['csv']:$csv='';
            (isset($param['header']))?$header = $param['header']    :$header='';
            
            TTransaction::open('gexpertlotes');
                $plano = Plano::where('EMPRESA','=',$empresaid);
                $count = $plano->count();                      
            TTransaction::close(); 
            
            if ($csv<>'tmp/')
            {
                    if($count>0)
                    {
                        $dlg = new TQuickForm('fmDlg');
                        $dlg->setFormTitle('Já existe um plano de contas para a empresa selecionada!');
                        $dlg->style = 'padding: 30px';
                        $vcsv = new THidden('csv');
                        $vcsv->setValue($csv);  
                        $vheader = new THidden('header');
                        $vheader->setValue($header);              
                        $voper = new TCombo('op');
                        $voper->addItems([2=>'Excluir e importar novamente', 3=>'Importar Somente não importados']);         
                        $dlg->addQuickField(new TLabel('<b>O que fazer?</b>'), $voper, TWgtSizes::ws80);
                        $dlg->addQuickField('', $vcsv, '0%');
                        $dlg->addQuickField('', $vheader, '0%');
                        $dlg->addQuickAction('Iniciar', new TAction([$this, 'Executar']), 'fa:check blue');

        
                    
                        new TInputDialog('Importar', $dlg);
                    } 
                    else 
                    {
                        Self::Executar(['csv'=>$csv, 'op'=>1, 'header'=>$header]);  
                    }
            } 
            else
            {
                new TMessage('error', 'Nenhum arquivo selecionado');
            }
            
        }
        
         public function Executar($param)
        {                        
            $op = $param['op'];
            
            $i = 0;
            $header = $param['header'];
            
            if(!empty($param['csv']<>'tmp/'))
            {
                $reader = new TReadCsv($param['csv']);
                $reader->set_delim(",");
                               
            } else {
                       new TMessage('error', 'Nenhum arquivo selecionado.');
                   }
                   
            if(isset($reader))
            {
                TTransaction::open('gexpertlotes');
                
                switch ($op) 
                {
                   case 0:{
                              new TMessage('error', 'Opção selecionada é inválida');
                              break;
                          }
                   
                   //Somente importar
                   case 1: {
                               foreach($reader->abre() as $row)
                                {
                                    if(($header=='S'&$i>0)||$header=='N') 
                                    {
                                        if (!empty($obj->CODIGO = $row[0]))
                                        {  
                                            $obj = new Plano;
                                            $obj->CODIGO = str_replace('.','',$row[0]);
                                            $obj->EMPRESA = TSession::getValue('userunitid');
                                            $obj->GRUPO = $row[2];
                                            $obj->NATUREZA = $row[3];
                                            $obj->CLASSIFICACAO = $row[4];
                                            $obj->DESCRICAO = utf8_encode($row[5]);
                                            $obj->store();
                                        }   
                                     }
                                     
                                     $i++;    
                                } 
                                new TMessage('info','Plano de contas importado com sucesso!');
                                break; 
                            }
                    //Excluir e importar novamente        
                    case 2: {
                               $con = TTransaction::get();
                               $qry = $con->Query('delete from plano where empresa = '.TSession::getValue('userunitid'));
                               $qry->execute();
                               
                               foreach($reader->abre() as $row)
                                {
                                    if(($header=='S'&$i>0)||$header=='N') 
                                    {
                                        if (!empty($row[0]))
                                        {  
                                            $obj = new Plano;
                                            $obj->CODIGO = str_replace('.','',$row[0]);
                                            $obj->EMPRESA = TSession::getValue('userunitid');
                                            $obj->GRUPO = $row[2];
                                            $obj->NATUREZA = $row[3];
                                            $obj->CLASSIFICACAO = $row[4];
                                            $obj->DESCRICAO = utf8_encode($row[5]);
                                            $obj->store();
                                        }    
                                     }
                                     
                                     $i++;    
                                } 
                                new TMessage('info','Plano de contas importado com sucesso!');
                                break;  
                            } 
                    case 3: {  //Importar apenas nao importados
                               foreach($reader->abre() as $row)
                                {
                                          
                                      if(($header=='S'&$i>0)||$header=='N') 
                                        {
                                            $record = Plano::where('EMPRESA', '=', intval(TSession::getValue('userunitid')))->where('CODIGO','=',str_replace('.','',$row[0])); 

                                            if($record->count()<=0)
                                            {                                                
                                              if (!empty($row[0]))
                                              {   
                                                $obj = new Plano;
                                                $obj->CODIGO = $row[0];
                                                $obj->EMPRESA = TSession::getValue('userunitid');
                                                $obj->GRUPO = $row[2];
                                                $obj->NATUREZA = $row[3];
                                                $obj->CLASSIFICACAO = $row[4];
                                                $obj->DESCRICAO = utf8_encode($row[5]);
                                                $obj->store();
                                               } 
                                            }
                                            
                                         }
                                             
                                    $i++;    
                                        
                                 } 
                                new TMessage('info','Plano de contas importado com sucesso!');          
                                break;                                              
                             } 

                }
                
                
               
                TTransaction::close(); 
                
            }     
            
        }
        
        public function onLoad($param = NULL)
        {
            $this->loaded = TRUE;
        }
        
        public function onDownloadClick($param)
        {
            TPage::openFile('layouts/layout-plano.csv');
        }          
        
    }
    
?>
