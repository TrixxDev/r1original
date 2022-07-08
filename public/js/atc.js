/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

$(document).ready(function () {
   $('.summer-sorter').each(function() {
     $(this).tablesorter({
         headers: {
           0: {sorter: false},
           1: {sorter: false},
           2: {sorter: false},
           3: {sorter: false},
           4: {sorter: false},
           5: {sorter: false},
           6: {sorter: false},
           9: {sorter: false},
           10: {sorter: false},
           11: {sorter: false}
         },
         // sortList: [[7,1]]
       }
     );
   });
  $('.industrial-sorter').each(function() {
    $(this).tablesorter({
        headers: {
          0: {sorter: false},
          1: {sorter: false},
          2: {sorter: false},
          3: {sorter: false},
          4: {sorter: false},
          5: {sorter: false},
          8: {sorter: false},
          9: {sorter: false},
          10: {sorter: false},
          11: {sorter: false}
        },
      }
    );
  });
  $('.moto-sorter').each(function() {
    $(this).tablesorter({
        headers: {
          0: {sorter: false},
          1: {sorter: false},
          2: {sorter: false},
          3: {sorter: false},
          4: {sorter: false},
          5: {sorter: true},
          6: {sorter: true},
          7: {sorter: false},
          8: {sorter: false},
          9: {sorter: false}
        },
        // sortList: [[7,1]]
      }
    );
  });
  $('.quadr-sorter').each(function() {
    $(this).tablesorter({
        headers: {
          0: {sorter: false},
          1: {sorter: false},
          2: {sorter: false},
          3: {sorter: true},
          4: {sorter: true},
          5: {sorter: false},
          6: {sorter: false},
          7: {sorter: false}
        },
        // sortList: [[7,1]]
      }
    );
  });

  let xValues = [];
  let yValues1 = [];
  let yValues2 = [];

  for (let i = 1; i < 26; i++) {
    xValues.push(i);
    yValues1.push(Math.floor((Math.random() * 30) + 15));
    yValues2.push(Math.floor((Math.random() * 10) + 10));
  }
  // console.log(xValues, yValues);
  new Chart("myChart", {
    type: "line",
    data: {
      labels: xValues,
      datasets: [{
        fill: false,
        lineTension: 0,
        backgroundColor: "gray",
        borderColor: "lightgray",
        data: yValues1
      },
        {
          fill: false,
          lineTension: 0,
          backgroundColor: "pink",
          borderColor: "pink",
          data: yValues2
        }]
    },
    options: {
      legend: {display: true},
      scales: {
        yAxes: [{ticks: {min: -20, max:100}}],
      }
    }
  });
});
