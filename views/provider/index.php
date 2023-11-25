<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Fornecedores";

$this->registerCssFile('@web/common/css/provider.css?v=1.1', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/provider.js?v=1.2', ['depends' => ['app\assets\AppAsset']]);

?>

<div class="provider-container">
    <div class="providers-container">
        <label>Fornecedores:</label>
        <?= Html::activeDropDownList($fornecedores, 'id', $itensDeFornecedores, [
            'prompt' => 'Selecione...', 'class' => 'providers',
        ]) ?>
        <input type="hidden" class="contract-provider-type">
        <div class="load-provider-loading-gif">
            <i class="fa fa-spin fa-spinner fa-2x"></i>
        </div>
        <div class="provider-menu">
            <i class="provider-info darkred fa fa-question-circle-o"></i>
            <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_fornecedores): ?>
                <i class="provider-edit darkred fa fa-edit"></i>
                <i class="provider-remove darkred fa fa-times" data-toggle="modal"
                   data-target="#remove-provider-modal"></i>
            <?php endif ?>
        </div>
    </div>
    <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_fornecedores): ?>
        <div class="add-new-button-container">
            <button class="btn btn-default add-new-provider"><i class="fa fa-plus"></i> Novo Fornecedor</button>
        </div>
    <?php endif ?>
    <div class="provider-items">
        <div class="provider-no-result alert">Nenhum contrato vinculado.</div>
        <table id="provider-items-table" class="table table-bordered" cellspacing="0"
               width="100%">

        </table>
        <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_fornecedores): ?>
            <button class="btn btn-default add-new-contract"><i class="fa fa-plus"></i> Adicionar</button>
        <?php endif ?>
        <button class="btn btn-default provider-provision-button" data-toggle="modal"
                data-target="#provider-provision-modal-date"><i class="fa fa-bank"></i> Provisionamento</button>
    </div>
</div>

