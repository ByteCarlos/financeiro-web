<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recuperar_senha".
 *
 * @property string $email
 * @property string $codigo
 *
 * @property Usuario $email0
 */
class RecuperarSenha extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recuperar_senha';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'codigo'], 'required'],
            [['email'], 'string', 'max' => 100],
            [['codigo'], 'string', 'max' => 32],
            [['email'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['email' => 'email']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'codigo' => 'Codigo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmail0()
    {
        return $this->hasOne(Usuario::className(), ['email' => 'email']);
    }
}
