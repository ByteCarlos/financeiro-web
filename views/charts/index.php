<?php
use yii\helpers\Html;

$this->title = "Gráficos";

$this->registerJsFile('@web/lib/gcharts.min.js', ['depends' => ['app\assets\AppAsset']]);
$this->registerCssFile('@web/common/css/charts.css?v=1.0', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/charts.js?v=1.0', ['depends' => ['app\assets\AppAsset']]);

?>

<div class="charts-container">
    <div class="form-inline form-group">
        <label class="control-label">Ano</label>
        <input type="text" class="form-control year" onkeydown="return false">
        <div class="load-year-loading-gif">
            <i class="fa fa-spin fa-spinner fa-3x"></i>
        </div>
        <div class="charts">
            <div id="line" style="width: 100%; height: 600px; display: inline-block"></div>
        </div>
    </div>
</div>
