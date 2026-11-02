-- -----------------------------------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------

-- Criar a base de dados (rode separado, conectado na base postgres):
-- CREATE DATABASE dados_rfb WITH ENCODING = 'UTF8';
-- COMMENT ON DATABASE dados_rfb IS 'Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil';
-- Depois conecte na base dados_rfb e rode o restante deste script.

CREATE SCHEMA IF NOT EXISTS dados_rfb;
COMMENT ON SCHEMA dados_rfb IS 'Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil';
SET search_path TO dados_rfb;

DROP TABLE IF EXISTS dados_rfb.socios CASCADE;
DROP TABLE IF EXISTS dados_rfb.simples CASCADE;
DROP TABLE IF EXISTS dados_rfb.estabelecimento CASCADE;
DROP TABLE IF EXISTS dados_rfb.empresa CASCADE;
DROP TABLE IF EXISTS dados_rfb.quals CASCADE;
DROP TABLE IF EXISTS dados_rfb.pais CASCADE;
DROP TABLE IF EXISTS dados_rfb.natju CASCADE;
DROP TABLE IF EXISTS dados_rfb.munic CASCADE;
DROP TABLE IF EXISTS dados_rfb.moti CASCADE;
DROP TABLE IF EXISTS dados_rfb.cnae CASCADE;

-- -----------------------------------------------------
-- Tabela cnae
-- -----------------------------------------------------
CREATE TABLE dados_rfb.cnae (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_cnae PRIMARY KEY (codigo)
);
COMMENT ON TABLE dados_rfb.cnae IS 'CNAEs: tabela de domínio de atividades econômicas';
COMMENT ON COLUMN dados_rfb.cnae.codigo IS 'CÓDIGO DA ATIVIDADE ECONÔMICA';
COMMENT ON COLUMN dados_rfb.cnae.descricao IS 'NOME DA ATIVIDADE ECONÔMICA';

-- -----------------------------------------------------
-- Tabela moti
-- -----------------------------------------------------
CREATE TABLE dados_rfb.moti (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_moti PRIMARY KEY (codigo)
);
COMMENT ON TABLE dados_rfb.moti IS 'Motivos da situação cadastral: tabela de domínio (arquivo Motivos da RFB)';
COMMENT ON COLUMN dados_rfb.moti.codigo IS 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL';
COMMENT ON COLUMN dados_rfb.moti.descricao IS 'DESCRIÇÃO DO MOTIVO DA SITUAÇÃO CADASTRAL';

-- -----------------------------------------------------
-- Tabela munic
-- -----------------------------------------------------
CREATE TABLE dados_rfb.munic (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_munic PRIMARY KEY (codigo)
);
COMMENT ON TABLE dados_rfb.munic IS 'Municípios: tabela de domínio (código de município da RFB, não é o código IBGE)';
COMMENT ON COLUMN dados_rfb.munic.codigo IS 'CÓDIGO DO MUNICÍPIO';
COMMENT ON COLUMN dados_rfb.munic.descricao IS 'NOME DO MUNICÍPIO';

-- -----------------------------------------------------
-- Tabela natju
-- -----------------------------------------------------
CREATE TABLE dados_rfb.natju (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_natju PRIMARY KEY (codigo)
);
COMMENT ON TABLE dados_rfb.natju IS 'Naturezas jurídicas: tabela de domínio';
COMMENT ON COLUMN dados_rfb.natju.codigo IS 'CÓDIGO DA NATUREZA JURÍDICA';
COMMENT ON COLUMN dados_rfb.natju.descricao IS 'NOME DA NATUREZA JURÍDICA';

-- -----------------------------------------------------
-- Tabela pais
-- -----------------------------------------------------
CREATE TABLE dados_rfb.pais (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_pais PRIMARY KEY (codigo)
);
COMMENT ON TABLE dados_rfb.pais IS 'Países: tabela de domínio';
COMMENT ON COLUMN dados_rfb.pais.codigo IS 'CÓDIGO DO PAÍS';
COMMENT ON COLUMN dados_rfb.pais.descricao IS 'NOME DO PAÍS';

-- -----------------------------------------------------
-- Tabela quals
-- -----------------------------------------------------
CREATE TABLE dados_rfb.quals (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_quals PRIMARY KEY (codigo)
);
COMMENT ON TABLE dados_rfb.quals IS 'Qualificações de sócios: tabela de domínio';
COMMENT ON COLUMN dados_rfb.quals.codigo IS 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO';
COMMENT ON COLUMN dados_rfb.quals.descricao IS 'NOME DA QUALIFICAÇÃO DO SÓCIO';

