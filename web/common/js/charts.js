$(".charts-menu").addClass("active");
google.charts.load('current', {'packages': ['corechart', 'bar']});
$('.year').datepicker({
    language: "pt-BR",
    format: "yyyy",
    autoclose: true,
    viewMode: 2,
    minViewMode: 2
}).on("changeDate", function () {
    $.ajax({
        method: "POST",
        url: "/index.php?r=charts/get-year-charts",
        data: {
            "year": $(this).val(),
        },
        beforeSend: function () {
            $(".load-year-loading-gif").css("display", "inline-block");
            $(".charts").css("opacity", "0.3");
        },
    }).success(function (data) {
        data = JSON.parse(data);
        google.charts.setOnLoadCallback(function () {
            drawLineChart(data.line, $(".year").val());
            $(".load-year-loading-gif").hide();
            $(".charts").css("opacity", "1");
        });
    });
});
$(".year").val(new Date().getFullYear()).trigger("changeDate");

function drawLineChart(data, year) {
    var matrix = [];
    var length = Object.keys(data).length;
    for (var i = 0; i <= 12; i++) {
        if (i === 0) {
            matrix[i] = ["Mês"];
            matrix[i].push("Despesas Administrativas");
            matrix[i].push("Mediana");
        } else {
            matrix[i] = [data[i].mes];
            matrix[i].push(data[i].percentual == undefined ? 0 : data[i].percentual);
            matrix[i].push(data.mediana);

        }
    }
    var data = google.visualization.arrayToDataTable(matrix);

    var options = {
        title: ' Evolução dos gastos do IPTI em ' + year + ' (%)',
        chartArea: {width: '95%'},
        height: 500,
        legend: {position: 'top', maxLines: 4},
        backgroundColor: {fill: 'transparent'},
    };

    var chart = new google.visualization.LineChart(document.getElementById('line'));

    chart.draw(data, options);
}