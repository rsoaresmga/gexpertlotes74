<?php
/**
 * Apuracao Active Record
 * @author  <your-name-here>
 */
class Apuracao extends TRecord
{
    const TABLENAME = 'apuracao';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('EMPRESA');
        parent::addAttribute('EMPREENDIMENTO');
        parent::addAttribute('VENDA');
        parent::addAttribute('QUADRA');
        parent::addAttribute('LOTE');
        parent::addAttribute('MES');
        parent::addAttribute('DTVENDA');
        parent::addAttribute('DTDISTRATO');
        parent::addAttribute('PERRECEBACUMUL');
        parent::addAttribute('PERRECEBMES');
        parent::addAttribute('VLRCUSTOAQUISICAO');
        parent::addAttribute('VLRCUSTOLOTE');
        parent::addAttribute('VLRINFRAACUMULADA');
        parent::addAttribute('VLRVENDA');
        parent::addAttribute('VLRRECEBACUMULADO');
        parent::addAttribute('VLRINFRAMES');
        parent::addAttribute('VLRRECEBMES');
        parent::addAttribute('VLRJUROSMES');
        parent::addAttribute('VLRMULTAMES');
        parent::addAttribute('VLRDESCONTOMES');
        parent::addAttribute('VLRATUALIZACAOMES');
        parent::addAttribute('VLRCUSTOAPROP');
        parent::addAttribute('VLRCUSTOPROP');
        parent::addAttribute('VLRDISTRATO');
        parent::addAttribute('VLRCUSTOCP');
        parent::addAttribute('VLRCUSTOLP');
        parent::addAttribute('VLRRECEITACP');
        parent::addAttribute('VLRRECEITALP');
        parent::addAttribute('VLRINFRADISTRATO');
        parent::addAttribute('VLRRECEITADISTRATO');
        parent::addAttribute('DATA');
        parent::addAttribute('USUARIO');
    }


}
