<?php
/**
 * EntidadesForm Form
 * @author  <your name here>
 */
class EntidadesForm extends TPage
{
    use Adianti\Base\AdiantiStandardFormTrait;
    use Tr2FormUtilsTrait;
    
    protected $form; // form
    
    public function __construct( $param )
    {
        parent::__construct();

        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Entidades');
                
        $this->form = new BootstrapFormBuilder('form_Entidades');
        $this->form->setFormTitle('Entidades');
        
        $CODIGO        = new TEntry('CODIGO');
        $RAZAO         = new TEntry('RAZAO');                    
        $FANTASIA      = new TEntry('FANTASIA');
        $TIPO          = new TCombo('TIPO');        
        $CNPJ          = new TEntry('CNPJ');        
        $CPF           = new TEntry('CPF');        
        $IE            = new TEntry('IE');
        $ENDERECO      = new TEntry('ENDERECO');
        $NUMERO        = new TEntry('NUMERO');
        $BAIRRO        = new TEntry('BAIRRO');
        $COMPLEMENTO   = new TEntry('COMPLEMENTO');
        $CEP           = new TEntry('CEP');        
        $UF            = new TDBCombo('UF', $this->database, 'Estados', 'CODIGO', '{NOME} ({SIGLA})');    
        $CIDADE        = new TDBCombo('CIDADE', $this->database, 'Municipios', 'CODIGO', '{RAIS}. {NOME}', 'concat(RAIS,NOME)');       
        $DDD           = new TEntry('DDD');        
        $FONE          = new TEntry('FONE');        
        $CELULAR       = new TEntry('CELULAR');        
        $EMAIL         = new TEntry('EMAIL');
        $CLIENTE       = new TCombo('CLIENTE');
        $FORNECEDOR    = new TCombo('FORNECEDOR');        
        $TRANSPORTADOR = new TCombo('TRANSPORTADOR');              
        $VENDEDOR      = new TCombo('VENDEDOR');        
        $FUNCIONARIO   = new TCombo('FUNCIONARIO');        
        $ATIVO         = new TCombo('ATIVO');        
        $RGEXP         = new TEntry('RGEXP');
        $RG            = new TEntry('RG');

        $RAZAO->forceUpperCase();
        $FANTASIA->forceUpperCase();
        
        $CNPJ->setMask(TMascara::maskCNPJ);
        $CPF->setMask(TMascara::maskCPF);
        $CEP->setMask(TMascara::maskCEP);
        $DDD->setMask(TMascara::maskDDD);
        $FONE->setMask(TMascara::maskFone);
        $CELULAR->setMask(TMascara::maskCel);
        
        $UF->enableSearch();
        $CIDADE->enableSearch();
                
        $TIPO->addItems(['F'=>'Física', 'J'=>'Jurídica']);
        $CLIENTE->addItems(['S'=>'Sim', 'N'=>'Não']);
        $FORNECEDOR->addItems(['S'=>'Sim', 'N'=>'Não']);
        $TRANSPORTADOR->addItems(['S'=>'Sim', 'N'=>'Não']);
        $VENDEDOR->addItems(['S'=>'Sim', 'N'=>'Não']); 
        $FUNCIONARIO->addItems(['S'=>'Sim', 'N'=>'Não']); 
        $ATIVO->addItems(['S'=>'Sim', 'N'=>'Não']);
        
        $UF->setChangeAction(new TAction([$this, 'onChangeUF'],['static'=>'1']));
        
        $this->form->appendPage('Gerais');
        $this->form->addFields( [ new TLabel('Codigo') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Razao') ], [ $RAZAO ] );
        $this->form->addFields( [ new TLabel('Fantasia') ], [ $FANTASIA ] );
        $this->form->addFields( [ new TLabel('Tipo') ], [ $TIPO ] );
        $this->form->addFields( [ new TLabel('Cnpj') ], [ $CNPJ ] );
        $this->form->addFields( [ new TLabel('Cpf') ], [ $CPF ] );
        $this->form->addFields( [ new TLabel('Ie') ], [ $IE ] );
        $this->form->addFields( [ new TLabel('Rg') ], [ $RG ] );
        $this->form->addFields( [ new TLabel('Rgexp') ], [ $RGEXP ] );
        $this->form->addFields( [ new TLabel('Ativo') ], [ $ATIVO ] );
        
        $this->form->appendPage('Endereço');
        $this->form->addFields( [ new TLabel('Endereco') ], [ $ENDERECO ] );
        $this->form->addFields( [ new TLabel('Numero') ], [ $NUMERO ] );
        $this->form->addFields( [ new TLabel('Bairro') ], [ $BAIRRO ] );
        $this->form->addFields( [ new TLabel('Complemento') ], [ $COMPLEMENTO ] );
        $this->form->addFields( [ new TLabel('Cep') ], [ $CEP ] );
        $this->form->addFields( [ new TLabel('Uf') ], [ $UF ] );
        $this->form->addFields( [ new TLabel('Cidade') ], [ $CIDADE ] );
        
        $this->form->appendPage('Contato');
        $this->form->addFields( [ new TLabel('Ddd') ], [ $DDD ] );
        $this->form->addFields( [ new TLabel('Fone') ], [ $FONE ] );
        $this->form->addFields( [ new TLabel('Celular') ], [ $CELULAR ] );
        $this->form->addFields( [ new TLabel('Email') ], [ $EMAIL ] );
        
        $this->form->appendPage('Definições');
        $this->form->addFields( [ new TLabel('Cliente') ], [ $CLIENTE ] );
        $this->form->addFields( [ new TLabel('Fornecedor') ], [ $FORNECEDOR ] );
        $this->form->addFields( [ new TLabel('Transportador') ], [ $TRANSPORTADOR ] );
        $this->form->addFields( [ new TLabel('Vendedor') ], [ $VENDEDOR ] );
        $this->form->addFields( [ new TLabel('Funcionario') ], [ $FUNCIONARIO ] );

        $CODIGO->setSize(TWgtSizes::wsInt);
        $RAZAO->setSize(TWgtSizes::ws60);
        $FANTASIA->setSize(TWgtSizes::ws60);
        $TIPO->setSize(TWgtSizes::ws10);
        $CNPJ->setSize(TWgtSizes::wsCNPJ);
        $CPF->setSize(TWgtSizes::wsCPF);
        $IE->setSize(TWgtSizes::wsIE);
        $ENDERECO->setSize(TWgtSizes::ws60);
        $NUMERO->setSize(TWgtSizes::wsInt);
        $BAIRRO->setSize(TWgtSizes::ws40);
        $COMPLEMENTO->setSize(TWgtSizes::ws30);
        $CEP->setSize(TWgtSizes::wsCEP);
        $CIDADE->setSize(TWgtSizes::ws60);
        $UF->setSize(TWgtSizes::ws40);
        $DDD->setSize(TWgtSizes::wsDDD);
        $FONE->setSize(TWgtSizes::wsFone);
        $CELULAR->setSize(TWgtSizes::wsFone);
        $EMAIL->setSize(TWgtSizes::wsDef);
        $CLIENTE->setSize(TWgtSizes::ws05);
        $FORNECEDOR->setSize(TWgtSizes::ws05);
        $TRANSPORTADOR->setSize(TWgtSizes::ws05);
        $VENDEDOR->setSize(TWgtSizes::ws05);
        $FUNCIONARIO->setSize(TWgtSizes::ws05);
        $ATIVO->setSize(TWgtSizes::wsBol);
        $RGEXP->setSize(TWgtSizes::wsInt);
        $RG->setSize(TWgtSizes::wsIE);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['EntidadesList', 'onReload']), 'far:arrow-alt-circle-left blue'); 
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Entidades', 'Cadastro'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onChangeUF($param)
    {
        if(isset($param['UF']))
        {
            $criteria = new TCriteria();
            $criteria->add(new TFilter('UF', '=', $param['UF']));
            
            TDBCombo::reloadFromModel($this->form->getName(), 'CIDADE', $this->database, 'Municipios', 'CODIGO', '{RAIS}. {NOME}', 'concat(RAIS,NOME)', $criteria, TRUE);
        }
    }
    
    public function onAfterEdit($object)
    {
        echo "AAAA";
    }
   
}
