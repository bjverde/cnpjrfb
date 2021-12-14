-- seleciona o banco
use receita ;

-- declaração de variáveis 
declare @SCHEMA_NAME NVARCHAR(30);
declare @SQL NVARCHAR(max);
declare @CURRENT_USER NVARCHAR(100);

-- declaração das constantes 
declare @SCHEMA_DEFAULT_NAME NVARCHAR(30) = 'dados_rfb';
declare @COMENTARIO_LEVEL0NAME NVARCHAR(50) = N'''' + @SCHEMA_DEFAULT_NAME + ''''

-- seleciona o banco default informado no schema do banco
SELECT @SCHEMA_NAME =  s.name  
	FROM sys.schemas s 
WHERE s.name = @SCHEMA_DEFAULT_NAME;

--  select * from sys.schemas s;

-- Apaga todas as tabelas 
DROP TABLE IF EXISTS cnae ;
DROP TABLE IF EXISTS natju ;
DROP TABLE IF EXISTS quals ;
DROP TABLE IF EXISTS pais ;
DROP TABLE IF EXISTS moti ;
DROP TABLE IF EXISTS munic ;
DROP TABLE IF EXISTS estabelecimento ;
DROP TABLE IF EXISTS simples ;
DROP TABLE IF EXISTS empresa ;


-- -----------------------------------------------------
-- Schema @schema_default_name (dados_rfb)
-- -----------------------------------------------------
IF  @schema_name is not null 
	begin
		set @sql = ' Drop schema ' +  @schema_default_name + '; '; 
		exec sp_executesql @sql;	
	end;

set @sql = 'Create schema ' + @schema_default_name + '; '; 

exec sp_executesql @sql;	

select @current_user = CURRENT_USER;

set @sql = 'ALTER USER "'+ @CURRENT_USER+'" WITH DEFAULT_SCHEMA = "' + @SCHEMA_DEFAULT_NAME +'";';
exec sp_executesql @sql;	

-- SELECT SCHEMA_NAME()

