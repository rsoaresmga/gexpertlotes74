<?php
/**
 * VendasRescisaoParcelas Active Record
 * @author  <your-name-here>
 */
class VendasRescisaoParcelas extends TRecord
{
    const TABLENAME = 'vendas_rescisao_parcelas';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('PARCELA');
        parent::addAttribute('VENDA');
        parent::addAttribute('VENCIMENTO');
        parent::addAttribute('VALOR');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('EMPRESA');
    }


}
