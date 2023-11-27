var startDate;
var endDate;
var incomeTable;
var expenseTable;
var taxTable;
var activityTable;

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function (a) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },

    "date-uk-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "date-uk-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
});

$(".launches-menu").addClass("active");

$(".projects").select2({
    placeholder: "Selecione...",
    language: "pt-BR",
    allowClear: true,
    sorter: function (data) {
        return data.sort(function (a, b) {
            return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
        });
    }
});

$('.income-date, .expense-date, .modal-expense-date, .tax-date, .activity-date').datepicker({
    language: "pt-BR",
    format: "dd/mm/yyyy",
    todayHighlight: true,
    autoclose: true,
});

$(document).on("change", ".projects", function () {
    if ($(this).val() !== "") {
        $.ajax({
            method: "POST",
            url: "/index.php?r=site/load-contract-info",
            data: {
                "id": $(this).val(),
            },
            beforeSend: function () {
                $(".load-project-loading-gif").show();
                $(".project-info, .project, .balance-container, .tax-container, .project-info-button, .project-bank-statements").css("opacity", "0.3");
            },
        }).success(function (data) {
            $(".load-project-loading-gif").hide();
            $(".project-info, .project, .balance-container, .tax-container, .project-info-button, .project-bank-statements").css("opacity", "1");
            loadProjectData(data);
        });
    } else {
        $(".project").hide();
        $(".project-info-button, .project-bank-statements").hide();
        $(".project-info").hide();
        $(".balance-container, .tax-container").hide();

    }
});
if (getParameterByName("id") == null) {
    $(".projects").trigger("change");
} else {
    $(".projects").val(getParameterByName("id")).change();
}


$(document).on("click", ".project-info-button", function () {
    $(".project-info").toggle();
});

$(document).on("click", ".tax-button", function () {
    $(".manage-tax-id").val("");
    $("#manage-tax-modal input[type=text], #manage-tax-modal textarea").val("");
    $(".tax-provider").val("").trigger("change");
    $(".tax-tax").removeAttr("checked");
    $(".tax-type").removeAttr("checked");
    $("#manage-tax-modal").find(".modal-title").text("Adicionar Taxa");
    $("#manage-tax-modal").find(".manage-tax").html('<i class="fa fa-plus"></i> Adicionar');
    $("#manage-tax-modal").modal("show");
});

$(document).on("click", ".edit-tax", function () {
    var icon = this;
    var id = $(this).parent().parent().children().first().text();
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/load-tax-info",
        data: {
            "id": id,
        },
        beforeSend: function () {
            $(icon).removeClass("fa-edit").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-edit");
        $(".tax-date").val(data.data);
        $(".manage-tax-id").val(id);
        $(".tax-description").val(data.descricao);
        $(".tax-provider").val(data.fornecedor).change();
        $(".tax-tax[value=" + (data.taxa == "Tarifa" ? "T" : "J")).prop("checked", true);
        $(".tax-type[value=" + data.tipo).prop("checked", true);
        $(".tax-money").val(data.valor);
        $('.tax-money').trigger('mask.maskMoney');
        $("#manage-tax-modal").find(".modal-title").text("Alterar Taxa");
        $("#manage-tax-modal").find(".manage-tax").html('<i class="fa fa-edit"></i> Alterar');
        $("#manage-tax-modal").modal("show");
    });
});

