<?php

namespace app\controllers;

use app\models\Usuario;
use Yii;

class UserController extends GlobalController
{

    public function actionIndex()
    {
        if ($this->usuario->admin) {
            $usuariosArray = [];
            $usuarios = Usuario::find()->all();
            foreach ($usuarios as $usuario) {
                $usuariosArray[$usuario->id] = $usuario->nome;
            }
            return $this->render('index', ['usuarios' => $usuariosArray]);
        } else {
            return $this->redirect("/?r=site/index");
        }
    }

    public function actionGetUser()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        $usuarioDB2 = Yii::$app->db2->createCommand('SELECT * FROM usuario WHERE email=:email')->bindValue("email", $usuario->email)
            ->queryOne();
        $usuarioDB3 = Yii::$app->db3->createCommand('SELECT * FROM usuario WHERE email=:email')->bindValue("email", $usuario->email)
            ->queryOne();
        $response = $usuario->attributes;
        $response["self"] = $usuario->id == $this->usuario->id;
        $response["admin_other_platform"] = $usuarioDB2["admin"] || $usuarioDB3["admin"];
        return json_encode($response);
    }

    public function actionChangeSituation()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        $usuario->ativo = $usuario->ativo ? 0 : 1;
        $usuario->save();
        Yii::$app->db2->createCommand('UPDATE usuario SET ativo = :ativo WHERE email = :email')->bindValues(["ativo" => $usuario->ativo, "email" => $usuario->email])->execute();
        Yii::$app->db3->createCommand('UPDATE usuario SET ativo = :ativo WHERE email = :email')->bindValues(["ativo" => $usuario->ativo, "email" => $usuario->email])->execute();
    }

    public function actionChangeAssessor()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        if (!$usuario->assessor) {
            $usuario->assessor = 1;
            $usuario->admin_lancamentos = 0;
            $usuario->admin_projetos = 0;
            $usuario->admin_fornecedores = 0;
        } else {
            $usuario->assessor = 0;
        }
        $usuario->save();
    }

    public function actionChangeAdminLancamentos()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        if (!$usuario->admin_lancamentos) {
            $usuario->admin_lancamentos = 1;
            $usuario->assessor = 0;
        } else {
            $usuario->admin_lancamentos = 0;
        }
        $usuario->save();
    }

    public function actionChangeAdminProjetos()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        if (!$usuario->admin_projetos) {
            $usuario->admin_projetos = 1;
            $usuario->assessor = 0;
        } else {
            $usuario->admin_projetos = 0;
        }
        $usuario->save();
    }

    public function actionChangeAdminFornecedores()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        if (!$usuario->admin_fornecedores) {
            $usuario->admin_fornecedores = 1;
            $usuario->assessor = 0;
        } else {
            $usuario->admin_fornecedores = 0;
        }
        $usuario->save();
    }

    public function actionRemoveUser()
    {
        $request = Yii::$app->request;
        $usuario = Usuario::find()->where("id = :id", ["id" => $request->post("id")])->one();
        Yii::$app->db2->createCommand()->delete('usuario', 'email = :email', ["email" => $usuario->email])->execute();
        Yii::$app->db3->createCommand()->delete('usuario', 'email = :email', ["email" => $usuario->email])->execute();
        $usuario->delete();
    }

    public function actionAddUser()
    {
        $request = Yii::$app->request;
        $usuario = new Usuario();
        $usuario->nome = trim($request->post("name"));
        $usuario->email = $request->post("email");
        $usuario->senha = md5("ipti");
        $usuario->admin = 0;
        $usuario->admin_lancamentos = 0;
        $usuario->admin_projetos = 0;
        $usuario->admin_fornecedores = 0;
        $usuario->assessor = 0;
        $usuario->ativo = 1;
        if ($usuario->validate()) {
            Yii::$app->db2->createCommand()->insert('usuario', [
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'senha' => $usuario->senha,
                'admin' => $usuario->admin,
                'ativo' => $usuario->ativo
            ])->execute();
            Yii::$app->db3->createCommand()->insert('usuario', [
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'senha' => $usuario->senha,
                'admin' => $usuario->admin,
                'ativo' => $usuario->ativo
            ])->execute();
            $usuario->save();
            $response = ["valid" => true, "usuario" => $usuario->attributes];
        } else {
            if ($usuario->errors["email"]) {
                $response = ["valid" => false, "error" => 'O E-mail "' . $usuario->email . '" j&aacute; foi utilizado.<br>'];
            }
        }
        return json_encode($response);
    }
}