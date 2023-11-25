<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Relatórios";

$this->registerCssFile('@web/common/css/reports.css?v=1.1', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/reports.js?v=1.3', ['depends' => ['app\assets\AppAsset']]);
?>

<div id="report-container">
    <div class="report-search-container">
        <form class="unsubmitable-form" id="report-form" name="report-form" autocomplete="off" method="POST">
            <div class="report-error alert-danger alert"></div>
            <div class="form-group form-inline">
                <label class="control-label">Relatório <span class="red">*</span></label>
                <select class="form-control report">
                    <option value="">Selecione...</option>
                    <option value="conciliacao">Conciliação</option>
                    <option value="contabil">Contábil</option>
                    <option value="financeiro">Financeiro</option>
                    <option value="provisionamento">Provisionamento</option>
                </select>
            </div>
            <div class="form-inline form-group">
                <label class="control-label">Projeto(s) <span class="red">*</span></label>
                <?= Html::activeDropDownList(new \app\models\Contrato(), 'id', \yii\helpers\ArrayHelper::map($contratos, 'id', 'nome', 'grupo'), [
                    'class' => 'form-control contracts', 'options' => $optionContratos
                ]) ?>
                <div class="include-options"><span class="active-options">Ativos</span> | <span
                            class="inactive-options">Encerrados</span> | <span class="chained-options">Contratos</span>
                    | <span class="free-options">Livres</span> | <span class="all-options">Todos</span> | <span
                            class="remove-options">Remover</span></div>
            </div>
            <div class="costing-container form-inline form-group">
                <label class="control-label">Filtrar por</label>
                <div class="checkbox">
                    <label><input type="checkbox" class="only-costing"
                                  name="only-costing"
                                  value="OC"><span>Apenas Custeio</span></label>
                </div>
            </div>
            <div class="provisioning-interval-container form-inline form-group">
                <label class="control-label">Filtrar por</label>
                <div class="checkbox">
                    <label><input type="checkbox" class="provisioning-interval"
                                  name="provisioning-interval"
                                  value="PI"><span>Intervalo</span></label>
                </div>
            </div>
            <div class="form-inline form-group date-container">
                <label class="control-label">Período <span class="red">*</span></label>
                <input type="text" class="form-control date report-initial-date" name="report-initial-date"
                       placeholder="Inicial">
                <input type="text" class="form-control date report-final-date" name="report-final-date"
                       placeholder="Final">
            </div>
            <button type="button" class="apply-filters btn btn-default"><i class="fa fa-file-text-o"></i> Gerar</button>
            <div class="clear"></div>
        </form>
    </div>
    <div id="generate-container" class="generate-container"></div>
    <div class="inv-container">

    </div>
</div>