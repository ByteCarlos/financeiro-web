<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "parcela".
 *
 * @property integer $id
 * @property integer $contrato_fk
 * @property string $descricao
 * @property string $valor
 * @property integer $ordem
 * @property string $data
 * @property string $fonte_pagadora
 *
 * @property Contrato $contratoFk
 * @property Receita[] $receitas
 */
class Parcela extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parcela';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contrato_fk', 'descricao', 'valor', 'ordem', 'data', 'fonte_pagadora'], 'required'],
            [['contrato_fk', 'ordem'], 'integer'],
            [['valor'], 'number'],
            [['data'], 'safe'],
            [['descricao'], 'string', 'max' => 200],
            [['fonte_pagadora'], 'string', 'max' => 100],
            [['contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contrato_fk' => 'Contrato Fk',
            'descricao' => 'Descricao',
            'valor' => 'Valor',
            'ordem' => 'Ordem',
            'data' => 'Data',
            'fonte_pagadora' => 'Fonte Pagadora',
        ];
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
    public function getReceitas()
    {
        return $this->hasMany(Receita::className(), ['parcela_fk' => 'id']);
    }
}
