$(".providers-menu").addClass("active");

initSelect2();

$(".provider-cpf").inputmask("999.999.999-99");
$(".provider-pis").inputmask("999.99999.99-9");
$(".provider-cnpj").inputmask("99.999.999/9999-99");
$(".provider-telefone").inputmask("(99) 99999-9999");

$('.contract-initial-date').datepicker({
    language: "pt-BR",
    format: "dd/mm/yyyy",
    todayHighlight: true,
    autoclose: true,
}).on('changeDate', function (ev) {
    if ($(".contract-final-date").val() !== "" && ev.date !== undefined) {
        var startDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
        var endDateStr = $(".contract-final-date").val().split("/");
        var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
        if (endDate < startDate) {
            $(".contract-initial-date").val("");
            $("#manage-contract-modal").find(".alert").show().text("A data inicial deve ser inferior à data final.");
        }
        if ($(".contract-plots").val() > 0) {
            $(".activity-date").each(function () {
                $(this).show();
                $(this).data('datepicker').setStartDate(startDate);
                $(this).data('datepicker').setEndDate(endDate);
            });
        }
    }
}).on('focusout', function (ev) {
    if ($(".contract-initial-date").val() === "") {
        $(".activity-date").each(function () {
            $(this).hide();
        });
    }
});


$('.contract-final-date').datepicker({
    language: "pt-BR",
    format: "dd/mm/yyyy",
    todayHighlight: true,
    autoclose: true,
}).on('changeDate', function (ev) {
    if ($(".contract-initial-date").val() !== "" && ev.date !== undefined) {
        var endDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
        var startDateStr = $(".contract-initial-date").val().split("/");
        var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
        if (endDate < startDate) {
            $(".contract-final-date").val("");
            $("#manage-contract-modal").find(".alert").show().text("A data final deve ser superior à data inicial.");
        }
        if ($(".contract-plots").val() > 0) {
            $(".activity-date").each(function () {
                $(this).show();
                $(this).data('datepicker').setStartDate(startDate);
                $(this).data('datepicker').setEndDate(endDate);
            });
        }
    }
}).on('focusout', function (ev) {
    if ($(".contract-final-date").val() === "") {
        $(".activity-date").each(function () {
            $(this).hide();
        });
    }
});

$('.provision-initial-date').datepicker({
    language: "pt-BR",
    todayHighlight: true,
    format: "dd/mm/yyyy",
    autoclose: true,
    startDate: new Date()
}).on('changeDate', function (ev) {
    if ($(".provision-final-date").val() !== "" && ev.date !== undefined) {
        var startDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
        var endDateStr = $(".provision-final-date").val().split("/");
        var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
        if (endDate < startDate) {
            $(".provision-initial-date").val("");
            $("#provision-modal-date").find(".alert").show().text("A data inicial deve ser inferior à data final.");
        }
    }
});

$('.provision-final-date').datepicker({
    language: "pt-BR",
    todayHighlight: true,
    format: "dd/mm/yyyy",
    autoclose: true,
    startDate: new Date()
}).on('changeDate', function (ev) {
    if ($(".provision-initial-date").val() !== "" && ev.date !== undefined) {
        var endDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
        var startDateStr = $(".provision-initial-date").val().split("/");
        var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
        if (endDate < startDate) {
            $(".provision-final-date").val("");
            $("#provision-modal-date").find(".alert").show().text("A data final deve ser superior à data inicial.");
        }
    }
});

$('.contract-value').maskMoney({
    prefix: "R$ ",
    thousands: ".",
    decimal: ",",
});

$('.unitary-value').maskMoney({
    prefix: "R$ ",
    thousands: ".",
    decimal: ",",
});

$(document).ready(function() {
    $('#workload-check').change(function() {
      if ($(this).is(':checked')) {
        $(".checkbox-workload").hide();
        $(".hide-container-unitary").css('display', 'inline-flex');
      } else {
        $(".hide-container-unitary").hide();
      }
    });
  });

$(document).on("click", ".add-new-provider", function () {
    $("#manage-provider-modal input[type=text]").val("");
    $("#manage-provider-modal input[type=email]").val("");
    $("#manage-provider-modal input[type=radio]").prop('checked', false);
    $("#manage-provider-modal .provider-conta-banco, #manage-provider-modal .provider-conta-tipo").val("").trigger("change.select2");
    $("#manage-provider-modal .modal-title").text("Adicionar Fornecedor");
    $("#manage-provider-modal .manage-provider-id").val("");
    $("#manage-provider-modal .manage-provider").html('<i class="fa fa-plus"></i> Adicionar');
    $(".provider-data-container").hide();
    $(".conta-container").hide();
    $("#manage-provider-modal .alert").hide();
    $("#manage-provider-modal").modal("show");
});

