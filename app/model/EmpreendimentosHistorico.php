<?php

use Adianti\Database\TRecord;

class EmpreendimentosHistorico extends TRecord {

      const TABLENAME = 'empreendimentos_historico';
      const PRIMARYKEY= 'id';
      const IDPOLICY =  'max'; // {max, serial}

      public function __construct($id = NULL, $callObjectLoad = TRUE)
      {
        
            parent::__construct($id, $callObjectLoad);

            parent::addAttribute('EMPRESA');
            parent::addAttribute('EMPREENDIMENTO');
            parent::addAttribute('INAUGURACAO');
            parent::addAttribute('TIPOCALC');
            parent::addAttribute('CUSTOORC');
            parent::addAttribute('CUSTOINC');
            parent::addAttribute('PERANDAMENTO');
            parent::addAttribute('PERSUSPENSAO');
            parent::addAttribute('DIASSUSPENSAO');
            parent::addAttribute('SUSPENSO');
            parent::addAttribute('DATAHORA');
            parent::addAttribute('USUARIO');
      }     


}
?>