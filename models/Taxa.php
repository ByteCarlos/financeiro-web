<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "taxa".
 *
 * @property integer $id
 * @property string $data
 * @property string $descricao
 * @property integer $fornecedor_fk
 * @property integer $contrato_fk
 * @property string $valor
 * @property string $tipo
 * @property string $taxa
 *
 * @property Contrato $contratoFk
 * @property Fornecedor $fornecedorFk
 */
class Taxa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'descricao', 'fornecedor_fk', 'contrato_fk', 'valor', 'tipo', 'taxa'], 'required'],
            [['data'], 'safe'],
            [['fornecedor_fk', 'contrato_fk'], 'integer'],
            [['valor'], 'number'],
            [['taxa'], 'string'],
            [['descricao'], 'string', 'max' => 200],
            [['tipo'], 'string', 'max' => 1],
            [['contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_fk' => 'id']],
            [['fornecedor_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Fornecedor::className(), 'targetAttribute' => ['fornecedor_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'descricao' => 'Descricao',
            'fornecedor_fk' => 'Fornecedor Fk',
            'contrato_fk' => 'Contrato Fk',
            'valor' => 'Valor',
            'tipo' => 'Tipo',
            'taxa' => 'Taxa',
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
    public function getFornecedorFk()
    {
        return $this->hasOne(Fornecedor::className(), ['id' => 'fornecedor_fk']);
    }
}
