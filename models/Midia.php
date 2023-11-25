<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "midia".
 *
 * @property integer $id
 * @property integer $contrato_fk
 * @property string $link
 * @property string $nome
 * @property string $tipo
 * @property integer $tamanho
 * @property string $nome_falso
 *
 * @property Contrato $contratoFk
 */
class Midia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'midia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contrato_fk', 'link', 'nome', 'tipo', 'tamanho', 'nome_falso'], 'required'],
            [['contrato_fk', 'tamanho'], 'integer'],
            [['link'], 'string', 'max' => 1000],
            [['nome', 'nome_falso'], 'string', 'max' => 500],
            [['tipo'], 'string', 'max' => 200],
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
            'link' => 'Link',
            'nome' => 'Nome',
            'tipo' => 'Tipo',
            'tamanho' => 'Tamanho',
            'nome_falso' => 'Nome Falso',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContratoFk()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_fk']);
    }
}
