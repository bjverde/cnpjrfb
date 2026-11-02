-- -----------------------------------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

DROP SCHEMA IF EXISTS `dados_rfb`;
CREATE SCHEMA IF NOT EXISTS `dados_rfb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dados_rfb`;

-- -----------------------------------------------------
-- Tabela `cnae`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cnae`;

CREATE TABLE IF NOT EXISTS `cnae` (
  `codigo` INT NOT NULL COMMENT 'CÓDIGO DA ATIVIDADE ECONÔMICA',
  `descricao` VARCHAR(1000) NOT NULL COMMENT 'NOME DA ATIVIDADE ECONÔMICA',
  PRIMARY KEY (`codigo`)
) ENGINE = InnoDB
COMMENT = 'CNAEs: tabela de domínio de atividades econômicas';

-- -----------------------------------------------------
-- Tabela `moti`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moti`;

CREATE TABLE IF NOT EXISTS `moti` (
  `codigo` INT NOT NULL COMMENT 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL',
  `descricao` VARCHAR(1000) NOT NULL COMMENT 'DESCRIÇÃO DO MOTIVO DA SITUAÇÃO CADASTRAL',
  PRIMARY KEY (`codigo`)
) ENGINE = InnoDB
COMMENT = 'Motivos da situação cadastral: tabela de domínio (arquivo Motivos da RFB)';

-- -----------------------------------------------------
-- Tabela `munic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `munic`;

CREATE TABLE IF NOT EXISTS `munic` (
  `codigo` INT NOT NULL COMMENT 'CÓDIGO DO MUNICÍPIO',
  `descricao` VARCHAR(1000) NOT NULL COMMENT 'NOME DO MUNICÍPIO',
  PRIMARY KEY (`codigo`)
) ENGINE = InnoDB
COMMENT = 'Municípios: tabela de domínio (código de município da RFB, não é o código IBGE)';

-- -----------------------------------------------------
-- Tabela `natju`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `natju`;

CREATE TABLE IF NOT EXISTS `natju` (
  `codigo` INT NOT NULL COMMENT 'CÓDIGO DA NATUREZA JURÍDICA',
  `descricao` VARCHAR(1000) NOT NULL COMMENT 'NOME DA NATUREZA JURÍDICA',
  PRIMARY KEY (`codigo`)
) ENGINE = InnoDB
COMMENT = 'Naturezas jurídicas: tabela de domínio';

-- -----------------------------------------------------
-- Tabela `pais`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pais`;

CREATE TABLE IF NOT EXISTS `pais` (
  `codigo` INT NOT NULL COMMENT 'CÓDIGO DO PAÍS',
  `descricao` VARCHAR(1000) NOT NULL COMMENT 'NOME DO PAÍS',
  PRIMARY KEY (`codigo`)
) ENGINE = InnoDB
COMMENT = 'Países: tabela de domínio';

-- -----------------------------------------------------
-- Tabela `quals`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `quals`;

CREATE TABLE IF NOT EXISTS `quals` (
  `codigo` INT NOT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO',
  `descricao` VARCHAR(1000) NOT NULL COMMENT 'NOME DA QUALIFICAÇÃO DO SÓCIO',
  PRIMARY KEY (`codigo`)
) ENGINE = InnoDB
COMMENT = 'Qualificações de sócios: tabela de domínio';

-- -----------------------------------------------------
-- Tabela `empresa`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `empresa`;

CREATE TABLE IF NOT EXISTS `empresa` (
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `razao_social` VARCHAR(255) NULL COMMENT 'NOME EMPRESARIAL DA PESSOA JURÍDICA',
  `natureza_juridica` INT NULL COMMENT 'CÓDIGO DA NATUREZA JURÍDICA',
  `qualificacao_responsavel` INT NULL COMMENT 'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA',
  `capital_social` DECIMAL(18,2) NULL COMMENT 'CAPITAL SOCIAL DA EMPRESA',
  `porte_empresa` CHAR(2) NULL COMMENT 'CÓDIGO DO PORTE DA EMPRESA: 00 - NÃO INFORMADO; 01 - MICRO EMPRESA; 03 - EMPRESA DE PEQUENO PORTE; 05 - DEMAIS',
  `ente_federativo_responsavel` VARCHAR(100) NULL COMMENT 'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO',
  PRIMARY KEY (`cnpj_basico`),
  INDEX `idx_emp_razao_social` (`razao_social`),
  INDEX `idx_emp_natju` (`natureza_juridica`),
  INDEX `idx_emp_quals` (`qualificacao_responsavel`)
) ENGINE = InnoDB
COMMENT = 'Empresas: dados cadastrais da matriz, uma linha por CNPJ básico';