-- -----------------------------------------------------
-- Tabela empresa
-- -----------------------------------------------------
CREATE TABLE dados_rfb.empresa (
  cnpj_basico CHAR(8) NOT NULL,
  razao_social VARCHAR(255) NULL,
  natureza_juridica INTEGER NULL,
  qualificacao_responsavel INTEGER NULL,
  capital_social NUMERIC(18,2) NULL,
  porte_empresa CHAR(2) NULL,
  ente_federativo_responsavel VARCHAR(100) NULL,
  CONSTRAINT pk_empresa PRIMARY KEY (cnpj_basico)
);
CREATE INDEX idx_emp_razao_social ON dados_rfb.empresa (razao_social);
CREATE INDEX idx_emp_natju ON dados_rfb.empresa (natureza_juridica);
CREATE INDEX idx_emp_quals ON dados_rfb.empresa (qualificacao_responsavel);
COMMENT ON TABLE dados_rfb.empresa IS 'Empresas: dados cadastrais da matriz, uma linha por CNPJ básico';
COMMENT ON COLUMN dados_rfb.empresa.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN dados_rfb.empresa.razao_social IS 'NOME EMPRESARIAL DA PESSOA JURÍDICA';
COMMENT ON COLUMN dados_rfb.empresa.natureza_juridica IS 'CÓDIGO DA NATUREZA JURÍDICA';
COMMENT ON COLUMN dados_rfb.empresa.qualificacao_responsavel IS 'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA';
COMMENT ON COLUMN dados_rfb.empresa.capital_social IS 'CAPITAL SOCIAL DA EMPRESA';
COMMENT ON COLUMN dados_rfb.empresa.porte_empresa IS 'CÓDIGO DO PORTE DA EMPRESA: 00 - NÃO INFORMADO; 01 - MICRO EMPRESA; 03 - EMPRESA DE PEQUENO PORTE; 05 - DEMAIS';
COMMENT ON COLUMN dados_rfb.empresa.ente_federativo_responsavel IS 'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO';

