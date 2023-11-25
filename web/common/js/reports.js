$(".reports-menu").addClass("active");

$(document).on("click", ".print", function () {
    var title = "";
    var html = "";
    switch ($('.report').val()) {
        case "conciliacao":
            title += "Relatório de Conciliação";
            html += $(".conciliation-table")[0].outerHTML +
                $(".fare-table")[0].outerHTML +
                $(".interest-table")[0].outerHTML +
                $(".total-income").parent()[0].outerHTML +
                $(".total-expense").parent()[0].outerHTML;
            break;
        case "contabil":
            title += "Relatório Contábil";
            html += $(".generate-container").find(".table")[0].outerHTML;
            break;
        case "financeiro":
            title += "Relatório Financeiro";
            html +=
                $(".project-title")[0].outerHTML + $(".project-date")[0].outerHTML +
                $(".generate-container").find(".expense-table")[0].outerHTML +
                $(".generate-container").find(".income-table")[0].outerHTML +
                $(".generate-container").find(".fare-table")[0].outerHTML +
                $(".generate-container").find(".interest-table")[0].outerHTML +
                $(".outtable-label").parent()[0].outerHTML;

            break;
        case "provisionamento":
            title += "Relatório de Provisionamento";
            html += $(".generate-container").find(".table")[0].outerHTML;
            break;
    }
    var popup = window.open('', '', 'toolbar=no, menubar=no');
    popup.document.writeln('<!DOCTYPE html>');
    popup.document.writeln('<html moznomarginboxes mozdisallowselectionprint><head><title>' + title + '</title>');
    popup.document.writeln('<link rel="stylesheet" type="text/css" media="print" href="../common/css/prints/print-reports.css">');
    popup.document.writeln('</head><body>');
    popup.document.writeln("<div class='body-container'>" + html + "</div>");
    popup.document.writeln('</body>');
    popup.document.writeln('</html>');
    popup.document.close();
    popup.onload = function () {
        setTimeout(function () {
            popup.focus();
            popup.print();
            popup.close();
        }, 200);
    };
})

$(".report").select2({
    placeholder: "Selecione...",
    language: "pt-BR",
    sorter: function (data) {
        return data.sort(function (a, b) {
            return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
        });
    }
});

var select2 = {
    placeholder: "Selecione...",
    width: "400px",
    language: "pt-BR",
    sorter: function (data) {
        return data.sort(function (a, b) {
            return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
        });
    }
};

select2.multiple = true;
$(".contracts").select2(select2).val(null).trigger("change.select2");

$(document).on("change", ".report", function () {
    $(".contracts option").prop("disabled", false);
    switch ($(this).val()) {
        case "provisionamento":
            $(".provisioning-interval").trigger("change");
            $(".provisioning-interval-container").show();
            $(".costing-container").hide();
            $(".contracts option[livre=1]").prop("disabled", false);
            $(".contracts option[ativo=0]").prop("disabled", true);
            break;
        case "conciliacao":
            $(".report-initial-date").show();
            $(".provisioning-interval-container").hide();
            $(".costing-container").show();
            $(".contracts option[livre=1]").prop("disabled", false);
            $(".contracts option[ativo=0]").prop("disabled", false);
            break;
        case "contabil":
            $(".report-initial-date").hide();
            $(".provisioning-interval-container").hide();
            $(".costing-container").hide();
            $(".contracts option[livre=1]").prop("disabled", true);
            $(".contracts option[ativo=0]").prop("disabled", false);
            break;
        case "financeiro":
            $(".report-initial-date").show();
            $(".provisioning-interval-container").hide();
            $(".costing-container").hide();
            $(".contracts option[livre=1]").prop("disabled", true);
            $(".contracts option[ativo=0]").prop("disabled", false);
            break;
    }

    $(".contracts").select2('destroy');
    if ($(this).val() === "financeiro") {
        if (!$(".contracts > option").length) {
            $(".contracts").prepend(new Option());
        }
        select2.multiple = false;
        $(".contracts").select2(select2).val("").trigger("change.select2");
        $(".include-options").hide();
    } else {
        if ($(".contracts > option").length) {
            $(".contracts > option").remove();
        }
        select2.multiple = true;
        $(".contracts").select2(select2).val(null).trigger("change.select2");
        $(".include-options").show();
    }

    if ($(this).val() === "provisionamento") {
        if ($(".report-initial-date").val() !== "") {
            var currentDate = new Date().setHours(0, 0, 0, 0);
            var selectedDateStr = $('.report-initial-date').val().split("/");
            var selectedDate = new Date(selectedDateStr[2], selectedDateStr[1] - 1, selectedDateStr[0]).setHours(0, 0, 0, 0);
            if (selectedDate < currentDate) {
                $(".report-initial-date").val("").datepicker("update");
            }
        }
        $('.report-initial-date').data('datepicker').setStartDate(new Date());
        $('.report-final-date').data('datepicker').setStartDate(new Date());
    } else {
        $('.report-initial-date').data('datepicker').setStartDate(new Date(1900, 0, 1, 0, 0, 0));
        $('.report-final-date').data('datepicker').setStartDate(new Date(1900, 0, 1, 0, 0, 0));
    }
});

