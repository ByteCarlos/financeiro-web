<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Relatório de Provisionamento";

$this->registerCssFile('@web/common/css/reports.css?v=1.0', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/reports.js?v=1.0', ['depends' => ['app\assets\AppAsset']]);
?>

<a href="/?r=provider/index&id=<?= $fornecedorId ?>" class="hidden-print btn btn-default"><i
            class="fa fa-chevron-left"></i>
    Voltar</a>
<div id="provision-report-container">
    <div class="search-container form-inline form-group hidden-print">
        <label for="provision-search control-label">Pesquisar:</label>
        <input type="text" class="form-control" id="provision-search">
    </div>
    <table class="table table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th colspan="7" class="table-title"><?= $fornecedor ?></th>
        </tr>
        <tr>
            <th colspan="7" class="table-subtitle"><span class="date"><?= $dataInicial ?></span> - <span
                        class="date"><?= $dataFinal ?></th>
        </tr>
        <tr>
            <th style="width: 15%;">Projeto</th>
            <th style="width: 15%;">Categoria</th>
            <th style="width: 25%;">Rubrica</th>
            <th style="width: 15%;">Valor do contrato</th>
            <th style="width: 15%;">Valor pago</th>
            <th style="width: 15%;">Valor provisionado</th>
            <th style="width: 15%;">Valor restante</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($contratos as $contrato) : ?>
            <tr>
                <td><?= $contrato["contrato"] ?></td>
                <td><?= $contrato["categoria"] ?></td>
                <td><?= $contrato["descricao"] . ($contrato["ordem"] > 1 ? " (" . ($contrato["ordem"] - 1) . "º Termo Aditivo)" : "") ?></td>
                <td class="money item"><?= $contrato["valor_total"] * 100 ?></td>
                <td class="money paid"><?= $contrato["valor_pago"] * 100 ?></td>
                <td class="money provisioned"><?= $contrato["valor_provisionado"] * 100 ?></td>
                <td class="money remaining"><?= round(($contrato["valor_total"] - ($contrato["valor_pago"] + $contrato["valor_provisionado"])) * 100, 2) ?></td>
            </tr>
        <?php
        endforeach;
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="total total-item money"></td>
            <td class="total total-paid money"></td>
            <td class="total total-provisioned money"></td>
            <td class="total total-remaining money"></td>
        </tr>
        </tfoot>
    </table>
    <div class="buttons-container">
        <button class="hidden-print btn btn-default pull-right print"><i class="fa fa-print"></i> Imprimir</button>
    </div>
</div>
