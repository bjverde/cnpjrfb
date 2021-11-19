-- Criar a base de dados "Dados_RFB"
CREATE DATABASE "Dados_RFB"
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    CONNECTION LIMIT = -1;

COMMENT ON DATABASE "Dados_RFB"
    IS 'Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil';


DROP TABLE IF EXISTS cnae ;
CREATE TABLE cnae (
	codigo INT PRIMARY KEY,
	descricao VARCHAR (1000)
);
ALTER TABLE cnae OWNER TO postgres;

DROP TABLE IF EXISTS natju ;
CREATE TABLE natju (
	codigo INT PRIMARY KEY,
	descricao VARCHAR (1000)
);
ALTER TABLE natju OWNER TO postgres;


DROP TABLE IF EXISTS quals ;
CREATE TABLE quals (
	codigo INT PRIMARY KEY,
	descricao VARCHAR (1000)
);
ALTER TABLE quals OWNER TO postgres;


DROP TABLE IF EXISTS pais ;
CREATE TABLE pais (
	codigo INT PRIMARY KEY,
	descricao VARCHAR (1000)
);
ALTER TABLE pais OWNER TO postgres;

DROP TABLE IF EXISTS moti ;
CREATE TABLE moti (
	codigo INT PRIMARY KEY,
	descricao VARCHAR (1000)
);
ALTER TABLE moti OWNER TO postgres;

DROP TABLE IF EXISTS munic ;
CREATE TABLE munic (
	codigo INT PRIMARY KEY,
	descricao VARCHAR (1000)
);
ALTER TABLE munic OWNER TO postgres;

DROP TABLE IF EXISTS estabelecimento ;
CREATE TABLE IF NOT EXISTS estabelecimento (
   cnpj_basico CHAR(8) NOT NULL
  ,cnpj_ordem CHAR(4) NOT NULL
  ,cnpj_dv CHAR(2) NOT NULL
  ,identificador_matriz_filial CHAR(1) NOT NULL
  ,nome_fantasia VARCHAR(1000) NULL
  ,situacao_cadastral CHAR(1) NOT NULL
  ,data_situacao_cadastral DATE NULL
  ,motivo_situacao_cadastral INT NOT NULL
  ,nome_cidade_exterior VARCHAR(45) NULL
  ,pais INT NULL
  ,data_inicio_atividade DATE NULL
  ,cnae_fiscal_principal INT NOT NULL
  ,cnae_fiscal_secundaria VARCHAR(1000) NULL
  ,tipo_logradouro VARCHAR(500) NULL
  ,logradouro VARCHAR(1000) NULL
  ,numero VARCHAR(45) NULL
  ,complemento VARCHAR(100) NULL
  ,bairro VARCHAR(45) NULL
  ,cep VARCHAR(45) NULL
  ,uf VARCHAR(45) NULL
  ,municipio INT NULL
  ,ddd_1 VARCHAR(45) NULL
  ,telefone_1 VARCHAR(45) NULL
  ,ddd_2 VARCHAR(45) NULL
  ,telefone_2 VARCHAR(45) NULL
  ,ddd_fax VARCHAR(45) NULL
  ,fax VARCHAR(45) NULL
  ,correio_eletronico VARCHAR(45) NULL
  ,situacao_especial VARCHAR(45) NULL
  ,data_situacao_especial DATE NULL
);
ALTER TABLE estabelecimento OWNER TO postgres;


DROP TABLE IF EXISTS simples;
CREATE TABLE IF NOT EXISTS simples (
   cnpj_basico CHAR(8)
  ,opcao_pelo_simples CHAR(1) NULL
  ,data_opcao_simples DATE NULL
  ,data_exclusao_simples DATE NULL
  ,opcao_mei CHAR(1) NULL
  ,data_opcao_mei DATE NULL
  ,data_exclusao_mei DATE NULL
  );
ALTER TABLE simples OWNER TO postgres;


DROP TABLE IF EXISTS socios;
CREATE TABLE IF NOT EXISTS socios (
   cnpj_basico CHAR(8) NOT NULL
  ,identificador_socio INT NOT NULL
  ,nome_socio_razao_social VARCHAR(1000) NULL
  ,cpf_cnpj_socio VARCHAR(45) NULL
  ,qualificacao_socio INT NULL
  ,data_entrada_sociedade DATE NULL
  ,pais INT NULL
  ,representante_legal VARCHAR(45) NULL
  ,nome_do_representante VARCHAR(500) NULL
  ,qualificacao_representante_legal INT NULL
  ,faixa_etaria INT NULL
  );
ALTER TABLE socios OWNER TO postgres;



DROP TABLE IF EXISTS empresa;
CREATE TABLE IF NOT EXISTS empresa (
    cnpj_basico char(8),
    razao_social text COLLATE pg_catalog."default",
    natureza_juridica text COLLATE pg_catalog."default",
    qualificacao_responsavel text COLLATE pg_catalog."default",
    capital_social double precision,
    porte_empresa text COLLATE pg_catalog."default",
    ente_federativo_responsavel text COLLATE pg_catalog."default"
);
ALTER TABLE empresa OWNER TO postgres;

-- Diretório físico do banco de dados:
--SHOW data_directory;