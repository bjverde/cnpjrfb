#!/usr/bin/env python3
"""
Gera os scripts de criação do banco de dados dos dados abertos do CNPJ
para MySQL/MariaDB, PostgreSQL, SQLite, SQL Server e Oracle a partir de
uma única definição do modelo, para que todos fiquem iguais.

Layout de referência: "Novo Layout para os DADOS ABERTOS do CNPJ"
(cnpj-metadados.pdf) publicado pela Receita Federal do Brasil.

Uso:
    python3 gerar_modelos.py

Altere apenas a definição das tabelas abaixo e rode o script de novo.
"""
from pathlib import Path

SCHEMA = 'dados_rfb'
DESCRICAO_BANCO = 'Base de dados para gravar os dados públicos de CNPJ da Receita Federal do Brasil'
PASTA = Path(__file__).resolve().parent

# -----------------------------------------------------------------------------
# Definição do modelo
#
# Coluna: (nome, tipo, tamanho, obrigatoria, comentario)
#   tipo: 'int', 'char', 'varchar', 'decimal', 'date', 'id'
#   'id' é uma chave substituta numérica gerada pelo banco (autoincremento).
# -----------------------------------------------------------------------------


def dominio(nome, comentario, desc_codigo, desc_descricao):
    return {
        'nome': nome,
        'comentario': comentario,
        'colunas': [
            ('codigo', 'int', None, True, desc_codigo),
            ('descricao', 'varchar', 1000, True, desc_descricao),
        ],
        'pk': ['codigo'],
        'indices': [],
        'fks': [],
    }


CNPJ_BASICO = 'NÚMERO BASE DE INSCRIÇÃO NO CNPJ (OITO PRIMEIROS DÍGITOS DO CNPJ).'

