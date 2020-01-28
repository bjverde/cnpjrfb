BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS `socios` (
	`cnpj`	TEXT,
	`tipo_socio`	TEXT,
	`nome_socio`	TEXT,
	`cnpj_cpf_socio`	TEXT,
	`cod_qualificacao`	TEXT,
	`perc_capital`	REAL,
	`data_entrada`	TEXT,
	`cod_pais_ext`	TEXT,
	`nome_pais_ext`	TEXT,
	`cpf_repres`	TEXT,
	`nome_repres`	TEXT,
	`cod_qualif_repres`	TEXT
);
CREATE TABLE IF NOT EXISTS `empresas` (
	`cnpj`	TEXT,
	`matriz_filial`	TEXT,
	`razao_social`	TEXT,
	`nome_fantasia`	TEXT,
	`situacao`	TEXT,
	`data_situacao`	TEXT,
	`motivo_situacao`	TEXT,
	`nm_cidade_exterior`	TEXT,
	`cod_pais`	TEXT,
	`nome_pais`	TEXT,
	`cod_nat_juridica`	TEXT,
	`data_inicio_ativ`	TEXT,
	`cnae_fiscal`	TEXT,
	`tipo_logradouro`	TEXT,
	`logradouro`	TEXT,
	`numero`	TEXT,
	`complemento`	TEXT,
	`bairro`	TEXT,
	`cep`	TEXT,
	`uf`	TEXT,
	`cod_municipio`	TEXT,
	`municipio`	TEXT,
	`ddd_1`	TEXT,
	`telefone_1`	TEXT,
	`ddd_2`	TEXT,
	`telefone_2`	TEXT,
	`ddd_fax`	TEXT,
	`num_fax`	TEXT,
	`email`	TEXT,
	`qualif_resp`	TEXT,
	`capital_social`	REAL,
	`porte`	TEXT,
	`opc_simples`	TEXT,
	`data_opc_simples`	TEXT,
	`data_exc_simples`	TEXT,
	`opc_mei`	TEXT,
	`sit_especial`	TEXT,
	`data_sit_especial`	TEXT
);
CREATE TABLE IF NOT EXISTS `cnaes_secundarios` (
	`cnpj`	TEXT,
	`cnae_ordem`	INTEGER,
	`cnae`	TEXT
);
CREATE INDEX IF NOT EXISTS `ix_socios_nome` ON `socios` (
	`nome_socio`
);
CREATE INDEX IF NOT EXISTS `ix_socios_cpf_cnpj` ON `socios` (
	`cnpj_cpf_socio`
);
CREATE INDEX IF NOT EXISTS `ix_socios_cnpj` ON `socios` (
	`cnpj`
);
CREATE INDEX IF NOT EXISTS `ix_empresas_cnpj` ON `empresas` (
	`cnpj`
);
CREATE INDEX IF NOT EXISTS `ix_cnaes_cnpj` ON `cnaes_secundarios` (
	`cnpj`
);
COMMIT;