$(document).on("click", ".provider-edit", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=provider/load-provider",
        data: {
            "id": $(".providers").val()
        },
        beforeSend: function () {
            $(icon).removeClass("fa-edit").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-edit");
        data = JSON.parse(data);
        $("#manage-provider-modal .modal-title").text("Alterar Fornecedor");
        $("#manage-provider-modal .manage-provider-id").val(data.fornecedor.id);
        $("#manage-provider-modal .provider-name").val(data.fornecedor.nome);
        $("#manage-provider-modal .provider-cpf").val(data.fornecedor.cpf);
        $("#manage-provider-modal .provider-cnpj").val(data.fornecedor.cnpj);
        $("#manage-provider-modal .provider-representante").val(data.fornecedor.respresentante_legal);
        $("#manage-provider-modal .provider-pis").val(data.fornecedor.pis);
        $("#manage-provider-modal .provider-rg").val(data.fornecedor.rg);
        $("#manage-provider-modal .provider-email").val(data.fornecedor.email);
        $("#manage-provider-modal .provider-endereco").val(data.fornecedor.endereco);
        $("#manage-provider-modal .provider-telefone").val(data.fornecedor.telefone);
        $("input[name=type]").each(function () {
            if ($(this).val() == data.fornecedor.tipo_de_contrato_fk) {
                $(this).prop("checked", true).trigger("change");
            }
        })
        $("#manage-provider-modal .provider-conta-banco").val(data.conta_bancaria.banco_fk).trigger("change");
        $("#manage-provider-modal .provider-conta-tipo").val(data.conta_bancaria.tipo_de_conta).trigger("change.select2");
        $("#manage-provider-modal .provider-conta-agencia").val(data.conta_bancaria.agencia);
        $("#manage-provider-modal .provider-conta-conta").val(data.conta_bancaria.conta);
        $("#manage-provider-modal .provider-conta-proprietario").val(data.conta_bancaria.proprietario);
        $("#manage-provider-modal .provider-conta-pix").val(data.conta_bancaria.pix);
        $("#manage-provider-modal .manage-provider").html('<i class="fa fa-edit"></i> Alterar');
        $("#manage-provider-modal .alert").hide();
        $("#manage-provider-modal").modal("show");
    });
});

$(document).on("click", ".provider-info", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=provider/load-provider",
        data: {
            "id": $(".providers").val()
        },
        beforeSend: function () {
            $(icon).removeClass("fa-question-circle-o").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-question-circle-o");
        data = JSON.parse(data);

        $("#load-provider-modal .provider-name-info").text(data.fornecedor.nome);
        $("#load-provider-modal .provider-tipo-contrato-info").text(data.tipo_de_contrato);
        data.fornecedor.cnpj != null ? $("#load-provider-modal .provider-cnpj-info").text(data.fornecedor.cnpj).parent().show() : $("#load-provider-modal .provider-cnpj-info").parent().hide();
        data.fornecedor.profissao != null ? $("#load-provider-modal .provider-profissao-info").text(data.fornecedor.profissao).parent().show() : $("#load-provider-modal .provider-profissao-info").parent().hide();
        data.fornecedor.respresentante_legal != null ? $("#load-provider-modal .provider-representante-info").text(data.fornecedor.respresentante_legal).parent().show() : $("#load-provider-modal .provider-representante-info").parent().hide();
        data.fornecedor.rg != null ? $("#load-provider-modal .provider-rg-info").text(data.fornecedor.rg).parent().show() : $("#load-provider-modal .provider-rg-info").parent().hide();
        data.fornecedor.pis != null ? $("#load-provider-modal .provider-pis-info").text(data.fornecedor.pis).parent().show() : $("#load-provider-modal .provider-pis-info").parent().hide();
        data.fornecedor.cpf != null ? $("#load-provider-modal .provider-cpf-info").text(data.fornecedor.cpf).parent().show() : $("#load-provider-modal .provider-cpf-info").parent().hide();
        data.fornecedor.endereco != null ? $("#load-provider-modal .provider-endereco-info").text(data.fornecedor.endereco).parent().show() : $("#load-provider-modal .provider-endereco-info").parent().hide();
        data.fornecedor.email != null ? $("#load-provider-modal .provider-email-info").text(data.fornecedor.email).parent().show() : $("#load-provider-modal .provider-email-info").parent().hide();
        data.fornecedor.telefone != null ? $("#load-provider-modal .provider-telefone-info").text(data.fornecedor.telefone).parent().show() : $("#load-provider-modal .provider-telefone-info").parent().hide();
        data.conta_bancaria.banco != null ? $("#load-provider-modal .provider-conta-banco-info").text(data.conta_bancaria.banco).parent().show() : $("#load-provider-modal .provider-conta-banco-info").parent().hide();
        data.conta_bancaria.tipo_de_conta != null ? $("#load-provider-modal .provider-conta-tipo-info").text(translateTipoDeConta(data.conta_bancaria.tipo_de_conta)).parent().show() : $("#load-provider-modal .provider-conta-tipo-info").parent().hide();
        data.conta_bancaria.agencia != null ? $("#load-provider-modal .provider-conta-agencia-info").text(data.conta_bancaria.agencia).parent().show() : $("#load-provider-modal .provider-conta-agencia-info").parent().hide();
        data.conta_bancaria.conta != null ? $("#load-provider-modal .provider-conta-conta-info").text(data.conta_bancaria.conta).parent().show() : $("#load-provider-modal .provider-conta-conta-info").parent().hide();
        data.conta_bancaria.proprietario != null ? $("#load-provider-modal .provider-conta-proprietario-info").text(data.conta_bancaria.proprietario).parent().show() : $("#load-provider-modal .provider-conta-proprietario-info").parent().hide();
        data.conta_bancaria.pix != null ? $("#load-provider-modal .provider-conta-pix-info").text(data.conta_bancaria.pix).parent().show() : $("#load-provider-modal .provider-conta-pix-info").parent().hide();
        $("#load-provider-modal").modal("show");
    });
});

