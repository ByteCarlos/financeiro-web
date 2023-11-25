$(".users-menu").addClass("active");
$(".users").select2({
    placeholder: "Selecione...",
    language: "pt-BR",
    sorter: function (data) {
        return data.sort(function (a, b) {
            return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
        });
    }
});

$(document).on("change", ".users", function () {
    if ($(this).val() !== "") {
        $.ajax({
            method: "POST",
            url: "/index.php?r=user/get-user",
            data: {
                "id": $(this).val()
            },
            beforeSend: function () {
                $(".load-users-loading-gif").css("display", "inline-block");
                $(".user-info").css("opacity", "0.3");
            },
        }).success(function (data) {
            data = JSON.parse(data);
            $(".user-remove").remove();
            $(".user-name").text(data.nome);
            $(".user-email").text(data.email);
            $(".user-admin").text(data.admin ? "Sim" : "Não");
            $(".user-admin-lancamentos").html((data.admin_lancamentos ? "Sim" : "Não") + '<i class="fa fa-refresh change-icon change-admin-lancamentos darkred"></i>');
            $(".user-admin-projetos").html((data.admin_projetos ? "Sim" : "Não") + '<i class="fa fa-refresh change-icon change-admin-projetos darkred"></i>');
            $(".user-admin-fornecedores").html((data.admin_fornecedores ? "Sim" : "Não") + '<i class="fa fa-refresh change-icon change-admin-fornecedores darkred"></i>');
            $(".user-assessor").html((data.assessor ? "Sim" : "Não") + '<i class="fa fa-refresh change-icon change-assessor darkred"></i>');
            data.admin
                ? $(".user-assessor, .user-admin-lancamentos, .user-admin-projetos, .user-admin-fornecedores").parent().hide()
                : $(".user-assessor, .user-admin-lancamentos, .user-admin-projetos, .user-admin-fornecedores").parent().show();
            if (!data.admin_other_platform && !data.admin) {
                $('<i class="user-remove darkred fa fa-times" data-toggle="modal" data-target="#remove-user-modal"></i>').insertBefore(".load-users-loading-gif");
            }
            $(".user-situation").html((data.ativo ? "Ativo" : "Inativo") + (!data.admin && !data.admin_other_platform ? '<i class="fa fa-refresh change-icon change-situation darkred"></i>' : ''));
            $(".load-users-loading-gif").hide();
            $(".user-info").css("opacity", "1");
            $(".user-info").css("display", "inline-block");
        });
    } else {
        $(".user-info, .user-remove").hide();
    }
});
$(".users").trigger("change");

$(document).on("click", ".change-situation", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=user/change-situation",
        data: {
            "id": $(".users").val()
        },
        beforeSend: function () {
            $(icon).addClass("fa-spin");
            $(".change-icon").prop("disabled", true);
        },
    }).success(function (data) {
        $(".users").trigger("change");
        $(icon).removeClass("fa-spin");
        $(".change-icon").prop("disabled", false);
    });
});

$(document).on("click", ".change-assessor", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=user/change-assessor",
        data: {
            "id": $(".users").val()
        },
        beforeSend: function () {
            $(icon).addClass("fa-spin");
            $(".change-icon").prop("disabled", true);
        },
    }).success(function (data) {
        $(".users").trigger("change");
        $(icon).removeClass("fa-spin");
        $(".change-icon").prop("disabled", false);
    });
});

$(document).on("click", ".change-admin-lancamentos", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=user/change-admin-lancamentos",
        data: {
            "id": $(".users").val()
        },
        beforeSend: function () {
            $(icon).addClass("fa-spin");
            $(".change-icon").prop("disabled", true);
        },
    }).success(function (data) {
        $(".users").trigger("change");
        $(icon).removeClass("fa-spin");
        $(".change-icon").prop("disabled", false);
    });
});

$(document).on("click", ".change-admin-projetos", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=user/change-admin-projetos",
        data: {
            "id": $(".users").val()
        },
        beforeSend: function () {
            $(icon).addClass("fa-spin");
            $(".change-icon").prop("disabled", true);
        },
    }).success(function (data) {
        $(".users").trigger("change");
        $(icon).removeClass("fa-spin");
        $(".change-icon").prop("disabled", false);
    });
});

