<?php
    class RelInfraestrutura extends RelatorioModel
    {
        
        public $props;
        public $fields;
        public $fieldsstr;
        public $empresaid;
        public $empresacriteria;
        
        function __construct($param = NULL)
        {
            
            $this->empresaid         = TSession::getValue('userunitid');
            $this->empresacriteria   = new TCriteria();
            $this->empresacriteria->add(new TFilter('EMPRESA','=',$this->empresaid)); 
            
            $this->fields = array(
                                TUtil::str_to_field('empresa', 'Empresa', 40, 'left', 'count'),
                                TUtil::str_to_field('empreendimento', 'Empreend.', 40, 'left'),
                                TUtil::str_to_field('lancamento', 'Lcto', 40, 'left'),
                                TUtil::str_to_field('data', 'Data', 60, 'left', null, 'date'),
                                TUtil::str_to_field('valor', 'Valor', 60, 'left', 'sum', 'double'),
                                TUtil::str_to_field('quadra', 'Quadra', 40, 'left'),
                                TUtil::str_to_field('lote', 'Lote', 40, 'left'),
                                TUtil::str_to_field('rateio', 'Vlr Rateio', 60, 'left', 'sum', 'double'),
                                TUtil::str_to_field('observacao', 'obs', 200, 'left')
                            );
            
            $this->props['title']     = 'Relatório Infraestrutura';
            $this->props['fields']    = $this->fields;
            $this->props['group']     = array(                                            
                                            ['field'=>'quadra', 'footer'=>'Totais Quadra', 'header'=>'Quadra'],
                                            ['field'=>'empreendimento', 'footer'=>'Totais Empreendimento', 'header'=>'Empreendimento']
                                        ); 
            
            $this->fieldsstr = implode(',', array_column($this->fields,'name'));         
            
            parent::__construct($this->props);
            
            $e = new THidden('e');
            $e->setValue($this->empresaid);
            $t1 = new TDBSeekButton('t1','gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't1', null, $this->empresacriteria);
            $ht1 = new THidden('ht1');
            $t1->setAuxiliar($ht1);
            $t1->setSize(TWgtSizes::wsInt);
            $t2 = new TDBSeekButton('t2','gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't2', null, $this->empresacriteria);
            $ht2 = new THidden('ht2');
            $t2->setAuxiliar($ht2);
            $t2->setSize(TWgtSizes::wsInt);    
            $q1 = new TEntry('q1');
            $q1->setSize(TWgtSizes::wsInt);
            $q2 = new TEntry('q2');
            $q2->setSize(TWgtSizes::wsInt);
            $l1 = new TEntry('l1');
            $l1->setSize(TWgtSizes::wsInt);
            $l2 = new TEntry('l2');
            $l2->setSize(TWgtSizes::wsInt);
            $d1 = new TDate('d1');
            $d1->setMask('dd/mm/yyyy');
            $d1->setDatabaseMask('yyyy-mm-dd');
            $d1->setSize(TWgtSizes::wsDate);
            $d2 = new TDate('d2');
            $d2->setMask('dd/mm/yyyy');
            $d2->setDatabaseMask('yyyy-mm-dd');
            $d2->setSize(TWgtSizes::wsDate);
            
            $this->form->addFields([],[$e]);
            $this->form->addFields([new TLabel('Empreendimentos de')],[$t1, '&nbsp&nbsp&nbspa', $t2]);
            $this->form->addFields([new TLabel('Quadras de')],[$q1, '&nbspa', $q2]);
            $this->form->addFields([new TLabel('Lotes de')],[$l1, '&nbspa', $l2]);
            $this->form->addFields([new TLabel('Data de')],[$d1, '&nbspa', $d2]);
              
        }
        
        public function onGenerate($param = NULL)
        {
           $pe     = TUtil::coalesce($param['e'],'-1');           
           $pt1    = TUtil::coalesce($param['t1'],'1');
           $pt2    = TUtil::coalesce($param['t2'],'9999');
           $pq1    = TUtil::coalesce($param['q1'],'1');
           $pq2    = TUtil::coalesce($param['q2'],'9999');
           $pl1    = TUtil::coalesce($param['l1'],'1');
           $pl2    = TUtil::coalesce($param['l2'],'9999');
           $pd1    = TUtil::coalesce(TConversion::asSQLDate($param['d1']),'1899-12-31');
           $pd2    = TUtil::coalesce(TConversion::asSQLDate($param['d2']),'2100-12-31'); 
           
           $sql = "select  empresa as empresa,	
                           empreendimento as empreendimento,
                           lancamento as lancamento,
                           data as data,
                           valor as valor,
                           quadra as quadra,
                           lote as lote,
                           rateio as rateio,
                    	   observacao as observacao
                      from sel_infraestrutura_lotes 
                     where empresa = {$pe}
                       and empreendimento between {$pt1} and {$pt2}
                       and quadra between {$pq1} and {$pq2}
                       and lote between {$pl1} and {$pl2}
                       and data between '{$pd1}' and '{$pd2}'
                       order by 1,2,6,7,4"; 
            
            $param['q'] = $sql;
            
            parent::onGenerate($param);
        
        }
        
    } 
?>