TABELAS = [
    dominio('cnae', 'CNAEs: tabela de domínio de atividades econômicas',
            'CÓDIGO DA ATIVIDADE ECONÔMICA', 'NOME DA ATIVIDADE ECONÔMICA'),
    dominio('moti', 'Motivos da situação cadastral: tabela de domínio (arquivo Motivos da RFB)',
            'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL', 'DESCRIÇÃO DO MOTIVO DA SITUAÇÃO CADASTRAL'),
    dominio('munic', 'Municípios: tabela de domínio (código de município da RFB, não é o código IBGE)',
            'CÓDIGO DO MUNICÍPIO', 'NOME DO MUNICÍPIO'),
    dominio('natju', 'Naturezas jurídicas: tabela de domínio',
            'CÓDIGO DA NATUREZA JURÍDICA', 'NOME DA NATUREZA JURÍDICA'),
    dominio('pais', 'Países: tabela de domínio',
            'CÓDIGO DO PAÍS', 'NOME DO PAÍS'),
    dominio('quals', 'Qualificações de sócios: tabela de domínio',
            'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO', 'NOME DA QUALIFICAÇÃO DO SÓCIO'),
    {
        'nome': 'empresa',
        'comentario': 'Empresas: dados cadastrais da matriz, uma linha por CNPJ básico',
        'colunas': [
            ('cnpj_basico', 'char', 8, True, CNPJ_BASICO),
            ('razao_social', 'varchar', 255, False, 'NOME EMPRESARIAL DA PESSOA JURÍDICA'),
            ('natureza_juridica', 'int', None, False, 'CÓDIGO DA NATUREZA JURÍDICA'),
            ('qualificacao_responsavel', 'int', None, False, 'QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PELA EMPRESA'),
            ('capital_social', 'decimal', (18, 2), False, 'CAPITAL SOCIAL DA EMPRESA'),
            ('porte_empresa', 'char', 2, False,
             'CÓDIGO DO PORTE DA EMPRESA: 00 - NÃO INFORMADO; 01 - MICRO EMPRESA; 03 - EMPRESA DE PEQUENO PORTE; 05 - DEMAIS'),
            ('ente_federativo_responsavel', 'varchar', 100, False,
             'O ENTE FEDERATIVO RESPONSÁVEL É PREENCHIDO PARA OS CASOS DE ÓRGÃOS E ENTIDADES DO GRUPO DE NATUREZA JURÍDICA 1XXX. PARA AS DEMAIS NATUREZAS, ESTE ATRIBUTO FICA EM BRANCO'),
        ],
        'pk': ['cnpj_basico'],
        'indices': [
            ('idx_emp_razao_social', ['razao_social']),
            ('idx_emp_natju', ['natureza_juridica']),
            ('idx_emp_quals', ['qualificacao_responsavel']),
        ],
        'fks': [
            ('fk_emp_natju', ['natureza_juridica'], 'natju', ['codigo']),
            ('fk_emp_quals', ['qualificacao_responsavel'], 'quals', ['codigo']),
        ],
    },
    {
        'nome': 'estabelecimento',
        'comentario': 'Estabelecimentos: matriz e filiais, uma linha por CNPJ completo',
        'colunas': [
            ('cnpj_basico', 'char', 8, True, CNPJ_BASICO),
            ('cnpj_ordem', 'char', 4, True, 'NÚMERO DO ESTABELECIMENTO DE INSCRIÇÃO NO CNPJ (DO NONO ATÉ O DÉCIMO SEGUNDO DÍGITO DO CNPJ).'),
            ('cnpj_dv', 'char', 2, True, 'DÍGITO VERIFICADOR DO NÚMERO DE INSCRIÇÃO NO CNPJ (DOIS ÚLTIMOS DÍGITOS DO CNPJ).'),
            ('identificador_matriz_filial', 'char', 1, True, 'CÓDIGO DO IDENTIFICADOR MATRIZ/FILIAL: 1 - MATRIZ; 2 - FILIAL'),
            ('nome_fantasia', 'varchar', 255, False, 'CORRESPONDE AO NOME FANTASIA'),
            ('situacao_cadastral', 'char', 2, True,
             'CÓDIGO DA SITUAÇÃO CADASTRAL: 01 - NULA; 02 - ATIVA; 03 - SUSPENSA; 04 - INAPTA; 08 - BAIXADA'),
            ('data_situacao_cadastral', 'date', None, False, 'DATA DO EVENTO DA SITUAÇÃO CADASTRAL'),
            ('motivo_situacao_cadastral', 'int', None, True, 'CÓDIGO DO MOTIVO DA SITUAÇÃO CADASTRAL'),
            ('nome_cidade_exterior', 'varchar', 100, False, 'NOME DA CIDADE NO EXTERIOR'),
            ('pais', 'int', None, False, 'CÓDIGO DO PAIS'),
            ('data_inicio_atividade', 'date', None, False, 'DATA DE INÍCIO DA ATIVIDADE'),
            ('cnae_fiscal_principal', 'int', None, True, 'CÓDIGO DA ATIVIDADE ECONÔMICA PRINCIPAL DO ESTABELECIMENTO'),
            ('cnae_fiscal_secundaria', 'varchar', 1000, False,
             'CÓDIGO DA(S) ATIVIDADE(S) ECONÔMICA(S) SECUNDÁRIA(S) DO ESTABELECIMENTO, SEPARADOS POR VÍRGULA'),
            ('tipo_logradouro', 'varchar', 50, False, 'DESCRIÇÃO DO TIPO DE LOGRADOURO'),
            ('logradouro', 'varchar', 255, False, 'NOME DO LOGRADOURO ONDE SE LOCALIZA O ESTABELECIMENTO.'),
            ('numero', 'varchar', 20, False,
             'NÚMERO ONDE SE LOCALIZA O ESTABELECIMENTO. QUANDO NÃO HOUVER PREENCHIMENTO DO NÚMERO HAVERÁ S/N.'),
            ('complemento', 'varchar', 255, False, 'COMPLEMENTO PARA O ENDEREÇO DE LOCALIZAÇÃO DO ESTABELECIMENTO'),
            ('bairro', 'varchar', 100, False, 'BAIRRO ONDE SE LOCALIZA O ESTABELECIMENTO.'),
            ('cep', 'char', 8, False,
             'CÓDIGO DE ENDEREÇAMENTO POSTAL REFERENTE AO LOGRADOURO NO QUAL O ESTABELECIMENTO ESTA LOCALIZADO'),
            ('uf', 'char', 2, False, 'SIGLA DA UNIDADE DA FEDERAÇÃO EM QUE SE ENCONTRA O ESTABELECIMENTO'),
            ('municipio', 'int', None, False, 'CÓDIGO DO MUNICÍPIO DE JURISDIÇÃO ONDE SE ENCONTRA O ESTABELECIMENTO'),
            ('ddd_1', 'varchar', 4, False, 'CONTÉM O DDD 1'),
            ('telefone_1', 'varchar', 10, False, 'CONTÉM O NÚMERO DO TELEFONE 1'),
            ('ddd_2', 'varchar', 4, False, 'CONTÉM O DDD 2'),
            ('telefone_2', 'varchar', 10, False, 'CONTÉM O NÚMERO DO TELEFONE 2'),
            ('ddd_fax', 'varchar', 4, False, 'CONTÉM O DDD DO FAX'),
            ('fax', 'varchar', 10, False, 'CONTÉM O NÚMERO DO FAX'),
            ('correio_eletronico', 'varchar', 255, False, 'CONTÉM O E-MAIL DO CONTRIBUINTE'),
            ('situacao_especial', 'varchar', 100, False, 'SITUAÇÃO ESPECIAL DA EMPRESA'),
            ('data_situacao_especial', 'date', None, False, 'DATA EM QUE A EMPRESA ENTROU EM SITUAÇÃO ESPECIAL'),
        ],
        'pk': ['cnpj_basico', 'cnpj_ordem', 'cnpj_dv'],
        'indices': [
            ('idx_estab_pais', ['pais']),
            ('idx_estab_munic', ['municipio']),
            ('idx_estab_cnae', ['cnae_fiscal_principal']),
            ('idx_estab_moti', ['motivo_situacao_cadastral']),
        ],
        'fks': [
            ('fk_estab_empresa', ['cnpj_basico'], 'empresa', ['cnpj_basico']),
            ('fk_estab_pais', ['pais'], 'pais', ['codigo']),
            ('fk_estab_munic', ['municipio'], 'munic', ['codigo']),
            ('fk_estab_cnae', ['cnae_fiscal_principal'], 'cnae', ['codigo']),
            ('fk_estab_moti', ['motivo_situacao_cadastral'], 'moti', ['codigo']),
        ],
    },
    {
        'nome': 'simples',
        'comentario': 'Dados do Simples Nacional e do MEI, uma linha por CNPJ básico',
        'colunas': [
            ('cnpj_basico', 'char', 8, True, CNPJ_BASICO),
            ('opcao_pelo_simples', 'char', 1, False,
             'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO SIMPLES: S - SIM; N - NÃO; EM BRANCO - OUTROS'),
            ('data_opcao_simples', 'date', None, False, 'DATA DE OPÇÃO PELO SIMPLES'),
            ('data_exclusao_simples', 'date', None, False, 'DATA DE EXCLUSÃO DO SIMPLES'),
            ('opcao_mei', 'char', 1, False,
             'INDICADOR DA EXISTÊNCIA DA OPÇÃO PELO MEI: S - SIM; N - NÃO; EM BRANCO - OUTROS'),
            ('data_opcao_mei', 'date', None, False, 'DATA DE OPÇÃO PELO MEI'),
            ('data_exclusao_mei', 'date', None, False, 'DATA DE EXCLUSÃO DO MEI'),
        ],
        'pk': ['cnpj_basico'],
        'indices': [],
        'fks': [
            ('fk_simples_empresa', ['cnpj_basico'], 'empresa', ['cnpj_basico']),
        ],
    },
    {
        'nome': 'socios',
        'comentario': 'Sócios: várias linhas por CNPJ básico',
        'colunas': [
            ('id', 'id', None, True, 'CHAVE SUBSTITUTA GERADA PELO BANCO (NÃO EXISTE NO ARQUIVO DA RFB)'),
            ('cnpj_basico', 'char', 8, True, CNPJ_BASICO),
            ('identificador_socio', 'int', None, True,
             'CÓDIGO DO IDENTIFICADOR DE SÓCIO: 1 - PESSOA JURÍDICA; 2 - PESSOA FÍSICA; 3 - ESTRANGEIRO'),
            ('nome_socio_razao_social', 'varchar', 255, False,
             'NOME DO SÓCIO PESSOA FÍSICA OU A RAZÃO SOCIAL E/OU NOME EMPRESARIAL DA PESSOA JURÍDICA E/OU NOME DO SÓCIO/RAZÃO SOCIAL DO SÓCIO ESTRANGEIRO'),
            ('cpf_cnpj_socio', 'varchar', 14, False,
             'CPF OU CNPJ DO SÓCIO (SÓCIO ESTRANGEIRO NÃO TEM ESTA INFORMAÇÃO). CPF DESCARACTERIZADO: TRÊS PRIMEIROS DÍGITOS E OS DOIS DÍGITOS VERIFICADORES OCULTOS'),
            ('qualificacao_socio', 'int', None, False, 'CÓDIGO DA QUALIFICAÇÃO DO SÓCIO'),
            ('data_entrada_sociedade', 'date', None, False, 'DATA DE ENTRADA NA SOCIEDADE'),
            ('pais', 'int', None, False, 'CÓDIGO PAÍS DO SÓCIO ESTRANGEIRO'),
            ('representante_legal', 'varchar', 11, False,
             'NÚMERO DO CPF DO REPRESENTANTE LEGAL (DESCARACTERIZADO)'),
            ('nome_do_representante', 'varchar', 255, False, 'NOME DO REPRESENTANTE LEGAL'),
            ('qualificacao_representante_legal', 'int', None, False, 'CÓDIGO DA QUALIFICAÇÃO DO REPRESENTANTE LEGAL'),
            ('faixa_etaria', 'int', None, False,
             'CÓDIGO DA FAIXA ETÁRIA DO SÓCIO: 0 - NÃO SE APLICA; 1 - 0 A 12 ANOS; 2 - 13 A 20; 3 - 21 A 30; 4 - 31 A 40; 5 - 41 A 50; 6 - 51 A 60; 7 - 61 A 70; 8 - 71 A 80; 9 - MAIORES DE 80'),
        ],
        'pk': ['id'],
        'indices': [
            ('idx_soc_cnpj_basico', ['cnpj_basico']),
            ('idx_soc_cpf_cnpj', ['cpf_cnpj_socio']),
            ('idx_soc_nome', ['nome_socio_razao_social']),
            ('idx_soc_quals', ['qualificacao_socio']),
            ('idx_soc_quals_rep', ['qualificacao_representante_legal']),
            ('idx_soc_pais', ['pais']),
        ],
        'fks': [
            ('fk_soc_empresa', ['cnpj_basico'], 'empresa', ['cnpj_basico']),
            ('fk_soc_quals', ['qualificacao_socio'], 'quals', ['codigo']),
            ('fk_soc_quals_rep', ['qualificacao_representante_legal'], 'quals', ['codigo']),
            ('fk_soc_pais', ['pais'], 'pais', ['codigo']),
        ],
    },
]

