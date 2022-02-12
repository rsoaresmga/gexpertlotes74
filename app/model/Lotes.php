<?php
/**
 * Lotes Active Record
 * @author  <your-name-here>
 */
class Lotes extends TRecord
{
    const TABLENAME = 'lotes';
    const PRIMARYKEY= 'ID';
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
        parent::addAttribute('CODIGO');
        parent::addAttribute('QUADRA');
        parent::addAttribute('DESMEMBRAMENTO');
        parent::addAttribute('AREA');
        parent::addAttribute('VLRCUSTO');
        parent::addAttribute('SITUACAO');
        parent::addAttribute('ATIVO');
        parent::addAttribute('OBS');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('CONTACTBCUSTO');
        parent::addAttribute('CONTACTBESTOQUE');
        parent::addAttribute('CONTACTBRECEITADIFER');
        parent::addAttribute('CONTACTBDESPESADIFER');
        parent::addAttribute('CONTACTBRECEITADIFERLP');
        parent::addAttribute('CONTACTBDESPESADIFERLP');
        parent::addAttribute('HISTCTBCUSTO');
        parent::addAttribute('HISTCTBRECEITADIFER');
        parent::addAttribute('HISTCTBDESPESADIFER');
        parent::addAttribute('HISTCTBESTOQUE');
        parent::addAttribute('CONTACTBRECEITA');
    }
    
    public function get_empreendimentos()
    {
        if($this->EMPREENDIMENTO)
        {
            return new Empreendimentos($this->EMPREENDIMENTO);
        }
        
        return new Empreendimentos;
    }
    
    public static function getFromCodigo($empreendimento, $quadra, $lote)
    {
        return Lotes::where('EMPREENDIMENTO','=',$empreendimento)->where('QUADRA','=',$quadra)->where('CODIGO','=',$lote)->first();
    }
    
    public function get_vendas()
    {
        if($this->SITUACAO==1)
        {
            return Vendas::where('EMPREENDIMENTO','=',$this->EMPREENDIMENTO)->where('QUADRA','=',$this->QUADRA)->where('LOTE','=',$this->CODIGO)->first() ;
        }
        else 
        {
            return FALSE;
        } 
    }

}
