<?php
/**
 * Estados Active Record
 * @author  <your-name-here>
 */
class Estados extends TRecord
{
    const TABLENAME = 'estados';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('SIGLA');
        parent::addAttribute('NOME');
        parent::addAttribute('ALIQICMSINTERNA');
        parent::addAttribute('ALIQICMSEXTERNA');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
    }


}
