<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_de_contrato".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property Fornecedor[] $fornecedors
 * @property Rubrica[] $rubricas
 */
class TipoDeContrato extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_de_contrato';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 100],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFornecedors()
    {
        return $this->hasMany(Fornecedor::className(), ['tipo_de_contrato_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricas()
    {
        return $this->hasMany(Rubrica::className(), ['tipo_de_contrato_fk' => 'id']);
    }
}
