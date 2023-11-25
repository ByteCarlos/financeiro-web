$(".projects-menu").addClass("active");
addSelect2();

$(".free-project-initial-date, .free-project-final-date").inputmask({mask: '99/99/9999', showMaskOnHover: false});

$(".free-project-initial-date").datepicker({
    language: "pt-BR",
    format: "dd/mm/yyyy",
    autoclose: true,
    todayHighlight: true,
    allowInputToggle: true,
    disableTouchKeyboard: true,
    keyboardNavigation: false,
    orientation: "bottom left",
    clearBtn: true,
    maxViewMode: 2,
    startDate: "01/01/2000",
}).on('changeDate', function (ev, indirect) {
    if ($(".free-project-initial-date").val() !== "" && $(".free-project-initial-date").inputmask('unmaskedvalue').length == 8
        && $(".free-project-final-date").val() !== "" && $(".free-project-final-date").inputmask('unmaskedvalue').length == 8) {
        var startDateStr = $(".free-project-initial-date").val().split("/");
        var startDate = !indirect ? new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0) : new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
        var endDateStr = $(".free-project-final-date").val().split("/");
        var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
        if (endDate < startDate) {
            correctIntervalDate = false;
        } else {
            correctIntervalDate = true;
        }
    } else {
        correctIntervalDate = false;
    }
}).on('clearDate', function (ev) {
    correctIntervalDate = false;
});

$(".free-project-final-date").datepicker({
    language: "pt-BR",
    format: "dd/mm/yyyy",
    autoclose: true,
    todayHighlight: true,
    allowInputToggle: true,
    disableTouchKeyboard: true,
    keyboardNavigation: false,
    orientation: "bottom left",
    clearBtn: true,
    maxViewMode: 2,
    startDate: "01/01/2000",
}).on('changeDate', function (ev) {
    if ($(".free-project-initial-date").val() !== "" && $(".free-project-initial-date").inputmask('unmaskedvalue').length == 8
        && $(".free-project-final-date").val() !== "" && $(".free-project-final-date").inputmask('unmaskedvalue').length == 8) {
        var endDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
        var startDateStr = $(".free-project-initial-date").val().split("/");
        var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
        if (endDate < startDate) {
            correctIntervalDate = false;
        } else {
            correctIntervalDate = true;
        }
    } else {
        correctIntervalDate = false;
    }
}).on('clearDate', function (ev) {
    correctIntervalDate = false;
});
$(".free-project-initial-date").trigger("changeDate", [true]);

$('.free-project-initial-date, .free-project-final-date').on('paste', function () {
    if ($(this).val() !== "" && $(this).inputmask('unmaskedvalue').length == 8) {
        $(this).datepicker("hide").blur();
    }
});

$(document).on("click", ".project-info, .quit-button", function () {
    if (!$(".info").is(":visible")) {
        $(".projects-container").show();
        $(".projects").trigger("change");
    }
});

$(document).on("click", ".add-new-project", function () {
    $(".projects-container").hide();
    $(".edit").val("").show();
    $(".ribbon-title").html('<div class="ribbon-title-effect"></div><i class="fa fa-list"></i> Projeto');
    $(".project-imported").val(0);
    $(".contract-name, .contract-supporter").removeAttr("disabled");
    $(".contract-conta-banco, .contract-conta-tipo, .contract-coordenadores").val("").trigger("change");
    $(".contract-plots-info, .contract-supporter-info, .contract-coordenadores-info").parent().show();
    $(".contract-free-project-initial-date-info, .contract-free-project-final-date-info").parent().hide();
    $(".contract-public-origin").prop("checked", false);
    $(".project-bank-account").show();
    $(".info").hide();
    $(".categories").val("").trigger("change");
    $(".item-quantity").val("");
    $(".project-info-container").css("opacity", "1").show();
    $(".manage-actions").show();
    $(".manage-project-button").html('<i class="fa fa-plus"></i> Salvar');
    $(".reject-proposal").remove();
    $(".project-error").hide();
    $(".alert-gestao").hide();
    $(".proposal.selected").removeClass("selected");
    $(".tab-contract").parent().addClass("active");
    $(".contract-plots-container").children().remove();
    $(".removed-ccs").children().remove();
    $(".removed-items").children().remove();
    $(".add-more-cc").hide();
    $(".cc-container").css("opacity", "1").show();
    $(".cc-panels").children().remove();
    $(".cc-panels").append(generateNewPanel("active", 1));
    $(".info-total-value").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    })
    addDatePickers();
    $(".tab-additive").parent().remove();
    $(".project-id").val("");
});

$(document).on("click", ".project-edit", function (evt, proposalAddictive) {
    if ($(".project-info").is(":visible")) {
        if (!$(".edit").is(":visible")) {
            $.ajax({
                method: "POST",
                url: "/index.php?r=project/load-contract-types-and-sources",
                beforeSend: function () {
                    $(".load-project-loading-gif").show();
                    $(".project-menu, .project-info-container, .cc-container").css("opacity", "0.3");
                },
            }).success(function (data) {
                $(".load-project-loading-gif").hide();
                $(".project-menu, .project-info-container, .cc-container").css("opacity", "1");
                data = JSON.parse(data);
                addContractTypes(data.tipos_de_contrato);
                addSources(data.fontes);
                var totalPanels = $('.cc-panels > div').length;
                $(".cc-panels > div").each(function (index) {
                    if (index < totalPanels - 1) {
                        $(this).find('select').select2("enable", false);
                    }
                });
                $(".contract-name").val($(".contract-name-info").text());
                $(".contract-supporter").val($(".contract-supporter-info").text());
                $(".contract-public-origin").prop("checked", Number($(".contract-public-origin-info").attr("checkvalue")));
                $(".contract-plots").val($(".contract-plots-info").text());
                $(".contract-conta-agencia").val($(".agencia-info").text());
                $(".contract-conta-conta").val($(".conta-info").text());
                $(".contract-conta-proprietario").val($(".proprietario-info").text());
                $(".contract-conta-pix").val($(".pix-info").text());
                $(".edit, .add-more-cc, .remove-cc").show();
                if ($(".project-imported").val() == 1) {
                    $(".contract-name, .contract-supporter").attr("disabled", "disabled");
                    $(".add-item-container").hide();
                    $(".add-more-cc").hide();
                    $(".remove-cc").remove();
                } else {
                    $(".contract-name, .contract-supporter").removeAttr("disabled");
                    $(".cc-tabs > ul > li.active").index() == $(".cc-tabs > ul > li").length - 1 ? $(".add-item-container").show() : $(".add-item-container").hide();
                }
                $(".contract-plots-info, .contract-supporter-info, .contract-coordenadores-info, .pix-info").parent().show();
                $(".categories").val("").trigger("change");
                $(".item-quantity").val("");
                $(".project-bank-account").show();
                $(".col-item-icon").show();
                $(".item-value").each(function () {
                    $(this).val(Number($(this).val()).toFixed(2));
                });
                $('.item-value, .plot-value').trigger('mask.maskMoney');
                $(".cc-panels > div").each(function (index) {
                    var sum = 0;
                    $(this).find(".used-value").each(function () {
                        sum += Number($(this).unmask()) / 100;
                    });
                    if (sum > 0) {
                        $($(".cc-tabs").find("li").get(index)).find(".remove-cc").hide();
                    }
                });

                $(".info").hide();
                $(".manage-actions").show();
                $(".manage-project-button").html('<i class="fa fa-plus"></i> Salvar');
                $(".reject-proposal").remove();
                $(".project-error").hide();
                $(".project-id").val($(".projects").val());
                if (proposalAddictive !== undefined) {
                    $(".proposals").find(".proposal").each(function () {
                        if ($(this).find(".proposal-id").val() == proposalAddictive.id) {
                            $(this).addClass("selected");
                        } else {
                            $(this).removeClass("selected");
                        }
                    });
                    $(".alert-gestao").show();
                    $(".add-more-cc").click();
                    $(".remove-cc").hide();
                    $(".cc-panels > div").last().find(".items-table").children().remove();
                    $(".contract-supporter").val(proposalAddictive.cliente);
                    buildProposalCCPanel(proposalAddictive);
                    buildProposalTechPanel(proposalAddictive);
                }
            });
        }
    } else {
        $(".free-project-id").val($(".projects").val());
        $(".free-project-name").val($('.projects').select2('data')[0].text);
        $(".free-project-public-origin").prop("checked", Number($(".contract-public-origin-info").attr("checkvalue")));
        $(".free-project-initial-date").val($(".contract-free-project-initial-date-info").text()).datepicker("update");
        ;
        $(".free-project-final-date").val($(".contract-free-project-final-date-info").text()).datepicker("update");
        ;
        $("#manage-free-project-modal .alert").hide();
        $("#manage-free-project-modal").modal("show");
    }
});

$(document).on("click", "#print-button", function () {
    window.print();
});

