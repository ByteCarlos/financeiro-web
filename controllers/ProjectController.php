<?php

namespace app\controllers;

use app\models\Bancos;
use app\models\Categoria;
use app\models\CentroDeCusto;
use app\models\Coordenador;
use app\models\Fonte;
use app\models\Parcela;
use app\models\TipoDeContrato;
use app\models\Usuario;
use PHPMailer\PHPMailer\PHPMailer;
use Yii;
use yii\base\BaseObject;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

use app\models\Contrato;
use app\models\ContaBancaria;
use app\models\Despesa;
use app\models\Item;
use app\models\Receita;
use app\models\Rubrica;
use app\models\RubricaFornecedores;
use app\models\Atividade;

class ProjectController extends GlobalController
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if ($this->usuario->admin || $this->usuario->admin_lancamentos || $this->usuario->admin_projetos || $this->usuario->admin_fornecedores || $this->usuario->assessor) {
            $contratos = Contrato::find()->all();
        } else {
            $contratos = Contrato::find()->join("JOIN", "coordenador", "contrato_fk = contrato.id")->where("usuario_fk = :usuario_fk", ["usuario_fk" => $this->usuario->id])->all();
        }
        $propostasPendentes = \Yii::$app->db2->createCommand("select * from proposta where exportado_financeiro = 'P'")->queryAll();
        $bancosArray = [];
        foreach (Bancos::find()->all() as $banco) {
            $bancosArray[$banco->id] = $banco->codigo . " - " . $banco->nome_abreviado;
        }
        return $this->render('index', [
            'contrato' => new Contrato(), 'itensDoContrato' => ArrayHelper::map($contratos, 'id', 'nome'),
            'categoria' => new Categoria(), 'itensDeCategoria' => ArrayHelper::map(Categoria::find()->all(), 'id', 'nome'),
            'coordenador' => new Coordenador(), 'itensDeCoordenador' => ArrayHelper::map(Usuario::find()->all(), 'id', 'nome'),
            'propostasPendentes' => $propostasPendentes,
            'bancos' => $bancosArray
        ]);
    }

    public function actionLoadContractTypesAndSources()
    {
        $tiposDeContrato = [];
        foreach (TipoDeContrato::find()->all() as $tipoDeContrato) {
            $tiposDeContrato[$tipoDeContrato->id] = $tipoDeContrato->nome;
        }
        $fontes = [];
        foreach (Fonte::find()->all() as $fonte) {
            $fontes[$fonte->id] = $fonte->nome;
        }
        return json_encode(["tipos_de_contrato" => $tiposDeContrato, "fontes" => $fontes]);
    }

    public function actionLoadContractInfo()
    {
        /** @var Contrato $contrato */
        $request = Yii::$app->request;
        $id = $request->post("id");
        $contrato = Contrato::find()->where("id = :id", [":id" => $id])->one();
        if ($contrato != null) {
            $result = [];
            $result["contrato"]["nome"] = $contrato->nome;
            $result["contrato"]["apoiadora"] = $contrato->apoiadora;
            $result["contrato"]["origem_publica"] = $contrato->origem_publica;
            $result["contrato"]["data_inicial"] = $contrato->data_inicial;
            $result["contrato"]["data_final"] = $contrato->data_final;
            $result["contrato"]["parcelas"] = [];
            foreach ($contrato->parcelas as $parcela) {
                array_push($result["contrato"]["parcelas"], $parcela->attributes);
            }
            if ($contrato->conta_bancaria_fk != null) {
                $result["contrato"]["conta_bancaria"] = $contrato->contaBancariaFk->attributes;
                $result["contrato"]["conta_bancaria"]["banco"] = $contrato->contaBancariaFk->bancoFk != null ? $contrato->contaBancariaFk->bancoFk->codigo . " - " . $contrato->contaBancariaFk->bancoFk->nome_abreviado : null;
            }
            $coordenadores = [];
            foreach ($contrato->coordenadors as $coordenador) {
                $coordenadorArray = $coordenador->attributes;
                $coordenadorArray["nome"] = $coordenador->usuarioFk->nome;
                array_push($coordenadores, $coordenadorArray);
            }
            $result["contrato"]["coordenadores"] = $coordenadores;
            $result["contrato"]["importado"] = Yii::$app->db2->createCommand('SELECT * FROM proposta WHERE exportado_financeiro_id = :exportado_financeiro_id')->bindValue("exportado_financeiro_id", $contrato->id)->queryOne();
            foreach ($contrato->getCentroDeCustos()->all() as $centroDeCusto) {
                $result["contrato"]["centro_de_custo"][$centroDeCusto->ordem] = $centroDeCusto->attributes;
                $result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"] = [];
                foreach ($centroDeCusto->getRubricas()->all() as $rubrica) {
                    $hasCategory = false;
                    foreach ($result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"] as $index => $category) {
                        if ($category["id"] == $rubrica->categoriaFk->id) {
                            $categoryIndex = $index;
                            $hasCategory = true;
                        }
                    }
                    if (!$hasCategory) {
                        array_push($result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"], $rubrica->categoriaFk->attributes);
                        end($result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"]);
                        $categoryIndex = key($result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"]);
                    }

                    $rubricaFornecedores = RubricaFornecedores::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->all();
                    $valorUtilizadoContratos = 0;
                    foreach ($rubricaFornecedores as $rubricaFornecedor) {
                        $valorUtilizadoContratos += $rubricaFornecedor->valor_total;
                    }
                    $despesas = Despesa::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->all();
                    $valorUtilizadoLancamentos = 0;
                    foreach ($despesas as $despesa) {
                        foreach ($despesa->despesaAtividades as $despesaAtividade) {
                            $valorUtilizadoLancamentos += $despesaAtividade->valor;
                        }
                    }
                    $rubricaArray = $rubrica->attributes;
                    $rubricaArray["tipo_de_contrato"] = $rubrica->tipoDeContratoFk != null ? $rubrica->tipoDeContratoFk->nome : null;
                    $rubricaArray["fonte"] = $rubrica->fonteFk != null ? $rubrica->fonteFk->nome : null;
                    $rubricaArray["valor_utilizado_contratos"] = $valorUtilizadoContratos;
                    $rubricaArray["valor_utilizado_lancamentos"] = $valorUtilizadoLancamentos;
                    if (!isset($result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"][$categoryIndex]["rubricas"])) {
                        $result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"][$categoryIndex]["rubricas"] = [];
                    }
                    array_push($result["contrato"]["centro_de_custo"][$centroDeCusto->ordem]["categorias"][$categoryIndex]["rubricas"], $rubricaArray);
                }
            }
            return json_encode($result);
        }
    }

    public function actionManageProject()
    {
        $request = Yii::$app->request;
        if ($request->post("id") == "") {
            $contrato = new Contrato();
        } else {
            $contrato = Contrato::find()->where("id = :id", [":id" => $request->post("id")])->one();
            if ($contrato->conta_bancaria_fk != null && $request->post("agencia") == "") {
                $contrato->contaBancariaFk->delete();
            }
        }

        $contaBancaria = ($contrato->conta_bancaria_fk == null ? new ContaBancaria() : $contrato->contaBancariaFk);
        $contaBancaria->agencia = $request->post("agencia") == "" ? NULL : $request->post("agencia");
        $contaBancaria->tipo_de_conta = $request->post("tipo_de_conta") == "" ? NULL : $request->post("tipo_de_conta");
        $contaBancaria->banco_fk = $request->post("banco") == "" ? NULL : $request->post("banco");
        $contaBancaria->conta = $request->post("conta") == "" ? NULL : $request->post("conta");
        $contaBancaria->proprietario = $request->post("proprietario") == "" ? NULL : $request->post("proprietario");
        $contaBancaria->pix = $request->post("pix") == "" ? NULL : $request->post("pix");
        $contaBancaria->save();

        $contrato->nome = $request->post("nome");
        $contrato->apoiadora = $request->post("apoiadora") == "" ? NULL : $request->post("apoiadora");
        $contrato->origem_publica = $request->post("origem_publica") == "true" ? 1 : 0;
        $contrato->conta_bancaria_fk = $contaBancaria->id;
        $contrato->save();

        $parcelaIds = [];
        foreach ($request->post("parcelas") as $p) {
            $parcela = Parcela::find()->where("id = :id", [":id" => $p["id"]])->one();
            if ($parcela == null) {
                $parcela = new Parcela();
            }
            $parcela->contrato_fk = $contrato->id;
            $parcela->descricao = $p["descricao"];
            $parcela->fonte_pagadora = $p["fontePagadora"];
            $parcela->valor = $p["valor"];
            $parcela->ordem = $p["ordem"];
            $arr = explode('/', $p["data"]);
            $plotDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            $parcela->data = $plotDate;
            $parcela->save();
            array_push($parcelaIds, $parcela->id);
        }
        Parcela::deleteAll(["AND", "contrato_fk = :contrato_fk", ["NOT IN", "id", $parcelaIds]], ["contrato_fk" => $contrato->id]);

        Coordenador::deleteAll("contrato_fk = :contrato_fk", ["contrato_fk" => $contrato->id]);
        $bulkInsertArray = [];
        foreach ($request->post("coordenadores") as $coordenadorId) {
            $coordenador = new Coordenador();
            $coordenador->usuario_fk = $coordenadorId;
            $coordenador->contrato_fk = $contrato->id;
            array_push($bulkInsertArray, $coordenador->attributes);
        }
        $columnNameArray = ['id', 'usuario_fk', 'contrato_fk'];
        $insertCount = Yii::$app->db->createCommand()
            ->batchInsert("coordenador", $columnNameArray, $bulkInsertArray)->execute();

        if ($request->post("centros_de_custo_removidos") != null) {
            foreach ($request->post("centros_de_custo_removidos") as $cc) {
                $receitas = Receita::find()->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->where("centro_de_custo_fk = :centro_de_custo_fk", [":centro_de_custo_fk" => $cc["id"]])->all();
                $centroDeCusto = CentroDeCusto::find()->where("id = :id", [":id" => $cc["id"]])->one();
                $centroDeCustoAnterior = CentroDeCusto::find()->where("contrato_fk = :contrato_fk and ordem = :ordem", [":contrato_fk" => $contrato->id, ":ordem" => $centroDeCusto->ordem - 1])->one();
                foreach ($receitas as $receita) {
                    $receita->centro_de_custo_fk = $centroDeCustoAnterior->id;
                    $receita->save();
                }
                $centroDeCusto->delete();
            }
        }

        if ($request->post("rubricas_removidas") != null) {
            foreach ($request->post("rubricas_removidas") as $rubrica) {
                Rubrica::deleteAll("id = :id", [":id" => $rubrica["id"]]);
            }
        }

        $cc = $request->post("centro_de_custo");
        $addictive = false;
        $centroDeCusto = CentroDeCusto::find()->where("id = :id", [":id" => $cc["id"]])->one();
        if ($centroDeCusto == null) {
            $addictive = true;
            $centroDeCusto = new CentroDeCusto();
        } else {
            $oldCentroDeCusto = new CentroDeCusto();
            $oldCentroDeCusto->contrato_fk = $contrato->id;
            $oldCentroDeCusto->data_inicial = $centroDeCusto->data_inicial;
            $oldCentroDeCusto->data_final = $centroDeCusto->data_final;
        }

        $celebrationDate = null;
        if ($cc["celebracao_termo_aditivo"] != "") {
            $arr = explode('/', $cc["celebracao_termo_aditivo"]);
            $celebrationDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        $arr = explode('/', $cc["data_inicial"]);
        $initialDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        $arr = explode('/', $cc["data_final"]);
        $finalDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        $centroDeCusto->contrato_fk = $contrato->id;
        $centroDeCusto->ordem = $cc["ordem"];
        $centroDeCusto->celebracao_termo_aditivo = $celebrationDate;
        $centroDeCusto->data_inicial = $initialDate;
        $centroDeCusto->data_final = $finalDate;
        $centroDeCusto->valor_total = $cc["valor_total"];
        $centroDeCusto->save();

        foreach ($cc["rubricas"] as $r) {
            $rubrica = null;
            if (!$addictive) {
                $rubrica = Rubrica::find()->where("id = :id", [":id" => $r["id"]])->one();
            }
            if ($rubrica == null) {
                $rubrica = new Rubrica();
            }
            $rubrica->categoria_fk = $r["categoria"];
            $rubrica->centro_de_custo_fk = $centroDeCusto->id;
            $rubrica->descricao = $r["descricao"];
            $rubrica->valor_total = $r["valor_total"];
            $rubrica->tipo_de_contrato_fk = $r["tipo_de_contrato"];
            $rubrica->fonte_fk = $r["fonte"];
            $rubrica->vinculante = $r["vinculante"];
            $rubrica->importado_projeto_id = $r["proposta_id"] == "" ? null : $r["proposta_id"];
            $rubrica->save();

            if ($addictive) {
                Rubrica::updateAll(["importado_projeto_id" => null], "id = :id", ["id" => $r["id"]]);
                $rubricaFornecedores = RubricaFornecedores::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $r["id"]])->all();
                foreach ($rubricaFornecedores as $rubricaFornecedor) {
                    $rubricaFornecedor->rubrica_fk = $rubrica->id;
                    $rubricaFornecedor->save();
                }
                $despesas = Despesa::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $r["id"]])->all();
                foreach ($despesas as $despesa) {
                    $despesa->rubrica_fk = $rubrica->id;
                    $despesa->save();
                }
                $atividades = Atividade::find()->where("rubrica_fk = :rubrica_fk and rubrica_fornecedores_fk is null and contrato_fk is null", [":rubrica_fk" => $r["id"]])->all();
                foreach ($atividades as $atividade) {
                    $atividade->rubrica_fk = $rubrica->id;
                    $atividade->save();
                }
            }
        }

        if ($addictive) {
            $rubricaFornecedores = RubricaFornecedores::find()->join("JOIN", "rubrica", "rubrica.id = rubrica_fornecedores.rubrica_fk")->join("JOIN", "centro_de_custo", "centro_de_custo.id = rubrica.centro_de_custo_fk")->where("centro_de_custo.ordem = :ordem and contrato_fk = :contrato_fk", ["ordem" => $cc["ordem"] - 1, "contrato_fk" => $contrato->id])->all();
            foreach ($rubricaFornecedores as $rubricaFornecedor) {
                $rubricaFornecedor->delete();
            }
            $despesas = Despesa::find()->join("JOIN", "rubrica", "rubrica.id = despesa.rubrica_fk")->join("JOIN", "centro_de_custo", "centro_de_custo.id = rubrica.centro_de_custo_fk")->where("centro_de_custo.ordem = :ordem and centro_de_custo.contrato_fk = :contrato_fk", ["ordem" => $cc["ordem"] - 1, "contrato_fk" => $contrato->id])->all();
            foreach ($despesas as $despesa) {
                $despesa->delete();
            }
            $atividades = Atividade::find()->join("JOIN", "rubrica", "rubrica.id = atividade.rubrica_fk")->join("JOIN", "centro_de_custo", "centro_de_custo.id = rubrica.centro_de_custo_fk")->where("centro_de_custo.ordem = :ordem and centro_de_custo.contrato_fk = :contrato_fk", ["ordem" => $cc["ordem"] - 1, "contrato_fk" => $contrato->id])->all();
            foreach ($atividades as $atividade) {
                $atividade->delete();
            }
            $receitas = Receita::find()->join("JOIN", "centro_de_custo", "`receita`.`centro_de_custo_fk` = `centro_de_custo`.`id`")->where("centro_de_custo.contrato_fk = :contrato_fk", [":contrato_fk" => $contrato->id])->all();
            foreach ($receitas as $receita) {
                $receita->centro_de_custo_fk = $centroDeCusto->id;
                $receita->save();
            }
        }

        if ($request->post("proposta_id") != "") {
            $this->exportToGestao($request->post("proposta_id"), $request->post("coordenadores"), $contrato, $centroDeCusto);
            Yii::$app->db2->createCommand('UPDATE proposta SET exportado_financeiro = "Y", exportado_financeiro_id = :exportado_financeiro_id, exportado_financeiro_justificativa = null WHERE id=:id')->bindValues(["id" => $request->post("proposta_id"), "exportado_financeiro_id" => $contrato->id])->execute();
        } else {
            $proposalDB2 = Yii::$app->db2->createCommand('SELECT * FROM proposta WHERE exportado_financeiro_id = :exportado_financeiro_id')->bindValue("exportado_financeiro_id", $contrato->id)->queryOne();
            if ($proposalDB2 != null) {
                if ($proposalDB2["proposta_pai_fk"] != null) {
                    $proposalDB2 = $this->getMostRecentProposal($proposalDB2["proposta_pai_fk"]);
                }
                $this->changeDB2AndDB3Dates($proposalDB2, $oldCentroDeCusto->data_inicial, $oldCentroDeCusto->data_final, $centroDeCusto);
                $projetoDB3 = Yii::$app->db3->createCommand('SELECT * FROM projeto WHERE importado_financeiro_id = :importado_financeiro_id')->bindValue("importado_financeiro_id", $contrato->id)->queryOne();
                if ($projetoDB3 != null) {
                    $this->exportCoordinators($request->post("coordenadores"), $projetoDB3["id"]);
                }
            }
        }

        $result = ["contrato" => $contrato->attributes];
        return json_encode($result);
    }

    private function getMostRecentProposal($parent)
    {
        $proposalDB2 = Yii::$app->db2->createCommand("select * from proposta where id = :id")->bindValue("id", $parent)->queryOne();
        if ($proposalDB2["proposta_pai_fk"] == null) {
            return $proposalDB2;
        } else {
            return $this->getMostRecentProposal($proposalDB2["proposta_pai_fk"]);
        }
    }

    private function changeDB2AndDB3Dates($proposalDB2, $oldInitialDate, $oldFinalDate, $centroDeCusto)
    {
        $oldInitialDate = new \DateTime($oldInitialDate);
        $oldFinalDate = new \DateTime($oldFinalDate);
        $newInitialDate = new \DateTime($centroDeCusto->data_inicial);
        $newFinalDate = new \DateTime($centroDeCusto->data_final);
        $diffInitialDate = $oldInitialDate->diff($newInitialDate)->format("%r%a");
        $diffFinalDate = $oldFinalDate->diff($newFinalDate)->format("%r%a");
        Yii::$app->db2->createCommand('UPDATE proposta SET data_inicial = DATE_ADD(data_inicial, INTERVAL :diffInitialDate DAY), data_final = DATE_ADD(data_final, INTERVAL :diffFinalDate DAY) WHERE id = :id')->bindValues(["id" => $proposalDB2["id"], "diffInitialDate" => $diffInitialDate, "diffFinalDate" => $diffFinalDate])->execute();
        Yii::$app->db2->createCommand('UPDATE etapa JOIN meta ON etapa.meta_fk = meta.id JOIN acao ON meta.acao_fk = acao.id SET data_inicial = DATE_ADD(data_inicial, INTERVAL :diffInitialDate DAY), data_final = DATE_ADD(data_final, INTERVAL :diffInitialDate DAY) WHERE acao.proposta_fk = :id')->bindValues(["id" => $proposalDB2["id"], "diffInitialDate" => $diffInitialDate])->execute();
        Yii::$app->db2->createCommand('UPDATE produto JOIN etapa ON produto.etapa_fk = etapa.id JOIN meta ON etapa.meta_fk = meta.id JOIN acao ON meta.acao_fk = acao.id SET data_de_entrega = DATE_ADD(data_de_entrega, INTERVAL :diffInitialDate DAY) WHERE acao.proposta_fk = :id')->bindValues(["id" => $proposalDB2["id"], "diffInitialDate" => $diffInitialDate])->execute();
        $projectDB3 = Yii::$app->db3->createCommand('SELECT * FROM projeto WHERE importado_financeiro_id = :importado_financeiro_id')->bindValue("importado_financeiro_id", $centroDeCusto->contrato_fk)->queryOne();
        Yii::$app->db3->createCommand('UPDATE projeto SET data_inicial = DATE_ADD(data_inicial, INTERVAL :diffInitialDate DAY), data_final = DATE_ADD(data_final, INTERVAL :diffFinalDate DAY) WHERE id = :id')->bindValues(["id" => $projectDB3["id"], "diffInitialDate" => $diffInitialDate, "diffFinalDate" => $diffFinalDate])->execute();
        Yii::$app->db3->createCommand('UPDATE etapa JOIN meta ON etapa.meta_fk = meta.id JOIN acao ON meta.acao_fk = acao.id SET data_inicial = DATE_ADD(data_inicial, INTERVAL :diffInitialDate DAY), data_final = DATE_ADD(data_final, INTERVAL :diffInitialDate DAY) WHERE acao.projeto_fk = :id')->bindValues(["id" => $projectDB3["id"], "diffInitialDate" => $diffInitialDate])->execute();
        Yii::$app->db3->createCommand('UPDATE produto JOIN etapa ON produto.etapa_fk = etapa.id JOIN meta ON etapa.meta_fk = meta.id JOIN acao ON meta.acao_fk = acao.id SET data_de_entrega = DATE_ADD(data_de_entrega, INTERVAL :diffInitialDate DAY) WHERE acao.projeto_fk = :id')->bindValues(["id" => $projectDB3["id"], "diffInitialDate" => $diffInitialDate])->execute();
        Yii::$app->db3->createCommand('UPDATE atividade JOIN indicador ON atividade.indicador_fk = indicador.id JOIN produto ON indicador.produto_fk = produto.id JOIN etapa ON produto.etapa_fk = etapa.id JOIN meta ON etapa.meta_fk = meta.id JOIN acao ON meta.acao_fk = acao.id SET atividade.data = DATE_ADD(atividade.data, INTERVAL :diffInitialDate DAY) WHERE acao.projeto_fk = :id')->bindValues(["id" => $projectDB3["id"], "diffInitialDate" => $diffInitialDate])->execute();
    }

    private function exportToGestao($propostaId, $coordenadores, $contrato, $centroDeCusto)
    {
        $proposalDB2 = Yii::$app->db2->createCommand('SELECT * FROM proposta WHERE id = :id')->bindValue("id", $propostaId)->queryOne();
        $projetoDB3 = Yii::$app->db3->createCommand('SELECT * FROM projeto WHERE importado_financeiro_id = :importado_financeiro_id')->bindValue("importado_financeiro_id", $contrato->id)->queryOne();
        if ($projetoDB3 == null) {
            Yii::$app->db3->createCommand()->insert('projeto', [
                'nome' => $proposalDB2["nome"],
                'cliente' => $proposalDB2["cliente"],
                'data_inicial' => $proposalDB2["data_inicial"],
                'data_final' => $proposalDB2["data_final"],
                'importado_financeiro_id' => $contrato->id,
            ])->execute();
            $projetoDB3Id = Yii::$app->db3->getLastInsertID();
            $propostaElaboracoesDB2 = Yii::$app->db2->createCommand('SELECT * FROM proposta_elaboracao WHERE proposta_fk = :proposta_fk')->bindValue("proposta_fk", $proposalDB2["id"])->queryAll();
            foreach ($propostaElaboracoesDB2 as $propostaElaboracaoDB2) {
                Yii::$app->db3->createCommand()->insert('projeto_campos', [
                    'projeto_fk' => $projetoDB3Id,
                    'titulo' => $propostaElaboracaoDB2["titulo"],
                    'descricao' => $propostaElaboracaoDB2["descricao"],
                ])->execute();
            }
            $this->exportActionsToGestao($proposalDB2["id"], $projetoDB3Id);
        } else {
            $projetoDB3Id = $projetoDB3["id"];
            Yii::$app->db3->createCommand('UPDATE projeto SET data_inicial = :data_inicial, data_final = :data_final WHERE id = :id')->bindValues(["id" => $projetoDB3Id, "data_inicial" => $proposalDB2["data_inicial"], "data_final" => $proposalDB2["data_final"]])->execute();
            $indicadoresDeletadosDB3 = Yii::$app->db3
                ->createCommand('SELECT indicador.* FROM acao JOIN meta ON meta.acao_fk = acao.id JOIN etapa ON etapa.meta_fk = meta.id JOIN produto ON produto.etapa_fk = etapa.id JOIN indicador ON indicador.produto_fk = produto.id WHERE indicador.importado_projeto_id IS NULL AND projeto_fk = :projeto_fk')
                ->bindValues(["projeto_fk" => $projetoDB3Id])->queryAll();
            if ($indicadoresDeletadosDB3 != null) {
                foreach ($indicadoresDeletadosDB3 as $indicadorDeletadoDB3) {
                    if (is_dir($this->mediasBaseDir . 'gestao' . DIRECTORY_SEPARATOR . $projetoDB3Id . DIRECTORY_SEPARATOR . $indicadorDeletadoDB3["id"] . DIRECTORY_SEPARATOR)) {
                        $this->rrmdir($this->mediasBaseDir . 'gestao' . DIRECTORY_SEPARATOR . $projetoDB3Id . DIRECTORY_SEPARATOR . $indicadorDeletadoDB3["id"] . DIRECTORY_SEPARATOR);
                    }
                }
            }
            Yii::$app->db3->createCommand("DELETE FROM acao WHERE projeto_fk = :projeto_fk AND acao.importado_projeto_id IS NULL")->bindValues(["projeto_fk" => $projetoDB3Id])->execute();
            Yii::$app->db3->createCommand("DELETE m FROM meta m JOIN acao ON acao.id = m.acao_fk WHERE projeto_fk = :projeto_fk AND m.importado_projeto_id IS NULL")->bindValues(["projeto_fk" => $projetoDB3Id])->execute();
            Yii::$app->db3->createCommand("DELETE e FROM etapa e JOIN meta ON meta.id = e.meta_fk JOIN acao ON acao.id = meta.acao_fk WHERE projeto_fk = :projeto_fk AND e.importado_projeto_id IS NULL")->bindValues(["projeto_fk" => $projetoDB3Id])->execute();
            Yii::$app->db3->createCommand("DELETE p FROM produto p JOIN etapa ON etapa.id = p.etapa_fk JOIN meta ON meta.id = etapa.meta_fk JOIN acao ON acao.id = meta.acao_fk WHERE projeto_fk = :projeto_fk AND p.importado_projeto_id IS NULL")->bindValues(["projeto_fk" => $projetoDB3Id])->execute();
            Yii::$app->db3->createCommand("DELETE i FROM indicador i JOIN produto ON produto.id = i.produto_fk JOIN etapa ON etapa.id = produto.etapa_fk JOIN meta ON meta.id = etapa.meta_fk JOIN acao ON acao.id = meta.acao_fk WHERE projeto_fk = :projeto_fk AND i.importado_projeto_id IS NULL")->bindValues(["projeto_fk" => $projetoDB3Id])->execute();
            $this->exportActionsToGestao($proposalDB2["id"], $projetoDB3Id);
        }
        $this->changeDB2AndDB3Dates($proposalDB2, $proposalDB2["data_inicial"], $proposalDB2["data_final"], $centroDeCusto);
        $this->exportCoordinators($coordenadores, $projetoDB3Id);
    }

    private function exportCoordinators($coordenadores, $projetoDB3Id)
    {
        $coordenadoresDB3 = [];
        foreach ($coordenadores as $key => $coordenador) {
            $usuario = Usuario::find()->where("id = :id", ["id" => $coordenador])->one();
            $usuarioDB3 = Yii::$app->db3->createCommand('SELECT * FROM usuario WHERE email=:email')->bindValue("email", $usuario->email)->queryOne();
            array_push($coordenadoresDB3, $usuarioDB3["id"]);
        }
        Yii::$app->db3->createCommand("DELETE FROM projeto_coordenadores WHERE projeto_fk = :projeto_fk AND usuario_fk NOT IN ('" . implode("', '", $coordenadoresDB3) . "')")->bindValues(["projeto_fk" => $projetoDB3Id])->execute();
        foreach ($coordenadoresDB3 as $cDB3) {
            $projetoCoordenadorDB3 = Yii::$app->db3->createCommand('SELECT * FROM projeto_coordenadores WHERE projeto_fk = :projeto_fk AND usuario_fk = :usuario_fk')->bindValues(["projeto_fk" => $projetoDB3Id, "usuario_fk" => $cDB3])->queryOne();
            if ($projetoCoordenadorDB3 == null) {
                Yii::$app->db3->createCommand()->insert('projeto_coordenadores', [
                    'usuario_fk' => $cDB3,
                    'projeto_fk' => $projetoDB3Id
                ])->execute();
            }
        }
    }

    private function exportActionsToGestao($proposalDB2Id, $projectDB3Id)
    {
        $acoesDB2 = Yii::$app->db2->createCommand('SELECT * FROM acao WHERE proposta_fk = :proposta_fk')->bindValue("proposta_fk", $proposalDB2Id)->queryAll();
        foreach ($acoesDB2 as $acaoDB2) {
            $acaoDB3 = Yii::$app->db3->createCommand('SELECT * FROM acao WHERE importado_projeto_id = :importado_projeto_id')->bindValue("importado_projeto_id", $acaoDB2["id"])->queryOne();
            if ($acaoDB3 == null) {
                Yii::$app->db3->createCommand()->insert('acao', [
                    'numero' => $acaoDB2["numero"],
                    'projeto_fk' => $projectDB3Id,
                    'descricao' => $acaoDB2["descricao"],
                    'importado_projeto_id' => $acaoDB2["id"],
                ])->execute();
            } else {
                Yii::$app->db3->createCommand('UPDATE acao SET numero = :numero, descricao = :descricao WHERE importado_projeto_id = :importado_projeto_id')->bindValues(["importado_projeto_id" => $acaoDB2["id"], "numero" => $acaoDB2["numero"], "descricao" => $acaoDB2["descricao"]])->execute();
            }
            $acaoDB3Id = $acaoDB3 == null ? Yii::$app->db3->getLastInsertID() : $acaoDB3["id"];
            $metasDB2 = Yii::$app->db2->createCommand('SELECT * FROM meta WHERE acao_fk = :acao_fk')->bindValue("acao_fk", $acaoDB2["id"])->queryAll();
            foreach ($metasDB2 as $metaDB2) {
                $metaDB3 = Yii::$app->db3->createCommand('SELECT * FROM meta WHERE importado_projeto_id = :importado_projeto_id')->bindValue("importado_projeto_id", $metaDB2["id"])->queryOne();
                if ($metaDB3 == null) {
                    Yii::$app->db3->createCommand()->insert('meta', [
                        'numero' => $metaDB2["numero"],
                        'acao_fk' => $acaoDB3Id,
                        'descricao' => $metaDB2["descricao"],
                        'importado_projeto_id' => $metaDB2["id"],
                    ])->execute();
                } else {
                    Yii::$app->db3->createCommand('UPDATE meta SET numero = :numero, descricao = :descricao WHERE importado_projeto_id = :importado_projeto_id')->bindValues(["importado_projeto_id" => $metaDB2["id"], "numero" => $metaDB2["numero"], "descricao" => $metaDB2["descricao"]])->execute();
                }
                $metaDB3Id = $metaDB3 == null ? Yii::$app->db3->getLastInsertID() : $metaDB3["id"];
                $etapasDB2 = Yii::$app->db2->createCommand('SELECT * FROM etapa WHERE meta_fk = :meta_fk')->bindValue("meta_fk", $metaDB2["id"])->queryAll();
                foreach ($etapasDB2 as $etapaDB2) {
                    $etapaDB3 = Yii::$app->db3->createCommand('SELECT * FROM etapa WHERE importado_projeto_id = :importado_projeto_id')->bindValue("importado_projeto_id", $etapaDB2["id"])->queryOne();
                    if ($etapaDB3 == null) {
                        Yii::$app->db3->createCommand()->insert('etapa', [
                            'numero' => $etapaDB2["numero"],
                            'meta_fk' => $metaDB3Id,
                            'descricao' => $etapaDB2["descricao"],
                            'data_inicial' => $etapaDB2["data_inicial"],
                            'data_final' => $etapaDB2["data_final"],
                            'importado_projeto_id' => $etapaDB2["id"],
                        ])->execute();
                    } else {
                        Yii::$app->db3->createCommand('UPDATE etapa SET numero = :numero, descricao = :descricao, data_inicial = :data_inicial, data_final = :data_final WHERE importado_projeto_id = :importado_projeto_id')->bindValues(["importado_projeto_id" => $etapaDB2["id"], "numero" => $etapaDB2["numero"], "descricao" => $etapaDB2["descricao"], "data_inicial" => $etapaDB2["data_inicial"], "data_final" => $etapaDB2["data_final"]])->execute();
                    }
                    $etapaDB3Id = $etapaDB3 == null ? Yii::$app->db3->getLastInsertID() : $etapaDB3["id"];
                    $produtosDB2 = Yii::$app->db2->createCommand('SELECT * FROM produto WHERE etapa_fk = :etapa_fk')->bindValue("etapa_fk", $etapaDB2["id"])->queryAll();
                    foreach ($produtosDB2 as $produtoDB2) {
                        $produtoDB3 = Yii::$app->db3->createCommand('SELECT * FROM produto WHERE importado_projeto_id = :importado_projeto_id')->bindValue("importado_projeto_id", $produtoDB2["id"])->queryOne();
                        if ($produtoDB3 == null) {
                            Yii::$app->db3->createCommand()->insert('produto', [
                                'numero' => $produtoDB2["numero"],
                                'etapa_fk' => $etapaDB3Id,
                                'descricao' => $produtoDB2["descricao"],
                                'data_de_entrega' => $produtoDB2["data_de_entrega"],
                                'importado_projeto_id' => $produtoDB2["id"]
                            ])->execute();
                        } else {
                            Yii::$app->db3->createCommand('UPDATE produto SET numero = :numero, descricao = :descricao, data_de_entrega = :data_de_entrega WHERE importado_projeto_id = :importado_projeto_id')->bindValues(["importado_projeto_id" => $produtoDB2["id"], "numero" => $produtoDB2["numero"], "descricao" => $produtoDB2["descricao"], "data_de_entrega" => $produtoDB2["data_de_entrega"]])->execute();
                        }
                        $produtoDB3Id = $produtoDB3 == null ? Yii::$app->db3->getLastInsertID() : $produtoDB3["id"];
                        $indicadoresDB2 = Yii::$app->db2->createCommand('SELECT * FROM indicador WHERE produto_fk = :produto_fk')->bindValue("produto_fk", $produtoDB2["id"])->queryAll();
                        foreach ($indicadoresDB2 as $indicadorDB2) {
                            $indicadorDB3 = Yii::$app->db3->createCommand('SELECT * FROM indicador WHERE importado_projeto_id = :importado_projeto_id')->bindValue("importado_projeto_id", $indicadorDB2["id"])->queryOne();
                            if ($indicadorDB3 == null) {
                                Yii::$app->db3->createCommand()->insert('indicador', [
                                    'produto_fk' => $produtoDB3Id,
                                    'descricao' => $indicadorDB2["descricao"],
                                    'status' => "A",
                                    'comentario' => null,
                                    'importado_projeto_id' => $indicadorDB2["id"]
                                ])->execute();
                            } else {
                                Yii::$app->db3->createCommand('UPDATE indicador SET descricao = :descricao WHERE importado_projeto_id = :importado_projeto_id')->bindValues(["importado_projeto_id" => $indicadorDB2["id"], "descricao" => $indicadorDB2["descricao"]])->execute();
                            }
                        }
                    }
                }
            }
        }
    }

    public function actionManageFreeProject()
    {
        $request = Yii::$app->request;
        if ($request->post("id") == "") {
            $contrato = new Contrato();
        } else {
            $contrato = Contrato::find()->where("id = :id", [":id" => $request->post("id")])->one();
        }

        $contrato->nome = $request->post("nome");
        $contrato->origem_publica = $request->post("origem_publica") == "true" ? 1 : 0;

        $arr = explode('/', $request->post("data_inicial"));
        $initialDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        $arr = explode('/', $request->post("data_final"));
        $finalDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        $contrato->data_inicial = $initialDate;
        $contrato->data_final = $finalDate;

        $contrato->save();

        $result = ["contrato" => $contrato->attributes];
        return json_encode($result);
    }

    public function actionRemoveProject()
    {
        $request = Yii::$app->request;
        $id = $request->post("id");
        if (is_dir($this->mediasBaseDir . 'financeiro' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR)) {
            $this->rrmdir($this->mediasBaseDir . 'financeiro' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR);
        }
        $contrato = Contrato::find()->where("id = :id", [":id" => $id])->one();
        $projetoDB3 = Yii::$app->db3->createCommand('SELECT * FROM projeto WHERE importado_financeiro_id=:importado_financeiro_id')->bindValue("importado_financeiro_id", $contrato->id)->queryOne();
        if ($projetoDB3 != null) {
            if (is_dir($this->mediasBaseDir . 'gestao' . DIRECTORY_SEPARATOR . $projetoDB3["id"] . DIRECTORY_SEPARATOR)) {
                $this->rrmdir($this->mediasBaseDir . 'gestao' . DIRECTORY_SEPARATOR . $projetoDB3["id"] . DIRECTORY_SEPARATOR);
            }
            Yii::$app->db3->createCommand("DELETE FROM projeto WHERE id = :id")->bindValues(["id" => $projetoDB3["id"]])->execute();
        }
        $propostaDB2 = Yii::$app->db2->createCommand('SELECT * FROM proposta WHERE exportado_financeiro_id=:exportado_financeiro_id limit 1')->bindValue("exportado_financeiro_id", $contrato->id)->queryOne();
        if ($propostaDB2 != null) {
            $this->removeJustificationRecursively($propostaDB2["id"], null);
            $this->removeJustificationRecursively(null, $propostaDB2["id"]);
        }
        if ($contrato->conta_bancaria_fk != null) {
            $contrato->contaBancariaFk->delete();
        }
        $contrato->delete();
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object))
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }

    private function removeJustificationRecursively($parent, $children)
    {
        if ($parent != null && $children == null) {
            $proposta = Yii::$app->db2->createCommand("select * from proposta where proposta_pai_fk = :id")->bindValue("id", $parent)->queryOne();
            if ($proposta == null) {
                return;
            } else {
                Yii::$app->db2->createCommand('UPDATE proposta SET exportado_financeiro = "N", exportado_financeiro_id = null, exportado_financeiro_justificativa = null WHERE id = :id')->bindValues(["id" => $proposta["id"]])->execute();
                return $this->removeJustificationRecursively($proposta["id"], null);
            }
        } else {
            $proposta = Yii::$app->db2->createCommand("select * from proposta where id = :id")->bindValue("id", $children)->queryOne();
            if ($proposta == null) {
                return;
            } else {
                Yii::$app->db2->createCommand('UPDATE proposta SET exportado_financeiro = "N", exportado_financeiro_id = null, exportado_financeiro_justificativa = null WHERE id = :id')->bindValues(["id" => $proposta["id"]])->execute();
                return $this->removeJustificationRecursively(null, $proposta["proposta_pai_fk"]);
            }
        }
    }

    public function actionLoadProposal()
    {
        $request = Yii::$app->request;
        $proposta = \Yii::$app->db2->createCommand("select * from proposta where id = :id")->bindValue("id", $request->post("id"))->queryOne();
        $propostaElaboracao = \Yii::$app->db2->createCommand("select * from proposta_elaboracao where proposta_fk = :id")->bindValue("id", $request->post("id"))->queryAll();
        $rubricas = \Yii::$app->db2->createCommand("select * from rubrica where proposta_fk = :proposta_fk")->bindValue("proposta_fk", $request->post("id"))->queryAll();
        $proposta["elaboracao"] = $propostaElaboracao;
        $proposta["aditivo"] = $this->isAddictiveTerm($request->post("id"));
        $proposta["categorias"] = [];
        foreach ($rubricas as $key => $rubrica) {
            $rubricaDB = Rubrica::find()->where("importado_projeto_id = :id", ["id" => $rubrica["id"]])->one();
            $valorUtilizadoContratos = 0;
            $valorUtilizadoLancamentos = 0;
            if ($rubricaDB != null) {
                $rubricaFornecedores = RubricaFornecedores::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubricaDB->id])->all();
                foreach ($rubricaFornecedores as $rubricaFornecedor) {
                    $valorUtilizadoContratos += $rubricaFornecedor->valor_total;
                }
                $despesas = Despesa::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubricaDB->id])->all();
                foreach ($despesas as $despesa) {
                    foreach ($despesa->despesaAtividades as $despesaAtividade) {
                        $valorUtilizadoLancamentos += $despesaAtividade->valor;
                    }
                }
            }
            $rubrica["valor_utilizado_contratos"] = $valorUtilizadoContratos;
            $rubrica["valor_utilizado_lancamentos"] = $valorUtilizadoLancamentos;

            $rubrica["id_relacionado"] = $rubricaDB != null ? $rubricaDB->id : null;
            $rubrica["tipo_de_contrato_fk_relacionado"] = $rubricaDB != null ? $rubricaDB->tipo_de_contrato_fk : null;
            $rubrica["vinculante_relacionado"] = $rubricaDB != null ? $rubricaDB->vinculante : 0;
            $rubrica["fonte_relacionada"] = $rubricaDB != null ? $rubricaDB->fonte_fk : null;
            $hasCategory = false;
            foreach ($proposta["categorias"] as $index => $category) {
                if ($category["id"] == $rubrica["categoria_fk"]) {
                    $categoryIndex = $index;
                    $hasCategory = true;
                }
            }
            if (!$hasCategory) {
                $categoria = \Yii::$app->db2->createCommand("select * from categoria where id = :id")->bindValue("id", $rubrica["categoria_fk"])->queryOne();
                array_push($proposta["categorias"], $categoria);
                end($proposta["categorias"]);
                $categoryIndex = key($proposta["categorias"]);
            }
            if (!isset($proposta["categorias"][$categoryIndex]["rubricas"])) {
                $proposta["categorias"][$categoryIndex]["rubricas"] = [];
            }
            array_push($proposta["categorias"][$categoryIndex]["rubricas"], $rubrica);
        }
        $tiposDeContrato = [];
        foreach (TipoDeContrato::find()->all() as $tipoDeContrato) {
            $tiposDeContrato[$tipoDeContrato->id] = $tipoDeContrato->nome;
        }
        $proposta["tipos_de_contrato"] = $tiposDeContrato;

        $fontes = [];
        foreach (Fonte::find()->all() as $fonte) {
            $fontes[$fonte->id] = $fonte->nome;
        }
        $proposta["fontes"] = $fontes;

        foreach (\Yii::$app->db2->createCommand("select * from acao where proposta_fk = :id")->bindValue("id", $request->post("id"))->queryAll() as $acao) {
            $proposta["acoes"][$acao["numero"]] = $acao;
            foreach (\Yii::$app->db2->createCommand("select * from meta where acao_fk = :id")->bindValue("id", $acao["id"])->queryAll() as $meta) {
                $proposta["acoes"][$acao["numero"]]["metas"][$meta["numero"]] = $meta;
                foreach (\Yii::$app->db2->createCommand("select * from etapa where meta_fk = :id")->bindValue("id", $meta["id"])->queryAll() as $etapa) {
                    $proposta["acoes"][$acao["numero"]]["metas"][$meta["numero"]]["etapas"][$etapa["numero"]] = $etapa;
                    foreach (\Yii::$app->db2->createCommand("select * from produto where etapa_fk = :id")->bindValue("id", $etapa["id"])->queryAll() as $produto) {
                        $proposta["acoes"][$acao["numero"]]["metas"][$meta["numero"]]["etapas"][$etapa["numero"]]["produtos"][$produto["numero"]] = $produto;
                        $proposta["acoes"][$acao["numero"]]["metas"][$meta["numero"]]["etapas"][$etapa["numero"]]["produtos"][$produto["numero"]]["indicadores"] = [];
                        foreach (\Yii::$app->db2->createCommand("select * from indicador where produto_fk = :id")->bindValue("id", $produto["id"])->queryAll() as $indicador) {
                            array_push($proposta["acoes"][$acao["numero"]]["metas"][$meta["numero"]]["etapas"][$etapa["numero"]]["produtos"][$produto["numero"]]["indicadores"], $indicador["descricao"]);
                        }
                    }
                }
            }
        }

        return json_encode($proposta);
    }

    private function isAddictiveTerm($parent)
    {
        $proposta = \Yii::$app->db2->createCommand("select * from proposta where proposta_pai_fk = :id")->bindValue("id", $parent)->queryOne();
        if ($proposta == null) {
            return null;
        } else if ($proposta["exportado_financeiro"] == "Y") {
            return $proposta["exportado_financeiro_id"];
        } else {
            return $this->isAddictiveTerm($proposta["id"]);
        }
    }

    public function actionRejectProposal()
    {
        $request = Yii::$app->request;
        $propostaDB2 = Yii::$app->db2->createCommand('SELECT * FROM proposta WHERE id=:id')->bindValue("id", $request->post("id"))->queryOne();
        $usuarioDB2 = Yii::$app->db2->createCommand('SELECT * FROM usuario WHERE id=:id')->bindValue("id", $propostaDB2["usuario_fk"])->queryOne();
        $mailer = $this->setSMTPEmail($usuarioDB2, $propostaDB2, $request->post("justificativa"));
        if (!$mailer->send()) {
            $result = [
                'valid' => FALSE,
                'error' => 'Houve um problema ao enviar o e-mail ao destinatário. Tente novamente.',
                'mail_error' => $mailer->ErrorInfo
            ];
        } else {
            Yii::$app->db2->createCommand('UPDATE proposta SET exportado_financeiro = "N", exportado_financeiro_justificativa = :justificativa WHERE id=:id')->bindValues(["id" => $request->post("id"), "justificativa" => $request->post("justificativa")])->execute();
            $result = ['valid' => TRUE];
        }
        return json_encode($result);
    }

    /**
     * Função de gerenciamento de e-mail para ativação
     *
     * @param $usuario Recebe os dados do usuário
     * @param bool $register Recebe os dados do registro
     * @return \PHPMailer Retorna a função do mailer
     */
    private function setSMTPEmail($usuario, $proposta, $justificativa)
    {
        require_once("../vendor/phpmailer/phpmailer/src/PHPMailer.php");
        require_once("../vendor/phpmailer/phpmailer/src/SMTP.php");
        require_once("../vendor/phpmailer/phpmailer/src/Exception.php");
        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->Port = 587; //Indica a porta de conexão para a saída de e-mails
        $mailer->SMTPSecure = "tls";
        $mailer->Host = 'smtp.office365.com';//Endereço do Host do SMTP Locaweb
        $mailer->SMTPAuth = TRUE; //define se haverá ou não autenticação no SMTP
        $mailer->Username = 'naoresponda@ipti.org.br'; //Login de autenticação do SMTP
        $mailer->Password = 'Quna5048'; //Senha de autenticação do SMTP
        $mailer->FromName = "Instituto de Pesquisas em Tecnologia e Inovação (IPTI)";
        $mailer->From = 'naoresponda@ipti.org.br'; //Obrigatório ser a mesma caixa postal configurada no remetente do SMTP
        $mailer->addAddress($usuario["email"], $usuario["nome"]); //Destinatários
        $mailer->CharSet = 'UTF-8';
        $mailer->Subject = 'IPTI Financeiro - Proposta rejeitada';
        $mailer->Body = "Prezado(a) " . $usuario["nome"] . ",\n\n";
        $mailer->Body .= "Sua proposta (" . $proposta["nome"] . ") foi recusada.\n";
        $mailer->Body .= "Justificativa: " . $justificativa;
        return $mailer;
    }
}
