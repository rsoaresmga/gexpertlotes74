<?php
/**
 * SelConsultaVendasParcelasBaixas Active Record
 * @author  <your-name-here>
 */
class SelConsultaVendasParcelasBaixas extends TRecord
{
    const TABLENAME = 'sel_consulta_vendas_parcelas_baixas';
    const PRIMARYKEY= 'LANCAMENTO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('CLIENTECOD');
        parent::addAttribute('CLIENTERAZAO');
        parent::addAttribute('EMPREENDIMENTOCOD');
        parent::addAttribute('EMPREENDIMENTODESCR');
        parent::addAttribute('QUADRA');
        parent::addAttribute('LOTE');
        parent::addAttribute('EMISSAO');
        parent::addAttribute('VALORVENDA');
        parent::addAttribute('PARCELA');
        parent::addAttribute('VENCIMENTO');
        parent::addAttribute('VALORPARCELA');
        parent::addAttribute('QUITADO');
        parent::addAttribute('RECEBIMENTO');
        parent::addAttribute('VALOR');
        parent::addAttribute('JUROS');
        parent::addAttribute('MULTA');
        parent::addAttribute('ATUALIZACAO');
        parent::addAttribute('TOTALRECEBIDO');
    }


}
