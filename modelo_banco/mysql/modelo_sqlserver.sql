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
-- Table 'estabelecimento'
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS 'estabelecimento' (
  'cnpj_basico' CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  'cnpj_ordem' CHAR(4) NOT NULL COMMENT 'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).',
  'cnpj_dv' CHAR(2) NOT NULL COMMENT 'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).',
  'identificador_matriz_filial' CHAR(1) NOT NULL COMMENT 'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL:\n1 – MATRIZ\n2 – FILIAL',
  'nome_fantasia' VARCHAR(1000) NULL COMMENT 'CORRESPONDE AO NOME FANTASIA',
  'situacao_cadastral' CHAR(1) NOT NULL COMMENT 'CÓDIGO DA SITUAÇÃO CADASTRAL:\n01 – NULA\n2 – ATIVA\n3 – SUSPENSA\n4 – INAPTA\n08 – BAIXADA',
  'data_situacao_cadastral' DATE NULL COMMENT 'DATA DO EVENTO DA SITUAÇÃO CADASTRAL',
  'motivo_situacao_cadastral' INT NOT NULL COMMENT 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL',
  'nome_cidade_exterior' VARCHAR(45) NULL COMMENT 'NOME DA CIDADE NO EXTERIOR',
  'pais' INT NULL COMMENT 'CÓDIGO DO PAIS',
  'data_inicio_atividade' DATETIME NULL COMMENT 'DATA DE INÍCIO DA ATIVIDADE',
  'cnae_fiscal_principal' INT NOT NULL COMMENT 'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO',
  'cnae_fiscal_secundaria' VARCHAR(1000) NULL COMMENT 'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO',
  'tipo_logradouro' VARCHAR(500) NULL,
  'logradouro' VARCHAR(1000) NULL COMMENT 'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.',
  'numero' VARCHAR(45) NULL COMMENT 'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ ‘S/N’.',
  'complemento' VARCHAR(100) NULL COMMENT 'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO',
  'bairro' VARCHAR(45) NULL COMMENT 'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.',
  'cep' VARCHAR(45) NULL COMMENT 'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO',
  'uf' VARCHAR(45) NULL COMMENT 'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO',
  'municipio' INT NULL COMMENT 'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO',
  'ddd_1' VARCHAR(45) NULL,
  'telefone_1' VARCHAR(45) NULL,
  'ddd_2' VARCHAR(45) NULL,
  'telefone_2' VARCHAR(45) NULL,
  'ddd_fax' VARCHAR(45) NULL,
  'fax' VARCHAR(45) NULL,
  'correio_eletronico' VARCHAR(45) NULL,
  'situacao_especial' VARCHAR(45) NULL,
  'data_situacao_especial' DATE NULL,
  PRIMARY KEY ('cnpj_basico'),
  INDEX 'fk_estabelecimento_pais_idx' ('pais' ASC) VISIBLE,
  INDEX 'fk_estabelecimento_munic1_idx' ('municipio' ASC) VISIBLE,
  INDEX 'fk_estabelecimento_cnae1_idx' ('cnae_fiscal_principal' ASC) VISIBLE,
  INDEX 'fk_estabelecimento_moti1_idx' ('motivo_situacao_cadastral' ASC) VISIBLE,
  CONSTRAINT 'fk_estabelecimento_pais'
    FOREIGN KEY ('pais')
    REFERENCES 'dados_rfb'.'pais' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_estabelecimento_munic1'
    FOREIGN KEY ('municipio')
    REFERENCES 'dados_rfb'.'munic' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_estabelecimento_cnae1'
    FOREIGN KEY ('cnae_fiscal_principal')
    REFERENCES 'dados_rfb'.'cnae' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_estabelecimento_moti1'
    FOREIGN KEY ('motivo_situacao_cadastral')
    REFERENCES 'dados_rfb'.'moti' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'simples'
-- -----------------------------------------------------


CREATE TABLE IF NOT EXISTS 'simples' (
  'cnpj_basico' CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  'opcao_pelo_simples' CHAR(1) NULL COMMENT 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES.\n S - SIM\n N - NÃO\n EM BRANCO – OUTROS',
  'data_opcao_simples' DATE NULL COMMENT 'DATA DE OPÇÃO PELO SIMPLES',
  'data_exclusao_simples' DATE NULL COMMENT 'DATA DE EXCLUSÃO DO SIMPLES',
  'opcao_mei' CHAR(1) NULL COMMENT 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI\n S - SIM\n N - NÃO\n EM BRANCO - OUTROS',
  'data_opcao_mei' DATE NULL COMMENT 'DATA DE OPÇÃO PELO MEI',
  'data_exclusao_mei' DATE NULL COMMENT 'DATA DE EXCLUSÃO DO MEI',
  INDEX 'fk_simples_estabelecimento1_idx' ('cnpj_basico' ASC) VISIBLE,
  PRIMARY KEY ('cnpj_basico'),
  CONSTRAINT 'fk_simples_estabelecimento1'
    FOREIGN KEY ('cnpj_basico')
    REFERENCES 'dados_rfb'.'estabelecimento' ('cnpj_basico')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Table 'socios'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'socios' ;

CREATE TABLE IF NOT EXISTS 'socios' (
  'cnpj_basico' CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  'identificador_socio' INT NOT NULL COMMENT 'CÓDIGO DO IDENTIFICADOR DE SÓCIO\n1 – PESSOA JURÍDICA\n2 – PESSOA FÍSICA\n3 – ESTRANGEIRO',
  'nome_socio_razao_social' VARCHAR(1000) NULL COMMENT 'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL \nE/OU NOME EMPRESARIAL DA PESSOA JURÍDICA \nE/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO',
  'cpf_cnpj_socio' VARCHAR(45) NULL COMMENT 'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO).',
  'qualificacao_socio' INT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO',
  'data_entrada_sociedade' DATE NULL,
  'pais' INT NULL COMMENT 'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO',
  'representante_legal' VARCHAR(45) NULL COMMENT 'NÚMERO DO CPF DO REPRESENTANTE LEGAL',
  'nome_do_representante' VARCHAR(500) NULL,
  'qualificacao_representante_legal' INT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL',
  'faixa_etaria' INT NULL COMMENT 'CÓDIGO CORRESPONDENTE À FAIXA ETÁRIA DO SÓCIO.\nBaseada na data de nascimento do CPF de cada sócio, deverá ser criado o valor para o\ncampo \"Faixa Etária\" conforme a regra abaixo:\n- 1 para os intervalos entre 0 a 12 anos;\n- 2 para os intervalos entre 13 a 20 anos;\n- 3 para os intervalos entre 21 a 30 anos;\n- 4 para os intervalos entre 31 a 40 anos;\n- 5 para os intervalos entre 41 a 50 anos;\n- 6 para os intervalos entre 51 a 60 anos;\n- 7 para os intervalos entre 61 a 70 anos;\n- 8 para os intervalos entre 71 a 80 anos; - 9 para maiores de 80 anos.\n- 0 para não se aplica',
  INDEX 'fk_socios_quals1_idx' ('qualificacao_socio' ASC) VISIBLE,
  INDEX 'fk_socios_pais1_idx' ('pais' ASC) VISIBLE,
  INDEX 'fk_socios_quals2_idx' ('qualificacao_representante_legal' ASC) VISIBLE,
  PRIMARY KEY ('cnpj_basico'),
  CONSTRAINT 'fk_socios_quals1'
    FOREIGN KEY ('qualificacao_socio')
    REFERENCES 'dados_rfb'.'quals' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_socios_pais1'
    FOREIGN KEY ('pais')
    REFERENCES 'dados_rfb'.'pais' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_socios_quals2'
    FOREIGN KEY ('qualificacao_representante_legal')
    REFERENCES 'dados_rfb'.'quals' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'empresa'
-- -----------------------------------------------------


CREATE TABLE IF NOT EXISTS 'empresa' (
  'cnpj_basico' CHAR(8) NOT NULL,
  'razao_social' VARCHAR(1000) NULL COMMENT 'NOME EMPRESARIAL DA PESSOA JURÍDICA',
  'natureza_juridica' INT NULL COMMENT 'CÓDIGO DA NATUREZA JURÍDICA',
  'qualificacao_responsavel' INT NULL COMMENT 'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA',
  'capital_social' VARCHAR(45) NULL COMMENT 'CAPITAL SOCIAL DA EMPRESA',
  'porte_empresa' VARCHAR(45) NULL COMMENT 'CÓDIGO DO PORTE DA EMPRESA:\n1 – NÃO INFORMADO\n2 - MICRO EMPRESA\n03 - EMPRESA DE PEQUENO PORTE\n05 - DEMAIS',
  'ente_federativo_responsavel' VARCHAR(45) NULL COMMENT 'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO',
  INDEX 'fk_empresa_estabelecimento1_idx' ('cnpj_basico' ASC) VISIBLE,
  PRIMARY KEY ('cnpj_basico'),
  INDEX 'fk_empresa_natju1_idx' ('natureza_juridica' ASC) VISIBLE,
  INDEX 'fk_empresa_quals1_idx' ('qualificacao_responsavel' ASC) VISIBLE,
  CONSTRAINT 'fk_empresa_estabelecimento1'
    FOREIGN KEY ('cnpj_basico')
    REFERENCES 'dados_rfb'.'estabelecimento' ('cnpj_basico')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_empresa_natju1'
    FOREIGN KEY ('natureza_juridica')
    REFERENCES 'dados_rfb'.'natju' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'fk_empresa_quals1'
    FOREIGN KEY ('qualificacao_responsavel')
    REFERENCES 'dados_rfb'.'quals' ('codigo')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



DROP USER IF EXISTS 'dados_rfb'@'%';
CREATE USER 'dados_rfb'@'%' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON dados_rfb.* TO 'dados_rfb'@'%';