$(document).on("change", "#manage-provider-modal input[type=radio]", function () {
    var type = $("#manage-provider-modal input[type=radio]:checked").parent().text();
    if (type === "Pessoa Jurídica") {
        $(".cnpj-container").show();
        $(".representante-container").show();
        $(".profissao-container").show();
        $(".rg-container").hide().find("input").val("");
        $(".nome-container").children(".control-label").html('Razão Social <span class="red">*</span>');
        $(".cpf-container").children(".control-label").html('CPF do Representante');
    } else {
        $(".cnpj-container").hide().find("input").val("");
        $(".representante-container").hide().find("input").val("");
        $(".profissao-container").hide().find("input").val("");
        $(".rg-container").show();
        $(".nome-container").children(".control-label").html('Nome <span class="red">*</span>');
        $(".cpf-container").children(".control-label").html('CPF <span class="red">*</span>');
        (type === "CLT" || type === "RPA") ? $(".pis-container").show() : $(".pis-container").hide().find("input").val("");
    }
    $(".provider-data-container").show();
    $(".conta-container").show();
});

$(document).on("click", ".manage-provider", function () {
    var type = $("#manage-provider-modal input[type=radio]:checked").parent().text();
    var error = false;
    var message = "";
    $("#manage-provider-modal .alert").hide();
    if (type === "Pessoa Jurídica") {
        if ($(".provider-name").val() == "") {
            message = "Informe uma razão social.";
            error = true;
        } else if ($(".provider-cnpj").val() == "" || $(".provider-cnpj").inputmask('unmaskedvalue').length !== 14) {
            message = "Informe um CNPJ válido.";
            error = true;
        } else if ($(".provider-cpf").val() !== "" && $(".provider-cpf").inputmask('unmaskedvalue').length !== 11) {
            message = "Informe um CPF válido.";
            error = true;
        }
    } else {
        if ($(".provider-name").val() == "") {
            message = "Informe um nome.";
            error = true;
        } else if ($(".provider-cpf").val() == "" || $(".provider-cpf").inputmask('unmaskedvalue').length !== 11) {
            message = "Informe um CPF válido.";
            error = true;
        }
    }
    if ($(".provider-pis").val() !== "" && $(".provider-pis").inputmask('unmaskedvalue').length < 11) {
        message = "Informe um PIS válido.";
        error = true;
    }
    if ($(".provider-telefone").val() !== "" && $(".provider-telefone").inputmask('unmaskedvalue').length < 10) {
        message = "Informe um telefone válido.";
        error = true;
    }
    var testMail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if ($(".provider-email").val() !== "" && !testMail.test($(".provider-email").val())) {
        message = "Informe um email válido.";
        error = true;
    }
    if ($(".provider-conta-banco").val() !== "" || $(".provider-conta-agencia").val() !== "" || $(".provider-conta-tipo").val() !== "" ||
        $(".provider-conta-conta").val() !== "" || $(".provider-conta-proprietario").val() !== "" || $(".provider-conta-pix").val() !== "") {
        if ($(".provider-conta-banco").val() == "" || $(".provider-conta-agencia").val() == "" || $(".provider-conta-tipo").val() == "" ||
            $(".provider-conta-conta").val() == "" || $(".provider-conta-proprietario").val() == "") {
            message = "Informe todos os campos da conta bancária (apenas o campo pix é opcional).";
            error = true;
        }
    }

    if (!error) {
        $("#manage-provider-modal .alert").hide();
        $.ajax({
            method: "POST",
            url: "/index.php?r=provider/manage-provider",
            data: {
                "id": $(".manage-provider-id").val(),
                "tipo_de_contrato_fk": $("input[type=radio]:checked").val(),
                "name": $(".provider-name").val(),
                "cpf": $(".provider-cpf").val(),
                "cnpj": $(".provider-cnpj").val(),
                "rg": $(".provider-rg").val(),
                "pis": $(".provider-pis").val(),
                "email": $(".provider-email").val(),
                "endereco": $(".provider-endereco").val(),
                "telefone": $(".provider-telefone").val(),
                "respresentante_legal": $(".provider-representante").val(),
                "profissao": $(".provider-profissao").val(),
                "banco": $(".provider-conta-banco").val(),
                "tipo_de_conta": $(".provider-conta-tipo").val(),
                "agencia": $(".provider-conta-agencia").val(),
                "conta": $(".provider-conta-conta").val(),
                "proprietario": $(".provider-conta-proprietario").val(),
                "pix": $(".provider-conta-pix").val()
            },
            beforeSend: function () {
                $(".manage-provider-loading-gif").show();
                $(".manage-provider-container").css("opacity", "0.3");
                $(".manage-provider").attr("disabled", "disabled");
            },
        }).success(function (data) {
            $(".manage-provider-loading-gif").hide();
            $(".manage-provider-container").css("opacity", "1");
            $(".manage-provider").removeAttr("disabled");
            data = JSON.parse(data);
            $("#manage-provider-modal").modal("hide");
            var value = data.fornecedor.nome + "|" + (data.fornecedor.cpf == null ? "" : data.fornecedor.cpf) + "|" + (data.fornecedor.cnpj == null ? "" : data.fornecedor.cnpj);
            if ($(".manage-provider-id").val() !== "") {
                $(".providers").select2("destroy");
                $(".providers option[value=" + data.fornecedor.id + "]").text(value);
                initSelect2();
            } else {
                $(".providers").append(new Option(value, data.fornecedor.id));
            }
            $(".providers").val(data.fornecedor.id).trigger("change");
        });
    } else {
        $("#manage-provider-modal .alert").show().text(message);
    }
});

