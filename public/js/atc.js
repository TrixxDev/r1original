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

  $('.rims-sorter').each(function() {
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

  $('.rims-tread-sorter').each(function() {
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
});