AVISO = f"""-- -----------------------------------------------------------------------------
-- {DESCRICAO_BANCO}
-- Layout: "Novo Layout para os DADOS ABERTOS do CNPJ" (cnpj-metadados.pdf)
--
-- ARQUIVO GERADO por modelo_banco/gerar_modelos.py. Não edite à mão:
-- altere o gerador e rode de novo para manter todos os bancos iguais.
-- -----------------------------------------------------------------------------
"""

AVISO_FK = """-- -----------------------------------------------------------------------------
-- Chaves estrangeiras. Rode este script DEPOIS da carga dos dados.
-- Durante a carga as FKs deixam o processo muito mais lento e impedem carregar
-- estabelecimento/socios/simples antes de empresa. Se algum código não existir
-- nas tabelas de domínio a criação da FK correspondente vai falhar: nesse caso
-- corrija os dados ou deixe essa FK de fora.
-- -----------------------------------------------------------------------------
"""


def formatar_tamanho(tam):
    if tam is None:
        return ()
    if isinstance(tam, tuple):
        return tam
    return (tam,)


def sql_str(texto):
    return "'" + texto.replace("'", "''") + "'"


def ordem_drop():
    return list(reversed(TABELAS))


# -----------------------------------------------------------------------------
# MySQL / MariaDB
# -----------------------------------------------------------------------------
def tipo_mysql(tipo, tam):
    return {
        'int': 'INT',
        'char': 'CHAR({0})',
        'varchar': 'VARCHAR({0})',
        'decimal': 'DECIMAL({0},{1})',
        'date': 'DATE',
        'id': 'BIGINT',
    }[tipo].format(*formatar_tamanho(tam))


