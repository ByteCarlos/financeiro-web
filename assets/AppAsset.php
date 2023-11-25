<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        "/common/css/general.css?v=1.0",
        "/lib/jquery-ui-1.12.1.custom/jquery-ui.min.css",
        "/lib/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css",
        "/lib/bootstrap-datepicker-1.6.1-dist/css/bootstrap-datepicker.min.css",
        "/lib/select2-4.0.3/dist/css/select2.min.css",
        "/lib/font-awesome-4.6.3/css/font-awesome.min.css",
        "/lib/DataTables-1.10.12/media/css/dataTables.bootstrap.min.css",
    ];
    public $js = [
        "/lib/jquery-ui-1.12.1.custom/jquery-ui.min.js",
        "/lib/bootstrap-datepicker-1.6.1-dist/js/bootstrap-datepicker.min.js",
        "/lib/bootstrap-datepicker-1.6.1-dist/locales/bootstrap-datepicker.pt-BR.min.js",
        "/lib/select2-4.0.3/dist/js/select2.min.js",
        "/lib/select2-4.0.3/dist/js/i18n/pt-BR.js",
        "/lib/jquery.price_format.2.0.min.js",
        "/lib/DataTables-1.10.12/media/js/jquery.dataTables.min.js",
        "/lib/DataTables-1.10.12/media/js/dataTables.bootstrap.min.js",
        "/lib/jquery-maskmoney-3.0.2.min.js",
        "/lib/jquery.inputmask/dist/min/inputmask/inputmask.min.js",
        "/lib/jquery.inputmask/dist/min/inputmask/jquery.inputmask.min.js",
        "/lib/jquery-validation-1.17.0/dist/jquery.validate.min.js",
        "/common/js/general.js?v=1.1"
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
