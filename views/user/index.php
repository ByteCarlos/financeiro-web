<?php
use yii\helpers\Html;

$this->title = "Usuários";

$this->registerCssFile('@web/common/css/user.css?v=1.1', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/user.js?v=1.1', ['depends' => ['app\assets\AppAsset']]);

?>
<div class="users-container">
    <div class="user-container form-inline form-group">
        <label class="control-label">Usuário</label>
        <?= Html::activeDropDownList(new \app\models\Usuario(), 'id', $usuarios, [
            'prompt' => 'Selecione...', 'class' => 'form-control users',
        ]) ?>
        <div class="load-users-loading-gif">
            <i class="fa fa-spin fa-spinner fa-3x"></i>
        </div>
    </div>
    <div class="add-new-user-container">
        <button class="btn btn-default add-new-user"><i class="fa fa-plus"></i> Novo Usuário</button>
    </div>
    <div class="clear"></div>
    <div class="user-info">
        <div>
            <label>Nome Completo:</label>
            <span class="user-name"></span>
        </div>
        <div>
            <label>E-mail:</label>
            <span class="user-email"></span>
        </div>
        <div>
            <label>Administrador:</label>
            <span class="user-admin"></span>
        </div>
        <div>
            <label>Editor de Lançamentos:</label>
            <span class="user-admin-lancamentos"></span>
        </div>
        <div>
            <label>Editor de Projetos:</label>
            <span class="user-admin-projetos"></span>
        </div>
        <div>
            <label>Editor de Fornecedores:</label>
            <span class="user-admin-fornecedores"></span>
        </div>
        <div>
            <label>Assessor:</label>
            <span class="user-assessor"></span>
        </div>
        <div>
            <label>Situação:</label>
            <span class="user-situation"></span>
        </div>
    </div>

    <!-- Modal -->
    <div id="add-user-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="add-user-form" role="form" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Adicionar Usuário</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger"></div>
                        <div class="add-user-loading-gif">
                            <i class="fa fa-spin fa-spinner fa-3x"></i>
                        </div>
                        <div class="add-user-container">
                            <div class="form-inline form-group">
                                <label class="control-label">Nome Completo <span
                                        class="red">*</span></label>
                                <input type="text" class="form-control add-user-name" name="name">
                            </div>
                            <div class="form-inline form-group">
                                <label class="control-label">E-mail <span class="red">*</span></label>
                                <input type="text" class="form-control add-user-email" name="email">
                            </div>
                            <div class="form-inline form-group">
                                <label class="control-label">Senha (Alterar)</label>
                                <span>ipti</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                            Fechar
                        </button>
                        <button type="button" class="btn btn-default add-user-button"><i class="fa fa-plus"></i>
                            Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="remove-user-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remover Usuário</h4>
                </div>
                <div class="modal-body">
                    <span><strong>Tem certeza</strong> que deseja fazer isso? O usuário será removido de todos os sistemas!</span>
                    <div class="remove-user-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default remove-user-button"><i class="fa fa-trash"></i>
                        Remover
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
