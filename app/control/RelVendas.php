<?php
    class RelVendas extends RelatorioModel
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
                TUtil::str_to_field('emissao','Emissão',80, 'left'),
                TUtil::str_to_field('entidade','Cód',50, 'left'),
                TUtil::str_to_field('dsentidade','Razão',180, 'left'),
                TUtil::str_to_field('quadra','Qda',40, 'left'),
                TUtil::str_to_field('lote','Lte',40, 'left'),
                TUtil::str_to_field('valor','Valor',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('contrato','Nr. Ctt',60, 'left'),
                TUtil::str_to_field('parcelas','Nr Parc',40, 'left'),
                TUtil::str_to_field('valorparcela','Vl Parc',80, 'right', null, 'double'),
                TUtil::str_to_field('cancelado','Distr',40, 'left'),
                TUtil::str_to_field('cancelamento','Dt Distr',80, 'left')
            );
            
            $this->group = array(
                TUtil::str_to_group('empreendimento', 'Totais do empreendimento', 'Empreendimento')
            );
            
            $this->props['title'] = 'Relatório de Vendas';
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
            $this->form->addFields([new TLabel('Data de')],[$d1, 'a', $d2]);
            $this->form->addFields([new TLabel('Cancelados')],[$d]);
                         
               
        }
        
        public function onGenerate($param = null)
        {
           
           $e  = $param['e'];
           $c  = $param['c'];
           $d  = $param['d'];
           $t1 = empty($param['t1'])?1:$param['t1'];
           $t2 = empty($param['t2'])?9999:$param['t2'];
           $q1 = empty($param['q1'])?1:$param['q1'];
           $q2 = empty($param['q2'])?9999:$param['q2'];
           $l1 = empty($param['l1'])?1:$param['l1'];
           $l2 = empty($param['l2'])?9999:$param['l2'];
           $d1 = empty($param['d1'])?'1899-12-31':$param['d1'];
           $d2 = empty($param['d2'])?'2100-12-31':$param['d2'];          
           
           $sql = "SELECT 
                            empresa,
                            empreendimento,
                            lancamento,
                            date_format(emissao, '%d/%m/%y') emissao,
                            entidade,
                            (SELECT 
                                    razao
                                FROM
                                    entidades
                                WHERE
                                    codigo = vendas.entidade) dsentidade,
                            quadra,
                            lote,
                            valor,
                            contrato,
                            parcelas,
                            valorparcela,
                            case when cancelado = 'S' then 'Sim' else 'Não' end cancelado,
                            date_format(cancelamento, '%d/%m/%y') cancelamento
                        FROM
                            vendas
                        WHERE
                            empresa = {$e}
                                AND empreendimento BETWEEN {$t1} AND {$t2}
                                ".(!empty($c)?"AND entidade = {$c}":"")."
                                AND emissao BETWEEN '{$d1}' AND '{$d2}'
                                AND quadra BETWEEN {$q1} AND {$q2}
                                AND lote BETWEEN {$l1} AND {$l2}
                                ".(!empty($d)?"AND cancelado = '{$d}'":"")."
                        ORDER BY 1 , 2 , 4
                        ";
           
           $param['q'] = $sql;
          
          parent::onGenerate($param);  
        }
    }
?>
