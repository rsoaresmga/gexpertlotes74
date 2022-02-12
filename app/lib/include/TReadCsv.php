<?php 
  
  class TReadCsv {
        private $_file;
        public $_delim;
        
        function __construct($file) {
            $this->_file = $file;
        }
        
        function get_delim() 
        {
            return $this->_delim;
        }
        
        function set_delim($delim)
        {
            $this->_delim = $delim;
        }
        
        public function set_file($file) {
            $this->_file = $file;
        }
        
        public function abre() {
            $fp = fopen ($this->_file,"r");
            while ($data = fgetcsv($fp, 1000, $this->get_delim())) {
           
                $conteudo[] = $data;
                              
            }
            
            return $conteudo;
        }
    } 
?>