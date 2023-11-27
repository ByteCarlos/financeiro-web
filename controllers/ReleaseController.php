<?php

namespace app\controllers;

use app\models\CentroDeCusto;
use app\models\ContaBancaria;
use app\models\Despesa;
use app\models\DespesaAtividades;
use app\models\Fonte;
use app\models\Fornecedor;
use app\models\Midia;
use app\models\Receita;
use app\models\Rubrica;
use app\models\RubricaFornecedores;
use app\models\Atividade;
use app\models\Taxa;
use app\models\TipoDeReceita;
use app\models\TipoDeDespesa;
use app\models\TituloDaReceita;
use Yii;
use yii\db\Query;
use app\models\Contrato;

class ReleaseController extends GlobalController
{


    /**
     * Displays homepage.
     *
     * @return string
     */
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
        $favorecidos = Fornecedor::find()->all();
        $result["favorecidos"] = [];
        foreach ($favorecidos as $favorecido) {
            $identificacao = $favorecido->cnpj != null ? $favorecido->cnpj : $favorecido->cpf;
            $favorecidoArray["id"] = $favorecido->id;;
            $favorecidoArray["dados"] = $favorecido->nome . "|" . $identificacao . "|" . $favorecido->tipoDeContratoFk->nome;
            array_push($result["favorecidos"], $favorecidoArray);
        }
        return $this->render('index', [
            "contratos" => $result["contratos"],
            "fontes" => Fonte::find()->orderBy("nome")->asArray()->all(),
            "favorecidos" => $result["favorecidos"]
        ]);
    }

    public function actionLoadContractInfo()
    {
        /** @var Contrato $contrato */
        $request = Yii::$app->request;
        $id = $request->post("id");
        $contrato = Contrato::find()->where("id = :id", [":id" => $id])->one();
        if ($contrato != null) {
            $result = [];
            $result["admin"] = $this->usuario->admin || $this->usuario->admin_lancamentos;
            $result["contrato"]["nome"] = $contrato->nome;
            $result["contrato"]["parcelas"] = [];

            foreach ($contrato->parcelas as $parcela) {
                array_push($result["contrato"]["parcelas"], $parcela->attributes);
                if (!isset($result["contrato"]["parcelas_select"])) {
                    $result["contrato"]["parcelas_select"] = [];
                }
                $pago = Receita::find()->where("parcela_fk = :parcela_fk", [":parcela_fk" => $parcela->id])->sum("valor");
                if (round($parcela->valor) !== round($pago)) {
                    array_push($result["contrato"]["parcelas_select"], $parcela->id . "|" . $parcela->descricao . "|" . $parcela->valor . "|" . $pago . "|" . $parcela->ordem . "|" . $parcela->data . "|" . $parcela->fonte_pagadora);
                }
            }
            $result["contrato"]["apoiadora"] = $contrato->apoiadora;
            $result["contrato"]["origem_publica"] = $contrato->origem_publica ? "Sim" : "Não";
            $dataInicial = new \DateTime($contrato->data_inicial);
            $dataFinal = new \DateTime($contrato->data_final);
            $result["contrato"]["data_inicial"] = $dataInicial->format("d/m/Y");
            $result["contrato"]["data_final"] = $dataFinal->format("d/m/Y");
            if ($contrato->conta_bancaria_fk != null) {
                $result["contrato"]['conta_bancaria'] = $contrato->contaBancariaFk->attributes;
                $result["contrato"]['conta_bancaria']["banco"] = $contrato->contaBancariaFk->bancoFk != null ? $contrato->contaBancariaFk->bancoFk->codigo . " - " . $contrato->contaBancariaFk->bancoFk->nome_abreviado : null;
            }
            $result["contrato"]["coordenadores"] = [];

            foreach ($contrato->coordenadors as $coordenador) {
                $coordenadorArray = $coordenador->attributes;
                $coordenadorArray["nome"] = $coordenador->usuarioFk->nome;
                array_push($result["contrato"]["coordenadores"], $coordenadorArray);
            }

            $result["contrato"]["midias"] = [];
            foreach ($contrato->midias as $midia) {
                array_push($result["contrato"]["midias"], $midia->attributes);
            }

            $centroDeCusto = CentroDeCusto::find()->where("contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)", [":contrato_fk" => $id])->one();
            $result["contrato"]['livre'] = 1;
            if ($centroDeCusto != null) {
                $result["contrato"]['livre'] = 0;
                $dataInicial = new \DateTime($centroDeCusto->data_inicial);
                $dataFinal = new \DateTime($centroDeCusto->data_final);
                $result["contrato"]['data_inicial'] = $dataInicial->format("d/m/Y");
                $result["contrato"]['data_final'] = $dataFinal->format("d/m/Y");
                $result["contrato"]["valor_total"] = $centroDeCusto->valor_total;
                foreach ($centroDeCusto->getRubricas()->all() as $rubrica) {
                    $result["contrato"]['rubricas'][$rubrica->id] = $rubrica->descricao;
                }
                $result["contrato"]["receita_total"] = Receita::find()->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $contrato->id])->sum("valor");
                $result["contrato"]["despesa_total"] = Despesa::find()->join("JOIN", "rubrica", "`despesa`.`rubrica_fk` = `rubrica`.`id`")->join("JOIN", "centro_de_custo", "`rubrica`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $contrato->id])->sum("despesa_atividades.valor");
            } else {
                $result["contrato"]["receita_total"] = Receita::find()->where("contrato_fk = :contrato_fk", [":contrato_fk" => $contrato->id])->sum("valor");
                $result["contrato"]["despesa_total"] = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("contrato_fk = :contrato_fk", [":contrato_fk" => $contrato->id])->sum("despesa_atividades.valor");
            }
            $result["contrato"]["tarifa_credito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'C' and taxa = 'Tarifa'", [":contrato_fk" => $contrato->id])->sum("valor");
            $result["contrato"]["tarifa_debito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'D' and taxa = 'Tarifa'", [":contrato_fk" => $contrato->id])->sum("valor");
            $result["contrato"]["juros_credito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'C' and taxa != 'Tarifa'", [":contrato_fk" => $contrato->id])->sum("valor");
            $result["contrato"]["juros_debito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'D' and taxa != 'Tarifa'", [":contrato_fk" => $contrato->id])->sum("valor");

            $tiposDeReceita = [];
            foreach (TipoDeReceita::find()->all() as $tipoDeReceita) {
                $tiposDeReceita[$tipoDeReceita->id] = $tipoDeReceita->nome;
            }
            $result["tipos_de_receita"] = $tiposDeReceita;
            $titulosDaReceita = [];
            foreach (TituloDaReceita::find()->all() as $tituloDaReceita) {
                $titulosDaReceita[$tituloDaReceita->id] = $tituloDaReceita->nome;
            }
            $result["titulos_da_receita"] = $titulosDaReceita;

            // O trecho de código abaixo realiza a seguinte sequência de comandos
            // Inicia o array que irá armazenar as rúbricas associadas ao contrato
            // Monta o comando SQL que irá consultar no banco quais as rubricas que
            // estão relacionadas ao contrato selecionado
            $rubricas = [];
            $rubrica_query =
            Rubrica::find()
            ->select("rubrica.id, rubrica.descricao")
            ->leftJoin("centro_de_custo", "`centro_de_custo`.`id` = `rubrica`.`centro_de_custo_fk`")
            ->leftJoin("contrato","`contrato`.`id` = `centro_de_custo`.`contrato_fk`")
            ->where("contrato.id = :id",[":id"=> $contrato->id])
            ->orderBy(["`rubrica`.`id`" => SORT_ASC])->all();

            foreach ($rubrica_query as $rubrica) {
                $rubricas[$rubrica->id] = $rubrica->descricao;
            }
            $result["rubricas"] = $rubricas;
            // Fim do script que obtém as rubricas associadas ao contrato selecionado

            $fornecedores = Fornecedor::find()->all();

            foreach ($fornecedores as $fornecedor) {
                $identificacao = $fornecedor->cnpj != null ? $fornecedor->cnpj : $fornecedor->cpf;
                $result["fornecedores"][$fornecedor->id] = $fornecedor->nome . "|" . $identificacao . "|" . $fornecedor->tipoDeContratoFk->nome;
            }

            $j = 1;
            $atividadesRubrica = Atividade::find()->where("contrato_fk = :contrato_fk", [":contrato_fk" => $contrato->id])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
            foreach ($atividadesRubrica as $atividadeRubrica) {
                $pago = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("atividade_fk = :atividade_fk", [":atividade_fk" => $atividadeRubrica->id])->sum("valor");
                if (round($atividadeRubrica->valor) !== round($pago)) {
                    $result["atividades_rubrica"][$j] = $atividadeRubrica->id . "|" . $atividadeRubrica->descricao . "|" . $atividadeRubrica->valor . "|" . $pago . "|" . $j . "|" . $atividadeRubrica->data;
                }
                $j++;
            }

            return json_encode($result);
        }
    }

    public function actionLoadIncomeInfo()
    {
        $request = Yii::$app->request;
        $receita = Receita::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $data = new \DateTime($receita->data);
        $result["data"] = $data->format("d/m/Y");
        $result["descricao"] = $receita->descricao;
        $result["fonte_pagadora"] = $receita->fonte_pagadora;
        $result["tipo_id"] = $receita->tipo_de_receita_fk;
        $result["tipo"] = $receita->tipoDeReceitaFk->nome;
        if($receita->tipo_de_despesa_fk != null) {
            $tipo_de_despesa = TipoDeDespesa::find()->where("nome = :nome", [":nome" => $receita->tipo_de_despesa_fk])->one();
            $result["tipo_de_despesa_id"] = $tipo_de_despesa->id;
            $result["tipo_de_despesa_nome"] = $tipo_de_despesa->nome;
        }else {
            $result["tipo_de_despesa_id"] = null;
            $result["tipo_de_despesa_nome"] = null;
        }
        $result["titulo_id"] = $receita->titulo_da_receita_fk;
        $result["titulo"] = $receita->tituloDaReceitaFk->nome;
        $result["parcela_id"] = $receita->parcela_fk == null ? null : $receita->parcela_fk;
        $result["parcela"] = $receita->parcela_fk == null ? "-" : $receita->parcelaFk->ordem . "º";
        $result["valor"] = number_format($receita->valor, 2);
        if($receita->rubrica_fk != null){
            $rubrica = Rubrica::find()->where("id = :id", [":nome" => $receita->rubrica_fk])->one();
            $result["rubrica_id"] = $rubrica->id;
            $result["rubrica_nome"] = $rubrica->descricao;
        }else {
            $result["rubrica_id"] = null;
            $result["rubrica_nome"] = null;
        }
                
        return json_encode($result);
    }

    public function actionLoadExpenseInfo()
    {
        $request = Yii::$app->request;
        $despesa = Despesa::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $data = new \DateTime($despesa->data);
        $result["rubrica"] = $despesa->rubricaFk != null ? $despesa->rubricaFk->descricao : "";
        $result["centro_de_custo"] = $despesa->centro_de_custo;
        $result["data"] = $data->format("d/m/Y");
        $result["descricao"] = $despesa->descricao;
        $result["fornecedor_id"] = $despesa->fornecedor_fk;
        $result["fornecedor"] = $despesa->fornecedorFk->nome;
        $result["favorecido_id"] = $despesa->favorecido_fk;
        $result["favorecido"] = $despesa->favorecidoFk->nome;
        foreach ($despesa->despesaAtividades as $key => $despesaAtividade) {
            $result["despesa_atividades"][$key]["id"] = $despesaAtividade->id;
            $result["despesa_atividades"][$key]["atividade_id"] = $despesaAtividade->atividade_fk;
            $result["despesa_atividades"][$key]["atividade"] = $despesaAtividade->atividade_fk == null ? "-" : ($despesaAtividade->atividadeFk->ordem > 0 ? "Contrato" : ($despesa->rubricaFk != null ? "Rubrica" : "Projeto"));
            $result["despesa_atividades"][$key]["valor"] = number_format($despesaAtividade->valor, 2);
        }
        $result["custeio"] = $despesa->custeio;
        $result["numero_transferencia_cheque"] = $despesa->numero_transferencia_cheque;
        $result["fonte_id"] = $despesa->fonte_fk;
        $result["fonte"] = $despesa->fonteFk->nome;
        $result["competencia"] = $despesa->competencia;
        return json_encode($result);
    }

    public function actionLoadTaxInfo()
    {
        $request = Yii::$app->request;
        $taxa = Taxa::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $data = new \DateTime($taxa->data);
        $result["data"] = $data->format("d/m/Y");
        $result["descricao"] = $taxa->descricao;
        $result["fornecedor"] = $taxa->fornecedor_fk;
        $result["contrato"] = $taxa->contrato_fk;
        $result["valor"] = number_format($taxa->valor, 2);
        $result["tipo"] = $taxa->tipo;
        $result["taxa"] = $taxa->taxa;
        return json_encode($result);
    }

    public function actionLoadExpense() {
        $request = Yii::$app->request;
        $despesa = new TipoDeDespesa();
        $despesa->nome = $request->post("nome");
        $despesa->save();
        $j = TipoDeDespesa::find()->where("nome = :nome", [":nome" => $request->post("nome")])->one();
        $result = [];
        $result["id"] = $j->id;
        $result["nome"] = $j->nome;
        return json_encode($result);
    }

    public function actionAddIncome()
    {
        $request = Yii::$app->request;
        $arr = explode('/', $request->post("data"));
        $data = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        if ($request->post("id") == null) {
            $receita = new Receita();
        } else {
            $receita = Receita::find()->where("id = :id", [":id" => $request->post("id")])->one();
        }
        $receita->data = $data;
        $receita->descricao = $request->post("descricao");
        $receita->fonte_pagadora = $request->post("fontePagadora");
        $receita->tipo_de_receita_fk = $request->post("tipoDeReceita");
        if($request->post("tipoDeDespesa") != null) {
            $tipo_de_despesa = TipoDeDespesa::find()->where("id = :id", [":id" => $request->post("tipoDeDespesa")])->one();
            $receita->tipo_de_despesa_fk = $tipo_de_despesa->nome;
        }
        $receita->titulo_da_receita_fk = $request->post("tituloDaReceita");
        $receita->parcela_fk = $request->post("parcela") == "" ? null : $request->post("parcela");
        $receita->rubrica_fk = $request->post("rubrica") == "" ? null : $request->post("rubrica");
        $receita->valor = $request->post("valor");
        $centroDeCusto = CentroDeCusto::find()->where("contrato_fk = :contrato_fk and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)", [":contrato_fk" => $request->post("contrato")])->one();

        $result = [];
        if ($centroDeCusto != null) {
            $receita->centro_de_custo_fk = $centroDeCusto->id;
            $receita->save();

            foreach ($centroDeCusto->contratoFk->parcelas as $parcela) {
                if (!isset($result["contrato"]["parcelas_select"])) {
                    $result["contrato"]["parcelas_select"] = [];
                }
                $pago = Receita::find()->where("parcela_fk = :parcela_fk", [":parcela_fk" => $parcela->id])->sum("valor");
                if (round($parcela->valor) !== round($pago)) {
                    array_push($result["contrato"]["parcelas_select"], $parcela->id . "|" . $parcela->descricao . "|" . $parcela->valor . "|" . $pago . "|" . $parcela->ordem . "|" . $parcela->data . "|" . $parcela->fonte_pagadora);
                }
            }
            $result["receita_total"] = Receita::find()->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $centroDeCusto->contratoFk->id])->sum("valor");
            $result["despesa_total"] = Despesa::find()->join("JOIN", "rubrica", "`despesa`.`rubrica_fk` = `rubrica`.`id`")->join("JOIN", "centro_de_custo", "`rubrica`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $centroDeCusto->contratoFk->id])->sum("despesa_atividades.valor");
        } else {
            $receita->contrato_fk = $request->post("contrato");
            $receita->save();

            $result["receita_total"] = Receita::find()->where("contrato_fk = :contrato_fk", [":contrato_fk" => $request->post("contrato")])->sum("valor");
            $result["despesa_total"] = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("contrato_fk = :contrato_fk", [":contrato_fk" => $request->post("contrato")])->sum("despesa_atividades.valor");
        }

        return json_encode($result);
    }

    public function actionAddExpense()
    {
        $request = Yii::$app->request;
        $arr = explode('/', $request->post("data"));
        $data = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        if ($request->post("id") == null) {
            $despesa = new Despesa();
        } else {
            $despesa = Despesa::find()->where("id = :id", [":id" => $request->post("id")])->one();
        }
        $despesa->data = $data;
        $despesa->descricao = $request->post("descricao");
        $despesa->fornecedor_fk = $request->post("fornecedor");
        $despesa->favorecido_fk = $request->post("favorecido");
        $despesa->fonte_fk = $request->post("fonte");
        $despesa->numero_transferencia_cheque = $request->post("numero_transferencia_cheque") == "" ? NULL : $request->post("numero_transferencia_cheque");
        $despesa->competencia = $request->post("competencia") == "" ? NULL : $request->post("competencia");
        $despesa->custeio = $request->post("custeio");
        $rubrica = Rubrica::find()->where("id = :id", [":id" => $request->post("rubrica")])->one();
        if ($rubrica != null) {
            $despesa->rubrica_fk = $rubrica->id;
        } else {
            $despesa->centro_de_custo = $request->post("centro_de_custo") == "" ? NULL : $request->post("centro_de_custo");
            $despesa->contrato_fk = $request->post("contrato_id");
        }
        $despesa->save();

        $despesaAtividadesIds = [];
        foreach ($request->post("despesa_atividades") as $despAtv) {
            $despesaAtividades = DespesaAtividades::find()->where("id = :id", [":id" => $despAtv["id"]])->one();
            if ($despesaAtividades == null) {
                $despesaAtividades = new DespesaAtividades();
            }
            $despesaAtividades->despesa_fk = $despesa->id;
            $despesaAtividades->atividade_fk = $despAtv["atividade"] == "" ? NULL : $despAtv["atividade"];
            $despesaAtividades->valor = $despAtv["valor"];
            $despesaAtividades->save();
            array_push($despesaAtividadesIds, $despesaAtividades->id);
        }
        DespesaAtividades::deleteAll(["AND", "despesa_fk = :despesa_fk", ["NOT IN", "id", $despesaAtividadesIds]], ["despesa_fk" => $despesa->id]);

        $result = [];
        if ($rubrica != null) {
            $result["pago"] = round(Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $despesa->rubrica_fk])->sum("valor"), 2);
            $result["receita_total"] = Receita::find()->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $rubrica->centroDeCustoFk->contrato_fk])->sum("valor");
            $result["despesa_total"] = Despesa::find()->join("JOIN", "rubrica", "`despesa`.`rubrica_fk` = `rubrica`.`id`")->join("JOIN", "centro_de_custo", "`rubrica`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $rubrica->centroDeCustoFk->contrato_fk])->sum("despesa_atividades.valor");
        } else {
            $result["pago"] = 0;
            $result["receita_total"] = Receita::find()->where("contrato_fk = :contrato_fk", [":contrato_fk" => $request->post("contrato_id")])->sum("valor");
            $result["despesa_total"] = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("contrato_fk = :contrato_fk", [":contrato_fk" => $request->post("contrato_id")])->sum("despesa_atividades.valor");
        }
        return json_encode($result);
    }

    public function actionManageTax()
    {
        $request = Yii::$app->request;
        $arr = explode('/', $request->post("data"));
        $data = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        if ($request->post("id") == null) {
            $taxa = new Taxa();
        } else {
            $taxa = Taxa::find()->where("id = :id", [":id" => $request->post("id")])->one();
        }
        $taxa->contrato_fk = $request->post("contratoId");
        $taxa->data = $data;
        $taxa->descricao = $request->post("descricao");
        $taxa->fornecedor_fk = $request->post("fornecedor");
        $taxa->valor = $request->post("valor");
        $taxa->taxa = $request->post("taxa") == "T" ? "Tarifa" : "Juros de Poupança";
        $taxa->tipo = $request->post("tipo");
        $taxa->save();

        $result = [];
        $result["tarifa_credito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'C' and taxa = 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        $result["tarifa_debito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'D' and taxa = 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        $result["juros_credito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'C' and taxa != 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        $result["juros_debito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'D' and taxa != 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        return json_encode($result);
    }

    public function actionManageActivity()
    {
        $request = Yii::$app->request;
        $arr = explode('/', $request->post("data"));
        $data = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        if ($request->post("id") == null) {
            $atividade = new Atividade();
        } else {
            $atividade = Atividade::find()->where("id = :id", [":id" => $request->post("id")])->one();
        }
        $atividade->data = $data;
        $atividade->ordem = 0;
        $atividade->descricao = $request->post("descricao");
        $atividade->rubrica_fornecedores_fk = null;
        if ($request->post("rubrica") != "") {
            $atividade->rubrica_fk = $request->post("rubrica");
        } else {
            $atividade->contrato_fk = $request->post("contratoId");
        }
        $atividade->valor = $request->post("valor");
        $atividade->save();
    }

    public function actionLoadIncomeDatatable()
    {
        $request = Yii::$app->request;
        $id = $request->post("id");
        $contrato = Contrato::find()->where("id = :id", [":id" => $id])->one();
        $i = 0;
        $result["data"] = [];
        if ($contrato->centroDeCustos != null) {
            foreach ($contrato->centroDeCustos as $centroDeCusto) {
                foreach ($centroDeCusto->getReceitas()->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all() as $receita) {
                    $data = new \DateTime($receita->data);
                    $result["data"][$i]["id"] = $receita->id;
                    $result["data"][$i]["data"] = $data->format("d/m/Y");
                    $result["data"][$i]["descricao"] = $receita->descricao;
                    $result["data"][$i]["fonte_pagadora"] = $receita->fonte_pagadora;
                    if($receita->rubrica_fk !== null){
                        $rubrica = Rubrica::findOne($receita->rubrica_fk);
                        $result["data"][$i]["rubrica_fk"] = $rubrica->descricao;
                    } else $result["data"][$i]["rubrica_fk"] = "-";
                    $result["data"][$i]["tipo"] = $receita->getTipoDeReceitaFk()->one()->nome;
                    $result["data"][$i]["titulo"] = $receita->getTituloDaReceitaFk()->one()->nome;
                    $result["data"][$i]["parcela"] = $receita->parcela_fk == null ? "" : $receita->parcelaFk->ordem . "º";
                    $result["data"][$i]["valor"] = number_format($receita->valor, 2);
                    $result["data"][$i]["icon"] = '<i class="load-income-info fa fa-question-circle-o"></i>' . ($this->usuario->admin || $this->usuario->admin_lancamentos ? '<i class="edit-income fa fa-edit"></i><i class="remove-income fa fa-times"></i>' : '');
                    $i++;
                }
            }
        } else {
            foreach ($contrato->getReceitas()->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all() as $receita) {
                $data = new \DateTime($receita->data);
                $result["data"][$i]["id"] = $receita->id;
                $result["data"][$i]["data"] = $data->format("d/m/Y");
                $result["data"][$i]["descricao"] = $receita->descricao;
                $result["data"][$i]["fonte_pagadora"] = $receita->fonte_pagadora;
                if($receita->rubrica_fk !== null){
                    $rubrica = Rubrica::findOne($receita->rubrica_fk);
                    $result["data"][$i]["rubrica_fk"] = $rubrica->descricao;
                } else $result["data"][$i]["rubrica_fk"] = "-";
                $result["data"][$i]["tipo"] = $receita->getTipoDeReceitaFk()->one()->nome;
                $result["data"][$i]["titulo"] = $receita->getTituloDaReceitaFk()->one()->nome;
                $result["data"][$i]["parcela"] = $receita->parcela_fk == null ? "" : $receita->parcelaFk->ordem . "º";
                $result["data"][$i]["valor"] = number_format($receita->valor, 2);
                $result["data"][$i]["icon"] = '<i class="load-income-info fa fa-question-circle-o"></i>' . ($this->usuario->admin || $this->usuario->admin_lancamentos ? '<i class="edit-income fa fa-edit"></i><i class="remove-income fa fa-times"></i>' : '');
                $i++;
            }
        }
        return json_encode($result);
    }

    public function actionLoadTaxDatatable()
    {
        $request = Yii::$app->request;
        $id = $request->post("id");
        $taxas = Taxa::find()->where("contrato_fk = :contrato_fk", [":contrato_fk" => $id])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
        $result["data"] = [];
        $i = 0;
        foreach ($taxas as $taxa) {
            $data = new \DateTime($taxa->data);
            $result["data"][$i]["id"] = $taxa->id;
            $result["data"][$i]["data"] = $data->format("d/m/Y");
            $result["data"][$i]["descricao"] = $taxa->descricao;
            $result["data"][$i]["fornecedor"] = $taxa->fornecedorFk->nome;
            $result["data"][$i]["taxa"] = $taxa->taxa;
            $result["data"][$i]["tipo"] = ($taxa->tipo == "C" ? "Crédito" : "Débito");
            $result["data"][$i]["valor"] = number_format($taxa->valor, 2);
            $result["data"][$i]["icon"] = $this->usuario->admin || $this->usuario->admin_lancamentos ? '<i class="edit-tax fa fa-edit"></i><i class="remove-tax fa fa-times"></i>' : '';
            $i++;
        }
        return json_encode($result);
    }

    public function actionLoadActivityDatatable()
    {
        $request = Yii::$app->request;
        if ($request->post("contratoId") == null) {
            $atividades = Atividade::find()->where("rubrica_fk = :rubrica_fk and rubrica_fornecedores_fk is null and contrato_fk is null", [":rubrica_fk" => $request->post("id")])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
        } else {
            $atividades = Atividade::find()->where("contrato_fk = :contrato_fk and rubrica_fornecedores_fk is null and rubrica_fk is null", [":contrato_fk" => $request->post("contratoId")])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
        }
        $result["data"] = [];
        $i = 0;
        foreach ($atividades as $ordem => $atividade) {
            $pago = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("atividade_fk = :atividade_fk", [":atividade_fk" => $atividade->id])->sum("valor");
            $data = new \DateTime($atividade->data);
            $result["data"][$i]["ordem"] = $ordem + 1;
            $result["data"][$i]["id"] = $atividade->id;
            $result["data"][$i]["descricao"] = $atividade->descricao;
            $result["data"][$i]["valor"] = number_format($atividade->valor, 2);
            $result["data"][$i]["pago"] = number_format($pago, 2);
            if (round($atividade->valor, 1) === round($pago, 1)) {
                $result["data"][$i]["status"] = "<span>3</span><i class='fa activity-status fa-check-circle'></i>";
            } else if (round($atividade->valor, 1) > round($pago, 1)) {
                $result["data"][$i]["status"] = "<span>1</span><i class='fa activity-status fa-times-circle'></i>";
            } else {
                $result["data"][$i]["status"] = "<span>2</span><i class='fa activity-status fa-question-circle'></i>";
            }
            $result["data"][$i]["data"] = $data->format("d/m/Y");
            $result["data"][$i]["icon"] = $this->usuario->admin || $this->usuario->admin_lancamentos ? '<i class="edit-activity fa fa-edit"></i><i class="remove-activity fa fa-times"></i>' : '';
            $i++;
        }
        return json_encode($result);
    }

    public
    function actionLoadExpenseDatatable()
    {
        $request = Yii::$app->request;
        if ($request->post("contratoId") == null) {
            $rubrica = Rubrica::find()->where("id = :id", [":id" => $request->post("id")])->one();
            $despesas = Despesa::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
        } else {
            $despesas = Despesa::find()->where("contrato_fk = :contrato_fk", [":contrato_fk" => $request->post("contratoId")])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
        }
        $result = [];
        $result["data"] = $this->buildExpenseArray($despesas);
        return json_encode($result);
    }

    public
    function actionLoadItemInfo()
    {
        $request = Yii::$app->request;
        $rubrica = Rubrica::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $result["admin"] = $this->usuario->admin || $this->usuario->admin_lancamentos;
        $result["rubrica"] = $rubrica->attributes;
        $result["rubrica"]["fonte"] = $rubrica->fonteFk->nome;
        $result["rubrica"]["tipo_de_contrato"] = $rubrica->tipoDeContratoFk != null ? $rubrica->tipoDeContratoFk->nome : null;
        $result["rubrica"]["categoria"] = $rubrica->categoriaFk->nome;
        $result["rubrica"]["pago"] = round(Despesa::find()->join("JOIN", "despesa_atividades", "despesa.id = despesa_atividades.despesa_fk")->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->sum("valor"), 2);
        $result["rubrica"]["vinculante"] = $rubrica->vinculante;
        if (!$rubrica->vinculante) {
            $fornecedores = Fornecedor::find()->orderBy("nome")->all();
        } else {
            $fornecedores = Fornecedor::find()->join("JOIN", "rubrica_fornecedores", "`fornecedor`.`id` = `rubrica_fornecedores`.`fornecedor_fk`")->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->orderBy("nome")->all();
        }
        foreach ($fornecedores as $fornecedor) {
            $identificacao = $fornecedor->cnpj != null ? $fornecedor->cnpj : $fornecedor->cpf;
            $result["fornecedores"][$fornecedor->id] = $fornecedor->nome . "|" . $identificacao . "|" . $fornecedor->tipoDeContratoFk->nome;
        }

        return json_encode($result);
    }

    private
    function buildExpenseArray($despesas)
    {
        $return = [];
        $i = 0;
        foreach ($despesas as $despesa) {
            $data = new \DateTime($despesa->data);
            $return[$i]["id"] = $despesa->id;
            $return[$i]["data"] = $data->format("d/m/Y");
            $return[$i]["descricao"] = $despesa->descricao;
            $return[$i]["competencia"] = $despesa->competencia == null ? "-" : $despesa->competencia;
            $return[$i]["fonte"] = $despesa->fonteFk->nome;
            $return[$i]["fornecedor_id"] = $despesa->fornecedorFk->id;
            $return[$i]["fornecedor"] = $despesa->fornecedorFk->nome;
            $return[$i]["favorecido"] = $despesa->favorecidoFk->nome;
            $return[$i]["atividade"] = "";
            $return[$i]["centro_de_custo"] = $despesa->centro_de_custo == null ? "-" : $despesa->centro_de_custo;
            $return[$i]["valor"] = "";
            foreach ($despesa->despesaAtividades as $key => $despesaAtividade) {
                if ($key != 0) {
                    $return[$i]["atividade"] .= "<br>";
                    $return[$i]["valor"] .= "<br>";
                }
                if ($despesaAtividade->atividade_fk == null) {
                    $return[$i]["atividade"] .= "-";
                } else {
                    if ($despesaAtividade->atividadeFk->rubrica_fornecedores_fk == null) {
                        if ($despesa->contrato_fk == null) {
                            $return[$i]["atividade"] .= "Rubrica: ";
                            $atividades = Atividade::find()->where("rubrica_fk = :rubrica_fk and rubrica_fornecedores_fk is null and contrato_fk is null", [":rubrica_fk" => $despesa->rubrica_fk])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
                        } else {
                            $return[$i]["atividade"] .= "Projeto: ";
                            $atividades = Atividade::find()->where("contrato_fk = :contrato_fk and rubrica_fornecedores_fk is null and rubrica_fk is null", [":contrato_fk" => $despesa->contrato_fk])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
                        }
                        $rowNumber = "?";
                        foreach ($atividades as $key => $atividade) {
                            if ($atividade->id == $despesaAtividade->atividade_fk) {
                                $rowNumber = ($key + 1);
                            }
                        }
                        $return[$i]["atividade"] .= $rowNumber . "º";
                    } else {
                        $return[$i]["atividade"] .= "Contrato: " . $despesaAtividade->atividadeFk->ordem . "º";
                    }
                }
                $return[$i]["valor"] .= "<span>" . number_format($despesaAtividade->valor, 2) . "</span>";
            }
            $return[$i]["icon"] = '<i class="load-expense-info fa fa-question-circle-o"></i>' . ($this->usuario->admin || $this->usuario->admin_lancamentos ? '<i class="edit-expense fa fa-edit"></i><i class="remove-expense fa fa-times"></i>' : '');
            $i++;
        }
        return $return;
    }

    public
    function actionRemoveLaunch()
    {
        $request = Yii::$app->request;
        if ($request->post("type") == "income") {
            $receita = Receita::find()->where("id = :id", [":id" => $request->post("id")])->one();
            $receita->delete();
            return json_encode(["valor" => $receita->valor]);
        } else {
            $despesa = Despesa::find()->where("id = :id", [":id" => $request->post("id")])->one();
            $valor = 0;
            foreach ($despesa->despesaAtividades as $despesaAtividade) {
                $valor += $despesaAtividade->valor;
            }
            $despesa->delete();
            $pago = round(Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $despesa->rubrica_fk])->sum("valor"), 2);
            return json_encode(["valor" => $valor, "pago" => $pago]);
        }
    }

    public function actionRemoveTax()
    {
        $request = Yii::$app->request;
        $taxa = Taxa::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $taxa->delete();
        $result = [];
        $result["tarifa_credito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'C' and taxa = 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        $result["tarifa_debito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'D' and taxa = 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        $result["juros_credito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'C' and taxa != 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        $result["juros_debito_total"] = Taxa::find()->where("contrato_fk = :contrato_fk and tipo = 'D' and taxa != 'Tarifa'", [":contrato_fk" => $request->post("contratoId")])->sum("valor");
        return json_encode($result);
    }

    public function actionRemoveActivity()
    {
        $request = Yii::$app->request;
        $atividade = Atividade::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $despesas = round(Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("atividade_fk = :atividade_fk", [":atividade_fk" => $atividade->id])->sum("despesa_atividades.valor"), 2);
        $atividade->delete();
        $pago = round(Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $request->post("rubrica")])->sum("despesa_atividades.valor"), 2);
        return json_encode(["valor" => $despesas, "pago" => $pago]);
    }

    public
    function actionLoadProviderInfo()
    {
        $request = Yii::$app->request;
        $rubrica = Rubrica::find()->where("id = :idrubrica", [":idrubrica" => $request->post("rubrica")])->one();
        $result["admin"] = $this->usuario->admin || $this->usuario->admin_lancamentos;
        $result["data_inicial"] = $rubrica->centroDeCustoFk->data_inicial;
        $result["data_final"] = $rubrica->centroDeCustoFk->data_final;
        if ($rubrica->vinculante) {
            $rubricaFornecedores = RubricaFornecedores::find()->where("fornecedor_fk = :fornecedor_fk and rubrica_fk = :rubrica_fk", [":fornecedor_fk" => $request->post("id"), ":rubrica_fk" => $request->post("rubrica")])->orderBy("ordem")->all();
            $j = 1;
            $atividadesRubrica = Atividade::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
            foreach ($atividadesRubrica as $atividadeRubrica) {
                $pago = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("atividade_fk = :atividade_fk", [":atividade_fk" => $atividadeRubrica->id])->sum("valor");
                if (round($atividadeRubrica->valor) !== round($pago)) {
                    $result["atividades_rubrica"][$j] = $atividadeRubrica->id . "|" . $atividadeRubrica->descricao . "|" . $atividadeRubrica->valor . "|" . $pago . "|" . $j . "|" . $atividadeRubrica->data;
                }
                $j++;
            }
            $i = 0;
            foreach ($rubricaFornecedores as $rubricaFornecedor) {
                $result["fornecedor"] = $rubricaFornecedor->fornecedorFk->attributes;
                $result["tipo_de_contrato"] = $rubricaFornecedor->fornecedorFk->tipoDeContratoFk->nome;
                $result["rubrica"] = $rubricaFornecedor->rubricaFk->descricao;
                $result["contaBancaria"] = new ContaBancaria();
                if ($rubricaFornecedor->fornecedorFk->conta_bancaria_fk != null) {
                    $result["contaBancaria"] = $rubricaFornecedor->fornecedorFk->contaBancariaFk->attributes;
                    $result["contaBancaria"]["banco"] = $rubricaFornecedor->fornecedorFk->contaBancariaFk->bancoFk != null ? $rubricaFornecedor->fornecedorFk->contaBancariaFk->bancoFk->codigo . " - " . $rubricaFornecedor->fornecedorFk->contaBancariaFk->bancoFk->nome_abreviado : null;
                }
                $array = $rubricaFornecedor->attributes;
                $query = new Query();
                $paid = $query->select([
                    "valor" => "round(if(sum(despesa_atividades.valor) is null, 0, sum(despesa_atividades.valor)), 2)"])
                    ->from("despesa")
                    ->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")
                    ->join("JOIN", "atividade", "`despesa_atividades`.`atividade_fk` = `atividade`.`id`")
                    ->where("despesa.fornecedor_fk = :fornecedor_fk and despesa.rubrica_fk = :rubrica_fk and atividade.rubrica_fornecedores_fk = :rubrica_fornecedores_fk")
                    ->params([":fornecedor_fk" => $request->post("id"), ":rubrica_fk" => $request->post("rubrica"), ":rubrica_fornecedores_fk" => $rubricaFornecedor->id])->one();
                $array["valor_pago"] = $paid["valor"];
                $array["valor_restante"] = $array["valor_total"] - $array["valor_pago"];

                $atividadesFornecedor = Atividade::find()->where("rubrica_fornecedores_fk = :rubrica_fornecedores_fk", [":rubrica_fornecedores_fk" => $rubricaFornecedor->id])->orderBy(['rubrica_fornecedores_fk' => SORT_ASC, 'ordem' => SORT_ASC])->all();
                foreach ($atividadesFornecedor as $atividadeFornecedor) {
                    if (!isset($array["atividades"])) {
                        $array["atividades"] = [];
                    }
                    $pago = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("atividade_fk = :atividade_fk", [":atividade_fk" => $atividadeFornecedor->id])->sum("valor");
                    if (round($atividadeFornecedor->valor) !== round($pago)) {
                        $result["atividades_fornecedor"][$i] = $atividadeFornecedor->id . "|" . $atividadeFornecedor->descricao . "|" . $atividadeFornecedor->valor . "|" . $pago . "|" . $atividadeFornecedor->ordem . "|" . $atividadeFornecedor->data;
                    }
                    array_push($array["atividades"], $atividadeFornecedor->attributes);
                    $i++;
                }

                if (!isset($result["rubrica_fornecedores"])) {
                    $result["rubrica_fornecedores"] = [];
                }
                array_push($result["rubrica_fornecedores"], $array);
            }
            return json_encode($result);
        } else {
            $j = 1;
            $atividadesRubrica = Atividade::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->orderBy(['data' => SORT_ASC, 'id' => SORT_ASC])->all();
            foreach ($atividadesRubrica as $atividadeRubrica) {
                $pago = Despesa::find()->join("JOIN", "despesa_atividades", "`despesa`.`id` = `despesa_atividades`.`despesa_fk`")->where("atividade_fk = :atividade_fk", [":atividade_fk" => $atividadeRubrica->id])->sum("valor");
                if (round($atividadeRubrica->valor) !== round($pago)) {
                    $result["atividades_rubrica"][$j] = $atividadeRubrica->id . "|" . $atividadeRubrica->descricao . "|" . $atividadeRubrica->valor . "|" . $pago . "|" . $j . "|" . $atividadeRubrica->data;
                }
                $j++;
            }
            return json_encode($result);
        }
    }

    public function actionUploadFile()
    {
        $uploadDir = $this->mediasBaseDir . 'financeiro' . DIRECTORY_SEPARATOR . $_POST["id"] . DIRECTORY_SEPARATOR;
        $md5FileName = md5($_FILES['file']['name'] . time());
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $uploadFile = $uploadDir . $md5FileName . "." . $ext;

        $dirCreated = false;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            $dirCreated = true;
        }
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $midia = new Midia();
            $midia->contrato_fk = $_POST["id"];
            $midia->link = "cdn.guigoh.com" . DIRECTORY_SEPARATOR . "financeiro" . DIRECTORY_SEPARATOR . $_POST["id"] . DIRECTORY_SEPARATOR . $md5FileName . "." . $ext;
            $midia->nome = $_FILES['file']['name'];
            $midia->tipo = $_FILES['file']['type'];
            $midia->tamanho = $_FILES['file']['size'];
            $midia->nome_falso = $_POST["falseName"];
            $midia->save();
            return json_encode(["valid" => true, "message" => "Arquivo anexado com sucesso!", "midia" => $midia->attributes]);
        } else {
            if ($dirCreated) {
                rmdir($uploadDir);
            }
            return json_encode(["valid" => false, "message" => "Não foi possível anexar o arquivo. Tente novamente."]);
        }
    }

    public function actionRemoveFile()
    {
        $request = Yii::$app->request;
        $midia = Midia::find()->where("id = :id", ["id" => $request->post("id")])->one();
        if (unlink($this->mediasBaseDir . 'financeiro' . DIRECTORY_SEPARATOR . $midia->contrato_fk . DIRECTORY_SEPARATOR . basename(parse_url($midia->link)['path']))) {
            if ($this->isEmptyDir($this->mediasBaseDir . 'financeiro' . DIRECTORY_SEPARATOR . $midia->contrato_fk . DIRECTORY_SEPARATOR)) {
                rmdir($this->mediasBaseDir . 'financeiro' . DIRECTORY_SEPARATOR . $midia->contrato_fk . DIRECTORY_SEPARATOR);
            }
            $midia->delete();
        }
    }

    private function isEmptyDir($dir)
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return FALSE;
            }
        }
        closedir($handle);
        return TRUE;
    }

    /**
     * Get classrooms by school from TAG
     * 
     * @return array
     */
    private function getTypeOfExpense()
    {
        $query = "select * from `tipo_de_despesa`";
        $result = \Yii::$app->db->createCommand($query);
        return $result->queryAll();
    }

    /**
     * Get a classrooms.
     * 
     * @return json
     */
    public function actionGetTypeOfExpense()
    {
        $expenses = $this->getTypeOfExpense();

        $response = [];
        foreach ($expenses as $expense) {
            $response[$expense['nome']] = $expense['id'];
        }

        echo \yii\helpers\Json::encode($response);
        exit;
    }
}
