<?php
/**
 * VendasRescisaoParcelasBaixas Active Record
 * @author  <your-name-here>
 */
class VendasRescisaoParcelasBaixas extends TRecord
{
    const TABLENAME = 'vendas_rescisao_parcelas_baixas';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('VENDA');
        parent::addAttribute('PARCELA');
        parent::addAttribute('RECEBIMENTO');
        parent::addAttribute('VALOR');
        parent::addAttribute('JUROS');
        parent::addAttribute('MULTA');
        parent::addAttribute('DESCONTO');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('EMPRESA');
        parent::addAttribute('CONTACTBPAGTO');
    }


}