$(document).on("change", ".providers", function () {
    if ($(this).val() !== "") {
        $.ajax({
            method: "POST",
            url: "/index.php?r=provider/load-provider",
            data: {
                "id": $(".providers").val()
            },
        }).success(function (data) {
            data = JSON.parse(data);
            $(".contract-provider-type").val(data.tipo_de_contrato);
        });
        $.ajax({
            method: "POST",
            url: "/index.php?r=provider/load-provider-items",
            data: {
                "id": $(".providers").val()
            },
            beforeSend: function () {
                $(".load-provider-loading-gif").show();
                $(".provider-menu, .provider-items").css("opacity", "0.3");
            },
        }).success(function (data) {
                $(".load-provider-loading-gif").hide();
                $("#provider-items-table").children().remove();
                data = JSON.parse(data);
                var html = '';
                var column = $(".contract-provider-type").val() === "CLT" ? "Meses" : "Parcelas";
                $.each(data.contratos, function (contract, index) {
                    html += '<tbody>';
                    html += '<tr><th colspan="7" class="contract-name">' + contract + '</th></tr>';
                    html += '<tr><th class="col-item-id"></th><th>Rubrica</th><th>Valor Total</th><th>' + column + '</th><th>Data Inicial</th><th>Data Final</th><th style="width: 110px;">Carga Horária</th><th>Valor Unitário</th><th></th></tr>';
                    $.each(index, function (index, item) {
                        console.log(item)
                        if(item.valor_unitario > 0.0) {
                            valor_unitario = Number(item.valor_unitario).toFixed(2);
                        }else {
                            valor_unitario = '';
                        }
                        if(item.carga_horaria > 0) {
                            carga_horaria = item.carga_horaria;
                        }else {
                            carga_horaria = '';
                        }
                        html += '<tr>';
                        var initialDate = item.data_inicial.split("-");
                        var finalDate = item.data_final.split("-");
                        html += '<td class="col-item-id">' + item.id + '</td>' +
                            '<td class="col-item-name">' + (item.ordem > 1 ? item.rubrica + " (" + (item.ordem - 1) + "º Termo Aditivo)" : item.rubrica) + '</td>' +
                            '<td class="col-item-money">' + Number(item.valor_total).toFixed(2) + '</td>' +
                            '<td class="col-item-plots">' + item.parcelas + '</td>' +
                            '<td class="col-item-initial-date">' + initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0] + '</td>' +
                            '<td class="col-item-final-date ' + (!item.ativo ? "inativo" : "") + '">' + finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0] + '</td>' +
                            '<td class="col-item-workload">'+ carga_horaria +'</td>'+
                            '<td class="col-item-money">'+ valor_unitario +'</td>'+
                            '<td class="table-icons"><i class="load-contract-icon fa fa-question-circle-o"></i>' + (data.admin ? '<i class="edit-contract-icon fa fa-edit"></i><i class="remove-contract-icon fa fa-times"></i>' : '') + '</td>';

                        html += '</tr>';
                    });
                    html += '</tbody>';
                });
                $("#provider-items-table").append(html);
                if (!data.admin) {
                    $(".table-icons").css("width", "46px")
                }
                $(".col-item-money").priceFormat({
                    prefix: 'R$ ',
                    centsSeparator: ',',
                    thousandsSeparator: '.',
                    allowNegative: true
                });

                if ($.isEmptyObject(data.contratos)) {
                    $(".provider-no-result").show();
                    $("#provider-items-table").hide();
                    $(".provider-provision-button").hide();
                } else {
                    $(".provider-no-result").hide();
                    $("#provider-items-table").show();
                    $(".provider-provision-button").show();
                }
                $(".provider-menu, .provider-items").css("opacity", "1").show();
            }
        );
    } else {
        $(".provider-menu, .provider-items").hide();
    }
})
if (getParameterByName("id") == null) {
    $(".providers").trigger("change");
} else {
    $(".providers").val(getParameterByName("id")).change();
}


