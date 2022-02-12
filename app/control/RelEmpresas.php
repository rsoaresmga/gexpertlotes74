<?php
    class RelEmpresas extends TPage 
    {
        protected $form;
        protected $reportdir;
        protected $report;
        protected $cols;
        protected $title;
       
        function __construct()
        {
            parent::__construct();
              
            /* @ Titulo do Form e Relatorio */
            $this->title  = 'Relatório Cadastro de Empresas';
            
            /* @ Tamanho das colunas */
            $this->cols   = array(40,250,250,120,80,25); 
            
            $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
            $this->form->setFormTitle($this->title);            
            
            $output_type = new TRadioGroup('output_type');
            $output_type->addItems(['pdf'=>'PDF','xls'=>'XLS','html'=>'HTML', 'rtf'=>'RTF']);
            $output_type->setValue('pdf');
            $output_type->setLayout('horizontal');
            $output_type->setUseButton();
            
            $e1 = new TDBSeekButton('e1', 'gexpertlotes', $this->form->getName(), 'Empresas', 'RAZAO', 'e1');
            $e1aux = new THidden('e1aux');
            $e1->setAuxiliar($e1aux);
            
            $e2 = new TDBSeekButton('e2', 'gexpertlotes', $this->form->getName(), 'Empresas', 'RAZAO', 'e2');
            $e2aux = new THidden('e2aux');
            $e2->setAuxiliar($e2aux);
            
            $e1->setSize(TWgtSizes::wsInt);
            $e2->setSize(TWgtSizes::wsInt);
            
            $this->form->addFields([new TLabel('Empresas de')],[$e1, new TLabel('&nbsp&nbspa'), $e2]);
            
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
            
             /* @ Comando SQL e clausula Where */ 
            $sql     = " select codigo, 
                                razao, 
                                fantasia, 
                                cnpj, 
                                (select nome from municipios where codigo=empresas.cidade and uf=empresas.uf) cidade,
                                (select sigla from estados where codigo=empresas.uf) uf
                           from empresas "." where (1=1) ";
            $where   = " and codigo between {$pe1} and {$pe2} ";
            $orderby = " order by 1";
            
             $this->reportdir = 'app/output/'.__CLASS__.'.'.$data->output_type;
                        
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
            
            $this->report->addStyle('header', 'Times', '15', 'B', '#000000', '#ffffff');
            $this->report->addStyle('title', 'Arial', '9', '',    '#000000', '#ffffff');
            $this->report->addStyle('datap', 'Arial', '7', '',    '#000000', '#E3E3E3', 'LR');
            $this->report->addStyle('datai', 'Arial', '7', '',    '#000000', '#ffffff', 'LR');
            $this->report->addStyle('footer', 'Times', '8', '', '#000000', '#ffffff');
            
            $color = FALSE;           
            $style = 'datap';
                           
            $this->report->setHeaderCallback(function()
            {
                $this->report->addRow();
                $this->report->addCell($this->title, 'center', 'header', count($this->cols));
                
                /* @ Titulos das Colunas */
                $this->report->addRow();
                $this->report->addCell('Cód', 'left', 'title');
                $this->report->addCell('Razão', 'left', 'title');
                $this->report->addCell('Fantasia', 'left', 'title');
                $this->report->addCell('Cnpj', 'left', 'title');
                $this->report->addCell('Cidade', 'left', 'title');
                $this->report->addCell('UF', 'left', 'title');
               
            });
            
            $this->report->setFooterCallback(function()
            {
                $this->report->addRow();
                $this->report->addCell('Usuário: '.TSession::getValue('username').', Data/Hora Impressão: '.date('d/m/Y h:i:s'), 'right', 'footer', count($this->cols));
            });
            
            TTransaction::open('gexpertlotes');
            $database = TTransaction::get();
            $records = $database->query($sql.$where.$orderby)->fetchAll();
            
            foreach($records as $recno)
            {
               $style = $color?'datap':'datai';
               
               $this->report->addRow();
               $this->report->addCell($recno["codigo"], 'left', $style);
               $this->report->addCell($recno["razao"], 'left', $style);
               $this->report->addCell($recno["fantasia"], 'left', $style);
               $this->report->addCell($recno["cnpj"], 'left', $style);
               $this->report->addCell($recno["cidade"], 'left', $style);
               $this->report->addCell($recno["uf"], 'left', $style);
               
               $color = !$color; 
            }
            
            $this->report->save($this->reportdir);
            parent::openFile($this->reportdir);
            $this->form->setData($data);          
        }
        
    }
?>
