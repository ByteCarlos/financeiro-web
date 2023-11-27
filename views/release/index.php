<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Lançamentos";

$this->assetBundles['Receita'] = new app\assets\AppAsset();
$this->assetBundles['Receita']->js = [
    'scripts/ReceitaView/Click.js'
];

$this->registerCssFile('@web/common/css/release.css?v=1.2', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/release.js?v=1.4', ['depends' => ['app\assets\AppAsset']]);

?>

<div class="launch-container">
    <div class="projects-container">
        <label>Projeto:</label>
        <?= Html::activeDropDownList(new \app\models\Contrato(), 'id', \yii\helpers\ArrayHelper::map($contratos, 'id', 'nome', 'grupo'), [
            'prompt' => 'Selecione...', 'class' => 'projects',
        ]) ?>
        <button class="btn btn-default project-info-button"><i class="fa fa-list"></i> Informações do Projeto</button>
        <button class="btn btn-default project-bank-statements"><i class="fa fa-file-text-o"></i> Extratos Bancários
        </button>
        <input type="hidden" class="free-project">
    </div>
    <div class="balance-container">
        <div class="tax-title">Despesas e Receitas</div>
        <div><i class="income-icon fa fa-arrow-circle-o-up"></i><span class="project-income"></span></div>
        <div><i class="expense-icon fa fa-arrow-circle-o-down"></i><span class="project-expense"></span></div>
        <div><i class="fa fa-money"></i><span class="project-balance"></span></div>
    </div>
    <div class="tax-container">
        <div class="fare-balance-container">
            <div class="tax-title">Tarifas</div>
            <div><i class="income-icon fa fa-arrow-circle-o-up"></i><span class="fare-income"></span></div>
            <div><i class="expense-icon fa fa-arrow-circle-o-down"></i><span class="fare-expense"></span></div>
            <div><i class="fa fa-money"></i><span class="fare-balance"></span></div>
        </div>
        <div class="interest-balance-container">
            <div class="tax-title">Juros de Poupança</div>
            <div><i class="income-icon fa fa-arrow-circle-o-up"></i><span class="interest-income"></span></div>
            <div><i class="expense-icon fa fa-arrow-circle-o-down"></i><span class="interest-expense"></span></div>
            <div><i class="fa fa-money"></i><span class="interest-balance"></span></div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="load-project-loading-gif">
        <i class="fa fa-spin fa-spinner fa-3x"></i>
    </div>
    <div class="project-info">
        <div class="ribbon-title">
            <div class="ribbon-title-effect"></div>
            <i class="fa fa-list"></i> Informações
        </div>
        <div>
            <div>
                <label>Nome:</label>
                <span class="contract-name"></span>
            </div>
            <div>
                <label>Apoiadora:</label>
                <span class="project-supporter"></span>
            </div>
            <div>
                <label>Origem Pública:</label>
                <span class="project-public-origin"></span>
            </div>
            <div>
                <label>Data Inicial:</label>
                <span class="project-free-initial-date"></span>
            </div>
            <div>
                <label>Data Final:</label>
                <span class="project-free-final-date"></span>
            </div>
            <div>
                <label>Parcela(s):</label>
                <span class="project-plots"></span>
            </div>
            <div class="project-plots-container"></div>
            <div class="project-dates">
                <label>Vigência:</label>
                <span class='initial-date'></span>
                <span> até </span>
                <span class='final-date'></span>
            </div>
            <div>
                <label>Valor Total:</label>
                <span class="project-value"></span>
            </div>
            <div class="project-bank-accounts">
                <label>Conta Bancária:</label>
                <button class="btn btn-default" data-toggle="modal" data-target="#bank-accounts-modal"><i
                            class="fa fa-external-link"></i> Visualizar
                </button>
            </div>
            <div class="project-coordinators-container">
                <label>Coordenador(es):</label>
                <span class="project-coordinators"></span>
            </div>
        </div>
    </div>
    <div class="project">
        <button class="btn btn-default tax-button" <?= (!Yii::$app->controller->usuario->admin && !Yii::$app->controller->usuario->admin_lancamentos ? "disabled" : "") ?>>
            <i
                    class="fa fa-money"></i> <?= (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_lancamentos ? "Adicionar Taxa" : "Taxas") ?>
        </button>
        <i class="info-icon fa fa-question-circle-o" data-toggle="modal"
           data-target="#tax-info-modal"></i>
        <div class="expenses">
            <p class="panel-title"><i class="expense-icon fa fa-arrow-circle-o-down"></i> Despesas</p>

            <div class="panel panel-default">
                <div class="panel-heading"></div>
                <div class="panel-body">
                    <div class="select-item">
                        <label>Rubrica:</label>
                        <select class="items"></select>

                        <div class="load-item-info">
                            <i class="info-icon fa fa-question-circle-o" data-toggle="modal"
                               data-target="#item-info-modal"></i>
                        </div>
                    </div>
                    <button class="btn btn-default manage-activity-button"><i class="fa fa-search"></i> Produtos da
                        Rubrica
                    </button>
                    <div class="load-item-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-2x"></i>
                    </div>
                    <div class="clear"></div>
                    <hr/>
                    <div class="add-expense-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                    <div class="item-container">
                        <div class="alert alert-danger"></div>
                        <div class="input-column">
                            <div class="form-inline provider-container form-group">
                                <label class="control-label">Fornecedor <span class="red">*</span></label>
                                <select class="expense-provider"></select>

                                <div class="load-provider-info">
                                    <i class="info-icon fa fa-question-circle-o" data-toggle="modal"
                                       data-target="#load-provider-info"></i>
                                </div>
                            </div>
                            <div class="form-inline provider-container date-container expense-date-container form-group">
                                <label class="control-label">Data <span class="red">*</span></label>
                                <input type="text" class="expense-date form-control">
                            </div>
                            <div class="product-container provider-container">
                                <div class="form-inline expense-activity-container">
                                    <label class="control-label">Produto </label>
                                    <select class="expense-activity"></select>
                                </div>
                                <div class="form-inline expense-value-container">
                                    <label class="control-label">Valor <span class="red">*</span></label>
                                    <input class="form-control expense-money">
                                </div>
                            </div>
                            <i class="add-more-products fa fa-plus"></i>
                            <div class="load-provider-loading-gif">
                                <i class="fa fa-spin fa-spinner fa-3x"></i>
                            </div>
                        </div>
                        <div class="input-column">
                            <div class="form-inline form-group admin-privilege">
                                <label class="control-label">Favorecido <span class="red">*</span></label>
                                <?= Html::activeDropDownList(new \app\models\Fornecedor(), 'id', \yii\helpers\ArrayHelper::map($favorecidos, 'id', 'dados'), [
                                    'prompt' => 'Selecione...', 'class' => 'expense-favorite',
                                ]) ?>
                            </div>
                            <div class="form-inline form-group admin-privilege">
                                <label class="control-label">Nº Transf/Cheque</label>
                                <input class="form-control expense-transf-check-number">
                            </div>
                            <div class="form-inline form-group admin-privilege">
                                <label class="control-label">Descrição <span class="red">*</span></label>
                                <textarea type="text" class="form-control expense-description"></textarea>
                            </div>
                            <div class="form-inline form-group admin-privilege">
                                <label class="control-label">Competência</label>
                                <input class="form-control expense-competence">
                            </div>
                            <div class="form-inline form-group admin-privilege expense-cc-container">
                                <label class="control-label">Centro de Custo</label>
                                <input class="form-control expense-cc">
                            </div>
                            <div class="form-inline form-group admin-privilege">
                                <label class="control-label">Fonte <span class="red">*</span></label>
                                <?= Html::activeDropDownList(new \app\models\Fonte(), 'id', \yii\helpers\ArrayHelper::map($fontes, 'id', 'nome'), [
                                    'prompt' => 'Selecione...', 'class' => 'expense-source',
                                ]) ?>
                            </div>
                            <div class="form-inline form-group admin-privilege">
                                <label>Custeio</label>
                                <input name="costing" type="checkbox" class="expense-costing">
                            </div>
                        </div>
                        <button class="btn btn-default add-expense admin-privilege"><i class="fa fa-plus"></i> Adicionar
                        </button>
                        <div class="clear"></div>
                        <div class="contract-balance-container">
                        </div>
                        <div class="clear"></div>
                        <div class="item-balance-container">
                            <div class="item-balance">
                                <div class="item-balance-title">Rubrica</div>
                                <div class="item-balance-body">
                                    <div>
                                        <label>Valor:</label>
                                        <span class="item-balance-value"></span>
                                    </div>
                                    <div>
                                        <label>Gasto:</label>
                                        <span class="item-balance-paid"></span>
                                    </div>
                                    <div>
                                        <label>Restante:</label>
                                        <span class="item-balance-remaining"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <table id="expense-table" class="expense-table table table-bordered" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th></th>
                                <th>Fornecedor</th>
                                <th>Favorecido</th>
                                <th>Competência</th>
                                <th>Centro de Custo</th>
                                <th>Produto</th>
                                <th>Valor</th>
                                <th>Categoria</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="income">
            <p class="panel-title"><i class="income-icon fa fa-arrow-circle-o-up"></i> Receitas</p>

            <div class="panel panel-default">
                <div class="panel-heading"></div>
                <div class="panel-body">
                    <table id="income-table" class="table table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>Parcela</th>
                            <th>Fonte de Recursos</th>
                            <th>Valor</th>
                            <th>Rubrica</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_lancamentos): ?>
                        <button class="pull-right btn btn-default add-income-button"><i class="fa fa-plus" id="add-income"></i>
                            Adicionar
                        </button>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="bank-accounts-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Conta Bancária</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <label>Banco:</label>
                        <span class="banco-info"></span>
                    </div>
                    <div>
                        <label>Agência:</label>
                        <span class="agencia-info"></span>
                    </div>
                    <div>
                        <label>Tipo de Conta:</label>
                        <span class="tipo-info"></span>
                    </div>
                    <div>
                        <label>PIX:</label>
                        <span class="pix-info"></span>
                    </div>
                    <div>
                        <label>Conta:</label>
                        <span class="conta-info"></span>
                    </div>
                    <div>
                        <label>Proprietário:</label>
                        <span class="proprietario-info"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>

        </div>
    </div>
                     
    <!-- Modal -->
    <div id="income-info-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Receita</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <label>Data:</label>
                        <span class="income-date-info"></span>
                    </div>
                    <div>
                        <label>Descrição:</label>
                        <span class="income-description-info"></span>
                    </div>
                    <div>
                        <label>Categoria:</label>
                        <span class="income-type-info"></span>
                    </div>
                    <div>
                        <label>Tipo de Despesa:</label>
                        <span class="income-type-expense"></span>
                    </div>
                    <div>
                        <label>Tipo:</label>
                        <span class="income-title-info"></span>
                    </div>
                    <div class="income-plot-info-container">
                        <label>Parcela:</label>
                        <span class="income-plot-info"></span>
                    </div>
                    <div>
                        <label>Fonte de Recursos:</label>
                        <span class="income-paying-source-info"></span>
                    </div>
                    <div>
                        <label>Valor:</label>
                        <span class="income-value-info"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="expense-info-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Despesa</h4>
                </div>
                <div class="modal-body">
                    <div class="expense-item-info-container">
                        <label>Rubrica:</label>
                        <span class="expense-item-info"></span>
                    </div>
                    <div>
                        <label>Competência:</label>
                        <span class="expense-competence-info"></span>
                    </div>
                    <div class="expense-cc-info-container">
                        <label>Centro de Custo:</label>
                        <span class="expense-cc-info"></span>
                    </div>
                    <div>
                        <label>Data:</label>
                        <span class="expense-date-info"></span>
                    </div>
                    <div>
                        <label>Descrição:</label>
                        <span class="expense-description-info"></span>
                    </div>
                    <div>
                        <label>Fornecedor:</label>
                        <span class="expense-provider-info"></span>
                    </div>
                    <div>
                        <label>Favorecido:</label>
                        <span class="expense-favorite-info"></span>
                    </div>
                    <div class="expense-activities-container">
                    </div>
                    <div>
                        <label>Nº Transf/Cheque:</label>
                        <span class="expense-transf-check-number-info"></span>
                    </div>
                    <div>
                        <label>Categoria:</label>
                        <span class="expense-source-info"></span>
                    </div>
                    <div>
                        <label>Custeio:</label>
                        <span class="expense-costing-info"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MARCAÇÃO XXXXXXXXXXXXXXXXXXXXXX-->   
    <!-- Modal -->
    <div id="manage-income-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="manage-income-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger"></div>
                    <input type="hidden" name="income-id" class="income-id">

                    <div class="form-inline date-container form-group">
                        <label class="control-label">Data <span class="red">*</span></label>
                        <input type="text" class="income-date form-control">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Descrição <span class="red">*</span></label>
                        <textarea type="text" class="form-control income-description"></textarea>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Categoria <span class="red">*</span></label>
                        <select class="income-type" id="categoria-select"></select>
                    </div>
                    <div class="form-inline form-group" id="tipo-de-despesa-container" style="display: none;">
                        <label class="control-label">Tipo de Despesa <span class="red">*</span></label>
                        <select class="income-expense" id="despesa-select"></select>
                    </div>
                    <div class="form-inline form-group" id="tipo-de-despesa-text" style="display: none;">
                        <label class="control-label">Descrição da Despesa <span class="red">*</span></label>
                        <textarea type="text" class="form-control income-expense-description"></textarea>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Tipo <span class="red">*</span></label>
                        <select class="income-title"></select>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Rubrica </label>
                        <select class="income-rubric"></select>
                    </div>
                    <div class="form-inline form-group plot-container">
                        <label class="control-label">Parcela</label>
                        <select class="income-plot"></select>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Fonte de Recursos <span class="red">*</span></label>
                        <input class="form-control income-paying-source">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Valor <span class="red">*</span></label>
                        <input class="form-control income-money">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default add-income">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="manage-expense-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="manage-expense-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger"></div>
                    <input type="hidden" name="expense-id" class="expense-id">

                    <div class="modal-provider-container form-inline form-group">
                        <label class="control-label">Fornecedor <span class="red">*</span></label>
                        <select class="modal-expense-provider"></select>
                    </div>
                    <div class="load-modal-provider-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                    <div
                            class="modal-provider-container form-inline date-container form-group modal-expense-date-container">
                        <label class="control-label">Data <span class="red">*</span></label>
                        <input type="text" class="modal-expense-date form-control">
                    </div>
                    <div class="modal-product-container modal-provider-container">
                        <input type="hidden" class="modal-product-id">
                        <div class="form-inline modal-expense-activity-container">
                            <label class="control-label">Produto </label>
                            <select class="modal-expense-activity"></select>
                        </div>
                        <div class="form-inline modal-expense-value-container">
                            <label class="control-label">Valor <span class="red">*</span></label>
                            <input class="form-control modal-expense-money">
                        </div>
                    </div>
                    <div class="modal-add-more-products"><i class="fa fa-plus"></i></div>
                    <div class="form-inline form-group admin-privilege">
                        <label class="control-label">Favorecido <span class="red">*</span></label>
                        <?= Html::activeDropDownList(new \app\models\Fornecedor(), 'id', \yii\helpers\ArrayHelper::map($favorecidos, 'id', 'dados'), [
                            'prompt' => 'Selecione...', 'class' => 'modal-expense-favorite',
                        ]) ?>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Nº Transf/Cheque</label>
                        <input class="form-control modal-expense-transf-check-number">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Descrição <span class="red">*</span></label>
                        <textarea type="text" class="form-control modal-expense-description"></textarea>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Competência</label>
                        <input class="form-control modal-expense-competence">
                    </div>
                    <div class="form-inline form-group modal-expense-cc-container">
                        <label class="control-label">Centro de Custo</label>
                        <input class="form-control modal-expense-cc">
                    </div>
                    <div class="form-inline form-group admin-privilege">
                        <label class="control-label">Fonte <span class="red">*</span></label>
                        <?= Html::activeDropDownList(new \app\models\Fonte(), 'id', \yii\helpers\ArrayHelper::map($fontes, 'id', 'nome'), [
                            'prompt' => 'Selecione...', 'class' => 'modal-expense-source',
                        ]) ?>
                    </div>
                    <div class="form-inline form-group">
                        <label>Custeio</label>
                        <input name="modalcosting" type="checkbox" class="modal-expense-costing">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default change-expense"><i class="fa fa-edit"></i> Alterar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="item-info-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Rubrica</h4>
                </div>
                <div class="modal-body">
                    <div class="item-info">
                        <div>
                            <label>Descrição:</label>
                            <span class="item-description-info"></span>
                        </div>
                        <div>
                            <label>Natureza de Despesa:</label>
                            <span class="item-category-info"></span>
                        </div>
                        <div>
                            <label>Valor Total:</label>
                            <span class="item-total-value-info"></span>
                        </div>
                        <div>
                            <label>Vínculo:</label>
                            <span class="item-contract-type-info"></span>
                        </div>
                        <div>
                            <label>Categoria:</label>
                            <span class="item-source-info"></span>
                        </div>
                        <div>
                            <label>Vinculante:</label>
                            <span class="item-vinculation-info"></span>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="remove-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remover</h4>
                </div>
                <div class="modal-body">
                    <span>O lançamento será removido e os valores serão recalculados. Tem certeza disso?</span>
                    <div class="remove-launch-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                    <input type="hidden" name="type" class="launch-type"/>
                    <input type="hidden" name="id" class="launch-id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default remove-launch"><i class="fa fa-trash"></i>
                        Remover
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="load-provider-info" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Contrato</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <label>Tipo de Contrato:</label>
                        <span class="provider-tipo-contrato-info"></span>
                    </div>
                    <div>
                        <label>Fornecedor:</label>
                        <span class="provider-name-info"></span>
                    </div>
                    <div>
                        <label>CNPJ:</label>
                        <span class="provider-cnpj-info"></span>
                    </div>
                    <div>
                        <label>Representante Legal:</label>
                        <span class="provider-representante-info"></span>
                    </div>
                    <div>
                        <label>CPF:</label>
                        <span class="provider-cpf-info"></span>
                    </div>
                    <div>
                        <label>Pis:</label>
                        <span class="provider-pis-info"></span>
                    </div>
                    <div>
                        <label>RG:</label>
                        <span class="provider-rg-info"></span>
                    </div>
                    <div>
                        <label>E-mail:</label>
                        <span class="provider-email-info"></span>
                    </div>
                    <div>
                        <label>Endereço:</label>
                        <span class="provider-endereco-info"></span>
                    </div>
                    <div>
                        <label>Profissão:</label>
                        <span class="provider-profissao-info"></span>
                    </div>
                    <div>
                        <label>Telefone:</label>
                        <span class="provider-telefone-info"></span>
                    </div>
                    <div class="bank-data">
                        <div>
                            <label>Banco:</label>
                            <span class="provider-conta-banco-info"></span>
                        </div>
                        <div>
                            <label>Agência:</label>
                            <span class="provider-conta-agencia-info"></span>
                        </div>
                        <div>
                            <label>Tipo de Conta:</label>
                            <span class="provider-conta-tipo-info"></span>
                        </div>
                        <div>
                            <label>PIX:</label>
                            <span class="provider-conta-pix-info"></span>
                        </div>
                        <div>
                            <label>Conta:</label>
                            <span class="provider-conta-conta-info"></span>
                        </div>
                        <div>
                            <label>Proprietário:</label>
                            <span class="provider-conta-proprietario-info"></span>
                        </div>
                    </div>
                    <hr/>
                    <div class="provider-item-container">

                    </div>
                    <div class="clear"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal-->
    <div id="manage-tax-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="manage-tax-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger"></div>
                    <input type="hidden" class="manage-tax-id">
                    <div class="form-inline date-container form-group">
                        <label class="control-label">Data <span class="red">*</span></label>
                        <input type="text" class="tax-date form-control">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Descrição <span class="red">*</span></label>
                        <textarea type="text" class="form-control tax-description"></textarea>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Fornecedor <span class="red">*</span></label>
                        <select class="tax-provider"></select>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Taxa <span class="red">*</span></label>
                        <div class="radio">
                            <label><input type="radio" class="tax-tax" name="tax-tax"
                                          value="T">Tarifa</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" class="tax-tax" name="tax-tax"
                                          value="J">Juros de Poupança</label>
                        </div>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Tipo <span class="red">*</span></label>
                        <div class="radio">
                            <label><input type="radio" class="tax-type" name="tax-type"
                                          value="C">Crédito</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" class="tax-type" name="tax-type"
                                          value="D">Débito</label>
                        </div>
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Valor <span class="red">*</span></label>
                        <input type="text" class="form-control tax-money">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default manage-tax"></button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal-->
    <div id="tax-info-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Taxas</h4>
                </div>
                <div class="modal-body">
                    <table id="tax-table" class="tax-table table table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Fornecedor</th>
                            <th>Taxa</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th style="width: 44px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="remove-tax-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remover</h4>
                </div>
                <div class="modal-body">
                    <span>A taxa será removida e os valores serão recalculados. Tem certeza disso?</span>
                    <div class="remove-tax-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                    <input type="hidden" name="id" class="remove-tax-id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default remove-tax-button"><i class="fa fa-trash"></i>
                        Remover
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal-->
    <div id="manage-activity-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Produtos da Rubrica</h4>
                </div>
                <div class="manage-activity-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger"></div>
                    <input type="hidden" class="manage-activity-id">
                    <div class="new-activity form-inline form-group admin-privilege">
                        <textarea class="form-control activity-description"
                                  placeholder="Descrição da Atividade"></textarea>
                        <input class="form-control activity-value" type="text" placeholder="Valor *">
                        <input class="form-control activity-date" type="text" placeholder="Data *">
                        <button type="button" class="btn btn-default manage-activity"><i class="fa fa-plus"></i>
                            Adicionar
                        </button>
                    </div>
                    <table id="activity-table" class="activity-table table table-bordered" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Produto</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Pago</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="remove-activity-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remover</h4>
                </div>
                <div class="modal-body">
                    <span>O produto será removido e os lançamentos relacionados perderão o vínculo. Tem certeza disso?</span>
                    <div class="remove-activity-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                    <input type="hidden" name="id" class="remove-activity-id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default remove-activity-button"><i class="fa fa-trash"></i>
                        Remover
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="bank-statements-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Extratos Bancários</h4>
                </div>
                <div class="modal-body">
                    <div class="uploaded-files-container"></div>
                    <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_lancamentos): ?>
                        <div class="upload-container">
                            <div class="alert"></div>
                            <input class="upload-max-file-size" type="hidden" value="838860800">
                            <input class="upload-input" type="file" accept="application/pdf">
                            <div class="upload-fake-button">
                                <i class="fa fa-upload"></i> Anexar
                            </div>
                            <div class="file-name"></div>
                            <button class="btn btn-default upload">Enviar <i class="fa fa-chevron-circle-right"></i>
                            </button>
                            <div class="upload-file-loading-gif"><i class="fa fa-spin fa-spinner fa-3x"></i></div>
                        </div>
                    <?php endif ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="remove-file-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remover</h4>
                </div>
                <div class="modal-body">
                    <span>Tem certeza disso?</span>
                    <div class="remove-file-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                    <input type="hidden" name="id" class="remove-file-id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default remove-file-button"><i class="fa fa-trash"></i>
                        Remover
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
