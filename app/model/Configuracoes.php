<?php
/**
 * Configuracoes Active Record
 * @author  <your-name-here>
 */
class Configuracoes extends TRecord
{
    const TABLENAME = 'configuracoes';
    const PRIMARYKEY= 'EMPRESA';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('LOCACOESDIA');
        parent::addAttribute('EXIBIRLOCACOESINICIO');
        parent::addAttribute('CONTATROCO');
        parent::addAttribute('CONTACAIXA');
        parent::addAttribute('CONTACTBRECEBIMENTOS');
        parent::addAttribute('CONTACTBPAGAMENTOS');
        parent::addAttribute('BACKUP');
        parent::addAttribute('USUARIOCAD');
        parent::addAttribute('DATACAD');
        parent::addAttribute('USUARIOALT');
        parent::addAttribute('DATAALT');
        parent::addAttribute('RESUMO');
        parent::addAttribute('DIASRESUMO');
        parent::addAttribute('SMTP');
        parent::addAttribute('EMAIL');
        parent::addAttribute('USUARIOEMAIL');
        parent::addAttribute('SENHA');
        parent::addAttribute('MENSAGEMECF');
        parent::addAttribute('AUTENTICAEMAIL');
        parent::addAttribute('PORTA');
        parent::addAttribute('SSLEMAIL');
        parent::addAttribute('PORTAECF');
        parent::addAttribute('DIASREAGENDAR');
        parent::addAttribute('HRABRE');
        parent::addAttribute('HRFECHA');
        parent::addAttribute('CARTRECEBVISTA');
        parent::addAttribute('CARTRECEBPRAZO');
        parent::addAttribute('CARTPAGTOVISTA');
        parent::addAttribute('CARTPAGTOPRAZO');
        parent::addAttribute('CARTCHEQUERECEB');
        parent::addAttribute('CARTCHEQUEPAGTO');
        parent::addAttribute('REGIMETRIBUTARIO');
        parent::addAttribute('RAMOATIVIDADE');
        parent::addAttribute('CNAEPRINCIPAL');
        parent::addAttribute('CNAESECUNDARIO');
        parent::addAttribute('CONTADORRESPONSAVEL');
        parent::addAttribute('DATAABERTURA');
        parent::addAttribute('DATAREGISTRO');
        parent::addAttribute('TIPOREGISTRO');
        parent::addAttribute('NUMEROREGISTRO');
        parent::addAttribute('CODIGOEMPRESACONTABIL');
        parent::addAttribute('HISTCTBAQUISICAO');
        parent::addAttribute('HISTCTBDESMEMBRAMENTO');
        parent::addAttribute('HISTCTBVENDAVISTA');
        parent::addAttribute('HISTCTBCUSTOVENDAVISTA');
        parent::addAttribute('HISTCTBVENDAPRAZO');
        parent::addAttribute('HISTCTBCUSTOVENDAPRAZO');
        parent::addAttribute('HISTCTBRECDIFER');
        parent::addAttribute('HISTCTBDESPDIFER');
        parent::addAttribute('HISTCTBRECEBPARC');
        parent::addAttribute('HISTCTBCUSTORECEBPARC');
        parent::addAttribute('HISTCTBRECEITARECEBPARC');
        parent::addAttribute('HISTCTBCANCELAMENTO');
        parent::addAttribute('HISTCTBRECDIFERCANCELAMENTO');
        parent::addAttribute('HISTCTBDESPDIFERCANCELAMENTO');
        parent::addAttribute('HISTCTBCUSTOCANCELAMENTO');
        parent::addAttribute('HISTCTBINFRAESTRUTURA');
        parent::addAttribute('HISTCTBINFRAVENDIDOS');
        parent::addAttribute('HISTCTBINFRAAVENDER');
        parent::addAttribute('HISTCTBRECJUROS');
        parent::addAttribute('HISTCTBATUALIZACAO');
        parent::addAttribute('CONTACTBVENDAVISTA');
        parent::addAttribute('CONTACTBCANCELAMENTO');
        parent::addAttribute('CONTACTBINFRAESTRUTURA');
        parent::addAttribute('CONTACTBJUROSRECEB');
        parent::addAttribute('HISTCTBRECEITAEVENT');
        parent::addAttribute('ALIQPIS');
        parent::addAttribute('ALIQCOFINS');
        parent::addAttribute('ALIQIRPJ');
        parent::addAttribute('ALIQCSLL');
        parent::addAttribute('CSTPIS');
        parent::addAttribute('CSTCOFINS');
        parent::addAttribute('SOCIORESPONSAVEL');
    }


}