def gerar_mysql():
    s = [AVISO]
    s.append("SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;")
    s.append("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;")
    s.append("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';\n")
    s.append(f"DROP SCHEMA IF EXISTS `{SCHEMA}`;")
    s.append(f"CREATE SCHEMA IF NOT EXISTS `{SCHEMA}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")
    s.append(f"USE `{SCHEMA}`;\n")
    for t in TABELAS:
        s.append("-- -----------------------------------------------------")
        s.append(f"-- Tabela `{t['nome']}`")
        s.append("-- -----------------------------------------------------")
        s.append(f"DROP TABLE IF EXISTS `{t['nome']}`;\n")
        linhas = []
        for nome, tipo, tam, obrig, com in t['colunas']:
            nulo = 'NOT NULL' if obrig else 'NULL'
            extra = ' AUTO_INCREMENT' if tipo == 'id' else ''
            linhas.append(f"  `{nome}` {tipo_mysql(tipo, tam)} {nulo}{extra} COMMENT {sql_str(com)}")
        linhas.append("  PRIMARY KEY (" + ', '.join(f'`{c}`' for c in t['pk']) + ")")
        for nome_idx, cols in t['indices']:
            linhas.append(f"  INDEX `{nome_idx}` (" + ', '.join(f'`{c}`' for c in cols) + ")")
        s.append(f"CREATE TABLE IF NOT EXISTS `{t['nome']}` (")
        s.append(',\n'.join(linhas))
        s.append(f") ENGINE = InnoDB\nCOMMENT = {sql_str(t['comentario'])};\n")
    s.append("SET SQL_MODE=@OLD_SQL_MODE;")
    s.append("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;")
    s.append("SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;\n")
    s.append("-- Usuário da aplicação. Troque a senha antes de usar.")
    s.append(f"-- DROP USER IF EXISTS '{SCHEMA}'@'%';")
    s.append(f"-- CREATE USER '{SCHEMA}'@'%' IDENTIFIED BY 'troque_esta_senha';")
    s.append(f"-- GRANT ALL PRIVILEGES ON {SCHEMA}.* TO '{SCHEMA}'@'%';")
    return '\n'.join(s) + '\n'


