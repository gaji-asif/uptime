$(document).ready(function() {
  var labels = $("#categorynames").val() ? $("#categorynames")
    .val()
    .split(",") : '';
  var data = $("#count_data").val() ? $("#count_data")
    .val()
    .split(",") : '';
  var hover_txt = $("#hover_txt").val();
  var hover_labels = hover_txt.split("&&&");
  var hover_sublabels = [];
  for (var i = 0; i < hover_labels.length; i++) {
    hover_sublabels[i] = hover_labels[i].split("#");
  }
  var backgroundcolor = ["rgba(115, 132, 233, 1)", "rgba(145, 218, 205, 1)", "rgba(235, 71, 113, 1)", "rgba(68, 132, 144, 1)", "rgba(127, 126, 125, 1)", "rgba(212, 157, 83, 1)", "rgba(192, 105, 85, 1)"];
  var color = [];
  for (var i = 0; i < labels.length; i++) {
    if (i >= length) color[i] = backgroundcolor[i % labels.length];
    else color[i] = backgroundcolor[i];
  }
  var ctx = document.getElementById("employeeChart") ? document.getElementById("employeeChart").getContext("2d") : null;
  var chartoption1 = {
    responsive: true,
    tooltips: {
      callbacks: {
        title: function(tooltipItem, data) {
          return data["labels"][tooltipItem[0]["index"]];
        },
        label: function(tooltipItem, data) {
          return data["datasets"][0]["data"][tooltipItem["index"]];
        },
        afterLabel: function(tooltipItem, data) {
          return hover_sublabels[tooltipItem["index"]];
        },
      },
    },
    scales: { yAxes: [{ ticks: { beginAtZero: true } }], xAxes: [{ ticks: { autoSkip: false }, gridLines: { color: "rgba(0, 0, 0, 0.3)", display: false } }] },
    legend: { display: false },
  };
  chartoption2 = {
    responsive: true,
    tooltips: {
      callbacks: {
        title: function(tooltipItem, data) {
          return data["labels"][tooltipItem[0]["index"]];
        },
        label: function(tooltipItem, data) {
          return data["datasets"][0]["data"][tooltipItem["index"]];
        },
        afterLabel: function(tooltipItem, data) {
          return hover_sublabels[tooltipItem["index"]];
        },
      },
    },
    scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }], xAxes: [{ ticks: { autoSkip: false }, gridLines: { color: "rgba(0, 0, 0, 0.3)", display: false } }] },
    legend: { display: false },
  };
  var chartoption;
  if (data[0] == "0" || data[0] === "") chartoption = chartoption2;
  else {
    var maxval = 0;
    for (var i = 0; i < data.length; i++) {
      if (maxval < data[i]) maxval = data[i];
    }
    if (maxval > 5) chartoption = chartoption1;
    else chartoption = chartoption2;
  }
  if (ctx){
    var myChart = new Chart(ctx, {
        type: "bar",
        data: { labels: labels, datasets: [{ label: "", data: data, backgroundColor: color, borderColor: color, borderWidth: 2 }] },
        click: function(e) {
        alert(e.dataSeries.type + " x:" + e.dataPoint.x + ", y: " + e.dataPoint.y);
        },
        options: chartoption,
    });
    myChart.render();
  }
});
