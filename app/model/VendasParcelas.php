<?php
/**
 * VendasParcelas Active Record
 * @author  <your-name-here>
 */
class VendasParcelas extends TRecord
{
    const TABLENAME = 'vendas_parcelas';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $vendas;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('VENDA');
        parent::addAttribute('PARCELA');
        parent::addAttribute('VALOR');
        parent::addAttribute('QUITADO');
        parent::addAttribute('OBSERVACAO');
        parent::addAttribute('VENCIMENTO');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('SALDO');     
    }
    
    function get_recebido()
    {        
        return round($this->VALOR-$this->SALDO, 5);
    }
}
