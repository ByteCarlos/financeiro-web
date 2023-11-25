<?php

namespace app\controllers;

use app\models\Bancos;
use app\models\ContaBancaria;
use app\models\Contrato;
use app\models\Fornecedor;
use app\models\Rubrica;
use app\models\RubricaFornecedores;
use app\models\Atividade;
use app\models\TipoDeContrato;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class ProviderController extends GlobalController
{

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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $fornecedoresArray = [];
        if ($this->usuario->admin || $this->usuario->admin_lancamentos || $this->usuario->admin_projetos || $this->usuario->admin_fornecedores || $this->usuario->assessor) {
            $fornecedores = Fornecedor::find()->all();
        } else {
            $fornecedores = Fornecedor::find()->join("JOIN", "rubrica_fornecedores", "rubrica_fornecedores.fornecedor_fk = fornecedor.id")->
            join("JOIN", "rubrica", "rubrica.id = rubrica_fornecedores.rubrica_fk")->
            join("JOIN", "centro_de_custo", "centro_de_custo.id = rubrica.centro_de_custo_fk")->
            join("JOIN", "coordenador", "centro_de_custo.contrato_fk = coordenador.contrato_fk")->
            where("usuario_fk = :usuario_fk", ["usuario_fk" => $this->usuario->id])->all();
        }
        foreach ($fornecedores as $fornecedor) {
            $identificacao = $fornecedor->cnpj != null ? $fornecedor->cnpj : $this->hideCPF($fornecedor->cpf);
            $fornecedoresArray[$fornecedor->id] = $fornecedor->nome . "|" . $identificacao . "|" . $fornecedor->tipoDeContratoFk->nome;
        }
        $contratosArray = [];
        foreach (Contrato::find()->all() as $contrato) {
            $contratosArray[$contrato->id] = $contrato->nome;
        }
        $bancosArray = [];
        foreach (Bancos::find()->all() as $banco) {
            $bancosArray[$banco->id] = $banco->codigo . " - " . $banco->nome_abreviado;
        }
        return $this->render('index', [
            'fornecedores' => new Fornecedor(), 'itensDeFornecedores' => $fornecedoresArray,
            'contratos' => new Contrato(), 'itensDeContrato' => $contratosArray,
            'tiposDeContrato' => TipoDeContrato::find()->all(),
            'bancos' => $bancosArray
        ]);
    }

    public function actionManageProvider()
    {
        $request = Yii::$app->request;
        if ($request->post("id") == "") {
            $fornecedor = new Fornecedor();
        } else {
            $fornecedor = Fornecedor::find()->where("id = :id", [":id" => $request->post("id")])->one();
            if ($fornecedor->conta_bancaria_fk != null && $request->post("agencia") == "") {
                $fornecedor->contaBancariaFk->delete();
            }
        }

        $contaBancaria = ($fornecedor->conta_bancaria_fk == null ? new ContaBancaria() : $fornecedor->contaBancariaFk);
        $contaBancaria->agencia = $request->post("agencia") == "" ? NULL : $request->post("agencia");
        $contaBancaria->tipo_de_conta = $request->post("tipo_de_conta") == "" ? NULL : $request->post("tipo_de_conta");
        $contaBancaria->banco_fk = $request->post("banco") == "" ? NULL : $request->post("banco");
        $contaBancaria->conta = $request->post("conta") == "" ? NULL : $request->post("conta");
        $contaBancaria->proprietario = $request->post("proprietario") == "" ? NULL : $request->post("proprietario");
        $contaBancaria->pix = $request->post("pix") == "" ? NULL : $request->post("pix");
        $contaBancaria->save();

        $fornecedor->nome = $request->post("name");
        $fornecedor->tipo_de_contrato_fk = $request->post("tipo_de_contrato_fk");
        $fornecedor->cpf = $request->post("cpf") == "" ? NULL : $request->post("cpf");
        $fornecedor->cnpj = $request->post("cnpj") == "" ? NULL : $request->post("cnpj");
        $fornecedor->pis = $request->post("pis") == "" ? NULL : $request->post("pis");
        $fornecedor->rg = $request->post("rg") == "" ? NULL : $request->post("rg");
        $fornecedor->email = $request->post("email") == "" ? NULL : $request->post("email");
        $fornecedor->endereco = $request->post("endereco") == "" ? NULL : $request->post("endereco");
        $fornecedor->profissao = $request->post("profissao") == "" ? NULL : $request->post("profissao");
        $fornecedor->telefone = $request->post("telefone") == "" ? NULL : $request->post("telefone");
        $fornecedor->respresentante_legal = $request->post("respresentante_legal") == "" ? NULL : $request->post("respresentante_legal");
        $fornecedor->conta_bancaria_fk = $contaBancaria->id;
        $fornecedor->save();

        $result = ["fornecedor" => $fornecedor->attributes];
        return json_encode($result);
    }

    public function actionLoadProvider()
    {
        $request = Yii::$app->request;
        $fornecedor = Fornecedor::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $result["fornecedor"] = $fornecedor->attributes;
        $result["tipo_de_contrato"] = $fornecedor->tipoDeContratoFk->nome;
        $result["conta_bancaria"] = new ContaBancaria();
        if ($fornecedor->conta_bancaria_fk != null) {
            $result["conta_bancaria"] = $fornecedor->contaBancariaFk->attributes;
            $result["conta_bancaria"]["banco"] = $fornecedor->contaBancariaFk->bancoFk != null ? $fornecedor->contaBancariaFk->bancoFk->codigo . " - " . $fornecedor->contaBancariaFk->bancoFk->nome_abreviado : null;
        }
        return json_encode($result);
    }

    public function actionRemoveProvider()
    {
        $request = Yii::$app->request;
        $fornecedor = Fornecedor::find()->where("id = :id", [":id" => $request->post("id")])->one();
        if ($fornecedor->conta_bancaria_fk != null) {
            $fornecedor->contaBancariaFk->delete();
        }
        $fornecedor->delete();
    }

    public function actionLoadProviderItems()
    {
        $request = Yii::$app->request;
        if ($this->usuario->admin || $this->usuario->admin_lancamentos || $this->usuario->admin_projetos || $this->usuario->admin_fornecedores || $this->usuario->assessor) {
            $rubricaFornecedores = RubricaFornecedores::find()->where("fornecedor_fk = :fornecedor_fk", [":fornecedor_fk" => $request->post("id")])->orderBy("rubrica_fornecedores.data_final desc")->all();
        } else {
            $rubricaFornecedores = RubricaFornecedores::find()->join("JOIN", "rubrica", "rubrica.id = rubrica_fornecedores.rubrica_fk")->
            join("JOIN", "centro_de_custo", "centro_de_custo.id = rubrica.centro_de_custo_fk")->
            join("JOIN", "coordenador", "centro_de_custo.contrato_fk = coordenador.contrato_fk")->
            where("usuario_fk = :usuario_fk and fornecedor_fk = :fornecedor_fk", ["usuario_fk" => $this->usuario->id, "fornecedor_fk" => $request->post("id")])->orderBy("rubrica_fornecedores.data_final desc")->all();
        }
        $result = [];
        $result["admin"] = $this->usuario->admin || $this->usuario->admin_fornecedores;
        foreach ($rubricaFornecedores as $rubricaFornecedor) {
            if (!isset($result["contratos"][$rubricaFornecedor->rubricaFk->centroDeCustoFk->contratoFk->nome])) {
                $result["contratos"][$rubricaFornecedor->rubricaFk->centroDeCustoFk->contratoFk->nome] = [];
            }
            $array = [];
            $array["id"] = $rubricaFornecedor->id;
            $array["ordem"] = $rubricaFornecedor->ordem;
            $array["valor_total"] = $rubricaFornecedor->valor_total;
            $array["carga_horaria"] = $rubricaFornecedor->workload;
            $array["valor_unitario"] = $rubricaFornecedor->unitary_value;
            $array["parcelas"] = $rubricaFornecedor->parcelas;
            $array["data_inicial"] = $rubricaFornecedor->data_inicial;
            $array["data_final"] = $rubricaFornecedor->data_final;
            $array["rubrica"] = $rubricaFornecedor->rubricaFk->descricao;
            $array["ativo"] = $rubricaFornecedor->data_final >= date('Y-m-d');
            array_push($result["contratos"][$rubricaFornecedor->rubricaFk->centroDeCustoFk->contratoFk->nome], $array);
        }
        return json_encode($result);
    }

    public function actionLoadContractItems()
    {
        $request = Yii::$app->request;
        $rubricas = Rubrica::find()->join("JOIN", "centro_de_custo", "rubrica.centro_de_custo_fk = centro_de_custo.id")->where("contrato_fk = :contrato_fk and vinculante = 1  and ordem = (select max(ordem) from centro_de_custo where contrato_fk = :contrato_fk)", [":contrato_fk" => $request->post("id")])->all();
        $result = [];
        foreach ($rubricas as $rubrica) {
            $result[$rubrica->descricao] = $rubrica->id;
        }
        return json_encode($result);
    }

    public function actionLoadItemInfo()
    {
        $request = Yii::$app->request;
        $rubrica = Rubrica::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $result["data_inicial"] = $rubrica->centroDeCustoFk->data_inicial;
        $result["data_final"] = $rubrica->centroDeCustoFk->data_final;
        $rubricaFornecedores = RubricaFornecedores::find()->where("rubrica_fk = :rubrica_fk", [":rubrica_fk" => $rubrica->id])->all();
        $valorUtilizado = 0;
        foreach ($rubricaFornecedores as $rubricaFornecedor) {
            $valorUtilizado += $rubricaFornecedor->valor_total;
        }
        $result["valor"] = $rubrica->valor_total - $valorUtilizado;
        return json_encode($result);
    }

    public function actionManageContract()
    {
        $request = Yii::$app->request;
        $arr = explode('/', $request->post("initialDate"));
        $initialDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        $arr = explode('/', $request->post("finalDate"));
        $finalDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];

        if ($request->post("id") != "") {
            $rubricaFornecedores = RubricaFornecedores::find()->where("id = :id", [":id" => $request->post("id")])->one();
        } else {
            $rubricaFornecedores = new RubricaFornecedores();
            $query = new Query();
            $ordem = $query->select(["ordem" => "if(max(ordem) is null, 0, max(ordem))"])->from("rubrica_fornecedores")->where("id != :id and fornecedor_fk = :fornecedor_fk and rubrica_fk = :rubrica_fk", [
                ":id" => $request->post("id"), ":fornecedor_fk" => $request->post("provider"),
                ":rubrica_fk" => $request->post("item")
            ])->one();
            $rubricaFornecedores->ordem = $ordem["ordem"] + 1;
        }
        $rubricaFornecedores->fornecedor_fk = $request->post("provider");
        $rubricaFornecedores->rubrica_fk = $request->post("item");
        $rubricaFornecedores->data_inicial = $initialDate;
        $rubricaFornecedores->data_final = $finalDate;
        $rubricaFornecedores->valor_total = $request->post("totalValue");
        $rubricaFornecedores->unitary_value = $request->post("unitaryValue");
        $rubricaFornecedores->workload = $request->post("workloadValue");
        $rubricaFornecedores->parcelas = $request->post("plots") === "" ? null : abs($request->post("plots"));
        $rubricaFornecedores->save();

        if ($request->post("removedActivities") != null) {
            foreach ($request->post("removedActivities") as $activity) {
                Atividade::deleteAll("id = :id", [":id" => $activity["id"]]);
            }
        }

        if ($request->post("activities") !== null) {
            foreach ($request->post("activities") as $key => $activity) {
                $atividade = Atividade::find()->where("id = :id", [":id" => $activity["id"]])->one();
                if ($atividade == null) {
                    $atividade = new Atividade();
                }
                $atividade->rubrica_fornecedores_fk = $rubricaFornecedores->id;
                $atividade->rubrica_fk = null;
                $atividade->descricao = $activity["description"];
                $atividade->valor = $activity["value"];
                $atividade->ordem = $activity["number"];
                $arr = explode('/', $activity["data"]);
                $activityDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                $atividade->data = $activityDate;
                $atividade->save();
            }
        }
    }

    public function actionRemoveContract()
    {
        $request = Yii::$app->request;
        RubricaFornecedores::deleteAll("id = :id", [":id" => $request->post("id")]);
    }

    public function actionLoadContract()
    {
        $request = Yii::$app->request;
        $rubricaFornecedores = RubricaFornecedores::find()->where("id = :id", [":id" => $request->post("id")])->one();
        $result = [];
        $result["id"] = $rubricaFornecedores->id;
        $result["contrato"] = $rubricaFornecedores->rubricaFk->centroDeCustoFk->contratoFk->nome;
        $result["contrato_id"] = $rubricaFornecedores->rubricaFk->centroDeCustoFk->contrato_fk;
        $result["ordem"] = $rubricaFornecedores->ordem;
        $result["rubrica"] = $rubricaFornecedores->rubricaFk->descricao;
        $result["rubrica_id"] = $rubricaFornecedores->rubrica_fk;
        $result["valor_total"] = $rubricaFornecedores->valor_total;
        $result["valor_unitario"] = $rubricaFornecedores->unitary_value;
        $result["carga_horaria"] = $rubricaFornecedores->workload;
        $result["parcelas"] = $rubricaFornecedores->parcelas;
        $result["data_inicial"] = $rubricaFornecedores->data_inicial;
        $result["data_final"] = $rubricaFornecedores->data_final;
        $atividades = Atividade::find()->where("rubrica_fornecedores_fk = :rubrica_fornecedores_fk", [":rubrica_fornecedores_fk" => $rubricaFornecedores->id])->orderBy("ordem")->all();
        foreach ($atividades as $atividade) {
            if (!isset($result["atividades"])) {
                $result["atividades"] = [];
            }
            array_push($result["atividades"], $atividade->attributes);
        }
        return json_encode($result);
    }
}