$(document).on("change", ".projects", function (evt, proposalAddictive) {
    if ($(this).val() !== "") {
        $("#print-button").show();
        $.ajax({
            method: "POST",
            url: "/index.php?r=project/load-contract-info",
            data: {
                "id": $(this).val(),
            },
            beforeSend: function () {
                $(".load-project-loading-gif").show();
                $(".project-menu, .project-info-container, .cc-container").css("opacity", "0.3");
            },
        }).success(function (data) {
            data = JSON.parse(data);
            $(".info").show();
            $(".edit").hide();
            $(".contract-name-info").text(data.contrato.nome);
            $(".contract-public-origin-info").text(data.contrato.origem_publica ? "Sim" : "Não").attr("checkvalue", Number(data.contrato.origem_publica));
            if (data.contrato.centro_de_custo == undefined) {
                $(".project-menu, .project-info-container").css("opacity", "1").show();
                $(".project-info").hide();
                $(".project-edit").length ? "" : $(".project-menu").hide();
                $(".cc-container").hide();
                $(".load-project-loading-gif").hide();
                $(".manage-actions").hide();
                var initialDate = data.contrato.data_inicial.split("-");
                var finalDate = data.contrato.data_final.split("-");
                $(".contract-free-project-initial-date-info").text(initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0]).parent().show();
                $(".contract-free-project-final-date-info").text(finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0]).parent().show();
                $(".contract-supporter-info").text("").parent().hide();
                $(".contract-plots-info").text("").parent().hide();
                $(".contract-plots-container-info").html("");
                $(".project-bank-account").hide();
                $(".banco-info, .agencia-info, .conta-info, .proprietario-info").text("");
                $(".contract-coordenadores-info").parent().hide();
                $(".ribbon-title").html('<div class="ribbon-title-effect"></div><i class="fa fa-list"></i> Projeto (Livre)')
            } else {
                $(".load-project-loading-gif").hide();
                $(".add-more-cc").hide();
                $(".alert-gestao").hide();
                $(".project-info").show();
                $(".project-info-container, .project-menu, .cc-container").css("opacity", "1").show();
                $(".manage-project-button").removeAttr("disabled");
                $(".project-error").hide();
                $(".manage-actions").hide();
                $(".contract-free-project-initial-date-info").text("").parent().hide();
                $(".contract-free-project-final-date-info").text("").parent().hide();
                $(".contract-supporter-info").text(data.contrato.apoiadora).parent().show();
                $(".contract-plots-info").text(Object.keys(data.contrato.parcelas).length).parent().show();
                $(".contract-plots").val(Object.keys(data.contrato.parcelas).length).trigger("focusout");
                $(".contract-plots-container").hide();
                var plotsHtml = "";
                $.each(data.contrato.parcelas, function (index, value) {
                    $($(".plot-id").get(index)).val(value.id);
                    $($(".plot-description").get(index)).val(value.descricao);
                    $($(".plot-paying-source").get(index)).val(value.fonte_pagadora);
                    $($(".plot-value").get(index)).val(Number(value.valor).toFixed(2));
                    var plotDate = value.data.split("-");
                    $($(".plot-date").get(index)).data('datepicker').setDate(plotDate[2] + "/" + plotDate[1] + "/" + plotDate[0]);

                    plotsHtml += '<div>'
                        + '<span class="plot-number-info">' + value.ordem + 'º</span>'
                        + '<span class="plot-description-info">' + value.descricao + '</span>'
                        + '<span class="plot-paying-source-info">' + value.fonte_pagadora + '</span>'
                        + '<span class="plot-value-info">' + Number(value.valor).toFixed(2) + '</span>'
                        + '<span class="plot-date-info">' + plotDate[2] + '/' + plotDate[1] + '/' + plotDate[0] + '</span>'
                        + '</div><hr class="plot-divisor"/>';
                });
                $(".project-error").hide();
                $(".contract-plots-container-info").html(plotsHtml);

                if (data.contrato.conta_bancaria != undefined) {
                    $(".contract-conta-banco").val(data.contrato.conta_bancaria.banco_fk).trigger("change.select2");
                    $(".contract-conta-tipo").val(data.contrato.conta_bancaria.tipo_de_conta).trigger("change.select2");
                    $(".banco-info").text(data.contrato.conta_bancaria.banco);
                    $(".tipo-info").text(translateTipoDeConta(data.contrato.conta_bancaria.tipo_de_conta));
                    $(".agencia-info").text(data.contrato.conta_bancaria.agencia);
                    $(".conta-info").text(data.contrato.conta_bancaria.conta);
                    $(".proprietario-info").text(data.contrato.conta_bancaria.proprietario);
                    data.contrato.conta_bancaria.pix == null ? $(".pix-info").text("").parent().hide() : $(".pix-info").text(data.contrato.conta_bancaria.pix).parent().show();
                    $(".project-bank-account").show();
                } else {
                    $(".project-bank-account").hide();
                    $(".banco-info, .agencia-info, .conta-info, .proprietario-info").text("");
                }

                var coordenadoresLabel = "";
                var coordenadoresValue = new Array();
                $.each(data.contrato.coordenadores, function () {
                    coordenadoresLabel += '<span class="coordenador-nome">' + this.nome + '</span>';
                    coordenadoresValue.push(this.usuario_fk);
                });
                coordenadoresLabel != "" ? $(".contract-coordenadores-info").html(coordenadoresLabel).parent().show() : $(".contract-coordenadores-info").parent().hide();
                $(".contract-coordenadores").val(coordenadoresValue).trigger("change.select2");

                if (data.contrato.importado) {
                    $(".project-imported").val(1);
                    $(".ribbon-title").html('<div class="ribbon-title-effect"></div><i class="fa fa-list"></i> Projeto (Integrado)');
                } else {
                    $(".project-imported").val(0);
                    $(".ribbon-title").html('<div class="ribbon-title-effect"></div><i class="fa fa-list"></i> Projeto');
                }

                $(".tab").parent().remove();
                $(".cc-panels").children().remove();
                $(".removed-ccs").children().remove();
                $(".removed-items").children().remove();
                var i = 0;
                $.each(data.contrato.centro_de_custo, function (index) {
                    var lastIndex = i == Object.keys(data.contrato.centro_de_custo).length - 1;
                    var prevIndex = i < Object.keys(data.contrato.centro_de_custo).length - 1;
                    var active = lastIndex ? "active" : "";
                    var celebrationDateHtml = "";
                    if (i == 0) {
                        $(".cc-tabs").children("ul").append('<li class="' + active + '"><span class="tab tab-contract">Contrato</li>');
                    } else {
                        if (this.celebracao_termo_aditivo != null) {
                            var celebrationDateSplit = this.celebracao_termo_aditivo.split("-");
                            celebrationDateHtml = '<label class="cc-label">Celebração: <span class="red edit">*</span></label> <span class="info celebration-info-date"> ' + (celebrationDateSplit[2] + "/" + celebrationDateSplit[1] + "/" + celebrationDateSplit[0]) + ' </span><input type="text" class="edit form-control date celebration-date" ' + (prevIndex ? "disabled" : "") + ' value="' + (celebrationDateSplit[2] + "/" + celebrationDateSplit[1] + "/" + celebrationDateSplit[0]) + '"><br/>';
                        }
                        var removeCC = i == Object.keys(data.contrato.centro_de_custo).length - 1 ? '<i class="remove-cc fa fa-times"></i>' : "";
                        $(".cc-tabs").children("ul").append('<li class="' + active + '"><span class="tab tab-additive"><span class="additive-number">' + i + '</span>º Termo Aditivo ' + removeCC + '</span></li>');
                    }
                    var item = "";
                    $.each(this.categorias, function () {
                        var sum = 0;
                        item += '<tbody>';
                        item += '<tr><th colspan="8" class="category-name" pessoal="' + this.pessoal + '"><span>' + this.nome + '</span><input type="hidden" class="category-id" value="' + this.id + '"</th></tr>';
                        item += '<tr><th class="col-item-id"></th><th class="col-item-proposal-id"></th><th style="width: 33%;">Descrição</th><th style="width: 33%;">Valor Total' + (lastIndex ? '<span class="used-value-title">Valor Utilizado</span>' : "") + '</th><th style="width: 15%;">Vínculo</th><th style="width:14%">Categoria</th><th style="width:5%">Vinculante</th><th class="col-item-icon"></th></tr>';
                        $.each(this.rubricas, function () {
                            sum += Number(this.valor_total);
                            var remove = !data.contrato.importado && !prevIndex && (Number(this.valor_utilizado_contratos) + Number(this.valor_utilizado_lancamentos) == 0) ? '<td class="col-item-icon remove-item"><i class="fa fa-times"/></td>' : '<td class="col-item-icon"></td>';
                            item += '<tr class="item">';
                            item += '<td class="col-item-id">' + this.id + '</td><td class="col-item-proposal-id">' + (this.importado_projeto_id == null ? "" : this.importado_projeto_id) + '</td>' +
                                '<td class="col-item-description"><span class="info">' + this.descricao + '</span><input class="form-control edit item-description" ' + (data.contrato.importado || prevIndex ? "disabled" : "") + ' value="' + this.descricao + '"/></td>' +
                                '<td class="col-item-value"><span class="info">' + Number(this.valor_total).toFixed(2) + '</span>' + (lastIndex ? '<div class="used-value-container"><div>Contratos: <span class="used-value used-value-contracts">' + Number(this.valor_utilizado_contratos).toFixed(2) + '</span></div><div>Lançado: <span class="used-value used-value-launches">' + Number(this.valor_utilizado_lancamentos).toFixed(2) + '</span></div></div>' : "") + '<input class="form-control edit item-value" ' + (data.contrato.importado || prevIndex ? "disabled" : "") + ' value="' + this.valor_total + '"/></td>' +
                                '<td class="col-item-type"><input type="hidden" class="item-type-id" value="' + this.tipo_de_contrato_fk + '"/><span class="info">' + (this.tipo_de_contrato == null ? "" : this.tipo_de_contrato) + '</span><select class="edit form-control item-type"></select>' +
                                '<td class="col-item-source"><input type="hidden" class="item-source-id" value="' + this.fonte_fk + '"/><span class="info">' + (this.fonte == null ? "" : this.fonte) + '</span><select class="edit form-control item-source"></select>' +
                                '<td class="col-item-vinculate"><span class="info">' + (this.vinculante == 0 ? 'Não' : 'Sim') + '</span><input type="checkbox" ' + (prevIndex ? "disabled" : "") + ' class="edit item-vinculate" ' + (this.vinculante == 1 ? "checked" : "") + '/></td>' +
                                remove;
                            item += '</tr>';
                        });
                        item += '<tr class="category-total-row"><td></td><td class="category-total-value">' + Number(sum).toFixed(2) + '</td><td></td><td></td><td class="col-item-icon"></td></tr>';
                        item += '</tbody>';
                    });
                    var initialDate = this.data_inicial.split("-");
                    var finalDate = this.data_final.split("-");
                    var panel = '<div class="' + active + '">' +
                        '<input type="hidden" class="cc-id" value="' + this.id + '"/>' +
                        '<input type="hidden" class="order" value="' + this.ordem + '"/>' +
                        celebrationDateHtml +
                        '<label class="cc-label">Vigência: <span class="red edit">*</span></label> ' +
                        '<span class="info initial-info-date"> ' + (initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0]) + ' </span>' +
                        '<input type="text" class="edit form-control date contract-initial-date" ' + (prevIndex || index > 1 ? "disabled" : "") + ' value="' + initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0] + '">' +
                        '<span> até </span>' +
                        '<span class="info final-info-date"> ' + (finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0]) + ' </span>' +
                        '<input type="text" class="edit form-control date contract-final-date" ' + (prevIndex ? "disabled" : "") + ' value="' + finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0] + '">' +
                        '<br><label class="cc-label">Valor Total:</label> ' +
                        '<span class="info-total-value"> ' + Number(this.valor_total).toFixed(2) + ' </span>' +
                        '<table class="items-table table table-bordered" cellspacing="0" width="100%">' +
                        item +
                        '</table></div>';
                    $(".cc-panels").append(panel);
                    $(".col-item-icon").hide();
                    addDatePickers();
                    $(".col-item-value .info, .info-total-value, .col-item-value .used-value, .category-total-value, .plot-value-info").priceFormat({
                        prefix: 'R$ ',
                        centsSeparator: ',',
                        thousandsSeparator: '.',
                        allowNegative: true
                    });
                    $('.col-item-value .edit, .plot-value').maskMoney({
                        prefix: "R$ ",
                        thousands: ".",
                        decimal: ",",
                    });
                    i++;
                });
                $(".remove-cc").hide();
                $(".edit").hide();
                if (proposalAddictive) {
                    $(".project-edit").trigger("click", [proposalAddictive]);
                }
            }
        });
    } else {
        $(".project-info-container").hide();
        $(".project-menu").hide();
        $(".cc-container").hide();
        $(".manage-actions").hide();
    }
    $(".proposal.selected").removeClass("selected");
});
$(".projects").trigger("change");

