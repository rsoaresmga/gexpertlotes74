<?php
    class RelPlano extends TPage
    {
        protected $form;
        protected $reportdir;
        protected $report;
        protected $cols;
        protected $title;
        
        public function __construct()
        {
            parent::__construct();
            
            
            /* @ Titulo do Form e Relatorio */
            $this->title  = 'Cadastro Plano de Contas';
            
            /* @ Tamanho das colunas */
            $this->cols   = array(20,30,50,20,100,300); 
            
            $this->reportdir = 'app/output/'.__CLASS__.'.pdf';                        
            $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
            $this->form->setFormTitle($this->title);            
            
            $output_type = new TRadioGroup('output_type');
            $output_type->addItems(['pdf'=>'PDF','xls'=>'XLS','html'=>'HTML', 'rtf'=>'RTF']);
            $output_type->setValue('pdf');
            $output_type->setLayout('horizontal');
            $output_type->setUseButton();
            
                        
            /* @ Campos do Form */
            $e1 = new TDBSeekButton('e1', 'gexpertlotes', $this->form->getName(), 'Empresas', 'RAZAO', 'e1');
            $e1->setAuxiliar(new THidden('e1a'));           
            $e2 = new TDBSeekButton('e2', 'gexpertlotes', $this->form->getName(), 'Empresas', 'RAZAO', 'e2');
            $e2->setAuxiliar(new THidden('e2a'));
            
            $e1->setSize(TWgtSizes::wsInt);
            $e2->setSize(TWgtSizes::wsInt);
            
            $this->form->addFields([new TLabel('Empresas de:')], [$e1, '&nbsp&nbsp a', $e2]);
            
            /* @ Fim Campos do Form */ 
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
            
            /* @ Variaveis GET */
            $pe1 = !empty($data->e1)?$data->e1:'1';
            $pe2 = !empty($data->e2)?$data->e2:'9999';
            
            /* @ Comando SQL e clausula Where */ 
            $sql     = "select empresa, codigo, grupo, case when natureza = -1 then 'Créd' else 'Déb' end natureza, classificacao, descricao from plano"." where (1=1)";
            $where   = " and empresa between {$pe1} and {$pe2} ";
            $orderby = " order by 1,5 ";
            
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
                $this->report->addCell('Emp', 'left', 'title');
                $this->report->addCell('Cod', 'left', 'title');
                $this->report->addCell('Natureza', 'left', 'title');
                $this->report->addCell('Grupo', 'left', 'title');
                $this->report->addCell('Classificação', 'left', 'title');
                $this->report->addCell('Descrição', 'left', 'title');
               
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
                
                /* @ Valores das Colunas */                
                $this->report->addRow();
                $this->report->addCell($recno['empresa'], 'left', $style);
                $this->report->addCell($recno['codigo'], 'left', $style);
                $this->report->addCell($recno['natureza'], 'left', $style);
                $this->report->addCell($recno['grupo'], 'left', $style);
                $this->report->addCell($recno['classificacao'], 'left', $style);
                $this->report->addCell($recno['descricao'], 'left', $style);
                
                
                $color = !$color;
            }
                         
            $this->report->save($this->reportdir);
            parent::openFile($this->reportdir);
            $this->form->setData($data);    
        }
        
    }
?>