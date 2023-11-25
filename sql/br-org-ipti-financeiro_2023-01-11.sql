-- `br.org.ipti.financeiro`.tipo_de_despesa definition

CREATE TABLE `tipo_de_despesa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb3;

ALTER TABLE `br.org.ipti.financeiro`.receita ADD tipo_de_despesa_fk varchar(100) NULL;

INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(1, 'Analista ADM/FIN PL');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(2, 'Auxiliar Administrativo');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(3, 'Auxiliar Biblioteca CVT');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(4, 'Coordenação ADM/FIN');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(5, 'Coordenação ADM/FIN de Projeto');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(6, 'Diretor Presidente');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(7, 'Encargos e Impostos');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(8, 'Estágio ADM/FIN 1');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(9, 'Estágio ADM/FIN 2');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(10, 'Seguro de Vida/Estagiários');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(11, 'Segurança patrimonial');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(12, 'Vale alimentação');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(13, 'Vale transporte');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(14, 'Água biblioteca');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(15, 'Água CVT');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(16, 'Água Escritório');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(17, 'Aluguel Escritório AJU');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(18, 'Condomínio escritório');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(19, 'Conservação e Limpeza CVT');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(20, 'Conservação e Limpeza sede');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(21, 'Conservação e Limpeza escritório');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(22, 'Container');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(23, 'Correios');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(24, 'Energia CVT');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(25, 'Energia elétrica escritório');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(26, 'Energia elétrica sede');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(27, 'Internet CVT');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(28, 'Internet sede');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(29, 'IPTU');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(30, 'Seguro equipamentos');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(31, 'Seguro imóvel');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(32, 'Telefonia móvel');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(33, 'Telefonia/internet escritório');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(34, 'Auditoria Externa');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(35, 'Auditoria Interna');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(36, 'Contabilidade');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(37, 'Doare');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(38, 'Exames demissionais/periódicos');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(39, 'LGPD - Consultoria');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(40, 'LGPD - DPOaaS');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(41, 'Segurança do trabalho');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(42, 'Cartão de Crédito');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(43, 'Cartório');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(44, 'Dívida Mtur');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(45, 'Captação Rede Synapse');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(46, 'Imprevistos');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(47, 'Eventos/reuniões');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(48, 'Impressão/encardenação');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(49, 'Licenciamento/IPVA');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(50, 'Combustível');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(51, 'Manutenção do Veículo');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(52, 'Seguro carros');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(53, 'Tarifas Bancárias');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(54, 'Material de Escritório/Papelaria/Limpeza');
INSERT INTO `br.org.ipti.financeiro`.tipo_de_despesa
(id, nome)
VALUES(55, 'Outros');

UPDATE `br.org.ipti.financeiro`.tipo_de_receita
SET nome='Recurso de Projeto'
WHERE id=1;