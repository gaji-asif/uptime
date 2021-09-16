$(document).ready(function(){
  
  //get the category names   
  //var labels = $('#categorynames').val().split(",");
  //var data = $('#count_data').val().split(",");
  var backgroundcolor = ['rgba(79, 129, 188, 1)',
          'rgba(192, 80, 78, 1)',
          'rgba(155, 187, 88, 1)',
          'rgba(35, 191, 170, 1)','rgba(128, 100, 161, 1)','rgba(74, 172, 197, 1)','rgba(247, 150, 71, 1)','rgba(127, 96, 132, 1)'];
 /* var color = [];
  for(var i = 0 ; i < labels.length ; i ++){
     if(i >= length) color[i] = backgroundcolor[i%labels.length];
     else color[i] = backgroundcolor[i];
  }
  
  */
  
   /*the section of drawing chart*/  
  var ctx = document.getElementById("employeeChart").getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: ['Event','Sales','Operation'],
        data: ['20','30','40'],
        backgroundColor:['rgba(79, 129, 188, 1)',
          'rgba(192, 80, 78, 1)',
          'rgba(155, 187, 88, 1)'],
        borderColor:['rgba(79, 129, 188, 1)',
          'rgba(192, 80, 78, 1)',
          'rgba(155, 187, 88, 1)'],
        borderWidth: 2
      }]
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });

/*The section of drawing  challenge space */

});