<?php

namespace app\controllers;

use app\models\RecuperarSenha;
use app\models\Sessao;
use app\models\Usuario;
use PHPMailer\PHPMailer\PHPMailer;
use Yii;
use yii\web\Controller;


class LoginController extends Controller
{
    public function init()
    {
        parent::init();
        $this->checkRedirect();
    }

    private function checkRedirect()
    {
        if (!isset($_GET["r"]) || (isset($_GET["r"]) && $_GET["r"] != "login/validate")) {
            if (isset($_COOKIE["access_token"])) {
                $sessao = Sessao::find()->where("codigo = :codigo", ['codigo' => $_COOKIE["access_token"]])->one();
                if ($sessao != null && $sessao->usuarioFk->ativo) {
                    return $this->redirect('?r=site/index');
                }
            }
        }
    }
    
    public function actionIndex()
    {
        $this->layout = false;
        return $this->render('index', ['action' => '']);
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        if ($request->post('email') !== "" && $request->post('password') !== "") {
            $usuario = Usuario::find()->where("email = :email && senha = :senha", ['email' => $request->post('email'), 'senha' => md5($request->post('password'))])->one();
            if ($usuario != NULL) {
                if ($usuario->ativo) {
                    $usuarioDB2 = Yii::$app->db2->createCommand('SELECT * FROM usuario WHERE email = :email')->bindValue("email", $usuario->email)->queryOne();
                    $usuarioDB3 = Yii::$app->db3->createCommand('SELECT * FROM usuario WHERE email = :email')->bindValue("email", $usuario->email)->queryOne();
                    $codigo = md5($usuario->nome . date("Y-m-d H:i:s"));
                    $sessao = Sessao::find()->where("usuario_fk = :usuario_fk", [":usuario_fk" => $usuario->id])->one();
                    if ($sessao != NULL) {
                        $sessao->delete();
                        Yii::$app->db2->createCommand('delete from sessao WHERE usuario_fk = :usuario_fk')->bindValues(["usuario_fk" => $usuarioDB2["id"]])->execute();
                        Yii::$app->db3->createCommand('delete from sessao WHERE usuario_fk = :usuario_fk')->bindValues(["usuario_fk" => $usuarioDB3["id"]])->execute();
                    }
                    $sessao = new Sessao();
                    $sessao->attributes = ["usuario_fk" => $usuario->id, "codigo" => $codigo];

                    if ($sessao->validate()) {
                        $sessao->save();
                        Yii::$app->db2->createCommand()->insert('sessao', [
                            'usuario_fk' => $usuarioDB2["id"],
                            'codigo' => $codigo,
                        ])->execute();
                        Yii::$app->db3->createCommand()->insert('sessao', [
                            'usuario_fk' => $usuarioDB3["id"],
                            'codigo' => $codigo,
                        ])->execute();
                        if ($request->post("rememberme") == "true") {
                            setcookie('access_token', $codigo, time() + (86400 * 30), '/', '.ipti.org.br');
                        } else {
                            setcookie('access_token', $codigo, 0, '/', '.ipti.org.br');
                        }
                        $response = ['valid' => TRUE];
                    } else {
                        $response = ["valid" => FALSE, "error" => $sessao->getErrors()];
                    }
                } else {
                    $response = ["valid" => FALSE, "error" => 'Conta desativada pelo administrador do sistema.'];
                }
            } else {
                $response = ['valid' => FALSE, 'error' => 'Login incorreto.'];
            }
            return json_encode($response);
        }
    }

    public function actionPasswordRecover()
    {
        $request = Yii::$app->request;
        if ($request->post('email') !== "") {
            $usuario = Usuario::find()->where("email = :email", ['email' => $request->post('email')])->one();
            if ($usuario != NULL) {
                $mailer = $this->setSMTPEmail($usuario);
                if (!$mailer->send()) {
                    $response = [
                        'valid' => FALSE,
                        'error' => 'Houve um problema ao enviar o e-mail de recupera&ccedil;&atilde;o. Tente novamente.',
                        'mail_error' => $mailer->ErrorInfo
                    ];
                } else {
                    $recuperarSenha = RecuperarSenha::find()->where("email = :email", ['email' => $usuario->email])->one();
                    if ($recuperarSenha == NULL) {
                        $recuperarSenha = new RecuperarSenha();
                        $recuperarSenha->email = $usuario->email;
                    }
                    $recuperarSenha->codigo = md5($usuario->email . "underboy");
                    $recuperarSenha->save();
                    $response = ['valid' => TRUE];
                }
            } else {
                $response = [
                    'valid' => FALSE, 'error' => 'N&atilde;o existe esse e-mail cadastrado na plataforma.'
                ];
            }
            return json_encode($response);
        }
    }