$(document).on("click", ".change-admin-fornecedores", function () {
    var icon = this;
    $.ajax({
        method: "POST",
        url: "/index.php?r=user/change-admin-fornecedores",
        data: {
            "id": $(".users").val()
        },
        beforeSend: function () {
            $(icon).addClass("fa-spin");
            $(".change-icon").prop("disabled", true);
        },
    }).success(function (data) {
        $(".users").trigger("change");
        $(icon).removeClass("fa-spin");
        $(".change-icon").prop("disabled", false);
    });
});

$(document).on("click", ".remove-user-button", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=user/remove-user",
        data: {
            "id": $(".users").val(),
        },
        beforeSend: function () {
            $(".remove-user-loading-gif").show();
            $("#remove-user-modal .modal-body").css("opacity", "0.3");
            $(".remove-user-button").attr("disabled", "disabled");
        },
    }).success(function () {
        $(".remove-user-loading-gif").hide();
        $("#remove-user-modal .modal-body").css("opacity", "1");
        $(".remove-user-button").removeAttr("disabled");
        $("#remove-user-modal").modal("hide");
        $(".users").select2("destroy");
        $(".users").find("option[value=" + $(".users").val() + "]").remove();
        $(".users").select2({
            placeholder: "Selecione...",
            language: "pt-BR",
            sorter: function (data) {
                return data.sort(function (a, b) {
                    return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                });
            }
        });
        $(".users").trigger("change");
    });
});

$(document).on("click", ".add-new-user", function () {
    $("#add-user-modal").find(".alert").hide();
    $("#add-user-modal input[type=text]").val("");
    $(".add-user-role").val("").trigger("change");
    $("#add-user-modal").modal("show");
});

$("#add-user-form").validate({
    rules: {
        name: {
            twoNames: true,
            required: true,
        },
        email: {
            required: true,
            email: true,
        },
    },
    messages: {
        name: {
            twoNames: "Informe um nome completo.",
            required: "Campo obrigat&oacute;rio.",
        },
        email: {
            required: "Campo obrigat&oacute;rio.",
            email: "Informe um endere&ccedil;o de e-mail v&aacute;lido."
        },
    },
    errorClass: "invalid",
    highlight: function (element, errorClass) {
        $(element).addClass(errorClass);
        if ($(element).is("select")) {
            $(element).next().addClass(errorClass);
        }
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass);
        if ($(element).is("select")) {
            $(element).next().removeClass(errorClass);
        }
    },
    errorPlacement: function (error, element) {
        $(element).parent().append(error);
    },
    invalidHandler: function (validator) {
        $("#add-user-form").find(".alert").addClass("alert-danger").show();
        $("#add-user-form").find(".alert").text("Preencha os dados corretamente.");
    },
    submitHandler: function (form) {
        $("#add-user-form").find(".alert-danger").removeClass("alert-danger").hide();
        $.ajax({
            url: "/index.php?r=user/add-user",
            method: "POST",
            data: {
                name: $(".add-user-name").val(),
                email: $(".add-user-email").val(),
            },
            beforeSend: function (xhr) {
                $(".add-user-loading-gif").show();
                $("#add-user-form").css("opacity", "0.3");
                $("#add-user-form").find('button').attr('disabled', 'disabled');
            },
        }).success(function (data) {
            data = JSON.parse(data);
            if (!data.valid) {
                $("#add-user-form").find(".alert").addClass("alert-danger").show();
                $("#add-user-form").find(".alert").html(data.error);
            } else {
                $(".users").select2("destroy");
                $(".users").append("<option value='" + data.usuario.id + "'>" + data.usuario.nome + "</option>");
                $(".users").select2({
                    placeholder: "Selecione...",
                    language: "pt-BR",
                    sorter: function (data) {
                        return data.sort(function (a, b) {
                            return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                        });
                    }
                });
                $(".users").val(data.usuario.id).trigger("change");
                $("#add-user-modal").modal("hide");
            }
            $(".add-user-loading-gif").hide();
            $("#add-user-form").css("opacity", "1");
            $("#add-user-form").find('button').removeAttr('disabled');
        });
    }
});

$("#add-user-form").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        $("#add-user-form .add-user-button").click();
    }
});

$(".add-user-button").click(function (event) {
    event.preventDefault();
    $("#add-user-form").submit();
});