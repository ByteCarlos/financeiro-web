<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categoria".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $pessoal
 *
 * @property Rubrica[] $rubricas
 */
class Categoria extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'categoria';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'pessoal'], 'required'],
            [['pessoal'], 'integer'],
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
            'pessoal' => 'Pessoal',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricas()
    {
        return $this->hasMany(Rubrica::className(), ['categoria_fk' => 'id']);
    }
}
