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
(1,	'Recurso Humano',	NULL),
(2,	'TI',	1),
(3,	'Combustível',	NULL),
(4,	'Design',	1),
(5,	'Tarifa',	NULL);

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
(1,	2,	'2016-01-01',	'2016-12-31',	1),
(2,	2,	'2017-01-01',	'2017-03-31',	2),
(4,	1,	'2000-01-01',	'2100-01-01',	1);

DROP TABLE IF EXISTS `conta_bancaria`;
CREATE TABLE `conta_bancaria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agencia` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `conta` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `proprietario` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `conta_bancaria`;
INSERT INTO `conta_bancaria` (`id`, `agencia`, `conta`, `proprietario`) VALUES
(1,	'3546-7',	'34.150-9',	'Instituto de Pesquisas em Tecnologia e Inovação'),
(2,	'058',	'01/011543-0',	'Instituto de Pesquisas em Tecnologia e Inovação');

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
(2,	'CG-075',	2,	3);

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
(2,	2,	2);

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
  `nota_fiscal` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `desp_forn_fk` (`fornecedor_fk`),
  KEY `desp_rub_fk` (`rubrica_fk`),
  KEY `item_fk` (`item_fk`),
  CONSTRAINT `desp_forn_fk` FOREIGN KEY (`fornecedor_fk`) REFERENCES `fornecedor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `desp_rub_fk` FOREIGN KEY (`rubrica_fk`) REFERENCES `rubrica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `despesa_ibfk_1` FOREIGN KEY (`item_fk`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `despesa`;
INSERT INTO `despesa` (`id`, `data`, `descricao`, `fornecedor_fk`, `rubrica_fk`, `item_fk`, `valor`, `observacao`, `nota_fiscal`) VALUES
(1,	'2016-03-01',	'Despesa teste',	1,	3,	2,	30,	NULL,	NULL),
(2,	'2017-01-06',	'Despesa teste aditivo',	2,	4,	1,	40,	NULL,	NULL),
(3,	'2016-07-06',	'Despesa meio',	2,	3,	2,	400,	'hehe',	123),
(4,	'2016-07-06',	'anarrie',	3,	1,	3,	3000,	'ènois',	1),
(5,	'2016-07-07',	'despesa gasosa',	2,	3,	7,	100,	NULL,	NULL);

DROP TABLE IF EXISTS `fonte_pagadora`;
CREATE TABLE `fonte_pagadora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `fonte_pagadora`;
INSERT INTO `fonte_pagadora` (`id`, `nome`) VALUES
(1,	'SEED'),
(2,	'SEDETEC');

DROP TABLE IF EXISTS `fornecedor`;
CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `fornecedor`;
INSERT INTO `fornecedor` (`id`, `nome`) VALUES
(1,	'Ultragás'),
(2,	'Indaiá'),
(3,	'PPCC Comércio e Representações'),
(4,	'AssineWEB');

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
(1,	'Óleo Diesel',	3),
(2,	'Gasolina',	3),
(3,	'Programador Júnior',	2),
(4,	'Programador Sênior',	2),
(5,	'Design Júnior',	4),
(6,	'Design Sênior',	4),
(7,	'Tarifa Bancária',	5);

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

TRUNCATE `migration`;
INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base',	1468936758),
('m160719_125555_inicializacao',	1469109959);

DROP TABLE IF EXISTS `projeto`;
CREATE TABLE `projeto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `projeto`;
INSERT INTO `projeto` (`id`, `nome`) VALUES
(1,	'TAG'),
(2,	'Synapse'),
(3,	'Guigoh'),
(4,	'HB'),
(5,	'CLOC');

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
  `nota_fiscal` bigint(20) DEFAULT NULL,
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
(2,	'2016-03-03',	'Grana da SEED',	1,	2,	1,	3000,	'Ae man',	123),
(37,	'2016-07-06',	'asdasf',	1,	1,	1,	123.5,	'asd',	123),
(38,	'2017-02-02',	'teste',	2,	2,	2,	390,	NULL,	NULL),
(39,	'2016-08-04',	'Receitão do robsão',	2,	3,	1,	70,	'mama mia',	123);

DROP TABLE IF EXISTS `rubrica`;
CREATE TABLE `rubrica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_fk` int(11) DEFAULT NULL,
  `centro_de_custo_fk` int(11) NOT NULL,
  `descricao` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `unidade` enum('Mês','Hora','Serviço','Unidade','Verba','Produto') COLLATE utf8_unicode_ci NOT NULL,
  `quantidade` int(11) NOT NULL,
  `ocorrencia` int(11) NOT NULL,
  `fixo` tinyint(4) NOT NULL,
  `valor_unitario` float NOT NULL,
  `encargos` float NOT NULL,
  `valor_total` float NOT NULL,
  `tipo_de_contrato_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rub_it_fk` (`item_fk`),
  KEY `rub_cc_fk` (`centro_de_custo_fk`),
  KEY `tipo_de_contrato_fk` (`tipo_de_contrato_fk`),
  CONSTRAINT `rub_cc_fk` FOREIGN KEY (`centro_de_custo_fk`) REFERENCES `centro_de_custo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rub_it_fk` FOREIGN KEY (`item_fk`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rubrica_ibfk_1` FOREIGN KEY (`tipo_de_contrato_fk`) REFERENCES `tipo_de_contrato` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `rubrica`;
INSERT INTO `rubrica` (`id`, `item_fk`, `centro_de_custo_fk`, `descricao`, `unidade`, `quantidade`, `ocorrencia`, `fixo`, `valor_unitario`, `encargos`, `valor_total`, `tipo_de_contrato_fk`) VALUES
(1,	3,	1,	'Programador Java ',	'Produto',	12,	1,	1,	3000,	1.2,	43200,	3),
(2,	5,	1,	'Design Front-End',	'Produto',	12,	1,	1,	3000,	1,	36000,	3),
(3,	NULL,	1,	'Combustível para viagem à carro',	'Mês',	12,	1,	0,	1500,	1,	18000,	NULL),
(4,	NULL,	2,	'Combustível para viagem à carro',	'Mês',	3,	1,	0,	1500,	1,	4500,	NULL);

DROP TABLE IF EXISTS `rubrica_fornecedores`;
CREATE TABLE `rubrica_fornecedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rubrica_fk` int(11) NOT NULL,
  `fornecedor_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rubforn_unique` (`rubrica_fk`,`fornecedor_fk`),
  KEY `rubforn_forn_fk` (`fornecedor_fk`),
  CONSTRAINT `rubforn_forn_fk` FOREIGN KEY (`fornecedor_fk`) REFERENCES `fornecedor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rubforn_rub_fk` FOREIGN KEY (`rubrica_fk`) REFERENCES `rubrica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `rubrica_fornecedores`;
INSERT INTO `rubrica_fornecedores` (`id`, `rubrica_fk`, `fornecedor_fk`) VALUES
(1,	1,	3),
(2,	1,	4),
(3,	3,	1),
(5,	4,	1),
(4,	4,	2);

DROP TABLE IF EXISTS `rubrica_itens`;
CREATE TABLE `rubrica_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rubrica_fk` int(11) NOT NULL,
  `item_fk` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rubit_unique` (`rubrica_fk`,`item_fk`),
  KEY `rubit_it_fk` (`item_fk`),
  CONSTRAINT `rubit_it_fk` FOREIGN KEY (`item_fk`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rubit_rub_fk` FOREIGN KEY (`rubrica_fk`) REFERENCES `rubrica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `rubrica_itens`;
INSERT INTO `rubrica_itens` (`id`, `rubrica_fk`, `item_fk`) VALUES
(1,	3,	1),
(2,	3,	2);

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
(3,	'PJ');

DROP TABLE IF EXISTS `tipo_de_receita`;
CREATE TABLE `tipo_de_receita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE `tipo_de_receita`;
INSERT INTO `tipo_de_receita` (`id`, `nome`) VALUES
(1,	'Estorno'),
(2,	'Desembolso'),
(3,	'Doação');

-- 2016-08-02 12:32:01
