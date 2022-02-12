<?php
    class RelEmpreendimentos extends TPage 
    {
        protected $form;
        protected $where;        
        protected $dirpdf;
        protected $cols;     
        
        function __construct()
        {
            parent::__construct();
            
            $this->dirpdf = "app/output/%s.pdf";            
            $this->cols = array(40,40,250,80,80,150,25);
            
            $this->form = new BootstrapFormBuilder('form_RelEmpreendimentos');
            $this->form->setFormTitle('Relatório de Cadastro de Empreendimentos');
            
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
            
            $e1->setSize(TWgtSizes::wsInt);
            $e2->setSize(TWgtSizes::wsInt);
            $t1->setSize(TWgtSizes::wsInt);
            $t2->setSize(TWgtSizes::wsInt);
            
            $this->form->addFields([new TLabel('Empresas de')],[$e1, new TLabel('&nbsp&nbspa'), $e2]);
            $this->form->addFields([new TLabel('Empreendimentos de')],[$t1, new TLabel('&nbsp&nbspa'), $t2]);
            
            $this->form->addAction('Gerar', new TAction([$this, 'onGenerate']), 'fa:download blue'); 
            
            $content = new TVBox();
            $content->style = 'overflow-x: auto';
            $content->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $content->add($this->form);
            
            parent::add($content);
            
        }
        
        public function onGenerate()
        {
            $data = $this->form->getData();
            
            $pe1 = (!empty($data->e1))?$data->e1:'1';
            $pe2 = (!empty($data->e2))?$data->e2:'9999';
            $pt1 = (!empty($data->t1))?$data->t1:'1';
            $pt2 = (!empty($data->t2))?$data->t2:'9999';
            
            TTransaction::open('gexpertlotes');
            
            $con = TTransaction::get();
            
            $records = $con->query("select e.codigo,
                                        e.empresa,
                                        e.descricao,
                                        e.areatotal,
                                        e.vlraquisicao,
                                        (select nome from municipios where codigo=e.cidade and uf=e.uf) cidade,
                                        (select sigla from estados where codigo=e.uf) uf 
                                   from empreendimentos e 
                                  where codigo between {$pt1} and {$pt2} 
                                    and empresa between {$pe1} and {$pe2}")->fetchAll();                       
            
            $pdf = new TTableWriterPDF($this->cols);
            
            $pdf->addStyle('header', 'Times', '15', 'B', '#000000', '#ffffff');
            $pdf->addStyle('title', 'Arial', '9', '',    '#000000', '#ffffff');
            $pdf->addStyle('datap', 'Arial', '7', '',    '#000000', '#E3E3E3', 'LR');
            $pdf->addStyle('datai', 'Arial', '7', '',    '#000000', '#ffffff', 'LR');
            $pdf->addStyle('footer', 'Times', '8', '', '#000000', '#ffffff');
           
            
            $pdf->setHeaderCallback(function($pdf)
            {
                $pdf->addRow();
                $pdf->addCell('Cadastro de Emprendimentos', 'center', 'header',count($this->cols));
                
                $pdf->addRow();   
                $pdf->addCell('Cód', 'left', 'title');
                $pdf->addCell('Emp', 'left', 'title');
                $pdf->addCell('Descrição', 'left', 'title');
                $pdf->addCell('Mts²', 'left', 'title');
                $pdf->addCell('Vl. Aqs', 'left', 'title');
                $pdf->addCell('Cidade', 'left', 'title');
                $pdf->addCell('Uf', 'left', 'title');   
            });
            
            $pdf->setFooterCallback(function($pdf)
            {
                $pdf->addRow();
                $pdf->addCell('Usuário: '.TSession::getValue('username').', Data/Hora Impressão: '.date('d/m/Y h:i:s'), 'right', 'footer', count($this->cols));
            });
            
            $color = FALSE;
               
            foreach($records as $recno)
            {
               $style = $color?'datap':'datai';
               
               $pdf->addRow();
               $pdf->addCell($recno["codigo"], 'Left', $style);
               $pdf->addCell($recno["empresa"], 'Left', $style);
               $pdf->addCell($recno["descricao"], 'Left', $style);
               $pdf->addCell(TConversion::asDoubleBR($recno["areatotal"],5), 'Left', $style);
               $pdf->addCell(TConversion::asDoubleBR($recno["vlraquisicao"],2), 'Left', $style);
               $pdf->addCell($recno["cidade"], 'Left', $style);
               $pdf->addCell($recno["uf"], 'Left', $style);
               
               $color = !$color; 
            }
            
            $pdf->save(sprintf($this->dirpdf,__CLASS__));
            parent::openFile(sprintf($this->dirpdf,__CLASS__)); 
            
            $this->form->setData($data);   
            
        }
        
    }
?>