-- -----------------------------------------------------
-- Tabela `estabelecimento`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `estabelecimento`;

CREATE TABLE IF NOT EXISTS `estabelecimento` (
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `cnpj_ordem` CHAR(4) NOT NULL COMMENT 'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).',
  `cnpj_dv` CHAR(2) NOT NULL COMMENT 'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).',
  `identificador_matriz_filial` CHAR(1) NOT NULL COMMENT 'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL: 1 - MATRIZ; 2 - FILIAL',
  `nome_fantasia` VARCHAR(255) NULL COMMENT 'CORRESPONDE AO NOME FANTASIA',
  `situacao_cadastral` CHAR(2) NOT NULL COMMENT 'CÓDIGO DA SITUAÇÃO CADASTRAL: 01 - NULA; 02 - ATIVA; 03 - SUSPENSA; 04 - INAPTA; 08 - BAIXADA',
  `data_situacao_cadastral` DATE NULL COMMENT 'DATA DO EVENTO DA SITUAÇÃO CADASTRAL',
  `motivo_situacao_cadastral` INT NOT NULL COMMENT 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL',
  `nome_cidade_exterior` VARCHAR(100) NULL COMMENT 'NOME DA CIDADE NO EXTERIOR',
  `pais` INT NULL COMMENT 'CÓDIGO DO PAIS',
  `data_inicio_atividade` DATE NULL COMMENT 'DATA DE INÍCIO DA ATIVIDADE',
  `cnae_fiscal_principal` INT NOT NULL COMMENT 'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO',
  `cnae_fiscal_secundaria` VARCHAR(1000) NULL COMMENT 'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO, SEPARADOS POR VÍRGULA',
  `tipo_logradouro` VARCHAR(50) NULL COMMENT 'DESCRIÇÃO DO TIPO DE LOGRADOURO',
  `logradouro` VARCHAR(255) NULL COMMENT 'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.',
  `numero` VARCHAR(20) NULL COMMENT 'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ S/N.',
  `complemento` VARCHAR(255) NULL COMMENT 'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO',
  `bairro` VARCHAR(100) NULL COMMENT 'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.',
  `cep` CHAR(8) NULL COMMENT 'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO',
  `uf` CHAR(2) NULL COMMENT 'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO',
  `municipio` INT NULL COMMENT 'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO',
  `ddd_1` VARCHAR(4) NULL COMMENT 'CONTÉM O DDD 1',
  `telefone_1` VARCHAR(10) NULL COMMENT 'CONTÉM O NÚMERO DO TELEFONE 1',
  `ddd_2` VARCHAR(4) NULL COMMENT 'CONTÉM O DDD 2',
  `telefone_2` VARCHAR(10) NULL COMMENT 'CONTÉM O NÚMERO DO TELEFONE 2',
  `ddd_fax` VARCHAR(4) NULL COMMENT 'CONTÉM O DDD DO FAX',
  `fax` VARCHAR(10) NULL COMMENT 'CONTÉM O NÚMERO DO FAX',
  `correio_eletronico` VARCHAR(255) NULL COMMENT 'CONTÉM O E-MAIL DO CONTRIBUINTE',
  `situacao_especial` VARCHAR(100) NULL COMMENT 'SITUAÇÃO ESPECIAL DA EMPRESA',
  `data_situacao_especial` DATE NULL COMMENT 'DATA EM QUE A EMPRESA ENTROU EM SITUAÇÃO ESPECIAL',
  PRIMARY KEY (`cnpj_basico`, `cnpj_ordem`, `cnpj_dv`),
  INDEX `idx_estab_pais` (`pais`),
  INDEX `idx_estab_munic` (`municipio`),
  INDEX `idx_estab_cnae` (`cnae_fiscal_principal`),
  INDEX `idx_estab_moti` (`motivo_situacao_cadastral`)
) ENGINE = InnoDB
COMMENT = 'Estabelecimentos: matriz e filiais, uma linha por CNPJ completo';