$(".date").inputmask({ mask: '99/99/9999', showMaskOnHover: false });

$(".report-initial-date").datepicker({
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
    if ($(".report-initial-date").val() !== "" && $(".report-initial-date").inputmask('unmaskedvalue').length == 8
        && $(".report-final-date").val() !== "" && $(".report-final-date").inputmask('unmaskedvalue').length == 8) {
        var startDateStr = $(".report-initial-date").val().split("/");
        var startDate = !indirect ? new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0) : new Date(startDateStr[2], startDateStr[1] - 1, startDateStr[0], 0, 0, 0);
        var endDateStr = $(".report-final-date").val().split("/");
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

$(".report-final-date").datepicker({
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
    if ($(".report-initial-date").val() !== "" && $(".report-initial-date").inputmask('unmaskedvalue').length == 8
        && $(".report-final-date").val() !== "" && $(".report-final-date").inputmask('unmaskedvalue').length == 8) {
        var endDate = new Date(ev.date.getFullYear(), ev.date.getMonth(), ev.date.getDate(), 0, 0, 0);
        var startDateStr = $(".report-initial-date").val().split("/");
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
$(".report-initial-date").trigger("changeDate", [true]);

$(document).on("keyup", ".report-initial-date, .report-final-date", function (e) {
    if (e.keyCode == 13) {
        $(".apply-filters").click();
    }
});

$('.report-initial-date, .report-final-date').on('paste', function () {
    if ($(this).val() !== "" && $(this).inputmask('unmaskedvalue').length == 8) {
        $(this).datepicker("hide").blur();
    }
});

$(document).on("click", ".active-options", function () {
    $(".contracts").val([]).trigger("change.select2");
    $(".contracts option[ativo=1]:not(:disabled)").prop("selected", true).trigger("change.select2");
});

$(document).on("click", ".inactive-options", function () {
    $(".contracts").val([]).trigger("change.select2");
    $(".contracts option[ativo=0]:not(:disabled)").prop("selected", true).trigger("change.select2");
});

$(document).on("click", ".free-options", function () {
    $(".contracts").val([]).trigger("change.select2");
    $(".contracts option[livre=1]:not(:disabled)").prop("selected", true).trigger("change.select2");
});

$(document).on("click", ".chained-options", function () {
    $(".contracts").val([]).trigger("change.select2");
    $(".contracts option[livre=0]:not(:disabled)").prop("selected", true).trigger("change.select2");
});

$(document).on("click", ".all-options", function () {
    $(".contracts option:not(:disabled)").prop("selected", true).trigger("change.select2");
});

$(document).on("click", ".remove-options", function () {
    $(".contracts").val([]).trigger("change.select2");
});

$(document).on("change", ".provisioning-interval", function () {
    $(this).is(":checked")
        ? $(".report-initial-date").show()
        : $(".report-initial-date").hide();
});

$(document).on("click", ".apply-filters", function () {
    if ($(".report").val() !== ""
        && ($(".contracts").val() !== "" && $(".contracts").val() !== null)
        && (($(".report").val() === "conciliacao" && correctIntervalDate)
            || ($(".report").val() === "financeiro" && correctIntervalDate)
            || ($(".report").val() === "contabil" && $(".report-final-date").val() !== "")
            || ($(".report").val() === "provisionamento"
                && (($(".provisioning-interval").is(":checked") && correctIntervalDate)
                    || (!$(".provisioning-interval").is(":checked") && $(".report-final-date").val() !== ""))))) {
        $(".report-error").hide();
        $.ajax({
            method: "POST",
            url: "/index.php?r=reports/generate-report",
            data: {
                "reportType": $(".report").val(),
                "contracts": $(".contracts").val(),
                "initialDate": $(".report-initial-date").val(),
                "finalDate": $(".report-final-date").val(),
                "onlyCosting": $(".only-costing").is(":checked"),
                "provisioningInterval": $(".provisioning-interval").is(":checked")
            },
            beforeSend: function () {
                $(".apply-filters").find("i").removeClass("fa-search").addClass("fa-spin").addClass("fa-spinner");
                $(".apply-filters").attr("disabled", "disabled");
                $(".generate-container").css("opacity", "0.3");
            },
            error: function (request, status, error) {
                alert(request.status + ": " + request.statusText + "\nOcorreu um erro inesperado. Tente novamente.\nCaso o erro persista, contate o administrador do sistema.");
            },
            success: function (data) {
                data = JSON.parse(data);
                $(".generate-container").html("");
                var html = "";
                switch ($(".report").val()) {
                    case "conciliacao":
                        html += buildConciliationReportStructure(data);
                        $(".generate-container").html(html);
                        $(".date").each(function () {
                            var date = $(this).text().split("-");
                            $(this).text(date[2] + "/" + date[1] + "/" + date[0]);
                        })
                        initConciliationDatatable();
                        refreshTotalIncome();
                        refreshTotalExpense();
                        $(".buttons-container").append('<button class="hidden-print btn btn-default pull-right export-conciliation-xls"><i class="fa fa-file-pdf-o"></i> XLS (Conciliação)</button>');
                        $(".buttons-container").append('<button class="hidden-print btn btn-default pull-right export-fare-xls"><i class="fa fa-file-pdf-o"></i> XLS (Tarifas)</button>');
                        $(".buttons-container").append('<button class="hidden-print btn btn-default pull-right export-interest-xls"><i class="fa fa-file-pdf-o"></i> XLS (Juros de Poupança)</button>');
                        // $(".buttons-container").append('<button class="hidden-print btn btn-default pull-right export-dominio-txt"><i class="fa fa-balance-scale"></i> TXT (Domínio)</button>');
                        break;
                    case "contabil":
                        html += buildAccountingReportStructure(data);
                        $(".generate-container").html(html);
                        initAccountingDatatable();
                        break;
                    case "financeiro":
                        html += buildFinancialReportStructure(data);
                        $(".generate-container").html(html);
                        $(".money").priceFormat({
                            prefix: 'R$ ',
                            centsSeparator: ',',
                            thousandsSeparator: '.',
                            allowNegative: true
                        });
                        break;
                    case "provisionamento":
                        html += buildProvisioningReportStructure(data);
                        $(".generate-container").html(html);
                        initProvisioningDatatable();
                        break;
                }
                $(".buttons-container").prepend('<button class="hidden-print btn btn-default pull-right print"><i class="fa fa-print"></i> Imprimir</button>');
                $(".generate-container").show();
            },
            complete: function () {
                $(".apply-filters").find("i").addClass("fa-search").removeClass("fa-spin").removeClass("fa-spinner");
                $(".apply-filters").removeAttr("disabled");
                $(".generate-container").css("opacity", "1");
            }
        });
    } else {
        $(".generate-container").hide();
        $(".report-error").text("Preencha os campos de pesquisa corretamente.").show();
        $("html").animate({ scrollTop: 0 }, "fast");
    }
});

function buildConciliationReportStructure(data) {
    var lancamentos = "";
    $.each(data.lancamentos, function (data, lancamento) {
        $.each(lancamento, function () {
            lancamentos += ''
                + '<tr>'
                + '<td class="date" style="white-space: pre;">' + data + '</td>'
                + '<td>' + this.contrato + '</td>'
                + '<td class="provider">' + this.fornecedor + '</td>'
                + '<td class="description">' + this.descricao + '</td>'
                + '<td>' + (this.competencia === null ? "" : this.competencia) + '</td>'
                + '<td>' + (this.rcc === null ? "" : this.rcc) + '</td>'
                + '<td class="money green income-launch">' + (this.tipo == "receita" ? Math.round(Number(this.valor) * 100) : (this.tipo === "despesa" && Number(this.valor) < 0) ? Math.round(Number(-this.valor)) : "") + '</td>'
                + '<td class="money red expense-launch">' + (this.tipo == "despesa" && Number(this.valor) > 0 ? Math.round(Number(this.valor)) : "") + '</td>'
                + '<td style="mso-number-format:0">' + (this.numero_transferencia_cheque === null ? "" : this.numero_transferencia_cheque) + '</td>'
                + '</tr>'
        })
    });
    var tarifas = "";
    $.each(data.tarifas, function () {
        tarifas += ''
            + '<tr>'
            + '<td class="date" style="white-space: pre;">' + this.data + '</td>'
            + '<td>' + this.contrato + '</td>'
            + '<td>' + this.descricao + '</td>'
            + '<td class="money green income-fare">' + (this.tipo === "C" ? Math.round(Number(this.valor) * 100) : "") + '</td>'
            + '<td class="money red expense-fare">' + (this.tipo === "D" ? Math.round(Number(this.valor) * 100) : "") + '</td>'
            + '</tr>'
    });
    var jurosDePoupancas = "";
    $.each(data.jurosDePoupancas, function () {
        jurosDePoupancas += ''
            + '<tr>'
            + '<td class="date" style="white-space: pre;">' + this.data + '</td>'
            + '<td>' + this.contrato + '</td>'
            + '<td>' + this.descricao + '</td>'
            + '<td class="money green income-fare">' + (this.tipo === "C" ? Math.round(Number(this.valor) * 100) : "") + '</td>'
            + '<td class="money red expense-fare">' + (this.tipo === "D" ? Math.round(Number(this.valor) * 100) : "") + '</td>'
            + '</tr>'
    });
    return ''
        + '<div class="search-container form-inline form-group hidden-print">'
        + '<label for="provision-search control-label">Pesquisar:</label>'
        + '<input type="text" class="form-control" id="conciliation-search">'
        + '</div>'
        + '<table class="table table-bordered conciliation-table" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr>'
        + '<th colspan="9" class="table-title"><span class="initial-date-title">' + $(".report-initial-date").val() + "</span> - <span class='final-date-title'>" + $(".report-final-date").val() + '</span></th>'
        + '</tr>'
        + '<tr>'
        + '<th style="width: 65px">Data</th>'
        + '<th style="">Projeto</th>'
        + '<th style="">Fornecedor</th>'
        + '<th style="">Descrição</th>'
        + '<th style="">Competência</th>'
        + '<th style="">Rubrica/C.Custo</th>'
        + '<th style="">Receitas</th>'
        + '<th style="">Despesas</th>'
        + '<th style="white-space: pre">Nº Tr./Cheque</th>'
        + '</tr>'
        + '</thead>'
        + '<tbody>'
        + lancamentos
        + '</tbody>'
        + '<tfoot style="display: table-row-group">'
        + '<tr>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td class="money total total-income-launch"></td>'
        + '<td class="money total total-expense-launch"></td>'
        + '<td></td>'
        + '</tr>'
        + '</tfoot>'
        + '</table>'
        + '<table class="table table-bordered fare-table" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr>'
        + '<th colspan="5" class="table-title">Tarifas</th>'
        + '</tr>'
        + '<tr>'
        + '<th style="width: 65px">Data</th>'
        + '<th style="width: 10%">Projeto</th>'
        + '<th style="width: 55%;">Descrição</th>'
        + '<th style="width: 15%;">Crédito</th>'
        + '<th style="width: 15%;">Débito</th>'
        + '</tr>'
        + '</thead>'
        + '<tbody>'
        + tarifas
        + '</tbody>'
        + '<tfoot style="display: table-row-group">'
        + '<tr>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td class="money total total-income-fare"></td>'
        + '<td class="money total total-expense-fare"></td>'
        + '</tr>'
        + '</tfoot>'
        + '</table>'
        + '<table class="table table-bordered interest-table" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr>'
        + '<th colspan="5" class="table-title">Juros de Poupança</th>'
        + '</tr>'
        + '<tr>'
        + '<th style="width: 65px">Data</th>'
        + '<th style="width: 10%">Projeto</th>'
        + '<th style="width: 55%;">Descrição</th>'
        + '<th style="width: 15%;">Crédito</th>'
        + '<th style="width: 15%;">Débito</th>'
        + '</tr>'
        + '</thead>'
        + '<tbody>'
        + jurosDePoupancas
        + '</tbody>'
        + '<tfoot style="display: table-row-group">'
        + '<tr>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td class="money total total-income-interest"></td>'
        + '<td class="money total total-expense-interest"></td>'
        + '</tr>'
        + '</tfoot>'
        + '</table>'
        + '<div class="outtable-container">'
        + '<label class="outtable-label">Despesa total:</label>'
        + '<span class="total-expense"></span>'
        + '</div>'
        + '<div>'
        + '<label class="outtable-label">Receita total:</label>'
        + '<span class="total-income"></span>'
        + '</div>'
        + '<div class="buttons-container"></div>';
}

function buildAccountingReportStructure(data) {
    var rubricas = "";
    $.each(data.pagamentos, function () {
        rubricas += ''
            + '<tr>'
            + '<td>' + this.contrato + '</td>'
            + '<td>' + (this.categoria === undefined ? "" : this.categoria) + '</td>'
            + '<td>' + (this.descricao === undefined ? "" : this.descricao) + '</td>'
            + '<td class="money value">' + Math.round(Number(this.valor_total) * 100) + '</td>'
            + '<td class="money paid">' + Math.round(Number(this.valor_utilizado) * 100) + '</td>'
            + '<td class="money remaining">' + Math.round(Number(this.valor_restante) * 100) + '</td>'
            + '</tr>';
    });
    return ''
        + '<div class="search-container form-inline form-group hidden-print">'
        + '<label for="provision-search control-label">Pesquisar:</label>'
        + '<input type="text" class="form-control" id="general-search">'
        + '</div>'
        + '<table class="table table-bordered" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr><th colspan="6" class="table-title">' + $(".report-final-date").val() + '</th></tr>'
        + '<tr>'
        + '<th style="width: 20%;">Projeto</th>'
        + '<th style="width: 20%">Categoria</th>'
        + '<th style="width: 40%;">Descrição</th>'
        + '<th style="width: 20%;white-space: pre">Valor da rubrica</th>'
        + '<th style="width: 20%;white-space: pre">Valor gasto</th>'
        + '<th style="width: 20%;white-space: pre">Valor restante</th>'
        + '</tr>'
        + '</thead>'
        + '<tbody>'
        + rubricas
        + '</tbody>'
        + '<tfoot style="display: table-row-group">'
        + '<tr>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td class="money total total-value"></td>'
        + '<td class="money total total-paid"></td>'
        + '<td class="money total total-remaining"></td>'
        + '</tr>'
        + '</tfoot>'
        + '</table>'
        + '<div class="buttons-container"></div>';
}

function buildFinancialReportStructure(data) {
    var despesas = "";
    $.each(data.despesas, function () {
        despesas += ''
            + '<tr>'
            + '<td>' + this.descricao + '</td>'
            + '<td class="money">' + Math.round(Number(this.saldo_inicial) * 100) + '</td>'
            + '<td class="money">' + Math.round(Number(this.valor) * 100) + '</td>'
            + '<td class="money">' + Math.round(Number(this.saldo_final) * 100) + '</td>'
            + '</tr>';
    });
    var receitas = "";
    $.each(data.receitas, function () {

        if (this.data == null) this.data = '<center>-</center>'
        else data_temp = this.data.split("-").reverse().join("/");
        if (this.descricao == null) this.descricao = '<center>-</center>'
        if (this.tipo_de_receita == null) this.tipo_de_receita = '<center>-</center>'
        if (this.tipo_de_despesa == null) this.tipo_de_despesa = '<center>-</center>'
        if (this.titulo_da_receita == null) this.titulo_da_receita = '<center>-</center>'
        if (this.parcela == null) {this.parcela = '<center>-</center>'} else {this.parcela += 'ª'}
        if (this.fonte_pagadora == null) this.fonte_pagadora = '<center>-</center>'

        receitas += ''
            + '<tr>'
            + '<td>' + data_temp + '</td>'
            + '<td>' + this.descricao + '</td>'
            + '<td>' + this.tipo_de_receita + '</td>'
            + '<td>' + this.tipo_de_despesa + '</td>'
            + '<td>' + this.titulo_da_receita + '</td>'
            + '<td>' + this.parcela + '</td>'
            + '<td>' + this.fonte_pagadora + '</td>'
            + '<td><center>-</center></td>'
            + '<td class="money">' + Math.round(Number(this.valor) * 100) + '</td>'
            + '<td><center>-</center></td>'
            + '</tr>';
    });
    return ''
        + '<p class="project-title">' + $('.contracts').select2('data')[0]['text'] + '</p>'
        + '<div class="project-date">' + ($(".report-initial-date").val() + " - " + $(".report-final-date").val()) + '</div>'
        + '<table class="expense-table table table-bordered" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr><th colspan="5" class="table-title"><i class="expense-icon fa fa-arrow-circle-o-down"></i> Despesas</th></tr>'
        + '<tr><th style="width: 50%;">Descrição</th><th style="width: 15%;">Saldo Anterior</th><th style="width: 20%;">Valor</th><th style="width: 15%;">Saldo Atual</th></tr>'
        + '</thead>'
        + '<tbody>'
        + despesas
        + '<tr><td></td><td></td><td class="money total">' + Math.round(Number(data.despesaRubricas.valor) * 100) + '</td><td></td></tr>'
        + '</tbody>'
        + '</table>'
        + '<table class="income-table table table-bordered" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr><th colspan="10" class="table-title"><i class="expense-icon fa fa-arrow-circle-o-up"></i> Receitas</th></tr>'
        + '<tr><th style="width: 5%;">Data</th><th style="width: 10%;">Descrição</th><th style="width: 10%;">Categoria</th><th style="width: 10%;">Tipo de Despesa</th><th style="width: 10%;">Tipo</th><th style="width: 5%;">Parcela</th><th style="width: 10%;">Fonte de Recursos</th><th style="width: 10%;">Saldo Anterior</th><th style="width: 10%;">Valor</th><th style="width: 10%;">Saldo Atual</th></tr>'
        + '</thead>'
        + '<tbody>' + receitas
        + '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class="money">' + Math.round(Number(data.receitaTotal.saldo_inicial) * 100) + '</td><td class="money total">' + Math.round(Number(data.receitaTotal.valor) * 100) + '</td><td class="money">' + Math.round(Number(data.receitaTotal.saldo_final) * 100) + '</td></tr>'
        + '</tbody>'
        + '</table>'
        + '<table class="fare-table table table-bordered" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr><th colspan="5" class="table-title">Tarifas</th></tr>'
        + '<tr><th style="width: 50%;">Descrição</th><th style="width: 15%;"></th><th style="width: 20%;">Valor</th><th style="width: 15%;"></th></tr>'
        + '</thead>'
        + '<tbody>'
        + '<tr><td>Crédito</td><td></td><td class="money">' + Math.round(Number(data.tarifaCreditoTotal) * 100) + '</td><td></td></tr>'
        + '<tr><td>Débito</td><td></td><td class="money">' + Math.round(Number(data.tarifaDebitoTotal) * 100) + '</td><td></td></tr>'
        + '<tr><td>Saldo</td><td></td><td class="money total">' + Math.round((Number(data.tarifaCreditoTotal) - Number(data.tarifaDebitoTotal)) * 100) + '</td><td></td></tr>'
        + '</tbody>'
        + '</table>'
        + '<table class="interest-table table table-bordered" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr><th colspan="5" class="table-title">Juros de Poupança</th></tr>'
        + '<tr><th style="width: 50%;">Descrição</th><th style="width: 15%;"></th><th style="width: 20%;">Valor</th><th style="width: 15%;"></th></tr>'
        + '</thead>'
        + '<tbody>'
        + '<tr><td>Crédito</td><td></td><td class="money">' + Math.round(Number(data.jurosCreditoTotal) * 100) + '</td><td></td></tr>'
        + '<tr><td>Débito</td><td></td><td class="money">' + Math.round(Number(data.jurosDebitoTotal) * 100) + '</td><td></td></tr>'
        + '<tr><td>Saldo</td><td></td><td class="money total">' + Math.round((Number(data.jurosCreditoTotal) - Number(data.jurosDebitoTotal)) * 100) + '</td><td></td></tr>'
        + '</tbody>'
        + '</table>'
        + '<div><label class="outtable-label">Saldo Total: </label><span class="money">'
        + Math.round(((Number(data.receitaTotal.valor) + Number(data.tarifaCreditoTotal) + Number(data.jurosCreditoTotal)) - (Number(data.despesaRubricas.valor) + Number(data.tarifaDebitoTotal) + Number(data.jurosDebitoTotal))) * 100)
        + '</span></div>'
        + '<div class="buttons-container"></div>';
}

function buildProvisioningReportStructure(data) {
    var rubricas = "";
    $.each(data, function () {
        rubricas += ''
            + '<tr>'
            + '<td>' + this.contrato + '</td>'
            + '<td>' + (this.categoria === undefined ? "" : this.categoria) + '</td>'
            + '<td>' + (this.descricao === undefined ? "" : this.descricao) + '</td>'
            + '<td class="money item">' + (!this.livre ? Math.round(Number(this.valor_total) * 100) : "") + '</td>'
            + '<td class="money paid">' + (!this.livre ? Math.round(Number(this.valor_pago) * 100) : "") + '</td>'
            + '<td class="money provisioned">' + Math.round(Number(this.valor_provisionado) * 100) + '</td>'
            + '<td class="money remaining">' + (!this.livre ? Math.round((Number(this.valor_total) - (Number(this.valor_pago) + Number(this.valor_provisionado))) * 100) : "") + '</td>'
            + '</tr>';
    });
    return ''
        + '<div class="search-container form-inline form-group hidden-print">'
        + '<label for="provision-search control-label">Pesquisar:</label>'
        + '<input type="text" class="form-control" id="general-search">'
        + '</div>'
        + '<table class="table table-bordered" cellspacing="0" width="100%">'
        + '<thead>'
        + '<tr><th colspan="7" class="table-title">' + (($(".provisioning-interval").is(":checked") ? $(".report-initial-date").val() + " - " : "") + $(".report-final-date").val()) + '</th></tr>'
        + '<tr>'
        + '<th style="width: 10%;">Projeto</th>'
        + '<th style="width: 20%">Categoria</th>'
        + '<th style="width: 40%;">Descrição</th>'
        + '<th style="width: 15%;white-space: pre">Valor da rubrica</th>'
        + '<th style="width: 15%;white-space: pre">Valor gasto</th>'
        + '<th style="width: 15%;white-space: pre">Valor provisionado</th>'
        + '<th style="width: 15%;white-space: pre">Valor restante</th>'
        + '</tr>'
        + '</thead>'
        + '<tbody>'
        + rubricas
        + '</tbody>'
        + '<tfoot style="display: table-row-group">'
        + '<tr>'
        + '<td></td>'
        + '<td></td>'
        + '<td></td>'
        + '<td class="money total total-item"></td>'
        + '<td class="money total total-paid"></td>'
        + '<td class="money total total-provisioned"></td>'
        + '<td class="money total total-remaining"></td>'
        + '</tr>'
        + '</tfoot>'
        + '</table>'
        + '<div class="buttons-container"></div>';
}

function initConciliationDatatable() {
    $(".conciliation-table").DataTable({
        "bPaginate": false,
        "bInfo": false,
        "ordering": false,
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sZeroRecords": "Nenhum registro encontrado",
            "sLoadingRecords": "Carregando...",
        },
        "autoWidth": true,
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var incomeTotal = api
                .column(6, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var expenseTotal = api
                .column(7, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(6).footer()).html(
                incomeTotal
            );
            $(api.column(7).footer()).html(
                expenseTotal
            );

            $(".conciliation-table .money").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        initComplete: function () {
            count = 0;
            this.api().columns().every(function (index) {
                if (index === 5) {
                    var title = this.header();
                    //replace spaces with dashes
                    title = $(title).html().replace(/[\W]/g, '-');
                    var column = this;
                    var select = $('<select class="filter-select2" ></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            //Get the "text" property from each selected data
                            //regex escape the value and store in array
                            var data = $.map($(this).select2('data'), function (value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                            });

                            //if no data selected use ""
                            if (data.length === 0) {
                                data = [""];
                            }

                            //join array into string with regex or (|)
                            var val = data.join('|');

                            //search for the option(s) selected
                            column
                                .search(val ? val : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function (d, j) {
                        if (d !== "") {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        }
                    });

                    //use column title as selector and placeholder
                    $('.filter-select2').select2({
                        multiple: true,
                        theme: "default filter-select2 hidden-print",
                    });

                    //initially clear select otherwise first option is selected
                    $('.filter-select2').val(null).trigger('change');
                }
            });
        }
    });
    $(".fare-table").DataTable({
        "bPaginate": false,
        "bInfo": false,
        "ordering": false,
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sZeroRecords": "Nenhum registro encontrado",
            "sLoadingRecords": "Carregando...",
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;

            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var incomeTotal = api
                .column(3, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var expenseTotal = api
                .column(4, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(3).footer()).html(
                incomeTotal
            );
            $(api.column(4).footer()).html(
                expenseTotal
            );

            $(".fare-table .money").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        }
    });
    $(".interest-table").DataTable({
        "bPaginate": false,
        "bInfo": false,
        "ordering": false,
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sZeroRecords": "Nenhum registro encontrado",
            "sLoadingRecords": "Carregando...",
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var incomeTotal = api
                .column(3, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var expenseTotal = api
                .column(4, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(3).footer()).html(
                incomeTotal
            );
            $(api.column(4).footer()).html(
                expenseTotal
            );

            $(".interest-table .money").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
    });
}

function initAccountingDatatable() {
    $(".table").DataTable({
        "bPaginate": false,
        "bInfo": false,
        "ordering": false,
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sZeroRecords": "Nenhum registro encontrado",
            "sLoadingRecords": "Carregando...",
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var itemTotal = api
                .column(3, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var paidTotal = api
                .column(4, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var remainingTotal = api
                .column(5, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(3).footer()).html(
                itemTotal
            );
            $(api.column(4).footer()).html(
                paidTotal
            );
            $(api.column(5).footer()).html(
                remainingTotal
            );

            $(".table .money").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        initComplete: function () {
            count = 0;
            this.api().columns().every(function (index) {
                if (index === 1 || index === 2) {
                    //replace spaces with dashes
                    var column = this;
                    var select = $('<select class="filter-select2"></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            //Get the "text" property from each selected data
                            //regex escape the value and store in array
                            var data = $.map($(this).select2('data'), function (value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                            });

                            //if no data selected use ""
                            if (data.length === 0) {
                                data = [""];
                            }

                            //join array into string with regex or (|)
                            var val = data.join('|');

                            //search for the option(s) selected
                            column
                                .search(val ? val : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function (d, j) {
                        if (d !== "") {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        }
                    });

                    //use column title as selector and placeholder
                    $('.filter-select2').select2({
                        multiple: true,
                        theme: "default filter-select2 hidden-print",
                    });

                    //initially clear select otherwise first option is selected
                    $('.filter-select2').val(null).trigger('change');
                }
            });
        }
    });
}

function initProvisioningDatatable() {
    $(".table").DataTable({
        "bPaginate": false,
        "bInfo": false,
        "ordering": false,
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sZeroRecords": "Nenhum registro encontrado",
            "sLoadingRecords": "Carregando...",
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            var itemTotal = api
                .column(3, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var paidTotal = api
                .column(4, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var provisionedTotal = api
                .column(5, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            var remainingTotal = api
                .column(6, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
            $(api.column(3).footer()).html(
                itemTotal
            );
            $(api.column(4).footer()).html(
                paidTotal
            );
            $(api.column(5).footer()).html(
                provisionedTotal
            );
            $(api.column(6).footer()).html(
                remainingTotal
            );

            $(".table .money").priceFormat({
                prefix: 'R$ ',
                centsSeparator: ',',
                thousandsSeparator: '.',
                allowNegative: true
            });
        },
        initComplete: function () {
            count = 0;
            this.api().columns().every(function (index) {
                if (index === 1 || index === 2) {
                    //replace spaces with dashes
                    var column = this;
                    var select = $('<select class="filter-select2"></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            //Get the "text" property from each selected data
                            //regex escape the value and store in array
                            var data = $.map($(this).select2('data'), function (value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                            });

                            //if no data selected use ""
                            if (data.length === 0) {
                                data = [""];
                            }

                            //join array into string with regex or (|)
                            var val = data.join('|');

                            //search for the option(s) selected
                            column
                                .search(val ? val : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function (d, j) {
                        if (d !== "") {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        }
                    });

                    //use column title as selector and placeholder
                    $('.filter-select2').select2({
                        multiple: true,
                        theme: "default filter-select2 hidden-print",
                    });

                    //initially clear select otherwise first option is selected
                    $('.filter-select2').val(null).trigger('change');
                }
            });
        }
    });
}

$(document).on("input", "#general-search", function () {
    $(".table").DataTable().search(this.value).draw();
});

$(document).on("input", "#conciliation-search", function () {
    $(".table").DataTable().search(this.value).draw();
    refreshTotalIncome();
    refreshTotalExpense();
});

function refreshTotalIncome() {
    $(".total-income").html(Number($(".total-income-launch").unmask()) + Number($(".total-income-fare").unmask()) + Number($(".total-income-interest").unmask()));
    $(".total-income").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
}

function refreshTotalExpense() {
    $(".total-expense").html(Number($(".total-expense-launch").unmask()) + Number($(".total-expense-fare").unmask()) + Number($(".total-expense-interest").unmask()));
    $(".total-expense").priceFormat({
        prefix: 'R$ ',
        centsSeparator: ',',
        thousandsSeparator: '.',
        allowNegative: true
    });
}

$(document).on("click", ".export-conciliation-xls", function () {
    $(".inv-container").html($(".conciliation-table")[0].outerHTML);
    $(".inv-container").find(".select2, select").remove();
    exportToExcel($(".inv-container")[0].innerHTML, 'Conciliação');
});

$(document).on("click", ".export-fare-xls", function () {
    exportToExcel($(".fare-table")[0].outerHTML, 'Tarifas');
});

$(document).on("click", ".export-interest-xls", function () {
    exportToExcel($(".interest-table")[0].outerHTML, 'Juros de Poupança');
});

function exportToExcel(table, worksheetName) {
    var uri = 'data:application/vnd.ms-excel;charset=UTF-8;base64,';
    var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
    var base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    };

    var format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) {
            return c[p];
        })
    };

    var ctx = {
        worksheet: [worksheetName],
        table: table
    };

    var link = document.createElement("a");
    link.download = worksheetName + " " + $(".report-initial-date").val() + "-" + $(".report-final-date").val() + ".xls";
    link.href = uri + base64(format(template, ctx));
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// $(document).on("click", ".export-dominio-txt", function () {
//     var element = document.createElement('a');
//     var content = "";
//     content += "01000005205929852000181" + $(".initial-date-title").text() + $(".final-date-title").text() + "N0500000117\n";
//     var sequenceCode = 1;
//     $(".conciliation-table tbody tr").each(function () {
//         var valor;
//         if ($(this).find(".income-launch").text() !== "") {
//             valor = Math.abs($(this).find(".income-launch").unmask());
//         } else {
//             valor = Math.abs($(this).find(".expense-launch").unmask());
//         }
//         content += "02" + numberPad(sequenceCode, 7) + "X" + $(this).find(".date").text() + fillStringByLength("GERENTE", 30) + fillStringByLength("", 100) + "\n";
//         sequenceCode++;
//         content += "03" + numberPad(sequenceCode, 7) + numberPad(Number($(this).find(".debit-account").text()), 7) + numberPad(Number($(this).find(".credit-account").text()), 7) + numberPad(valor, 15) + "0000000" + fillStringByLength($(this).find(".description").text(), 512) + "0000052" + fillStringByLength("", 100) + "\n";
//         sequenceCode++;
//     });
//     content += "9999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999";
//     element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
//     element.setAttribute('download', "Export " + $(".report-initial-date").val() + " - " + $(".report-initial-date").val() + ".txt");
//
//     element.style.display = 'none';
//     document.body.appendChild(element);
//
//     element.click();
//
//     document.body.removeChild(element);
// });

function numberPad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function fillStringByLength(str, length) {
    str = str.length > length ? str.substring(0, length) : str;
    str = str + Array(length + 1 - str.length).join(" ");
    return str;
}

//provisionamento de fornecedor
$("#provision-report-container .table").DataTable({
    "bPaginate": false,
    "bInfo": false,
    "ordering": false,
    "language": {
        "sEmptyTable": "Nenhum registro encontrado",
        "sZeroRecords": "Nenhum registro encontrado",
        "sLoadingRecords": "Carregando...",
    },
    "footerCallback": function (row, data, start, end, display) {
        var api = this.api(), data;
        var intVal = function (i) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                    i : 0;
        };
        var itemTotal = api
            .column(3, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        var paidTotal = api
            .column(4, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        var provisionedTotal = api
            .column(5, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        var remainingTotal = api
            .column(6, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
        $(api.column(3).footer()).html(
            itemTotal
        );
        $(api.column(4).footer()).html(
            paidTotal
        );
        $(api.column(5).footer()).html(
            provisionedTotal
        );
        $(api.column(6).footer()).html(
            remainingTotal
        );

        $(".table .money").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            allowNegative: true
        });
    },
    initComplete: function () {
        count = 0;
        this.api().columns().every(function (index) {
            if (index === 1 || index === 2) {
                //replace spaces with dashes
                var column = this;
                var select = $('<select class="filter-select2"></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        //Get the "text" property from each selected data
                        //regex escape the value and store in array
                        var data = $.map($(this).select2('data'), function (value, key) {
                            return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                        });

                        //if no data selected use ""
                        if (data.length === 0) {
                            data = [""];
                        }

                        //join array into string with regex or (|)
                        var val = data.join('|');

                        //search for the option(s) selected
                        column
                            .search(val ? val : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    if (d !== "") {
                        select.append('<option value="' + d + '">' + d + '</option>');
                    }
                });

                //use column title as selector and placeholder
                $('.filter-select2').select2({
                    multiple: true,
                    theme: "default filter-select2 hidden-print",
                });

                //initially clear select otherwise first option is selected
                $('.filter-select2').val(null).trigger('change');
            }
        });
    }
});
