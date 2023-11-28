$('.modal').modal({backdrop: 'static', show: false});

function changeNameLength(name, limit) {
    return (name.length > limit) ? name.substring(0, limit - 3) + "..." : name;
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function providerResult(data) {
    var textSplit = data.text.split("|");
    var identification = "";
    if (textSplit[1] !== undefined) {
        identification = textSplit[1].length === 18 ? "CNPJ" : "CPF";
    }
    return "<div class='select2-provider-name'>" + textSplit[0] + "</div>" +
        "<div><span class='select2-provider-identification'>" + identification + ":</span> " + textSplit[1] + "</div>" +
        "<div>" + textSplit[2] + "</div>";
}

function providerSelection(data) {
    var textSplit = data.text.split("|");
    return textSplit[0];
}

function activityResult(data) {
    if (!data.loading) {
        if (data.element.tagName === "OPTGROUP") {
            return data.text;
        } else {
            var textSplit = data.text.split("|");
            var date = textSplit[5].split("-");
            var icon = Math.round(Number(textSplit[2])) < Math.round(Number(textSplit[3])) ? "<i class='select2-activity-icon fa fa-question-circle'></i>" : (Math.round(Number(textSplit[2])) == Math.round(Number(textSplit[3])) ? "<i class='select2-activity-icon fa fa-check-circle'></i>" : "<i class='select2-activity-icon fa fa-times-circle'></i>");
            return "<div class='select2-activity-order'>" + (textSplit[4] + "º") + "</div><div class='select2-activity-info-container'><div class='select2-activity-name'>" + textSplit[1] + "</div>" +
                "<div><span class='select2-price-label'>Valor:</span><span class='select2-activity-price'>" + Number(textSplit[2]).toFixed(2) + "</span></div>" +
                "<div><span class='select2-price-label'>Pago:</span><span class='select2-activity-paid'>" + Number(textSplit[3]).toFixed(2) + "</span>" + icon + "</div>" +
                (textSplit[6] !== undefined ? "<div><span class='select2-activity-label'>Fonte:</span><span class='select2-activity-paying-source'>" + textSplit[6] + "</span></div>" : "") +
                "<div><span class='select2-activity-date'>" + date[2] + "/" + date[1] + "/" + date[0] + "</span></div></div>";
        }
    }
}

function activitySelection(data) {
    if (data.element !== undefined && data.element.tagName === "OPTGROUP") {
        return data.text;
    } else if (data.text == "Selecione...") {
        return data.text;
    } else {
        var textSplit = data.text.split("|");
        if (textSplit[6] !== undefined) {
            $(".income-paying-source").val(textSplit[6]);
        }
        return textSplit[1];
    }
}

function translateTipoDeConta(tipoDeConta) {
    switch(tipoDeConta) {
        case "CC":
            return "Conta Corrente";
        case "CP":
            return "Conta Poupança";
        case "CS":
            return "Conta Salário";
    }
}

$(".expense-activity, .modal-expense-activity, .income-plot").on("select2:open", function () {
    setTimeout(function () {
        $(".select2-activity-price, .select2-activity-paid").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            allowNegative: true
        });
    }, 0);
});

jQuery.validator.addMethod("twoNames", function (value, element) {
    var names = value.split(" ");
    for (var i = 0; i < names.length; i++) {
        if (names[i] == "") {
            names[i] = null;
        }
    }
    var elementsWithText = 0;
    for (var i = 0; i < names.length; i++) {
        if (names[i] != null) {
            elementsWithText++;
        }
    }
    return elementsWithText >= 2;
});

$("#edit-user-form").validate({
    rules: {
        name: {
            twoNames: true,
            required: true,
        },
        email: {
            required: true,
            email: true,
        },
        password: {
            required: false
        },
        confirm: {
            required: false,
            equalTo: $("#edit-user-form").find("input[name=password]")
        },
    },
    messages: {
        name: {
            twoNames: "Informe seu nome completo.",
            required: "Campo obrigat&oacute;rio.",
        },
        email: {
            required: "Campo obrigat&oacute;rio.",
            email: "Informe um endere&ccedil;o de e-mail v&aacute;lido."
        },
        password: {
            required: "Campo obrigat&oacute;rio.",
        },
        confirm: {
            required: "Campo obrigat&oacute;rio.",
            equalTo: "As senhas preenchidas devem ser iguais."
        },
    },
    errorClass: "invalid",
    highlight: function (element, errorClass) {
        $(element).addClass(errorClass);
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass);
    },
    invalidHandler: function (validator) {
        $("#edit-user-form").find(".alert").addClass("alert-danger").show();
        $("#edit-user-form").find(".alert").text("Preencha os dados corretamente.");
    },
    submitHandler: function (form) {
        $("#edit-user-form").find(".alert-danger").removeClass("alert-danger").hide();
        $.ajax({
            url: "/index.php?r=global/edit-user",
            method: "POST",
            data: {
                name: $(".edit-user-name").val(),
                email: $(".edit-user-email").val(),
                password: $("#edit-user-form").find("input[name=password]").val(),
                confirm: $("#edit-user-form").find("input[name=confirm]").val(),
            },
            beforeSend: function (xhr) {
                $(".edit-user-loading-gif").show();
                $("#edit-user-form").css("opacity", "0.3");
                $("#edit-user-form").find('.edit-user-info').attr('disabled', 'disabled');
            },
        }).success(function (data) {
            data = JSON.parse(data);
            if (!data.valid) {
                $("#edit-user-form").find(".alert").addClass("alert-danger").show();
                $("#edit-user-form").find(".alert").html(data.error);
                $(".edit-user-loading-gif").hide();
                $("#edit-user-form").css("opacity", "1");
                $("#edit-user-form").find('.edit-user-info').removeAttr('disabled');
            } else {
                window.location = window.location;
            }
        });
    }
});

$("#edit-user-form").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        $("#edit-user-form .edit-user-info").click();
    }
});

$(".edit-user-info").click(function (event) {
    event.preventDefault();
    $("#edit-user-form").submit();
});

$(document).on("click", ".edit-user", function () {
    $(this).addClass("fa-spin");
    setTimeout(function () {
        $(".edit-user").removeClass('fa-spin');
    }, 2000);
    $("#edit-user-modal").modal("show");
});

$(document).on("click", "#logout", function () {
    delete_cookie("access_token");
    window.location = "/";
});

function delete_cookie(name) {
    var domain = window.location.href.indexOf("ipti.org.br") !== -1 ? "domain=ipti.org.br;" : "";
    document.cookie = name + "=; Path=/; " + domain + " expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function ( a ) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },

    "date-uk-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "date-uk-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );