<!DOCTYPE html>
<html>

<head>
    <title>CH Financeiro - Login</title>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <link href="/lib/bootstrap-3.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/lib/font-awesome-4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <link href="/common/css/login.css?v=1.0" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="login-panel panel-default <?= $action != "recover" ? "" : "display-hide" ?>">
            <img class="logo" src="/common/img/logo.png" />
            <div class="panel-heading main-panel">
                <h3 class="panel-title">Login</h3>
            </div>
            <div class="panel-body">
                <form id="login-form" role="form" method="post">
                    <div class="alert">
                        <span></span>
                    </div>
                    <fieldset>
                        <div class="fields-containers">
                            <div class="inner-addon left-addon">
                                <i class="fa fa-envelope-o"></i>
                                <input class="form-control" placeholder="E-mail" name="email" type="email" autofocus>
                            </div>
                        </div>
                        <div class="fields-containers">
                            <div class="inner-addon left-addon">
                                <i class="fa fa-lock"></i>
                                <input class="form-control" placeholder="Senha" name="password" type="password">
                            </div>
                        </div>
                        <div class="container-actions">
                            <div class="checkbox">
                                <label>
                                    <input name="persistent" type="checkbox" value="1">
                                    <span>Mantenha-me conectado</span>
                                </label>
                            </div>
                            <span class="separator"></span>
                            <span>Esqueceu sua senha?</span>
                            <a href="javascript:;" id="forget-password">Clique aqui.</a>
                        </div>
                        <div class="submit-button-container">
                            <button class="login-button login">Entrar</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="recover-panel panel panel-default display-hide">
            <div class="panel-heading main-panel">
                <h3 class="panel-title">Informe seu e-mail</h3>
            </div>
            <div class="panel-body">
                <form id="recover-form" role="form" method="post">
                    <div class="alert display-hide">
                        <span></span>
                    </div>
                    <fieldset>
                        <div class="form-group">
                            <div class="inner-addon left-addon">
                                <i class="fa fa-envelope-o"></i>
                                <input class="form-control" placeholder="E-mail" name="email" type="email">
                            </div>
                        </div>
                        <div class="form-actions">
                            <i class="fa fa-chevron-circle-left pull-left back-to-login"></i>
                            <button class="btn pull-right btn-default recover">Enviar</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="password-panel panel panel-default <?= $action == "recover" ? "" : "display-hide" ?>">
            <div class="panel-heading main-panel">
                <h3 class="panel-title">Alterar senha</h3>
            </div>
            <div class="panel-body">
                <form id="password-form" role="form" method="post" autocomplete="off">
                    <div class="alert display-hide">
                        <span></span>
                    </div>
                    <fieldset>
                        <div class="form-group">
                            <div class="inner-addon left-addon">
                                <i class="fa fa-lock"></i>
                                <input class="form-control" placeholder="Senha" name="password" type="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="inner-addon left-addon">
                                <i class="fa fa-check"></i>
                                <input class="form-control" placeholder="Confirmar senha" name="confirm"
                                    type="password">
                            </div>
                        </div>
                        <div class="form-actions">
                            <i class="fa fa-chevron-circle-left pull-left back-to-login"></i>
                            <button class="btn pull-right btn-default change-password">Confirmar</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <script src="/lib/jquery-2.2.4/dist/jquery.min.js"></script>
    <script src="/lib/bootstrap-3.3.6/dist/js/bootstrap.min.js"></script>
    <script src="/lib/jquery-validation-1.17.0/dist/jquery.validate.min.js"></script>
    <script src="/common/js/login.js?v=1.0"></script>
</body>

</html>