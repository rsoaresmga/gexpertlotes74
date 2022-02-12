<?php

    class TCsvFile 
    {
        protected $filename;
        protected $handle;
                 
        public function __construct($file)
        {
            define('moAppend', 'a+');
            define('moRewrite', 'w+');
       
            $this->filename = $file;           
        }
        
        public function open($mode = 'a+')
        {
            $this->handle = fopen(self::getname(), $mode);
        } 
        
        public function close()
        {
            fclose($this->handle);
        }
        
        public function getname()
        {
            return $this->filename;
        }
        
        public function setname($value)
        {
            $this->filename = $value;
        }
        
        /**
         * @Exporta Resultados da query->load() do Adianti
         */
        public function writeqr(array $values, $delimiter = ';')
        {
           if(!isset($this->handle))
           {
               new TMessage('error', 'É necessário abrir o arquivo antes da escrita');
           } 
           else
           {                 
               foreach($values as $records)
               {                 
                  $i = 0;
                  $r = '';
                  
                  for($i==0;$i<(count($records)-1)/2; $i++)
                    {
                        $r.= (!isset($records[$i])||is_null($records[$i]))?'':$records[$i];
                        $r.= $delimiter;
                    }                   
                    
                  $r .= "\r\n";
                    
                  fwrite($this->handle, $r, strlen($r)); 
               }
           }
        }
        
         /**
         * @Exporta dados de Array do Adianti
         */
        public function writeln(array $values, $delimiter)
        {
           if(!isset($this->handle))
           {
               new TMessage('error', 'É necessário abrir o arquivo antes da escrita');
           } 
           else
           {
               foreach($values as $value)
               {
                   fputcsv($this->handle, $value, $delimiter);    
               } 
           }  
             
        }
        
        public function read($delimiter = ';')
        {
           if(!isset($this->handle))
           {
               new TMessage('error', 'É necessário abrir o arquivo antes da leitura');
           } 
           else
           {   
               $conteudo[] = null;
               
               while($data = fgetcsv($this->handle, 100000, $delimiter)) 
               {
                    $conteudo[] = $data;                             
               }    
               return $conteudo;
           }
        }
        
        /*
        * @ Faz download do arquivo
        */
        public function download()
        {
           TPage::openFile($this->getname());
        }
        
        public function dump()
        {
            print_r(file($this->getname()));
        }
        
        /*
        * @Exclui o arquivo do diretorio tmp
        */
        public function delete()
        {
            if(file_exists($this->getname()))
            {
                return unlink($this->getname());    
            }            
        }
                
    }
    

?>
