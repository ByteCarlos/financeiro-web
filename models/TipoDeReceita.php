<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_de_receita".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property Receita[] $receitas
 */
class TipoDeReceita extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_de_receita';
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
    public function getReceitas()
    {
        return $this->hasMany(Receita::className(), ['tipo_de_receita_fk' => 'id']);
    }
}
