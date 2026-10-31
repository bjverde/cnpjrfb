-- -----------------------------------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------

-- Requer SQL Server 2016 ou superior (DROP TABLE IF EXISTS).
-- Crie a base antes, se necessário, e selecione-a:
-- CREATE DATABASE dados_rfb;
-- GO
-- USE dados_rfb;
-- GO

DROP TABLE IF EXISTS dados_rfb.socios;
DROP TABLE IF EXISTS dados_rfb.simples;
DROP TABLE IF EXISTS dados_rfb.estabelecimento;
DROP TABLE IF EXISTS dados_rfb.empresa;
DROP TABLE IF EXISTS dados_rfb.quals;
DROP TABLE IF EXISTS dados_rfb.pais;
DROP TABLE IF EXISTS dados_rfb.natju;
DROP TABLE IF EXISTS dados_rfb.munic;
DROP TABLE IF EXISTS dados_rfb.moti;
DROP TABLE IF EXISTS dados_rfb.cnae;
GO

IF SCHEMA_ID(N'dados_rfb') IS NULL EXEC(N'CREATE SCHEMA dados_rfb');
GO

-- -----------------------------------------------------
-- Tabela cnae
-- -----------------------------------------------------
CREATE TABLE dados_rfb.cnae (
  codigo INT NOT NULL,
  descricao NVARCHAR(1000) NOT NULL,
  CONSTRAINT pk_cnae PRIMARY KEY CLUSTERED (codigo)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CNAEs: tabela de domínio de atividades econômicas', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'cnae';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA ATIVIDADE ECONÔMICA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'cnae', @level2type=N'COLUMN', @level2name=N'codigo';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DA ATIVIDADE ECONÔMICA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'cnae', @level2type=N'COLUMN', @level2name=N'descricao';
GO

-- -----------------------------------------------------
-- Tabela moti
-- -----------------------------------------------------
CREATE TABLE dados_rfb.moti (
  codigo INT NOT NULL,
  descricao NVARCHAR(1000) NOT NULL,
  CONSTRAINT pk_moti PRIMARY KEY CLUSTERED (codigo)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Motivos da situação cadastral: tabela de domínio (arquivo Motivos da RFB)', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'moti';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'moti', @level2type=N'COLUMN', @level2name=N'codigo';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DESCRIÇÃO DO MOTIVO DA SITUAÇÃO CADASTRAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'moti', @level2type=N'COLUMN', @level2name=N'descricao';
GO

-- -----------------------------------------------------
-- Tabela munic
-- -----------------------------------------------------
CREATE TABLE dados_rfb.munic (
  codigo INT NOT NULL,
  descricao NVARCHAR(1000) NOT NULL,
  CONSTRAINT pk_munic PRIMARY KEY CLUSTERED (codigo)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Municípios: tabela de domínio (código de município da RFB, não é o código IBGE)', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'munic';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO MUNICÍPIO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'munic', @level2type=N'COLUMN', @level2name=N'codigo';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO MUNICÍPIO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'munic', @level2type=N'COLUMN', @level2name=N'descricao';
GO

-- -----------------------------------------------------
-- Tabela natju
-- -----------------------------------------------------
CREATE TABLE dados_rfb.natju (
  codigo INT NOT NULL,
  descricao NVARCHAR(1000) NOT NULL,
  CONSTRAINT pk_natju PRIMARY KEY CLUSTERED (codigo)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Naturezas jurídicas: tabela de domínio', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'natju';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA NATUREZA JURÍDICA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'natju', @level2type=N'COLUMN', @level2name=N'codigo';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DA NATUREZA JURÍDICA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'natju', @level2type=N'COLUMN', @level2name=N'descricao';
GO

-- -----------------------------------------------------
-- Tabela pais
-- -----------------------------------------------------
CREATE TABLE dados_rfb.pais (
  codigo INT NOT NULL,
  descricao NVARCHAR(1000) NOT NULL,
  CONSTRAINT pk_pais PRIMARY KEY CLUSTERED (codigo)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Países: tabela de domínio', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'pais';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO PAÍS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'pais', @level2type=N'COLUMN', @level2name=N'codigo';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO PAÍS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'pais', @level2type=N'COLUMN', @level2name=N'descricao';
GO

-- -----------------------------------------------------
-- Tabela quals
-- -----------------------------------------------------
CREATE TABLE dados_rfb.quals (
  codigo INT NOT NULL,
  descricao NVARCHAR(1000) NOT NULL,
  CONSTRAINT pk_quals PRIMARY KEY CLUSTERED (codigo)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Qualificações de sócios: tabela de domínio', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'quals';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'quals', @level2type=N'COLUMN', @level2name=N'codigo';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DA QUALIFICAÇÃO DO SÓCIO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'quals', @level2type=N'COLUMN', @level2name=N'descricao';
GO

-- -----------------------------------------------------
-- Tabela empresa
-- -----------------------------------------------------
CREATE TABLE dados_rfb.empresa (
  cnpj_basico CHAR(8) NOT NULL,
  razao_social NVARCHAR(255) NULL,
  natureza_juridica INT NULL,
  qualificacao_responsavel INT NULL,
  capital_social DECIMAL(18,2) NULL,
  porte_empresa CHAR(2) NULL,
  ente_federativo_responsavel NVARCHAR(100) NULL,
  CONSTRAINT pk_empresa PRIMARY KEY CLUSTERED (cnpj_basico)
);
CREATE INDEX idx_emp_razao_social ON dados_rfb.empresa (razao_social);
CREATE INDEX idx_emp_natju ON dados_rfb.empresa (natureza_juridica);
CREATE INDEX idx_emp_quals ON dados_rfb.empresa (qualificacao_responsavel);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Empresas: dados cadastrais da matriz, uma linha por CNPJ básico', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'cnpj_basico';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME EMPRESARIAL DA PESSOA JURÍDICA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'razao_social';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA NATUREZA JURÍDICA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'natureza_juridica';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'qualificacao_responsavel';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CAPITAL SOCIAL DA EMPRESA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'capital_social';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO PORTE DA EMPRESA: 00 - NÃO INFORMADO; 01 - MICRO EMPRESA; 03 - EMPRESA DE PEQUENO PORTE; 05 - DEMAIS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'porte_empresa';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'empresa', @level2type=N'COLUMN', @level2name=N'ente_federativo_responsavel';
GO

-- -----------------------------------------------------
-- Tabela estabelecimento
-- -----------------------------------------------------
CREATE TABLE dados_rfb.estabelecimento (
  cnpj_basico CHAR(8) NOT NULL,
  cnpj_ordem CHAR(4) NOT NULL,
  cnpj_dv CHAR(2) NOT NULL,
  identificador_matriz_filial CHAR(1) NOT NULL,
  nome_fantasia NVARCHAR(255) NULL,
  situacao_cadastral CHAR(2) NOT NULL,
  data_situacao_cadastral DATE NULL,
  motivo_situacao_cadastral INT NOT NULL,
  nome_cidade_exterior NVARCHAR(100) NULL,
  pais INT NULL,
  data_inicio_atividade DATE NULL,
  cnae_fiscal_principal INT NOT NULL,
  cnae_fiscal_secundaria NVARCHAR(1000) NULL,
  tipo_logradouro NVARCHAR(50) NULL,
  logradouro NVARCHAR(255) NULL,
  numero NVARCHAR(20) NULL,
  complemento NVARCHAR(255) NULL,
  bairro NVARCHAR(100) NULL,
  cep CHAR(8) NULL,
  uf CHAR(2) NULL,
  municipio INT NULL,
  ddd_1 NVARCHAR(4) NULL,
  telefone_1 NVARCHAR(10) NULL,
  ddd_2 NVARCHAR(4) NULL,
  telefone_2 NVARCHAR(10) NULL,
  ddd_fax NVARCHAR(4) NULL,
  fax NVARCHAR(10) NULL,
  correio_eletronico NVARCHAR(255) NULL,
  situacao_especial NVARCHAR(100) NULL,
  data_situacao_especial DATE NULL,
  CONSTRAINT pk_estabelecimento PRIMARY KEY CLUSTERED (cnpj_basico, cnpj_ordem, cnpj_dv)
);
CREATE INDEX idx_estab_pais ON dados_rfb.estabelecimento (pais);
CREATE INDEX idx_estab_munic ON dados_rfb.estabelecimento (municipio);
CREATE INDEX idx_estab_cnae ON dados_rfb.estabelecimento (cnae_fiscal_principal);
CREATE INDEX idx_estab_moti ON dados_rfb.estabelecimento (motivo_situacao_cadastral);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Estabelecimentos: matriz e filiais, uma linha por CNPJ completo', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'cnpj_basico';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'cnpj_ordem';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'cnpj_dv';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL: 1 - MATRIZ; 2 - FILIAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'identificador_matriz_filial';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CORRESPONDE AO NOME FANTASIA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'nome_fantasia';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA SITUAÇÃO CADASTRAL: 01 - NULA; 02 - ATIVA; 03 - SUSPENSA; 04 - INAPTA; 08 - BAIXADA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'situacao_cadastral';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DO EVENTO DA SITUAÇÃO CADASTRAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'data_situacao_cadastral';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'motivo_situacao_cadastral';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DA CIDADE NO EXTERIOR', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'nome_cidade_exterior';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO PAIS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'pais';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE INÍCIO DA ATIVIDADE', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'data_inicio_atividade';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'cnae_fiscal_principal';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO, SEPARADOS POR VÍRGULA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'cnae_fiscal_secundaria';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DESCRIÇÃO DO TIPO DE LOGRADOURO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'tipo_logradouro';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'logradouro';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ S/N.', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'numero';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'complemento';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'bairro';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'cep';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'uf';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'municipio';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O DDD 1', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'ddd_1';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O NÚMERO DO TELEFONE 1', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'telefone_1';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O DDD 2', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'ddd_2';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O NÚMERO DO TELEFONE 2', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'telefone_2';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O DDD DO FAX', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'ddd_fax';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O NÚMERO DO FAX', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'fax';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CONTÉM O E-MAIL DO CONTRIBUINTE', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'correio_eletronico';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'SITUAÇÃO ESPECIAL DA EMPRESA', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'situacao_especial';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA EM QUE A EMPRESA ENTROU EM SITUAÇÃO ESPECIAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'estabelecimento', @level2type=N'COLUMN', @level2name=N'data_situacao_especial';
GO

-- -----------------------------------------------------
-- Tabela simples
-- -----------------------------------------------------
CREATE TABLE dados_rfb.simples (
  cnpj_basico CHAR(8) NOT NULL,
  opcao_pelo_simples CHAR(1) NULL,
  data_opcao_simples DATE NULL,
  data_exclusao_simples DATE NULL,
  opcao_mei CHAR(1) NULL,
  data_opcao_mei DATE NULL,
  data_exclusao_mei DATE NULL,
  CONSTRAINT pk_simples PRIMARY KEY CLUSTERED (cnpj_basico)
);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Dados do Simples Nacional e do MEI, uma linha por CNPJ básico', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'cnpj_basico';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES: S - SIM; N - NÃO; EM BRANCO - OUTROS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'opcao_pelo_simples';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE OPÇÃO PELO SIMPLES', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'data_opcao_simples';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE EXCLUSÃO DO SIMPLES', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'data_exclusao_simples';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI: S - SIM; N - NÃO; EM BRANCO - OUTROS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'opcao_mei';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE OPÇÃO PELO MEI', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'data_opcao_mei';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE EXCLUSÃO DO MEI', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'simples', @level2type=N'COLUMN', @level2name=N'data_exclusao_mei';
GO

-- -----------------------------------------------------
-- Tabela socios
-- -----------------------------------------------------
CREATE TABLE dados_rfb.socios (
  id BIGINT IDENTITY(1,1) NOT NULL,
  cnpj_basico CHAR(8) NOT NULL,
  identificador_socio INT NOT NULL,
  nome_socio_razao_social NVARCHAR(255) NULL,
  cpf_cnpj_socio NVARCHAR(14) NULL,
  qualificacao_socio INT NULL,
  data_entrada_sociedade DATE NULL,
  pais INT NULL,
  representante_legal NVARCHAR(11) NULL,
  nome_do_representante NVARCHAR(255) NULL,
  qualificacao_representante_legal INT NULL,
  faixa_etaria INT NULL,
  CONSTRAINT pk_socios PRIMARY KEY CLUSTERED (id)
);
CREATE INDEX idx_soc_cnpj_basico ON dados_rfb.socios (cnpj_basico);
CREATE INDEX idx_soc_cpf_cnpj ON dados_rfb.socios (cpf_cnpj_socio);
CREATE INDEX idx_soc_nome ON dados_rfb.socios (nome_socio_razao_social);
CREATE INDEX idx_soc_quals ON dados_rfb.socios (qualificacao_socio);
CREATE INDEX idx_soc_quals_rep ON dados_rfb.socios (qualificacao_representante_legal);
CREATE INDEX idx_soc_pais ON dados_rfb.socios (pais);
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'Sócios: várias linhas por CNPJ básico', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CHAVE SUBSTITUTA GERADA PELO BANCO (NÃO EXISTE NO ARQUIVO DA RFB)', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'id';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'cnpj_basico';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DO IDENTIFICADOR DE SÓCIO: 1 - PESSOA JURÍDICA; 2 - PESSOA FÍSICA; 3 - ESTRANGEIRO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'identificador_socio';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL E/OU NOME EMPRESARIAL DA PESSOA JURÍDICA E/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'nome_socio_razao_social';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO). CPF DESCARACTERIZADO: TRÊS PRIMEIROS DÍGITOS E OS DOIS DÍGITOS VERIFICADORES OCULTOS', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'cpf_cnpj_socio';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'qualificacao_socio';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'DATA DE ENTRADA NA SOCIEDADE', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'data_entrada_sociedade';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'pais';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NÚMERO DO CPF DO REPRESENTANTE LEGAL (DESCARACTERIZADO)', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'representante_legal';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'NOME DO REPRESENTANTE LEGAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'nome_do_representante';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'qualificacao_representante_legal';
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'CÓDIGO DA FAIXA ETÁRIA DO SÓCIO: 0 - NÃO SE APLICA; 1 - 0 A 12 ANOS; 2 - 13 A 20; 3 - 21 A 30; 4 - 31 A 40; 5 - 41 A 50; 6 - 51 A 60; 7 - 61 A 70; 8 - 71 A 80; 9 - MAIORES DE 80', @level0type=N'SCHEMA', @level0name=N'dados_rfb', @level1type=N'TABLE', @level1name=N'socios', @level2type=N'COLUMN', @level2name=N'faixa_etaria';
GO

