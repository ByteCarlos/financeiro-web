<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "atividade".
 *
 * @property integer $id
 * @property integer $rubrica_fornecedores_fk
 * @property integer $rubrica_fk
 * @property integer $contrato_fk
 * @property string $descricao
 * @property string $valor
 * @property integer $ordem
 * @property string $data
 *
 * @property Rubrica $rubricaFk
 * @property Contrato $contratoFk
 * @property RubricaFornecedores $rubricaFornecedoresFk
 * @property DespesaAtividades[] $despesaAtividades
 */
class Atividade extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'atividade';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rubrica_fornecedores_fk', 'rubrica_fk', 'contrato_fk', 'ordem'], 'integer'],
            [['valor', 'ordem', 'data'], 'required'],
            [['valor'], 'number'],
            [['data'], 'safe'],
            [['descricao'], 'string', 'max' => 4000],
            [['rubrica_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Rubrica::className(), 'targetAttribute' => ['rubrica_fk' => 'id']],
            [['contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_fk' => 'id']],
            [['rubrica_fornecedores_fk'], 'exist', 'skipOnError' => true, 'targetClass' => RubricaFornecedores::className(), 'targetAttribute' => ['rubrica_fornecedores_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rubrica_fornecedores_fk' => 'Rubrica Fornecedores Fk',
            'rubrica_fk' => 'Rubrica Fk',
            'contrato_fk' => 'Contrato Fk',
            'descricao' => 'Descricao',
            'valor' => 'Valor',
            'ordem' => 'Ordem',
            'data' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFk()
    {
        return $this->hasOne(Rubrica::className(), ['id' => 'rubrica_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratoFk()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFornecedoresFk()
    {
        return $this->hasOne(RubricaFornecedores::className(), ['id' => 'rubrica_fornecedores_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespesaAtividades()
    {
        return $this->hasMany(DespesaAtividades::className(), ['atividade_fk' => 'id']);
    }
}
