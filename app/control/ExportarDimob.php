<?php

    class ExportarDimob extends TPage
    {
        protected $form;
        protected $empresaid;
        protected $empresanome;
        protected $empresacnpj;
        
        public function __construct($param)
        {
            parent::__construct();
            
            $this->empresaid   = TSession::getValue('userunitid');
            $this->empresanome = TSession::getValue('userunitname');
            $this->empresacnpj = TSession::getValue('userunitcnpj');
            
            $this->form = new BootstrapFormBuilder('formExportarDimob');
            $this->form->setFormTitle('Exportar arquivo DIMOB');
            $this->form->style = 'width: 90%';
            
            $ano = new TDBCombo('ano', 'gexpertlotes', 'Versaodimob', 'ANO', 'ANO');
               // $ano->addItems([2015=>2015, 2016=>2016, 2017=>2017, 2018=>2018, 2019=>2019, 2020=>2020]);
            $retificadora = new TCombo('retificadora');
                $retificadora->addItems(['0'=>'0-Não', '1'=>'1-Sim']);
                $retificadora->setValue('0');
            $recibo = new TEntry('recibo');
            $sitespec = new TCombo('sitespec');
                $sitespec->addItems(['0'=>'0-Não', '1'=>'1-Sim']);
                $sitespec->setValue('0');
            $datasitespec = new TDate('datasitespec');
                $datasitespec->setMask(TMascara::maskDate);
            $codsitespec = new TCombo('codsitespec');
                $codsitespec->addItems(['00'=>'00-Normal', '01'=>'01-Extinção', '02'=>'02-Fusão', '03'=>'03-Incorporação', '04'=>'04-Cisão Total']);
                $codsitespec->setValue('00');
                
                
           $ano->setSize(TWgtSizes::wsInt);
           $retificadora->setSize(TWgtSizes::wsBol);
           $recibo->setSize(TWgtSizes::ws40);
           $sitespec->setSize(TWgtSizes::wsBol);
           $datasitespec->setSize(TWgtSizes::wsDate);
           $codsitespec->setSize(TWgtSizes::ws40);
           
           $this->form->addFields([new TLabel('Ano')],[$ano]);
           $this->form->addFields([new TLabel('Retificadora')],[$retificadora]);
           $this->form->addFields([new TLabel('Nr. Recibo')],[$recibo]); 
           $this->form->addFields([new TLabel('Situação Especial')],[$sitespec]); 
           $this->form->addFields([new TLabel('Data Situação Especial')],[$datasitespec]); 
           $this->form->addFields([new TLabel('Código Situação Especial')],[$codsitespec]);
           
           $this->form->addAction('Gerar', new TAction([$this, 'onGerarClick']), 'fa:download red');
           
           $container = new TVBox();
           $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));            
           $container->style = 'width: 100%';
           $container->add($this->form);
           
           parent::add($container);                                       
        }
        
        public function onGerarClick($param)
        {
            $vano          = !empty($param['ano'])?$param['ano']:'1999';
            $vretificadora = !empty($param['retificadora'])?$param['retificadora']:'0';
            $vrecibo       = !empty($param['recibo'])?$param['recibo']:"0000000000";
            $vsitespec     = !empty($param['sitespec'])?$param['sitespec']:'0';
            $vdatasitespec = !empty($param['datasitespec'])?TDate::convertToMask($param['datasitespec'], 'dd/mm/yyyy', 'ddmmyyyy'):'00000000';
            $vcodsitespec  = !empty($param['codsitespec'])?$param['codsitespec']:'00';
            $uniqid        = uniqid();
            
            $sqlLotes = "CREATE TEMPORARY TABLE lotes_{$uniqid} AS (              
                         SELECT L.ID AS ID,
            	         L.EMPRESA AS EMPRESA,
            			 E.RAZAOEMPRESA AS RAZAOEMPRESA,
            			 E.CNPJEMPRESA AS CNPJEMPRESA,
            			 L.EMPREENDIMENTO AS EMPREENDIMENTO,
            			 E.DESCRICAO AS DESCRICAO,
            			 E.DATAAQUISICAO AS DATAAQUISICAO,
            			 (SELECT vendas.EMISSAO FROM vendas WHERE ((vendas.EMPRESA = L.EMPRESA) AND (vendas.EMPREENDIMENTO = L.EMPREENDIMENTO) AND (vendas.LOTE = L.CODIGO) AND (vendas.QUADRA = L.QUADRA)) ORDER BY COALESCE(vendas.EMISSAO,'2100-12-31') DESC LIMIT 1,1) AS DATAVENDA,
            			 (SELECT vendas.CANCELAMENTO FROM vendas WHERE ((vendas.EMPRESA = L.EMPRESA) AND (vendas.EMPREENDIMENTO = L.EMPREENDIMENTO) AND (vendas.LOTE = L.CODIGO) AND (vendas.QUADRA = L.QUADRA)) ORDER BY COALESCE(vendas.CANCELAMENTO,'2100-12-31') DESC LIMIT 1,1) AS DATADISTRATO,
            			 L.CODIGO AS CODIGO,
            			 L.QUADRA AS QUADRA,
            			 L.DESMEMBRAMENTO AS DESMEMBRAMENTO,
            			 L.AREA AS AREA,
            			 L.VLRCUSTO AS VLRCUSTO,
            			 L.SITUACAO AS SITUACAO,
            			 L.ATIVO AS ATIVO,
            			 L.CONTACTBCUSTO AS CONTACTBCUSTO,
            			 L.CONTACTBESTOQUE AS CONTACTBESTOQUE,
            			 L.CONTACTBRECEITADIFER AS CONTACTBRECEITADIFER,
            			 L.CONTACTBDESPESADIFER AS CONTACTBDESPESADIFER,
            			 L.HISTCTBCUSTO AS HISTCTBCUSTO,
            			 L.HISTCTBRECEITADIFER AS HISTCTBRECEITADIFER,
            			 L.HISTCTBDESPESADIFER AS HISTCTBDESPESADIFER,
            			 L.HISTCTBESTOQUE AS HISTCTBESTOQUE,
            			 L.CONTACTBRECEITA AS CONTACTBRECEITA,
            			 (L.AREA / E.AREATOTAL) AS EQUIVALENCIA,
            			 L.CONTACTBRECEITADIFERLP AS CONTACTBRECEITADIFERLP,
            			 L.CONTACTBDESPESADIFERLP AS CONTACTBDESPESADIFERLP
            	FROM lotes L
            	JOIN sel_empreendimentos E ON (E.CODIGO = L.EMPREENDIMENTO AND E.EMPRESA = L.EMPRESA)
            	WHERE (E.EMPRESA = {$this->empresaid}));";
            	
            	
          $sqlVendas = "
            CREATE TEMPORARY TABLE IF NOT EXISTS vendas_{$uniqid} as
			(select v.LANCAMENTO AS LANCAMENTO,
			       v.EMPRESA AS EMPRESA,
					 e.RAZAO AS RAZAOEMPRESA,
					 e.CNPJ AS CNPJEMPRESA,
					 v.EMPREENDIMENTO AS EMPREENDIMENTO,
					 l.DESCRICAO AS DESCRICAO,
					 v.LOTE AS LOTE,
					 v.ENTIDADE AS ENTIDADE,
					 p.RAZAO AS RAZAOENTIDADE,
					 p.CNPJ AS CNPJENTIDADE,
					 p.CPF AS CPF,
					 v.EMISSAO AS EMISSAO,
					 v.VALOR AS VALOR,
					 v.ENTRADA AS ENTRADA,
					 v.CONTRATO AS CONTRATO,
					 v.PARCELAS AS PARCELAS,
					 v.REAJUSTE AS REAJUSTE,
					 v.OBSERVACAO AS OBSERVACAO,
					 v.CANCELADO AS CANCELADO,
					 v.CANCELAMENTO AS CANCELAMENTO,
					 v.ESTORNO AS ESTORNO,
					 v.SALDO AS SALDO,
					 v.QUADRA AS QUADRA,
					 v.VALORPARCELA AS VALORPARCELA,
					 v.VALORPARCELARESCISAO AS VALORPARCELARESCISAO,
					 v.PARCELASRESCISAO AS PARCELASRESCISAO,
					 v.CONTACTBCANCELAMENTO AS CONTACTBCANCELAMENTO,
					 l.DATAAQUISICAO AS DATAAQUISICAO,
					 t.DESMEMBRAMENTO AS DESMEMBRAMENTO,
					 cast(t.VLRCUSTO as decimal(18,2)) AS CUSTO,cast((t.VLRCUSTO * cast((1 - (v.ENTRADA / v.VALOR)) as decimal(18,2))) as decimal(18,2)) AS CUSTODESCONTADO,
					 l.TIPO AS TIPO,
					 v.PARCELAS AS TOTALPARCELAS,
					 v.CONTACTBENTIDADE AS CONTACTBENTIDADECP,
					 v.CONTACTBENTIDADELP AS CONTACTBENTIDADELP,
					 v.CONTACTBENTRADA AS CONTACTBRECEBIMENTO,
			         cast(concat(extract(year from coalesce(v.CANCELAMENTO,'2100-12-31')),'-',extract(month from coalesce(v.CANCELAMENTO,'2100-12-31')),'-','01') as date) AS COMPETENCIADISTRATO,
					 l.CONTACTBCUSTO AS CONTACTBEMPREENDCUSTO,
					 t.CONTACTBRECEITADIFER AS CONTACTBRECEITADIFER,
					 t.CONTACTBRECEITADIFERLP AS CONTACTBRECEITADIFERLP,
					 t.CONTACTBDESPESADIFER AS CONTACTBDESPESADIFER,
					 t.CONTACTBDESPESADIFERLP AS CONTACTBDESPESADIFERLP,
					 c.CODIGOEMPRESACONTABIL AS EMPRESACONTABIL,
					 l.CONTACTBDEVOLUCAODRE AS CONTACTBDISTRATODRE,
					 l.CONTACTBINFRAESTRUTURA AS CONTACTBINFRAESTRUTURA,
					 t.EQUIVALENCIA AS EQUIVALENCIA 
			  FROM  vendas v 
			  join empresas e ON (e.CODIGO = v.EMPRESA) 
			  join entidades p ON (p.CODIGO = v.ENTIDADE) 
			  join empreendimentos l ON (l.CODIGO = v.EMPREENDIMENTO) 
			  join lotes_{$uniqid} t ON ((t.EMPRESA = v.EMPRESA) and (t.EMPREENDIMENTO = v.EMPREENDIMENTO) and (t.CODIGO = v.LOTE) and (t.QUADRA = v.QUADRA) ) 
			  join configuracoes c ON (c.EMPRESA = v.EMPRESA)
			 where (v.EMPRESA = {$this->empresaid} AND 
			        v.EMISSAO BETWEEN '{$vano}-01-01' AND '{$vano}-12-31') );";  
			        
		   $sqlEmpreendimentos = 
		     "CREATE TEMPORARY TABLE IF NOT EXISTS empreendimentos_{$uniqid} AS
		     (SELECT l.CODIGO AS CODIGO,
                     l.EMPRESA AS EMPRESA,
            		 e.RAZAO AS RAZAOEMPRESA,
            		 e.CNPJ AS CNPJEMPRESA,
            		 l.TIPO AS TIPO,
            		 (CASE WHEN (l.TIPO = 0) THEN '0-URBANO' ELSE '1-RURAL' END) AS DESCRTIPO,
            		 l.DESCRICAO AS DESCRICAO,
            		 l.DATAAQUISICAO AS DATAAQUISICAO,
            		 l.VLRAQUISICAO AS VLRAQUISICAO,
            		 l.AREATOTAL AS AREATOTAL,
            		 l.QUADRAS AS QUADRAS,
            		 l.LOTES AS LOTES,
            		 l.ENDERECO AS ENDERECO,
            		 l.NUMERO AS NUMERO,
            		 l.BAIRRO AS BAIRRO,
            		 l.COMPLEMENTO AS COMPLEMENTO,
            		 l.CEP AS CEP,
            		 l.CIDADE AS CIDADE,
            		 m.ESTADUAL AS CIDADE_CODESTADUAL,
            		 m.FEDERAL AS CIDADE_CODFEDERAL,
            		 m.RAIS AS CIDADE_CODRAIS,
            		 m.NOME AS CIDADE_NOME,
            		 l.UF AS UF,
            		 u.SIGLA AS UF_SIGLA,
            		 l.OBS AS OBS,
            		 l.ATIVO AS ATIVO,
            		 l.CONTACTB AS CONTACTB,
            		 l.CONTACTBRECEITA AS CONTACTBRECEITA,
            		 l.CONTACTBCUSTO AS CONTACTBCUSTO,
            		 l.CONTACTBDEVOLUCAO AS CONTACTBDEVOLUCAO,
            		 l.CONTACTBINFRAESTRUTURA AS CONTACTBINFRAESTRUTURA,
            		 l.CONTACTBPAGTO AS CONTACTBPAGTO,
            		 l.CONTACTBATUALIZACAO AS CONTACTBATUALIZACAO,
            		 l.CONTACTBJUROS AS CONTACTBJUROS,
            		 l.CONTACTBRECEITAEVENTUAL AS CONTACTBRECEITAEVENTUAL,
            		 l.AREALOTE AS AREALOTE,
            		 l.CUSTOLOTE AS CUSTOLOTE
            FROM (((empreendimentos l
            JOIN empresas e)
            JOIN municipios m)
            JOIN estados u)
            WHERE ((e.CODIGO = l.EMPRESA) AND 
                   (m.CODIGO = l.CIDADE) AND 
            		 (m.UF = l.UF) AND 
            		 (u.CODIGO = l.UF) AND
            		 (l.EMPRESA = {$this->empresaid})));";	  
            		 
          $sqlEmpresas = 
          "CREATE TEMPORARY TABLE IF NOT EXISTS empresas_{$uniqid} AS
          (SELECT emp.CODIGO AS CODIGO,
                 emp.RAZAO AS RAZAO,
        		 emp.FANTASIA AS FANTASIA,
        		 emp.TIPO AS TIPO,
        		 emp.CNPJ AS CNPJ,
        		 emp.CPF AS CPF,
        		 emp.IE AS IE,
        		 emp.ENDERECO AS ENDERECO,
        		 emp.NUMERO AS NUMERO,
        		 emp.BAIRRO AS BAIRRO,
        		 emp.COMPLEMENTO AS COMPLEMENTO,
        		 emp.CEP AS CEP,
        		 emp.CIDADE AS CIDADE,
        		 mun.ESTADUAL AS CIDADE_CODESTADUAL,
        		 mun.FEDERAL AS CIDADE_CODFEDERAL,
        		 mun.RAIS AS CIDADE_CODRAIS,
        		 mun.NOME AS CIDADE_NOME,
        		 emp.UF AS UF,
        		 uf.SIGLA AS UF_SIGLA,
        		 emp.DDD AS DDD,
        		 emp.FONE AS FONE,
        		 emp.CELULAR AS CELULAR,
        		 emp.FAX AS FAX,
        		 emp.OBSERVACAO AS OBSERVACAO,
        		 emp.OBSERVACAONF AS OBSERVACAONF,
        		 emp.ATIVO AS ATIVO,
        		 cfg.SOCIORESPONSAVEL AS SOCIO,
        		 pes.RAZAO AS SOCIO_NOME,
        		 pes.CPF AS SOCIO_CPF,
        		 cfg.DATAABERTURA AS DATAABERTURA,
        		 cfg.TIPOREGISTRO AS TIPOREGISTRO,
        		 cfg.DATAREGISTRO AS DATAREGISTRO,
        		 cfg.RAMOATIVIDADE AS RAMOATIVIDADE,
        		 cfg.REGIMETRIBUTARIO AS REGIMETRIBUTARIO
        FROM ((((empresas emp
        JOIN municipios mun)
        JOIN estados uf)
        JOIN configuracoes cfg)
        JOIN entidades pes)
        WHERE ((1 = 1) AND 
               (mun.CODIGO = emp.CIDADE) AND 
        	   (mun.UF = emp.UF) AND 
        	   (uf.CODIGO = emp.UF) AND 
        	   (cfg.EMPRESA = emp.CODIGO) AND 
        	   (pes.CODIGO = cfg.SOCIORESPONSAVEL) AND
        	   (emp.CODIGO = {$this->empresaid})))";  		        	
            	 	
            	
          $sqlR01 = 
          "SELECT DISTINCT 'R01' AS TIPO, 
        		RPAD(REPLACE(REPLACE(REPLACE(e.CNPJ,'.',''),'/',''),'-',''),14,' ') AS CNPJDECLARANTE,
        		d.ANO AS ANOCALENDARIO, 
        		{$vretificadora} AS RETIFICADORA,
        		'{$vrecibo}' AS RECIBO,
        		{$vsitespec} AS SITUAESPEC,
        		'{$vdatasitespec}' AS DATASITUAESPEC,
        		'{$vcodsitespec}' AS CODSITUAESPEC,
        		removeAcentos(RPAD(e.RAZAO,60,' ')) AS NOMEEMPRESARIAL, 
        		RPAD(REPLACE(REPLACE(REPLACE(e.SOCIO_CPF,'.',''),'/',''),'-',''),11,' ') AS CPFRESPONSAVEL, 
        		removeAcentos(RPAD(CONCAT(CONVERT(e.ENDERECO USING utf8),',', CAST(e.NUMERO AS CHAR(9) CHARSET utf8),' ', CONVERT(e.BAIRRO USING utf8),' ', CONVERT(e.COMPLEMENTO USING utf8)),120,' ')) AS ENDERECO, 
        		RPAD(e.UF_SIGLA,2,' ') AS UF, 
        		LPAD(e.CIDADE_CODFEDERAL,4,0) AS MUNICIPIO, 
        		RPAD('',20,' ') AS RESERVADO1, 
        		RPAD('',10,' ') AS RESERVADO2
           FROM (empresas_{$uniqid} e
           JOIN versaodimob d) 
          WHERE (d.ANO = {$vano} and e.CODIGO={$this->empresaid})";  
          
          
          $sqlR03 = 
           "SELECT DISTINCT 'R03' AS TIPO, 
                    RPAD(REPLACE(REPLACE(REPLACE(v.CNPJEMPRESA,'.',''),'/',''),'-',''),14,' ') AS CNPJDECLARANTE, 
                    EXTRACT(YEAR FROM v.EMISSAO) AS ANOCALENDARIO, 
                    LPAD(v.LANCAMENTO,5,0) AS SEQUENCIALVENDA, 
                    RPAD(REPLACE(REPLACE(REPLACE((CASE WHEN (p.TIPO = 'F') THEN p.CPF ELSE p.CNPJ END),'.',''),'/',''),'-',''),14,' ') AS CNPJCOMPRADOR, 
                    removeAcentos(RPAD(v.RAZAOENTIDADE,60,' ')) AS RAZAOCOMPRADOR, 
                    RPAD(v.CONTRATO,6,' ') AS NUMEROCONTRATO, 
                    CONCAT(LPAD(EXTRACT(DAY FROM v.EMISSAO),2,0), LPAD(EXTRACT(MONTH FROM v.EMISSAO),2,0), EXTRACT(YEAR FROM v.EMISSAO)) AS DATACONTRATO, 
                    LPAD(REPLACE(COALESCE(CAST(v.VALOR AS DECIMAL(18,2)),0),'.',''),14,0) AS VALOROPERACAO, 
                    LPAD(COALESCE(REPLACE((SELECT (SUM(CAST(COALESCE(vpb.VALOR,0) AS DECIMAL(18,2))) + (CASE WHEN (EXTRACT(YEAR FROM v.EMISSAO) = EXTRACT(YEAR FROM v.EMISSAO)) THEN CAST(v.ENTRADA AS DECIMAL(18,2)) ELSE 0 END))
                                             FROM vendas_parcelas_baixas vpb
                                            WHERE ((vpb.EMPRESA = v.EMPRESA) 
                    								  AND (vpb.VENDA = v.LANCAMENTO) 
                    								  AND (EXTRACT(YEAR FROM vpb.RECEBIMENTO) = EXTRACT(YEAR FROM v.EMISSAO)))),'.',''),0),14,0) AS VALORPAGOANO,
                    (CASE WHEN (v.TIPO = 0) THEN 'U' ELSE 'R' END) AS TIPOIMOVEL, 
                    removeAcentos(RPAD(CONCAT(CONVERT(e.ENDERECO USING utf8),',', CAST(e.NUMERO AS CHAR(9) CHARSET utf8),'-Q ', CAST(v.QUADRA AS CHAR(9) CHARSET utf8),'-Lt ', CAST(v.LOTE AS CHAR(9) CHARSET utf8)),60,' ')) AS ENDERECOIMOVEL, 
                    LPAD(REPLACE(REPLACE(e.CEP,'.',''),'-',''),8,0) AS CEP, 
                    LPAD(m.FEDERAL,4,0) AS CODIGOMUNICIPIO, 
                    RPAD('',20,' ') AS RESERVADO1, 
                    RPAD(u.SIGLA,2,' ') AS UF, 
                    RPAD('',10,' ') AS RESERVADO2
                    FROM ((((vendas_{$uniqid} v
                    JOIN empreendimentos_{$uniqid} e)
                    JOIN municipios m)
                    JOIN entidades p)
                    JOIN estados u)
                    WHERE ((e.CODIGO = v.EMPREENDIMENTO) AND 
                           (e.EMPRESA = v.EMPRESA) AND 
                    		 (m.CODIGO = e.CIDADE) AND
                    		 (m.UF = e.UF) AND 
                    		 (p.CODIGO = v.ENTIDADE) AND 
                    		 (u.CODIGO = e.UF));";

          $sqlT9 = 'select * from sel_dimob_t9';

          $sqlHeader = 'select * from sel_dimob_header';
          
          TTransaction::open('gexpertlotes');
          
          $conexao = TTransaction::get();
          
          $lote = $conexao->query($sqlLotes);
          
          $vendas = $conexao->query($sqlVendas);
          
          $empresas = $conexao->query($sqlEmpresas);
          
          $empreendimentos = $conexao->query($sqlEmpreendimentos);
                   
          $qrR01     = $conexao->query($sqlR01);
          $qrR01->execute();
          $qrR03     = $conexao->Query($sqlR03);
          $qrR03->execute(); 
        
          $qrT9      = $conexao->Query($sqlT9);
          $qrT9->execute();
          $qrHeader  = $conexao->Query($sqlHeader);
          $qrHeader->execute();
          $csv = new TCsvFile('tmp/dimob_'.$this->empresanome.'_'.$vano.'.txt');
                    
          $csv->open(moRewrite);
      
          $csv->writeqr($qrHeader->fetchAll(),'');
          $csv->writeqr($qrR01->fetchAll(), '');
          $csv->writeqr($qrR03->fetchAll(), '');
          $csv->writeqr($qrT9->fetchAll(), '');
          
          $csv->download();
          
          $csv->close(); 
          
          TTransaction::close();
   
        }       
    }

?>
