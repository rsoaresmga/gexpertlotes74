<?php
/**
 * Plano Active Record
 * @author  <your-name-here>
 */
class Plano extends TRecord
{
    const TABLENAME = 'plano';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $empresas;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODIGO');
        parent::addAttribute('EMPRESA');
        parent::addAttribute('GRUPO');
        parent::addAttribute('CLASSIFICACAO');
        parent::addAttribute('NATUREZA');
        parent::addAttribute('DESCRICAO');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
    }
    
    public function onBeforeStore($object)
    {
        
        if(empty($object->EMPRESA))
        {
            $object->EMPRESA = TSession::getValue('userunitid');
        }
        
        return $object;
    }

}
