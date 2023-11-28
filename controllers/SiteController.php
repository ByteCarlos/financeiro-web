<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;

class SiteController extends GlobalController
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', ['action' => '']);
    }
}