$(document).on("click", ".remove-project", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=project/remove-project",
        data: {
            "id": $(".projects").val()
        },
        beforeSend: function () {
            $(".remove-project-loading-gif").show();
            $("#remove-project-modal span").css("opacity", "0.3");
            $(".remove-project").attr("disabled", "disabled");
        },
    }).success(function (data) {
        $(".remove-project-loading-gif").hide();
        $("#remove-project-modal span").css("opacity", "1");
        $(".remove-project").removeAttr("disabled");
        $(".projects option[value=" + $(".projects").val() + "]").remove();
        $(".projects").trigger("change");
        $("#remove-project-modal").modal("hide");
    });
});

$(document).on("click", ".manage-project-button", function () {
    var message = "";
    var error = false;
    if ($(".contract-name").val() == "") {
        message = 'O campo "nome" é obrigatório.';
        error = true;
    }
    if ($(".contract-supporter").val() == "") {
        message = 'O campo "apoiadora" é obrigatório.';
        error = true;
    }
    if (!error && ($(".contract-plots").val() == "" || $(".contract-plots").val() <= 0)) {
        message = 'O campo "parcelas" é obrigatório.';
        error = true;
    }
    var emptyDescriptions = $(".plot-description").filter(function () {
        return $(this).val() === "";
    });
    var emptyPayingSources = $(".plot-paying-source").filter(function () {
        return $(this).val() === "";
    });
    var emptyValues = $(".plot-value").filter(function () {
        return $(this).val() === "";
    });
    var emptyDates = $(".plot-date").filter(function () {
        return $(this).val() === "";
    });
    if (!error && (emptyDescriptions.length || emptyValues.length || emptyDates.length || emptyPayingSources.length)) {
        message = 'Preencha todos os campos das parcelas.';
        error = true;
    }

    if ($(".contract-conta-banco").val() !== "" || $(".contract-conta-agencia").val() !== "" || $(".contract-conta-tipo").val() !== "" ||
        $(".contract-conta-conta").val() !== "" || $(".contract-conta-proprietario").val() !== "" || $(".contract-conta-pix").val() !== "") {
        if (($(".contract-conta-banco").val() == "" || $(".contract-conta-agencia").val() == "" || $(".contract-conta-tipo").val() == "" ||
            $(".contract-conta-conta").val() == "" || $(".contract-conta-proprietario").val() == "") && !error) {
            message = "Informe todos os dados da conta bancária (apenas o campo pix é opcional).";
            error = true;
        }
    }

    if ($(".contract-coordenadores").val() == null) {
        message = "Selecione ao menos um Coordenador para o projeto.";
        error = true;
    }

    var cc = $(".cc-panels").children().last();

    if (!error && $(cc).find(".celebration-date").length && $(cc).find(".celebration-date").val() == "") {
        message = 'O campo "celebração" é obrigatório.';
        error = true;
    }

    if (($(cc).find(".contract-initial-date").val() == "" || $(cc).find(".contract-final-date").val() == "") && !error) {
        message = 'O campos de "vigência" são obrigatórios.';
        error = true;
    }

    if ($(cc).find(".celebration-date").length && $(cc).find(".celebration-date").val() != "" && $(cc).find(".contract-final-date").val() != "") {
        var celebrationDateSplit = $(cc).find(".celebration-date").val().split("/");
        var finalDateSplit = $(cc).find(".contract-final-date").val().split("/");
        var celebrationDate = new Date(celebrationDateSplit[2], celebrationDateSplit[1] - 1, celebrationDateSplit[0], 0, 0, 0);
        var finalDate = new Date(finalDateSplit[2], finalDateSplit[1] - 1, finalDateSplit[0], 0, 0, 0);
        if (finalDate <= celebrationDate) {
            message = 'A data de celebração do termo aditivo deve ser inferior à data final da vigência.';
            error = true;
        }
    }

    $(cc).find(".item-description, .item-value, .item-source").each(function () {
        if ($(this).val() == "" && !error) {
            message = 'Os campos "Descrição", "Valor Total" e "Categoria" das rubricas são obrigatórios.';
            error = true;
            return;
        }
    });

    var inputs = $(cc).find(".item-description");
    var repeatable = inputs.filter(function (i, el) {
        return inputs.not(this).filter(function () {
            return this.value === el.value;
        }).length !== 0;
    }).val();
    if (repeatable != undefined && !error) {
        message = 'A rubrica "' + repeatable + '" foi cadastrada mais de uma vez no mesmo contrato.';
        error = true;
    }

    $(cc).find(".item").each(function () {
        if ((($(this).find(".item-value").unmask() / 100) < (Number($(this).find(".used-value-contracts").unmask()) / 100) || ($(this).find(".item-value").unmask() / 100) < (Number($(this).find(".used-value-launches").unmask()) / 100)) && !error) {
            message = 'O valor total da rubrica não pode ser inferior aos valores já utilizados.';
            error = true;
            return;
        }

        if (($(this).find(".used-value-contracts").unmask() / 100) > 0 && !$(this).find(".item-vinculate").is(":checked") && !error) {
            message = 'As rubricas com contratos vinculados devem estar marcados como vinculantes.';
            error = true;
            return;
        }
    });

    var plotSum = 0;
    $(".contract-plot").each(function (index) {
        if (!error) {
            var contractInitialDateSplit = $(cc).find(".contract-initial-date").val().split("/");
            var contractFinalDateSplit = $(cc).find(".contract-final-date").val().split("/");
            var plotDateSplit = $(this).find(".plot-date").val().split("/");
            var contractInitialDate = new Date(contractInitialDateSplit[2], contractInitialDateSplit[1] - 1, contractInitialDateSplit[0], 0, 0, 0);
            var contractFinalDate = new Date(contractFinalDateSplit[2], contractFinalDateSplit[1] - 1, contractFinalDateSplit[0], 0, 0, 0);
            var plotDate = new Date(plotDateSplit[2], plotDateSplit[1] - 1, plotDateSplit[0], 0, 0, 0);
            if (plotDate < contractInitialDate || plotDate > contractFinalDate) {
                message = 'As datas das parcelas devem estar entre o intervalo de vigência do contrato.';
                error = true;
            }
        }
        if (index > 0) {
            var prevDateStr = $($(".contract-plot").get(index - 1)).find(".plot-date").val();
            var dateStr = $($(".contract-plot").get(index)).find(".plot-date").val();
            var prevDateSplit = prevDateStr.split("/");
            var dateSplit = dateStr.split("/");
            var prevDate = new Date(prevDateSplit[2], prevDateSplit[1] - 1, prevDateSplit[0], 0, 0, 0);
            var date = new Date(dateSplit[2], dateSplit[1] - 1, dateSplit[0], 0, 0, 0);
            if (date < prevDate) {
                message = 'As datas das parcelas devem seguir uma ordem cronológica.';
                error = true;
            }
        }
        plotSum += $(this).find(".plot-value").unmask() / 100;
    });

    var totalValue = parseFloat($(cc).find(".info-total-value").unmask() / 100);
    if (parseFloat(plotSum.toFixed(2)) != parseFloat(totalValue.toFixed(2)) && !error) {
        message = 'O valor total das parcelas deve ser igual ao valor total do contrato.';
        error = true;
    }

    if (!error) {
        $(".project-error").hide();
        var parcelas = new Array();
        $(".contract-plot").each(function () {
            parcelas.push({
                id: $(this).find(".plot-id").val(),
                ordem: $(this).find(".plot-number").text().split("º")[0],
                descricao: $(this).find(".plot-description").val(),
                fontePagadora: $(this).find(".plot-paying-source").val(),
                valor: $(this).find(".plot-value").unmask() / 100,
                data: $(this).find(".plot-date").val(),
            });
        });
        var rubricas = new Array();
        var sum = 0;
        $(cc).find(".item").each(function () {
            sum += ($(this).find(".item-value").unmask() / 100);
            rubricas.push({
                id: $(this).find(".col-item-id").text(),
                proposta_id: $(this).find(".col-item-proposal-id").text(),
                descricao: $(this).find(".item-description").val(),
                valor_total: $(this).find(".item-value").unmask() / 100,
                tipo_de_contrato: $(this).find(".item-type").val(),
                fonte: $(this).find(".item-source").val(),
                vinculante: $(this).find(".item-vinculate").is(":checked") ? 1 : 0,
                categoria: $(this).closest("tbody").find(".category-id").val()
            });
        });
        var centroDeCusto = {
            id: $(cc).find(".cc-id").val(),
            ordem: $(cc).find(".order").val(),
            celebracao_termo_aditivo: $(cc).find(".celebration-date").val() == undefined ? "" : $(cc).find(".celebration-date").val(),
            data_inicial: $(cc).find(".contract-initial-date").val(),
            data_final: $(cc).find(".contract-final-date").val(),
            rubricas: rubricas,
            valor_total: sum,
        };
        var centrosDeCustoRemovidos = new Array();
        $(".removed-ccs > input").each(function () {
            centrosDeCustoRemovidos.push({
                id: $(this).val(),
            });
        });
        var rubricasRemovidas = new Array();
        $(".removed-items > input").each(function () {
            rubricasRemovidas.push({
                id: $(this).val(),
            });
        });
        $.ajax({
            method: "POST",
            url: "/index.php?r=project/manage-project",
            data: {
                "id": $(".project-id").val(),
                "proposta_id": $(".proposal.selected").find(".proposal-id").val() == undefined ? null : $(".proposal.selected").find(".proposal-id").val(),
                "nome": $(".contract-name").val(),
                "apoiadora": $(".contract-supporter").val(),
                "origem_publica": $(".contract-public-origin").is(":checked"),
                "parcelas": parcelas,
                "banco": $(".contract-conta-banco").val(),
                "tipo_de_conta": $(".contract-conta-tipo").val(),
                "proprietario": $(".contract-conta-proprietario").val(),
                "agencia": $(".contract-conta-agencia").val(),
                "conta": $(".contract-conta-conta").val(),
                "pix": $(".contract-conta-pix").val(),
                "coordenadores": $(".contract-coordenadores").val(),
                "centro_de_custo": centroDeCusto,
                "centros_de_custo_removidos": centrosDeCustoRemovidos,
                "rubricas_removidas": rubricasRemovidas,
            },
            beforeSend: function () {
                $(".load-project-loading-gif").show();
                $(".project-menu").css("opacity", "0.3");
                $(".project-info-container").css("opacity", "0.3");
                $(".cc-container").css("opacity", "0.3");
                $(".manage-project-button").attr("disabled", "disabled");
            },
        }).success(function (data) {
            data = JSON.parse(data);
            $(".projects").select2("destroy");
            if ($(".project-id").val() == "") {
                $(".projects").append("<option value='" + data.contrato.id + "'>" + data.contrato.nome + "</option>").val(data.contrato.id);
            } else {
                $(".projects option[value=" + data.contrato.id + "]").text(data.contrato.nome);
            }
            addSelect2();
            if ($(".proposal.selected").find(".proposal-id").val() != undefined) {
                removeProposal();
            }
            $(".projects-container").show();
            $(".projects").trigger("change");
        });
    } else {
        $(".project-error").text(message).show();
        $("html, body").animate({scrollTop: 0}, "fast");
    }
});

