<?php
/**
 * InfraestruturaForm Form
 * @author  <your name here>
 */
class InfraestruturaForm extends TTr2Page
{
    use Adianti\Base\AdiantiStandardFormTrait;
    use Tr2FormUtilsTrait;
    
    protected $form; 
    protected $criteria;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Infraestrutura');
        
        $this->form = new BootstrapFormBuilder('form_Infraestrutura');
        $this->form->setFormTitle('Infraestrutura');
        
        $LANCAMENTO = new TEntry('LANCAMENTO');
        $EMPRESA    = new TEntry('EMPRESA');
        $EMPRESA->setEditable(FALSE);
        
        $this->criteria = new TCriteria();
        $this->criteria->add(new TFilter('EMPRESA', '=', TSession::getValue('userunitid')));
                
        $EMPREENDIMENTO = new TDBSeekButton('EMPREENDIMENTO', $this->database, $this->form->getName(), 'Empreendimentos', 'DESCRICAO', 'EMPREENDIMENTO', null, $this->criteria);
        $DSEMPREENDIMENTO = new TEntry('DSEMPREENDIMENTO');
        $DSEMPREENDIMENTO->setEditable(FALSE);
        $EMPREENDIMENTO->setAuxiliar($DSEMPREENDIMENTO);
            
        $HISTORICO = new TDBSeekButton('HISTORICO',  $this->database, $this->form->getName(), 'Historicos', 'concat(CODIGO, DESCRICAO)', 'HISTORICO', null);         
        $HISTORICO->setDisplayMask('{DESCRICAO}');
        $HISTAUX = new TEntry('HISTAUX');
        $HISTAUX->setEditable(FALSE);
        $HISTORICO->setAuxiliar($HISTAUX);
        
        
        $DATA = new TDate('DATA');
        $DATA->setMask(TMascara::maskDate);
            $DATA->setDatabaseMask(TMascara::maskDBDate);
        
        $VALOR = new TEntry('VALOR');
        $VALOR->setNumericMask(2,',','.', TRUE);
        
        $OBSERVACAO = new TText('OBSERVACAO');
        
        $CONTACTB = new TTr2DBSeekButton('CONTACTB',  $this->database, $this->form->getName(), 'Plano', 'concat(CODIGO,CLASSIFICACAO,DESCRICAO)', 'CONTACTB', null, $this->criteria);
        $CONTACTB->setDisplayMask('{CLASSIFICACAO}. {DESCRICAO}');
        $CONTACTB->setModelKey('CODIGO');
        $CTAAUX = new TEntry('CTAAUX');
        $CTAAUX->setEditable(FALSE);
        $CONTACTB->setAuxiliar($CTAAUX);

        // add the fields
        $this->form->addFields( [ new TLabel('Lancamento') ], [ $LANCAMENTO ] );
        $this->form->addFields( [ new TLabel('Empresa') ], [ $EMPRESA ] );
        $this->form->addFields( [ new TLabel('Empreendimento') ], [ $EMPREENDIMENTO ] );
        $this->form->addFields( [ new TLabel('Data') ], [ $DATA ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $VALOR ] );
        $this->form->addFields( [ new TLabel('Conta ctb') ], [ $CONTACTB ] );
        $this->form->addFields( [ new TLabel('Histórico') ], [ $HISTORICO ] );
        $this->form->addFields( [ new TLabel('Observacao') ], [ $OBSERVACAO ] );        

        $LANCAMENTO->setSize(TWgtSizes::wsInt);
        $EMPRESA->setSize(TWgtSizes::wsInt);
        $EMPREENDIMENTO->setSize(TWgtSizes::wsInt);
        $DSEMPREENDIMENTO->setSize(TWgtSizes::wsAux);
        $HISTORICO->setSize(TWgtSizes::wsInt);
        $HISTAUX->setSize(TWgtSizes::wsAux);
        $DATA->setSize(TWgtSizes::wsDate);
        $VALOR->setSize(TWgtSizes::wsDouble);
        $OBSERVACAO->setSize(TWgtSizes::wsBlob);
        $CONTACTB->setSize(TWgtSizes::wsInt);
        $CTAAUX->setSize(TWgtSizes::wsAux);

        $LANCAMENTO->setEditable(FALSE);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['InfraestruturaList', 'onReload']), 'far:arrow-alt-circle-left blue'); 

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Infraestrutura', 'Lançamento'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }
}
