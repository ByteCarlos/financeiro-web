<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sessao".
 *
 * @property integer $id
 * @property integer $usuario_fk
 * @property string $codigo
 *
 * @property Usuario $usuarioFk
 */
class Sessao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sessao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_fk', 'codigo'], 'required'],
            [['usuario_fk'], 'integer'],
            [['codigo'], 'string', 'max' => 32],
            [['usuario_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['usuario_fk' => 'id']],
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
            'codigo' => 'Codigo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioFk()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'usuario_fk']);
    }
}