$(document).on("click", ".add-new-contract", function () {
    $("#workload-check").prop('checked', false)
    $(".checkbox-workload").show();
    $(".hide-container-unitary").hide();
    $(".contracts").val("").trigger("change").removeAttr("disabled");
    $(".removed-activities").children().remove();
    $(".contract-items").removeAttr("disabled");
    $(".contract-items-container, .item-selected-container").hide();
    $(".item-providers-id").val("");
    $(".item-old-value").val(0);
    $("#manage-contract-modal").find(".alert").hide();
    $("#manage-contract-modal").modal("show");
});

$(document).on("click", ".edit-contract-icon", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=provider/load-contract",
        data: {
            "id": $(this).parent().parent().children(".col-item-id").text()
        },
        beforeSend: function () {
            $(icon).removeClass("fa-edit").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        $(icon).addClass("fa-edit").removeClass("fa-spin").removeClass("fa-spinner");
        data = JSON.parse(data);
        console.log(data)
        if(data.carga_horaria != null || data.valor_unitario > null) {
            $('.checkbox-workload').hide();
            $(".hide-container-unitary").css('display', 'inline-flex');
        }else {
            $("#workload-check").prop('checked', false)
            $(".checkbox-workload").show();
            $(".hide-container-unitary").hide();
        }
        if(data.valor_unitario > 0) {
            valor_unitario = Number(data.valor_unitario).toFixed(2);
        }else {
            valor_unitario = null;
        }
        if(data.carga_horaria != 0) {
            carga_horaria = data.carga_horaria;
        }else {
            carga_horaria = null;
        }
        $(".removed-activities").children().remove();
        $(".contracts").val(data.contrato_id).trigger("change", [false]).attr("disabled", "disabled");
        $(".contract-items").val(data.rubrica_id).trigger("change", [false]).attr("disabled", "disabled");
        var initialDate = data.data_inicial.split("-");
        var finalDate = data.data_final.split("-");
        $(".contract-initial-date").val(initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0]);
        $(".contract-final-date").val(finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0]);
        $(".contract-value").val(Number(data.valor_total).toFixed(2));
        $(".unitary-value").val(valor_unitario);
        $(".workload-value").val(carga_horaria);
        $(".item-old-value").val(Number(data.valor_total));
        $(".contract-plots").val(data.parcelas).trigger("focusout");
        $.each(data.atividades, function (index, value) {
            $($(".activity-id").get(index)).val(value.id);
            $($(".activity-description").get(index)).val(value.descricao);
            $($(".activity-value").get(index)).val(Number(value.valor).toFixed(2));
            var activityDate = value.data.split("-");
            $($(".activity-date").get(index)).val(activityDate[2] + "/" + activityDate[1] + "/" + activityDate[0]);
        });
        $('.activity-value').trigger('mask.maskMoney');
        $(".item-providers-id").val(data.id);
        $("#manage-contract-modal").find(".alert").hide();
        $("#manage-contract-modal").modal("show");
    });
});

$(document).on("change", ".contracts", function (evt, async) {
    if ($(this).val() !== "") {
        $.ajax({
            method: "POST",
            async: async,
            url: "/index.php?r=provider/load-contract-items",
            data: {
                "id": $(this).val()
            },
            beforeSend: function () {
                $(".load-items-loading-gif").show();
                $(".contract-items-container, .item-selected-container, .item-total-value").css("opacity", "0.3");
            },
        }).success(function (data) {
            $(".load-items-loading-gif").hide();
            $(".contract-items-container, .item-selected-container, .item-total-value").css("opacity", "1");
            data = JSON.parse(data);
            $(".contract-items").children().remove();
            $(".contract-items").append(new Option());
            $.each(data, function (index, value) {
                $(".contract-items").append(new Option(index, value));
            });
            $(".item-selected-container, .item-total-value").hide();
            $(".contract-items-container").show();
        });
    } else {
        $(".contract-items-container, .item-selected-container, .item-total-value").hide();
    }
});