-- -----------------------------------------------------
-- Tabela `simples`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `simples`;

CREATE TABLE IF NOT EXISTS `simples` (
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `opcao_pelo_simples` CHAR(1) NULL COMMENT 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES: S - SIM; N - NÃO; EM BRANCO - OUTROS',
  `data_opcao_simples` DATE NULL COMMENT 'DATA DE OPÇÃO PELO SIMPLES',
  `data_exclusao_simples` DATE NULL COMMENT 'DATA DE EXCLUSÃO DO SIMPLES',
  `opcao_mei` CHAR(1) NULL COMMENT 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI: S - SIM; N - NÃO; EM BRANCO - OUTROS',
  `data_opcao_mei` DATE NULL COMMENT 'DATA DE OPÇÃO PELO MEI',
  `data_exclusao_mei` DATE NULL COMMENT 'DATA DE EXCLUSÃO DO MEI',
  PRIMARY KEY (`cnpj_basico`)
) ENGINE = InnoDB
COMMENT = 'Dados do Simples Nacional e do MEI, uma linha por CNPJ básico';

-- -----------------------------------------------------
-- Tabela `socios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `socios`;

CREATE TABLE IF NOT EXISTS `socios` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'CHAVE SUBSTITUTA GERADA PELO BANCO (NÃO EXISTE NO ARQUIVO DA RFB)',
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `identificador_socio` INT NOT NULL COMMENT 'CÓDIGO DO IDENTIFICADOR DE SÓCIO: 1 - PESSOA JURÍDICA; 2 - PESSOA FÍSICA; 3 - ESTRANGEIRO',
  `nome_socio_razao_social` VARCHAR(255) NULL COMMENT 'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL E/OU NOME EMPRESARIAL DA PESSOA JURÍDICA E/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO',
  `cpf_cnpj_socio` VARCHAR(14) NULL COMMENT 'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO). CPF DESCARACTERIZADO: TRÊS PRIMEIROS DÍGITOS E OS DOIS DÍGITOS VERIFICADORES OCULTOS',
  `qualificacao_socio` INT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO',
  `data_entrada_sociedade` DATE NULL COMMENT 'DATA DE ENTRADA NA SOCIEDADE',
  `pais` INT NULL COMMENT 'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO',
  `representante_legal` VARCHAR(11) NULL COMMENT 'NÚMERO DO CPF DO REPRESENTANTE LEGAL (DESCARACTERIZADO)',
  `nome_do_representante` VARCHAR(255) NULL COMMENT 'NOME DO REPRESENTANTE LEGAL',
  `qualificacao_representante_legal` INT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL',
  `faixa_etaria` INT NULL COMMENT 'CÓDIGO DA FAIXA ETÁRIA DO SÓCIO: 0 - NÃO SE APLICA; 1 - 0 A 12 ANOS; 2 - 13 A 20; 3 - 21 A 30; 4 - 31 A 40; 5 - 41 A 50; 6 - 51 A 60; 7 - 61 A 70; 8 - 71 A 80; 9 - MAIORES DE 80',
  PRIMARY KEY (`id`),
  INDEX `idx_soc_cnpj_basico` (`cnpj_basico`),
  INDEX `idx_soc_cpf_cnpj` (`cpf_cnpj_socio`),
  INDEX `idx_soc_nome` (`nome_socio_razao_social`),
  INDEX `idx_soc_quals` (`qualificacao_socio`),
  INDEX `idx_soc_quals_rep` (`qualificacao_representante_legal`),
  INDEX `idx_soc_pais` (`pais`)
) ENGINE = InnoDB
COMMENT = 'Sócios: várias linhas por CNPJ básico';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- Usuário da aplicação. Troque a senha antes de usar.
-- DROP USER IF EXISTS 'dados_rfb'@'%';
-- CREATE USER 'dados_rfb'@'%' IDENTIFIED BY 'troque_esta_senha';
-- GRANT ALL PRIVILEGES ON dados_rfb.* TO 'dados_rfb'@'%';
