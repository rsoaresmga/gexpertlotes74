<?php
/**
 * Entidades Active Record
 * @author  <your-name-here>
 */
class Entidades extends TRecord
{
    const TABLENAME = 'entidades';
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
        parent::addAttribute('EMAIL');
        parent::addAttribute('NASCIMENTO');
        parent::addAttribute('CLIENTE');
        parent::addAttribute('FORNECEDOR');
        parent::addAttribute('TRANSPORTADOR');
        parent::addAttribute('VENDEDOR');
        parent::addAttribute('FUNCIONARIO');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATAALT');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('FOTO');
        parent::addAttribute('ATIVO');
        parent::addAttribute('RGEXP');
        parent::addAttribute('PROFISSAO');
        parent::addAttribute('ESTCIVIL');
        parent::addAttribute('RG');
    }
    
    public function get_cidades()
    {
         if($this->CIDADE)
         {
             return new Municipios($this->CIDADE);
         }   
         
         return new Municipios;
    }
    
    public function get_estados()
    {
         if($this->UF)
         {
             return new Estados($this->UF);
         }   
         
         return new Estados;
    }


}
