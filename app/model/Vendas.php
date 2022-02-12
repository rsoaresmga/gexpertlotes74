<?php
/**
 * Vendas Active Record
 * @author  <your-name-here>
 */
class Vendas extends TRecord
{
    const TABLENAME = 'vendas';
    const PRIMARYKEY= 'LANCAMENTO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $empreendimentos;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('EMPREENDIMENTO');
        parent::addAttribute('LOTE');
        parent::addAttribute('ENTIDADE');
        parent::addAttribute('EMISSAO');
        parent::addAttribute('VALOR');
        parent::addAttribute('ENTRADA');
        parent::addAttribute('CONTRATO');
        parent::addAttribute('PARCELAS');
        parent::addAttribute('REAJUSTE');
        parent::addAttribute('OBSERVACAO');
        parent::addAttribute('CANCELADO');
        parent::addAttribute('CANCELAMENTO');
        parent::addAttribute('ESTORNO');
        parent::addAttribute('SALDO');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('QUADRA');
        parent::addAttribute('VALORPARCELA');
        parent::addAttribute('VALORPARCELARESCISAO');
        parent::addAttribute('PARCELASRESCISAO');
        parent::addAttribute('CONTACTBCANCELAMENTO');
        parent::addAttribute('CONTACTBENTRADA');
        parent::addAttribute('CONTACTBENTIDADE');
        parent::addAttribute('CONTACTBENTIDADELP');
        parent::addAttribute('CTACTBRECEITADIFERCP');
        parent::addAttribute('CTACTBRECEITADIFERLP');
        parent::addAttribute('CTACTBDESPESADIFERCP');
        parent::addAttribute('CTACTBDESPESADIFERLP');
        
    }
    
    function onBeforeStore($object)
    {
        if( empty($object->EMPRESA))
        {
            $object->EMPRESA = TSession::getValue('userunitid');  
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
    
    function get_empreendimentos()
    {
         if($this->EMPREENDIMENTO)
         {
             return new Empreendimentos($this->EMPREENDIMENTO);
         }   
         
         return new Empreendimentos;
    }
    
    function get_entidades()
    {
         if($this->ENTIDADE)
         {
             return new Entidades($this->ENTIDADE);
         }   
         
         return new Entidades;   
    }
    
}
