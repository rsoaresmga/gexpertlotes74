<?php
/**
 * EmpresasForm Form
 * @author  <your name here>
 */
 
class EmpresasForm extends TPage
{
    use Tr2CollectionUtilsTrait;
    
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Empresas');
        $this->form->setFormTitle('Empresas');
        
        $this->setDefaultPageAction();
        $this->setDatabase('gexpertlotes');
        $this->setActiveRecord('Empresas');
   
        // create the form fields
        $CODIGO = new TEntry('CODIGO');
        $RAZAO = new TEntry('RAZAO');
        $RAZAO->forceUpperCase();
        $FANTASIA = new TEntry('FANTASIA');
        $FANTASIA->forceUpperCase();
        $TIPO = new TCombo('TIPO');
        $TIPO->addItems(['1'=>'Física', '0'=>'Jurídica']);
        $CNPJ = new TEntry('CNPJ');
        $CNPJ->setMask(TMascara::maskCNPJ);
        $CPF = new TEntry('CPF');
        $CPF->setMask(TMascara::maskCPF);
        $IE = new TEntry('IE');
        $IE->forceUpperCase();
        $ENDERECO = new TEntry('ENDERECO');
        $NUMERO = new TEntry('NUMERO');
        $BAIRRO = new TEntry('BAIRRO');
        $COMPLEMENTO = new TEntry('COMPLEMENTO');
        $CEP = new TEntry('CEP');
        $CEP->setMask(TMascara::maskCEP);
        $CIDADE = new TDBCombo('CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', '{RAIS}. {NOME}');
        $CIDADE->enableSearch();
        $UF = new TDBCombo('UF', 'gexpertlotes', 'Estados', 'CODIGO', '{NOME} ({SIGLA})');
        $UF->setChangeAction(new TAction([$this, 'onChangeUF'],['static'=>'1']));
        $UF->enableSearch();
        $DDD = new TEntry('DDD');
        $DDD->setMask(TMascara::maskDDD);
        $FONE = new TEntry('FONE');
        $FONE->setMask(TMascara::maskFone);
        $CELULAR = new TEntry('CELULAR');
        $CELULAR->setMask(TMascara::maskCel); 
        $FAX = new TEntry('FAX');
        $FAX->setMask(TMascara::maskFone);
        
        $ATIVO = new TCombo('ATIVO');
        $ATIVO->addItems(['S'=>'Sim', 'N'=>'Não']);
        $DATACAD = new TEntry('DATACAD');
        $USUARIOCAD = new TEntry('USUARIOCAD');
        $DATAALT = new TEntry('DATAALT');
        $USUARIOALT = new TEntry('USUARIOALT');
        $LOGO = new TFile('LOGO');
        
        $LOGO->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );        
        $LOGO->enableFileHandling();
        $LOGO->setDisplayMode('fullwidth');
        
        $OBSERVACAO = new TText('OBSERVACAO');
        $OBSERVACAONF = new TText('OBSERVACAONF');
        
        // add the fields
        $this->form->appendPage('Geral');
        
        $this->form->addFields( [ new TLabel('Codigo') ], [ $CODIGO ] );
        $this->form->addFields( [ new TLabel('Razao') ], [ $RAZAO ] );
        $this->form->addFields( [ new TLabel('Fantasia') ], [ $FANTASIA ] );
        $this->form->addFields( [ new TLabel('Tipo') ], [ $TIPO ] );
        $this->form->addFields( [ new TLabel('Cnpj') ], [ $CNPJ ] );
        $this->form->addFields( [ new TLabel('Cpf') ], [ $CPF ] );
        $this->form->addFields( [ new TLabel('Ie') ], [ $IE ] );
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
        $this->form->addFields( [ new TLabel('Fax') ], [ $FAX ] );
        
        $this->form->appendPage('Logo');
        $this->form->addFields( [ new TLabel('Logo') ], [ $LOGO ] );
        
        $this->form->appendPage('Adicionais');
        $this->form->addFields( [ new TLabel('Observacao') ], [ $OBSERVACAO ] );
        $this->form->addFields( [ new TLabel('Observacaonf') ], [ $OBSERVACAONF ] );

        // set sizes
        $CODIGO->setSize(TWgtSizes::wsInt);
        $RAZAO->setSize(TWgtSizes::ws60);
        $FANTASIA->setSize(TWgtSizes::ws60);
        $TIPO->setSize(TWgtSizes::wsInt);
        $CNPJ->setSize(TWgtSizes::wsCNPJ);
        $CPF->setSize(TWgtSizes::wsCPF);
        $IE->setSize(TWgtSizes::wsIE);
        $ENDERECO->setSize(TWgtSizes::ws50);
        $NUMERO->setSize(TWgtSizes::wsInt);
        $BAIRRO->setSize(TWgtSizes::ws40);
        $COMPLEMENTO->setSize(TWgtSizes::ws40);
        $CEP->setSize(TWgtSizes::wsCEP);
        $CIDADE->setSize(TWgtSizes::ws35);
        $UF->setSize(TWgtSizes::ws30);
        $DDD->setSize(TWgtSizes::wsDDD);
        $FONE->setSize(TWgtSizes::wsFone);
        $CELULAR->setSize(TWgtSizes::wsFone);
        $FAX->setSize(TWgtSizes::wsFone);
        $ATIVO->setSize(TWgtSizes::wsBol);
        $DATACAD->setSize('100%');
        $USUARIOCAD->setSize('100%');
        $DATAALT->setSize('100%');
        $USUARIOALT->setSize('100%');
        $LOGO->setSize(TWgtSizes::wsBlob);
        $OBSERVACAO->setSize(TWgtSizes::wsBlob);
        $OBSERVACAONF->setSize(TWgtSizes::wsBlob);
      
        $CODIGO->setEditable(FALSE);
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-success';
        $this->form->addAction(_t('Back'),  new TAction(['EmpresasList', 'onReload']), 'far:arrow-alt-circle-left blue'); 
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TXMLBreadCrumb::create(['Menu', 'Empresas', 'Cadastro'], TRUE));
        $container->add($this->form);
        
        parent::add($container);
    }
    
   function fireEvents($object)
   {    return $object;    
   }
   
   function onChangeUF($param) 
    {
        $filter = new TCriteria();
        $filter->add(new TFilter('UF','=', $param['UF']));
        
        TDBCombo::reloadFromModel('form_Empresas', 'CIDADE', 'gexpertlotes', 'Municipios', 'CODIGO', '{RAIS}. {NOME}', "concat(RAIS,'. ',NOME)", $filter, TRUE, TRUE);
    }
    
     
}