$(document).on("click", ".tab", function () {
    $(".tab").parent().removeClass("active");
    $(this).parent().addClass("active");
    $(".cc-panels").children().removeClass("active");
    $($(".cc-panels").children().get($(this).parent().index())).addClass("active");
    $(this).parent().index() < $(".cc-tabs > ul > li").length - 1 || $(".project-imported").val() == 1 || $(".info").is(":visible") ? $(".add-item-container").hide() : $(".add-item-container").show();
});

$(document).on("click", ".add-more-cc", function () {
    var number = Number($(".tab-additive").last().find(".additive-number").text());
    $(".cc-tabs, .cc-panels").find(".active").removeClass("active");
    $(".cc-tabs").find(".remove-cc").hide();
    $(".cc-tabs").children("ul").append('<li class="active"><span class="tab tab-additive"> <span class="additive-number">' + (number + 1) + '</span>º Termo Aditivo <i class="remove-cc fa fa-times"></i></span></li>');
    $(".cc-panels > div").last().find(".item-type").select2("destroy");
    $(".cc-panels").append($(".cc-panels > div").last().clone());
    !$(".cc-panels > div").last().find(".celebration-date").length ? $('<label class="cc-label">Celebração: <span class="red edit">*</span></label> <input type="text" class="edit form-control date celebration-date"><br/>').insertAfter($(".cc-panels > div").last().find(".order")) : $(".cc-panels > div").last().find(".celebration-date").val("");
    $(".cc-panels > div").last().find(".order").val(Number($(".cc-panels > div").last().find(".order").val()) + 1);
    $(".cc-panels > div").last().find(".cc-id").val("");
    $(".cc-panels > div").last().addClass("active");
    $(".cc-panels > div").last().find(".contract-initial-date").attr("disabled", "disabled");
    $('.item-value').maskMoney({
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });
    $(".item-type").select2({
        placeholder: "Selecione...",
        width: "200px",
        language: "pt-BR",
        allowClear: true,
    })
    addDatePickers();
    $(".add-more-cc").hide();
    $(".cc-panels > div").last().prev().find("input").attr("disabled", "disabled");
    $(".cc-panels > div").last().prev().find('select').select2("enable", false);
    $(".cc-panels > div").last().prev().find('.remove-item').removeClass("remove-item").addClass("remove-item-disabled").find("i").remove();
    $(".cc-tabs > ul > li.active").index() < $(".cc-tabs > ul > li").length - 1 || $(".project-imported").val() == 1 ? $(".add-item-container").hide() : $(".add-item-container").show();
});

