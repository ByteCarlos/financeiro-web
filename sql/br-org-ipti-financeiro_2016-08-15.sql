-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `br.org.ipti.financeiro`;
CREATE DATABASE `br.org.ipti.financeiro` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `br.org.ipti.financeiro`;

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `categoria_pai_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_cat_fk` (`categoria_pai_fk`),
  CONSTRAINT `cat_cat_fk` FOREIGN KEY (`categoria_pai_fk`) REFERENCES `categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `categoria`;
INSERT INTO `categoria` (`id`, `nome`, `categoria_pai_fk`) VALUES
(1,	'Outras Despesas',	NULL),
(2,	'Recursos Humanos',	NULL),
(3,	'Combustíveis',	NULL);

DROP TABLE IF EXISTS `centro_de_custo`;
CREATE TABLE `centro_de_custo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contrato_fk` int(11) NOT NULL,
  `data_inicial` date NOT NULL,
  `data_final` date NOT NULL,
  `ordem` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cc_cont_fk` (`contrato_fk`),
  CONSTRAINT `cc_cont_fk` FOREIGN KEY (`contrato_fk`) REFERENCES `contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `centro_de_custo`;
INSERT INTO `centro_de_custo` (`id`, `contrato_fk`, `data_inicial`, `data_final`, `ordem`) VALUES
(1,	2,	'2015-12-14',	'2016-12-14',	1),
(2,	3,	'2015-12-29',	'2016-12-29',	1),
(3,	1,	'2015-01-01',	'2050-01-01',	1);

DROP TABLE IF EXISTS `conta_bancaria`;
CREATE TABLE `conta_bancaria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agencia` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `conta` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `proprietario` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `banco` enum('Banco do Brasil','Banese','Caixa Econômica Federal','Itaú') COLLATE utf8_unicode_ci NOT NULL,
  `cnpj` varchar(18) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `conta_bancaria`;
INSERT INTO `conta_bancaria` (`id`, `agencia`, `conta`, `proprietario`, `banco`, `cnpj`) VALUES
(1,	'4300-1',	'22.510-x',	'Instituto de Pesquisas em Tecnologia e Inovação',	'Banco do Brasil',	'05.929.852/0001-81');

DROP TABLE IF EXISTS `contrato`;
CREATE TABLE `contrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `projeto_fk` int(11) DEFAULT NULL,
  `parcelas` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cont_proj_fk` (`projeto_fk`),
  CONSTRAINT `cont_proj_fk` FOREIGN KEY (`projeto_fk`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `contrato`;
INSERT INTO `contrato` (`id`, `nome`, `projeto_fk`, `parcelas`) VALUES
(1,	'IPTI',	NULL,	NULL),
(2,	'Telefônica - Fase 3',	2,	1),
(3,	'Itaú - Synapse 3ª Série',	1,	1);

DROP TABLE IF EXISTS `contrato_contas_bancarias`;
CREATE TABLE `contrato_contas_bancarias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contrato_fk` int(11) NOT NULL,
  `conta_bancaria_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contcb_unique` (`contrato_fk`,`conta_bancaria_fk`),
  KEY `contcb_cb_fk` (`conta_bancaria_fk`),
  CONSTRAINT `contcb_cb_fk` FOREIGN KEY (`conta_bancaria_fk`) REFERENCES `conta_bancaria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contcb_cont_fk` FOREIGN KEY (`contrato_fk`) REFERENCES `contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `contrato_contas_bancarias`;
INSERT INTO `contrato_contas_bancarias` (`id`, `contrato_fk`, `conta_bancaria_fk`) VALUES
(1,	2,	1),
(2,	3,	1);

