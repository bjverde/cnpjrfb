-- -----------------------------------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------

-- Requer Oracle 12c ou superior (coluna IDENTITY).
-- No Oracle o schema é o usuário. Crie o usuário (como DBA) e conecte com ele:
-- CREATE USER dados_rfb IDENTIFIED BY "troque_esta_senha" QUOTA UNLIMITED ON USERS;
-- GRANT CREATE SESSION, CREATE TABLE TO dados_rfb;
-- Atenção: no Oracle texto vazio ('') é NULL.

BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE socios CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE simples CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE estabelecimento CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE empresa CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE quals CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE pais CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE natju CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE munic CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE moti CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/
BEGIN
  EXECUTE IMMEDIATE 'DROP TABLE cnae CASCADE CONSTRAINTS PURGE';
EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;
END;
/

-- -----------------------------------------------------
-- Tabela cnae
-- -----------------------------------------------------
CREATE TABLE cnae (
  codigo NUMBER(10) NOT NULL,
  descricao VARCHAR2(1000 CHAR) NOT NULL,
  CONSTRAINT pk_cnae PRIMARY KEY (codigo)
);
COMMENT ON TABLE cnae IS 'CNAEs: tabela de domínio de atividades econômicas';
COMMENT ON COLUMN cnae.codigo IS 'CÓDIGO DA ATIVIDADE ECONÔMICA';
COMMENT ON COLUMN cnae.descricao IS 'NOME DA ATIVIDADE ECONÔMICA';

-- -----------------------------------------------------
-- Tabela moti
-- -----------------------------------------------------
CREATE TABLE moti (
  codigo NUMBER(10) NOT NULL,
  descricao VARCHAR2(1000 CHAR) NOT NULL,
  CONSTRAINT pk_moti PRIMARY KEY (codigo)
);
COMMENT ON TABLE moti IS 'Motivos da situação cadastral: tabela de domínio (arquivo Motivos da RFB)';
COMMENT ON COLUMN moti.codigo IS 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL';
COMMENT ON COLUMN moti.descricao IS 'DESCRIÇÃO DO MOTIVO DA SITUAÇÃO CADASTRAL';

-- -----------------------------------------------------
-- Tabela munic
-- -----------------------------------------------------
CREATE TABLE munic (
  codigo NUMBER(10) NOT NULL,
  descricao VARCHAR2(1000 CHAR) NOT NULL,
  CONSTRAINT pk_munic PRIMARY KEY (codigo)
);
COMMENT ON TABLE munic IS 'Municípios: tabela de domínio (código de município da RFB, não é o código IBGE)';
COMMENT ON COLUMN munic.codigo IS 'CÓDIGO DO MUNICÍPIO';
COMMENT ON COLUMN munic.descricao IS 'NOME DO MUNICÍPIO';

-- -----------------------------------------------------
-- Tabela natju
-- -----------------------------------------------------
CREATE TABLE natju (
  codigo NUMBER(10) NOT NULL,
  descricao VARCHAR2(1000 CHAR) NOT NULL,
  CONSTRAINT pk_natju PRIMARY KEY (codigo)
);
COMMENT ON TABLE natju IS 'Naturezas jurídicas: tabela de domínio';
COMMENT ON COLUMN natju.codigo IS 'CÓDIGO DA NATUREZA JURÍDICA';
COMMENT ON COLUMN natju.descricao IS 'NOME DA NATUREZA JURÍDICA';

-- -----------------------------------------------------
-- Tabela pais
-- -----------------------------------------------------
CREATE TABLE pais (
  codigo NUMBER(10) NOT NULL,
  descricao VARCHAR2(1000 CHAR) NOT NULL,
  CONSTRAINT pk_pais PRIMARY KEY (codigo)
);
COMMENT ON TABLE pais IS 'Países: tabela de domínio';
COMMENT ON COLUMN pais.codigo IS 'CÓDIGO DO PAÍS';
COMMENT ON COLUMN pais.descricao IS 'NOME DO PAÍS';

-- -----------------------------------------------------
-- Tabela quals
-- -----------------------------------------------------
CREATE TABLE quals (
  codigo NUMBER(10) NOT NULL,
  descricao VARCHAR2(1000 CHAR) NOT NULL,
  CONSTRAINT pk_quals PRIMARY KEY (codigo)
);
COMMENT ON TABLE quals IS 'Qualificações de sócios: tabela de domínio';
COMMENT ON COLUMN quals.codigo IS 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO';
COMMENT ON COLUMN quals.descricao IS 'NOME DA QUALIFICAÇÃO DO SÓCIO';