$(document).on("click", ".remove-cc", function (e) {
    e.stopPropagation();
    var id = $(".cc-panels").children().last().find(".cc-id").val();
    if (id != "") {
        $(".removed-ccs").append('<input type="hidden" class="cc-removed" value=" ' + id + ' "/>');
    }
    $(".cc-panels").children().last().remove();
    $(".cc-panels").children().removeClass("active");
    $(".cc-panels").children().last().addClass("active");
    $(this).closest("li").remove();
    $(".tab").parent().removeClass("active");
    $(".cc-tabs").find("li").last().addClass("active");
    var sum = 0;
    $(".cc-panels > div").last().find(".used-value").each(function () {
        sum += Number($(this).unmask()) / 100;
    });
    if (sum == 0) {
        $(".cc-tabs").find("li").last().find(".remove-cc").show();
    }
    $(".add-more-cc").show();
    if ($(".cc-panels > div").last().index() == 0) {
        $(".cc-panels > div").last().find(".contract-initial-date").removeAttr("disabled");
    }
    if ($(".project-imported").val() == 1) {
        $(".cc-panels > div").last().find("input").not(".item-value, .item-description, .contract-initial-date").removeAttr("disabled");
    } else {
        $(".cc-panels > div").last().find("input").not(".contract-initial-date").removeAttr("disabled");
    }
    $(".cc-panels > div").last().find('select').select2("enable");
    $(".cc-panels > div").last().find('.remove-item-disabled').addClass("remove-item").removeClass("remove-item-disabled").append('<i class="fa fa-times"></i>');
    $(".cc-tabs > ul > li.active").index() < $(".cc-tabs > ul > li").length - 1 || $(".project-imported").val() == 1 ? $(".add-item-container").hide() : $(".add-item-container").show();
});

$(document).on("click", ".remove-item", function () {
    if ($(this).closest("tr").find(".col-item-id").text() != "" && $(this).closest("table").parent().find(".cc-id").val() !== "") {
        $(".removed-items").append('<input type="hidden" class="item-removed" value=" ' + $(this).closest("tr").find(".col-item-id").text() + ' "/>');
    }
    var table = $(this).closest("table");
    var tbody = $(this).closest("tbody");
    if (tbody.find(".item").length == 1) {
        tbody.remove();
    } else {
        $(this).closest("tr").remove();
    }
    refreshTotalValues(table);
});

$(document).on("keyup", ".item-value", function () {
    refreshTotalValues($(this).closest("table"));
});

$(document).on("click", ".add-item", function () {
    if ($(".categories").val() != "" && $(".item-quantity").val() > 0) {
        $.ajax({
            method: "POST",
            url: "/index.php?r=project/load-contract-types-and-sources",
            beforeSend: function () {
                $(".add-item-loading-gif").show();
                $(".add-item-container, .items-table").css("opacity", "0.3");
                $(".add-item").prop("disabled", true);
            },
        }).success(function (data) {
            $(".add-item-loading-gif").hide();
            $(".add-item-container, .items-table").css("opacity", "1");
            $(".add-item").prop("disabled", false);
            var items = "";
            for (i = 0; i < $(".item-quantity").val(); i++) {
                items += '<tr class="item">';
                items += '<td class="col-item-id"></td><td class="col-item-proposal-id"></td>' +
                    '<td class="col-item-description"><input class="form-control edit item-description"/></td>' +
                    '<td class="col-item-value"><div class="used-value-container"><div>Contratos: <span class="used-value used-value-contracts">0</span></div><div>Lançado: <span class="used-value used-value-launches">0</span></div></div><input class="form-control edit item-value"/></td>' +
                    '<td class="col-item-type"><input type="hidden" class="item-type-id"/><select class="edit form-control item-type"></select></td>' +
                    '<td class="col-item-source"><input type="hidden" class="item-source-id"/><select class="edit form-control item-source"></select>' +
                    '<td class="col-item-vinculate"><input type="checkbox" class="edit item-vinculate"/></td>' +
                    '<td class="col-item-icon remove-item"><i class="fa fa-times"/></td>';
                items += '</tr>';
            }
            var exists = false;
            $(".cc-panels > .active .category-id").each(function () {
                if ($(this).val() == $(".categories").val()) {
                    $(items).insertBefore($(this).closest("tbody").find(".category-total-row"));
                    exists = true;
                }
            });
            if (!exists) {
                var html = "";
                html += '<tbody>';
                html += '<tr><th colspan="8" class="category-name" pessoal="' + ($(".categories").val() == 2 ? 1 : 0) + '"><span>' + $('.categories').select2('data')[0]['text'] + '</span><input type="hidden" class="category-id" value="' + $(".categories").val() + '"</th></tr>';
                html += '<tr><th class="col-item-id"></th><th class="col-item-proposal-id"></th><th style="width: 33%;">Descrição</th><th style="width: 33%;">Valor Total<span class="used-value-title">Valor Utilizado</span></th><th style="width: 15%;">Vínculo</th><th style="width:14%">Categoria</th><th style="width:5%">Vinculante</th><th class="col-item-icon"></th></tr>';
                html += items;
                html += '<tr class="category-total-row"><td></td><td class="category-total-value">' + 0 + '</td><td></td><td></td><td class="col-item-icon"></td></tr>';
                html += '</tbody>';
                $(".cc-panels > .active .items-table").append(html);
            }
            data = JSON.parse(data);
            addContractTypes(data.tipos_de_contrato);
            addSources(data.fontes);
            $('.col-item-value .edit').maskMoney({
                prefix: "R$ ",
                thousands: ".",
                decimal: ",",
            });
            $('.category-total-value, .used-value').priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        });
    }
});

$(document).on("change", ".item-type", function () {
    $(this).parent().children(".item-type-id").val($(this).val());
});

function addContractTypes(data) {
    $(".item-type").children().remove();
    $(".item-type").append(new Option());
    $.each(data, function (index, value) {
        $(".item-type").append(new Option(value, index));
    });
    $(".item-type").select2({
        placeholder: "Selecione...",
        width: "200px",
        language: "pt-BR",
        allowClear: true,
    });
    $(".item-type").each(function () {
        $(this).val($(this).parent().find(".item-type-id").val()).trigger("change.select2");
    });
}

function addSources(data) {
    $(".item-source").children().remove();
    $(".item-source").append(new Option());
    $.each(data, function (index, value) {
        $(".item-source").append(new Option(value, index));
    });
    $(".item-source").select2({
        placeholder: "Selecione...",
        width: "200px",
        language: "pt-BR",
        allowClear: true,
    });
    $(".item-source").each(function () {
        $(this).val($(this).parent().find(".item-source-id").val()).trigger("change.select2");
    });
}

function addSelect2() {
    $(".projects, .contract-conta-banco, .contract-conta-tipo").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
        sorter: function (data) {
            return data.sort(function (a, b) {
                return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
            });
        }
    });

    $(".contract-coordenadores").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        sorter: function (data) {
            return data.sort(function (a, b) {
                return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
            });
        }
    });

    $(".categories").select2({
        placeholder: "Natureza de Despesa...",
        language: "pt-BR",
        allowClear: true,
        sorter: function (data) {
            return data.sort(function (a, b) {
                return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
            });
        }
    })

    $(".project-info-container .select2").addClass("edit");
}

function generateNewPanel(active, order) {
    return '<div class="' + active + '">' +
        '<input type="hidden" class="cc-id" value=""/>' +
        '<input type="hidden" class="order" value="' + order + '"/>' +
        '<label class="cc-label">Vigência: <span class="red edit">*</span></label> ' +
        '<input type="text" class="edit form-control date contract-initial-date">' +
        '<span> até </span>' +
        '<input type="text" class="edit form-control date contract-final-date">' +
        '<br><label class="cc-label">Valor Total:</label> ' +
        '<span class="info-total-value"> ' + 0 + ' </span>' +
        '<table class="items-table table table-bordered" cellspacing="0" width="100%"></table>' +
        '</div>';
}

