-- -----------------------------------------------------------------------------
-- Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- Chaves estrangeiras. Rode este script DEPOIS da carga dos dados.
-- Durante a carga as FKs deixam o processo muito mais lento e impedem carregar
-- estabelecimento/socios/simples antes de empresa. Se algum código não existir
-- nas tabelas de domínio a criação da FK correspondente vai falhar: nesse caso
-- corrija os dados ou deixe essa FK de fora.
-- -----------------------------------------------------------------------------

USE `dados_rfb`;

ALTER TABLE `empresa` ADD CONSTRAINT `fk_emp_natju` FOREIGN KEY (`natureza_juridica`) REFERENCES `natju` (`codigo`);
ALTER TABLE `empresa` ADD CONSTRAINT `fk_emp_quals` FOREIGN KEY (`qualificacao_responsavel`) REFERENCES `quals` (`codigo`);
ALTER TABLE `estabelecimento` ADD CONSTRAINT `fk_estab_empresa` FOREIGN KEY (`cnpj_basico`) REFERENCES `empresa` (`cnpj_basico`);
ALTER TABLE `estabelecimento` ADD CONSTRAINT `fk_estab_pais` FOREIGN KEY (`pais`) REFERENCES `pais` (`codigo`);
ALTER TABLE `estabelecimento` ADD CONSTRAINT `fk_estab_munic` FOREIGN KEY (`municipio`) REFERENCES `munic` (`codigo`);
ALTER TABLE `estabelecimento` ADD CONSTRAINT `fk_estab_cnae` FOREIGN KEY (`cnae_fiscal_principal`) REFERENCES `cnae` (`codigo`);
ALTER TABLE `estabelecimento` ADD CONSTRAINT `fk_estab_moti` FOREIGN KEY (`motivo_situacao_cadastral`) REFERENCES `moti` (`codigo`);
ALTER TABLE `simples` ADD CONSTRAINT `fk_simples_empresa` FOREIGN KEY (`cnpj_basico`) REFERENCES `empresa` (`cnpj_basico`);
ALTER TABLE `socios` ADD CONSTRAINT `fk_soc_empresa` FOREIGN KEY (`cnpj_basico`) REFERENCES `empresa` (`cnpj_basico`);
ALTER TABLE `socios` ADD CONSTRAINT `fk_soc_quals` FOREIGN KEY (`qualificacao_socio`) REFERENCES `quals` (`codigo`);
ALTER TABLE `socios` ADD CONSTRAINT `fk_soc_quals_rep` FOREIGN KEY (`qualificacao_representante_legal`) REFERENCES `quals` (`codigo`);
ALTER TABLE `socios` ADD CONSTRAINT `fk_soc_pais` FOREIGN KEY (`pais`) REFERENCES `pais` (`codigo`);