$(document).on("click", ".manage-tax", function () {
    if ($(".tax-date").val() !== "" && $(".tax-description").val() !== "" && $(".tax-provider").val() !== ""
        && $(".tax-tax:checked").val() != undefined && $(".tax-type:checked").val() != undefined && $(".tax-money").val() !== "") {
        $("#manage-tax-modal").find(".alert").hide();
        $.ajax({
            method: "POST",
            url: "/index.php?r=site/manage-tax",
            data: {
                "id": $(".manage-tax-id").val(),
                "contratoId": $(".projects").val(),
                "data": $(".tax-date").val(),
                "descricao": $(".tax-description").val(),
                "fornecedor": $(".tax-provider").val(),
                "taxa": $(".tax-tax:checked").val(),
                "tipo": $(".tax-type:checked").val(),
                "valor": $(".tax-money").unmask() / 100,
            },
            beforeSend: function () {
                $(".manage-tax-loading-gif").show();
                $("#manage-tax-modal .modal-body").css("opacity", "0.3");
                $(".manage-tax").attr("disabled", "disabled");
            },
        }).success(function (data) {
            data = JSON.parse(data);
            $(".manage-tax-loading-gif").hide();
            $("#manage-tax-modal .modal-body").css("opacity", "1");
            $(".manage-tax").removeAttr("disabled");
            taxTable.ajax.reload();
            calculateTaxValues(data.tarifa_credito_total, data.tarifa_debito_total, data.juros_credito_total, data.juros_debito_total);
            $("#manage-tax-modal").modal("hide");
        });
    } else {
        $("#manage-tax-modal").find(".alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("click", ".manage-activity-button", function () {
    $("#manage-activity-modal .activity-value, #manage-activity-modal .activity-date, #manage-activity-modal textarea").val("");
    $("#manage-activity-modal").find(".alert").hide();
    $("#manage-activity-modal").find(".cancel-edit-activity").remove();
    $(".manage-activity").html('<i class="fa fa-plus"></i> Adicionar');
    $("#manage-activity-modal").modal("show");
});

$(document).on("click", ".edit-activity", function () {
    $(".manage-activity-id").val($(this).parent().parent().children().first().text());
    $(".activity-description").val($(this).parent().parent().children().eq(2).text());
    $(".activity-value").val($(this).parent().parent().children().eq(3).unmask());
    $('.activity-value').trigger('mask.maskMoney');
    $(".activity-date").val($(this).parent().parent().children().eq(6).text());
    $(".manage-activity").html('<i class="fa fa-check-circle"></i>');
    $(".cancel-edit-activity").remove();
    $(".new-activity").append('<button type="button" class="btn btn-default cancel-edit-activity"><i class="fa fa-times-circle"></i></button>')
});

$(document).on("click", ".cancel-edit-activity", function () {
    $("#manage-activity-modal input, #manage-activity-modal textarea").val("");
    $(".manage-activity-id").val("");
    $(this).remove();
    $(".manage-activity").html('<i class="fa fa-plus"></i> Adicionar');
});

$(document).on("click", ".manage-activity", function () {
    if ($(".activity-date").val() !== "" && $(".activity-value").val() !== "") {
        $("#manage-activity-modal").find(".alert").hide();
        $.ajax({
            method: "POST",
            url: "/index.php?r=site/manage-activity",
            data: {
                "id": $(".manage-activity-id").val(),
                "descricao": $(".activity-description").val(),
                "data": $(".activity-date").val(),
                "valor": $(".activity-value").unmask() / 100,
                "rubrica": $(".items").val(),
                "contratoId": $(".projects").val()
            },
            beforeSend: function () {
                $(".manage-activity-loading-gif").show();
                $("#manage-activity-modal .modal-body").css("opacity", "0.3");
                $(".manage-activity").attr("disabled", "disabled");
            },
        }).success(function () {
            $(".manage-activity-loading-gif").hide();
            $("#manage-activity-modal .modal-body").css("opacity", "1");
            $(".manage-activity").removeAttr("disabled");
            if ($(".free-project").val() == 1) {
                $(".projects").trigger("change");
            } else {
                $(".expense-provider, .modal-expense-provider").trigger("change");
                activityTable.ajax.reload();
            }
            $("#manage-activity-modal input, #manage-activity-modal textarea").val("");
            $(".cancel-edit-activity").remove();
            $(".manage-activity").html('<i class="fa fa-plus"></i> Adicionar');
        });
    } else {
        $("#manage-activity-modal").find(".alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("click", ".add-income", function () {
    if ($(".income-date").val() !== "" && $(".income-description").val() !== "" && $(".income-paying-source").val() !== ""
        && $(".income-type").val() != "" && $(".income-title").val() != "" && $(".income-money").val() != "") {
        
        console.log($(".income-type").val())
        console.log($(".income-expense").val())
        console.log($(".income-expense-description").val())

        if($(".income-type").val() == 5 && $(".income-expense").val() == null) {
            $("#manage-income-modal").find(".alert").show().text("Campos com * são obrigatórios.");
            return false;
        }
        if($(".income-type").val() == 5 && $(".income-expense").val() == 55 && $(".income-expense-description").val() == "") {
            $("#manage-income-modal").find(".alert").show().text("Campos com * são obrigatórios.");
            return false;
        }

        $("#manage-income-modal").find(".alert").hide();
        if ($(".income-expense").val() != 55) {
            console.log($(".income-expense").val());
            $.ajax({
                method: "POST",
                url: "/index.php?r=site/add-income",
                data: {
                    "id": $(".income-id").val(),
                    "data": $(".income-date").val(),
                    "descricao": $(".income-description").val(),
                    "fontePagadora": $(".income-paying-source").val(),
                    "tipoDeReceita": $(".income-type").val(),
                    // "tipoDeDespesa": $(".income-expense").val(),
                    "tituloDaReceita": $(".income-title").val(),
                    "rubrica": $(".income-rubric").val(),
                    "parcela": $(".income-plot").val(),
                    "valor": $(".income-money").unmask() / 100,
                    "contrato": $(".projects").val()
                },
                beforeSend: function () {
                    $(".manage-income-loading-gif").show();
                    $("#manage-income-modal .modal-body").css("opacity", "0.3");
                    $(".add-income").attr("disabled", "disabled");
                },
            }).success(function (data) {
                data = JSON.parse(data);
                $(".manage-income-loading-gif").hide();
                $("#manage-income-modal .modal-body").css("opacity", "1");
                $(".add-income").removeAttr("disabled");
                incomeTable.ajax.reload();
                calculateProjectValues(data.receita_total, data.despesa_total);
                $("#manage-income-modal").modal("hide");
                $("#manage-income-modal input, #manage-income-modal textarea").val("");
                $(".income-plot").children().remove();
                $(".income-plot").append(new Option());
                if (data.contrato.parcelas_select !== undefined && Object.keys(data.contrato.parcelas_select).length) {
                    $(".income-plot").append("<optgroup class='plot-options' label='Parcela'></optgroup>");
                    $.each(data.contrato.parcelas_select, function (index, value) {
                        $(".income-plot .plot-options").append(new Option(value, value.split("|")[0]));
                    });
                }
                $(".income-type, .income-expense , .income-title").val("").trigger("change");
            });
        } else {
            $.ajax({
                method: "POST",
                url: "/index.php?r=site/load-expense",
                data: {
                    "nome": $(".income-expense-description").val(),
                },
            }).success(function (data) {
                data = JSON.parse(data);
                $.ajax({
                    method: "POST",
                    url: "/index.php?r=site/add-income",
                    data: {
                        "id": $(".income-id").val(),
                        "data": $(".income-date").val(),
                        "descricao": $(".income-description").val(),
                        "fontePagadora": $(".income-paying-source").val(),
                        "tipoDeReceita": $(".income-type").val(),
                        "tipoDeDespesa": data.id,
                        "tituloDaReceita": $(".income-title").val(),
                        "parcela": $(".income-plot").val(),
                        "valor": $(".income-money").unmask() / 100,
                        "contrato": $(".projects").val()
                    },
                    beforeSend: function () {
                        $(".manage-income-loading-gif").show();
                        $("#manage-income-modal .modal-body").css("opacity", "0.3");
                        $(".add-income").attr("disabled", "disabled");
                    },
                }).success(function (data) {
                    data = JSON.parse(data);
                    $(".manage-income-loading-gif").hide();
                    $("#manage-income-modal .modal-body").css("opacity", "1");
                    $(".add-income").removeAttr("disabled");
                    incomeTable.ajax.reload();
                    calculateProjectValues(data.receita_total, data.despesa_total);
                    $("#manage-income-modal").modal("hide");
                    $("#manage-income-modal input, #manage-income-modal textarea").val("");
                    $(".income-plot").children().remove();
                    $(".income-plot").append(new Option());
                    if (data.contrato.parcelas_select !== undefined && Object.keys(data.contrato.parcelas_select).length) {
                        $(".income-plot").append("<optgroup class='plot-options' label='Parcela'></optgroup>");
                        $.each(data.contrato.parcelas_select, function (index, value) {
                            $(".income-plot .plot-options").append(new Option(value, value.split("|")[0]));
                        });
                    }
                    $(".income-type, .income-title").val("").trigger("change");
                });
            });
        }
    } else {
        $("#manage-income-modal").find(".alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("click", ".add-income-button", function () {
    $("#manage-income-modal").find(".modal-title").text("Adicionar Receita");
    $("#manage-income-modal").find(".add-income").html('<i class="fa fa-plus"></i> Adicionar');
    $("#manage-income-modal input, #manage-income-modal textarea").val("");
    $(".income-type, .income-title, .income-expense, .income-plot, .income-rubric").val("").trigger("change");
    $(".income-expense-description").val("")
    $("#manage-income-modal").modal("show");
});

$(document).on("click", ".edit-income", function () {
    var icon = this;
    var id = $(this).parent().parent().children().first().text();
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/load-income-info",
        data: {
            "id": id,
        },
        beforeSend: function () {
            $(icon).removeClass("fa-edit").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-edit");
        $(".income-id").val(id);
        $(".income-date").val(data.data);
        $(".income-description").val(data.descricao);
        $(".income-paying-source").val(data.fonte_pagadora);
        $(".income-type").val(data.tipo_id).change();
        $('.income-expense').append($('<option>', {
            value: data.tipo_de_despesa_id,
            text: data.tipo_de_despesa_nome
        }));
        $('.income-rubric').append($('<option>',{
            value: data.rubrica_id,
            text: data.rubrica_nome
        }));
        $(".income-title").val(data.titulo_id).change();
        $(".income-plot").val(data.parcela_id).change();
        $(".income-money").val(data.valor);
        $('.income-money').trigger('mask.maskMoney');
        $("#manage-income-modal").find(".alert").hide();
        $("#manage-income-modal").find(".modal-title").text("Alterar Receita");
        $("#manage-income-modal").find(".add-income").html('<i class="fa fa-edit"></i> Alterar');
        $("#manage-income-modal").modal("show");
    });
});

$(document).on("click", ".edit-expense", function () {
    var icon = this;
    var id = $(this).parent().parent().children().first().text();
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/load-expense-info",
        data: {
            "id": id,
        },
        beforeSend: function () {
            $(icon).removeClass("fa-edit").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-edit");
        $("#manage-expense-modal").find(".alert").hide();
        $(".expense-id").val(id);
        $(".modal-expense-description").val(data.descricao);
        $(".modal-product-container").not(":first").remove();
        if ($(".free-project").val() == 1) {
            $(".modal-expense-provider").val(data.fornecedor_id).trigger("change.select2");
            $(".modal-expense-date").val(data.data);
            $.each(data.despesa_atividades, function (index, value) {
                if (index > 0) {
                    $(".modal-add-more-products i").click();
                }
                $($(".modal-product-container").find(".modal-product-id").get(index)).val(value.id);
                $($(".modal-product-container").find(".modal-expense-activity").get(index)).val(value.atividade_id).trigger("change.select2");
                $($(".modal-product-container").find(".modal-expense-money").get(index)).val(value.valor);
            });
            $(".modal-expense-money").trigger('mask.maskMoney');
        } else {
            $(".modal-expense-provider").val(data.fornecedor_id).trigger("change", [data]);
        }
        $(".modal-expense-favorite").val(data.favorecido_id).trigger("change.select2");
        $(".modal-expense-transf-check-number").val(data.numero_transferencia_cheque);
        $(".modal-expense-competence").val(data.competencia);
        $(".modal-expense-cc").val(data.centro_de_custo);
        $(".modal-expense-source").val(data.fonte_id).trigger("change.select2");
        $(".modal-expense-costing").prop("checked", data.custeio);
        $("#manage-expense-modal").find(".modal-title").text("Alterar Despesa");
        $("#manage-expense-modal").find(".modal-add-expense").html('<i class="fa fa-edit"></i> Alterar');
        $("#manage-expense-modal").modal("show");
    });
});

$(document).on("click", ".remove-income", function () {
    $(".launch-type").val("income");
    $(".launch-id").val($(this).parent().parent().children().first().text());
    $("#remove-modal").modal("show");
});

$(document).on("click", ".remove-expense", function () {
    $(".launch-type").val("expense");
    $(".launch-id").val($(this).parent().parent().children().first().text());
    $("#remove-modal").modal("show");
});

$(document).on("click", ".remove-launch", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/remove-launch",
        data: {
            "type": $(".launch-type").val(),
            "id": $(".launch-id").val(),
        },
        beforeSend: function () {
            $(".remove-launch-loading-gif").show();
            $("#remove-modal span, #remove-modal label").css("opacity", "0.3");
            $(".remove-launch").attr("disabled", "disabled");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        $(".remove-launch-loading-gif").hide();
        $("#remove-modal span, #remove-modal label").css("opacity", "1");
        $(".remove-launch").removeAttr("disabled");
        if ($(".launch-type").val() === "income") {
            incomeTable.ajax.reload();
            calculateProjectValues($(".project-income").unmask() / 100 - data.valor, $(".project-expense").unmask() / 100);
        } else {
            expenseTable.ajax.reload();
            calculateProjectValues($(".project-income").unmask() / 100, $(".project-expense").unmask() / 100 - data.valor);
            calculateItemValues(data.pago);
            $(".expense-provider").trigger("change");
        }
        $("#remove-modal").modal("hide");
    });
});

$(document).on("click", ".add-expense", function () {
    var emptyValues = $(".expense-money").filter(function () {
        return $(this).val() === "";
    });
    if ($(".expense-date").val() !== "" && $(".expense-description").val() !== "" && $(".expense-provider").val() != "" && $(".expense-source").val() != "" && $(".expense-favorite").val() != ""
        && !emptyValues.length) {
        var productsArray = new Array();
        var repeatedProduct = false;
        $(".product-container").each(function () {
            if ($(this).find(".expense-activity").val() !== "") {
                if (productsArray.includes($(this).find(".expense-activity").val())) {
                    repeatedProduct = true;
                } else {
                    productsArray.push($(this).find(".expense-activity").val());
                }
            }
        });
        if (!repeatedProduct) {
            $(".item-container").find(".alert").hide();
            var products = new Array();
            var valor = 0;
            $(".product-container").each(function () {
                valor += $(this).find(".expense-money").unmask().replace(/ /g, '') / 100;
                products.push({
                    id: "",
                    valor: $(this).find(".expense-money").unmask().replace(/ /g, '') / 100,
                    atividade: $(this).find(".expense-activity").val(),
                })
            });
            $.ajax({
                method: "POST",
                url: "/index.php?r=site/add-expense",
                data: {
                    "data": $(".expense-date").val(),
                    "descricao": $(".expense-description").val(),
                    "numero_transferencia_cheque": $(".expense-transf-check-number").val(),
                    "fornecedor": $(".expense-provider").val(),
                    "favorecido": $(".expense-favorite").val(),
                    "fonte": $('.expense-source').val(),
                    "custeio": $(".expense-costing").is(":checked") ? 1 : 0,
                    "rubrica": $(".items").val(),
                    "despesa_atividades": products,
                    "competencia": $(".expense-competence").val(),
                    "centro_de_custo": $(".expense-cc").val(),
                    "contrato_id": $(".projects").val(),
                },
                beforeSend: function () {
                    $(".add-expense-loading-gif").show();
                    $(".item-container").css("opacity", "0.3");
                    $(".add-expense").attr("disabled", "disabled");
                },
            }).success(function (data) {
                $(".add-expense-loading-gif").hide();
                $(".item-container").css("opacity", "1");
                $(".add-expense").removeAttr("disabled");
                expenseTable.ajax.reload();
                data = JSON.parse(data);
                calculateItemValues(data.pago);
                calculateProjectValues($(".project-income").unmask() / 100, $(".project-expense").unmask() / 100 + valor);
                $('.expense-costing').prop('checked', false);
                $(".item-container input, .item-container textarea").val("");
                $(".expense-provider").val("").trigger("change");
                if ($(".free-project").val() == 1) {
                    $(".expense-source").val("").trigger("change.select2");
                }
            });
        } else {
            $(".item-container").find(".alert").show().text("Os produtos selecionados não podem ser iguais.");
        }
    } else {
        $(".item-container").find(".alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("click", ".change-expense", function () {
    var emptyValues = $(".modal-expense-money").filter(function () {
        return $(this).val() === "";
    });
    if ($(".modal-expense-date").val() !== "" && $(".modal-expense-description").val() !== "" && $(".modal-expense-provider").val() != "" && $(".modal-expense-source").val() != "" && $(".modal-expense-favorite").val() != ""
        && !emptyValues.length) {
        var productsArray = new Array();
        var repeatedProduct = false;
        $(".modal-product-container").each(function () {
            if ($(this).find(".modal-expense-activity").val() !== "") {
                if (productsArray.includes($(this).find(".modal-expense-activity").val())) {
                    repeatedProduct = true;
                } else {
                    productsArray.push($(this).find(".modal-expense-activity").val());
                }
            }
        });
        if (!repeatedProduct) {
            $("#manage-expense-modal").find(".alert").hide();
            var products = new Array();
            var valor = 0;
            $(".modal-product-container").each(function () {
                valor += $(this).find(".modal-expense-money").unmask().replace(/ /g, '') / 100;
                products.push({
                    id: $(this).find(".modal-product-id").val(),
                    valor: $(this).find(".modal-expense-money").unmask().replace(/ /g, '') / 100,
                    atividade: $(this).find(".modal-expense-activity").val(),
                })
            });
            $.ajax({
                method: "POST",
                url: "/index.php?r=site/add-expense",
                data: {
                    "id": $(".expense-id").val(),
                    "data": $(".modal-expense-date").val(),
                    "descricao": $(".modal-expense-description").val(),
                    "numero_transferencia_cheque": $(".modal-expense-transf-check-number").val(),
                    "fornecedor": $(".modal-expense-provider").val(),
                    "favorecido": $(".modal-expense-favorite").val(),
                    "fonte": $('.modal-expense-source').val(),
                    "custeio": $(".modal-expense-costing").is(":checked") ? 1 : 0,
                    "rubrica": $(".items").val(),
                    "despesa_atividades": products,
                    "competencia": $(".modal-expense-competence").val(),
                    "centro_de_custo": $(".modal-expense-cc").val(),
                    "contrato_id": $(".projects").val(),
                },
                beforeSend: function () {
                    $(".manage-expense-loading-gif").show();
                    $("#manage-expense-modal .modal-body").css("opacity", "0.3");
                    $(".change-expense").attr("disabled", "disabled");
                },
            }).success(function (data) {
                $(".manage-expense-loading-gif").hide();
                $("#manage-expense-modal .modal-body").css("opacity", "1");
                $(".change-expense").removeAttr("disabled");
                $(".item-container").find(".alert").hide();
                if ($(".free-project").val() == 1) {
                    $(".projects").trigger("change");
                } else {
                    expenseTable.ajax.reload();
                    data = JSON.parse(data);
                    calculateItemValues(data.pago);
                    calculateProjectValues(data.receita_total, data.despesa_total);
                    $('.modal-expense-costing').prop('checked', false);
                    $("#manage-expense-modal input, #manage-expense-modal textarea").val("");
                    $(".modal-expense-provider, .modal-expense-source").val("").trigger("change");
                    $(".expense-provider").trigger("change");
                }
                $("#manage-expense-modal").modal("hide");
            });
        } else {
            $("#manage-expense-modal").find(".alert").show().text("Os produtos selecionados não podem ser iguais.");
        }
    } else {
        $("#manage-expense-modal").find(".alert").show().text("Campos com * são obrigatórios.");
    }
});

$(document).on("change", ".items", function () {
    if ($(this).val() !== "") {
        $.ajax({
            method: "POST",
            url: "/index.php?r=site/load-item-info",
            data: {
                "id": $(this).val(),
            },
            beforeSend: function () {
                $(".load-item-loading-gif").css("display", "inline-block");
                $(".item-container, .manage-activity-button").css("opacity", "0.3");
            },
        }).success(function (data) {
            $(".load-item-loading-gif").hide();
            $(".item-container, .manage-activity-button").css("opacity", "1");
            loadItemData(data);
            $(".item-container").find(".alert").hide();
            $('.expense-costing').prop('checked', false);
            $(".item-container input, .item-container textarea").val("");
            $(".expense-provider").trigger("change");
            $(".product-container").not(":first").remove();
            $('.load-item-info').css("display", "inline-block");
            $(".manage-activity-button").show();
            $('.item-container').show();
        });
    } else {
        $('.item-container').hide();
        $('.load-item-info').hide();
        $(".manage-activity-button").hide();
    }
});

$(document).on("keypress", ".item-container input, .item-container textarea", function (evt) {
    if (event.which == 13) {
        evt.preventDefault();
        $(".add-expense").click();
    }
});

$(document).on("click", ".load-income-info", function () {
    var icon = this;
    var id = $(this).parent().parent().children().first().text();
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/load-income-info",
        data: {
            "id": id,
        },
        beforeSend: function () {
            $(icon).removeClass("fa-question-circle-o").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-question-circle-o");
        data = JSON.parse(data);
        $(".income-date-info").text(data.data);
        $(".income-description-info").text(data.descricao);
        $(".income-paying-source-info").text(data.fonte_pagadora);
        $(".income-type-info").text(data.tipo);
        $(".income-type-expense").text(data.tipo_de_despesa_nome);
        $(".income-title-info").text(data.titulo);
        $(".income-plot-info").text(data.parcela);
        $(".income-value-info").text(data.valor);
        $(".income-value-info").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            allowNegative: true
        });
        $(".income-value-info").text(data.rubrica_nome);
        $("#income-info-modal").modal("show");
    });
});

$(document).on("click", ".load-expense-info", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/load-expense-info",
        data: {
            "id": $(this).parent().parent().children().first().text(),
        },
        beforeSend: function () {
            $(icon).removeClass("fa-question-circle-o").addClass("fa-spin").addClass("fa-spinner");
        },
    }).success(function (data) {
        $(icon).removeClass("fa-spin").removeClass("fa-spinner").addClass("fa-question-circle-o");
        data = JSON.parse(data);
        $(".expense-item-info").text(data.rubrica);
        $(".expense-cc-info").text(data.centro_de_custo == null ? "-" : data.centro_de_custo);
        $(".expense-date-info").text(data.data);
        $(".expense-description-info").text(data.descricao);
        $(".expense-provider-info").text(data.fornecedor);
        $(".expense-favorite-info").text(data.favorecido);
        $(".expense-competence-info").text(data.competencia == null ? "-" : data.competencia);
        $(".expense-transf-check-number-info").text(data.numero_transferencia_cheque == null ? "-" : data.numero_transferencia_cheque);
        $(".expense-source-info").text(data.fonte);
        $(".expense-costing-info").text(data.custeio ? "Sim" : "Não");
        var html = "";
        $.each(data.despesa_atividades, function () {
            html += '<div class="expense-activity-container-info">' +
                '<div class="expense-activity-info-container">' +
                '<label>Produto:</label>' +
                '<span class="expense-activity-info"> ' + this.atividade + '</span>' +
                '</div>' +
                '<div>' +
                '<label>Valor:</label>' +
                '<span class="expense-value-info">' + this.valor + '</span>' +
                '</div>' +
                '</div>';
        });
        $(".expense-activities-container").html(html);

        $(".expense-value-info").priceFormat({
            prefix: ' R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            allowNegative: true
        });
        $("#expense-info-modal").modal("show");
    });
});

