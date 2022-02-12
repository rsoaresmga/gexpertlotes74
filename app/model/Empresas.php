<?php
/**
 * Empresas Active Record
 * @author  <your-name-here>
 */
class Empresas extends TRecord
{
    const TABLENAME = 'empresas';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('RAZAO');
        parent::addAttribute('FANTASIA');
        parent::addAttribute('TIPO');
        parent::addAttribute('CNPJ');
        parent::addAttribute('CPF');
        parent::addAttribute('IE');
        parent::addAttribute('ENDERECO');
        parent::addAttribute('NUMERO');
        parent::addAttribute('BAIRRO');
        parent::addAttribute('COMPLEMENTO');
        parent::addAttribute('CEP');
        parent::addAttribute('CIDADE');
        parent::addAttribute('UF');
        parent::addAttribute('DDD');
        parent::addAttribute('FONE');
        parent::addAttribute('CELULAR');
        parent::addAttribute('FAX');
        parent::addAttribute('ATIVO');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATAALT');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('LOGO');
        parent::addAttribute('OBSERVACAO');
        parent::addAttribute('OBSERVACAONF');
    }
    
    function get_municipio()
    {
        if ($this->CIDADE)
        {
            return new Municipios($this->CIDADE);    
        }
        
        return new Municipios();
    }
    
    function get_estado()
    {
        if($this->UF)
        {
            return new Estados($this->UF);
        }
        
        return new Estados();
    }


}
