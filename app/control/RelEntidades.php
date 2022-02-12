<?php
    
    class RelEntidades extends RelatorioModel
    {    
        public $props;
        public $fields;
        public $fieldsstr;
    
        public function __construct()
        {
            
            $this->fields = array(
                                    array('name'=>'codigo','caption'=>'Cód','size'=>40, 'align'=>'left','total'=>'count'),
                                    array('name'=>'razao','caption'=>'Razão','size'=>250, 'align'=>'left'),
                                    array('name'=>'fantasia','caption'=>'Fantasia','size'=>200, 'align'=>'left'),
                                    array('name'=>'cnpjcpf','caption'=>'Cnpj/Cpf','size'=>90, 'align'=>'left'),
                                    array('name'=>'cidade','caption'=>'Cidade','size'=>60, 'align'=>'left'),
                                    array('name'=>'estado','caption'=>'Uf','size'=>25, 'align'=>'left')
            ); 
            
            
            $this->props['title']  = 'Cadastro Entidades';
            $this->props['fields'] = $this->fields;           
            $this->props['group'] = array(['field'=>'cidade','footer'=>'Totais Cidade', 'header'=>'Cidade'],
                                          ['field'=>'estado','footer'=>'Totais UF', 'header'=>'UF']);
            
            $this->fieldsstr       = implode(',', array_column($this->fields,'name'));
            
            parent::__construct($this->props);
            
            $c1 = new TDBSeekButton('c1', 'gexpertlotes', $this->form->getName(), 'Entidades', 'RAZAO', 'c1'); 
            $c1->setAuxiliar(new THidden('c1aux'));
            $c2 = new TDBSeekButton('c2', 'gexpertlotes', $this->form->getName(), 'Entidades', 'RAZAO', 'c2'); 
            $c2->setAuxiliar(new THidden('c2aux'));
            $tp = new TCombo('tp');
            $tp->addItems(["'F'"=>'Física', "'J'"=>'Jurídica']);
            
            
            $c1->setSize(TWgtSizes::wsInt);
            $c2->setSize(TWgtSizes::wsInt);
            $tp->setSize(TWgtSizes::ws20);
            
            $this->form->addFields([new TLabel('Códigos de ')],[$c1, '&nbsp&nbspa' ,$c2]);
            $this->form->addFields([new TLabel('Tipo Pessoa')], [$tp]);
        }
        
        public function onGenerate($param = NULL)
        {
            
            $pc1 = TUtil::coalesce($param['c1'], "1");
            $pc2 = TUtil::coalesce($param['c2'], "9999");
            $ptp = TUtil::coalesce($param['tp'], "'F','J'");
            
            $param['q'] = "select e.codigo, 
                             e.razao,
                             e.fantasia,
                             (case when e.tipo='F' then cpf else cnpj end) cnpjcpf,
                             (select nome from municipios where codigo=e.cidade and uf=e.uf) cidade,
                             (select sigla from estados where codigo = e.uf) estado  
                        from entidades e 
                       where e.codigo between {$pc1} and {$pc2}
                         and e.tipo in ({$ptp}) order by 6,5";
            
            parent::onGenerate($param);
        }
    }


?>