$(document).on("change", ".contract-items", function (evt, async) {
    if ($(this).val() !== "") {
        $.ajax({
            method: "POST",
            async: async,
            url: "/index.php?r=provider/load-item-info",
            data: {
                "id": $(this).val()
            },
            beforeSend: function () {
                $(".load-item-info-loading-gif").show();
                $(".item-selected-container, .item-total-value").css("opacity", "0.3");
            },
        }).success(function (data) {
            $(".load-item-info-loading-gif").hide();
            $(".item-selected-container, .item-total-value").css("opacity", "1");
            data = JSON.parse(data);
            var initialDate = data.data_inicial.split("-");
            var finalDate = data.data_final.split("-");
            $(".item-total-value").text(Number(data.valor).toFixed(2));
            $(".item-total-value").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
            $('.contract-initial-date').data('datepicker').setStartDate(new Date(initialDate[0], initialDate[1] - 1, initialDate[2], 0, 0, 0));
            $('.contract-initial-date').data('datepicker').setEndDate(new Date(finalDate[0], finalDate[1] - 1, finalDate[2], 0, 0, 0));
            $('.contract-final-date').data('datepicker').setStartDate(new Date(initialDate[0], initialDate[1] - 1, initialDate[2], 0, 0, 0));
            $('.contract-final-date').data('datepicker').setEndDate(new Date(finalDate[0], finalDate[1] - 1, finalDate[2], 0, 0, 0));
            $(".item-selected-container input").val("");
            $(".contract-activities-container").children().remove();
            if ($(".contract-provider-type").val() === "CLT") {
                $(".contract-plots").parent().children("label").html('Meses <span class="red">*</span>');
            } else {
                $(".contract-plots").parent().children("label").html('Parcelas <span class="red">*</span>');
            }
            $(".item-selected-container, .item-total-value").show();
        });
    } else {
        $(".item-selected-container, .item-total-value").hide();
    }
});

function splitContractValue() {
    if ($(".contract-provider-type").val() === "CLT") {
        var valor = $(".contract-value").unmask() / 100;
        var qtdPlots = $(".contract-plots").val();
        if (qtdPlots > 0 && valor > 0) {
            $(".activity-value").each(function () {
                $(this).val(Number(valor / qtdPlots).toFixed(2));
            });
        }
    }
}

$(document).on("focusout", ".contract-value", function () {
    splitContractValue();
});

$(document).on("focusout", ".contract-plots", function () {
    var activityInput = $(this).val();
    var renderedActivitiesLength = $(".contract-activity").length;
    var activitiesCount = activityInput;
    if (activityInput < renderedActivitiesLength) {
        for (var i = activityInput; i < renderedActivitiesLength; i++) {
            var activity = $(".contract-activities-container").children().eq(activityInput);
            if ($(activity).find(".activity-id").val() != "") {
                $(".removed-activities").append('<input type="hidden" class="activity-removed" value="' + $(activity).find(".activity-id").val() + '"/>');
            }
            $(".contract-activities-container").children().eq(activityInput).remove();
        }
        activitiesCount = 0;
    } else if (activityInput == renderedActivitiesLength) {
        activitiesCount = 0;
    } else {
        activitiesCount = activityInput - renderedActivitiesLength;
    }
    var html = "";
    for (var i = 0; i < activitiesCount; i++) {
        html += '<div class="contract-activity form-inline form-group">'
            + '<input type="hidden" class="activity-id">'
            + '<span class="activity-number">' + (renderedActivitiesLength + i + 1) + 'º</span>'
            + '<textarea class="form-control activity-description" placeholder="Descrição da Atividade"></textarea>'
            + '<input class="form-control activity-value" type="text" placeholder="Valor *">'
            + '<input class="form-control activity-date" type="text" placeholder="Data *">'
            + '<i class="remove-activity fa fa-times darkred"></i>'
            + '</div>';
    }
    $(".contract-activities-container").append(html);
    $(".activity-value").maskMoney({
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });
    $('.activity-date').datepicker({
        format: "dd/mm/yyyy",
        todayHighlight: true,
        autoclose: true,
        language: "pt-BR",
    });
    if ($(".contract-final-date").val() === "" || $(".contract-initial-date").val() === "") {
        $(".activity-date").each(function () {
            $(this).hide();
        });
    } else {
        var startDateStr = $(".contract-initial-date").val().split("/");
        var startDate = new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
        var endDateStr = $(".contract-final-date").val().split("/");
        var endDate = new Date(endDateStr[2], endDateStr[1] - 1, endDateStr[0], 0, 0, 0);
        $(".activity-date").each(function () {
            $(this).data('datepicker').setStartDate(startDate).setEndDate(endDate);
        });
    }
    splitContractValue();
});

$(document).on("click", ".remove-activity", function () {
    if ($(this).parent().find(".activity-id").val() != "") {
        $(".removed-activities").append('<input type="hidden" class="activity-removed" value="' + $(this).parent().find(".activity-id").val() + '"/>');
    }
    $(this).parent().remove();
    $(".contract-plots").val($(".contract-plots").val() - 1);
    $(".contract-activity").each(function (index) {
        $(this).find(".activity-number").text((index + 1) + "º")
    });
});

