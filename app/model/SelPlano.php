<?php
/**
 * SelPlano Active Record
 * @author  <your-name-here>
 */
class SelPlano extends TRecord
{
    const TABLENAME = 'sel_plano';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('CLASSIFICACAO');
        parent::addAttribute('DESCRICAO');
        parent::addAttribute('DISPLAY');
    }


}
