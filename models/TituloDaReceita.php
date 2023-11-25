<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "titulo_da_receita".
 *
 * @property int $id
 * @property string $nome
 *
 * @property Receita[] $receitas
 */
class TituloDaReceita extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'titulo_da_receita';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
        ];
    }

    /**
     * Gets query for [[Receitas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceitas()
    {
        return $this->hasMany(Receita::className(), ['titulo_da_receita_fk' => 'id']);
    }
}