def gerar_mysql_fk():
    s = [AVISO, AVISO_FK, f"USE `{SCHEMA}`;\n"]
    for t in TABELAS:
        for nome_fk, cols, ref, ref_cols in t['fks']:
            s.append(f"ALTER TABLE `{t['nome']}` ADD CONSTRAINT `{nome_fk}`"
                     f" FOREIGN KEY ({', '.join(f'`{c}`' for c in cols)})"
                     f" REFERENCES `{ref}` ({', '.join(f'`{c}`' for c in ref_cols)});")
    return '\n'.join(s) + '\n'


# -----------------------------------------------------------------------------
# PostgreSQL
# -----------------------------------------------------------------------------
def tipo_pg(tipo, tam):
    return {
        'int': 'INTEGER',
        'char': 'CHAR({0})',
        'varchar': 'VARCHAR({0})',
        'decimal': 'NUMERIC({0},{1})',
        'date': 'DATE',
        'id': 'BIGINT GENERATED BY DEFAULT AS IDENTITY',
    }[tipo].format(*formatar_tamanho(tam))


def gerar_postgres():
    s = [AVISO]
    s.append("-- Criar a base de dados (rode separado, conectado na base postgres):")
    s.append(f"-- CREATE DATABASE {SCHEMA} WITH ENCODING = 'UTF8';")
    s.append(f"-- COMMENT ON DATABASE {SCHEMA} IS {sql_str(DESCRICAO_BANCO)};")
    s.append(f"-- Depois conecte na base {SCHEMA} e rode o restante deste script.\n")
    s.append(f"CREATE SCHEMA IF NOT EXISTS {SCHEMA};")
    s.append(f"COMMENT ON SCHEMA {SCHEMA} IS {sql_str(DESCRICAO_BANCO)};")
    s.append(f"SET search_path TO {SCHEMA};\n")
    for t in ordem_drop():
        s.append(f"DROP TABLE IF EXISTS {SCHEMA}.{t['nome']} CASCADE;")
    s.append('')
    for t in TABELAS:
        n = f"{SCHEMA}.{t['nome']}"
        s.append("-- -----------------------------------------------------")
        s.append(f"-- Tabela {t['nome']}")
        s.append("-- -----------------------------------------------------")
        linhas = []
        for nome, tipo, tam, obrig, com in t['colunas']:
            nulo = 'NOT NULL' if obrig else 'NULL'
            linhas.append(f"  {nome} {tipo_pg(tipo, tam)} {nulo}")
        linhas.append(f"  CONSTRAINT pk_{t['nome']} PRIMARY KEY (" + ', '.join(t['pk']) + ")")
        s.append(f"CREATE TABLE {n} (")
        s.append(',\n'.join(linhas))
        s.append(");")
        for nome_idx, cols in t['indices']:
            s.append(f"CREATE INDEX {nome_idx} ON {n} (" + ', '.join(cols) + ");")
        s.append(f"COMMENT ON TABLE {n} IS {sql_str(t['comentario'])};")
        for nome, tipo, tam, obrig, com in t['colunas']:
            s.append(f"COMMENT ON COLUMN {n}.{nome} IS {sql_str(com)};")
        s.append('')
    return '\n'.join(s) + '\n'


