<?php
/**
 * VendasParcelasBaixas Active Record
 * @author  <your-name-here>
 */
class VendasParcelasBaixas extends TRecord
{
    const TABLENAME = 'vendas_parcelas_baixas';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('VENDA');
        parent::addAttribute('PARCELA');
        parent::addAttribute('RECEBIMENTO');
        parent::addAttribute('VALOR');
        parent::addAttribute('JUROS');
        parent::addAttribute('MULTA');
        parent::addAttribute('DESCONTO');
        parent::addAttribute('CARTEIRA');
        parent::addAttribute('TOTAL');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('CONTACTBPAGTO');
        parent::addAttribute('ATUALIZACAO');
    }

    
    public function get_vendas_parcelas()
    {
        if ($this->PARCELA)
        {
            return new VendasParcelas($this->PARCELA);
        }
            
        return new VendasParcelas;
    }
    
    public function get_vendas()
    {
        if ($this->VENDA)
        {
            return new Vendas($this->VENDA);
        }
            
        return new Vendas;        
    }

}