function addDatePickers() {
    $('.contract-initial-date').datepicker({
        language: "pt-BR",
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
    }).on('changeDate', function (ev) {
        var finalDate = $(this).parent().find(".contract-final-date");
        if (finalDate.val() !== "" && ev.date !== undefined) {
            var startDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
            var endDateStr = finalDate.val().split("/");
            var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
            if (endDate < startDate) {
                $(this).val("");
                $(".project-error").show().text("A data inicial deve ser inferior à data final.");
                $("html, body").animate({scrollTop: 0}, "fast");
            }
        }
    });

    $('.contract-final-date').datepicker({
        language: "pt-BR",
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
    }).on('changeDate', function (ev) {
        var initialDate = $(this).parent().find(".contract-initial-date");
        if (initialDate.val() !== "" && ev.date !== undefined) {
            var endDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
            var startDateStr = initialDate.val().split("/");
            var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
            if (endDate < startDate) {
                $(this).val("");
                $(".project-error").show().text("A data final deve ser superior à data inicial.");
                $("html, body").animate({scrollTop: 0}, "fast");
            }
        }
    });
    $('.plot-date').datepicker({
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
        language: "pt-BR",
    }).on("changeDate", function (ev) {
        if (ev.date !== undefined && $(".cc-panels > div").length) {
            var date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
            var error = false;
            if ($(".cc-panels > div").last().find(".contract-final-date").val() !== "") {
                var endDateStr = $(".cc-panels > div").last().find(".contract-final-date").val().split("/");
                var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
                if (date > endDate) {
                    $(this).val("");
                    $(".project-error").show().text("A data da parcela deve ser igual ou inferior à data final do contrato.");
                    $("html, body").animate({scrollTop: 0}, "fast");
                    error = true;
                } else {
                    $(".project-error").hide();
                }
            }
            if (!error) {
                if ($(".cc-panels > div").last().find(".contract-initial-date").val() !== "") {
                    var startDateStr = $(".cc-panels > div").last().find(".contract-initial-date").val().split("/");
                    var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
                    if (date < startDate) {
                        $(this).val("");
                        $(".project-error").show().text("A data da parcela deve ser igual ou superior à data inicial do contrato");
                        $("html, body").animate({scrollTop: 0}, "fast");
                    } else {
                        $(".project-error").hide();
                    }
                }
            }
        }
    });

    var startDate = new Date(1900, 0, 1, 0, 0, 0);
    if ($(".contract-initial-date").first().val() !== "") {
        var startDateStr = $(".contract-initial-date").first().val().split("/");
        startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
    }
    $('.celebration-date').datepicker({
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
        language: "pt-BR",
        endDate: new Date(),
        startDate: startDate,
    }).on("changeDate", function (ev) {
        if (ev.date !== undefined) {
            var error = false;
            var date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
            if ($(this).parent().find(".contract-final-date").val() !== "") {
                var endDateStr = $(this).parent().find(".contract-final-date").val().split("/");
                var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
                if (date > endDate) {
                    $(this).val("");
                    $(".project-error").show().text("A data de celebração do termo aditivo não pode ser superior à data final.");
                    $("html, body").animate({scrollTop: 0}, "fast");
                    error = true;
                } else {
                    $(".project-error").hide();
                }
            }
            if (!error) {
                if ($(this).parent().find(".contract-initial-date").val() !== "") {
                    var startDateStr = $(this).parent().find(".contract-initial-date").val().split("/");
                    var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
                    if (date < startDate) {
                        $(this).val("");
                        $(".project-error").show().text("A data de celebração do termo aditivo não pode ser inferior à data inicial.");
                        $("html, body").animate({scrollTop: 0}, "fast");
                    } else {
                        $(".project-error").hide();
                    }
                }
            }
        }
    });
}

(function (original) {
    jQuery.fn.clone = function () {
        var result = original.apply(this, arguments),
            my_textareas = this.find('textarea').add(this.filter('textarea')),
            result_textareas = result.find('textarea').add(result.filter('textarea')),
            my_selects = this.find('select').add(this.filter('select')),
            result_selects = result.find('select').add(result.filter('select'));

        for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
        for (var i = 0, l = my_selects.length; i < l; ++i) result_selects[i].selectedIndex = my_selects[i].selectedIndex;

        return result;
    };
})(jQuery.fn.clone);

function refreshTotalValues(table) {
    var totalValue = 0;
    $(table).find('tbody').each(function () {
        var partialTotal = 0;
        $(this).find(".item-value").each(function () {
            partialTotal += Number($(this).unmask()) / 100;
        });
        $(this).find(".category-total-value").text(Number(partialTotal).toFixed(2));
        totalValue += partialTotal;
    });
    $(table).parent().find(".info-total-value").text(Number(totalValue).toFixed(2));
    $(table).parent().find(".info-total-value, .category-total-value").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
}

$(document).on("focusout", ".contract-plots", function () {
    var plotsInput = $(this).val();
    var renderedPlotsLength = $(".contract-plot").length;
    var plotsCount = plotsInput;
    if (plotsInput < renderedPlotsLength) {
        for (var i = plotsInput; i < renderedPlotsLength; i++) {
            $(".contract-plots-container").children().eq(plotsInput).remove();
        }
        plotsCount = 0;
    } else if (plotsInput == renderedPlotsLength) {
        plotsCount = 0;
    } else {
        plotsCount = plotsInput - renderedPlotsLength;
    }
    var html = "";
    for (var i = 0; i < plotsCount; i++) {
        html += '<div class="contract-plot form-inline">'
            + '<input type="hidden" class="plot-id">'
            + '<span class="plot-number">' + (renderedPlotsLength + i + 1) + 'º</span>'
            + '<textarea class="form-control plot-description" placeholder="Descrição da Parcela *"></textarea>'
            + '<input class="form-control plot-paying-source" type="text" placeholder="Fonte Pagadora *">'
            + '<input class="form-control plot-value" type="text" placeholder="Valor *">'
            + '<input class="form-control plot-date" type="text" placeholder="Data *">'
            + '<i class="remove-plot fa fa-times darkred"></i>'
            + '</div>';
    }
    $(".contract-plots-container").append(html);
    $(".plot-value").maskMoney({
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });
    $('.plot-date').datepicker({
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
        language: "pt-BR",
    }).on("changeDate", function (ev) {
        if (ev.date !== undefined && $(".cc-panels > div").length) {
            var date = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
            var error = false;
            if ($(".cc-panels > div").last().find(".contract-final-date").val() !== "") {
                var endDateStr = $(".cc-panels > div").last().find(".contract-final-date").val().split("/");
                var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
                if (date > endDate) {
                    $(this).val("");
                    $(".project-error").show().text("A data da parcela deve ser igual ou inferior à data final do contrato.");
                    $("html, body").animate({scrollTop: 0}, "fast");
                    error = true;
                } else {
                    $(".project-error").hide();
                }
            }
            if (!error) {
                if ($(".cc-panels > div").last().find(".contract-initial-date").val() !== "") {
                    var startDateStr = $(".cc-panels > div").last().find(".contract-initial-date").val().split("/");
                    var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
                    if (date < startDate) {
                        $(this).val("");
                        $(".project-error").show().text("A data da parcela deve ser igual ou superior à data inicial do contrato");
                        $("html, body").animate({scrollTop: 0}, "fast");
                    } else {
                        $(".project-error").hide();
                    }
                }
            }
        }
    });
});

$(document).on("click", ".remove-plot", function () {
    $(this).parent().remove();
    $(".contract-plots").val($(".contract-plots").val() - 1);
    $(".contract-plot").each(function (index) {
        $(this).find(".plot-number").text((index + 1) + "º")
    });
});

$(document).on("click", ".check-proposals", function () {
    if ($(".proposals-container").css("display") == "none") {
        $(".proposals-container").show();
        $(".add-new-container").css("margin-bottom", "100px");
    } else {
        $(".proposals-container").hide();
        $(".add-new-container").css("margin-bottom", "34px");
    }
});

$(document).on("click", ".proposal", function () {
    var proposal = this;
    if (!$(this).hasClass("selected")) {
        $.ajax({
            method: "POST",
            url: "/index.php?r=project/load-proposal",
            data: {
                "id": $(this).find(".proposal-id").val(),
            },
            beforeSend: function () {
                $(".load-project-loading-gif").show();
                $(".project-info-container, .cc-container").css("opacity", "0.3");
            },
        }).success(function (data) {
            data = JSON.parse(data);
            if (data.aditivo != null) {
                $(".projects-container").show();
                $(".projects").val(data.aditivo).trigger("change", [data]);
            } else {
                $(".add-new-project").click();
                $(".ribbon-title").html('<div class="ribbon-title-effect"></div><i class="fa fa-list"></i> Projeto (Integrado)');
                $(".contract-name").val(data.nome).attr("disabled", "disabled");
                $(".contract-supporter").val(data.cliente).attr("disabled", "disabled");
                $(".add-item-container").hide();
                buildProposalCCPanel(data);
                buildProposalTechPanel(data);
                $(".load-project-loading-gif").hide();
                $(".alert-gestao").show();
                $(".projects-container").hide();
                $(".project-info-container, .cc-container").css("opacity", "1");
                $(".proposal").removeClass("selected");
                $(proposal).addClass("selected");
            }
        });
    }
});

