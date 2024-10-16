let wreqhttp = (e) =>
    new Promise((t, n) => {
        let o = new XMLHttpRequest();
        o.open("GET", e),
            o.send(),
            (o.onload = function () {
                200 != o.status ? alert(`Error ${o.status}: ${o.statusText}`) : t(JSON.parse(o.response));
            }),
            (o.onerror = function () {
                alert("Request failed");
            });
    });

// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Bar Chart Example
wreqhttp('functions/bdata.php?request=fw0w_dkar&dt0wq='+Math.random()*1000).then((result)=>{
// daily
var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: result.response.label,
    datasets: [
      {
      label: "Test Transactions",
      backgroundColor: result.response.color,
      hoverBackgroundColor: "#2e59d9",
      borderColor: "#4e73df",
      data: result.response.data,
    },
    {
      data: result.response.expenses,
      borderColor: "#ff0",
      label: 'Expenses',
      fill: false,
      // Changes this dataset to become a line
      type: 'bar',
      pointRadius: 4,
      backgroundColor: "#ff0",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'month'
        },
        gridLines: {
          display: true
        },
        ticks: {
          maxTicksLimit: 0
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          maxTicksLimit: 5,
          padding: 10,
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: true,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: true
    },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10
    },
  }
});
})

