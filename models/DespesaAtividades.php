<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "despesa_atividades".
 *
 * @property integer $id
 * @property integer $despesa_fk
 * @property integer $atividade_fk
 * @property string $valor
 *
 * @property Atividade $atividadeFk
 * @property Despesa $despesaFk
 */
class DespesaAtividades extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'despesa_atividades';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['despesa_fk', 'valor'], 'required'],
            [['despesa_fk', 'atividade_fk'], 'integer'],
            [['valor'], 'number'],
            [['atividade_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Atividade::className(), 'targetAttribute' => ['atividade_fk' => 'id']],
            [['despesa_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Despesa::className(), 'targetAttribute' => ['despesa_fk' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'despesa_fk' => 'Despesa Fk',
            'atividade_fk' => 'Atividade Fk',
            'valor' => 'Valor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtividadeFk()
    {
        return $this->hasOne(Atividade::className(), ['id' => 'atividade_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDespesaFk()
    {
        return $this->hasOne(Despesa::className(), ['id' => 'despesa_fk']);
    }
}
