
let jr = (e) =>
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


jr('functions/bdata.php?uf=z1238&rt4='+Math.random()*10).then((xhr)=>{
  /*
  $('#transfers').html("₦"+xhr.methods.transfers);
  $('#cash').html("₦"+xhr.methods.cash)
  $('#cheque').html("₦"+xhr.methods.cheque);
  $('#pos').html("₦"+xhr.methods.pos);
  */
  $('#mtotalSales').html("₦"+xhr.mtotalSales);
  $('#outstandingTab').html("₦"+xhr.outstanding);
  /*
  $('#outstanding').html("₦"+xhr.outstanding);
  $('#totalSales').html("₦"+xhr.totalSales);
  */
  $('#monthlyExpense').html("₦"+xhr.mTotalExpenses);
  $('#totalExpenses').html("₦"+xhr.totalExpenses);


  // Area Chart Example
  var ctx = document.getElementById("myAreaChart");
  var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: xhr.response.rduration,
    datasets: [{
      label: "Sales",
      lineTension: 0.3,
      backgroundColor: xhr.response.daysColor,
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: xhr.response.dailySale
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: true,
          drawBorder: true
        },
        ticks: {
          maxTicksLimit: 0
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10
    }
  }
});
})