-- -----------------------------------------------------
-- Tabela estabelecimento
-- -----------------------------------------------------
CREATE TABLE dados_rfb.estabelecimento (
  cnpj_basico CHAR(8) NOT NULL,
  cnpj_ordem CHAR(4) NOT NULL,
  cnpj_dv CHAR(2) NOT NULL,
  identificador_matriz_filial CHAR(1) NOT NULL,
  nome_fantasia VARCHAR(255) NULL,
  situacao_cadastral CHAR(2) NOT NULL,
  data_situacao_cadastral DATE NULL,
  motivo_situacao_cadastral INTEGER NOT NULL,
  nome_cidade_exterior VARCHAR(100) NULL,
  pais INTEGER NULL,
  data_inicio_atividade DATE NULL,
  cnae_fiscal_principal INTEGER NOT NULL,
  cnae_fiscal_secundaria VARCHAR(1000) NULL,
  tipo_logradouro VARCHAR(50) NULL,
  logradouro VARCHAR(255) NULL,
  numero VARCHAR(20) NULL,
  complemento VARCHAR(255) NULL,
  bairro VARCHAR(100) NULL,
  cep CHAR(8) NULL,
  uf CHAR(2) NULL,
  municipio INTEGER NULL,
  ddd_1 VARCHAR(4) NULL,
  telefone_1 VARCHAR(10) NULL,
  ddd_2 VARCHAR(4) NULL,
  telefone_2 VARCHAR(10) NULL,
  ddd_fax VARCHAR(4) NULL,
  fax VARCHAR(10) NULL,
  correio_eletronico VARCHAR(255) NULL,
  situacao_especial VARCHAR(100) NULL,
  data_situacao_especial DATE NULL,
  CONSTRAINT pk_estabelecimento PRIMARY KEY (cnpj_basico, cnpj_ordem, cnpj_dv)
);
CREATE INDEX idx_estab_pais ON dados_rfb.estabelecimento (pais);
CREATE INDEX idx_estab_munic ON dados_rfb.estabelecimento (municipio);
CREATE INDEX idx_estab_cnae ON dados_rfb.estabelecimento (cnae_fiscal_principal);
CREATE INDEX idx_estab_moti ON dados_rfb.estabelecimento (motivo_situacao_cadastral);
COMMENT ON TABLE dados_rfb.estabelecimento IS 'Estabelecimentos: matriz e filiais, uma linha por CNPJ completo';
COMMENT ON COLUMN dados_rfb.estabelecimento.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN dados_rfb.estabelecimento.cnpj_ordem IS 'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).';
COMMENT ON COLUMN dados_rfb.estabelecimento.cnpj_dv IS 'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN dados_rfb.estabelecimento.identificador_matriz_filial IS 'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL: 1 - MATRIZ; 2 - FILIAL';
COMMENT ON COLUMN dados_rfb.estabelecimento.nome_fantasia IS 'CORRESPONDE AO NOME FANTASIA';
COMMENT ON COLUMN dados_rfb.estabelecimento.situacao_cadastral IS 'CÓDIGO DA SITUAÇÃO CADASTRAL: 01 - NULA; 02 - ATIVA; 03 - SUSPENSA; 04 - INAPTA; 08 - BAIXADA';
COMMENT ON COLUMN dados_rfb.estabelecimento.data_situacao_cadastral IS 'DATA DO EVENTO DA SITUAÇÃO CADASTRAL';
COMMENT ON COLUMN dados_rfb.estabelecimento.motivo_situacao_cadastral IS 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL';
COMMENT ON COLUMN dados_rfb.estabelecimento.nome_cidade_exterior IS 'NOME DA CIDADE NO EXTERIOR';
COMMENT ON COLUMN dados_rfb.estabelecimento.pais IS 'CÓDIGO DO PAIS';
COMMENT ON COLUMN dados_rfb.estabelecimento.data_inicio_atividade IS 'DATA DE INÍCIO DA ATIVIDADE';
COMMENT ON COLUMN dados_rfb.estabelecimento.cnae_fiscal_principal IS 'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO';
COMMENT ON COLUMN dados_rfb.estabelecimento.cnae_fiscal_secundaria IS 'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO, SEPARADOS POR VÍRGULA';
COMMENT ON COLUMN dados_rfb.estabelecimento.tipo_logradouro IS 'DESCRIÇÃO DO TIPO DE LOGRADOURO';
COMMENT ON COLUMN dados_rfb.estabelecimento.logradouro IS 'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.';
COMMENT ON COLUMN dados_rfb.estabelecimento.numero IS 'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ S/N.';
COMMENT ON COLUMN dados_rfb.estabelecimento.complemento IS 'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO';
COMMENT ON COLUMN dados_rfb.estabelecimento.bairro IS 'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.';
COMMENT ON COLUMN dados_rfb.estabelecimento.cep IS 'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO';
COMMENT ON COLUMN dados_rfb.estabelecimento.uf IS 'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO';
COMMENT ON COLUMN dados_rfb.estabelecimento.municipio IS 'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO';
COMMENT ON COLUMN dados_rfb.estabelecimento.ddd_1 IS 'CONTÉM O DDD 1';
COMMENT ON COLUMN dados_rfb.estabelecimento.telefone_1 IS 'CONTÉM O NÚMERO DO TELEFONE 1';
COMMENT ON COLUMN dados_rfb.estabelecimento.ddd_2 IS 'CONTÉM O DDD 2';
COMMENT ON COLUMN dados_rfb.estabelecimento.telefone_2 IS 'CONTÉM O NÚMERO DO TELEFONE 2';
COMMENT ON COLUMN dados_rfb.estabelecimento.ddd_fax IS 'CONTÉM O DDD DO FAX';
COMMENT ON COLUMN dados_rfb.estabelecimento.fax IS 'CONTÉM O NÚMERO DO FAX';
COMMENT ON COLUMN dados_rfb.estabelecimento.correio_eletronico IS 'CONTÉM O E-MAIL DO CONTRIBUINTE';
COMMENT ON COLUMN dados_rfb.estabelecimento.situacao_especial IS 'SITUAÇÃO ESPECIAL DA EMPRESA';
COMMENT ON COLUMN dados_rfb.estabelecimento.data_situacao_especial IS 'DATA EM QUE A EMPRESA ENTROU EM SITUAÇÃO ESPECIAL';

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
  CONSTRAINT pk_simples PRIMARY KEY (cnpj_basico)
);
COMMENT ON TABLE dados_rfb.simples IS 'Dados do Simples Nacional e do MEI, uma linha por CNPJ básico';
COMMENT ON COLUMN dados_rfb.simples.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN dados_rfb.simples.opcao_pelo_simples IS 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES: S - SIM; N - NÃO; EM BRANCO - OUTROS';
COMMENT ON COLUMN dados_rfb.simples.data_opcao_simples IS 'DATA DE OPÇÃO PELO SIMPLES';
COMMENT ON COLUMN dados_rfb.simples.data_exclusao_simples IS 'DATA DE EXCLUSÃO DO SIMPLES';
COMMENT ON COLUMN dados_rfb.simples.opcao_mei IS 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI: S - SIM; N - NÃO; EM BRANCO - OUTROS';
COMMENT ON COLUMN dados_rfb.simples.data_opcao_mei IS 'DATA DE OPÇÃO PELO MEI';
COMMENT ON COLUMN dados_rfb.simples.data_exclusao_mei IS 'DATA DE EXCLUSÃO DO MEI';

