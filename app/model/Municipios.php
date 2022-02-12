<?php
/**
 * Municipios Active Record
 * @author  <your-name-here>
 */
class Municipios extends TRecord
{
    const TABLENAME = 'municipios';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NOME');
        parent::addAttribute('RAIS');
        parent::addAttribute('UF');
        parent::addAttribute('CEP');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATAALT');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('FEDERAL');
        parent::addAttribute('ESTADUAL');
    }

   
    public function get_estado()
    {
        if ($this->UF)
        {
            return new Estados($this->UF);
        }    
    
        return new Estados;
    }

}
