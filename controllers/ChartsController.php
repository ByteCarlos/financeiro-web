<?php

namespace app\controllers;

use Yii;
use yii\db\Query;

class ChartsController extends GlobalController
{

    public function actionIndex()
    {
        if ($this->usuario->admin || $this->usuario->admin_lancamentos || $this->usuario->admin_projetos || $this->usuario->admin_fornecedores || $this->usuario->assessor) {
            return $this->render('index');
        } else {
            return $this->redirect("/?r=site/index");
        }
    }

    public function actionGetYearCharts()
    {
        $request = Yii::$app->request;
        $response = [];
        $response["line"] = $this->buildMonthArray();
        $query = new Query();
        $lines = $query->select([
            "mes" => "month(data)",
            "valor" => "if (sum(valor) is null, 0, sum(valor))",
            "valor_de_custeio" => "(select if (sum(valor) is null, 0, sum(valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where custeio = 1 and month(data) = mes)"])
            ->from("despesa")
            ->join("JOIN", "despesa_atividades", "`despesa_atividades`.`despesa_fk` = `despesa`.`id`")
            ->where("year(data) = :year")
            ->params([":year" => $request->post("year")])->groupBy("month(data)")->all();
        $soma = 0;
        foreach ($lines as $line) {
            $percentual = $line["valor_de_custeio"] / $line["valor"] * 100;
            $soma += $percentual;
            $response["line"][$line["mes"]]["percentual"] = $percentual;
        }
        $response["line"]["mediana"] = $soma / 12;
        return json_encode($response);
    }

    private function buildMonthArray()
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Recife');
        for ($i = 0; $i < 12; $i++) {
            $dateObj = \DateTime::createFromFormat('!m', $i + 1);
            $monthName = $dateObj->format('F');
            $array[$i + 1] = ["mes" => ucwords(utf8_encode(strftime('%B', strtotime($monthName))))];
        }
        return $array;
    }

}