-- -----------------------------------------------------
-- Tabela empresa
-- -----------------------------------------------------
CREATE TABLE empresa (
  cnpj_basico CHAR(8 CHAR) NOT NULL,
  razao_social VARCHAR2(255 CHAR) NULL,
  natureza_juridica NUMBER(10) NULL,
  qualificacao_responsavel NUMBER(10) NULL,
  capital_social NUMBER(18,2) NULL,
  porte_empresa CHAR(2 CHAR) NULL,
  ente_federativo_responsavel VARCHAR2(100 CHAR) NULL,
  CONSTRAINT pk_empresa PRIMARY KEY (cnpj_basico)
);
CREATE INDEX idx_emp_razao_social ON empresa (razao_social);
CREATE INDEX idx_emp_natju ON empresa (natureza_juridica);
CREATE INDEX idx_emp_quals ON empresa (qualificacao_responsavel);
COMMENT ON TABLE empresa IS 'Empresas: dados cadastrais da matriz, uma linha por CNPJ básico';
COMMENT ON COLUMN empresa.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN empresa.razao_social IS 'NOME EMPRESARIAL DA PESSOA JURÍDICA';
COMMENT ON COLUMN empresa.natureza_juridica IS 'CÓDIGO DA NATUREZA JURÍDICA';
COMMENT ON COLUMN empresa.qualificacao_responsavel IS 'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA';
COMMENT ON COLUMN empresa.capital_social IS 'CAPITAL SOCIAL DA EMPRESA';
COMMENT ON COLUMN empresa.porte_empresa IS 'CÓDIGO DO PORTE DA EMPRESA: 00 - NÃO INFORMADO; 01 - MICRO EMPRESA; 03 - EMPRESA DE PEQUENO PORTE; 05 - DEMAIS';
COMMENT ON COLUMN empresa.ente_federativo_responsavel IS 'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO';