function buildProposalCCPanel(data) {
    var item = "";
    var startDate = data.data_inicial.split("-");
    var endDate = data.data_final.split("-");
    $(".cc-panels > div").last().find(".contract-initial-date").data('datepicker').setDate(new Date(startDate[0], startDate[1] - 1, startDate[2], 0, 0, 0));
    $(".cc-panels > div").last().find(".contract-final-date").data('datepicker').setDate(new Date(endDate[0], endDate[1] - 1, endDate[2], 0, 0, 0));
    $.each(data.categorias, function (index, categoria) {
        item += '<tbody>';
        item += '<tr><th colspan="8" class="category-name" pessoal="' + categoria.pessoal + '"><span>' + categoria.nome + '</span><input type="hidden" class="category-id" value="' + categoria.id + '"</th></tr>';
        item += '<tr><th class="col-item-id"></th><th class="col-item-proposal-id"></th><th style="width: 33%;">Descrição</th><th style="width: 33%;">Valor Total<span class="used-value-title">Valor Utilizado</span></th><th style="width: 15%;">Vínculo</th><th style="width:14%">Categoria</th><th style="width:5%">Vinculante</th><th class="col-item-icon"></th></tr>';
        $.each(this.rubricas, function () {
            var valor = (categoria.taxa == 0 && categoria.cmdca == 0 && categoria.encargo == 0 && categoria.ferias == 0 && categoria.decimo_terceiro == 0) ? this.valor_unitario * this.quantidade * this.ocorrencia : this.valor_unitario;
            item += '<tr class="item">';
            item += '<td class="col-item-id">' + (this.id_relacionado == null ? "" : this.id_relacionado) + '</td><td class="col-item-proposal-id">' + this.id + '</td>' +
                '<td class="col-item-description"><input disabled class="form-control edit item-description" value="' + this.nome + '"/></td>' +
                '<td class="col-item-value"><div class="used-value-container"><div>Contratos: <span class="used-value used-value-contracts">' + Number(this.valor_utilizado_contratos).toFixed(2) + '</span></div><div>Lançado: <span class="used-value used-value-launches">' + Number(this.valor_utilizado_lancamentos).toFixed(2) + '</span></div></div><input disabled class="form-control edit item-value ' + (categoria.taxa == 1 ? 'percent-tax' : '') + ' ' + (categoria.cmdca == 1 ? 'percent-cmdca' : '') + '" value="' + Number(valor).toFixed(2) + '"/></td>' +
                '<td class="col-item-type"><input type="hidden" class="item-type-id" value="' + (categoria.pessoal == 1 ? this.tipo_de_contrato_fk : this.tipo_de_contrato_fk_relacionado) + '"/><select class="edit form-control item-type"></select>' +
                '<td class="col-item-source"><input type="hidden" class="item-source-id" value="' + this.fonte_relacionada + '"/><select class="edit form-control item-source"></select>' +
                '<td class="col-item-vinculate"><input type="checkbox" class="edit item-vinculate" ' + (this.vinculante_relacionado == 0 ? "" : "checked") + '/></td><td class="col-item-icon"></td>';
            item += '</tr>';
        });
        item += '<tr class="category-total-row"><td></td><td class="category-total-value">0</td><td></td><td></td><td class="col-item-icon"></td></tr>';
        item += '</tbody>';
    });
    $(".cc-panels > div").last().find(".items-table").html(item);
    addContractTypes(data.tipos_de_contrato);
    addSources(data.fontes);

    var valorTotal = 0;
    $(".cc-panels > div").last().find(".item-value").not(".percent-tax").not(".percent-cmdca").each(function () {
        valorTotal += Number($(this).val());
    });
    var percentTax = 0;
    var percentCmdca = 0;
    $(".cc-panels > div").last().find(".item-value.percent-tax").each(function () {
        percentTax += Number($(this).val());
    });
    $(".cc-panels > div").last().find(".item-value.percent-cmdca").each(function () {
        percentCmdca += Number($(this).val());
    });
    var valorTotalComTaxas = valorTotal / (1 - (percentTax / 100));
    var valorTotalComTaxasECmdcas = valorTotalComTaxas / (1 - (percentCmdca / 100));
    $(".cc-panels > div").last().find(".info-total-value").text(Number(valorTotalComTaxasECmdcas).toFixed(2));
    $(".cc-panels > div").last().find(".item-value.percent-tax").each(function () {
        $(this).val(Number(valorTotalComTaxas * ($(this).val() / 100)).toFixed(2));
    });
    $(".cc-panels > div").last().find(".item-value.percent-cmdca").each(function () {
        $(this).val(Number(valorTotalComTaxasECmdcas * ($(this).val() / 100)).toFixed(2));
    });

    $(".cc-panels > div").last().find(".items-table").find("tbody").each(function () {
        var sum = 0;
        $(this).find(".item-value").each(function () {
            sum += Number($(this).val());
        });
        $(this).find(".category-total-value").text(Number(sum).toFixed(2));
    });

    $(".cc-panels > div").last().find(".col-item-value .used-value, .category-total-value, .info-total-value").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
    $(".cc-panels > div").last().find('.item-value').maskMoney({
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });
    $(".cc-panels > div").last().find('.item-value').trigger('mask.maskMoney');

    $('<button class="pull-right btn btn-default reject-proposal"><i class="fa fa-times-circle-o"></i> Rejeitar</button>').insertAfter(".manage-project-button");
    $(".manage-project-button").html('<i class="fa fa-plus"></i> Aceitar');
}

