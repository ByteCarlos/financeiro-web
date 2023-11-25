<?php
$HOST = getenv("HOST_DB");
$USER = getenv("USER_DB");
$PWD = getenv("PWD_DB");
return [
    'class' => 'yii\db\Connection',
    'dsn' => "mysql:host=$HOST;dbname=br.org.ipti.projeto",
    'username' => $USER,
    'password' => $PWD,
    'charset' => 'utf8',
];