-- -----------------------------------------------------
-- Tabela estabelecimento
-- -----------------------------------------------------
CREATE TABLE estabelecimento (
  cnpj_basico CHAR(8 CHAR) NOT NULL,
  cnpj_ordem CHAR(4 CHAR) NOT NULL,
  cnpj_dv CHAR(2 CHAR) NOT NULL,
  identificador_matriz_filial CHAR(1 CHAR) NOT NULL,
  nome_fantasia VARCHAR2(255 CHAR) NULL,
  situacao_cadastral CHAR(2 CHAR) NOT NULL,
  data_situacao_cadastral DATE NULL,
  motivo_situacao_cadastral NUMBER(10) NOT NULL,
  nome_cidade_exterior VARCHAR2(100 CHAR) NULL,
  pais NUMBER(10) NULL,
  data_inicio_atividade DATE NULL,
  cnae_fiscal_principal NUMBER(10) NOT NULL,
  cnae_fiscal_secundaria VARCHAR2(1000 CHAR) NULL,
  tipo_logradouro VARCHAR2(50 CHAR) NULL,
  logradouro VARCHAR2(255 CHAR) NULL,
  numero VARCHAR2(20 CHAR) NULL,
  complemento VARCHAR2(255 CHAR) NULL,
  bairro VARCHAR2(100 CHAR) NULL,
  cep CHAR(8 CHAR) NULL,
  uf CHAR(2 CHAR) NULL,
  municipio NUMBER(10) NULL,
  ddd_1 VARCHAR2(4 CHAR) NULL,
  telefone_1 VARCHAR2(10 CHAR) NULL,
  ddd_2 VARCHAR2(4 CHAR) NULL,
  telefone_2 VARCHAR2(10 CHAR) NULL,
  ddd_fax VARCHAR2(4 CHAR) NULL,
  fax VARCHAR2(10 CHAR) NULL,
  correio_eletronico VARCHAR2(255 CHAR) NULL,
  situacao_especial VARCHAR2(100 CHAR) NULL,
  data_situacao_especial DATE NULL,
  CONSTRAINT pk_estabelecimento PRIMARY KEY (cnpj_basico, cnpj_ordem, cnpj_dv)
);
CREATE INDEX idx_estab_pais ON estabelecimento (pais);
CREATE INDEX idx_estab_munic ON estabelecimento (municipio);
CREATE INDEX idx_estab_cnae ON estabelecimento (cnae_fiscal_principal);
CREATE INDEX idx_estab_moti ON estabelecimento (motivo_situacao_cadastral);
COMMENT ON TABLE estabelecimento IS 'Estabelecimentos: matriz e filiais, uma linha por CNPJ completo';
COMMENT ON COLUMN estabelecimento.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN estabelecimento.cnpj_ordem IS 'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).';
COMMENT ON COLUMN estabelecimento.cnpj_dv IS 'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN estabelecimento.identificador_matriz_filial IS 'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL: 1 - MATRIZ; 2 - FILIAL';
COMMENT ON COLUMN estabelecimento.nome_fantasia IS 'CORRESPONDE AO NOME FANTASIA';
COMMENT ON COLUMN estabelecimento.situacao_cadastral IS 'CÓDIGO DA SITUAÇÃO CADASTRAL: 01 - NULA; 02 - ATIVA; 03 - SUSPENSA; 04 - INAPTA; 08 - BAIXADA';
COMMENT ON COLUMN estabelecimento.data_situacao_cadastral IS 'DATA DO EVENTO DA SITUAÇÃO CADASTRAL';
COMMENT ON COLUMN estabelecimento.motivo_situacao_cadastral IS 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL';
COMMENT ON COLUMN estabelecimento.nome_cidade_exterior IS 'NOME DA CIDADE NO EXTERIOR';
COMMENT ON COLUMN estabelecimento.pais IS 'CÓDIGO DO PAIS';
COMMENT ON COLUMN estabelecimento.data_inicio_atividade IS 'DATA DE INÍCIO DA ATIVIDADE';
COMMENT ON COLUMN estabelecimento.cnae_fiscal_principal IS 'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO';
COMMENT ON COLUMN estabelecimento.cnae_fiscal_secundaria IS 'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO, SEPARADOS POR VÍRGULA';
COMMENT ON COLUMN estabelecimento.tipo_logradouro IS 'DESCRIÇÃO DO TIPO DE LOGRADOURO';
COMMENT ON COLUMN estabelecimento.logradouro IS 'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.';
COMMENT ON COLUMN estabelecimento.numero IS 'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ S/N.';
COMMENT ON COLUMN estabelecimento.complemento IS 'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO';
COMMENT ON COLUMN estabelecimento.bairro IS 'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.';
COMMENT ON COLUMN estabelecimento.cep IS 'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO';
COMMENT ON COLUMN estabelecimento.uf IS 'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO';
COMMENT ON COLUMN estabelecimento.municipio IS 'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO';
COMMENT ON COLUMN estabelecimento.ddd_1 IS 'CONTÉM O DDD 1';
COMMENT ON COLUMN estabelecimento.telefone_1 IS 'CONTÉM O NÚMERO DO TELEFONE 1';
COMMENT ON COLUMN estabelecimento.ddd_2 IS 'CONTÉM O DDD 2';
COMMENT ON COLUMN estabelecimento.telefone_2 IS 'CONTÉM O NÚMERO DO TELEFONE 2';
COMMENT ON COLUMN estabelecimento.ddd_fax IS 'CONTÉM O DDD DO FAX';
COMMENT ON COLUMN estabelecimento.fax IS 'CONTÉM O NÚMERO DO FAX';
COMMENT ON COLUMN estabelecimento.correio_eletronico IS 'CONTÉM O E-MAIL DO CONTRIBUINTE';
COMMENT ON COLUMN estabelecimento.situacao_especial IS 'SITUAÇÃO ESPECIAL DA EMPRESA';
COMMENT ON COLUMN estabelecimento.data_situacao_especial IS 'DATA EM QUE A EMPRESA ENTROU EM SITUAÇÃO ESPECIAL';