def gerar_postgres_fk():
    s = [AVISO, AVISO_FK]
    for t in TABELAS:
        for nome_fk, cols, ref, ref_cols in t['fks']:
            s.append(f"ALTER TABLE {SCHEMA}.{t['nome']} ADD CONSTRAINT {nome_fk}"
                     f" FOREIGN KEY ({', '.join(cols)})"
                     f" REFERENCES {SCHEMA}.{ref} ({', '.join(ref_cols)});")
    return '\n'.join(s) + '\n'


# -----------------------------------------------------------------------------
# SQLite (sem schema; FKs declaradas na tabela, pois o SQLite não aceita
# ALTER TABLE ADD CONSTRAINT. Só são verificadas com PRAGMA foreign_keys = ON)
# -----------------------------------------------------------------------------
def tipo_sqlite(tipo, tam):
    return {
        'int': 'INTEGER',
        'char': 'CHAR({0})',
        'varchar': 'VARCHAR({0})',
        'decimal': 'DECIMAL({0},{1})',
        'date': 'DATE',
        'id': 'INTEGER',
    }[tipo].format(*formatar_tamanho(tam))


def gerar_sqlite():
    s = [AVISO]
    s.append("-- O SQLite só verifica chaves estrangeiras com PRAGMA foreign_keys = ON.")
    s.append("-- Deixe desligado durante a carga dos dados.\n")
    for t in ordem_drop():
        s.append(f"DROP TABLE IF EXISTS {t['nome']};")
    s.append('')
    for t in TABELAS:
        s.append(f"-- {t['comentario']}")
        linhas = []
        for nome, tipo, tam, obrig, com in t['colunas']:
            if tipo == 'id':
                linhas.append(f"  {nome} INTEGER PRIMARY KEY AUTOINCREMENT")
                continue
            nulo = 'NOT NULL' if obrig else 'NULL'
            linhas.append(f"  {nome} {tipo_sqlite(tipo, tam)} {nulo}")
        if t['colunas'][0][1] != 'id':
            linhas.append(f"  CONSTRAINT pk_{t['nome']} PRIMARY KEY (" + ', '.join(t['pk']) + ")")
        for nome_fk, cols, ref, ref_cols in t['fks']:
            linhas.append(f"  CONSTRAINT {nome_fk} FOREIGN KEY ({', '.join(cols)}) REFERENCES {ref} ({', '.join(ref_cols)})")
        s.append(f"CREATE TABLE {t['nome']} (")
        s.append(',\n'.join(linhas))
        s.append(");")
        for nome_idx, cols in t['indices']:
            s.append(f"CREATE INDEX {nome_idx} ON {t['nome']} (" + ', '.join(cols) + ");")
        s.append('')
    return '\n'.join(s) + '\n'