$(document).on("click", ".manage-contract", function () {
    verifyWorkload = true;
    if($(".workload-value").val() != "" && $(".unitary-value").val() != "") {
        totalValueContract = $(".contract-value").unmask() / 100;
        unitaryValue = $(".unitary-value").unmask() / 100;
        workloadValue = $(".workload-value").val();
        if(unitaryValue * workloadValue != totalValueContract) {
            verifyWorkload = false;
        }
    }
    if( ($(".workload-value").val() == "" && $(".unitary-value").val() != "") || ($(".workload-value").val() != "" && $(".unitary-value").val() == "")) {
        verifyWorkload = false;
    }
    var emptyValues = $(".activity-value").filter(function () {
        return $(this).val() === "";
    });
    var emptyDates = $(".activity-date").filter(function () {
        return $(this).val() === "";
    });
    if ($(".contracts").val() !== "" && $(".contract-items").val() !== "" && $(".contract-initial-date").val() !== ""
        && $(".contract-final-date").val() !== "" && $(".contract-value").val() !== "" && $(".contract-plots").val() !== ""
        && !emptyValues.length && !emptyDates.length) {

        var totalValueDifference = Number($(".contract-value").unmask() / 100) - Number($(".item-old-value").val());
        if (totalValueDifference <= Number($(".item-total-value").unmask() / 100)) {
            var activityValues = 0;
            $(".activity-value").each(function () {
                activityValues += Number($(this).unmask());
            });
            if (activityValues === Number($(".contract-value").unmask())) {
                if(verifyWorkload) {
                    $("#manage-contract-modal .alert").hide();
                    var activities = new Array();
                    $(".contract-activity").each(function () {
                        activities.push({
                            id: $(this).find(".activity-id").val(),
                            number: $(this).find(".activity-number").text().split("º")[0],
                            description: $(this).find(".activity-description").val(),
                            value: $(this).find(".activity-value").unmask() / 100,
                            data: $(this).find(".activity-date").val(),
                        })
                    });
                    var removedActivities = new Array();
                    $(".removed-activities > input").each(function () {
                        removedActivities.push({
                            id: $(this).val(),
                        });
                    });
                    $.ajax({
                        method: "POST",
                        url: "/index.php?r=provider/manage-contract",
                        data: {
                            "id": $(".item-providers-id").val(),
                            "provider": $(".providers").val(),
                            "item": $(".contract-items").val(),
                            "initialDate": $(".contract-initial-date").val(),
                            "finalDate": $(".contract-final-date").val(),
                            "totalValue": $(".contract-value").unmask() / 100,
                            "plots": $(".contract-plots").val(),
                            "activities": activities,
                            "removedActivities": removedActivities,
                            "unitaryValue": $(".unitary-value").val() ? $(".unitary-value").unmask() / 100 : null,
                            "workloadValue": $(".workload-value").val()
                        },
                        beforeSend: function () {
                            $(".manage-contract-loading-gif").show();
                            $(".manage-contract-container").css("opacity", "0.3");
                            $(".manage-contract").attr("disabled", "disabled");
                        },
                    }).success(function (data) {
                        $(".manage-contract-loading-gif").hide();
                        $(".manage-contract-container").css("opacity", "1");
                        $(".manage-contract").removeAttr("disabled");
                        $(".providers").trigger("change");
                        $("#manage-contract-modal").modal("hide");
                    });
                }else {
                    $("#manage-contract-modal").animate({scrollTop: 0}, "fast");
                    $("#manage-contract-modal .alert").show().text("A carga horária multiplicada pelo valor unitário deve ser igual ao valor do contrato");
                }
            } else {
                $("#manage-contract-modal").animate({scrollTop: 0}, "fast");
                $("#manage-contract-modal .alert").show().text("A soma do valor das atividades deve ser igual ao valor do contrato.");
            }
        } else {
            $("#manage-contract-modal").animate({scrollTop: 0}, "fast");
            $("#manage-contract-modal .alert").show().text("O valor do contrato não pode exceder o valor restante da rubrica.");
        }
    } else {
        $("#manage-contract-modal").animate({scrollTop: 0}, "fast");
        $("#manage-contract-modal .alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("click", ".remove-provider", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=provider/remove-provider",
        data: {
            "id": $(".providers").val()
        },
        beforeSend: function () {
            $(".remove-provider-loading-gif").show();
            $("#remove-provider-modal span").css("opacity", "0.3");
            $(".remove-provider").attr("disabled", "disabled");
        },
    }).success(function (data) {
        $(".remove-provider-loading-gif").hide();
        $("#remove-provider-modal span").css("opacity", "1");
        $(".remove-provider").removeAttr("disabled");
        $(".providers option[value=" + $(".providers").val() + "]").remove();
        $(".providers").trigger("change");
        $("#remove-provider-modal").modal("hide");
    });
});