$(document).on("change", ".expense-provider", function () {
    if ($(".free-project").val() == 0) {
        if ($(this).val() !== "") {
            $.ajax({
                method: "POST",
                url: "/index.php?r=site/load-provider-info",
                data: {
                    "id": $(this).val(),
                    "rubrica": $(".items").val()
                },
                beforeSend: function () {
                    $(".provider-container").css("opacity", "0.3");
                    $(".load-provider-loading-gif").show();
                },
            }).success(function (data) {
                data = JSON.parse(data);
                $(".provider-container").css("opacity", "1");
                $(".load-provider-loading-gif").hide();
                $('.expense-date, .expense-money').val("");
                $(".product-container").not(":first").remove();

                var initialDate = data.data_inicial.split("-");
                $('.expense-date').data('datepicker').setStartDate(new Date(initialDate[0], initialDate[1] - 1, initialDate[2], 0, 0, 0));

                if (data.fornecedor != undefined) {
                    $(".provider-name-info").text(data.fornecedor.nome);
                    $(".provider-tipo-contrato-info").text(data.tipo_de_contrato);
                    data.fornecedor.cnpj != null ? $(".provider-cnpj-info").text(data.fornecedor.cnpj).parent().show() : $(".provider-cnpj-info").parent().hide();
                    data.fornecedor.profissao != null ? $(".provider-profissao-info").text(data.fornecedor.profissao).parent().show() : $(".provider-profissao-info").parent().hide();
                    data.fornecedor.respresentante_legal != null ? $(".provider-representante-info").text(data.fornecedor.respresentante_legal).parent().show() : $(".provider-representante-info").parent().hide();
                    data.fornecedor.rg != null ? $(".provider-rg-info").text(data.fornecedor.rg).parent().show() : $(".provider-rg-info").parent().hide();
                    data.fornecedor.pis != null ? $(".provider-pis-info").text(data.fornecedor.pis).parent().show() : $(".provider-pis-info").parent().hide();
                    data.fornecedor.cpf != null ? $(".provider-cpf-info").text(data.fornecedor.cpf).parent().show() : $(".provider-cpf-info").parent().hide();
                    data.fornecedor.endereco != null ? $(".provider-endereco-info").text(data.fornecedor.endereco).parent().show() : $(".provider-endereco-info").parent().hide();
                    data.fornecedor.email != null ? $(".provider-email-info").text(data.fornecedor.email).parent().show() : $(".provider-email-info").parent().hide();
                    data.fornecedor.telefone != null ? $(".provider-telefone-info").text(data.fornecedor.telefone).parent().show() : $(".provider-telefone-info").parent().hide();
                    data.contaBancaria.banco != null ? $(".provider-conta-banco-info").text(data.contaBancaria.banco).parent().show() : $(".provider-conta-banco-info").parent().hide();
                    data.contaBancaria.tipo_de_conta != null ? $(".provider-conta-tipo-info").text(translateTipoDeConta(data.contaBancaria.tipo_de_conta)).parent().show() : $(".provider-conta-tipo-info").parent().hide();
                    data.contaBancaria.agencia != null ? $(".provider-conta-agencia-info").text(data.contaBancaria.agencia).parent().show() : $(".provider-conta-agencia-info").parent().hide();
                    data.contaBancaria.conta != null ? $(".provider-conta-conta-info").text(data.contaBancaria.conta).parent().show() : $(".provider-conta-conta-info").parent().hide();
                    data.contaBancaria.proprietario != null ? $(".provider-conta-proprietario-info").text(data.contaBancaria.proprietario).parent().show() : $(".provider-conta-proprietario-info").parent().hide();
                    data.contaBancaria.pix != null ? $(".provider-conta-pix-info").text(data.contaBancaria.pix).parent().show() : $(".provider-conta-pix-info").parent().hide();
                    $(".load-provider-info").show();
                }

                $(".expense-activity").children().remove();
                $(".expense-activity").append(new Option());
                if (data.atividades_rubrica !== undefined) {
                    $(".expense-activity").append("<optgroup class='provider-item-options' label='Rubrica'></optgroup>");
                    $.each(data.atividades_rubrica, function (index, value) {
                        $(".expense-activity .provider-item-options").append(new Option(value, value.split("|")[0]));
                    });
                }
                if (data.atividades_fornecedor !== undefined) {
                    $(".expense-activity").prepend("<optgroup class='provider-contract-options' label='Contrato do Fornecedor'></optgroup>");
                    $.each(data.atividades_fornecedor, function (index, value) {
                        $(".expense-activity .provider-contract-options").append(new Option(value, value.split("|")[0]));
                    });
                }

                var html = buildProviderContractHtml(data);
                $(".provider-item-container").html(html);
                html = buildProviderBalanceHtml(data);
                $(".contract-balance-container").html(html).show();
                $(".provider-value-info, .activity-value-info, .contract-balance-value, .contract-balance-paid, .contract-balance-remaining").priceFormat({
                    prefix: 'R$ ',
                    centsSeparator: ',',
                    thousandsSeparator: '.',
                    allowNegative: true
                });
                if (data.admin) {
                    $(".expense-activity-container, .expense-value-container, .expense-date-container").show();
                    $(".add-more-products").show();
                } else {
                    $(".expense-activity-container, .expense-value-container, .expense-date-container").hide();
                    $(".add-more-products").hide();
                }
            });
        } else {
            $(".contract-balance-container").children().remove();
            $(".load-provider-info").hide();
            $(".expense-date-container, .expense-value-container, .expense-activity-container").hide();
            $(".add-more-products").hide();
        }
    }
});

