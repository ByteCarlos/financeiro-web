<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contrato".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $conta_bancaria_fk
 * @property string $apoiadora
 * @property integer $origem_publica
 * @property string $data_inicial
 * @property string $data_final
 *
 * @property Atividade[] $atividades
 * @property CentroDeCusto[] $centroDeCustos
 * @property ContaBancaria $contaBancariaFk
 * @property Coordenador[] $coordenadors
 * @property Despesa[] $despesas
 * @property Midia[] $midias
 * @property Parcela[] $parcelas
 * @property Receita[] $receitas
 * @property Taxa[] $taxas
 */
class Contrato extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contrato';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['conta_bancaria_fk', 'origem_publica'], 'integer'],
            [['data_inicial', 'data_final'], 'safe'],
            [['nome', 'apoiadora'], 'string', 'max' => 100],
            [['conta_bancaria_fk'], 'exist', 'skipOnError' => true, 'targetClass' => ContaBancaria::className(), 'targetAttribute' => ['conta_bancaria_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'conta_bancaria_fk' => 'Conta Bancaria Fk',
            'apoiadora' => 'Apoiadora',
            'origem_publica' => 'Origem Publica',
            'data_inicial' => 'Data Inicial',
            'data_final' => 'Data Final',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtividades()
    {
        return $this->hasMany(Atividade::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCentroDeCustos()
    {
        return $this->hasMany(CentroDeCusto::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContaBancariaFk()
    {
        return $this->hasOne(ContaBancaria::className(), ['id' => 'conta_bancaria_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoordenadors()
    {
        return $this->hasMany(Coordenador::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespesas()
    {
        return $this->hasMany(Despesa::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMidias()
    {
        return $this->hasMany(Midia::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParcelas()
    {
        return $this->hasMany(Parcela::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceitas()
    {
        return $this->hasMany(Receita::className(), ['contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxas()
    {
        return $this->hasMany(Taxa::className(), ['contrato_fk' => 'id']);
    }
}
