<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fonte".
 *
 * @property int $id
 * @property string $nome
 *
 * @property Rubrica[] $rubricas
 */
class Fonte extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fonte';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 100],
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
     * Gets query for [[Rubricas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRubricas()
    {
        return $this->hasMany(Rubrica::className(), ['fonte_fk' => 'id']);
    }
}