function buildProviderContractHtml(data) {
    var html = "";
    $.each(data.rubrica_fornecedores, function (index, contract) {
        var activitiesHtml = "";
        $.each(contract.atividades, function (index, activity) {
            var activityDate = activity.data.split("-");
            activitiesHtml += '<div>'
                + '<span class="activity-number-info">' + activity.ordem + 'º</span>'
                + '<span class="activity-description-info">' + (activity.descricao == "" ? "-" : activity.descricao) + '</span>'
                + '<span class="activity-value-info">' + Number(activity.valor).toFixed(2) + '</span>'
                + '<span class="activity-date-info">' + activityDate[2] + '/' + activityDate[1] + '/' + activityDate[0] + '</span>'
                + '</div><hr class="plot-divisor"/>';
        });
        var initialDate = contract.data_inicial.split("-");
        var finalDate = contract.data_final.split("-");
        html += '<div class="provider-contract">'
            + '<div>'
            + '<label>Rubrica:</label>'
            + '<span class="provider-item-info"> ' + (contract.ordem > 1 ? data.rubrica + " (" + (contract.ordem - 1) + "º Termo Aditivo)" : data.rubrica) + '</span>'
            + '</div>'
            + '<div class="contract-date-container">'
            + '<div>'
            + '<label>Data de início:</label>'
            + '<span class="provider-initial-date-info"> ' + initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0] + '</span>'
            + '</div>'
            + '<div>'
            + '<label>Data de término:</label>'
            + '<span class="provider-final-date-info"> ' + finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0] + '</span>'
            + '</div>'
            + '</div>'
            + '<div class="contract-value-plots-container">'
            + '<div>'
            + '<label>Valor:</label>'
            + '<span class="provider-value-info"> ' + Number(contract.valor_total).toFixed(2) + '</span>'
            + '</div>'
            + '<div>'
            + '<label>Parcelas:</label>'
            + '<span class="provider-plots-info"> ' + contract.parcelas + 'x</span>'
            + '</div>'
            + '</div>'
            + '<div class="contract-activities-container">'
            + activitiesHtml
            + '</div>'
            + '<div class="clear"></div>'
            + '</div>';
    });
    return html;
}

