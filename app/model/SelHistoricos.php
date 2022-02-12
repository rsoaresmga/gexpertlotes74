<?php
/**
 * SelHistoricos Active Record
 * @author  <your-name-here>
 */
class SelHistoricos extends TRecord
{
    const TABLENAME = 'sel_historicos';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('DESCRICAO');
    }


}