# -----------------------------------------------------------------------------
# SQL Server
# -----------------------------------------------------------------------------
def tipo_sqlserver(tipo, tam):
    return {
        'int': 'INT',
        'char': 'CHAR({0})',
        'varchar': 'NVARCHAR({0})',
        'decimal': 'DECIMAL({0},{1})',
        'date': 'DATE',
        'id': 'BIGINT IDENTITY(1,1)',
    }[tipo].format(*formatar_tamanho(tam))


def gerar_sqlserver():
    s = [AVISO]
    s.append("-- Requer SQL Server 2016 ou superior (DROP TABLE IF EXISTS).")
    s.append("-- Crie a base antes, se necessário, e selecione-a:")
    s.append(f"-- CREATE DATABASE {SCHEMA};")
    s.append("-- GO")
    s.append(f"-- USE {SCHEMA};")
    s.append("-- GO\n")
    for t in ordem_drop():
        s.append(f"DROP TABLE IF EXISTS {SCHEMA}.{t['nome']};")
    s.append("GO\n")
    s.append(f"IF SCHEMA_ID(N'{SCHEMA}') IS NULL EXEC(N'CREATE SCHEMA {SCHEMA}');")
    s.append("GO\n")
    for t in TABELAS:
        n = f"{SCHEMA}.{t['nome']}"
        s.append("-- -----------------------------------------------------")
        s.append(f"-- Tabela {t['nome']}")
        s.append("-- -----------------------------------------------------")
        linhas = []
        for nome, tipo, tam, obrig, com in t['colunas']:
            nulo = 'NOT NULL' if obrig else 'NULL'
            linhas.append(f"  {nome} {tipo_sqlserver(tipo, tam)} {nulo}")
        linhas.append(f"  CONSTRAINT pk_{t['nome']} PRIMARY KEY CLUSTERED (" + ', '.join(t['pk']) + ")")
        s.append(f"CREATE TABLE {n} (")
        s.append(',\n'.join(linhas))
        s.append(");")
        for nome_idx, cols in t['indices']:
            s.append(f"CREATE INDEX {nome_idx} ON {n} (" + ', '.join(cols) + ");")
        s.append(f"EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N{sql_str(t['comentario'])}, "
                 f"@level0type=N'SCHEMA', @level0name=N'{SCHEMA}', @level1type=N'TABLE', @level1name=N'{t['nome']}';")
        for nome, tipo, tam, obrig, com in t['colunas']:
            s.append(f"EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N{sql_str(com)}, "
                     f"@level0type=N'SCHEMA', @level0name=N'{SCHEMA}', @level1type=N'TABLE', @level1name=N'{t['nome']}', "
                     f"@level2type=N'COLUMN', @level2name=N'{nome}';")
        s.append("GO\n")
    return '\n'.join(s) + '\n'


def gerar_sqlserver_fk():
    s = [AVISO, AVISO_FK]
    for t in TABELAS:
        for nome_fk, cols, ref, ref_cols in t['fks']:
            s.append(f"ALTER TABLE {SCHEMA}.{t['nome']} ADD CONSTRAINT {nome_fk}"
                     f" FOREIGN KEY ({', '.join(cols)})"
                     f" REFERENCES {SCHEMA}.{ref} ({', '.join(ref_cols)});")
    s.append("GO")
    return '\n'.join(s) + '\n'