function buildProviderBalanceHtml(data) {
    var html = "";
    $.each(data.rubrica_fornecedores, function (index, contract) {
        var initialDate = contract.data_inicial.split("-");
        var finalDate = contract.data_final.split("-");
        html += '<div class="contract-balance">'
            + '<div class="contract-balance-title">' + (contract.ordem > 1 ? "Contrato do Fornecedor (" + (contract.ordem - 1) + "º Termo Aditivo)" : "Contrato do Fornecedor") + '</div>'
            + '<div class="contract-balance-body">'
            + '<div>'
            + '<label>Início:</label>'
            + '<span class="contract-balance-initial-date">' + initialDate[2] + "/" + initialDate[1] + "/" + initialDate[0] + '</span>'
            + '</div>'
            + '<div>'
            + '<label>Término:</label>'
            + '<span class="contract-balance-final-date">' + finalDate[2] + "/" + finalDate[1] + "/" + finalDate[0] + '</span>'
            + '</div>'
            + '<div>'
            + '<label>Valor:</label>'
            + '<span class="contract-balance-value">' + Number(contract.valor_total).toFixed(2) + '</span>'
            + '</div>'
            + '<div>'
            + '<label>Pago:</label>'
            + '<span class="contract-balance-paid">' + Number(contract.valor_pago).toFixed(2) + '</span>'
            + '</div>'
            + '<div>'
            + '<label>Restante:</label>'
            + '<span class="contract-balance-remaining" style="color: ' + (contract.valor_restante >= 0 ? "" : "red") + '">' + Number(contract.valor_restante).toFixed(2) + '</span>'
            + '</div>'
            + '</div>'
            + '</div>';
    });
    return html;
}

$(document).on("change", ".modal-expense-provider", function (evt, expenseInfo) {
    if ($(".free-project").val() == 0) {
        if ($(this).val() !== "") {
            $.ajax({
                method: "POST",
                url: "/index.php?r=site/load-provider-info",
                data: {
                    "id": $(this).val(),
                    "rubrica": $(".items").val()
                },
                beforeSend: function () {
                    $(".modal-provider-container").css("opacity", "0.3");
                    $(".load-modal-provider-loading-gif").show();
                },
            }).success(function (data) {
                data = JSON.parse(data);
                $(".modal-provider-container").css("opacity", "1");
                $(".load-modal-provider-loading-gif").hide();

                $(".modal-expense-activity").children().remove();
                $(".modal-expense-activity").append(new Option());
                if (data.atividades_rubrica !== undefined) {
                    $(".modal-expense-activity").append("<optgroup class='provider-item-options' label='Rubrica'></optgroup>");
                    $.each(data.atividades_rubrica, function (index, value) {
                        $(".modal-expense-activity .provider-item-options").append(new Option(value, value.split("|")[0]));
                    });
                }
                if (data.atividades_fornecedor !== undefined) {
                    $(".modal-expense-activity").prepend("<optgroup class='provider-contract-options' label='Contrato do Fornecedor'></optgroup>");
                    $.each(data.atividades_fornecedor, function (index, value) {
                        $(".modal-expense-activity .provider-contract-options").append(new Option(value, value.split("|")[0]));
                    });
                }

                var initialDate = data.data_inicial.split("-");
                $('.modal-expense-date').data('datepicker').setStartDate(new Date(initialDate[0], initialDate[1] - 1, initialDate[2], 0, 0, 0));
                $(".modal-expense-date").val(expenseInfo.data);
                $.each(expenseInfo.despesa_atividades, function (index, value) {
                    if (index > 0) {
                        $(".modal-add-more-products i").click();
                    }
                    $($("#manage-expense-modal .modal-body").find(".modal-product-container").get(index)).find(".modal-product-id").val(value.id);
                    $($("#manage-expense-modal .modal-body").find(".modal-product-container").get(index)).find(".modal-expense-activity").val(value.atividade_id).trigger("change.select2");
                    $($("#manage-expense-modal .modal-body").find(".modal-product-container").get(index)).find(".modal-expense-money").val(value.valor);
                });
                $(".modal-expense-money").trigger('mask.maskMoney');
                $(".modal-expense-date-container").show();
                $(".modal-expense-activity-container").show();
            });
        } else {
            $(".modal-expense-date-container").hide();
            $(".modal-expense-activity-container").hide();
        }
    }
});

function calculateProjectValues(receita, despesa) {
    $(".project-income").text(Number(receita).toFixed(2));
    $(".project-expense").text(Number(despesa).toFixed(2));
    var balance = receita - despesa;
    $(".project-balance").css("color", balance < 0 ? "red" : "green").text(Number(balance).toFixed(2));
    $(".project-expense, .project-income, .project-balance").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
}

function calculateTaxValues(tarifaCredito, tarifaDebito, jurosCredito, jurosDebito) {
    $(".fare-income").text(Number(tarifaCredito).toFixed(2));
    $(".fare-expense").text(Number(tarifaDebito).toFixed(2));
    $(".interest-income").text(Number(jurosCredito).toFixed(2));
    $(".interest-expense").text(Number(jurosDebito).toFixed(2));
    var fareBalance = tarifaCredito - tarifaDebito;
    var interestBalance = jurosCredito - jurosDebito;
    $(".fare-balance").css("color", fareBalance < 0 ? "red" : "green").text(Number(fareBalance).toFixed(2));
    $(".interest-balance").css("color", interestBalance < 0 ? "red" : "green").text(Number(interestBalance).toFixed(2));
    $(".fare-expense, .fare-income, .fare-balance, .interest-expense, .interest-income, .interest-balance").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
}

function calculateItemValues(expense) {
    var value = $(".item-balance-value").unmask() / 100;
    $(".item-balance-paid").text(Number(expense).toFixed(2));
    $(".item-balance-remaining").css("color", (Number(value) - Number(expense)) < 0 ? "red" : "#333").text(Number(Number(value) - Number(expense)).toFixed(2));
    $(".item-balance-paid, .item-balance-remaining").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
}

