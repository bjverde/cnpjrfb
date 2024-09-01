-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema dados_rfb
-- -----------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
DROP SCHEMA IF EXISTS `dados_rfb` ;

-- -----------------------------------------------------
-- Schema dados_rfb
--
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `dados_rfb` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;
USE `dados_rfb` ;

-- -----------------------------------------------------
-- Table `cnae`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cnae` ;

CREATE TABLE IF NOT EXISTS `cnae` (
  `codigo` INT NOT NULL,
  `descricao` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `natju`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `natju` ;

CREATE TABLE IF NOT EXISTS `natju` (
  `codigo` INT NOT NULL,
  `descricao` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Table `quals`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `quals` ;

CREATE TABLE IF NOT EXISTS `quals` (
  `codigo` INT NOT NULL,
  `descricao` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pais`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pais` ;

CREATE TABLE IF NOT EXISTS `pais` (
  `codigo` INT NOT NULL,
  `descricao` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moti`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moti` ;

CREATE TABLE IF NOT EXISTS `moti` (
  `codigo` INT NOT NULL,
  `descricao` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `munic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `munic` ;

CREATE TABLE IF NOT EXISTS `munic` (
  `codigo` INT NOT NULL,
  `descricao` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`codigo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `estabelecimento`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `estabelecimento` ;

CREATE TABLE IF NOT EXISTS `estabelecimento` (
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `cnpj_ordem` CHAR(4) NOT NULL COMMENT 'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).',
  `cnpj_dv` CHAR(2) NOT NULL COMMENT 'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).',
  `identificador_matriz_filial` CHAR(1) NOT NULL COMMENT 'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL:\n1 – MATRIZ\n2 – FILIAL',
  `nome_fantasia` VARCHAR(1000) NULL COMMENT 'CORRESPONDE AO NOME FANTASIA',
  `situacao_cadastral` CHAR(1) NOT NULL COMMENT 'CÓDIGO DA SITUAÇÃO CADASTRAL:\n01 – NULA\n2 – ATIVA\n3 – SUSPENSA\n4 – INAPTA\n08 – BAIXADA',
  `data_situacao_cadastral` DATE NULL COMMENT 'DATA DO EVENTO DA SITUAÇÃO CADASTRAL',
  `motivo_situacao_cadastral` INT NOT NULL COMMENT 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL',
  `nome_cidade_exterior` VARCHAR(45) NULL COMMENT 'NOME DA CIDADE NO EXTERIOR',
  `pais` INT NULL COMMENT 'CÓDIGO DO PAIS',
  `data_inicio_atividade` DATETIME NULL COMMENT 'DATA DE INÍCIO DA ATIVIDADE',
  `cnae_fiscal_principal` INT NOT NULL COMMENT 'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO',
  `cnae_fiscal_secundaria` VARCHAR(1000) NULL COMMENT 'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO',
  `tipo_logradouro` VARCHAR(500) NULL,
  `logradouro` VARCHAR(1000) NULL COMMENT 'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.',
  `numero` VARCHAR(45) NULL COMMENT 'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ ‘S/N’.',
  `complemento` VARCHAR(100) NULL COMMENT 'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO',
  `bairro` VARCHAR(45) NULL COMMENT 'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.',
  `cep` VARCHAR(45) NULL COMMENT 'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO',
  `uf` VARCHAR(45) NULL COMMENT 'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO',
  `municipio` INT NULL COMMENT 'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO',
  `ddd_1` VARCHAR(45) NULL,
  `telefone_1` VARCHAR(45) NULL,
  `ddd_2` VARCHAR(45) NULL,
  `telefone_2` VARCHAR(45) NULL,
  `ddd_fax` VARCHAR(45) NULL,
  `fax` VARCHAR(45) NULL,
  `correio_eletronico` VARCHAR(45) NULL,
  `situacao_especial` VARCHAR(45) NULL,
  `data_situacao_especial` DATE NULL,
  PRIMARY KEY (`cnpj_basico`),
  INDEX `fk_estabelecimento_pais_idx` (`pais` ASC),
  INDEX `fk_estabelecimento_munic1_idx` (`municipio` ASC),
  INDEX `fk_estabelecimento_cnae1_idx` (`cnae_fiscal_principal` ASC),
  INDEX `fk_estabelecimento_moti1_idx` (`motivo_situacao_cadastral` ASC),
  CONSTRAINT `fk_estabelecimento_pais`
    FOREIGN KEY (`pais`)
    REFERENCES `dados_rfb`.`pais` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estabelecimento_munic1`
    FOREIGN KEY (`municipio`)
    REFERENCES `dados_rfb`.`munic` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estabelecimento_cnae1`
    FOREIGN KEY (`cnae_fiscal_principal`)
    REFERENCES `dados_rfb`.`cnae` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_estabelecimento_moti1`
    FOREIGN KEY (`motivo_situacao_cadastral`)
    REFERENCES `dados_rfb`.`moti` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `simples`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `simples` ;

CREATE TABLE IF NOT EXISTS `simples` (
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `opcao_pelo_simples` CHAR(1) NULL COMMENT 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES.\n S - SIM\n N - NÃO\n EM BRANCO – OUTROS',
  `data_opcao_simples` DATE NULL COMMENT 'DATA DE OPÇÃO PELO SIMPLES',
  `data_exclusao_simples` DATE NULL COMMENT 'DATA DE EXCLUSÃO DO SIMPLES',
  `opcao_mei` CHAR(1) NULL COMMENT 'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI\n S - SIM\n N - NÃO\n EM BRANCO - OUTROS',
  `data_opcao_mei` DATE NULL COMMENT 'DATA DE OPÇÃO PELO MEI',
  `data_exclusao_mei` DATE NULL COMMENT 'DATA DE EXCLUSÃO DO MEI',
  INDEX `fk_simples_estabelecimento1_idx` (`cnpj_basico` ASC),
  PRIMARY KEY (`cnpj_basico`),
  CONSTRAINT `fk_simples_estabelecimento1`
    FOREIGN KEY (`cnpj_basico`)
    REFERENCES `dados_rfb`.`estabelecimento` (`cnpj_basico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Table `socios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `socios` ;

CREATE TABLE IF NOT EXISTS `socios` (
  `cnpj_basico` CHAR(8) NOT NULL COMMENT 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).',
  `identificador_socio` INT NOT NULL COMMENT 'CÓDIGO DO IDENTIFICADOR DE SÓCIO\n1 – PESSOA JURÍDICA\n2 – PESSOA FÍSICA\n3 – ESTRANGEIRO',
  `nome_socio_razao_social` VARCHAR(1000) NULL COMMENT 'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL \nE/OU NOME EMPRESARIAL DA PESSOA JURÍDICA \nE/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO',
  `cpf_cnpj_socio` VARCHAR(45) NULL COMMENT 'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO).',
  `qualificacao_socio` INT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO',
  `data_entrada_sociedade` DATE NULL,
  `pais` INT NULL COMMENT 'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO',
  `representante_legal` VARCHAR(45) NULL COMMENT 'NÚMERO DO CPF DO REPRESENTANTE LEGAL',
  `nome_do_representante` VARCHAR(500) NULL,
  `qualificacao_representante_legal` INT NULL COMMENT 'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL',
  `faixa_etaria` INT NULL COMMENT 'CÓDIGO CORRESPONDENTE À FAIXA ETÁRIA DO SÓCIO.\nBaseada na data de nascimento do CPF de cada sócio, deverá ser criado o valor para o\ncampo \"Faixa Etária\" conforme a regra abaixo:\n- 1 para os intervalos entre 0 a 12 anos;\n- 2 para os intervalos entre 13 a 20 anos;\n- 3 para os intervalos entre 21 a 30 anos;\n- 4 para os intervalos entre 31 a 40 anos;\n- 5 para os intervalos entre 41 a 50 anos;\n- 6 para os intervalos entre 51 a 60 anos;\n- 7 para os intervalos entre 61 a 70 anos;\n- 8 para os intervalos entre 71 a 80 anos; - 9 para maiores de 80 anos.\n- 0 para não se aplica',
  INDEX `fk_socios_quals1_idx` (`qualificacao_socio` ASC),
  INDEX `fk_socios_pais1_idx` (`pais` ASC),
  INDEX `fk_socios_quals2_idx` (`qualificacao_representante_legal` ASC),
  PRIMARY KEY (`cnpj_basico`),
  CONSTRAINT `fk_socios_quals1`
    FOREIGN KEY (`qualificacao_socio`)
    REFERENCES `dados_rfb`.`quals` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_socios_pais1`
    FOREIGN KEY (`pais`)
    REFERENCES `dados_rfb`.`pais` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_socios_quals2`
    FOREIGN KEY (`qualificacao_representante_legal`)
    REFERENCES `dados_rfb`.`quals` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `empresa`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `empresa` ;

CREATE TABLE IF NOT EXISTS `empresa` (
  `cnpj_basico` CHAR(8) NOT NULL,
  `razao_social` VARCHAR(1000) NULL COMMENT 'NOME EMPRESARIAL DA PESSOA JURÍDICA',
  `natureza_juridica` INT NULL COMMENT 'CÓDIGO DA NATUREZA JURÍDICA',
  `qualificacao_responsavel` INT NULL COMMENT 'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA',
  `capital_social` VARCHAR(45) NULL COMMENT 'CAPITAL SOCIAL DA EMPRESA',
  `porte_empresa` VARCHAR(45) NULL COMMENT 'CÓDIGO DO PORTE DA EMPRESA:\n1 – NÃO INFORMADO\n2 - MICRO EMPRESA\n03 - EMPRESA DE PEQUENO PORTE\n05 - DEMAIS',
  `ente_federativo_responsavel` VARCHAR(45) NULL COMMENT 'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO',
  INDEX `fk_empresa_estabelecimento1_idx` (`cnpj_basico` ASC),
  PRIMARY KEY (`cnpj_basico`),
  INDEX `fk_empresa_natju1_idx` (`natureza_juridica` ASC),
  INDEX `fk_empresa_quals1_idx` (`qualificacao_responsavel` ASC),
  CONSTRAINT `fk_empresa_estabelecimento1`
    FOREIGN KEY (`cnpj_basico`)
    REFERENCES `dados_rfb`.`estabelecimento` (`cnpj_basico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_empresa_natju1`
    FOREIGN KEY (`natureza_juridica`)
    REFERENCES `dados_rfb`.`natju` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_empresa_quals1`
    FOREIGN KEY (`qualificacao_responsavel`)
    REFERENCES `dados_rfb`.`quals` (`codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



DROP USER IF EXISTS 'dados_rfb'@'%';
CREATE USER 'dados_rfb'@'%' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON dados_rfb.* TO 'dados_rfb'@'%';
