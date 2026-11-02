-- -----------------------------------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------

-- O SQLite só verifica chaves estrangeiras com PRAGMA foreign_keys = ON.
-- Deixe desligado durante a carga dos dados.

DROP TABLE IF EXISTS socios;
DROP TABLE IF EXISTS simples;
DROP TABLE IF EXISTS estabelecimento;
DROP TABLE IF EXISTS empresa;
DROP TABLE IF EXISTS quals;
DROP TABLE IF EXISTS pais;
DROP TABLE IF EXISTS natju;
DROP TABLE IF EXISTS munic;
DROP TABLE IF EXISTS moti;
DROP TABLE IF EXISTS cnae;

-- CNAEs: tabela de domínio de atividades econômicas
CREATE TABLE cnae (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_cnae PRIMARY KEY (codigo)
);

-- Motivos da situação cadastral: tabela de domínio (arquivo Motivos da RFB)
CREATE TABLE moti (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_moti PRIMARY KEY (codigo)
);

-- Municípios: tabela de domínio (código de município da RFB, não é o código IBGE)
CREATE TABLE munic (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_munic PRIMARY KEY (codigo)
);

-- Naturezas jurídicas: tabela de domínio
CREATE TABLE natju (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_natju PRIMARY KEY (codigo)
);

-- Países: tabela de domínio
CREATE TABLE pais (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_pais PRIMARY KEY (codigo)
);

-- Qualificações de sócios: tabela de domínio
CREATE TABLE quals (
  codigo INTEGER NOT NULL,
  descricao VARCHAR(1000) NOT NULL,
  CONSTRAINT pk_quals PRIMARY KEY (codigo)
);

-- Empresas: dados cadastrais da matriz, uma linha por CNPJ básico
CREATE TABLE empresa (
  cnpj_basico CHAR(8) NOT NULL,
  razao_social VARCHAR(255) NULL,
  natureza_juridica INTEGER NULL,
  qualificacao_responsavel INTEGER NULL,
  capital_social DECIMAL(18,2) NULL,
  porte_empresa CHAR(2) NULL,
  ente_federativo_responsavel VARCHAR(100) NULL,
  CONSTRAINT pk_empresa PRIMARY KEY (cnpj_basico),
  CONSTRAINT fk_emp_natju FOREIGN KEY (natureza_juridica) REFERENCES natju (codigo),
  CONSTRAINT fk_emp_quals FOREIGN KEY (qualificacao_responsavel) REFERENCES quals (codigo)
);
CREATE INDEX idx_emp_razao_social ON empresa (razao_social);
CREATE INDEX idx_emp_natju ON empresa (natureza_juridica);
CREATE INDEX idx_emp_quals ON empresa (qualificacao_responsavel);

-- Estabelecimentos: matriz e filiais, uma linha por CNPJ completo
CREATE TABLE estabelecimento (
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
  CONSTRAINT pk_estabelecimento PRIMARY KEY (cnpj_basico, cnpj_ordem, cnpj_dv),
  CONSTRAINT fk_estab_empresa FOREIGN KEY (cnpj_basico) REFERENCES empresa (cnpj_basico),
  CONSTRAINT fk_estab_pais FOREIGN KEY (pais) REFERENCES pais (codigo),
  CONSTRAINT fk_estab_munic FOREIGN KEY (municipio) REFERENCES munic (codigo),
  CONSTRAINT fk_estab_cnae FOREIGN KEY (cnae_fiscal_principal) REFERENCES cnae (codigo),
  CONSTRAINT fk_estab_moti FOREIGN KEY (motivo_situacao_cadastral) REFERENCES moti (codigo)
);
CREATE INDEX idx_estab_pais ON estabelecimento (pais);
CREATE INDEX idx_estab_munic ON estabelecimento (municipio);
CREATE INDEX idx_estab_cnae ON estabelecimento (cnae_fiscal_principal);
CREATE INDEX idx_estab_moti ON estabelecimento (motivo_situacao_cadastral);

-- Dados do Simples Nacional e do MEI, uma linha por CNPJ básico
CREATE TABLE simples (
  cnpj_basico CHAR(8) NOT NULL,
  opcao_pelo_simples CHAR(1) NULL,
  data_opcao_simples DATE NULL,
  data_exclusao_simples DATE NULL,
  opcao_mei CHAR(1) NULL,
  data_opcao_mei DATE NULL,
  data_exclusao_mei DATE NULL,
  CONSTRAINT pk_simples PRIMARY KEY (cnpj_basico),
  CONSTRAINT fk_simples_empresa FOREIGN KEY (cnpj_basico) REFERENCES empresa (cnpj_basico)
);

-- Sócios: várias linhas por CNPJ básico
CREATE TABLE socios (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
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
  CONSTRAINT fk_soc_empresa FOREIGN KEY (cnpj_basico) REFERENCES empresa (cnpj_basico),
  CONSTRAINT fk_soc_quals FOREIGN KEY (qualificacao_socio) REFERENCES quals (codigo),
  CONSTRAINT fk_soc_quals_rep FOREIGN KEY (qualificacao_representante_legal) REFERENCES quals (codigo),
  CONSTRAINT fk_soc_pais FOREIGN KEY (pais) REFERENCES pais (codigo)
);
CREATE INDEX idx_soc_cnpj_basico ON socios (cnpj_basico);
CREATE INDEX idx_soc_cpf_cnpj ON socios (cpf_cnpj_socio);
CREATE INDEX idx_soc_nome ON socios (nome_socio_razao_social);
CREATE INDEX idx_soc_quals ON socios (qualificacao_socio);
CREATE INDEX idx_soc_quals_rep ON socios (qualificacao_representante_legal);
CREATE INDEX idx_soc_pais ON socios (pais);