-- -----------------------------------------------------
-- Table 'cnae'
-- -----------------------------------------------------
CREATE TABLE cnae (
			  codigo INT NOT NULL,
			  descricao VARCHAR(1000) NOT NULL,
			  CONSTRAINT [PK_CNAE] PRIMARY KEY CLUSTERED 
			  (
				codigo ASC
			  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
  
			  )ON [PRIMARY]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'PK_CNAE' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'CNAE', @level2type=N'COLUMN',@level2name=N'codigo'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Descrição do CNAE' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'CNAE', @level2type=N'COLUMN',@level2name=N'descricao'


-- -----------------------------------------------------
-- Table natju
-- -----------------------------------------------------

CREATE TABLE natju (
			  codigo INT NOT NULL,
			  descricao VARCHAR(1000) NOT NULL,
  			  CONSTRAINT [PK_NATJU] PRIMARY KEY CLUSTERED 
			  (
				codigo ASC
			  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
  
			  )ON [PRIMARY]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'PK_NATJU' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'NATJU', @level2type=N'COLUMN',@level2name=N'codigo'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Descrição do NATJU' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'NATJU', @level2type=N'COLUMN',@level2name=N'descricao'


-- -----------------------------------------------------
-- Table quals
-- -----------------------------------------------------

CREATE TABLE quals (
				  codigo INT NOT NULL,
				  descricao VARCHAR(1000) NOT NULL,
    			  CONSTRAINT [PK_QUALS] PRIMARY KEY CLUSTERED 
			  (
				codigo ASC
			  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
  
			  )ON [PRIMARY]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'PK_QUALS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'QUALS', @level2type=N'COLUMN',@level2name=N'codigo'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Descrição do QUALS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'QUALS', @level2type=N'COLUMN',@level2name=N'descricao'


-- -----------------------------------------------------
-- Table pais
-- -----------------------------------------------------

CREATE TABLE pais (
				codigo INT NOT NULL,
				descricao VARCHAR(500) NOT NULL,
      			  CONSTRAINT [PK_PAIS] PRIMARY KEY CLUSTERED 
			  (
				codigo ASC
			  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
  
			  )ON [PRIMARY]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'PK_PAIS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'PAIS', @level2type=N'COLUMN',@level2name=N'codigo'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Descrição do PAIS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'PAIS', @level2type=N'COLUMN',@level2name=N'descricao'


-- -----------------------------------------------------
-- Table moti
-- -----------------------------------------------------

CREATE TABLE moti (
				  codigo INT NOT NULL,
				  descricao VARCHAR(1000) NOT NULL,
         			  CONSTRAINT [PK_MOTI] PRIMARY KEY CLUSTERED 
			  (
				codigo ASC
			  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
  
			  )ON [PRIMARY]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'PK_MOTI' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'MOTI', @level2type=N'COLUMN',@level2name=N'codigo'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Descrição do MOTI' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'MOTI', @level2type=N'COLUMN',@level2name=N'descricao'


-- -----------------------------------------------------
-- Table munic
-- -----------------------------------------------------

CREATE TABLE munic (
					  codigo INT NOT NULL,
					  descricao VARCHAR(1000) NOT NULL,
             		 CONSTRAINT [PK_MUNIC] PRIMARY KEY CLUSTERED 
			  (
				codigo ASC
			  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
  
			  )ON [PRIMARY]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'PK_MUNIC' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'MUNIC', @level2type=N'COLUMN',@level2name=N'codigo'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Descrição do MUNIC' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'MUNIC', @level2type=N'COLUMN',@level2name=N'descricao'


-- -----------------------------------------------------
-- Table estabelecimento
-- -----------------------------------------------------

CREATE TABLE estabelecimento (
  cnpj_basico CHAR(8) NOT NULL,
  cnpj_ordem CHAR(4) NOT NULL,
  cnpj_dv CHAR(2) NOT NULL,
  identificador_matriz_filial CHAR(1) NOT NULL,
  nome_fantasia VARCHAR(1000) NULL,
  situacao_cadastral CHAR(1) NOT NULL,
  data_situacao_cadastral DATE NULL,
  motivo_situacao_cadastral INT NOT NULL,
  nome_cidade_exterior VARCHAR(45) NULL,
  pais INT NULL,
  data_inicio_atividade DATETIME NULL,
  cnae_fiscal_principal INT NOT NULL,
  cnae_fiscal_secundaria VARCHAR(1000) NULL,
  tipo_logradouro VARCHAR(500) NULL,
  logradouro VARCHAR(1000) NULL,
  numero VARCHAR(45) NULL,
  complemento VARCHAR(100) NULL,
  bairro VARCHAR(45) NULL,
  cep VARCHAR(45) NULL,
  uf VARCHAR(45) NULL,
  municipio INT NULL,
  ddd_1 VARCHAR(45) NULL,
  telefone_1 VARCHAR(45) NULL,
  ddd_2 VARCHAR(45) NULL,
  telefone_2 VARCHAR(45) NULL,
  ddd_fax VARCHAR(45) NULL,
  fax VARCHAR(45) NULL,
  correio_eletronico VARCHAR(45) NULL,
  situacao_especial VARCHAR(45) NULL,
  data_situacao_especial DATE NULL,
	CONSTRAINT [PK_CNPJ_BASICO] PRIMARY KEY CLUSTERED 
		  (
			cnpj_basico ASC
		  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
   )ON [PRIMARY]

CREATE INDEX fk_estabelecimento_pais_idx ON estabelecimento (pais ASC);
CREATE INDEX fk_estabelecimento_munic1_idx ON estabelecimento (municipio ASC);
CREATE INDEX fk_estabelecimento_cnae1_idx ON estabelecimento (cnae_fiscal_principal ASC);
CREATE INDEX fk_estabelecimento_moti1_idx ON estabelecimento (motivo_situacao_cadastral ASC);

ALTER TABLE estabelecimento  WITH CHECK ADD  CONSTRAINT [fk_estabelecimento_pais] FOREIGN KEY([pais])
REFERENCES pais ([codigo])
ALTER TABLE estabelecimento CHECK CONSTRAINT [fk_estabelecimento_pais]

ALTER TABLE estabelecimento  WITH CHECK ADD  CONSTRAINT fk_estabelecimento_munic1 FOREIGN KEY([municipio])
REFERENCES munic ([codigo])
ALTER TABLE estabelecimento CHECK CONSTRAINT fk_estabelecimento_munic1

ALTER TABLE estabelecimento  WITH CHECK ADD  CONSTRAINT [fk_estabelecimento_cnae1] FOREIGN KEY([cnae_fiscal_principal])
REFERENCES cnae ([codigo])
ALTER TABLE estabelecimento CHECK CONSTRAINT [fk_estabelecimento_cnae1]
 
ALTER TABLE estabelecimento  WITH CHECK ADD  CONSTRAINT [fk_estabelecimento_moti1] FOREIGN KEY([motivo_situacao_cadastral])
REFERENCES moti ([codigo])
ALTER TABLE estabelecimento CHECK CONSTRAINT [fk_estabelecimento_moti1]

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'COMMENT NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'cnpj_basico'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'cnpj_ordem'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'cnpj_dv'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL:\n1 – MATRIZ\n2 – FILIAL' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'identificador_matriz_filial'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CORRESPONDE AO NOME FANTASIA' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'nome_fantasia'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA SITUAÇÃO CADASTRAL:\n01 – NULA\n2 – ATIVA\n3 – SUSPENSA\n4 – INAPTA\n08 – BAIXADA' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'situacao_cadastral'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DO EVENTO DA SITUAÇÃO CADASTRAL' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'data_situacao_cadastral'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'motivo_situacao_cadastral'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DA CIDADE NO EXTERIOR' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'nome_cidade_exterior'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO PAIS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'pais'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE INÍCIO DA ATIVIDADE' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'data_inicio_atividade'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'cnae_fiscal_principal'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'cnae_fiscal_secundaria'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'tipo_logradouro' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'tipo_logradouro'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'logradouro'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ ‘S/N’.' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'numero'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'complemento'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'bairro'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'cep'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'uf'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'municipio'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'ddd_1' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'ddd_1'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'telefone_1 ' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'telefone_1'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'ddd_2' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'ddd_2'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'telefone_2' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'telefone_2'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'ddd_fax' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'ddd_fax'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'fax' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'fax'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'correio_eletronico' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'correio_eletronico'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'situacao_especial' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'situacao_especial'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'data_situacao_especial' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'ESTABELECIMENTO', @level2type=N'COLUMN',@level2name=N'data_situacao_especial'

-- -----------------------------------------------------
-- Table 'simples'
-- -----------------------------------------------------


CREATE TABLE simples (
  cnpj_basico CHAR(8) NOT NULL,
  opcao_pelo_simples CHAR(1) NULL,
  data_opcao_simples DATE NULL,
  data_exclusao_simples DATE NULL,
  opcao_mei CHAR(1) NULL,
  data_opcao_mei DATE NULL,
  data_exclusao_mei DATE NULL,
CONSTRAINT [PK_SIMPLES_CNPJ_BASICO] PRIMARY KEY CLUSTERED 
		  (
			cnpj_basico ASC
		  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
   )ON [PRIMARY]


CREATE INDEX fk_simples_estabelecimento1_idx ON simples (cnpj_basico ASC);


ALTER TABLE simples  WITH CHECK ADD  CONSTRAINT [fk_simples_estabelecimento1] FOREIGN KEY([cnpj_basico])
REFERENCES estabelecimento ([cnpj_basico])
ALTER TABLE simples CHECK CONSTRAINT [fk_simples_estabelecimento1]


EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'cnpj_basico'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES.\n S - SIM\n N - NÃO\n EM BRANCO – OUTROS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'opcao_pelo_simples'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE OPÇÃO PELO SIMPLES' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'data_opcao_simples'  

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE EXCLUSÃO DO SIMPLES' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'data_exclusao_simples'  

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI\n S - SIM\n N - NÃO\n EM BRANCO - OUTROS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'opcao_mei'  

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE OPÇÃO PELO MEI' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'data_opcao_mei'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE EXCLUSÃO DO MEI' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SIMPLES', @level2type=N'COLUMN',@level2name=N'data_exclusao_mei'
   
   
-- -----------------------------------------------------
-- Table socios
-- -----------------------------------------------------

CREATE TABLE socios (
  cnpj_basico CHAR(8) NOT NULL,
  identificador_socio INT NOT NULL,
  nome_socio_razao_social VARCHAR(1000) NULL,
  cpf_cnpj_socio VARCHAR(45) NULL,
  qualificacao_socio INT NULL,
  data_entrada_sociedade DATE NULL,
  pais INT NULL,
  representante_legal VARCHAR(45) NULL,
  nome_do_representante VARCHAR(500) NULL,
  qualificacao_representante_legal INT NULL,
  faixa_etaria INT NULL,
CONSTRAINT [PK_SOCIOS_CNPJ_BASICO] PRIMARY KEY CLUSTERED 
		  (
			cnpj_basico ASC
		  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
   )ON [PRIMARY]


CREATE INDEX fk_socios_quals1_idx ON socios (qualificacao_socio ASC);
CREATE INDEX fk_socios_pais1_idx ON socios (pais ASC);
CREATE INDEX fk_socios_quals2_idx ON socios (qualificacao_representante_legal ASC);

ALTER TABLE socios  WITH CHECK ADD  CONSTRAINT [fk_socios_quals1] FOREIGN KEY([qualificacao_socio])
REFERENCES quals ([codigo])
ALTER TABLE socios CHECK CONSTRAINT [fk_socios_quals1]

ALTER TABLE socios  WITH CHECK ADD  CONSTRAINT [fk_socios_pais1] FOREIGN KEY([pais])
REFERENCES pais ([codigo])
ALTER TABLE socios CHECK CONSTRAINT [fk_socios_pais1]

ALTER TABLE socios  WITH CHECK ADD  CONSTRAINT [fk_socios_quals2] FOREIGN KEY([qualificacao_representante_legal])
REFERENCES quals ([codigo])
ALTER TABLE socios CHECK CONSTRAINT [fk_socios_quals2]


EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'cnpj_basico'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO IDENTIFICADOR DE SÓCIO\n1 – PESSOA JURÍDICA\n2 – PESSOA FÍSICA\n3 – ESTRANGEIRO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'identificador_socio'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL \nE/OU NOME EMPRESARIAL DA PESSOA JURÍDICA \nE/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'nome_socio_razao_social'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO).' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'cpf_cnpj_socio'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'qualificacao_socio'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'data_entrada_sociedade' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'data_entrada_sociedade'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'pais'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO DO CPF DO REPRESENTANTE LEGAL' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'representante_legal'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'nome_do_representante' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'nome_do_representante'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'qualificacao_representante_legal'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO CORRESPONDENTE À FAIXA ETÁRIA DO SÓCIO.\nBaseada na data de nascimento do CPF de cada sócio, deverá ser criado o valor para o\ncampo \"Faixa Etária\" conforme a regra abaixo:\n- 1 para os intervalos entre 0 a 12 anos;\n- 2 para os intervalos entre 13 a 20 anos;\n- 3 para os intervalos entre 21 a 30 anos;\n- 4 para os intervalos entre 31 a 40 anos;\n- 5 para os intervalos entre 41 a 50 anos;\n- 6 para os intervalos entre 51 a 60 anos;\n- 7 para os intervalos entre 61 a 70 anos;\n- 8 para os intervalos entre 71 a 80 anos; - 9 para maiores de 80 anos.\n- 0 para não se aplica' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'SOCIOS', @level2type=N'COLUMN',@level2name=N'faixa_etaria'


-- -----------------------------------------------------
-- Table empresa
-- -----------------------------------------------------

CREATE TABLE empresa (
  cnpj_basico CHAR(8) NOT NULL,
  razao_social VARCHAR(1000) NULL,
  natureza_juridica INT NULL,
  qualificacao_responsavel INT NULL,
  capital_social VARCHAR(45) NULL,
  porte_empresa VARCHAR(45) NULL,
  ente_federativo_responsavel VARCHAR(45) NULL,
CONSTRAINT [PK_EMPRESA_CNPJ_BASICO] PRIMARY KEY CLUSTERED 
		  (
			cnpj_basico ASC
		  )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
   )ON [PRIMARY]

CREATE INDEX fk_empresa_estabelecimento1_idx ON empresa (cnpj_basico ASC);
CREATE INDEX fk_empresa_natju1_idx ON empresa (natureza_juridica ASC);
CREATE INDEX fk_empresa_quals1_idx ON empresa (qualificacao_responsavel ASC);



ALTER TABLE empresa  WITH CHECK ADD  CONSTRAINT [fk_empresa_estabelecimento1] FOREIGN KEY([cnpj_basico])
REFERENCES estabelecimento ([cnpj_basico])
ALTER TABLE empresa CHECK CONSTRAINT [fk_empresa_estabelecimento1]

ALTER TABLE empresa  WITH CHECK ADD  CONSTRAINT [fk_empresa_natju1] FOREIGN KEY([natureza_juridica])
REFERENCES natju ([codigo])
ALTER TABLE empresa CHECK CONSTRAINT [fk_empresa_natju1]

ALTER TABLE empresa  WITH CHECK ADD  CONSTRAINT [fk_empresa_quals1] FOREIGN KEY([qualificacao_responsavel])
REFERENCES quals ([codigo])
ALTER TABLE empresa CHECK CONSTRAINT [fk_empresa_quals1]


EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'cnpj_basico' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'cnpj_basico'
  
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME EMPRESARIAL DA PESSOA JURÍDICA' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'razao_social'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA NATUREZA JURÍDICA' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'natureza_juridica'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'qualificacao_responsavel'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CAPITAL SOCIAL DA EMPRESA' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'capital_social'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO PORTE DA EMPRESA:\n1 – NÃO INFORMADO\n2 - MICRO EMPRESA\n03 - EMPRESA DE PEQUENO PORTE\n05 - DEMAIS' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'porte_empresa'

EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO' , @level0type=N'SCHEMA',@level0name= @schema_default_name, @level1type=N'TABLE',@level1name=N'EMPRESA', @level2type=N'COLUMN',@level2name=N'ente_federativo_responsavel'

/* 
-- TODO:Trecho pra criação do usuário 

-- original

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP USER IF EXISTS dados_rfb@%;
CREATE USER dados_rfb@% IDENTIFIED BY 123456;
GRANT ALL PRIVILEGES ON dados_rfb.* TO dados_rfb@%;

-- SQLSERVER
IF NOT EXISTS (SELECT * FROM sys.database_principals WHERE name = N'dados_rfb')
BEGIN
    CREATE USER [dados_rfb]
    EXEC sp_addrolemember @SCHEMA_DEFAULT_NAME, N'NewAdminName'
END;
GO

*/