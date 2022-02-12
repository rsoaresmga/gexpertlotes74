<?php

    class RelApuracao extends RelatorioModel
    {
        public $form;
        public $filterEmpresa;
        public $empresalogada;
        public $fields;
        public $group;
        public $props;

        public function __construct($param = null)
        {
            $this->empresalogada = TSession::getValue('userunitid');
            
            $this->filterEmpresa = new TCriteria();
            $this->filterEmpresa->add(new TFilter('EMPRESA','=', $this->empresalogada));
            
            $this->fields = array(
                TUtil::str_to_field('EMPREENDIMENTO','Empto',40, 'left'),
                TUtil::str_to_field('QUADRA','Qda',30, 'left'),
                TUtil::str_to_field('LOTE','Lte',30, 'left'),
                TUtil::str_to_field('MES','Mês',60, 'left'),
                TUtil::str_to_field('VLRCUSTOLOTE','Ct',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('PERRECEBACUMUL','%Rec Ac',80, 'right'),
                TUtil::str_to_field('VLRINFRAACUMULADA','Inf Ac',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('VLRRECEBACUMULADO','Rec Ac',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('PERRECEBMES','%Rec Mes',80, 'right'),
                TUtil::str_to_field('VLRINFRAMES','Inf Mês',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('VLRCUSTOPROP','Ct Prop',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('VLRCUSTOAPROP','Ct Aprop',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('VLRCUSTOLP','Cust Lp',80, 'right', 'sum', 'double'),
                TUtil::str_to_field('VLRCUSTOCP','Cust Cp',80, 'right', 'sum', 'double')
            );
            
            $this->group = array(
                    
                    TUtil::str_to_group('LOTE', 'Totais do lote', 'Lote'),
                    TUtil::str_to_group('QUADRA', 'Totais da quadra', 'Quadra'),
                    TUtil::str_to_group('EMPREENDIMENTO', 'Totais do empreendimento', 'Empreendimento')                                      
            );
            
            $this->props['title']     = 'Relatório de Apuração';
            $this->props['fields']    = $this->fields;
            $this->props['group']     = $this->group;
            
            parent::__construct($this->props);
            
            $e = new THidden('e');
            $e->setValue($this->empresalogada);
            $empto1 = new TDBSeekButton('empto1', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', null, null, $this->filterEmpresa);  
            $empto1aux = new THidden('empto1aux');
            $empto1->setAuxiliar($empto1aux);
            $empto2 = new TDBSeekButton('empto2', 'gexpertlotes', $this->form->getName(), 'Empreendimentos', 'DESCRICAO', null, null, $this->filterEmpresa);            
            $empto2aux = new THidden('empto2aux');
            $empto2->setAuxiliar($empto2aux);
            $qda1 = new TEntry('qda1');
            $qda2 = new TEntry('qda2');
            $lte1 = new TEntry('lte1');
            $lte2 = new TEntry('lte2');
            $mes1 = new TDate('mes1');
            $mes1->setMask('dd/mm/yyyy');
            $mes1->setDatabaseMask('yyyy-mm-dd');
            $mes2 = new TDate('mes2');
            $mes2->setMask('dd/mm/yyyy');
            $mes2->setDatabaseMask('yyyy-mm-dd');
            
            $empto1->setSize(TWgtSizes::wsInt);
            $empto2->setSize(TWgtSizes::wsInt);
            $qda1->setSize(TWgtSizes::wsInt);
            $qda2->setSize(TWgtSizes::wsInt);
            $lte1->setSize(TWgtSizes::wsInt);
            $lte2->setSize(TWgtSizes::wsInt);  
            $mes1->setSize(TWgtSizes::wsDate);
            $mes2->setSize(TWgtSizes::wsDate);         
            
            $this->form->addFields([],[$e]);
            $this->form->addFields([new TLabel('Empreendimentos de')], [$empto1, '&nbsp&nbspa', $empto2]);
            $this->form->addFields([new TLabel('Quadras de')], [$qda1, '&nbspa', $qda2]);
            $this->form->addFields([new TLabel('Lotes de')], [$lte1, '&nbspa', $lte2]);
            $this->form->addFields([new TLabel('Competencia de')],[$mes1, '&nbspa', $mes2]);

        }
        
        public function onGenerate($param = null)
        {
             $e     = $param['e'];
             $t1    = (empty($param['empto1'])?1:$param['empto1']);
             $t2    = (empty($param['empto2'])?9999:$param['empto2']);
             $q1    = (empty($param['qda1'])?1:$param['qda1']);
             $q2    = (empty($param['qda2'])?9999:$param['qda2']);
             $l1    = (empty($param['lte1'])?1:$param['lte1']);
             $l2    = (empty($param['lte2'])?9999:$param['lte2']);
             $m1    = (empty($param['mes1'])?'1899-12-31':TConversion::asSQLDate($param['mes1']));
             $m2    = (empty($param['mes2'])?'2100-12-31':TConversion::asSQLDate($param['mes2']));
             
             $sql = "SELECT 
                        EMPREENDIMENTO,
                        QUADRA,
                        LOTE,
                        DATE_FORMAT(MES, '%d/%m/%y') MES,
                        PERRECEBACUMUL,
                        PERRECEBMES,
                        VLRCUSTOLOTE,
                        VLRINFRAACUMULADA,
                        VLRRECEBACUMULADO,
                        VLRINFRAMES,
                        VLRCUSTOAPROP,
                        VLRCUSTOPROP,
                        VLRCUSTOCP,
                        VLRCUSTOLP
                    FROM
                        apuracao
                    WHERE
                        EMPRESA = {$e}
                            AND EMPREENDIMENTO BETWEEN {$t1} AND {$t2}
                            AND QUADRA BETWEEN {$q1} AND {$q2}
                            AND LOTE BETWEEN {$l1} AND {$l2}
                            AND MES BETWEEN '{$m1}' AND '{$m2}'
                    ORDER BY 1 , 2 , 3 , 4";
               
              $param['q'] = $sql;
                    
              parent::onGenerate($param);                     
        }
    }

?>