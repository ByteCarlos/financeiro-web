var loginForm = $("#login-form");
var recoverForm = $("#recover-form");
var passwordForm = $("#password-form");

loginForm.validate({
    rules: {
        email: {
            required: true,
            email: true,
        },
        password: {
            required: true
        }
    },
    messages: {
        email: {
            required: "Campo obrigat&oacute;rio.",
            email: "Digite um endere&ccedil;o de e-mail v&aacute;lido."
        },
        password: {
            required: "Campo obrigat&oacute;rio.",
        }
    },
    errorClass: "invalid",
    highlight: function (element, errorClass) {
        $(element).addClass(errorClass);
        $(element).prev().addClass(errorClass);
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass);
        $(element).prev().removeClass(errorClass);
    },
    invalidHandler: function (event, validator) {
        loginForm.find(".alert").addClass("alert-danger").show();
        loginForm.find(".alert > span").text("Preencha os dados corretamente.");
    },
    submitHandler: function (form) {
        loginForm.find(".alert-danger").removeClass("alert-danger").hide();
        $.ajax({
            url: "/index.php?r=login/login",
            method: "POST",
            data: {
                email: loginForm.find("input[name=email]").val(),
                password: loginForm.find("input[name=password]").val(),
                rememberme: loginForm.find("input[name=persistent]").is(":checked"),
            },
            beforeSend: function (xhr) {
                $('*').css('cursor', 'wait');
                loginForm.find('.login').attr('disabled', 'disabled');
            },
        }).success(function (data) {
            data = $.parseJSON(data);
            if (!data.valid) {
                $('*').css('cursor', '');
                loginForm.find('.login').removeAttr('disabled');
                loginForm.find(".alert").addClass("alert-danger").show();
                loginForm.find(".alert > span").html(data.error);
            } else {
                window.location = "?r=site/index";
            }
        });
    }
});

recoverForm.validate({
    rules: {
        email: {
            required: true,
            email: true
        }
    },
    messages: {
        email: {
            required: "Campo obrigat&oacute;rio.",
            email: "Digite um endere&ccedil;o de e-mail v&aacute;lido."
        },
    },
    errorClass: "invalid",
    highlight: function (element, errorClass) {
        $(element).addClass(errorClass);
        $(element).prev().addClass(errorClass);
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass);
        $(element).prev().removeClass(errorClass);
    },
    invalidHandler: function (event, validator) {
        recoverForm.find(".alert").addClass("alert-danger").show();
        recoverForm.find(".alert > span").text("Preencha o e-mail corretamente.");
    },
    submitHandler: function (form) {
        recoverForm.find(".alert-danger").removeClass("alert-danger").hide();
        $.ajax({
            url: "/index.php?r=login/password-recover",
            method: "POST",
            data: {
                email: recoverForm.find("input[name=email]").val()
            },
            beforeSend: function (xhr) {
                $('*').css('cursor', 'wait');
                recoverForm.find('.recover').attr('disabled', 'disabled');
            },
        }).success(function (data) {
            data = $.parseJSON(data);
            if (!data.valid) {
                recoverForm.find(".alert").addClass("alert-danger").show();
                recoverForm.find(".alert > span").html(data.error);
            } else {
                recoverForm.find("input").val("");
                $(".login-panel").show();
                $(".recover-panel").hide();
                loginForm.find(".alert").removeClass("alert-danger").addClass("alert-success").show();
                loginForm.find(".alert > span").html("Verifique o seu e-mail.");
                $("input[name=email]").focus();
            }
            $('*').css('cursor', '');
            recoverForm.find('.recover').removeAttr('disabled');
        });
    }
});

passwordForm.validate({
    rules: {
        password: {
            required: true,
        },
        confirm: {
            required: true,
            equalTo: passwordForm.find("input[name=password]"),
        },
    },
    messages: {
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
        $(element).prev().addClass(errorClass);
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass(errorClass);
        $(element).prev().removeClass(errorClass);
    },
    invalidHandler: function (event, validator) {
        passwordForm.find(".alert").addClass("alert-danger").show();
        passwordForm.find(".alert > span").text("Preencha os dados corretamente.");
    },
    submitHandler: function (form) {
        passwordForm.find(".alert-danger").removeClass("alert-danger").hide();
        $.ajax({
            url: "/index.php?r=login/change-password",
            method: "POST",
            data: {
                password: passwordForm.find("input[name=password]").val(),
                confirm: passwordForm.find('input[name=confirm]').val(),
                email: getParameterByName("email"),
            },
            beforeSend: function (xhr) {
                $('*').css('cursor', 'wait');
                passwordForm.find('.change-password').attr('disabled', 'disabled');
            },
        }).success(function (data) {
            data = $.parseJSON(data);
            if (!data.valid) {
                passwordForm.find(".alert").addClass("alert-danger").show();
                passwordForm.find(".alert > span").html(data.error);
            } else {
                $(".login-panel").show();
                $(".password-panel").hide();
                loginForm.find(".alert").removeClass("alert-danger").addClass("alert-success").show();
                loginForm.find(".alert > span").html("Senha alterada com sucesso.");
                $("input[name=email]").focus();
            }
            $('*').css('cursor', '');
            passwordForm.find('.change-password').removeAttr('disabled');
        });
    }
});

$(document).on("click", "#forget-password", function () {
    $(".login-panel").hide();
    $(".recover-panel").show();
    $("input[name=email]").focus();
});

$(document).on("click", ".back-to-login", function (e) {
    $(".login-panel").show();
    $(".recover-panel").hide();
    $(".password-panel").hide();
    $("input[name=email]").focus();
});

$(document).on("keydown", "form input", function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        $(this).closest("form").find("button").click();
    }
});

$(document).on("click", "form button", function (event) {
    event.preventDefault();
    $(this).closest("form").submit();
});

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
