<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coordenador".
 *
 * @property integer $id
 * @property integer $usuario_fk
 * @property integer $contrato_fk
 *
 * @property Usuario $usuarioFk
 * @property Contrato $contratoFk
 */
class Coordenador extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coordenador';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_fk', 'contrato_fk'], 'required'],
            [['usuario_fk', 'contrato_fk'], 'integer'],
            [['usuario_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['usuario_fk' => 'id']],
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
            'usuario_fk' => 'Usuario Fk',
            'contrato_fk' => 'Contrato Fk',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioFk()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'usuario_fk']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratoFk()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_fk']);
    }
}