function loadProjectData(data) {
    data = JSON.parse(data);
    $(".free-project").val(data.contrato.livre);
    $(".contract-name, .project .panel-heading").text(data.contrato.nome);
    $(".project-public-origin").text(data.contrato.origem_publica);
    if (!data.contrato.livre) {
        $(".project-free-initial-date").text("").parent().hide();
        $(".project-free-final-date").text("").parent().hide();
        $(".project-plots").text(Object.keys(data.contrato.parcelas).length).parent().show();
        var plotsHtml = "";
        $.each(data.contrato.parcelas, function (index, value) {
            var plotDate = value.data.split("-");
            plotsHtml += '<div>'
                + '<span class="plot-number-info">' + value.ordem + 'º</span>'
                + '<span class="plot-description-info">' + value.descricao + '</span>'
                + '<span class="plot-paying-source-info">' + value.fonte_pagadora + '</span>'
                + '<span class="plot-value-info">' + Number(value.valor).toFixed(2) + '</span>'
                + '<span class="plot-date-info">' + plotDate[2] + '/' + plotDate[1] + '/' + plotDate[0] + '</span>'
                + '</div><hr class="plot-divisor"/>';
        });
        $(".project-plots-container").html(plotsHtml);
        $(".project-supporter").text(data.contrato.apoiadora).parent().show();
        $(".project-dates").show();
        $(".initial-date").text(data.contrato.data_inicial);
        $(".final-date").text(data.contrato.data_final);
        $(".project-value").text(Number(data.contrato.valor_total).toFixed(2)).parent().show();

        if (data.contrato.conta_bancaria === undefined) {
            $(".project-bank-accounts").hide();
        } else {
            $(".banco-info").text(data.contrato.conta_bancaria.banco);
            $(".agencia-info").text(data.contrato.conta_bancaria.agencia);
            $(".tipo-info").text(translateTipoDeConta(data.contrato.conta_bancaria.tipo_de_conta));
            $(".conta-info").text(data.contrato.conta_bancaria.conta);
            $(".proprietario-info").text(data.contrato.conta_bancaria.proprietario);
            data.contrato.conta_bancaria.pix == null ? $(".pix-info").text("").parent().hide() : $(".pix-info").text(data.contrato.conta_bancaria.pix).parent().show();
            $(".project-bank-accounts").show();
        }

        var coordenadoresLabel = "";
        $.each(data.contrato.coordenadores, function () {
            coordenadoresLabel += '<span class="coordenador-nome">' + this.nome + '</span>';
        });
        coordenadoresLabel != "" ? $(".project-coordinators").html(coordenadoresLabel).parent().show() : $(".project-coordinators").parent().hide();

        startDate = $(".project-info").find(".initial-date").text().split("/");
        endDate = $(".project-info").find(".final-date").text().split("/");
        $('.income-date').data('datepicker').setStartDate(new Date(startDate[2], startDate[1] - 1, startDate[0], 0, 0, 0));
        $('.income-date').data('datepicker').setEndDate(new Date(endDate[2], endDate[1] - 1, endDate[0], 0, 0, 0));
        $('.activity-date').data('datepicker').setStartDate(new Date(startDate[2], startDate[1] - 1, startDate[0], 0, 0, 0));
        $('.activity-date').data('datepicker').setEndDate(new Date(endDate[2], endDate[1] - 1, endDate[0], 0, 0, 0));

        $(".items").children().remove();
        $(".items").append(new Option());
        $.each(data.contrato.rubricas, function (index, value) {
            $(".items").append(new Option(value, index));
        });

        $(".income-plot").children().remove();
        $(".income-plot").append(new Option());
        if (data.contrato.parcelas_select !== undefined && Object.keys(data.contrato.parcelas_select).length) {
            $(".income-plot").append("<optgroup class='plot-options' label='Parcela'></optgroup>");
            $.each(data.contrato.parcelas_select, function (index, value) {
                $(".income-plot .plot-options").append(new Option(value, value.split("|")[0]));
            });
        }

        $(".project-value, .plot-value-info").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            allowNegative: true
        });

        $(".select-item, .plot-container, .income-plot-info-container, " +
            ".item-balance-container, .expense-item-info-container").show();
        $('.item-container, .load-item-info, .expense-cc-container, .modal-expense-cc-container, .expense-cc-info-container, .manage-activity-button').hide();
        $(".add-expense").css("margin-bottom", "0");
        $(".manage-activity-button").html('<i class="fa fa-search"></i> Produtos da Rubrica');
        $("#manage-activity-modal .modal-title").text("Produtos da Rubrica");
    } else {
        $(".project-free-initial-date").text(data.contrato.data_inicial).parent().show();
        $(".project-free-final-date").text(data.contrato.data_final).parent().show();
        $(".project-supporter").text("").parent().hide();
        $(".project-plots").text("").parent().hide();
        $(".project-plots-container").html("");
        $(".project-dates").hide();
        $(".project-value").text("").parent().hide();
        $(".project-bank-accounts").hide();
        $(".project-coordinators").parent().hide();
        $('.income-date').data('datepicker').setStartDate(new Date(1900, 0, 1, 0, 0, 0));
        $('.income-date').data('datepicker').setEndDate(new Date(9999, 11, 31, 0, 0, 0));
        $('.expense-date').data('datepicker').setStartDate(new Date(1900, 0, 1, 0, 0, 0));
        $('.modal-expense-date').data('datepicker').setStartDate(new Date(1900, 0, 1, 0, 0, 0));

        $(".expense-provider, .modal-expense-provider").children().remove();
        $(".expense-provider, .modal-expense-provider").append(new Option());
        $.each(data.fornecedores, function (index, value) {
            $(".expense-provider, .modal-expense-provider").append(new Option(value, index));
        });

        $(".expense-activity, .modal-expense-activity").children().remove();
        $(".expense-activity, .modal-expense-activity").append(new Option());
        if (data.atividades_rubrica !== undefined) {
            $(".expense-activity, .modal-expense-activity").append("<optgroup class='provider-project-options' label='Projeto'></optgroup>");
            $.each(data.atividades_rubrica, function (index, value) {
                $(".expense-activity .provider-project-options, .modal-expense-activity .provider-project-options").append(new Option(value, value.split("|")[0]));
            });
        }

        $(".product-container").not(":first").remove();
        $('.expense-costing').prop('checked', false);
        $(".item-container input, .item-container textarea, .expense-date, .expense-money").val("");
        $(".items, .expense-source").val("").trigger("change.select2");

        $(".item-container").find(".alert").hide();
        $(".select-item, " +
            ".plot-container, .income-plot-info-container, .item-balance-container, .contract-balance-container, " +
            ".load-provider-info, .expense-item-info-container").hide();
        $('.item-container, .expense-value-container, .expense-date-container, .expense-activity-container, .modal-expense-activity-container, .expense-cc-container, .modal-expense-cc-container,' +
            ' .expense-cc-info-container, .manage-activity-button, .add-more-products, .modal-add-more-products').show();
        $(".add-expense").css("margin-bottom", "30px");
        $(".manage-activity-button").html('<i class="fa fa-search"></i> Produtos do Projeto');
        $("#manage-activity-modal .modal-title").text("Produtos do Projeto");
    }

    var midias = "";
    $.each(data.contrato.midias, function () {
        midias += '<div class="file-container"><input type="hidden" class="file-id" value="' + this.id + '"><i class="file-icon fa fa-file-pdf-o"></i><a target="_blank" href="http://' + this.link + '">' + this.nome_falso + '</a>' + (data.admin ? '<i class="remove-file fa fa-times darkred"></i>' : '') + '</div>';
    });
    if (midias == "") {
        $(".uploaded-files-container").html('<div class="alert no-files">Nenhum extrato bancário.</div>');
        $(".no-files").show();
    } else {
        $(".uploaded-files-container").html(midias);
    }

    calculateProjectValues(data.contrato.receita_total, data.contrato.despesa_total);
    calculateTaxValues(data.contrato.tarifa_credito_total, data.contrato.tarifa_debito_total, data.contrato.juros_credito_total, data.contrato.juros_debito_total);

    $(".income-type").children().remove();
    $(".income-type").append(new Option());
    $.each(data.tipos_de_receita, function (index, value) {
        $(".income-type").append(new Option(value, index));
    });
    $(".income-title").children().remove();
    $(".income-title").append(new Option());
    $.each(data.titulos_da_receita, function (index, value) {
        $(".income-title").append(new Option(value, index));
    });

    $(".income-rubric").children().remove();
    $(".income-rubric").append(new Option());
    $.each(data.rubricas, function (index, value) {
        value = index + " - " + value;
        console.log(value);
        $(".income-rubric").append(new Option(value, index));
    })

    $(".tax-provider").children().remove();
    $(".tax-provider").append(new Option());
    $.each(data.fornecedores, function (index, value) {
        $(".tax-provider").append(new Option(value, index));
    });

    incomeTable = $('#income-table').DataTable({
        "bDestroy": true,
        "bAutoWidth": false,
        "ajax": {
            method: "POST",
            url: "/index.php?r=site/load-income-datatable",
            data: {
                "id": $(".projects").val(),
            }
        },
        fnDrawCallback: function () {
            $("td.income-value").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
            if ($(".free-project").val() == 1) {
                $('#income-table').DataTable().column(6).visible(false);
            } else {
                $('#income-table').DataTable().column(6).visible(true);
            }
        },
        "ordering": false,
        "columns": [
            { data: "id", class: "display-hide" },
            { data: "data", width: "5%" },
            { data: "descricao" },
            { data: "tipo" },
            { data: "titulo" },
            { data: "parcela" },
            { data: "fonte_pagadora" },
            { data: "valor", class: "income-value" },
            { data: "rubrica_fk"},
            { data: "icon", width: "70px", class: "table-icons" },
        ],
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        }
    });

    expenseTable = $('#expense-table').DataTable({
        "bDestroy": true,
        "bAutoWidth": false,
        "ordering": false,
        "ajax": {
            method: "POST",
            url: "/index.php?r=site/load-expense-datatable",
            data: {
                "contratoId": $(".projects").val(),
            }
        },
        fnDrawCallback: function () {
            $("td.expense-value span").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        "columns": [
            { data: "id", class: "display-hide" },
            { data: "data", width: "5%" },
            { data: "descricao" },
            { data: "fornecedor_id", class: "fornecedor-id display-hide" },
            { data: "fornecedor" },
            { data: "favorecido" },
            { data: "competencia" },
            { data: "centro_de_custo", class: "expense-cc-column" },
            { data: "atividade", class: "expense-activity-column" },
            { data: "valor", class: "expense-value" },
            { data: "fonte" },
            { data: "icon", width: "70px", class: "table-icons" },
        ],
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        }
    });

    activityTable = $('#activity-table').DataTable({
        "bDestroy": true,
        "bAutoWidth": false,
        "ajax": {
            method: "POST",
            url: "/index.php?r=site/load-activity-datatable",
            data: {
                "contratoId": $(".projects").val(),
            }
        },
        fnDrawCallback: function () {
            $("td.info-activity-value").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        sorting: [[6, "desc"]],
        "columns": [
            { data: "id", "bSortable": false, class: "display-hide" },
            { data: "ordem" },
            { data: "descricao" },
            { data: "valor", "bSortable": false, class: "info-activity-value" },
            { data: "pago", "bSortable": false, class: "info-activity-value" },
            { data: "status", class: "info-activity-status" },
            { data: "data", type: 'date-uk' },
            { data: "icon", "bSortable": false, width: "44px", class: "table-icons" },
        ],
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        }
    });

    taxTable = $('#tax-table').DataTable({
        "bDestroy": true,
        "ajax": {
            method: "POST",
            url: "/index.php?r=site/load-tax-datatable",
            data: {
                "id": $(".projects").val(),
            }
        },
        fnDrawCallback: function () {
            $("td.info-tax-type").each(function () {
                if ($(this).text() == "Crédito") {
                    $(this).parent().children(".info-tax-value").css("color", "green");
                } else {
                    $(this).parent().children(".info-tax-value").css("color", "red");
                }
            });
            $("td.info-tax-value").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        "ordering": false,
        "fixedColumns": true,
        "columns": [
            { data: "id", class: "display-hide" },
            { data: "data", width: "5%" },
            { data: "descricao" },
            { data: "fornecedor" },
            { data: "taxa" },
            { data: "tipo", class: "info-tax-type" },
            { data: "valor", class: "info-tax-value" },
            { data: "icon", width: "44px", class: "table-icons" },
        ],
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        }
    });

    if (!data.admin) {
        $(".table-icons").css("width", "28px");
        $(".admin-privilege").hide();
        if ($(".free-project").val() == "1") {
            $(".input-column").hide();
        } else {
            $(".input-column").show();
        }

    }

    $(".income-rubric").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
    });

    $(".items, .income-type, .income-expense, .income-title").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
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

    $(".expense-activity, .modal-expense-activity, .income-plot").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
        templateResult: activityResult,
        templateSelection: activitySelection,
        escapeMarkup: function (m) {
            return m;
        },
    });

    $(".expense-provider, .modal-expense-provider, .tax-provider, .expense-favorite, .modal-expense-favorite").select2({
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

    $(".expense-source, .modal-expense-source").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
    });

    $(".expense-money, .modal-expense-money").maskMoney({
        allowNegative: true,
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });

    $('.income-money, .tax-money, .activity-value').maskMoney({
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });

    $(".project").show();
    $(".project-bank-statements, .project-info-button").show();
    $(".balance-container, .tax-container").css("display", "block");
}

