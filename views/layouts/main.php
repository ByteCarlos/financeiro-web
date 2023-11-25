<?php
use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <title><?= Html::encode($this->title) ?></title>

    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<div id="wrapper" class="">
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-brand" href="/?r=site/index"><i class="fa fa-money"></i> Financeiro</a>
        </div>
        <span class="logout">Sair <i class="fa fa-sign-out"></i></span>
        <span class="welcome"><?= explode(" ", Yii::$app->controller->usuario->nome)[0] ?> <i
                class="fa fa-cog edit-user"></i></span>
    </nav>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">
            <!-- Sidebar -->
            <div class="row">
                <div class="sidebar hide-print">
                    <ul class="sidebar-nav">
                        <li>
                            <a href="/?r=site/index" class="launches-menu"><i class="fa fa-usd"></i> Lançamentos</a>
                        </li>
                        <li>
                            <a href="?r=project/index" class="projects-menu"><i class="fa fa-file-text-o"></i> Projetos <?= Yii::$app->controller->propostasPendentes > 0 ? '<span class="pending-proposals badge">' . Yii::$app->controller->propostasPendentes . '</span>' : ''?></a>
                        </li>
                        <li>
                            <a href="?r=provider/index" class="providers-menu"><i class="fa fa-users"></i> Fornecedores</a>
                        </li>
                        <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_lancamentos || Yii::$app->controller->usuario->admin_projetos || Yii::$app->controller->usuario->admin_fornecedores || Yii::$app->controller->usuario->assessor) : ?>
                        <li>
                            <a href="?r=charts/index" class="charts-menu"><i class="fa fa-line-chart"></i> Gráficos</a>
                        </li>
                        <?php endif ?>
                        <?php if (Yii::$app->controller->usuario->admin) : ?>
                            <li>
                                <a href="/?r=user/index" class="users-menu"><i class="fa fa-user"></i> Usuários</a>
                            </li>
                        <?php endif ?>
                        <li>
                            <a href="?r=reports/index" class="reports-menu"><i class="fa fa-file-pdf-o"></i> Relatórios</a>
                        </li>
                    </ul>
                </div>
                <!-- /#sidebar-wrapper -->
                <div class="main">
                    <?php echo $content; ?>

                    <!-- Modal -->
                    <div id="edit-user-modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <form id="edit-user-form" role="form" autocomplete="off">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Editar Informações Pessoais</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger"></div>
                                        <div class="edit-user-loading-gif">
                                            <i class="fa fa-spin fa-spinner fa-3x"></i>
                                        </div>
                                        <div class="edit-user-container">
                                            <div class="form-inline form-group">
                                                <label class="control-label">Nome Completo <span
                                                        class="red">*</span></label>
                                                <input type="text" class="form-control edit-user-name" name="name"
                                                       value="<?= Yii::$app->controller->usuario->nome ?>">
                                            </div>
                                            <div class="form-inline form-group">
                                                <label class="control-label">E-mail <span class="red">*</span></label>
                                                <input type="text" class="form-control edit-user-email" name="email"
                                                       value="<?= Yii::$app->controller->usuario->email ?>">
                                            </div>
                                            <label class="new-password-label">Deseja alterar sua senha?</label>
                                            <div class="form-inline form-group">
                                                <label class="control-label">Nova senha</label>
                                                <input type="password" class="form-control edit-user-new-password"
                                                       name="password">
                                            </div>
                                            <div class="form-inline form-group">
                                                <label class="control-label">Digite novamente</label>
                                                <input type="password" class="form-control edit-user-new-confirm"
                                                       name="confirm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-default" data-dismiss="modal"><i
                                                class="fa fa-times"></i>
                                            Fechar
                                        </button>
                                        <button type="submit" class="btn btn-default edit-user-info"><i
                                                class="fa fa-edit"></i> Alterar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
