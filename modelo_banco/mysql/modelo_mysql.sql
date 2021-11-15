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
-- Table `simples`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `simples` ;

CREATE TABLE IF NOT EXISTS `simples` (
  `cnpj_basico` INT NOT NULL,
  PRIMARY KEY (`cnpj_basico`))
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


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



DROP USER IF EXISTS 'dados_rfb'@'%';
CREATE USER 'dados_rfb'@'%' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON dados_rfb.* TO 'dados_rfb'@'%';