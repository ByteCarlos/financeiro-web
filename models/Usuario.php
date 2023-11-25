<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property int $admin
 * @property int $admin_lancamentos
 * @property int $admin_projetos
 * @property int $admin_fornecedores
 * @property int $assessor
 * @property int $ativo
 *
 * @property Coordenador[] $coordenadors
 * @property RecuperarSenha $recuperarSenha
 * @property Sessao[] $sessaos
 */
class Usuario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'email', 'senha', 'admin', 'admin_lancamentos', 'admin_projetos', 'admin_fornecedores', 'assessor', 'ativo'], 'required'],
            [['admin', 'admin_lancamentos', 'admin_projetos', 'admin_fornecedores', 'assessor', 'ativo'], 'integer'],
            [['nome'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 100],
            [['senha'], 'string', 'max' => 32],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'Email',
            'senha' => 'Senha',
            'admin' => 'Admin',
            'admin_lancamentos' => 'Admin Lancamentos',
            'admin_projetos' => 'Admin Projetos',
            'admin_fornecedores' => 'Admin Fornecedores',
            'assessor' => 'Assessor',
            'ativo' => 'Ativo',
        ];
    }

    /**
     * Gets query for [[Coordenadors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCoordenadors()
    {
        return $this->hasMany(Coordenador::className(), ['usuario_fk' => 'id']);
    }

    /**
     * Gets query for [[RecuperarSenha]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecuperarSenha()
    {
        return $this->hasOne(RecuperarSenha::className(), ['email' => 'email']);
    }

    /**
     * Gets query for [[Sessaos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessaos()
    {
        return $this->hasMany(Sessao::className(), ['usuario_fk' => 'id']);
    }
}
