<?php
/**
 * Infraestrutura Active Record
 * @author  <your-name-here>
 */
class Infraestrutura extends TRecord
{
    const TABLENAME = 'infraestrutura';
    const PRIMARYKEY= 'LANCAMENTO';
    const IDPOLICY =  'max'; // {max, serial}
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('EMPREENDIMENTO');
        parent::addAttribute('HISTORICO');
        parent::addAttribute('DATA');
        parent::addAttribute('VALOR');
        parent::addAttribute('OBSERVACAO');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('CONTACTB');
    }

    public function get_empreendimentos()
    {
        if (!empty($this->EMPREENDIMENTO))
        {
            return new Empreendimentos($this->EMPREENDIMENTO);
         }   
        
        return new Empreendimentos;
    }
    
    public function onBeforeStore($object)
    {
        if(!$object->EMPRESA)
        {
            $object->EMPRESA = TSession::getValue('userunitid');
        }
        
        return $object;
    }

}
