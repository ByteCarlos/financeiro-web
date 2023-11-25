<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Projetos";

$this->registerCssFile('@web/common/css/project.css?v=1.1', ['depends' => ['app\assets\AppAsset']]);
$this->registerJsFile('@web/common/js/project.js?v=1.4', ['depends' => ['app\assets\AppAsset']]);

?>

<div class="launch-container">
    <div class="hide-print">
        <div class="projects-container">
            <label>Projeto:</label>
            <?= Html::activeDropDownList($contrato, 'id', $itensDoContrato, [
                'prompt' => 'Selecione...', 'class' => 'projects',
            ]) ?>
            <div class="project-menu">
                <i class="project-info darkred fa fa-question-circle-o"></i>
                <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_projetos): ?>
                    <i class="project-edit darkred fa fa-edit"></i>
                    <i class="project-remove darkred fa fa-times" data-toggle="modal"
                    data-target="#remove-project-modal"></i>
                <?php endif ?>
            </div>
        </div>
        <?php if (Yii::$app->controller->usuario->admin || Yii::$app->controller->usuario->admin_projetos): ?>
            <div class="add-new-container">
                <?php if (Yii::$app->controller->propostasPendentes > 0): ?>
                    <div class="proposals-container">
                        <div class="proposals">
                            <?php foreach ($propostasPendentes as $propostaPendente) : ?>
                                <div class="proposal">
                                    <input type="hidden" class="proposal-id" value="<?= $propostaPendente["id"] ?>">
                                    <span><strong><?= $propostaPendente["nome"] ?></strong></span>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <button class="btn btn-default check-proposals"><span
                                class="pending-proposals badge"><?= Yii::$app->controller->propostasPendentes ?></span>
                        Propostas Pendentes
                    </button>
                <?php endif ?>
                <button class="btn btn-default add-new-project"><i class="fa fa-plus"></i> Novo Projeto (Contrato)</button>
                <div class="clear"></div>
                <button class="btn btn-default add-new-free-project"><i class="fa fa-plus"></i> Novo Projeto (Livre)
                </button>
            </div>
        <?php endif ?>
        <div class="clear"></div>
        <div class="load-project-loading-gif">
            <i class="fa fa-spin fa-spinner fa-3x"></i>
        </div>
        <div class="project-error alert alert-danger"></div>
        <button class="btn btn-default" id="print-button" style="display: none;float: right;margin-right: 20px;">
        <i class="fa fa-print" style="margin-right: 5px;"></i>
        Imprimir
        </button>
    </div>
    <div class="project-info-container">
        <input type="hidden" class="project-id"/>
        <input type="hidden" class="project-imported"/>
        <div class="ribbon-title">
            <div class="ribbon-title-effect"></div>
            <i class="fa fa-list"></i> Projeto
        </div>
        <div class="header">
            <div>
                <label>Nome: <span class="red edit">*</span></label>
                <span class="info contract-name-info"></span>
                <input type="text" class="edit form-control contract-name">
            </div>
            <div>
                <label>Apoiadora: <span class="red edit">*</span></label>
                <span class="info contract-supporter-info"></span>
                <input type="text" class="edit form-control contract-supporter">
            </div>
            <div>
                <label>Origem Pública: </label>
                <span class="info contract-public-origin-info"></span>
                <input type="checkbox" class="edit contract-public-origin">
            </div>
            <div>
                <label>Data Inicial: </label>
                <span class="info contract-free-project-initial-date-info"></span>
            </div>
            <div>
                <label>Data Final: </label>
                <span class="info contract-free-project-final-date-info"></span>
            </div>
            <div>
                <label>Parcela(s): <span class="red edit">*</span></label>
                <span class="info contract-plots-info"></span>
                <input type="number" class="edit form-control contract-plots">
            </div>
            <div class="contract-plots-container-info info"></div>
            <div class="contract-plots-container edit"></div>
        </div>
        <div class="project-bank-account">
            <div>
                <label>Banco:</label>
                <span class="info banco-info"></span>
                <?= Html::activeDropDownList(new \app\models\Bancos(), 'id', $bancos, [
                    'prompt' => 'Selecione...', 'class' => 'contract-conta-banco',
                ]) ?>
            </div>
            <div>
                <label>Proprietário:</label>
                <span class="info proprietario-info"></span>
                <input type="text" class="edit form-control contract-conta-proprietario">
            </div>
            <div>
                <label>Tipo de Conta:</label>
                <span class="info tipo-info"></span>
                <select class="edit form-control contract-conta-tipo">
                    <option></option>
                    <option value="CC">Conta Corrente</option>
                    <option value="CP">Conta Poupança</option>
                    <option value="CS">Conta Salário</option>
                </select>
            </div>
            <div>
                <label>PIX:</label>
                <span class="info pix-info"></span>
                <input type="text" class="edit form-control contract-conta-pix">
            </div>
            <div>
                <label>Agência:</label>
                <span class="info agencia-info"></span>
                <input type="text" class="edit form-control contract-conta-agencia">
            </div>
            <div>
                <label>Conta:</label>
                <span class="info conta-info"></span>
                <input type="text" class="edit form-control contract-conta-conta">
            </div>
        </div>
        <div class="coordenador-container">
            <label>Coordenador(es): <span class="red edit">*</span></label>
            <span class="info contract-coordenadores-info"></span>
            <?= Html::activeDropDownList($coordenador, 'id', $itensDeCoordenador, [
                'class' => 'edit contract-coordenadores', 'multiple' => 'multiple'
            ]) ?>
        </div>
    </div>

    <div class="cc-container">
        <div class="clear"></div>
        <div class="cc-tabs">
            <ul>
                <li class="active">
                    <span class="tab tab-contract"> Contrato</span>
                    <input type="hidden" class="order" value="1"/>
                </li>
            </ul>
            <i class="add-more-cc fa fa-plus"></i>
        </div>
        <div class="removed-ccs"></div>
        <div class="removed-items"></div>
        <div class="add-item-container form-inline edit">
            <label>Adicionar Elementos de Despesa (Rubricas):</label>
            <?= Html::activeDropDownList($categoria, 'id', $itensDeCategoria, [
                'prompt' => 'Selecione...', 'class' => 'categories',
            ]) ?>
            <input type="number" class="item-quantity form-control" placeholder="Qtd"/>
            <i class="add-item fa fa-plus"></i>
            <div class="add-item-loading-gif">
                <i class="fa fa-spin fa-spinner fa-3x"></i>
            </div>
        </div>
        <div class="cc-panels"></div>
        <div class="proposal-alert alert alert-gestao">Ao aceitar a proposta, as informações técnicas do projeto serão
            exportadas para o sistema de gestão. Caso deseje visualizá-las, <span class="proposal-click-me"
                                                                                  data-toggle="modal"
                                                                                  data-target="#check-tech-info-modal">clique aqui</span>.
        </div>
    </div>
    <div class="clear"></div>
    <div class="manage-actions">
        <button class="pull-right btn btn-default manage-project-button"><i class="fa fa-plus"></i> Salvar</button>
        <button type="button" class="btn btn-default pull-right quit-button"><i class="fa fa-times"></i> Cancelar
        </button>
    </div>

    <!-- Modal -->
    <div id="remove-project-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Excluir Projeto</h4>
                </div>
                <div class="modal-body">
                    <span class="remove-project-message">A exclusão do projeto implicará na remoção de todos os lançamentos e contratos de fornecedores vinculados a este. <strong>Tem certeza</strong> que deseja fazer isso?</span>
                    <div class="remove-project-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default remove-project"><i class="fa fa-trash"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="reject-justification-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Justificativa</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger"></div>
                    <div class="form-inline form-group">
                        <label class="control-label">Justificativa: <span class="red">*</span></label>
                        <textarea class="form-control reject-justification"></textarea>
                        <div class="reject-justification-email">* Um e-mail será enviado ao criador da proposta
                            informando o motivo da rejeição. Talvez demore um pouco.
                        </div>
                    </div>
                    <div class="reject-justification-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default reject-proposal-button"><i
                                class="fa fa-times-circle-o"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="manage-free-project-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Novo Projeto (Livre)</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="free-project-id">
                    <div class="alert alert-danger"></div>
                    <div class="form-inline form-group">
                        <label class="control-label">Nome: <span class="red">*</span></label>
                        <input type="text" class="form-control free-project-name">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Origem Pública: <span class="red">*</span></label>
                        <input type="checkbox" class="form-control free-project-public-origin">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Data Inicial: <span class="red">*</span></label>
                        <input type="text" class="form-control date free-project-initial-date" name="free-project-initial-date">
                    </div>
                    <div class="form-inline form-group">
                        <label class="control-label">Data Final: <span class="red">*</span></label>
                        <input type="text" class="form-control date free-project-final-date" name="free-project-final-date">
                    </div>
                    <div class="manage-free-project-loading-gif">
                        <i class="fa fa-spin fa-spinner fa-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                    <button type="button" class="btn btn-default manage-free-project-button"><i class="fa fa-plus"></i>
                        Adicionar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="check-tech-info-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Informações Técnicas da Proposta</h4>
                </div>
                <div class="modal-body">
                    <div>
                        <label class="prop-label">Título:</label>
                        <span class="prop-title-info"></span>
                    </div>
                    <div>
                        <label class="prop-label">Cliente:</label>
                        <span class="prop-client-info"></span>
                    </div>
                    <div>
                        <label class="prop-label">Período:</label>
                        <span class="prop-period-info"><span class="prop-period-initial-date"></span> até <span
                                    class="prop-period-final-date"></span>*</span>
                    </div>
                    <div class="generated-container">
                        <div>
                            <label>Objeto:</label>
                            <span class="prop-object"></span>
                        </div>
                        <div class="specific-objectives-container">
                            <label>Objetivos Específicos:</label>
                            <div class="specific-objective">
                                <div class="specific-objective-number">1º</div>
                                <span class="prop-specific-objective"></span>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div>
                            <label>Justificativa:</label>
                            <span class="prop-justification"></span>
                        </div>
                        <div>
                            <label>Capacidade Técnica e Operacional:</label>
                            <span class="prop-capacity"></span>
                        </div>
                    </div>
                    <div class="schedule"></div>
                    <div class="actions-container"></div>
                </div>
                <div class="modal-footer">
                    <span class="dates-info">* Os campos de data estão sujeitos a alterações de acordo com a vigência informada. Ou seja, caso a celebração ocorra 5 dias após o período previsto, todas as datas serão atualizadas proporcionalmente.</span>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
