<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_de_despesa".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property Despesa[] $despesas
 */
class TipoDeDespesa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_de_despesa';
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
}