-- -----------------------------------------------------
-- Tabela simples
-- -----------------------------------------------------
CREATE TABLE simples (
  cnpj_basico CHAR(8 CHAR) NOT NULL,
  opcao_pelo_simples CHAR(1 CHAR) NULL,
  data_opcao_simples DATE NULL,
  data_exclusao_simples DATE NULL,
  opcao_mei CHAR(1 CHAR) NULL,
  data_opcao_mei DATE NULL,
  data_exclusao_mei DATE NULL,
  CONSTRAINT pk_simples PRIMARY KEY (cnpj_basico)
);
COMMENT ON TABLE simples IS 'Dados do Simples Nacional e do MEI, uma linha por CNPJ básico';
COMMENT ON COLUMN simples.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN simples.opcao_pelo_simples IS 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES: S - SIM; N - NÃO; EM BRANCO - OUTROS';
COMMENT ON COLUMN simples.data_opcao_simples IS 'DATA DE OPÇÃO PELO SIMPLES';
COMMENT ON COLUMN simples.data_exclusao_simples IS 'DATA DE EXCLUSÃO DO SIMPLES';
COMMENT ON COLUMN simples.opcao_mei IS 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI: S - SIM; N - NÃO; EM BRANCO - OUTROS';
COMMENT ON COLUMN simples.data_opcao_mei IS 'DATA DE OPÇÃO PELO MEI';
COMMENT ON COLUMN simples.data_exclusao_mei IS 'DATA DE EXCLUSÃO DO MEI';

-- -----------------------------------------------------
-- Tabela socios
-- -----------------------------------------------------
CREATE TABLE socios (
  id NUMBER(19) GENERATED BY DEFAULT ON NULL AS IDENTITY NOT NULL,
  cnpj_basico CHAR(8 CHAR) NOT NULL,
  identificador_socio NUMBER(10) NOT NULL,
  nome_socio_razao_social VARCHAR2(255 CHAR) NULL,
  cpf_cnpj_socio VARCHAR2(14 CHAR) NULL,
  qualificacao_socio NUMBER(10) NULL,
  data_entrada_sociedade DATE NULL,
  pais NUMBER(10) NULL,
  representante_legal VARCHAR2(11 CHAR) NULL,
  nome_do_representante VARCHAR2(255 CHAR) NULL,
  qualificacao_representante_legal NUMBER(10) NULL,
  faixa_etaria NUMBER(10) NULL,
  CONSTRAINT pk_socios PRIMARY KEY (id)
);
CREATE INDEX idx_soc_cnpj_basico ON socios (cnpj_basico);
CREATE INDEX idx_soc_cpf_cnpj ON socios (cpf_cnpj_socio);
CREATE INDEX idx_soc_nome ON socios (nome_socio_razao_social);
CREATE INDEX idx_soc_quals ON socios (qualificacao_socio);
CREATE INDEX idx_soc_quals_rep ON socios (qualificacao_representante_legal);
CREATE INDEX idx_soc_pais ON socios (pais);
COMMENT ON TABLE socios IS 'Sócios: várias linhas por CNPJ básico';
COMMENT ON COLUMN socios.id IS 'CHAVE SUBSTITUTA GERADA PELO BANCO (NÃO EXISTE NO ARQUIVO DA RFB)';
COMMENT ON COLUMN socios.cnpj_basico IS 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).';
COMMENT ON COLUMN socios.identificador_socio IS 'CÓDIGO DO IDENTIFICADOR DE SÓCIO: 1 - PESSOA JURÍDICA; 2 - PESSOA FÍSICA; 3 - ESTRANGEIRO';
COMMENT ON COLUMN socios.nome_socio_razao_social IS 'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL E/OU NOME EMPRESARIAL DA PESSOA JURÍDICA E/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO';
COMMENT ON COLUMN socios.cpf_cnpj_socio IS 'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO). CPF DESCARACTERIZADO: TRÊS PRIMEIROS DÍGITOS E OS DOIS DÍGITOS VERIFICADORES OCULTOS';
COMMENT ON COLUMN socios.qualificacao_socio IS 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO';
COMMENT ON COLUMN socios.data_entrada_sociedade IS 'DATA DE ENTRADA NA SOCIEDADE';
COMMENT ON COLUMN socios.pais IS 'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO';
COMMENT ON COLUMN socios.representante_legal IS 'NÚMERO DO CPF DO REPRESENTANTE LEGAL (DESCARACTERIZADO)';
COMMENT ON COLUMN socios.nome_do_representante IS 'NOME DO REPRESENTANTE LEGAL';
COMMENT ON COLUMN socios.qualificacao_representante_legal IS 'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL';
COMMENT ON COLUMN socios.faixa_etaria IS 'CÓDIGO DA FAIXA ETÁRIA DO SÓCIO: 0 - NÃO SE APLICA; 1 - 0 A 12 ANOS; 2 - 13 A 20; 3 - 21 A 30; 4 - 31 A 40; 5 - 41 A 50; 6 - 51 A 60; 7 - 61 A 70; 8 - 71 A 80; 9 - MAIORES DE 80';

