<!DOCTYPE html>
<head>
    <title>CH Financeiro - Home Page</title>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
    <link href="/lib/bootstrap-3.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/lib/font-awesome-4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <link href="/common/css/site.css?v=1.0" rel="stylesheet">
</head>
<body>
    <div class="main-container">
        <div class="welcome-container">
            <h3 style="font-size: 50px">Olá, <?= explode(" ", Yii::$app->controller->usuario->nome)[0] ?></h3>
            <button class="logout-button" id="logout">Sair <i class="fa fa-sign-out"></i></button>
        </div>
        <div class="actions">
            <div class="line">
                <a href="/?r=release/index"><button class="action-button"><i class="fa fa-usd"></i>&nbsp Lançamentos</button></a>
                <a href="/?r=project/index"><button class="action-button"><i class="fa fa-file-text-o"></i>&nbsp Projetos</button></a>
                <a href="/?r=provider/index"><button class="action-button"><i class="fa fa-users"></i>&nbsp Fornecedores</button></a>
            </div>
            <div class="line">
                <a href="/?r=charts/index"><button class="action-button"><i class="fa fa-line-chart"></i>&nbsp Gráficos</button></a>
                <a href="/?r=user/index"><button class="action-button"><i class="fa fa-bar-chart"></i>&nbsp Contabilidade</button></a>
                <a href="/?r=reports/index"><button class="action-button"><i class="fa fa-file-pdf-o"></i>&nbsp Relatórios</button></a>
            </div>
        </div>
    </div>
    
</body>
</html>