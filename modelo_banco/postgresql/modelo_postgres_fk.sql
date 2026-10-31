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

ALTER TABLE dados_rfb.empresa ADD CONSTRAINT fk_emp_natju FOREIGN KEY (natureza_juridica) REFERENCES dados_rfb.natju (codigo);
ALTER TABLE dados_rfb.empresa ADD CONSTRAINT fk_emp_quals FOREIGN KEY (qualificacao_responsavel) REFERENCES dados_rfb.quals (codigo);
ALTER TABLE dados_rfb.estabelecimento ADD CONSTRAINT fk_estab_empresa FOREIGN KEY (cnpj_basico) REFERENCES dados_rfb.empresa (cnpj_basico);
ALTER TABLE dados_rfb.estabelecimento ADD CONSTRAINT fk_estab_pais FOREIGN KEY (pais) REFERENCES dados_rfb.pais (codigo);
ALTER TABLE dados_rfb.estabelecimento ADD CONSTRAINT fk_estab_munic FOREIGN KEY (municipio) REFERENCES dados_rfb.munic (codigo);
ALTER TABLE dados_rfb.estabelecimento ADD CONSTRAINT fk_estab_cnae FOREIGN KEY (cnae_fiscal_principal) REFERENCES dados_rfb.cnae (codigo);
ALTER TABLE dados_rfb.estabelecimento ADD CONSTRAINT fk_estab_moti FOREIGN KEY (motivo_situacao_cadastral) REFERENCES dados_rfb.moti (codigo);
ALTER TABLE dados_rfb.simples ADD CONSTRAINT fk_simples_empresa FOREIGN KEY (cnpj_basico) REFERENCES dados_rfb.empresa (cnpj_basico);
ALTER TABLE dados_rfb.socios ADD CONSTRAINT fk_soc_empresa FOREIGN KEY (cnpj_basico) REFERENCES dados_rfb.empresa (cnpj_basico);
ALTER TABLE dados_rfb.socios ADD CONSTRAINT fk_soc_quals FOREIGN KEY (qualificacao_socio) REFERENCES dados_rfb.quals (codigo);
ALTER TABLE dados_rfb.socios ADD CONSTRAINT fk_soc_quals_rep FOREIGN KEY (qualificacao_representante_legal) REFERENCES dados_rfb.quals (codigo);
ALTER TABLE dados_rfb.socios ADD CONSTRAINT fk_soc_pais FOREIGN KEY (pais) REFERENCES dados_rfb.pais (codigo);
