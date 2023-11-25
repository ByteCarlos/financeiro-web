<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fornecedor".
 *
 * @property integer $id
 * @property string $nome
 * @property string $cnpj
 * @property string $cpf
 * @property integer $tipo_de_contrato_fk
 * @property string $endereco
 * @property string $respresentante_legal
 * @property string $rg
 * @property string $profissao
 * @property string $email
 * @property string $telefone
 * @property string $pis
 * @property integer $conta_bancaria_fk
 *
 * @property Despesa[] $despesas
 * @property Despesa[] $despesas0
 * @property TipoDeContrato $tipoDeContratoFk
 * @property ContaBancaria $contaBancariaFk
 * @property RubricaFornecedores[] $rubricaFornecedores
 * @property Taxa[] $taxas
 */
class Fornecedor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fornecedor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'tipo_de_contrato_fk'], 'required'],
            [['tipo_de_contrato_fk', 'conta_bancaria_fk'], 'integer'],
            [['nome', 'respresentante_legal', 'profissao'], 'string', 'max' => 100],
            [['cnpj'], 'string', 'max' => 18],
            [['cpf', 'pis'], 'string', 'max' => 14],
            [['endereco'], 'string', 'max' => 200],
            [['rg'], 'string', 'max' => 12],
            [['email'], 'string', 'max' => 60],
            [['telefone'], 'string', 'max' => 20],
            [['tipo_de_contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDeContrato::className(), 'targetAttribute' => ['tipo_de_contrato_fk' => 'id']],
            [['conta_bancaria_fk'], 'exist', 'skipOnError' => true, 'targetClass' => ContaBancaria::className(), 'targetAttribute' => ['conta_bancaria_fk' => 'id']],
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
            'cnpj' => 'Cnpj',
            'cpf' => 'Cpf',
            'tipo_de_contrato_fk' => 'Tipo De Contrato Fk',
            'endereco' => 'Endereco',
            'respresentante_legal' => 'Respresentante Legal',
            'rg' => 'Rg',
            'profissao' => 'Profissao',
            'email' => 'Email',
            'telefone' => 'Telefone',
            'pis' => 'Pis',
            'conta_bancaria_fk' => 'Conta Bancaria Fk',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespesas()
    {
        return $this->hasMany(Despesa::className(), ['fornecedor_fk' => 'id']);
    }

    public function getDespesas0()
    {
        return $this->hasMany(Despesa::className(), ['favorecido_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDeContratoFk()
    {
        return $this->hasOne(TipoDeContrato::className(), ['id' => 'tipo_de_contrato_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContaBancariaFk()
    {
        return $this->hasOne(ContaBancaria::className(), ['id' => 'conta_bancaria_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFornecedores()
    {
        return $this->hasMany(RubricaFornecedores::className(), ['fornecedor_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxas()
    {
        return $this->hasMany(Taxa::className(), ['fornecedor_fk' => 'id']);
    }
}