-- -----------------------------------------------------
-- Tabela socios
-- -----------------------------------------------------
CREATE TABLE dados_rfb.socios (
  id BIGINT GENERATED BY DEFAULT AS IDENTITY NOT NULL,
  cnpj_basico CHAR(8) NOT NULL,
  identificador_socio INTEGER NOT NULL,
  nome_socio_razao_social VARCHAR(255) NULL,
  cpf_cnpj_socio VARCHAR(14) NULL,
  qualificacao_socio INTEGER NULL,
  data_entrada_sociedade DATE NULL,
  pais INTEGER NULL,
  representante_legal VARCHAR(11) NULL,
  nome_do_representante VARCHAR(255) NULL,
  qualificacao_representante_legal INTEGER NULL,
  faixa_etaria INTEGER NULL,
  CONSTRAINT pk_socios PRIMARY KEY (id)
);
CREATE INDEX idx_soc_cnpj_basico ON dados_rfb.socios (cnpj_basico);
CREATE INDEX idx_soc_cpf_cnpj ON dados_rfb.socios (cpf_cnpj_socio);
CREATE INDEX idx_soc_nome ON dados_rfb.socios (nome_socio_razao_social);
CREATE INDEX idx_soc_quals ON dados_rfb.socios (qualificacao_socio);
CREATE INDEX idx_soc_quals_rep ON dados_rfb.socios (qualificacao_representante_legal);
CREATE INDEX idx_soc_pais ON dados_rfb.socios (pais);
COMMENT ON TABLE dados_rfb.socios IS 'Sócios: várias linhas por CNPJ básico';
COMMENT ON COLUMN dados_rfb.socios.id IS 'CHAVE SUBSTITUTA GERADA PELO BANCO (NÃO EXISTE NO ARQUIVO DA RFB)';
COMMENT ON COLUMN dados_rfb.socios.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN dados_rfb.socios.identificador_socio IS 'CÓDIGO DO IDENTIFICADOR DE SÓCIO: 1 - PESSOA JURÍDICA; 2 - PESSOA FÍSICA; 3 - ESTRANGEIRO';
COMMENT ON COLUMN dados_rfb.socios.nome_socio_razao_social IS 'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL E/OU NOME EMPRESARIAL DA PESSOA JURÍDICA E/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO';
COMMENT ON COLUMN dados_rfb.socios.cpf_cnpj_socio IS 'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO). CPF DESCARACTERIZADO: TRÊS PRIMEIROS DÍGITOS E OS DOIS DÍGITOS VERIFICADORES OCULTOS';
COMMENT ON COLUMN dados_rfb.socios.qualificacao_socio IS 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO';
COMMENT ON COLUMN dados_rfb.socios.data_entrada_sociedade IS 'DATA DE ENTRADA NA SOCIEDADE';
COMMENT ON COLUMN dados_rfb.socios.pais IS 'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO';
COMMENT ON COLUMN dados_rfb.socios.representante_legal IS 'NÚMERO DO CPF DO REPRESENTANTE LEGAL (DESCARACTERIZADO)';
COMMENT ON COLUMN dados_rfb.socios.nome_do_representante IS 'NOME DO REPRESENTANTE LEGAL';
COMMENT ON COLUMN dados_rfb.socios.qualificacao_representante_legal IS 'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL';
COMMENT ON COLUMN dados_rfb.socios.faixa_etaria IS 'CÓDIGO DA FAIXA ETÁRIA DO SÓCIO: 0 - NÃO SE APLICA; 1 - 0 A 12 ANOS; 2 - 13 A 20; 3 - 21 A 30; 4 - 31 A 40; 5 - 41 A 50; 6 - 51 A 60; 7 - 61 A 70; 8 - 71 A 80; 9 - MAIORES DE 80';