    public function actionChangePassword()
    {
        $request = Yii::$app->request;
        if ($request->post('password') !== "" && $request->post('confirm') !== "") {
            if ($request->post('password') == $request->post('confirm')) {
                $usuario = Usuario::find()->where("email = :email", ['email' => $request->post('email')])->one();
                $recuperarSenha = RecuperarSenha::find()->where("email = :email", ['email' => $request->post('email')])->one();
                if ($usuario != null && $recuperarSenha != null) {
                    $usuario->senha = md5($request->post('password'));
                    Yii::$app->db2->createCommand('UPDATE usuario SET senha = :senha WHERE email = :email')->bindValues(["senha" => $usuario->senha, "email" => $usuario->email])->execute();
                    Yii::$app->db3->createCommand('UPDATE usuario SET senha = :senha WHERE email = :email')->bindValues(["senha" => $usuario->senha, "email" => $usuario->email])->execute();
                    $usuario->save();
                    $recuperarSenha->delete();
                    $response = ['valid' => TRUE];
                } else {
                    $response = ['valid' => FALSE, 'error' => 'Ocorreu um erro. Contate o administrador do sistema'];
                }
            } else {
                $response = ['valid' => FALSE, 'error' => 'As senhas preenchidas devem ser iguais.'];
            }
           return json_encode($response);
        }
    }

    public function actionValidate($email, $codigo)
    {
        $recuperarSenha = RecuperarSenha::find()->where("email = :email && codigo = :codigo", ['email' => $email, 'codigo' => $codigo])->one();
        $usuario = Usuario::find()->where("email = :email", ['email' => $email])->one();
        if ($recuperarSenha != NULL && $usuario != NULL) {
            unset($_COOKIE['access_token']);
            setcookie('access_token', '', time() - 3600, '/', strpos($_SERVER['SERVER_NAME'], "ipti.org.br") !== false ? 'ipti.org.br' : null);
            return $this->renderPartial('index', ['action' => 'recover']);
        } else {
            if (isset($_COOKIE["access_token"])) {
                $sessao = Sessao::find()->where("codigo = :codigo", ['codigo' => $_COOKIE["access_token"]])->one();
                if ($sessao != null && $sessao->usuarioFk->ativo) {
                    return $this->redirect('?r=site/index');
                } else {
                    return $this->renderPartial('index', ['action' => '']);
                }
            } else {
                return $this->renderPartial('index', ['action' => '']);
            }
        }
    }


    /**
     * Função de gerenciamento de e-mail para ativação
     *
     * @param $usuario Recebe os dados do usuário
     * @param bool $register Recebe os dados do registro
     * @return \PHPMailer Retorna a função do mailer
     */
    private function setSMTPEmail($usuario)
    {
        require_once("../vendor/phpmailer/phpmailer/src/PHPMailer.php");
        require_once("../vendor/phpmailer/phpmailer/src/SMTP.php");
        require_once("../vendor/phpmailer/phpmailer/src/Exception.php");
        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
        $mailer->SMTPSecure = "tls";
        $mailer->Host = 'smtp.office365.com';//Endereço do Host do SMTP Locaweb
        $mailer->SMTPAuth = TRUE; //define se haverá ou não autenticação no SMTP
        $mailer->Username = 'naoresponda@ipti.org.br'; //Login de autenticação do SMTP
        $mailer->Password = 'Quna5048'; //Senha de autenticação do SMTP
        $mailer->FromName = "Instituto de Pesquisas em Tecnologia e Inovação (IPTI)";
        $mailer->From = 'naoresponda@ipti.org.br'; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
        $mailer->addAddress($usuario->email, $usuario->nome); //Destinatários
        $mailer->CharSet = 'UTF-8';
        $mailer->Subject = 'IPTI Financeiro - Recuperação de senha';
        $mailer->Body = "Prezado(a) " . $usuario->nome . ",\n\n";
        $mailer->Body .= "Para poder recuperar sua senha, clique no link abaixo:\n";
        $mailer->Body .= \yii\helpers\Url::base(true) . "/?r=login/validate&email=" . $usuario->email . "&codigo=" . md5($usuario->email . "underboy");
        return $mailer;
    }
}