# -----------------------------------------------------------------------------
# Oracle (12c ou superior, por causa da coluna IDENTITY)
# -----------------------------------------------------------------------------
def tipo_oracle(tipo, tam):
    return {
        'int': 'NUMBER(10)',
        'char': 'CHAR({0} CHAR)',
        'varchar': 'VARCHAR2({0} CHAR)',
        'decimal': 'NUMBER({0},{1})',
        'date': 'DATE',
        'id': 'NUMBER(19) GENERATED BY DEFAULT ON NULL AS IDENTITY',
    }[tipo].format(*formatar_tamanho(tam))


def gerar_oracle():
    s = [AVISO]
    s.append("-- Requer Oracle 12c ou superior (coluna IDENTITY).")
    s.append("-- No Oracle o schema é o usuário. Crie o usuário (como DBA) e conecte com ele:")
    s.append(f"-- CREATE USER {SCHEMA} IDENTIFIED BY \"troque_esta_senha\" QUOTA UNLIMITED ON USERS;")
    s.append(f"-- GRANT CREATE SESSION, CREATE TABLE TO {SCHEMA};")
    s.append("-- Atenção: no Oracle texto vazio ('') é NULL.\n")
    for t in ordem_drop():
        s.append("BEGIN")
        s.append(f"  EXECUTE IMMEDIATE 'DROP TABLE {t['nome']} CASCADE CONSTRAINTS PURGE';")
        s.append("EXCEPTION WHEN OTHERS THEN IF SQLCODE != -942 THEN RAISE; END IF;")
        s.append("END;")
        s.append("/")
    s.append('')
    for t in TABELAS:
        n = t['nome']
        s.append("-- -----------------------------------------------------")
        s.append(f"-- Tabela {n}")
        s.append("-- -----------------------------------------------------")
        linhas = []
        for nome, tipo, tam, obrig, com in t['colunas']:
            nulo = 'NOT NULL' if obrig else 'NULL'
            if tipo == 'id':
                nulo = 'NOT NULL'
            linhas.append(f"  {nome} {tipo_oracle(tipo, tam)} {nulo}")
        linhas.append(f"  CONSTRAINT pk_{n} PRIMARY KEY (" + ', '.join(t['pk']) + ")")
        s.append(f"CREATE TABLE {n} (")
        s.append(',\n'.join(linhas))
        s.append(");")
        for nome_idx, cols in t['indices']:
            s.append(f"CREATE INDEX {nome_idx} ON {n} (" + ', '.join(cols) + ");")
        s.append(f"COMMENT ON TABLE {n} IS {sql_str(t['comentario'])};")
        for nome, tipo, tam, obrig, com in t['colunas']:
            s.append(f"COMMENT ON COLUMN {n}.{nome} IS {sql_str(com)};")
        s.append('')
    return '\n'.join(s) + '\n'


def gerar_oracle_fk():
    s = [AVISO, AVISO_FK]
    for t in TABELAS:
        for nome_fk, cols, ref, ref_cols in t['fks']:
            s.append(f"ALTER TABLE {t['nome']} ADD CONSTRAINT {nome_fk}"
                     f" FOREIGN KEY ({', '.join(cols)})"
                     f" REFERENCES {ref} ({', '.join(ref_cols)});")
    return '\n'.join(s) + '\n'


ARQUIVOS = {
    'mysql/modelo_mysql.sql': gerar_mysql,
    'mysql/modelo_mysql_fk.sql': gerar_mysql_fk,
    'postgresql/modelo_postgres.sql': gerar_postgres,
    'postgresql/modelo_postgres_fk.sql': gerar_postgres_fk,
    'sqlite/modelo_sqlite.sql': gerar_sqlite,
    'sqlserver/modelo_sqlserver.sql': gerar_sqlserver,
    'sqlserver/modelo_sqlserver_fk.sql': gerar_sqlserver_fk,
    'oracle/modelo_oracle.sql': gerar_oracle,
    'oracle/modelo_oracle_fk.sql': gerar_oracle_fk,
}


def main():
    for caminho, funcao in ARQUIVOS.items():
        destino = PASTA / caminho
        destino.parent.mkdir(parents=True, exist_ok=True)
        destino.write_text(funcao(), encoding='utf-8')
        print(f'gerado: {destino.relative_to(PASTA)}')


if __name__ == '__main__':
    main()