function loadItemData(data) {
    data = JSON.parse(data);
    $(".item-description-info").text(data.rubrica.descricao);
    $(".item-category-info").text(data.rubrica.categoria);
    $(".item-vinculation-info").text(data.rubrica.vinculante == 1 ? "Sim" : "Não");
    $(".item-source-info").text(data.rubrica.fonte);
    data.rubrica.tipo_de_contrato === null ? $(".item-contract-type-info").parent().hide() : $(".item-contract-type-info").text(data.rubrica.tipo_de_contrato).parent().show();
    $(".item-total-value-info").text(Number(data.rubrica.valor_total).toFixed(2));
    $(".item-balance-value").text(Number(data.rubrica.valor_total).toFixed(2));
    $(".item-balance-paid").text(Number(data.rubrica.pago).toFixed(2));
    $(".item-balance-remaining").css("color", Number(Number(data.rubrica.valor_total) - Number(data.rubrica.pago)) < 0 ? "red" : "#333").text(Number(Number(data.rubrica.valor_total) - Number(data.rubrica.pago)).toFixed(2));

    $(".item-unity-value-info, .item-total-value-info, .item-balance-value, .item-balance-paid, .item-balance-remaining").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });

    $(".expense-provider, .modal-expense-provider").children().remove();
    $(".expense-provider, .modal-expense-provider").append(new Option());
    $.each(data.fornecedores, function (index, value) {
        $(".expense-provider, .modal-expense-provider").append(new Option(value, index));
    });

    $(".expense-source").val(data.rubrica.fonte_fk).trigger("change.select2");

    expenseTable = $('#expense-table').DataTable({
        "bDestroy": true,
        "bAutoWidth": false,
        "ordering": false,
        "ajax": {
            method: "POST",
            url: "/index.php?r=site/load-expense-datatable",
            data: {
                "id": $(".items").val(),
            }
        },
        fnDrawCallback: function () {
            $("td.expense-value span").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
            if ($(".free-project").val() == 1) {
                $('#expense-table').DataTable().column(7).visible(true);
            } else {
                $('#expense-table').DataTable().column(7).visible(false);
            }
        },
        "columns": [
            { data: "id", class: "display-hide" },
            { data: "data", width: "5%" },
            { data: "descricao" },
            { data: "fornecedor_id", class: "fornecedor-id display-hide" },
            { data: "fornecedor" },
            { data: "favorecido" },
            { data: "competencia" },
            { data: "centro_de_custo", class: "expense-cc-column" },
            { data: "atividade", class: "expense-activity-column" },
            { data: "valor", class: "expense-value" },
            { data: "fonte" },
            { data: "icon", width: "70px", class: "table-icons" },
        ],
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        }
    });

    activityTable = $('#activity-table').DataTable({
        "bDestroy": true,
        "bAutoWidth": false,
        "ajax": {
            method: "POST",
            url: "/index.php?r=site/load-activity-datatable",
            data: {
                "id": $(".items").val(),
            }
        },
        fnDrawCallback: function () {
            $("td.info-activity-value").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        sorting: [[6, "desc"]],
        "columns": [
            { data: "id", "bSortable": false, class: "display-hide" },
            { data: "ordem" },
            { data: "descricao" },
            { data: "valor", "bSortable": false, class: "info-activity-value" },
            { data: "pago", "bSortable": false, class: "info-activity-value" },
            { data: "status", class: "info-activity-status" },
            { data: "data", type: 'date-uk' },
            { data: "icon", "bSortable": false, width: "44px", class: "table-icons" },
        ],
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            }
        }
    });

    if (!data.admin) {
        data.rubrica.vinculante ? $(".provider-container").show() : $(".provider-container").hide();
        $(".table-icons").css("width", "28px");
        $(".admin-privilege").hide();
    }
}

