<?php 
    class RelHistoricos extends RelatorioModel
    {
        public $fields;
        public $fieldsstr;
               
        public function __construct()
        {
            
            $this->fields = array(
                0=>array('name'=>'codigo','caption'=>'Cód.', 'align'=>'left', 'size'=>40, 'total'=>'count', 'type'=>'integer'),
                1=>array('name'=>'descricao','caption'=>'Descr.', 'align'=>'left', 'size'=>400, 'type'=>'string')                               
            );
            
            $this->fieldsstr = implode(',', array_column($this->fields,'name')); 
            
            $props['title']   = "Cadastro de Históricos";
            $props['fields']  = $this->fields;
            
            parent::__construct($props);
        }
        
        public function onGenerate($param = NULL)
        {
           $data = $this->form->getData();
           
           $query = "select {$this->fieldsstr}
                       from historicos";
           
           parent::onGenerate(['q'=>$query]);                    
           
        } 
    }
?>