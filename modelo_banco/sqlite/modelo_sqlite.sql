CREATE TABLE `cnae` ( `codigo` INTEGER, `descricao` TEXT, PRIMARY KEY(`codigo`) );
CREATE TABLE `moti` ( `codigo` INTEGER, `descricao` TEXT, PRIMARY KEY(`codigo`) );
CREATE TABLE `natju`( `codigo` INTEGER, `descricao` TEXT, PRIMARY KEY(`codigo`) );
CREATE TABLE `quals`( `codigo` INTEGER, `descricao` TEXT, PRIMARY KEY(`codigo`) );
CREATE TABLE `pais` ( `codigo` INTEGER, `descricao` TEXT, PRIMARY KEY(`codigo`) );
CREATE TABLE `munic`( `codigo` INTEGER, `descricao` TEXT, PRIMARY KEY(`codigo`) );

CREATE TABLE `empresa` (
  `cnpj_basico` char(8) NULL,
  `razao_social` varchar(1000) NULL,
  `natureza_juridica` int NULL,
  `qualificacao_responsavel` int NULL,
  `capital_social` varchar(45) NULL,
  `porte_empresa` varchar(45) NULL,
  `ente_federativo_responsavel` varchar(45) NULL
);

CREATE TABLE `estabelecimento` (
  `cnpj_basico` char(8) NOT NULL,
  `cnpj_ordem` char(4) NOT NULL,
  `cnpj_dv` char(2) NOT NULL,
  `identificador_matriz_filial` char(1) NOT NULL,
  `nome_fantasia` varchar(1000) NULL,
  `situacao_cadastral` char(1) NOT NULL,
  `data_situacao_cadastral` date NULL,
  `motivo_situacao_cadastral` int NOT NULL,
  `nome_cidade_exterior` varchar(45) NULL,
  `pais` int NULL,
  `data_inicio_atividade` datetime NULL,
  `cnae_fiscal_principal` int NOT NULL,
  `cnae_fiscal_secundaria` varchar(1000) NULL,
  `tipo_logradouro` varchar(500) NULL,
  `logradouro` varchar(1000) NULL,
  `numero` varchar(45) NULL,
  `complemento` varchar(100) NULL,
  `bairro` varchar(45) NULL,
  `cep` varchar(45) NULL,
  `uf` varchar(45) NULL,
  `municipio` int NULL,
  `ddd_1` varchar(45) NULL,
  `telefone_1` varchar(45) NULL,
  `ddd_2` varchar(45) NULL,
  `telefone_2` varchar(45) NULL,
  `ddd_fax` varchar(45) NULL,
  `fax` varchar(45) NULL,
  `correio_eletronico` varchar(45) NULL,
  `situacao_especial` varchar(45) NULL,
  `data_situacao_especial` date NULL
  );


CREATE TABLE IF NOT EXISTS `simples` (
  `cnpj_basico` CHAR(8) NOT NULL,
  `opcao_pelo_simples` CHAR(1) NULL,
  `data_opcao_simples` DATE NULL,
  `data_exclusao_simples` DATE NULL,
  `opcao_mei` CHAR(1) NULL,
  `data_opcao_mei` DATE NULL,
  `data_exclusao_mei` DATE NULL
);

CREATE TABLE IF NOT EXISTS `socios` (
  `cnpj_basico` CHAR(8) NOT NULL,
  `identificador_socio` INT NOT NULL ,
  `nome_socio_razao_social` VARCHAR(1000) NULL,
  `cpf_cnpj_socio` VARCHAR(45) NULL,
  `qualificacao_socio` INT NULL,
  `data_entrada_sociedade` DATE NULL,
  `pais` INT NULL,
  `representante_legal` VARCHAR(45) NULL,
  `nome_do_representante` VARCHAR(500) NULL,
  `qualificacao_representante_legal` INT NULL,
  `faixa_etaria` INT NULL
  );