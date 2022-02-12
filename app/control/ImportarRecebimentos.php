<?php
    class ImportarRecebimentos extends TPage
    {
        public $form;
        public $criteria;
        public $file;
        
        public function __construct()
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder('form_ImportarRecebimentos');
            $this->form->setFormTitle('Importação de Recebimentos');
            $this->form->style = 'width: 100%';
            
            $this->criteria = new TCriteria();
            
            
            $e = new THidden('e');
            $e->setValue(TSession::getValue('userunitid'));
            $this->criteria->add(new TFilter('EMPRESA','=', $e->getValue('-1')));
            $t  = new TDBSeekButton('t', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't', null, $this->criteria);
            $a  = new TEntry('a');
            $t->setAuxiliar($a);            
            $d  = new TDate('d');
            $f  = new TFile('f');
            $f->setAllowedExtensions(['csv', 'txt']);
            $c = new TCombo('c');
            $c->addItems(['S'=>'Sim', 'N'=>'Não']);
            $c->setValue('S');
                               
            $t->setSize(TWgtSizes::wsInt);
            $a->setSize(TWgtSizes::wsAux);
            $d->setSize(TWgtSizes::wsDate);
            $c->setSize(TWgtSizes::wsInt);  
            
            $this->form->addFields([new TLabel('Empreendimento', 'red')],[$t]);
            $this->form->addFields([new TLabel('Data Recebimento')],[$d]);
            $this->form->addFields([new TLabel('Arquivo de Recebimentos', 'red')],[$f]);
            $this->form->addFields([new TLabel('Cabeçalho na primeira linha?')],[$c]);
            $this->form->addFields([],[$e]);
            
            $t->addValidation('"Empreendimento"', new TRequiredValidator);
            $f->addValidation('"Arquivo de Recebimento"', new TRequiredValidator);
            
            $this->form->addAction('Executar', new TAction([$this, 'onExecutarClick']), 'fa:play red');
            $this->form->addAction('Download Layout', new TAction([$this, 'onDownloadClick']), 'fas:cloud-download-alt green');
            
            $container = new TVBox();
            $container->style = 'width: 90%; overflow-x: auto';          
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($this->form);
            
            parent::add($container);
            
        }
        
        public function onExecutarClick($param)
        {
            try
            {
                    $this->form->validate();
                    
                    
                    $e_ = $param['e'];
                    $t_ = $param['t'];
                    $d_ = $param['d'];
                    $f_ = $param['f'];
                    $c_ = $param['c'];
                    $u_ = TSession::getValue('userid');
                   
                    
                    $this->file = new TReadCsv('tmp/'.$f_);
                    $this->file->set_delim(";");
    
                    TTransaction::open('gexpertlotes');
                    
                    $recNo = 0;                        
                    
                    foreach($this->file->abre() as $r)
                    {
                       
                        $quadra = $r[0];
                        $lote = $r[1];
                        $parc = $r[2];
                        $dtpagto = $r[3];
                        $atualiza = TConversion::asDouble($r[4]);
                        $multa = TConversion::asDouble($r[5]);
                        $juros = TConversion::asDouble($r[6]);
                        $desconto = TConversion::asDouble($r[7]);
                        $ctactb = TConversion::asDouble($r[8]);
                        $vlpagto = TConversion::asDouble($r[9]);
                        
                        $total = $vlpagto+$juros+$multa+$atualiza-$desconto; 
                        
                        if(!empty($d_))
                        {
                            $dtpagto = TConversion::asSQLDate($d_);
                        }
                        else
                        {
                            $dtpagto = TConversion::asSQLDate($dtpagto);
                        }
                       
                        if (($c_=='S' && $recNo == 0)!=TRUE)
                        {
                            $sqlGetID = "SELECT VP.VENDA,
                                                VP.ID,
                                                VP.VALOR
                                          FROM vendas V
                                         INNER JOIN vendas_parcelas VP
                                            ON (VP.VENDA = V.LANCAMENTO)
                                         WHERE V.EMPREENDIMENTO = {$t_}
                                           AND V.EMPRESA = {$e_}
                                           AND V.QUADRA = {$quadra}
                                           AND V.LOTE = {$lote}
                                           AND VP.PARCELA = {$parc}
                                           AND COALESCE(V.CANCELADO,'N') = 'N'
                                         ORDER BY V.LANCAMENTO DESC
                                         LIMIT 1";                              
                            
                            $conn =  TTransaction::get();
                            $qry = $conn->query($sqlGetID);
                            
                            foreach($qry as $p)
                            {
                               if (!empty($p[0])&&!empty($p[1]))
                               { 
                                        $sqlInsert = "INSERT INTO vendas_parcelas_baixas
                                                        (EMPRESA,
                                                        VENDA,
                                                        PARCELA,
                                                        RECEBIMENTO,
                                                        VALOR,
                                                        JUROS,
                                                        MULTA,
                                                        DESCONTO,
                                                        TOTAL,
                                                        USUARIOCAD,
                                                        DATACAD,
                                                        CONTACTBPAGTO,
                                                        ATUALIZACAO)
                                                        VALUES
                                                        (
                                                        {$e_},
                                                        {$p[0]},
                                                        {$p[1]},
                                                        '{$dtpagto}',
                                                        {$vlpagto},
                                                        {$juros},
                                                        {$multa},
                                                        {$desconto},
                                                        {$total},
                                                        {$u_},
                                                        current_timestamp,
                                                        {$ctactb},
                                                        {$atualiza});";
                                                        
                                        $qry = $conn->query($sqlInsert); 
                                 }                                                                              
                            }
                        }
                        
                        $recNo+= 1;       
                    }
                    
                    TTransaction::close();
                    
                    new TMessage('info', 'Importação concluída!');
                                                          
            } 
            catch (Exception $err)
            {
                new TMessage('warning', $err->getMessage());
                TTransaction::rollback(); 
            }
            
        }
        
        
        public function onDownloadClick($param)
        {
            TPage::openFile('layouts/layout-recebimentos.csv');
        }
    }
?>
