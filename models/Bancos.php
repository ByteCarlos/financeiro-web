<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bancos".
 *
 * @property int $id
 * @property string $codigo
 * @property string $cnpj
 * @property string $nome
 * @property string $nome_abreviado
 *
 * @property ContaBancaria[] $contaBancarias
 */
class Bancos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bancos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'cnpj', 'nome', 'nome_abreviado'], 'required'],
            [['codigo'], 'string', 'max' => 3],
            [['cnpj'], 'string', 'max' => 18],
            [['nome'], 'string', 'max' => 150],
            [['nome_abreviado'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'cnpj' => 'Cnpj',
            'nome' => 'Nome',
            'nome_abreviado' => 'Nome Abreviado',
        ];
    }

    /**
     * Gets query for [[ContaBancarias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContaBancarias()
    {
        return $this->hasMany(ContaBancaria::className(), ['banco_fk' => 'id']);
    }
}
