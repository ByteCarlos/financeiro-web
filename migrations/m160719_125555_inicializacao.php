<?php

use yii\db\Migration;

class m160719_125555_inicializacao extends Migration
{
    public function up()
    {
        $this->createTable('conta_bancaria',[
            'id' => 'pk',
            'agencia' => 'varchar(10) NOT NULL',
            'conta' => 'varchar(15) NOT NULL',
            'proprietario' => 'varchar(100) NOT NULL',
            'banco' => 'enum("Banco do Brasil","Banese","Caixa Econômica Federal","Itaú","Bradesco","Santander","HSBC","BNB","Citibank","Bicbanco","Banco Cooperativo do Brasil S/A") NOT NULL'
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createTable('tipo_de_contrato',[
            'id' => 'pk',
            'nome' => 'varchar(100) NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->batchInsert('tipo_de_contrato', ['id', 'nome'], [['1', 'Bolsa'], ['2', 'RPA'], ['3', 'Estágio'], ['4', 'Prestador de Serviço'], ['5', 'CLT']]);
        
        $this->createTable('fornecedor',[
            'id' => 'pk',
            'tipo_de_contrato_fk' => 'int NOT NULL',
            'nome' => 'varchar(100) NOT NULL',
            'cnpj' => 'varchar(18)',
            'cpf' => 'varchar(14)',
            'endereco' => 'varchar(200)',
            'representante_legal' => 'varchar(100)',
            'rg' => 'varchar(12)',
            'profissao' => 'varchar(100)',
            'email' => 'varchar(60)',
            'telefone' => 'varchar(20)',
            'pis' => 'varchar(14)',
            'conta_bancaria_fk' => 'int NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('forn_tdc_fk', 'fornecedor', 'tipo_de_contrato_fk', 'tipo_de_contrato', 'id', 'CASCADE','CASCADE');
        $this->addForeignKey('forn_tdc_fk', 'fornecedor', 'conta_bancaria_fk', 'conta_bancaria', 'id', 'SET NULL','CASCADE');

        $this->createTable('contrato',[
            'id' => 'pk',
            'nome' => 'varchar(100) NOT NULL',
            'conta_bancaria_fk' => 'int',
            'fonte_pagadora' => 'varchar(100) NULL',
            'apoiadora' => 'varchar(100) NULL',
            'origem_publica' => 'int NULL'
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('cont_cb_fk','contrato','conta_bancaria_fk','conta_bancaria','id','SET NULL','CASCADE');

        $this->createTable('parcela',[
            'id' => 'pk',
            'contrato_fk' => 'int not null',
            'descricao' => 'varchar(200) NOT NULL',
            'valor' => 'decimal(10,2) NOT NULL',
            'ordem' => 'int NOT NULL',
            'data' => 'date NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('par_cont_fk','parcela','contrato_fk','contrato','id','CASCADE','CASCADE');

        $this->createTable('centro_de_custo',[
            'id' => 'pk',
            'contrato_fk' => 'int NOT NULL',
            'data_inicial' => 'date NOT NULL',
            'data_final' => 'date NOT NULL',
            'ordem' => 'int NOT NULL',
            'celebracao_termo_aditivo' => 'date',
            'valor_total' => 'decimal(10,2) NOT NULL'
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('cc_cont_fk','centro_de_custo','contrato_fk','contrato','id','CASCADE','CASCADE');

        $this->batchInsert('centro_de_custo', ['id', 'contrato_fk', 'data_inicial', 'data_final', 'ordem'], [['1', '1', '2015-01-01', '2050-01-01', '1']]);

        $this->createTable('categoria',[
            'id' => 'pk',
            'nome' => 'varchar(100) NOT NULL',
            'pessoal' => 'int NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->batchInsert('categoria', ['id', 'nome', 'pessoal'], [['1', 'Outras Despesas', '0'], ['2', 'Recursos Humanos', '1'], ['3', 'Materiais de Consumo', '0'], ['4', 'Material Permanente', '0'], ['5', 'Despesas Operacionais e Administrativas', '0'], ['6', 'Deslocamentos', '0'], ['7', 'Prestadores de Serviços', '0'], ['8', 'Comunicação e Divulgação', '0'], ['9', 'Diárias', '0'], ['10', 'Passagens Aéreas', '0'], ['11', 'CMDCA', '0'], ['12', 'Encargos', '0']]);

        $this->createTable('tipo_de_contrato',[
            'id' => 'pk',
            'nome' => 'varchar(100) NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->batchInsert('tipo_de_contrato', ['id', 'nome'], [['1', 'Bolsa'], ['2', 'RPA'], ['3', 'Estágio'], ['4', 'Prestador de Serviço'], ['5', 'CLT']]);

        $this->createTable('rubrica',[
            'id' => 'pk',
            'categoria_fk' => 'int NOT NULL',
            'centro_de_custo_fk' => 'int NOT NULL',
            'descricao' => 'varchar(100) NOT NULL',
            'valor_total' => 'decimal(10,2) NOT NULL',
            'tipo_de_contrato_fk' => "int",
            'importado_projeto_id' => "int",
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('rub_cat_fk','rubrica','categoria_fk','categoria','id','CASCADE','CASCADE');
        $this->addForeignKey('rub_cc_fk','rubrica','centro_de_custo_fk','centro_de_custo','id','CASCADE','CASCADE');
        $this->addForeignKey('rub_tdc_fk','rubrica','tipo_de_contrato_fk','tipo_de_contrato','id','CASCADE','CASCADE');

        $this->createTable('rubrica_fornecedores',[
            'id' => 'pk',
            'rubrica_fk' => 'int NOT NULL',
            'fornecedor_fk' => 'int NOT NULL',
            'data_inicial' => 'date NOT NULL',
            'data_final' => 'date NOT NULL',
            'ordem' => 'int NOT NULL',
            'valor_total' => 'decimal(10,2) NOT NULL',
            'parcelas' => 'int NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('rubforn_rub_fk','rubrica_fornecedores','rubrica_fk','rubrica','id','CASCADE','CASCADE');
        $this->addForeignKey('rubforn_forn_fk','rubrica_fornecedores','fornecedor_fk','fornecedor','id','CASCADE','CASCADE');

        $this->createTable('atividade',[
            'id' => 'pk',
            'rubrica_fornecedores_fk' => 'int',
            'rubrica_fk' => 'int',
            'contrato_fk' => 'int',
            'descricao' => 'varchar(2000)',
            'valor' => 'decimal(10,2) NOT NULL',
            'ordem' => 'int NOT NULL',
            'data' => 'date NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('atv_rf_fk','atividade','rubrica_fornecedores_fk','rubrica_fornecedores','id','CASCADE','CASCADE');
        $this->addForeignKey('atv_rub_fk','atividade','rubrica_fk','rubrica','id','CASCADE','CASCADE');
        $this->addForeignKey('atv_con_fk','atividade','contrato_fk','contrato','id','CASCADE','CASCADE');

        $this->createTable('despesa',[
            'id' => 'pk',
            'data' => 'date NOT NULL',
            'descricao' => 'text NOT NULL',
            'fornecedor_fk' => 'int NOT NULL',
            'rubrica_fk' => 'int',
            'numero_transferencia_cheque' => 'varchar(50)',
            'custeio' => 'int NOT NULL',
            'centro_de_custo' => 'varchar(200)',
            'contrato_fk' => 'int'
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('desp_forn_fk','despesa','fornecedor_fk','fornecedor','id','CASCADE','CASCADE');
        $this->addForeignKey('desp_rub_fk','despesa','rubrica_fk','rubrica','id','CASCADE','CASCADE');
        $this->addForeignKey('desp_con_fk','despesa','contrato_fk','contrato','id','CASCADE','CASCADE');

        $this->createTable('despesa_atividades',[
            'id' => 'pk',
            'despesa_fk' => 'int NOT NULL',
            'atividade_fk' => 'int',
            'valor' => 'int NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('desp_atv_desp_fk','despesa_atividades','despesa_fk','despesa','id','CASCADE','CASCADE');
        $this->addForeignKey('desp_atv_atv_fk','despesa_atividades','atividade_fk','atividade','id','SET NULL','CASCADE');

        $this->createTable('tipo_de_receita', [
            'id' => 'pk',
            'nome' => 'varchar(100) NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->batchInsert('tipo_de_receita', ['id', 'nome'], [['1', 'Desembolso'], ['2', 'Estorno'], ['3', 'Doação']]);

        $this->createTable('receita',[
            'id' => 'pk',
            'data' => 'date NOT NULL',
            'descricao' => 'text NOT NULL',
            'fonte_pagadora' => 'varchar(100) NOT NULL',
	        'tipo_de_receita_fk' => 'int NOT NULL',
            'centro_de_custo_fk' => 'int',
            'valor' => 'decimal(10,2) NOT NULL',
            'parcela_fk' => 'int',
            'contrato_fk' => 'int',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

	    $this->addForeignKey('rec_tdr_fk','receita','tipo_de_receita_fk','tipo_de_receita','id','CASCADE','CASCADE');
        $this->addForeignKey('rec_cc_fk','receita','centro_de_custo_fk','centro_de_custo','id','CASCADE','CASCADE');
        $this->addForeignKey('rec_par_fk','receita','parcela_fk','parcela','id','CASCADE','CASCADE');
        $this->addForeignKey('rec_con_fk','receita','contrato_fk','contrato','id','CASCADE','CASCADE');

        $this->createTable('taxa',[
            'id' => 'pk',
            'data' => 'date NOT NULL',
            'descricao' => 'varchar(200) NOT NULL',
            'fornecedor_fk' => 'int NOT NULL',
            'contrato_fk' => 'int NOT NULL',
            'valor' => 'decimal(10,2) NOT NULL',
            'tipo' => 'char(1) NOT NULL',
            'taxa' => 'enum("Juros de Poupança","Tarifa") NOT NULL',
        ],  'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addForeignKey('t_forn_fk','taxa','fornecedor_fk','fornecedor','id','CASCADE','CASCADE');
        $this->addForeignKey('t_cont_fk','taxa','contrato_fk','contrato','id','CASCADE','CASCADE');

        $this->createTable('usuario', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(200)->notNull(),
            'email' => $this->string(100)->notNull()->unique(),
            'senha' => $this->string(32)->notNull(),
            'admin' => $this->integer()->notNull(),
            'assessor' => $this->integer()->notNull(),
            'ativo' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
        
        $this->batchInsert("usuario", ['nome', 'email', 'senha', 'administrador', 'gerente', 'ativo'], [
            ['Nívea Motta Marchi', 'nivea@ipti.org.br', '71ad5a357f47cfc4104d5617fbefed1f', '1', '0', '1'],
            ['Paulo Roberto da Costa Cardoso', 'paulones89@gmail.com', '71ad5a357f47cfc4104d5617fbefed1f', '1', '0', '1']
        ]);
        
        $this->createTable('recuperar_senha', [
            'email' => \yii\db\mysql\Schema::TYPE_STRING . '(100) NOT NULL PRIMARY KEY',
            'codigo' => $this->string(32)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('recuperar_senha_usuario_fkey', 'recuperar_senha', 'email', 'usuario', 'email', 'CASCADE', 'CASCADE');

        $this->createTable('sessao', [
            'id' => $this->primaryKey(),
            'usuario_fk' => $this->integer()->notNull(),
            'codigo' => $this->string(32)->notNull()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('sessao_usuario_fkey', 'sessao', 'usuario_fk', 'usuario', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('coordenador', [
            'id' => $this->primaryKey(),
            'usuario_fk' => $this->integer()->notNull(),
            'contrato_fk' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('coordenador_usuario_fkey', 'coordenador', 'usuario_fk', 'usuario', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('coordenador_contrato_fkey', 'coordenador', 'contrato_fk', 'contrato', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('midia', [
            'id' => $this->primaryKey(),
            'contrato_fk' => $this->integer()->notNull(),
            'link' => $this->string(1000)->notNull(),
            'nome' => $this->string(500)->notNull(),
            'tipo' => $this->string(200)->notNull(),
            'tamanho' => $this->integer()->notNull(),
            'nome_falso' => $this->string(500)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('midia_contrato_fkey', 'midia', 'contrato_fk', 'contrato', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('coordenador');
        $this->dropTable('sessao');
        $this->dropTable('recuperar_senha');
        $this->dropTable('usuario');
        $this->dropTable('taxa');
        $this->dropTable('receita');
	    $this->dropTable('tipo_de_receita');
        $this->dropTable('despesa');
        $this->dropTable('atividade');
        $this->dropTable('rubrica_fornecedores');
        $this->dropTable('rubrica');
        $this->dropTable('categoria');
        $this->dropTable('centro_de_custo');
        $this->dropTable('parcela');
        $this->dropTable('contrato');
        $this->dropTable('fornecedor');
        $this->dropTable('tipo_de_contrato');
        $this->dropTable('conta_bancaria');

        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