<!-- Modal -->
<div id="manage-provider-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg ">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"></div>
                <div class="manage-provider-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>
                <div class="manage-provider-container">
                    <input type="hidden" class="manage-provider-id">

                    <div class="form-inline form-group provider-type-container">
                        <label class="control-label">Tipo <span class="red">*</span></label>
                        <?php foreach ($tiposDeContrato as $tipoDeContrato): ?>
                            <div class="radio">
                                <label><input type="radio" class="radio-type" name="type"
                                              value="<?= $tipoDeContrato->id ?>"><?= $tipoDeContrato->nome ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Geral -->
                    <div class="provider-data-container">
                        <div class="form-inline form-group nome-container">
                            <label class="control-label">Nome <span class="red">*</span></label>
                            <input type="text" class="form-control provider-name">
                        </div>
                        <!-- Geral FIM -->
                        <!-- PJ -->
                        <div class="form-inline form-group cnpj-container">
                            <label class="control-label">CNPJ <span class="red">*</span></label>
                            <input type="text" class="form-control provider-cnpj">
                        </div>
                        <div class="form-inline form-group representante-container">
                            <label class="control-label">Representante Legal </label>
                            <input type="text" class="form-control provider-representante">
                        </div>
                        <!-- FIM PJ-->
                        <!-- Geral -->
                        <div class="form-inline form-group cpf-container">
                            <label class="control-label">CPF <span class="red">*</span></label>
                            <input type="text" class="form-control provider-cpf">
                        </div>
                        <!-- CLT e RPA -->
                        <div class="form-inline form-group pis-container">
                            <label class="control-label">PIS </label>
                            <input type="text" class="form-control provider-pis">
                        </div>
                        <!-- CLT e RPA FIM -->
                        <div class="form-inline form-group rg-container">
                            <label class="control-label">RG </label>
                            <input type="text" class="form-control provider-rg">
                        </div>
                        <div class="form-inline form-group email-container">
                            <label class="control-label">E-mail </label>
                            <input type="email" class="form-control provider-email">
                        </div>
                        <div class="form-inline form-group endereco-container">
                            <label class="control-label">Endereço </label>
                            <input type="text" class="form-control provider-endereco">
                        </div>
                        <div class="form-inline form-group profissao-container">
                            <label class="control-label">Profissão </label>
                            <input type="text" class="form-control provider-profissao">
                        </div>
                        <div class="form-inline form-group telefone-container">
                            <label class="control-label">Telefone </label>
                            <input type="text" class="form-control provider-telefone">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="conta-container">
                        <hr/>
                        <div class="form-inline form-group banks-container">
                            <label class="control-label">Banco </label>
                            <?= Html::activeDropDownList(new \app\models\Bancos(), 'id', $bancos, [
                                'prompt' => 'Selecione...', 'class' => 'provider-conta-banco',
                            ]) ?>
                        </div>
                        <div class="form-inline form-group">
                            <label class="control-label">Proprietário </label>
                            <input type="text" class="form-control provider-conta-proprietario">
                        </div>
                        <div class="form-inline form-group">
                            <label class="control-label">Tipo de Conta </label>
                            <select class="form-control provider-conta-tipo">
                                <option></option>
                                <option value="CC">Conta Corrente</option>
                                <option value="CP">Conta Poupança</option>
                                <option value="CS">Conta Salário</option>
                            </select>
                        </div>
                        <div class="form-inline form-group">
                            <label class="control-label">PIX </label>
                            <input type="text" class="form-control provider-conta-pix">
                        </div>
                        <div class="form-inline form-group">
                            <label class="control-label">Agência </label>
                            <input type="text" class="form-control provider-conta-agencia">
                        </div>
                        <div class="form-inline form-group">
                            <label class="control-label">Conta </label>
                            <input type="text" class="form-control provider-conta-conta">
                        </div>
                    </div>
                    <!-- Geral FIM -->
                </div>
                <div class="clear"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
                <button type="button" class="btn btn-default manage-provider">
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="remove-provider-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Excluir Fornecedor</h4>
            </div>
            <div class="modal-body">
				<span>A exclusão de um fornecedor implicará também na remoção de todos os lançamentos vinculados a ele. <strong>Tem
                        certeza</strong> que deseja fazer isso?</span>
                <input type="hidden" class="remove-provider-id">

                <div class="remove-provider-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
                <button type="button" class="btn btn-default remove-provider"><i class="fa fa-trash"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="load-provider-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Fornecedor</h4>
            </div>
            <div class="modal-body">
                <div>
                    <label>Tipo de Contrato:</label>
                    <span class="provider-tipo-contrato-info"></span>
                </div>
                <div>
                    <label>Nome:</label>
                    <span class="provider-name-info"></span>
                </div>
                <div>
                    <label>CNPJ:</label>
                    <span class="provider-cnpj-info"></span>
                </div>
                <div>
                    <label>Representante Legal:</label>
                    <span class="provider-representante-info"></span>
                </div>
                <div>
                    <label>CPF:</label>
                    <span class="provider-cpf-info"></span>
                </div>
                <div>
                    <label>Pis:</label>
                    <span class="provider-pis-info"></span>
                </div>
                <div>
                    <label>RG:</label>
                    <span class="provider-rg-info"></span>
                </div>
                <div>
                    <label>E-mail:</label>
                    <span class="provider-email-info"></span>
                </div>
                <div>
                    <label>Endereço:</label>
                    <span class="provider-endereco-info"></span>
                </div>
                <div>
                    <label>Profissão:</label>
                    <span class="provider-profissao-info"></span>
                </div>
                <div>
                    <label>Telefone:</label>
                    <span class="provider-telefone-info"></span>
                </div>
                <div class="bank-data">
                    <div>
                        <label>Banco:</label>
                        <span class="provider-conta-banco-info"></span>
                    </div>
                    <div>
                        <label>Agência:</label>
                        <span class="provider-conta-agencia-info"></span>
                    </div>
                    <div>
                        <label>Tipo de Conta:</label>
                        <span class="provider-conta-tipo-info"></span>
                    </div>
                    <div>
                        <label>PIX:</label>
                        <span class="provider-conta-pix-info"></span>
                    </div>
                    <div>
                        <label>Conta:</label>
                        <span class="provider-conta-conta-info"></span>
                    </div>
                    <div>
                        <label>Proprietário:</label>
                        <span class="provider-conta-proprietario-info"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="manage-contract-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Adicionar Contrato do Fornecedor</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="item-providers-id">
                <input type="hidden" class="item-old-value">
                <div class="alert alert-danger"></div>
                <div class="manage-contract-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>
                <div class="manage-contract-container">
                    <div class="form-inline form-group contracts-container">
                        <label class="control-label">Contrato <span class="red">*</span></label>
                        <?= Html::activeDropDownList($contratos, 'id', $itensDeContrato, [
                            'prompt' => 'Selecione...', 'class' => 'contracts',
                        ]) ?>
                        <div class="load-items-loading-gif">
                            <i class="fa fa-spin fa-spinner fa-2x"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-inline form-group contract-items-container">
                            <label class="control-label">Rubrica <span class="red">*</span></label>
                            <select class="contract-items"></select>

                            <div class="load-item-info-loading-gif">
                                <i class="fa fa-spin fa-spinner fa-2x"></i>
                            </div>
                            <span class="item-total-value"></span>
                        </div>
                    </div>
                    <div class="item-selected-container">
                        <div class="date-container">
                            <div class="form-inline form-group">
                                <label class="control-label">Data Inicial <span class="red">*</span></label>
                                <input type="text" class="form-control contract-initial-date">
                            </div>
                            <div class="form-inline form-group">
                                <label class="control-label">Data Final <span class="red">*</span></label>
                                <input type="text" class="form-control contract-final-date">
                            </div>
                        </div>
                        <div class="value-plots-container">
                            <div class="form-inline form-group">
                                <label class="control-label">Valor <span class="red">*</span></label>
                                <input type="text" class="form-control contract-value">
                            </div>
                            <div class="form-inline form-group">
                                <label class="control-label">Parcelas <span class="red">*</span></label>
                                <input type="number" class="form-control contract-plots">
                            </div>
                        </div>
                        <div class="unitary-container">
                            <div class="form-inline form-group checkbox-workload">
                                <input type="checkbox" name="workload-check" id="workload-check" style="height: 20px;width: 20px;cursor:pointer;">
                                <label class="control-label" style="width: 200px;margin: 0 0 10px 10px;">Adicionar Carga Horária?</label>
                            </div>
                            <div class="hide-container-unitary" style="display: none;">
                                <div class="form-inline form-group" style="margin-right: 36px;">
                                    <label class="control-label" style="width: 100px;">Carga Horária</label>
                                    <input type="number" class="form-control workload-value" style="width:285px;">
                                </div>
                                <div class="form-inline form-group">
                                    <label class="control-label" style="width: 100px;">Valor Unitário</label>
                                    <input type="text" class="form-control unitary-value" style="width: 294px;">
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="removed-activities"></div>
                        <div class="contract-activities-container"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
                <button type="button" class="btn btn-default manage-contract"><i class="fa fa-plus"></i> Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="remove-contract-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Excluir Contrato</h4>
            </div>
            <div class="modal-body">
				<span>A exclusão de um contrato impedirá que você realize novos lançamentos no intervalo de vigência do contrato. <strong>Tem
                        certeza</strong> que deseja fazer isso?</span>
                <input type="hidden" class="remove-contract-id">

                <div class="remove-contract-loading-gif">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
                <button type="button" class="btn btn-default remove-contract"><i class="fa fa-trash"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="load-contract-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Informações do Contrato</h4>
            </div>
            <div class="modal-body">
                <div>
                    <label>Contrato:</label>
                    <span class="contract-name-info"></span>
                </div>
                <div>
                    <label>Rubrica:</label>
                    <span class="contract-item-info"></span>
                </div>
                <div class="contract-dates-info">
                    <div>
                        <label>Data Inicial:</label>
                        <span class="contract-initial-date-info"></span>
                    </div>
                    <div>
                        <label>Data Final:</label>
                        <span class="contract-final-date-info"></span>
                    </div>
                </div>
                <div class="contract-value-plots-info">
                    <div>
                        <label>Valor:</label>
                        <span class="contract-value-info"></span>
                    </div>
                    <div>
                        <label>Parcelas:</label>
                        <span class="contract-plots-info"></span>
                    </div>
                </div>
                <div class="contract-value-plots-info">
                    <div>
                        <label>Carga Horária:</label>
                        <span class="contract-workload-info"></span>
                    </div>
                    <div>
                        <label>Valor Unitário:</label>
                        <span class="contract-unitary-value-info"></span>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="contract-activities"></div>
                <div class="clear"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="provider-provision-modal-date" class="modal-date modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Data</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger"></div>
                <?php
                $form = ActiveForm::begin([
                    'action' => ['reports/load-provider-provision-report'], 'id' => 'provider-provision-form', 'method' => 'get',
                ]);
                ?>
                <input type="hidden" name="provider" class="provider-id"/>

                <div class="form-inline only-interval-container">
                    <label>Intervalo</label>
                    <input name="only-interval" type="checkbox" class="only-interval" value="1">
                </div>
                <div class="form-inline initial-date-container provision-initial-date-container">
                    <label class="control-label">Inicial <span class="red">*</span></label>
                    <input type="text" class="provision-initial-date date form-control" autocomplete="off"
                           name="initial-date">
                </div>
                <div class="form-inline provision-final-date-container">
                    <label class="control-label">Final <span class="red">*</span></label>
                    <input type="text" class="provision-final-date date form-control" autocomplete="off" name="final-date">
                </div>
                <?php
                $form = ActiveForm::end();
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Fechar
                </button>
                <button type="button" class="btn btn-default generate-provider-provision-report"><i
                        class="fa fa-file-text-o"></i> Gerar
                </button>
            </div>
        </div>
    </div>
</div>