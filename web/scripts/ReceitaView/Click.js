const {origin,pathname} = window.location;
$("#categoria-select").change(function () {
    $("#tipo-de-despesa-text").css("display", "none");
    $("#tipo-de-despesa-container").css("display", "none");
    if($("#categoria-select").val() == "5") {
        $('#despesa-select').empty();
        url = `${origin}${pathname}?r=site%2Fget-type-of-expense`;
        $.ajax({
            url: url,
        }).done(function (r) {
            $.each( $.parseJSON( r ), function(nome, id){
                $('#despesa-select').append($('<option>', {
                    value: id,
                    text: nome
                }));
            });
        });
        $("#tipo-de-despesa-container").css("display", "block");
    }
});

$('#despesa-select').change(function () {
    $("#tipo-de-despesa-text").css("display", "none");
    if($("#despesa-select").val() == 55) {
        $("#tipo-de-despesa-text").css("display", "block");
    }
});
