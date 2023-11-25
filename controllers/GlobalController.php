<?php

namespace app\controllers;

use yii\web\Controller;

class GlobalController extends Controller
{
    public $usuario;
    public $propostasPendentes;
    public $mediasBaseDir = DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'com.guigoh.cdn' . DIRECTORY_SEPARATOR;

    public function init()
    {
        parent::init();
        if (isset($_COOKIE["access_token"])) {
            $sessao = \app\models\Sessao::find()->where("codigo = :codigo", ['codigo' => $_COOKIE["access_token"]])->one();
            if ($sessao == null || !$sessao->usuarioFk->ativo) {
                return $this->redirect("/");
            }
            $propostasPendentes = \Yii::$app->db2->createCommand("select count(*) as numero from proposta where exportado_financeiro = 'P'")->queryOne();
            $this->propostasPendentes = $sessao->usuarioFk->admin || $sessao->usuarioFk->admin_projetos ? $propostasPendentes["numero"] : 0;
            $this->usuario = $sessao->usuarioFk;
        } else {
            return $this->redirect("/");
        }
    }

    public function actionEditUser()
    {
        $usuario = \app\Models\Usuario::find()->where("id = :id", ['id' => $this->usuario->id])->one();
        $emailAntigo = $usuario->email;
        $usuario->nome = trim($_POST["name"]);
        $usuario->email = $_POST["email"];
        $usuario->senha = $_POST["password"] == "" ? $usuario->senha : md5($_POST["password"]);
        if ($usuario->validate()) {
            \Yii::$app->db2->createCommand('UPDATE usuario SET nome = :nome, email = :email, senha = :senha WHERE email = :emailantigo')->bindValues(["nome" => $usuario->nome, "email" => $usuario->email, "senha" => $usuario->senha, "emailantigo" => $emailAntigo])->execute();
            \Yii::$app->db3->createCommand('UPDATE usuario SET nome = :nome, email = :email, senha = :senha WHERE email = :emailantigo')->bindValues(["nome" => $usuario->nome, "email" => $usuario->email, "senha" => $usuario->senha, "emailantigo" => $emailAntigo])->execute();
            $usuario->save();
            $response = ["valid" => true];
        } else {
            if ($usuario->errors["email"]) {
                $response = ["valid" => false, "error" => 'O E-mail "' . $usuario->email. '" j&aacute; foi utilizado.<br>'];
            }
        }
        return json_encode($response);
    }
}