function buildProposalTechPanel(data) {
    $("#check-tech-info-modal").find(".specific-objective:not(:first)").remove();
    $("#check-tech-info-modal").find(".custom-container").remove();
    $("#check-tech-info-modal").find(".actions-container").children().remove();
    $(".prop-title-info").text(data.nome);
    $(".prop-client-info").text(data.cliente);
    var startDate = data.data_inicial.split("-");
    var endDate = data.data_final.split("-");
    $(".prop-period-initial-date").text(startDate[2] + "/" + startDate[1] + "/" + startDate[0]);
    $(".prop-period-final-date").text(endDate[2] + "/" + endDate[1] + "/" + endDate[0]);
    $.each(data.elaboracao, function (index, value) {
        if (value.descricao != "") {
            $(".generated-container > div").eq(index).show();
            if (index == 1) {
                var descricaoSplit = value.descricao.split("|");
                for (var i = 0; i < descricaoSplit.length; i++) {
                    if (i > 0) {
                        $('<div class="specific-objective">' +
                            '<div class="specific-objective-number">' + (i + 1) + 'º</div>' +
                            '<span class="prop-specific-objective">' + descricaoSplit[i] + '</span>' +
                            '</div>').insertBefore($(".generated-container").find(".clear"));
                    } else {
                        $(".generated-container > div").eq(index).find(".specific-objective").eq(i).find(".prop-specific-objective").text(descricaoSplit[i]);
                    }
                }
            } else {
                if (index >= 4) {
                    $(".generated-container").append('<div class="custom-container">' +
                        '<label>' + value.titulo + ':</label> ' +
                        '<span class="prop-custom">' + value.descricao + '</span>' +
                        '</div>');
                } else {
                    $(".generated-container > div").eq(index).find("span").text(value.descricao);
                }
            }
        } else {
            $(".generated-container > div").eq(index).hide();
        }
    });

    $.each(data.acoes, function (actionIndex) {
        $(".actions-container").append('<div class="action-container form-inline form-group" action="' + actionIndex + '">' +
            '<label class="action-prop-label">Ação ' + actionIndex + ':</label>' +
            '<span class="prop-action-description"></span>' +
            '<div class="goals-container"></div>' +
            '</div>');
        var action = $(".actions-container").find(".action-container").eq(actionIndex - 1);
        action.find(".prop-action-description").text(this.descricao);
        $.each(this.metas, function (goalIndex) {
            action.find(".goals-container").append('<div class="goal-container form-inline form-group" goal="' + goalIndex + '">' +
                '<label class="goal-prop-label">Meta ' + actionIndex + '.' + goalIndex + ':</label>' +
                '<span class="prop-goal-description"></span>' +
                '<div class="stages-container"></div>' +
                '</div>');
            var goal = action.find(".goal-container").eq(goalIndex - 1);
            goal.find(".prop-goal-description").text(this.descricao);
            $.each(this.etapas, function (stageIndex) {
                goal.find(".stages-container").append('<div class="stage-container form-inline form-group" stage="' + stageIndex + '">' +
                    '<label class="stage-prop-label">Etapa ' + actionIndex + '.' + goalIndex + '.' + stageIndex + ':</label>' +
                    '<span class="prop-stage-description"></span>' +
                    '<div class="form-inline form-group stage-date-container">' +
                    '<label class="stage-prop-label">Data:</label>' +
                    '<span class="prop-stage-initial-date"></span>' +
                    '<span> até </span>' +
                    '<span class="prop-stage-final-date"></span>*' +
                    '</div>' +
                    '<div class="products-container"></div>' +
                    '</div>');
                var stage = goal.find(".stage-container").eq(stageIndex - 1);
                stage.find(".prop-stage-description").text(this.descricao);
                var startDate = this.data_inicial.split("-");
                var endDate = this.data_final.split("-");
                stage.find(".prop-stage-initial-date").text(startDate[2] + "/" + startDate[1] + "/" + startDate[0]);
                stage.find(".prop-stage-final-date").text(endDate[2] + "/" + endDate[1] + "/" + endDate[0]);
                $.each(this.produtos, function (productIndex) {
                    stage.find(".products-container").append('<div class="product-container form-inline form-group" product="' + productIndex + '">' +
                        '<label class="product-prop-label">Produto ' + actionIndex + '.' + goalIndex + '.' + stageIndex + '.' + productIndex + ':</label>' +
                        '<span class="prop-product-description"></span>' +
                        '<div class="product-date-container">' +
                        '<label class="product-prop-label">Data de Entrega:</label>' +
                        '<span class="prop-product-delivery-date"></span>*' +
                        '</div>' +
                        '<div class="indicators-container">' +
                        '<label class="product-prop-label">Indicador(es):</label>' +
                        '<span class="prop-indicators"></span>' +
                        '</div>' +
                        '</div>');
                    var product = stage.find(".product-container").eq(productIndex - 1);
                    product.find(".prop-product-description").text(this.descricao);
                    var deliveryDate = this.data_de_entrega.split("-");
                    product.find(".prop-product-delivery-date").text(deliveryDate[2] + "/" + deliveryDate[1] + "/" + deliveryDate[0]);
                    var indicatorsLabel = "";
                    $.each(this.indicadores, function () {
                        indicatorsLabel += '<span class="indicator">' + this + '</span>';
                    });
                    product.find(".prop-indicators").html(indicatorsLabel);
                });
            });
        });
    });

    var monthNames = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];
    var proposalDates = getMonthsByRange(data.data_inicial, data.data_final);
    var monthsInEachYearCount = {};
    var monthColumns = "";
    for (var i = 0; i < proposalDates.length; i++) {
        var dateSplit = proposalDates[i].split("/");
        if (!(dateSplit[1] in monthsInEachYearCount)) {
            monthsInEachYearCount[dateSplit[1]] = 1;
        } else {
            monthsInEachYearCount[dateSplit[1]]++;
        }
        monthColumns += '<th month="' + dateSplit[0] + '" year="' + dateSplit[1] + '">' + monthNames[parseInt(dateSplit[0]) - 1] + '</th>';
    }
    var yearColumns = "";
    Object.keys(monthsInEachYearCount).forEach(function (key, index) {
        yearColumns += '<th class="increased-th" colspan="' + monthsInEachYearCount[key] + '">' + key + '</th>';
    });
    var stageRows = "";
    $(".stage-container").each(function () {
        var stageCompleteNumber = $(this).closest(".action-container").attr("action") + "." + $(this).closest(".goal-container").attr("goal") + "." + $(this).attr("stage");
        var stages = "";
        var stageDates = getMonthsByRange($(this).find(".prop-stage-initial-date").text(), $(this).find(".prop-stage-final-date").text());
        for (var i = 0; i < proposalDates.length; i++) {
            if (stageDates.indexOf(proposalDates[i]) > -1) {
                stages += "<td class='filled'>x</td>";
            } else {
                stages += "<td></td>";
            }
        }
        stageRows += '<tr><td><strong>' + stageCompleteNumber + '</strong></td>' + stages + '</tr>';
    })

    var html = '<table class="table table-bordered">' +
        '<thead>' +
        '<tr><th class="increased-th" rowspan="2">Etapas</th>' + yearColumns + '</tr>' +
        '<tr>' + monthColumns + '</tr>' +
        '</thead>' +
        '<tbody>' +
        stageRows +
        '</tbody>' +
        '</table>';
    $(".schedule").html(html);
}

function getMonthsByRange(startDate, endDate) {
    if (startDate.indexOf("-") > -1) {
        var start = startDate.split('-');
        var end = endDate.split('-');
        var startYear = parseInt(start[0]);
        var endYear = parseInt(end[0]);
    } else {
        var start = startDate.split('/');
        var end = endDate.split('/');
        var startYear = parseInt(start[2]);
        var endYear = parseInt(end[2]);
    }
    var dates = [];
    for (var i = startYear; i <= endYear; i++) {
        var endMonth = i != endYear ? 11 : parseInt(end[1]) - 1;
        var startMon = i === startYear ? parseInt(start[1]) - 1 : 0;
        for (var j = startMon; j <= endMonth; j = j > 12 ? j % 12 || 11 : j + 1) {
            dates.push([j + 1, i].join('/'));
        }
    }
    return dates;
}

$(document).on("click", ".reject-proposal", function () {
    $(".reject-justification").val("");
    $("#reject-justification-modal").find(".alert").hide();
    $("#reject-justification-modal").modal("show");
});

$(document).on("click", ".reject-proposal-button", function () {
    if ($(".reject-justification").val() != "") {
        $("#reject-justification-modal").find(".alert").hide();
        $.ajax({
            method: "POST",
            url: "/index.php?r=project/reject-proposal",
            data: {
                "id": $(".proposal.selected").find(".proposal-id").val(),
                "justificativa": $(".reject-justification").val(),
            },
            beforeSend: function () {
                $(".reject-justification-loading-gif").show();
                $("#reject-justification-modal").find(".modal-body").css("opacity", "0.3");
                $(".reject-proposal-button").attr("disabled", "disabled");
            },
        }).success(function (data) {
            data = JSON.parse(data);
            $(".reject-justification-loading-gif").hide();
            $("#reject-justification-modal").find(".modal-body").css("opacity", "1");
            $(".reject-proposal-button").removeAttr("disabled");
            if (data.valid) {
                $("#reject-justification-modal").modal("hide");
                $(".project-error").hide();
                removeProposal();
                $(".projects-container").show();
                $(".projects").trigger("change");
            } else {
                $("#reject-justification-modal").find(".alert").html(data.error + "<br>" + data.mail_error).show();
            }
        });
    } else {
        $("#reject-justification-modal").find(".alert").html("Justifique a rejeição da proposta.").show();
    }
});

function removeProposal() {
    $(".proposal.selected").remove();
    $(".pending-proposals").each(function () {
        if ($(this).text() > 1) {
            $(this).text(Number($(this).text()) - 1);
        } else {
            $(this).remove();
            $(".check-proposals").remove();
            $(".proposals-container").remove();
            $(".add-new-container").css("margin-bottom", "34px");
        }
    });
}

$(document).on("click", ".add-new-free-project", function () {
    $(".free-project-id, .free-project-name, .free-project-initial-date, .free-project-final-date").val("");
    $(".free-project-public-origin").prop("checked", false);
    $("#manage-free-project-modal .alert").hide();
    $("#manage-free-project-modal").modal("show");
});

$(document).on("click", ".manage-free-project-button", function () {
    if ($(".free-project-name").val() !== "" && correctIntervalDate) {
        $("#manage-free-project-modal").find(".alert").hide();
        $.ajax({
            method: "POST",
            url: "/index.php?r=project/manage-free-project",
            data: {
                "id": $(".free-project-id").val(),
                "nome": $(".free-project-name").val(),
                "origem_publica": $(".free-project-public-origin").is(":checked"),
                "data_inicial": $(".free-project-initial-date").val(),
                "data_final": $(".free-project-final-date").val()
            },
            beforeSend: function () {
                $(".manage-free-project-loading-gif").show();
                $("#manage-free-project-modal .form-inline").css("opacity", "0.3");
                $(".manage-free-project-button").attr("disabled", "disabled");
            },
        }).success(function (data) {
            $(".manage-free-project-loading-gif").hide();
            $("#manage-free-project-modal .form-inline").css("opacity", "1");
            $(".manage-free-project-button").removeAttr("disabled");
            data = JSON.parse(data);
            $(".projects").select2("destroy");
            if ($(".free-project-id").val() == "") {
                $(".projects").append("<option value='" + data.contrato.id + "'>" + data.contrato.nome + "</option>").val(data.contrato.id);
            } else {
                $(".projects option[value=" + data.contrato.id + "]").text(data.contrato.nome);
            }
            addSelect2();
            $(".projects-container").show();
            $(".projects").trigger("change");
            $("#manage-free-project-modal").modal("hide");
        });
    } else {
        $("#manage-free-project-modal").find(".alert").html('Preencha os campos de pesquisa corretamente').show();
    }
});

$(document).on("keydown", ".contract-initial-date, .contract-final-date, .celebration-date, .plot-date", function (e) {
    if (e.keyCode == 8 || e.keyCode == 46) {
        $(this).val("");
    } else {
        e.preventDefault();
    }
});