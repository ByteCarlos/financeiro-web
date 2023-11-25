<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "centro_de_custo".
 *
 * @property integer $id
 * @property integer $contrato_fk
 * @property string $data_inicial
 * @property string $data_final
 * @property integer $ordem
 * @property string $celebracao_termo_aditivo
 * @property string $valor_total
 *
 * @property Contrato $contratoFk
 * @property Receita[] $receitas
 * @property Rubrica[] $rubricas
 */
class CentroDeCusto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'centro_de_custo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contrato_fk', 'data_inicial', 'data_final', 'ordem', 'valor_total'], 'required'],
            [['contrato_fk', 'ordem'], 'integer'],
            [['data_inicial', 'data_final', 'celebracao_termo_aditivo'], 'safe'],
            [['valor_total'], 'number'],
            [['contrato_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contrato_fk' => 'Contrato Fk',
            'data_inicial' => 'Data Inicial',
            'data_final' => 'Data Final',
            'ordem' => 'Ordem',
            'celebracao_termo_aditivo' => 'Celebracao Termo Aditivo',
            'valor_total' => 'Valor Total',
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
    public function getReceitas()
    {
        return $this->hasMany(Receita::className(), ['centro_de_custo_fk' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubricas()
    {
        return $this->hasMany(Rubrica::className(), ['centro_de_custo_fk' => 'id']);
    }
}