$(document).on("click", ".remove-tax", function () {
    $(".remove-tax-id").val($(this).parent().parent().children().first().text());
    $("#remove-tax-modal").modal("show");
});

$(document).on("click", ".remove-activity", function () {
    $(".remove-activity-id").val($(this).parent().parent().children().first().text());
    $("#remove-activity-modal").modal("show");
});

$(document).on("click", ".remove-tax-button", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/remove-tax",
        data: {
            "id": $(".remove-tax-id").val(),
            "contratoId": $(".projects").val()
        },
        beforeSend: function () {
            $(".remove-tax-loading-gif").show();
            $("#remove-tax-modal span").css("opacity", "0.3");
            $(".remove-tax-button").attr("disabled", "disabled");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        $(".remove-tax-loading-gif").hide();
        $("#remove-tax-modal span").css("opacity", "1");
        $(".remove-tax-button").removeAttr("disabled");
        taxTable.ajax.reload();
        calculateTaxValues(data.tarifa_credito_total, data.tarifa_debito_total, data.juros_credito_total, data.juros_debito_total);
        $("#remove-tax-modal").modal("hide");
    });
});

$(document).on("click", ".remove-activity-button", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/remove-activity",
        data: {
            "id": $(".remove-activity-id").val(),
            "rubrica": $(".items").val(),
        },
        beforeSend: function () {
            $(".remove-activity-loading-gif").show();
            $("#remove-activity-modal span").css("opacity", "0.3");
            $(".remove-activity-button").attr("disabled", "disabled");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        $(".remove-activity-loading-gif").hide();
        $("#remove-activity-modal span").css("opacity", "1");
        $(".remove-activity-button").removeAttr("disabled");
        expenseTable.ajax.reload();
        calculateProjectValues($(".project-income").unmask() / 100, $(".project-expense").unmask() / 100 - data.valor);
        calculateItemValues(data.pago);
        if ($(".free-project").val() == 1) {
            $(".projects").trigger("change");
        } else {
            $(".expense-provider, .modal-expense-provider").trigger("change");
            activityTable.ajax.reload();
        }
        $("#remove-activity-modal").modal("hide");
    });
});

$(document).on("click", ".add-more-products", function () {
    $(".expense-activity").select2("destroy");
    var container = $(".product-container").get(0);
    var el = $(container).clone().insertAfter($(".product-container").last()).find("input, select").val("");
    $(".product-container").last().append("<i class='remove-product fa fa-times darkred'></i>");

    $(".expense-activity").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
        templateResult: activityResult,
        templateSelection: activitySelection,
        escapeMarkup: function (m) {
            return m;
        },
    });
    $('.expense-money').maskMoney({
        allowNegative: true,
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });
});

$(document).on("click", ".modal-add-more-products i", function () {
    $(".modal-expense-activity").select2("destroy");
    var container = $(".modal-product-container").get(0);
    var el = $(container).clone().insertAfter($(".modal-product-container").last()).find("input, select").val("");
    $(".modal-product-container").last().append("<i class='modal-remove-product fa fa-times darkred'></i>");

    $(".modal-expense-activity").select2({
        placeholder: "Selecione...",
        language: "pt-BR",
        allowClear: true,
        templateResult: activityResult,
        templateSelection: activitySelection,
        escapeMarkup: function (m) {
            return m;
        },
    });
    $('.modal-expense-money').maskMoney({
        allowNegative: true,
        prefix: "R$ ",
        thousands: ".",
        decimal: ",",
    });
});

$(document).on("click", ".remove-product", function () {
    $(this).closest(".product-container").remove();
});

$(document).on("click", ".modal-remove-product", function () {
    $(this).closest(".modal-product-container").remove();
});

$(document).on("click", ".upload-fake-button", function () {
    $(this).parent().children(".upload-input").click();
});

$(document).on("click", ".project-bank-statements", function () {
    $(".upload-container").find(".upload-input").val("");
    $(".upload-container").find(".file-name").html("");
    $(".upload-container").find(".upload").hide();
    $(".upload-container").find(".alert").hide();
    $(".upload-container").find(".upload-file-loading-gif").hide();
    $("#bank-statements-modal").modal("show");
});

$(document).on("change", ".upload-input", function () {
    var file = $(this).prop('files')[0];
    $(this).parent().children(".file-name").html('<input type="text" class="file-name-input form-control" placeholder="Nome"><span>.pdf</span>');
    $(this).parent().children(".upload").show();
    $(this).parent().find(".alert").hide();
});

$(document).on('click', '.upload', function () {
    var button = this;
    if ($(this).closest(".upload-container").find(".file-name-input").val() !== "") {
        var fileData = $(this).parent().children(".upload-input").prop('files')[0];
        var formData = new FormData();
        formData.append('file', fileData);
        formData.append('falseName', $(this).closest(".upload-container").find(".file-name-input").val());
        formData.append("id", $(".projects").val());
        $.ajax({
            url: '/index.php?r=site/upload-file', // point to server-side PHP script
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            type: 'post',
            beforeSend: function () {
                $(button).parent().children(".upload-file-loading-gif").css("display", "inline-block");
                $(button).attr("disabled", "disabled");
            },
            success: function (data) {
                $(button).parent().children(".upload-file-loading-gif").hide();
                $(button).removeAttr("disabled");
                data = JSON.parse(data);
                if (data.valid) {
                    $(button).parent().find(".alert").addClass("alert-success").removeClass("alert-danger").html(data.message).show();
                    $(button).parent().find(".upload-input").val("");
                    $(button).parent().find(".file-name").html("");
                    $(button).parent().children(".upload").hide();
                    $(button).closest(".modal-body").find(".no-files").remove();
                    $(button).closest(".modal-body").find(".uploaded-files-container").append('<div class="file-container"><input type="hidden" class="file-id" value="' + data.midia.id + '"><i class="file-icon fa fa-file-pdf-o"></i><a target="_blank" href="http://' + data.midia.link + '">' + data.midia.nome_falso + '</a><i class="remove-file fa fa-times darkred"></i></div>');
                } else {
                    $(button).parent().find(".alert").removeClass("alert-success").addClass("alert-danger").html(data.message).show();
                }
            }
        });
    } else {
        $(button).parent().find(".alert").removeClass("alert-success").addClass("alert-danger").html('"Nome" obrigatório.').show();
    }
});

$(document).on("click", ".remove-file", function () {
    $(".remove-file-id").val($(this).parent().children(".file-id").val());
    $("#remove-file-modal").modal("show");
});

$(document).on("click", ".remove-file-button", function (evt) {
    $.ajax({
        method: "POST",
        url: "/index.php?r=site/remove-file",
        data: {
            "id": $(".remove-file-id").val(),
        },
        beforeSend: function () {
            $(".remove-file-loading-gif").show();
            $("#remove-file-modal span").css("opacity", "0.3");
            $(".remove-file-button").attr("disabled", "disabled");
        },
    }).success(function () {
        $(".remove-file-loading-gif").hide();
        $("#remove-file-modal span").css("opacity", "1");
        $(".remove-file-button").removeAttr("disabled");
        $("#bank-statements-modal").find(".upload-container").find(".alert").hide();
        $("#bank-statements-modal").find(".file-container").each(function () {
            if ($(this).find(".file-id").val() == $(".remove-file-id").val()) {
                $(this).remove();
            }
        });
        if (!$(".uploaded-files-container").children().length) {
            $(".uploaded-files-container").html('<div class="alert no-files">Nenhum extrato bancário.</div>');
            $(".no-files").show();
        }
        $("#remove-file-modal").modal("hide");

    });
});

$(document).on("change", ".expense-provider", function () {
    $(".expense-favorite").val($(this).val()).trigger("change.select2");
});

$(document).on("change", ".modal-expense-provider", function () {
    $(".modal-expense-favorite").val($(this).val()).trigger("change.select2");
});

$(document).on("change", ".income-plot", function () {
    if ($(this).val() === "") {
        $(".income-paying-source").val("");
    } else {

    }
});