<?php
 class RelVendasParcelas extends RelatorioModel
    {
        public $fields;
        public $props;
        public $group;
        public $empresaid;
        public $criteria;        
        
        public function __construct($param = null)
        {
            
            $this->empresaid = TSession::getValue('userunitid');
            $this->criteria = new TCriteria();
            $this->criteria->add(new TFilter('EMPRESA', '=', $this->empresaid));
            
            
            $this->fields = array(
                TUtil::str_to_field('empresa','Emp',50, 'left', 'count'),
                TUtil::str_to_field('empreendimento','Empto',50, 'left'),
                TUtil::str_to_field('lancamento','Lcto',50, 'left'),
                TUtil::str_to_field('entidade','Cód',50, 'left'),
                TUtil::str_to_field('razao','Razão',150, 'left'),
                TUtil::str_to_field('quadra','Qda',40, 'center'),
                TUtil::str_to_field('lote','Lte',40, 'center'),
                TUtil::str_to_field('emissao','Emissão',60, 'left', null, 'date'),
                TUtil::str_to_field('vlvenda','Vl Vda',80, 'right', null, 'double'),
                TUtil::str_to_field('parcela','Parc',40, 'left'),
                TUtil::str_to_field('vencimento','Vencto',60, 'left', null, 'date'),
                TUtil::str_to_field('vlparcela','Vl Parc',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('vlsaldo','Vl Saldo',80, 'right', 'sum', 'double')
            );
            
            $this->group = array(
                TUtil::str_to_group('lancamento', 'Totais da venda', 'Venda'),
                TUtil::str_to_group('empreendimento', 'Totais do empreendimento', 'Empreendimento')
            );
            
            $this->props['title'] = 'Relatório de Vendas Parcelas';
            $this->props['fields'] = $this->fields; 
            $this->props['group'] = $this->group;
       
            parent::__construct($this->props); 
            
            $e = new THidden('e');
            $e->setValue($this->empresaid);
            
            $t1 = new TDBSeekButton('t1', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't1', null, $this->criteria);
            $t1->setAuxiliar(new THidden('t1aux'));
            $t2 = new TDBSeekButton('t2', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 't2', null, $this->criteria);
            $t2->setAuxiliar(new THidden('t2aux'));
            $c  = new TDBSeekButton('c', 'gexpertlotes', $this->form->getName(), 'Entidades', 'RAZAO', 'c');
            $caux = new TEntry('caux');
            $caux->setEditable(FALSE);
            $c->setAuxiliar($caux);            
            $q1 = new TEntry('q1');
            $q2 = new TEntry('q2');
            $l1 = new TEntry('l1');
            $l2 = new TEntry('l2');
            $p1 = new TEntry('p1');
            $p2 = new TEntry('p2');
            $d1 = new TDate('d1');
            $d1->setMask('dd/mm/yyyy');
            $d1->setDatabaseMask('yyyy-mm-dd');
            $d2 = new TDate('d2');
            $d2->setMask('dd/mm/yyyy');
            $d2->setDatabaseMask('yyyy-mm-dd');
            $d  = new TCombo('d');
            $d->addItems(['N'=>'Não', 'S'=>'Sim']);
            
            $t1->setSize(TWgtSizes::wsInt);
            $t2->setSize(TWgtSizes::wsInt);
            $c->setSize(TWgtSizes::wsInt);
            $caux->setSize(TWgtSizes::wsAux);
            $q1->setSize(TWgtSizes::wsInt);
            $q2->setSize(TWgtSizes::wsInt);
            $l1->setSize(TWgtSizes::wsInt);
            $l2->setSize(TWgtSizes::wsInt);
            $d1->setSize(TWgtSizes::wsDate);
            $d2->setSize(TWgtSizes::wsDate);
            $d->setSize(TWgtSizes::wsDouble);
            
            $this->form->addFields([],[$e]);
            $this->form->addFields([new TLabel('Empreendimentos de')],[$t1, 'a' ,$t2]);             
            $this->form->addFields([new TLabel('Cliente')],[$c]);
            $this->form->addFields([new TLabel('Quadras de')],[$q1, 'a', $q2]);
            $this->form->addFields([new TLabel('Lotes de')],[$l1, 'a', $l2]);
            $this->form->addFields([new TLabel('Parcelas de')],[$p1, 'a', $p2]);
            $this->form->addFields([new TLabel('Data de')],[$d1, 'a', $d2]);
            
                         
               
        }
        
        public function onGenerate($param = null)
        {
           
           $e  = $param['e'];
           $c  = $param['c'];
           $t1 = empty($param['t1'])?1:$param['t1'];
           $t2 = empty($param['t2'])?9999:$param['t2'];
           $q1 = empty($param['q1'])?1:$param['q1'];
           $q2 = empty($param['q2'])?9999:$param['q2'];
           $l1 = empty($param['l1'])?1:$param['l1'];
           $l2 = empty($param['l2'])?9999:$param['l2'];
           $d1 = empty($param['d1'])?'1899-12-31':TConversion::asSQLDate($param['d1']);
           $d2 = empty($param['d2'])?'2100-12-31':TConversion::asSQLDate($param['d2']);    
           $p1 = empty($param['p1'])?1:$param['p1'];
           $p2 = empty($param['p2'])?9999:$param['p2'];           
           
           $sql = "SELECT 
                        v.empresa,
                        v.empreendimento,
                        v.lancamento,
                        v.entidade,
                        p.razao,
                        v.quadra,
                        v.lote,
                        v.emissao,
                        v.valor vlvenda,
                        vp.parcela,
                        vp.vencimento,
                        vp.valor vlparcela,
                        vp.saldo vlsaldo
                    FROM
                        vendas v
                            INNER JOIN
                        vendas_parcelas vp ON (v.EMPRESA = vp.EMPRESA
                            AND v.LANCAMENTO = vp.VENDA)
                            INNER JOIN
                        entidades p ON (p.CODIGO = v.ENTIDADE)
                    WHERE
                        vp.EMPRESA = {$e}
                            AND v.EMPREENDIMENTO BETWEEN {$t1} AND {$t2}
                            AND v.QUADRA BETWEEN {$q1} AND {$q2}
                            AND v.LOTE BETWEEN {$l1} AND {$l2}
                            AND vp.PARCELA BETWEEN {$p1} AND {$p2}
                            AND vp.VENCIMENTO BETWEEN '{$d1}' AND '{$d2}'
                            ".(!empty($c)?"AND v.ENTIDADE = {$c}":'')."
                    ORDER BY 1 , 2 , 6 , 7 , 11
                    ";
           
           $param['q'] = $sql;
          
          parent::onGenerate($param);  
        }
    }
?>