$(document).on("click", ".remove-contract-icon", function () {
    $(".remove-contract-id").val($(this).parent().parent().children(".col-item-id").text());
    $("#remove-contract-modal").modal("show");
})

$(document).on("click", ".remove-contract", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=provider/remove-contract",
        data: {
            "id": $(".remove-contract-id").val()
        },
        beforeSend: function () {
            $(".remove-contract-loading-gif").show();
            $("#remove-contract-modal span").css("opacity", "0.3");
            $(".remove-contract").attr("disabled", "disabled");
        },
    }).success(function (data) {
        $(".remove-contract-loading-gif").hide();
        $("#remove-contract-modal span").css("opacity", "1");
        $(".remove-contract").removeAttr("disabled");
        $(".providers").trigger("change");
        $("#remove-contract-modal").modal("hide");
    });
});

$(document).on("click", ".load-contract-icon", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=provider/load-contract",
        data: {
            "id": $(this).parent().parent().children(".col-item-id").text()
        },
        beforeSend: function () {
            $(icon).removeClass("fa-question-circle-o").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        $(icon).addClass("fa-question-circle-o").removeClass("fa-spin").removeClass("fa-spinner");
        data = JSON.parse(data);
        $(".contract-name-info").text(data.contrato);
        $(".contract-item-info").text(data.ordem > 1 ? data.rubrica + " (" + (data.ordem - 1) + "º Termo Aditivo)" : data.rubrica);
        var initialDate = data.data_inicial.split("-");
        var finalDate = data.data_final.split("-");
        $(".contract-initial-date-info").text(initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0]);
        $(".contract-final-date-info").text(finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0]);
        $(".contract-value-info").text(Number(data.valor_total).toFixed(2));
        if(data.valor_unitario > 0) {
            valor_unitario = Number(data.valor_unitario).toFixed(2);
        }else {
            valor_unitario = '';
        }
        if(data.carga_horaria != 0) {
            carga_horaria = data.carga_horaria;
        }else {
            carga_horaria = '';
        }
        $(".contract-workload-info").text(carga_horaria);
        $(".contract-unitary-value-info").text(valor_unitario);
        $(".contract-plots-info").text(data.parcelas);
        var html = "";
        var activityDate;
        $.each(data.atividades, function (index, value) {
            activityDate = value.data.split("-");
            html += '<div>'
                + '<span class="activity-number-info">' + value.ordem + 'º</span>'
                + '<span class="activity-description-info">' + (value.descricao == "" ? "-" : value.descricao) + '</span>'
                + '<span class="activity-value-info">' + Number(value.valor).toFixed(2) + '</span>'
                + '<span class="activity-date-info">' + activityDate[2] + '/' + activityDate[1] + '/' + activityDate[0] + '</span>'
                + '</div><hr class="plot-divisor"/>';
        });
        $(".contract-activities").html(html);
        $(".contract-value-info, .activity-value-info, .contract-unitary-value-info").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            allowNegative: true
        });
        $("#load-contract-modal").modal("show");
    });
});

function initSelect2() {
    $(".providers").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
        templateResult: providerResult,
        templateSelection: providerSelection,
        escapeMarkup: function (m) {
            return m;
        },
        sorter: function (data) {
            return data.sort(function (a, b) {
                a = a.text.toLowerCase();
                b = b.text.toLowerCase();
                if (a > b) {
                    return 1;
                } else if (a < b) {
                    return -1;
                }
                return 0;
            });
        }
    });

    $(".contracts, .contract-items, .provider-conta-banco, .provider-conta-tipo").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
        sorter: function (data) {
            return data.sort(function (a, b) {
                return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
            });
        }
    });
}

$(document).on("click", ".generate-provider-provision-report", function () {
    if (($(".only-interval").is(":checked") && ($(".provision-initial-date").val() !== "" && $(".provision-final-date").val() !== "")) || (!$(".only-interval").is(":checked") && $(".provision-final-date").val() !== "")) {
        $("#provider-provision-modal-date").find(".alert").hide();
        if ($(".only-interval").is(":checked")) {
            var initialDate = $(".provision-initial-date").val().split("/");
            $(".provision-initial-date").val(initialDate[2] + "-" + initialDate[1] + "-" + initialDate[0]);
        } else {
            $(".provision-initial-date").val("");
        }
        var finalDate = $(".provision-final-date").val().split("/");
        $(".provision-final-date").val(finalDate[2] + "-" + finalDate[1] + "-" + finalDate[0]);
        $(".provider-id").val($(".providers").val());
        $(this).attr("disabled", "disabled");
        $("#provider-provision-form").submit();
    } else {
        $("#provider-provision-modal-date").find(".alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("change", ".only-interval", function () {
    $(".provision-initial-date-container").toggle();
})