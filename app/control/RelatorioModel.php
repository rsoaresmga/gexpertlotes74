<?php
    class RelatorioModel extends TPage
    {
        public $form;
        public $reportdir;
        public $report;
        public $fields;
        public $fields_size;
        public $fields_str;
        public $records_count;
        public $records_sum;
        public $title;
        public $query;
        public $entity; 
        public $where;
        public $orderby;
        public $groupby;
        public $controlgroup;
        public $container;        
               
        public function __construct($props)
        {
            parent::__construct();
								            
            /* @ Titulo do Form e Relatorio */
            $this->title  = $props['title'];
            
             /* @ Colunas */
            $this->fields = $props['fields'];
            
            $this->orderby = isset($props['order'])?$props['order']:array('1');
            
            $this->groupby = isset($props['group'])?$props['group']:array();
            
            $this->controlgroup = array();
            
            /* @ Size Fields */
            $this->fields_size = array_column($this->fields, 'size');
            
            $this->form = new BootstrapFormBuilder('form_'.get_class($this));
            $this->form->setFormTitle($this->title);  
            
            $output_type = new TRadioGroup('output_type');
            $output_type->addItems(['pdf'=>'PDF','xls'=>'XLS','html'=>'HTML', 'rtf'=>'RTF']);
            $output_type->setValue('pdf');
            $output_type->setLayout('horizontal');
            $output_type->setUseButton();
            $this->form->addFields([new TLabel('Tipo de Arquivo')],[$output_type]);
            
            
            $this->form->addAction('Gerar', new TAction([$this, 'onGenerate']), 'fa:download blue');            
            $this->container = new TVBox();
            $this->container->style = 'overflow-x: auto; width: 90%';
            $this->container->add(new TXMLBreadCrumb('menu.xml', get_class($this)));
            $this->container->add($this->form);
            
            parent::add($this->container);
        }        
        
        public function onGenerate($param = NULL)
        {
          $data = $this->form->getData();
            
          $this->query = $param['q'];
            
          $this->reportdir = 'app/output/'.get_class($this).'.'.$data->output_type;
                        
            switch ($data->output_type)
            {
                case 'pdf': 
                        $this->report = new TTableWriterPDF($this->fields_size);                                 
                    break;
                case 'xls': 
                        $this->report = new TTableWriterXLS($this->fields_size);
                    break;
                case 'rtf': 
                        $this->report = new TTableWriterRTF($this->fields_size);
                    break;        
                case 'html': 
                        $this->report = new TTableWriterHTML($this->fields_size);
                    break;    
            }
            
            $this->report->addStyle('header', 'Times', '17', 'B', '#000000', '#ffffff','');
            $this->report->addStyle('line', 'Times', '17', 'B', '#000000', '#F5F5F5','');
            $this->report->addStyle('title',  'Arial', '7',  'B', '#000000', '#ffffff','');
            $this->report->addStyle('totalh',  'Arial', '7',  'BI','#000000', '#F5F5F5', '');
            $this->report->addStyle('totalf',  'Arial', '7',  'BI','#000000', '#F5F5F5', '');
            $this->report->addStyle('totali',  'Arial', '7',  'BI','#000000', '#F5F5F5', '');
            $this->report->addStyle('datap',  'Arial', '7',  '',  '#000000', '#E3E3E3', '');
            $this->report->addStyle('datai',  'Arial', '7',  '',  '#000000', '#ffffff', '');
            $this->report->addStyle('footer', 'Times', '8',  '',  '#000000', '#ffffff','');
            
            $color = FALSE;           
            $style = 'datap';
                           
            $this->report->setHeaderCallback(function()
            {
                $this->report->addRow();
                $this->report->addCell($this->title, 'center', 'header', count($this->fields));
                
                $this->report->addRow();
               
               /* @ Titulos das Colunas */   
               foreach($this->fields as $field)
               {
                   $this->report->addCell($field['caption'], $field['align'], 'title');
               }       
          });
            
            $this->report->setFooterCallback(function()
            {
                $this->report->addRow();
                $this->report->addCell('Usuário: '.TSession::getValue('username').', Data/Hora Impressão: '.date('d/m/Y h:i:s'), 'right', 'footer', count($this->fields));
            });
            
  
            TTransaction::open('gexpertlotes');
            $database = TTransaction::get();
            $records = $database->query($this->query)->fetchAll();
            TTransaction::close();
           
           /* @ Varrer Registros */ 
           foreach($records as $i=>$recno)
           {                         
                // @ ++++++++++++++++++ Inserir Header Group ++++++++++++++++//
                
               for($gid=count($this->groupby)-1;$gid>=0;$gid--)
                {
                   if(isset($records[$i-1]))
                   {
                        if($records[$i-1][$this->groupby[$gid]['field']]<>$recno[$this->groupby[$gid]['field']])
                        {                             
                            $this->report->addRow();
                            $this->report->addCell($this->groupby[$gid]['header'].' ['.$recno[$this->groupby[$gid]['field']].']', 'left', 'totalh', count($this->fields));
                        }   
                   }
                   else
                   {
                       $this->report->addRow();
                       $this->report->addCell($this->groupby[$gid]['header'].' ['.$recno[$this->groupby[$gid]['field']].']', 'left', 'totalh', count($this->fields));    
                   }                                                            
                }
                // @ +++++++++++++++ Fim Inserir Header Group ++++++++++++++++//
                
                // @ ++++++++++++++++++ Totalizar Relatorio +++++++++++++++++//
                foreach($this->fields as $f)
                 {                                                           
                    //Totalizador do Relatorio
                    if(isset($f['total']))
                    {
                        if ($f['total']=='count')
                        {
                            if(!isset($this->records_count[$f['name']]))
                            {
                                 $this->records_count[$f['name']] = 1;                                                                     
                            }
                            else
                            {
                                $this->records_count[$f['name']] += 1;
                            }
                            
                            //Totalizador do Grupo
                            foreach($this->groupby as $key=>$g)
                            {
                                if(!isset($this->controlgroup[$f['name']][$g['field']]['count']))
                                {
                                    $this->controlgroup[$f['name']][$g['field']]['count'] = 1;    
                                }
                                else
                                {
                                    $this->controlgroup[$f['name']][$g['field']]['count'] += 1;
                                } 
                                 
                            }
                                   
                                                                                   
                        }                        
                        if ($f['total']!=null)
                        {
                                if ($f['total']=='sum')
                                {
                                    if(!isset($this->records_sum[$f['name']]))
                                    {
                                        $this->records_sum[$f['name']] = $recno[$f['name']];        
                                    }
                                    else
                                    {
                                        $this->records_sum[$f['name']] += $recno[$f['name']];                                     
                                    }
                                    
                                    //Totalizador do Grupo
                                    foreach($this->groupby as $key=>$g)
                                    {
                                        if(!isset($this->controlgroup[$f['name']][$g['field']]['sum']))
                                        {
                                            $this->controlgroup[$f['name']][$g['field']]['sum'] = $recno[$f['name']];    
                                        }
                                        else
                                        {
                                            $this->controlgroup[$f['name']][$g['field']]['sum'] += $recno[$f['name']];
                                        }   
                                    }                            
                                }
                         }                               
                    }                                        
                } 
                // @ ++++++++++++++++++ Fim Totalizar Relatorio +++++++++++++++++// 
                
                // @ ++++++++++++++ Popular Linhas ++++++++++++++++++//
                 $style = $color?'datap':'datai';
                
                 $this->report->addRow();
                
                 foreach($this->fields as $field)
                 { 
                     $value = (isset($field['type']))?TUtil::format($field['type'], $recno[$field['name']]):$recno[$field['name']];
                                                             
                     $this->report->addCell($value, $field['align'], $style);
                 }
                 
                  $color = !$color; 
                // @ ++++++++++++++ Fim Popular Linhas ++++++++++++++++++//
                
                
                // @ +++++++++++++ Agrupar Relatorio ++++++++++++++ 
                foreach($this->groupby as $key=>$g)
                {                   
                     
                    if(isset($records[$i+1]))
                    {
                        if(($records[$i+1][$g['field']])<>($recno[$g['field']]))
                        {
                            
                            $this->report->addRow();
                            $this->report->addCell($g['footer'].' ['.$recno[$g['field']].']', $f['align'], 'totalf', count($this->fields));
                            
                            $this->report->addRow();
                            
                            foreach($this->fields as $f)
                            {
                                if(isset($f['total']))
                                {
                                    if(isset($this->controlgroup[$f['name']][$g['field']][$f['total']]))
                                    {
                                         
                                         $this->report->addCell(TUtil::format(isset($f['type'])?$f['type']:'natural',$this->controlgroup[$f['name']][$g['field']][$f['total']]), $f['align'], 'totali'); 
                                         $this->controlgroup[$f['name']][$g['field']][$f['total']] = 0;   
                                    }    
                                } 
                                else
                                {
                                    $this->report->addCell('', $field['align'], 'totali');   
                                }
                            }                                                                                   
                        }
                        
                    } 
                    else //Final do Array
                    {
                       $this->report->addRow();
                       $this->report->addCell($g['footer'].' ['.$recno[$g['field']].']', $f['align'], 'totalf', count($this->fields));
                       
                       $this->report->addRow();
                       
                       foreach($this->fields as $f)
                            {
                                if(isset($f['total']))
                                {
                                    if(isset($this->controlgroup[$f['name']][$g['field']][$f['total']]))
                                    {
                                         $this->report->addCell(TUtil::format(isset($f['type'])?$f['type']:'natural',$this->controlgroup[$f['name']][$g['field']][$f['total']]), $f['align'], 'totali'); 
                                         $this->controlgroup[$f['name']][$g['field']][$f['total']] = 0;   
                                    }    
                                }
                                else
                                {
                                    $this->report->addCell('', $field['align'], 'totali');   
                                } 
                            }   
                        }
                    }                                        
                
                // @ +++++++++++++++++++ Fim Agrupar Relatorio ++++++++++++++++// 
                
               
            }           
            
            
            /* @ Increment Total Vars */ 
            $this->report->addRow();
            $this->report->addCell('Total Geral', (isset($field)?$field['align']:'left'), 'totalf', count($this->fields));
                            
            $this->report->addRow();
            
            foreach($this->fields as $field)
            {
                if(isset($field['total']))
                {
                    if($field['total']=='count')
                    {
                        $this->report->addCell(TUtil::format(isset($field['type'])?$field['type']:'natural',$this->records_count[$field['name']]), $field['align'], 'totalf');                                                
                    }                        
                    if($field['total']=='sum')
                    {
                        $this->report->addCell(TUtil::format(isset($field['type'])?$field['type']:'natural',$this->records_sum[$field['name']]), $field['align'], 'totalf'); 
                    } 
                }
                else 
                {
                    $this->report->addCell('', $field['align'], 'totalf');   
                }
            }
            
            $this->report->save($this->reportdir);
            parent::openFile($this->reportdir);
            $this->form->setData($data);    
            
     }
           
 }
 ?>