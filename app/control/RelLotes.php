<?php
    class RelLotes extends TPage 
    {
        protected $form;
        protected $where;
        protected $reportdir;
        protected $report; 
        protected $cols;
        
        function __construct()
        {
            parent::__construct();
            
             /* @ Titulo do Form e Relatorio */
            $this->title  = 'Relatório de Cadastro de Lotes';
            
            /* @ Tamanho das colunas */
            $this->cols   = array(40,45,40,40,80,80,80,40,40); 
            
            $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
            $this->form->setFormTitle($this->title);          

            $e1 = new TDBSeekButton('e1', 'gexpertlotes', $this->form->getName(), 'Empresas', 'RAZAO', 'e1');
            $e1aux = new THidden('e1aux');
            $e1->setAuxiliar($e1aux);
            
            $e2 = new TDBSeekButton('e2', 'gexpertlotes', $this->form->getName(), 'Empresas', 'RAZAO', 'e2');
            $e2aux = new THidden('e2aux');
            $e2->setAuxiliar($e2aux);
            
            $t1 = new TDBSeekButton('t1', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't1');
            $t1aux = new THidden('t1aux');
            $t1->setAuxiliar($e1aux);
            
            $t2 = new TDBSeekButton('t2', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't2');
            $t2aux = new THidden('t2aux');
            $t2->setAuxiliar($e2aux);
            
            $q1 = new TEntry('q1');          
            $q2 = new TEntry('q2');
            
            $l1 = new TEntry('l1');          
            $l2 = new TEntry('l2');
            
            $sit = new TCombo('sit');
            $sit->addItems(['0'=>'Aberto', '1'=>'Vendido', '2'=>'Devolvido', '3'=>'Renegociado']);
            
            $output_type = new TRadioGroup('output_type');
            $output_type->addItems(['pdf'=>'PDF','xls'=>'XLS','html'=>'HTML', 'rtf'=>'RTF']);
            $output_type->setValue('pdf');
            $output_type->setLayout('horizontal');
            $output_type->setUseButton();
            
            $e1->setSize(TWgtSizes::wsInt);
            $e2->setSize(TWgtSizes::wsInt);
            $t1->setSize(TWgtSizes::wsInt);
            $t2->setSize(TWgtSizes::wsInt);
            $q1->setSize(TWgtSizes::wsInt);
            $q2->setSize(TWgtSizes::wsInt);
            $l1->setSize(TWgtSizes::wsInt);
            $l2->setSize(TWgtSizes::wsInt);
            $sit->setSize(TWgtSizes::ws30);
            
            $this->form->addFields([new TLabel('Empresas de')],[$e1, new TLabel('&nbsp&nbspa'), $e2]);
            $this->form->addFields([new TLabel('Empreendimentos de')],[$t1, new TLabel('&nbsp&nbspa'), $t2]);
            $this->form->addFields([new TLabel('Quadras de')],[$q1, new TLabel('&nbsp&nbspa'), $q2]);
            $this->form->addFields([new TLabel('Lotes de')],[$l1, new TLabel('&nbsp&nbspa'), $l2]);
            $this->form->addFields([new TLabel('Situação')],[$sit]);
            
            $this->form->addFields([new TLabel('Tipo de Arquivo')],[$output_type]);
            $this->form->addAction('Gerar', new TAction([$this, 'onGenerate']), 'fa:download blue'); 
            
            $container = new TVBox();
            $container->style = 'overflow-x: auto; width: 90%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($this->form);
            
            parent::add($container);
            
        }
        
        public function onGenerate()
        {
            $data = $this->form->getData();
            
   	        $pe1 = (!empty($data->e1))?$data->e1:'1';
            $pe2 = (!empty($data->e2))?$data->e2:'9999';
            $pt1 = (!empty($data->t1))?$data->t1:'1';
            $pt2 = (!empty($data->t2))?$data->t2:'9999';
            $pq1 = (!empty($data->q1))?$data->q1:'1';
            $pq2 = (!empty($data->q2))?$data->q2:'9999';
            $pl1 = (!empty($data->l1))?$data->l1:'1';
            $pl2 = (!empty($data->l2))?$data->l2:'9999';
            $psit = !empty($data->sit)?$data->sit:'0,1,2,3';
            
            $sql    = "select 
                            empresa,
                            empreendimento,
                            quadra,
                            codigo as lote,
                            desmembramento,
                            area,
                            vlrcusto,
                            case when situacao = 0 then 'Aberto'
                                 when situacao = 1 then 'Vendido'
                                 when situacao = 2 then 'Devolvido'
                                 when situacao = 3 then 'Renegociado'
                            end situacao,
                            case when ativo = 'S' then 'Sim' else 'Não' end ativo
                       from lotes
                      where (1=1)"; 
            
            $where  = "";
            $where .= " and empresa between {$pe1} and {$pe2} ";
            $where .= " and empreendimento between {$pt1} and {$pt2} ";
            $where .= " and quadra between {$pq1} and {$pq2} ";
            $where .= " and codigo between {$pl1} and {$pl2} ";
            $where .= " and situacao in ({$psit}) "; 
            
            $orderby = " order by 1,2,3,4";
            
            $this->reportdir = "app/output/".__CLASS__.".".$data->output_type;

            switch ($data->output_type)
            {
                case 'pdf': 
                        $this->report = new TTableWriterPDF($this->cols);
                         
                    break;
                case 'xls': 
                        $this->report = new TTableWriterXLS($this->cols);
                    break;
                case 'rtf': 
                        $this->report = new TTableWriterRTF($this->cols);
                    break;        
                case 'html': 
                        $this->report = new TTableWriterHTML($this->cols); 
                    break;    
            }
 
            $color = FALSE;           
            $style = 'datap'; 
 
            $this->report->addStyle('header', 'Times', '15', 'B', '#000000', '#ffffff');
            $this->report->addStyle('title', 'Arial', '9', 'B',    '#000000', '#ffffff');
            $this->report->addStyle('datap', 'Arial', '7', '',    '#000000', '#E3E3E3', 'LR');
            $this->report->addStyle('datai', 'Arial', '7', '',    '#000000', '#ffffff', 'LR');
            $this->report->addStyle('footer', 'Times', '8', '', '#000000', '#ffffff');
           
            
            $this->report->setHeaderCallback(function()
            {
                $this->report->addRow();
                $this->report->addCell($this->title, 'center', 'header', count($this->cols));
                
                /* @ Titulos das Colunas */                
                $this->report->addRow();   
                $this->report->addCell('Empresa', 'left', 'title');
                $this->report->addCell('Empreend', 'left', 'title');
                $this->report->addCell('Quadra', 'left', 'title');
                $this->report->addCell('Lote', 'left', 'title');
                $this->report->addCell('Desmemb', 'left', 'title');
                $this->report->addCell('Area', 'left', 'title');
                $this->report->addCell('Vlr Custo', 'left', 'title');
                $this->report->addCell('Situação', 'left', 'title');
                $this->report->addCell('Ativo', 'left', 'title');   
            });
            
            $this->report->setFooterCallback(function()
            {
                $this->report->addRow();
                $this->report->addCell('Usuário: '.TSession::getValue('username').', Data/Hora Impressão: '.date('d/m/Y h:i:s'), 'right', 'footer', count($this->cols));
            });
             
            TTransaction::open('gexpertlotes');           
            $database = TTransaction::get();            
            $records  = $database->query($sql.$where.$orderby)->fetchAll();                       
           
            $color = FALSE;
            
            $totalarea = 0;
            $totalcusto = 0;
            $count = 0;
            
            foreach($records as $recno)
            {
               $style = $color?'datap':'datai';
               
               $this->report->addRow();
               $this->report->addCell($recno["empresa"], 'left', $style);
               $this->report->addCell($recno["empreendimento"], 'left', $style);
               $this->report->addCell($recno["quadra"], 'left', $style);
               $this->report->addCell($recno["lote"], 'left', $style);
               $this->report->addCell(TConversion::asDate($recno["desmembramento"]), 'left', $style);
               $this->report->addCell(TConversion::asDoubleBR($recno["area"],5), 'left', $style);
               $this->report->addCell(TConversion::asDoubleBR($recno["vlrcusto"],5), 'left', $style);
               $this->report->addCell($recno["situacao"], 'left', $style);
               $this->report->addCell($recno["ativo"], 'left', $style);
               
               $totalarea += $recno["area"];
               $totalcusto += $recno["vlrcusto"];
               $count += 1; 
               
               $color = !$color;
            }
            
            $this->report->addRow();
            $this->report->addCell('Total->> '.$count,'left', 'title', 5);
            $this->report->addCell(TConversion::asDoubleBR($totalarea,5), 'left', 'title', 1);
            $this->report->addCell(TConversion::asDoubleBR($totalcusto,5), 'left', 'title', 1);
            
            
            $this->report->save($this->reportdir);
            parent::openFile($this->reportdir);
            $this->form->setData($data);   
            
        }
        
    }
?>