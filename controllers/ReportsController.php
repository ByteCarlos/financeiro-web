<?php

namespace app\controllers;

use app\models\Categoria;
use app\models\CentroDeCusto;
use app\models\Contrato;
use app\models\Despesa;
use app\models\Fornecedor;
use app\models\Receita;
use app\models\Taxa;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class ReportsController extends GlobalController
{
    public function actionIndex()
    {
        if ($this->usuario->admin || $this->usuario->admin_lancamentos || $this->usuario->admin_projetos || $this->usuario->admin_fornecedores || $this->usuario->assessor) {
            $contratos = Contrato::find()->orderBy("nome")->asArray()->all();
        } else {
            $contratos = Contrato::find()->join("JOIN", "coordenador", "contrato_fk = contrato.id")->where("usuario_fk = :usuario_fk", ["usuario_fk" => $this->usuario->id])->orderBy("nome")->asArray()->all();
        }
        $result["contratos"] = [];
        foreach ($contratos as $id => $contrato) {
            $array = $contrato;
            $centroDeCusto = CentroDeCusto::find()->join("JOIN", "contrato", "contrato.id = centro_de_custo.contrato_fk")->where("contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)", [":contrato_fk" => $contrato["id"]])->one();
            if (($centroDeCusto == null && $contrato["data_final"] >= date('Y-m-d')) || ($centroDeCusto !== null && $centroDeCusto->data_final >= date('Y-m-d'))) {
                $array["grupo"] = "Ativos";
                array_push($result["contratos"], $array);
            } else {
                $array["grupo"] = "Encerrados";
                array_push($result["contratos"], $array);
            }
        }
        $optionContratos = $this->loadOptionContratos($contratos);
        return $this->render('index', ["contratos" => $result["contratos"], "optionContratos" => $optionContratos]);
    }

    private function hideCPF($cpf)
    {
        $showCPF = false;
        $user = Yii::$app->controller->usuario;

        if($user->admin || $user->admin_lancamentos || $user->admin_projetos || $user->admin_fornecedores) {
            $showCPF = true;
        }

        if(!$showCPF) {
            $cpf[4] = "X";
            $cpf[5] = "X";
            $cpf[6] = "X";
            $cpf[7] = "X";
            $cpf[8] = "X";
            $cpf[9] = "X";
            $cpf[10] = "X";
        }
        return $cpf;
    }

    private function loadOptionContratos($contratos)
    {
        $optionContratos = [];
        foreach ($contratos as $contrato) {
            $centroDeCusto = CentroDeCusto::find()->join("JOIN", "contrato", "contrato.id = centro_de_custo.contrato_fk")->where("contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)", [":contrato_fk" => $contrato["id"]])->one();
            $optionContratos[$contrato["id"]]["livre"] = $centroDeCusto == null ? 1 : 0;
            $optionContratos[$contrato["id"]]["ativo"] = ($centroDeCusto == null && $contrato["data_final"] >= date('Y-m-d')) || ($centroDeCusto !== null && $centroDeCusto->data_final >= date('Y-m-d')) ? 1 : 0;
        }
        return $optionContratos;
    }

    public function actionGenerateReport()
    {
        $request = Yii::$app->request;
        if ($request->post("initialDate") != "") {
            $arr = explode('/', $request->post("initialDate"));
            $initialDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        if ($request->post("finalDate") != "") {
            $arr = explode('/', $request->post("finalDate"));
            $finalDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        $contratos = Contrato::find()->where(['in', 'id', $request->post("contracts")])->all();

        switch ($request->post("reportType")) {
            case "conciliacao":
                $costing = "";
                if ($request->post("onlyCosting") === "true") {
                    $costing .= " and custeio = 1";
                }
                $lancamentos = [];
                $tarifas = [];
                $jurosDePoupancas = [];
                $receitas = [];
                foreach ($contratos as $contrato) {
                    $livre = $contrato->centroDeCustos == null;
                    if ($livre) {
                        $despesas = Despesa::find()->where("contrato_fk = :contrato_fk and data >= :data_inicial and data <= :data_final" . $costing, [
                            ":contrato_fk" => $contrato->id, ":data_inicial" => $initialDate, ":data_final" => $finalDate
                        ])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
                        if ($request->post("onlyCosting") === "false") {
                            $receitas = Receita::find()->where("contrato_fk = :contrato_fk and data >= :data_inicial and data <= :data_final", [
                                ":contrato_fk" => $contrato->id, ":data_inicial" => $initialDate, ":data_final" => $finalDate
                            ])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
                        }
                    } else {
                        $despesas = Despesa::find()->join("JOIN", "rubrica", "rubrica.id = despesa.rubrica_fk")->join("JOIN", "centro_de_custo", "centro_de_custo.id = rubrica.centro_de_custo_fk")->where("centro_de_custo.contrato_fk = :contrato_fk and data >= :data_inicial and data <= :data_final" . $costing, [
                            ":contrato_fk" => $contrato->id, ":data_inicial" => $initialDate, ":data_final" => $finalDate
                        ])->orderBy(['despesa.data' => SORT_ASC, 'despesa.id' => SORT_ASC])->all();
                        if ($request->post("onlyCosting") === "false") {
                            $receitas = Receita::find()->join("JOIN", "centro_de_custo", "centro_de_custo.id = receita.centro_de_custo_fk")->where("centro_de_custo.contrato_fk = :contrato_fk and data >= :data_inicial and data <= :data_final", [
                                ":contrato_fk" => $contrato->id, ":data_inicial" => $initialDate, ":data_final" => $finalDate
                            ])->orderBy(['receita.data' => SORT_ASC, 'receita.id' => SORT_ASC])->all();
                        }
                    }

                    $taxas = Taxa::find()->where("contrato_fk = :contrato_fk and data >= :data_inicial and data <= :data_final", [
                        ":contrato_fk" => $contrato->id, ":data_inicial" => $initialDate, ":data_final" => $finalDate
                    ])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
                    foreach ($despesas as $despesa) {
                        if (!isset($lancamentos[$despesa->data])) {
                            $lancamentos[$despesa->data] = [];
                        }
                        $soma = 0;
                        foreach ($despesa->despesaAtividades as $key => $despesaAtividade) {
                            $soma += $despesaAtividade->valor;
                        }
                        array_push($lancamentos[$despesa->data], [
                            "tipo" => "despesa", "contrato" => $livre ? $despesa->contratoFk->nome : $despesa->rubricaFk->centroDeCustoFk->contratoFk->nome, "fornecedor" => $despesa->fornecedorFk->nome . "<br style='mso-data-placement:same-cell;'>" . ($despesa->fornecedorFk->cnpj != null ? $despesa->fornecedorFk->cnpj : $this->hideCPF($despesa->fornecedorFk->cpf)), "descricao" => $despesa->descricao . ($despesa->custeio ? " <strong>(IPTI-ADM)</strong>" : ""),
                            "competencia" => $despesa->competencia, "rcc" => ($livre ? $despesa->centro_de_custo : $despesa->rubricaFk->descricao), "valor" => ($soma * 100), "numero_transferencia_cheque" => $despesa->numero_transferencia_cheque
                        ]);
                    }
                    foreach ($receitas as $receita) {
                        if (!isset($lancamentos[$receita->data])) {
                            $lancamentos[$receita->data] = [];
                        }
                        $data_temp = new \DateTime($receita->data);
                        array_push($lancamentos[$receita->data], [
                            "tipo" => "receita", "contrato" => $livre ? $receita->contratoFk->nome : $receita->centroDeCustoFk->contratoFk->nome, "fornecedor" => "", "descricao" => $receita->descricao, "tipo_de_despesa_fk" => $receita->tipo_de_despesa_fk, "competencia" => "", "rcc" => "",
                            "valor" => $receita->valor, "numero_transferencia_cheque" => "", 
                            "data" => $data_temp->format("d/m/Y"), 
                            "tipo_de_receita_fk" => $receita->tipo_de_receita_fk, 
                            "titulo_da_receita_fk" => $receita->titulo_da_receita_fk, 
                            "parcela_fk" => $receita->parcela_fk, 
                            "fonte_pagadora" => $receita->fonte_pagadora
                        ]);
                    }
                    foreach ($taxas as $taxa) {
                        if ($taxa->taxa == "Tarifa") {
                            array_push($tarifas, [
                                "data" => $taxa->data, "contrato" => $taxa->contratoFk->nome, "descricao" => $taxa->descricao, "tipo" => $taxa->tipo, "valor" => $taxa->valor
                            ]);
                        } else {
                            array_push($jurosDePoupancas, [
                                "data" => $taxa->data, "contrato" => $taxa->contratoFk->nome, "descricao" => $taxa->descricao, "tipo" => $taxa->tipo, "valor" => $taxa->valor
                            ]);
                        }

                    }
                }
                ksort($lancamentos);

                $result["lancamentos"] = $lancamentos;
                $result["tarifas"] = $tarifas;
                $result["jurosDePoupancas"] = $jurosDePoupancas;
                break;


            case "contabil":
                $pagamentos = [];
                foreach ($contratos as $contrato) {
                    $query = new Query();
                    $rubricas = $query->select([
                        "contrato" => "contrato.nome",
                        "descricao" => "rubrica.descricao",
                        "valor_total" => "rubrica.valor_total",
                        "valor_utilizado" => "round((select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where rubrica_fk = rubrica.id and data <= :data), 2)",
                        "valor_restante" => "round(rubrica.valor_total - (select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where rubrica_fk = rubrica.id and data <= :data), 2)",
                        "categoria" => "categoria.nome"])
                        ->from("rubrica")
                        ->join("JOIN", "categoria", "categoria.id = rubrica.categoria_fk")
                        ->join("JOIN", "centro_de_custo", "`rubrica`.`centro_de_custo_fk` = `centro_de_custo`.`id` and data_inicial <= :data")
                        ->join("JOIN", "contrato", "`contrato`.`id` = `centro_de_custo`.`contrato_fk`")
                        ->where("contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)")
                        ->params([":data" => $finalDate, ":contrato_fk" => $contrato->id])->distinct()->all();
                    $pagamentos = array_merge($pagamentos, $rubricas);
                }

                $result["pagamentos"] = $pagamentos;
                break;


            case "financeiro":
                $contractId = $contratos[0]->id;
                $query = new Query();
                $despesas = $query->select([
                    "contrato" => "contrato.nome",
                    "descricao" => "rubrica.descricao",
                    "valor" => "round((select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where rubrica_fk = rubrica.id and data >= :inicial and data <= :final), 2)",
                    "saldo_inicial" => "round(rubrica.valor_total - (select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where rubrica_fk = rubrica.id and data < :inicial), 2)",
                    "saldo_final" => "round(rubrica.valor_total - (select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where rubrica_fk = rubrica.id and data <= :final), 2)"])
                    ->from("rubrica")
                    ->join("LEFT JOIN", "despesa", "`despesa`.`rubrica_fk` = `rubrica`.`id`")
                    ->join("JOIN", "centro_de_custo", "`centro_de_custo`.`id` = `rubrica`.`centro_de_custo_fk` and  (data_inicial  <=  :final and data_final >= :inicial)")
                    ->join("JOIN", "contrato", "`contrato`.`id` = `centro_de_custo`.`contrato_fk`")
                    ->where("centro_de_custo.contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where centro_de_custo.contrato_fk = :contrato_fk)")
                    ->params([":inicial" => $initialDate, ":final" => $finalDate, ":contrato_fk" => $contractId])->distinct()->all();
                $tarifaDebitoTotal = Taxa::find()->where("contrato_fk = :contratoFk and tipo = 'D' and taxa = 'Tarifa' and data >= :inicial and data <= :final")->params([":inicial" => $initialDate, ":final" => $finalDate, ":contratoFk" => $contractId])->sum("valor");
                $jurosDebitoTotal = Taxa::find()->where("contrato_fk = :contratoFk and tipo = 'D' and taxa != 'Tarifa' and data >= :inicial and data <= :final")->params([":inicial" => $initialDate, ":final" => $finalDate, ":contratoFk" => $contractId])->sum("valor");
                $query = new Query();
                $despesaRubricas = $query->select([
                    "valor" => "round(if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)), 2)"])
                    ->from("despesa")
                    ->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")
                    ->join("JOIN", "rubrica", "`despesa`.`rubrica_fk` = `rubrica`.`id`")
                    ->join("JOIN", "centro_de_custo", "`centro_de_custo`.`id` = `rubrica`.`centro_de_custo_fk` and  (data_inicial  <=  :final and data_final >= :inicial)")
                    ->where("centro_de_custo.contrato_fk = :contrato_fk and data >= :inicial and data <= :final and ordem = (select max(ordem) from centro_de_custo where centro_de_custo.contrato_fk = :contrato_fk)")
                    ->params([":inicial" => $initialDate, ":final" => $finalDate, ":contrato_fk" => $contractId])->distinct()->one();
                $query = new Query();
                $receitas = $query->select([
                    "contrato" => "contrato.nome",
                    "data" => "receita.data",
                    "descricao" => "receita.descricao",
                    "tipo_de_despesa" => "receita.tipo_de_despesa_fk",
                    "tipo_de_receita" => "tipo_de_receita.nome",
                    "titulo_da_receita" => "titulo_da_receita.nome",
                    "parcela" => "parcela.ordem",
                    "fonte_pagadora" => "receita.fonte_pagadora",
                    "valor" => "receita.valor",
                    "saldo_inicial" => "round((select if(sum(receita.valor) is null, 0, sum(receita.valor)) from receita where centro_de_custo_fk = centro_de_custo.id and receita.data < :inicial and centro_de_custo.contrato_fk = :contrato_fk), 2)",
                    "saldo_final" => "round((select if(sum(receita.valor) is null, 0, sum(receita.valor)) from receita where centro_de_custo_fk = centro_de_custo.id and receita.data <= :final and centro_de_custo.contrato_fk = :contrato_fk), 2)"])
                    ->from("receita")
                    ->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")
                    ->join("JOIN", "contrato", "`contrato`.`id` = `centro_de_custo`.`contrato_fk`")
                    ->join("JOIN", "tipo_de_receita", "`tipo_de_receita`.`id` = `receita`.`tipo_de_receita_fk`")
                    ->join("JOIN", "titulo_da_receita", "`titulo_da_receita`.`id` = `receita`.`titulo_da_receita_fk`")
                    ->leftJoin("parcela", "`parcela`.`id` = `receita`.`parcela_fk`")    
                    ->where("centro_de_custo.contrato_fk = :contrato_fk and receita.data >= :inicial and receita.data <= :final")
                    ->params([":inicial" => $initialDate, ":final" => $finalDate, ":contrato_fk" => $contractId])->distinct()->all();
                $tarifaCreditoTotal = Taxa::find()->where("contrato_fk = :contratoFk and tipo = 'C' and taxa = 'Tarifa' and data >= :inicial and data <= :final")->params([":inicial" => $initialDate, ":final" => $finalDate, ":contratoFk" => $contractId])->sum("valor");
                $jurosCreditoTotal = Taxa::find()->where("contrato_fk = :contratoFk and tipo = 'C' and taxa != 'Tarifa' and data >= :inicial and data <= :final")->params([":inicial" => $initialDate, ":final" => $finalDate, ":contratoFk" => $contractId])->sum("valor");
                $query = new Query();
                $receitaTotal = $query->select([
                    "valor" => "round(if(sum(valor) is null, 0, sum(valor)), 2)",
                    "saldo_inicial" => "round((select if(sum(valor) is null, 0, sum(valor)) from receita where centro_de_custo_fk = centro_de_custo.id and data < :inicial and centro_de_custo.contrato_fk = :contrato_fk), 2)",
                    "saldo_final" => "round((select if(sum(valor) is null, 0, sum(valor)) from receita join centro_de_custo where centro_de_custo_fk = centro_de_custo.id and data <= :final and centro_de_custo.contrato_fk = :contrato_fk), 2)"])
                    ->from("receita")
                    ->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")
                    ->where("centro_de_custo.contrato_fk = :contrato_fk and data >= :inicial and data <= :final")
                    ->params([":inicial" => $initialDate, ":final" => $finalDate, ":contrato_fk" => $contractId])->one();

                $result["despesas"] = $despesas;
                $result["despesaRubricas"] = $despesaRubricas;
                $result["receitas"] = $receitas;
                $result["receitaTotal"] = $receitaTotal;
                $result["tarifaDebitoTotal"] = $tarifaDebitoTotal == null ? 0 : $tarifaDebitoTotal;
                $result["jurosDebitoTotal"] = $jurosDebitoTotal == null ? 0 : $jurosDebitoTotal;
                $result["tarifaCreditoTotal"] = $tarifaCreditoTotal == null ? 0 : $tarifaCreditoTotal;
                $result["jurosCreditoTotal"] = $jurosCreditoTotal == null ? 0 : $jurosCreditoTotal;
                break;


            case "provisionamento":
                if ($request->post("provisioningInterval") === "false") {
                    $query = new Query();
                    $currentDate = $query->select(["data" => "DATE_FORMAT(now(),'%Y-%m-%d')"])->one();
                    $initialDate = $currentDate["data"];
                }
                $result = [];
                foreach ($contratos as $contrato) {
                    $query = new Query();
                    $livre = $contrato->centroDeCustos == null;
                    if (!$livre) {
                        $rubricas = $query->select([
                            "contrato" => "contrato.nome",
                            "categoria" => "categoria.nome",
                            "descricao" => "rubrica.descricao",
                            "valor_total" => "rubrica.valor_total",
                            "valor_pago" => "round((select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk where rubrica.id = rubrica_fk and data <= :data_inicial), 2)",
                            "valor_provisionado" =>
                                "round(" .
                                ($request->post("provisioningInterval") === "false" ?
                                    "(select if(sum(valor) is null, 0, sum(valor)) from atividade atv left join rubrica_fornecedores rf on atv.rubrica_fornecedores_fk = rf.id where rubrica.id = rf.rubrica_fk and :data_final >= atv.data) +
                         (select if(sum(valor) is null, 0, sum(valor)) from atividade atv where rubrica.id = atv.rubrica_fk and :data_final >= atv.data) -
                         (select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa desp join despesa_atividades on desp.id = despesa_atividades.despesa_fk where rubrica.id = desp.rubrica_fk and :data_final >= desp.data and despesa_atividades.atividade_fk is not null)" :

                                    "(select if(sum(valor) is null, 0, sum(valor)) from atividade atv left join rubrica_fornecedores rf on atv.rubrica_fornecedores_fk = rf.id where rubrica.id = rf.rubrica_fk and :data_final >= atv.data and atv.data >= :data_inicial) +
                         (select if(sum(valor) is null, 0, sum(valor)) from atividade atv where rubrica.id = atv.rubrica_fk and :data_final >= atv.data and atv.data >= :data_inicial)")
                                . ", 2)"])
                            ->from("rubrica")
                            ->join("JOIN", "categoria", "categoria.id = rubrica.categoria_fk")
                            ->join("JOIN", "centro_de_custo", "`rubrica`.`centro_de_custo_fk` = `centro_de_custo`.`id` and data_inicial <= :data_final")
                            ->join("JOIN", "contrato", "`centro_de_custo`.`contrato_fk` = `contrato`.`id`")
                            ->where("contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)")
                            ->params([":data_inicial" => $initialDate, ":data_final" => $finalDate, ":contrato_fk" => $contrato->id])->distinct()->all();
                    } else {
                        $rubricas = $query->select([
                            "contrato" => "contrato.nome",
                            "valor_provisionado" =>
                                "round(" .
                                ($request->post("provisioningInterval") === "false" ?
                                    "(select if(sum(valor) is null, 0, sum(valor)) from atividade atv where atv.contrato_fk = :id and :data_final >= atv.data) - 
                         (select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa desp join despesa_atividades on desp.id = despesa_atividades.despesa_fk where desp.contrato_fk = :id and :data_final >= desp.data and despesa_atividades.atividade_fk is not null)" :

                                    "(select if(sum(valor) is null, 0, sum(valor)) from atividade atv where atv.contrato_fk = :id and :data_final >= atv.data and atv.data >= :data_inicial)")
                                . ", 2)"])
                            ->from("contrato")
                            ->where("id = :id")
                            ->params([":data_inicial" => $initialDate, ":data_final" => $finalDate, ":id" => $contrato->id])->distinct()->all();
                    }
                    foreach ($rubricas as $rubrica) {
                        $rubrica["livre"] = $livre;
                        $rubrica["valor_provisionado"] = ($rubrica["valor_provisionado"] > 0 ? $rubrica["valor_provisionado"] : 0);
                        array_push($result, $rubrica);
                    }
                }
                break;


        }
        return json_encode($result);
    }

    public function actionLoadProviderProvisionReport()
    {
        $request = Yii::$app->request;
        $providerId = $request->get("provider");

        $query = new Query();
        $currentDate = $query->select(["data" => "DATE_FORMAT(now(),'%Y-%m-%d')"])->one();

        $initialDate = $request->get("initial-date") == "" ? $currentDate["data"] : $request->get("initial-date");
        $finalDate = $request->get("final-date");
        $fornecedor = Fornecedor::find()->where("id = :id", [":id" => $providerId])->one();
        $query = new Query();
        $contratos = $query->select([
            "contrato" => "c.nome",
            "categoria" => "categoria.nome",
            "descricao" => "rubrica.descricao",
            "ordem" => "rubrica_fornecedores.ordem",
            "valor_total" => "rubrica_fornecedores.valor_total",
            "valor_pago" => "round((select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa join despesa_atividades on despesa.id = despesa_atividades.despesa_fk join atividade on atividade_fk = atividade.id where rubrica_fornecedores.id = atividade.rubrica_fornecedores_fk and despesa.data <= :data_inicial), 2)",
            "valor_provisionado" =>
                "round(" .
                ($request->get("only-interval") == null ?
                    "(select if(sum(valor) is null, 0, sum(valor)) from atividade atv where rubrica_fornecedores_fk = rubrica_fornecedores.id and :data_final >= atv.data) -
                     (select if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)) from despesa desp join despesa_atividades on desp.id = despesa_atividades.despesa_fk  join atividade on atividade_fk = atividade.id where rubrica_fornecedores.id = atividade.rubrica_fornecedores_fk and :data_final >= desp.data)" :

                    "(select if(sum(valor) is null, 0, sum(valor)) from atividade atv where rubrica_fornecedores_fk = rubrica_fornecedores.id and :data_final >= atv.data and atv.data >= :data_inicial)")
                . ", 2)"])
            ->from("rubrica")
            ->join("JOIN", "categoria", "categoria.id = rubrica.categoria_fk")
            ->join("JOIN", "centro_de_custo", "`rubrica`.`centro_de_custo_fk` = `centro_de_custo`.`id` and data_inicial <= :data_final and centro_de_custo.ordem = (select max(ordem) from centro_de_custo where contrato_fk = c.id)")
            ->join("JOIN", "contrato c", "`centro_de_custo`.`contrato_fk` = `c`.`id`")
            ->join("JOIN", "rubrica_fornecedores", "rubrica_fornecedores.rubrica_fk = rubrica.id")
            ->where("fornecedor_fk = :fornecedor_fk")
            ->params([":data_inicial" => $initialDate, ":data_final" => $finalDate, ":fornecedor_fk" => $providerId])->distinct()->all();
        foreach ($contratos as $contrato) {
            $contrato["valor_provisionado"] = ($contrato["valor_provisionado"] > 0 ? $contrato["valor_provisionado"] : 0);
        }

        return $this->render('providerProvision', ["contratos" => $contratos, "fornecedor" => $fornecedor->nome, "fornecedorId" => $fornecedor->id, "dataInicial" => $initialDate, "dataFinal" => $finalDate]);
    }
}