DROP TABLE IF EXISTS `contrato_fontes_pagadoras`;
CREATE TABLE `contrato_fontes_pagadoras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contrato_fk` int(11) NOT NULL,
  `fonte_pagadora_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contfp_unique` (`contrato_fk`,`fonte_pagadora_fk`),
  KEY `contfp_fp_fk` (`fonte_pagadora_fk`),
  CONSTRAINT `contfp_cont_fk` FOREIGN KEY (`contrato_fk`) REFERENCES `contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contfp_fp_fk` FOREIGN KEY (`fonte_pagadora_fk`) REFERENCES `fonte_pagadora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `contrato_fontes_pagadoras`;
INSERT INTO `contrato_fontes_pagadoras` (`id`, `contrato_fk`, `fonte_pagadora_fk`) VALUES
(3,	2,	1),
(4,	3,	2);

DROP TABLE IF EXISTS `despesa`;
CREATE TABLE `despesa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `descricao` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `fornecedor_fk` int(11) NOT NULL,
  `rubrica_fk` int(11) NOT NULL,
  `item_fk` int(11) NOT NULL,
  `valor` float NOT NULL,
  `observacao` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nota_fiscal` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `desp_forn_fk` (`fornecedor_fk`),
  KEY `desp_rub_fk` (`rubrica_fk`),
  KEY `desp_it_fk` (`item_fk`),
  CONSTRAINT `desp_forn_fk` FOREIGN KEY (`fornecedor_fk`) REFERENCES `fornecedor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `desp_it_fk` FOREIGN KEY (`item_fk`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `desp_rub_fk` FOREIGN KEY (`rubrica_fk`) REFERENCES `rubrica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `despesa`;
INSERT INTO `despesa` (`id`, `data`, `descricao`, `fornecedor_fk`, `rubrica_fk`, `item_fk`, `valor`, `observacao`, `nota_fiscal`) VALUES
(3,	'2016-08-08',	'aaa',	6,	10,	15,	803.52,	NULL,	NULL),
(4,	'2016-08-08',	'adsas',	6,	10,	15,	259.2,	NULL,	NULL),
(5,	'2016-08-08',	'ffa',	6,	10,	15,	9983.76,	NULL,	NULL),
(15,	'2016-08-08',	'dasda',	9,	13,	17,	1231.23,	NULL,	NULL),
(16,	'2016-08-08',	'asasa',	3,	17,	9,	500,	NULL,	NULL),
(19,	'2016-08-10',	'asdas',	8,	12,	16,	132.13,	NULL,	NULL),
(20,	'2016-08-17',	'asdad',	11,	14,	5,	1500,	NULL,	NULL);

DROP TABLE IF EXISTS `fonte_pagadora`;
CREATE TABLE `fonte_pagadora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `fonte_pagadora`;
INSERT INTO `fonte_pagadora` (`id`, `nome`) VALUES
(1,	'Telefônica'),
(2,	'Itaú');

DROP TABLE IF EXISTS `fornecedor`;
CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cnpj` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forn_unique_cnpj` (`cnpj`),
  UNIQUE KEY `forn_unique_cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `fornecedor`;
INSERT INTO `fornecedor` (`id`, `nome`, `cnpj`, `cpf`) VALUES
(1,	'Saulo Faria Almeida Barreto',	NULL,	'235.730.905-91'),
(2,	'Fábio Henrique dos Santos',	NULL,	'062.460.995-21'),
(3,	'IPTI',	'05.929.852/0001-81',	NULL),
(4,	'Rafael de Oliveira Teles - ME',	'24.011.975/0001-00',	NULL),
(5,	'Renata Piazzalunga',	'10.871.574/0001-99',	NULL),
(6,	'Fábio Theoto Rocha',	'11.682.421/0001-66',	NULL),
(7,	'Marília Gonçalves da Rocha',	'24.255.115/0001-95',	NULL),
(8,	'Wallison Hipólito de Meira',	NULL,	'047.337.985-63'),
(9,	'Ana Letícia Carvalho',	NULL,	'027.013.955-93'),
(10,	'Francisco Mota Cabral Filho',	NULL,	'047.467.705-22'),
(11,	'Bruno Alves Reis Nascimento',	NULL,	'055.725.775-10'),
(12,	'Ítalo Rafael de Almeida Pedral',	NULL,	'036.779.885-94'),
(13,	'Saulo Faria Almeida Barreto EPP',	'13.134.131/0001-03',	NULL),
(14,	'Silveira & Figueiredo Viagens e Turismo Ltda',	'03.140.579.0003-48',	NULL),
(15,	'Porto Seguro Cia de Seguros Gerais',	'61.198.164/0001-60',	NULL),
(16,	'Celera Tecnologia em Equipamentos Médicos Ltda ME',	'08.477.694/0001-64',	NULL),
(17,	'Ministério da Fazenda',	'00.394.460/0058-87',	NULL),
(18,	'Ministério da Previdência',	'00.394.528/0001-92',	NULL),
(19,	'Município de Santa Luzia do Itanhy',	'13.098.942/0001-04',	NULL);

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `categoria_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `it_cat_fk` (`categoria_fk`),
  CONSTRAINT `it_cat_fk` FOREIGN KEY (`categoria_fk`) REFERENCES `categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `item`;
INSERT INTO `item` (`id`, `nome`, `categoria_fk`) VALUES
(2,	'Gasolina',	3),
(3,	'Óleo Diesel',	3),
(4,	'Álcool',	3),
(5,	'Programação',	2),
(6,	'Apoio Técnico',	2),
(7,	'Serviço Técnico Especializado',	2),
(8,	'Qualificação Profissional',	2),
(9,	'Passagens Aéreas',	1),
(10,	'Itens Operacionais',	1),
(11,	'Itens Administrativos',	1),
(12,	'Gestão de Projetos',	2),
(13,	'Apoio Administrativo',	2),
(15,	'Coordenação de Projetos',	2),
(16,	'Gestão de Campo',	2),
(17,	'Ilustração',	2),
(18,	'Tarifas',	1),
(19,	'Consumíveis',	1);

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `migration`;
INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base',	1468936758),
('m160719_125555_inicializacao',	1470331707);

DROP TABLE IF EXISTS `projeto`;
CREATE TABLE `projeto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `projeto`;
INSERT INTO `projeto` (`id`, `nome`) VALUES
(1,	'Synapse'),
(2,	'CLOC');

DROP TABLE IF EXISTS `receita`;
CREATE TABLE `receita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `descricao` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `fonte_pagadora_fk` int(11) NOT NULL,
  `tipo_de_receita_fk` int(11) NOT NULL,
  `centro_de_custo_fk` int(11) NOT NULL,
  `valor` float NOT NULL,
  `observacao` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nota_fiscal` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rec_fp_fk` (`fonte_pagadora_fk`),
  KEY `rec_tdr_fk` (`tipo_de_receita_fk`),
  KEY `rec_cc_fk` (`centro_de_custo_fk`),
  CONSTRAINT `rec_cc_fk` FOREIGN KEY (`centro_de_custo_fk`) REFERENCES `centro_de_custo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rec_fp_fk` FOREIGN KEY (`fonte_pagadora_fk`) REFERENCES `fonte_pagadora` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rec_tdr_fk` FOREIGN KEY (`tipo_de_receita_fk`) REFERENCES `tipo_de_receita` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `receita`;
INSERT INTO `receita` (`id`, `data`, `descricao`, `fonte_pagadora_fk`, `tipo_de_receita_fk`, `centro_de_custo_fk`, `valor`, `observacao`, `nota_fiscal`) VALUES
(3,	'2016-08-08',	'saasaas',	1,	1,	1,	350000,	NULL,	NULL),
(7,	'2016-08-09',	'asdaf',	2,	1,	2,	500,	NULL,	NULL),
(8,	'2016-08-08',	'sdas',	2,	1,	2,	150,	NULL,	NULL);

DROP TABLE IF EXISTS `rubrica`;
CREATE TABLE `rubrica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_fk` int(11) NOT NULL,
  `item_fk` int(11) DEFAULT NULL,
  `centro_de_custo_fk` int(11) NOT NULL,
  `descricao` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `unidade` enum('Mês','Hora','Serviço','Unidade','Verba','Produto') COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `ocorrencia` int(11) DEFAULT NULL,
  `fixo` tinyint(4) DEFAULT NULL,
  `valor_unitario` float DEFAULT NULL,
  `encargos` float DEFAULT NULL,
  `valor_total` float NOT NULL,
  `tipo_de_contrato_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rub_cat_fk` (`categoria_fk`),
  KEY `rub_it_fk` (`item_fk`),
  KEY `rub_cc_fk` (`centro_de_custo_fk`),
  KEY `rub_tdc_fk` (`tipo_de_contrato_fk`),
  CONSTRAINT `rub_cat_fk` FOREIGN KEY (`categoria_fk`) REFERENCES `categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rub_cc_fk` FOREIGN KEY (`centro_de_custo_fk`) REFERENCES `centro_de_custo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rub_it_fk` FOREIGN KEY (`item_fk`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rub_tdc_fk` FOREIGN KEY (`tipo_de_contrato_fk`) REFERENCES `tipo_de_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `rubrica`;
INSERT INTO `rubrica` (`id`, `categoria_fk`, `item_fk`, `centro_de_custo_fk`, `descricao`, `unidade`, `quantidade`, `ocorrencia`, `fixo`, `valor_unitario`, `encargos`, `valor_total`, `tipo_de_contrato_fk`) VALUES
(1,	2,	8,	1,	'Curso de qualificação profissional',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	28800,	4),
(2,	2,	7,	1,	'Serviço Técnico especializado - Tecnologia Social',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	36200,	4),
(3,	2,	6,	1,	'Apoio Técnico',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	12000,	3),
(4,	1,	9,	1,	'Despesas com deslocamento',	NULL,	NULL,	NULL,	0,	NULL,	NULL,	8000,	NULL),
(5,	1,	NULL,	1,	'Despesas Operacionais e Administrativas (15%)',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	15000,	4),
(6,	2,	12,	2,	'Gestor do Projeto',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	19200,	4),
(7,	2,	7,	2,	'Especialista em TS',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	68000,	4),
(8,	1,	10,	2,	'Equipamentos EEG',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	18000,	4),
(9,	2,	13,	2,	'Apoio Administrativo',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	38400,	4),
(10,	2,	15,	2,	'Coordenador do Projeto',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	69120,	4),
(11,	2,	7,	2,	'Especialista em design',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	21000,	4),
(12,	2,	16,	2,	'Gestor de Campo',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	33000,	2),
(13,	2,	17,	2,	'Bolsista em Ilustração',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	6000,	1),
(14,	2,	5,	2,	'Bolsista em Programação',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	18000,	1),
(15,	1,	19,	2,	'Material de consumo',	NULL,	NULL,	NULL,	0,	NULL,	NULL,	6780,	NULL),
(17,	1,	NULL,	2,	'Despesas Operacionais',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	52500,	4);

DROP TABLE IF EXISTS `rubrica_fornecedores`;
CREATE TABLE `rubrica_fornecedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rubrica_fk` int(11) NOT NULL,
  `fornecedor_fk` int(11) NOT NULL,
  `data_inicial` date DEFAULT NULL,
  `data_final` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rubforn_unique` (`rubrica_fk`,`fornecedor_fk`),
  KEY `rubforn_forn_fk` (`fornecedor_fk`),
  CONSTRAINT `rubforn_forn_fk` FOREIGN KEY (`fornecedor_fk`) REFERENCES `fornecedor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rubforn_rub_fk` FOREIGN KEY (`rubrica_fk`) REFERENCES `rubrica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `rubrica_fornecedores`;
INSERT INTO `rubrica_fornecedores` (`id`, `rubrica_fk`, `fornecedor_fk`, `data_inicial`, `data_final`) VALUES
(1,	2,	1,	NULL,	NULL),
(2,	3,	2,	NULL,	NULL),
(3,	5,	3,	NULL,	NULL),
(4,	6,	4,	NULL,	NULL),
(5,	7,	13,	NULL,	NULL),
(6,	9,	5,	NULL,	NULL),
(7,	10,	6,	NULL,	NULL),
(8,	11,	7,	NULL,	NULL),
(9,	12,	8,	NULL,	NULL),
(10,	13,	9,	NULL,	NULL),
(11,	14,	10,	'2016-03-01',	'2016-03-31'),
(12,	14,	11,	NULL,	NULL),
(13,	14,	12,	NULL,	NULL),
(14,	17,	3,	NULL,	NULL);

DROP TABLE IF EXISTS `tipo_de_contrato`;
CREATE TABLE `tipo_de_contrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `tipo_de_contrato`;
INSERT INTO `tipo_de_contrato` (`id`, `nome`) VALUES
(1,	'Bolsa'),
(2,	'RPA'),
(3,	'Estágio'),
(4,	'Prestador de Serviço');

DROP TABLE IF EXISTS `tipo_de_receita`;
CREATE TABLE `tipo_de_receita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `tipo_de_receita`;
INSERT INTO `tipo_de_receita` (`id`, `nome`) VALUES
(1,	'Desembolso'),
(2,	'Estorno'),
(3,	'Doação');

-- 2016-08-15 18:51:44
