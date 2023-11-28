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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background: #0c1d25 !important;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100%;
            margin: 0 !important;
            padding: 0 !important;
            
        }

        .sidebar-container {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidebar-container a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 18px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidebar-container a:hover {
            color: #f1f1f1;
        }

        .sidebar-container .close-btn {
            position: absolute;
            top: 0;
            z-index: 2;
        }

        .content {
            flex: 1;
            padding: 16px;
            transition: margin-left 0.5s;
        }

        @media screen and (max-height: 450px) {
        .sidebar-container {padding-top: 15px;}
        .sidebar-container a {font-size: 16px;}
        }

        .toggle-btn, .close-btn {
            height: 55px;
            width: 100px;
            margin-top: 10px;
            font-size: 30px;
            background: none;
            border: none;
            color: white;
        }

        .sidebar-action {
            font-size: 16px !important;
            color: white !important;
        }

        .logout-button-sidebar {
            margin-top: 140%;
            margin-left: 10%;
            background: none;
            border: none;
            color: white;
        }
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container">
  <button class="toggle-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
  <div class="sidebar-container" id="sidebar-container">
    <button class="close-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
    <a href="/?r=release/index" class="sidebar-action"><i class="fa fa-usd"></i>&nbsp Lançamentos</a>
    <a href="/?r=project/index" class="sidebar-action"><i class="fa fa-file-text-o"></i>&nbsp Projetos</a>
    <a href="/?r=provider/index" class="sidebar-action"><i class="fa fa-users"></i>&nbsp Fornecedores</a>
    <a href="/?r=charts/index" class="sidebar-action"><i class="fa fa-line-chart"></i>&nbsp Gráficos</a>
    <a href="/?r=user/index" class="sidebar-action"><i class="fa fa-bar-chart"></i>&nbsp Contabilidade</a>
    <a href="/?r=reports/index" class="sidebar-action"><i class="fa fa-file-pdf-o"></i>&nbsp Relatórios</a>
    <button class="logout-button-sidebar" id="logout">Sair <i class="fa fa-sign-out"></i></button>
  </div>
  <div class="content" id="content">
        <?php echo $content?>
  </div>
</div>

<script>
function toggleSidebar() {
  var sidebar = document.getElementById("sidebar-container");
  var content = document.getElementById("content");

  if (sidebar.style.width === "250px") {
    sidebar.style.width = "0";
    content.style.marginLeft = "0";
  } else {
    sidebar.style.width = "250px";
    content.style.marginLeft = "250px";
  }
}
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
