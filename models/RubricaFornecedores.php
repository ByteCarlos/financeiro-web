<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubrica_fornecedores".
 *
 * @property integer $id
 * @property integer $rubrica_fk
 * @property integer $fornecedor_fk
 * @property string $data_inicial
 * @property string $data_final
 * @property integer $ordem
 * @property string $valor_total
 * @property string $unitary_value
 * @property integer $workload
 * @property integer $parcelas
 *
 * @property Atividade[] $atividades
 * @property Rubrica $rubricaFk
 * @property Fornecedor $fornecedorFk
 */
class RubricaFornecedores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rubrica_fornecedores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rubrica_fk', 'fornecedor_fk', 'data_inicial', 'data_final', 'ordem', 'valor_total', 'parcelas'], 'required'],
            [['rubrica_fk', 'fornecedor_fk', 'ordem', 'parcelas', 'workload'], 'integer'],
            [['data_inicial', 'data_final'], 'safe'],
            [['valor_total', 'unitary_value'], 'number'],
            [['rubrica_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Rubrica::className(), 'targetAttribute' => ['rubrica_fk' => 'id']],
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
            'rubrica_fk' => 'Rubrica Fk',
            'fornecedor_fk' => 'Fornecedor Fk',
            'data_inicial' => 'Data Inicial',
            'data_final' => 'Data Final',
            'ordem' => 'Ordem',
            'valor_total' => 'Valor Total',
            'unitary_value' => 'Valor Unitário',
            'workload' => 'Carga Horária',
            'parcelas' => 'Parcelas',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtividades()
    {
        return $this->hasMany(Atividade::className(), ['rubrica_fornecedores_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricaFk()
    {
        return $this->hasOne(Rubrica::className(), ['id' => 'rubrica_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFornecedorFk()
    {
        return $this->hasOne(Fornecedor::className(), ['id' => 'fornecedor_fk']);
    }
}
