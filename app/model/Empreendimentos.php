<?php

use Adianti\Registry\TSession;

/**
 * Empreendimentos Active Record
 * @author  <your-name-here>
 */
class Empreendimentos extends TRecord
{
    const TABLENAME = 'empreendimentos';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $empresas;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('TIPO');
        parent::addAttribute('DESCRICAO');
        parent::addAttribute('DATAAQUISICAO');
        parent::addAttribute('VLRAQUISICAO');
        parent::addAttribute('AREATOTAL');
        parent::addAttribute('QUADRAS');
        parent::addAttribute('LOTES');
        parent::addAttribute('ENDERECO');
        parent::addAttribute('NUMERO');
        parent::addAttribute('BAIRRO');
        parent::addAttribute('COMPLEMENTO');
        parent::addAttribute('CEP');
        parent::addAttribute('CIDADE');
        parent::addAttribute('UF');
        parent::addAttribute('OBS');
        parent::addAttribute('ATIVO');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('CONTACTB');
        parent::addAttribute('CONTACTBRECEITA');
        parent::addAttribute('CONTACTBCUSTO');
        parent::addAttribute('CONTACTBDEVOLUCAO');
        parent::addAttribute('CONTACTBINFRAESTRUTURA');
        parent::addAttribute('CONTACTBPAGTO');
        parent::addAttribute('CONTACTBATUALIZACAO');
        parent::addAttribute('CONTACTBJUROS');
        parent::addAttribute('CONTACTBRECEITAEVENTUAL');
        parent::addAttribute('AREALOTE');
        parent::addAttribute('CUSTOLOTE');
        parent::addAttribute('CONTACTBDEVOLUCAOPAGAR');
        parent::addAttribute('CONTACTBDEVOLUCAODRE');
        
        parent::addAttribute('INAUGURACAO');
        parent::addAttribute('TIPOCALC');
        parent::addAttribute('CUSTOORC');
        parent::addAttribute('CUSTOINC');
        parent::addAttribute('PERANDAMENTO');
        parent::addAttribute('PERSUSPENSAO');
        parent::addAttribute('DIASSUSPENSAO');
        parent::addAttribute('SUSPENSO');
        parent::addAttribute('CUSTOFUT');
        parent::addAttribute('PERINCC');
    }
    
    function onBeforeStore($object)
    {
        if(empty($object->EMPRESA))
        {
            $object->EMPRESA= TSession::getValue('userunitid');
        }
        
        if(empty($object->USUARIOCAD))
        {
            $object->USUARIOCAD = TSession::getValue('userid');
            $object->DATACAD    = date('Y-m-d H:i:s'); 
        }
        
        $object->USUARIOALT = TSession::getValue('userid');
        $object->DATAALT    = date('Y-m-d H:i:s');  
        
                
        return $object;
    }
    
    function get_municipio()
    {
        if($this->CIDADE)
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

    function onAfterStore($object)
    {
        $object->EMPREENDIMENTO = $object->CODIGO;
        $object->USUARIO        = TSession::getValue('userid');
        $log = EmpreendimentosHistorico::create((array) $object);
    }

    function get_CUSTOINC()
    {
        if($total = Infraestrutura::where('EMPREENDIMENTO','=', $this->CODIGO)->where('EMPRESA', '=', $this->EMPRESA)->sumBy('VALOR', 'TOTAL') )
        {
            return $total;
        } else {
            return 0;
        }
    }

    function get_PERANDAMENTO()
    {
        return !is_null($this->CUSTOORC)? $this->get_CUSTOINC() / $this->CUSTOORC * 100 : 0;
    }

}
