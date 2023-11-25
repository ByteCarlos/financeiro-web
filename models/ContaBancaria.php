<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "conta_bancaria".
 *
 * @property int $id
 * @property string $agencia
 * @property string $tipo_de_conta
 * @property string $conta
 * @property string $proprietario
 * @property string|null $pix
 * @property int $banco_fk
 *
 * @property Bancos $bancoFk
 * @property Contrato[] $contratos
 * @property Fornecedor[] $fornecedors
 */
class ContaBancaria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conta_bancaria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agencia', 'tipo_de_conta', 'conta', 'proprietario', 'banco_fk'], 'required'],
            [['banco_fk'], 'integer'],
            [['agencia'], 'string', 'max' => 10],
            [['tipo_de_conta'], 'string', 'max' => 2],
            [['conta'], 'string', 'max' => 15],
            [['proprietario'], 'string', 'max' => 100],
            [['pix'], 'string', 'max' => 60],
            [['banco_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Bancos::className(), 'targetAttribute' => ['banco_fk' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agencia' => 'Agencia',
            'tipo_de_conta' => 'Tipo De Conta',
            'conta' => 'Conta',
            'proprietario' => 'Proprietario',
            'pix' => 'Pix',
            'banco_fk' => 'Banco Fk',
        ];
    }

    /**
     * Gets query for [[BancoFk]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBancoFk()
    {
        return $this->hasOne(Bancos::className(), ['id' => 'banco_fk']);
    }

    /**
     * Gets query for [[Contratos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['conta_bancaria_fk' => 'id']);
    }

    /**
     * Gets query for [[Fornecedors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFornecedors()
    {
        return $this->hasMany(Fornecedor::className(), ['conta_bancaria_fk' => 'id']);
    }
}
