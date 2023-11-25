<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receita".
 *
 * @property int $id
 * @property string $data
 * @property string $descricao
 * @property string $fonte_pagadora
 * @property int $tipo_de_receita_fk
 * @property int $tipo_de_despesa_fk
 * @property int $titulo_da_receita_fk
 * @property int $rubrica_fk
 * @property int|null $centro_de_custo_fk
 * @property float $valor
 * @property int|null $parcela_fk
 * @property int|null $contrato_fk
 *
 * @property CentroDeCusto $centroDeCustoFk
 * @property TipoDeReceita $tipoDeReceitaFk
 * @property TipoDeDespesa $tipoDeDespesaFk
 * @property Parcela $parcelaFk
 * @property Contrato $contratoFk
 * @property Rubrica $rubricaFk
 * @property TituloDaReceita $tituloDaReceitaFk
 */
class Receita extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receita';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data', 'descricao', 'fonte_pagadora', 'tipo_de_receita_fk', 'titulo_da_receita_fk', 'valor'], 'required'],
            [['data'], 'safe'],
            [['descricao'], 'string'],
            [['tipo_de_receita_fk', 'titulo_da_receita_fk', 'centro_de_custo_fk', 'parcela_fk', 'contrato_fk'], 'integer'],
            [['valor'], 'number'],
            [['fonte_pagadora'], 'string', 'max' => 100],
            [['centro_de_custo_fk'], 'exist', 'skipOnError' => true, 'targetClass' => CentroDeCusto::className(), 'targetAttribute' => ['centro_de_custo_fk' => 'id']],
            [['tipo_de_receita_fk'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDeReceita::className(), 'targetAttribute' => ['tipo_de_receita_fk' => 'id']],
            [['tipo_de_despesa_fk'], 'string'],
            [['parcela_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Parcela::className(), 'targetAttribute' => ['parcela_fk' => 'id']],
            [['contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_fk' => 'id']],
            [['titulo_da_receita_fk'], 'exist', 'skipOnError' => true, 'targetClass' => TituloDaReceita::className(), 'targetAttribute' => ['titulo_da_receita_fk' => 'id']],
            [['rubrica_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Rubrica::className(), 'targetAttribute' => ['rubrica_fk' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'descricao' => 'Descricao',
            'fonte_pagadora' => 'Fonte Pagadora',
            'tipo_de_receita_fk' => 'Tipo De Receita Fk',
            'tipo_de_despesa_fk' => 'Tipo De Despesa Fk',
            'titulo_da_receita_fk' => 'Titulo Da Receita Fk',
            'centro_de_custo_fk' => 'Centro De Custo Fk',
            'valor' => 'Valor',
            'parcela_fk' => 'Parcela Fk',
            'contrato_fk' => 'Contrato Fk',
            'rubrica_fk' => 'Rubrica Fk'
        ];
    }

    /**
     * Gets query for [[CentroDeCustoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCentroDeCustoFk()
    {
        return $this->hasOne(CentroDeCusto::className(), ['id' => 'centro_de_custo_fk']);
    }

    /**
     * Gets query for [[TipoDeReceitaFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDeReceitaFk()
    {
        return $this->hasOne(TipoDeReceita::className(), ['id' => 'tipo_de_receita_fk']);
    }

    /**
     * Gets query for [[ParcelaFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParcelaFk()
    {
        return $this->hasOne(Parcela::className(), ['id' => 'parcela_fk']);
    }

    /**
     * Gets query for [[ContratoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoFk()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_fk']);
    }

    /**
     * Gets query for [[TituloDaReceitaFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTituloDaReceitaFk()
    {
        return $this->hasOne(TituloDaReceita::className(), ['id' => 'titulo_da_receita_fk']);
    }

    /**
     * Gets query for [[RubricaFk]]
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFk()
    {
        return $this->hasOne(Rubrica::className(), ['id' => 'rubrica_fk']);
    }
}
