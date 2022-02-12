<?php

use Adianti\Database\TCriteria;
use Adianti\Database\TExpression;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;

define('LAUDO_TECNICO', 1);
define('CUSTO_INCORRIDO', 0);

class Apurador {
      private $database;
      
      function __construct()
      {
            $this->database = TSession::getValue('unit_database');      
      }

      /** reconhece a receita de acordo com a evolucao da obra
       * @param $tipo => [ 0=>`Custo incorrido`, 1=>`Laudo tecnico` ]
       */
      function poc($tipo = 0, $empresa, $competencia, $terrenoini, $quadraini, $quadrafim, $lotini, $lotefim)
      {
            switch ($tipo) {
                  case CUSTO_INCORRIDO: {
                        /**
                         * Se a unidade foi vendida
                         *  */   
                        
                        
                         
                  }
                  break;
                  case LAUDO_TECNICO: {
                        
                  }
                  break;
            }      

      }

      function tradicional($param) 
      {
            // aqui faz o calculo do custo apropriado proporcional a receita recebida 
      }

      function apurar($empresa, $competencia, $terrenoini, $terrenofim, $quadraini, $quadrafim, $lotini, $lotefim)
      {
            TTransaction::open($this->database);

            $criteria = new TCriteria;
            $criteria->add(new TFilter('EMPRESA','=', $empresa));
            $criteria->add(new TFilter('CODIGO', 'between', $terrenoini, $terrenofim));
            
            $repo = new TRepository('Empreendimentos');
            
            $terrenos = $repo->load($criteria);

            foreach($terrenos as $terreno)
            {
                  $terreno = (object) $terreno;

                  switch($terreno->TIPOCALC) {
                        case '0': {       // Apuracao tradicional
                              print_r("<br>{$terreno->CODIGO}. {$terreno->DESCRICAO} Tradicional");
                        }
                        break;
                        case '1': {        // poc custo incorrido   
                              print_r("<br>{$terreno->CODIGO}. {$terreno->DESCRICAO} POC Custo incorrido");
                              $this->poc(CUSTO_INCORRIDO, $empresa, $competencia, $terrenoini, $terrenofim, $quadraini, $quadrafim, $lotini, $lotefim);
                        }
                        break;
                        case '2': {       // poc laudo tecnico 
                              print_r("<br>{$terreno->CODIGO}. {$terreno->DESCRICAO} POC laudo tecnico");
                              $this->poc(LAUDO_TECNICO, $empresa, $competencia, $terrenoini, $terrenofim, $quadraini, $quadrafim, $lotini, $lotefim);
                        }
                        break;                        
                  }
            }

            TTransaction::close();
      }
}
?>