// /*
//  * Custom code goes here.
//  * A template should always ship with an empty custom.js
//  */
//
// //alert("test");
//
const ct_pagination = 0;
const ct_pagination_nb = 100000;
const ctp_fancybox = 0;
const ctp_sort = 1;
const ctp_sort_attr = 0;
const ctp_sort_attrid = '';
const ctp_sort_attrby = 0;
const ctp_psum = 0;
const ctp_cartexists = 1;
const products = [];
// SHOULD BE REMOVED IN PRODUCTION
const wheel_tires = [];

//
const url = window.location.pathname;
const pathParts = window.location.pathname.split('/');
//
const allVal = ['All', 'Visi', 'Все'];

const d1 = ($('.tire-width').val() == 'Visi') ? '' : $('.tire-width').val();
const d2 = ($('.tire-height').val() == 'Visi') ? '' : $('.tire-height').val();
const d3 = $('.tire-radius').val();
//
let date = '';
let queue_id = '';
let iorder = '';
let slot = '';
let slot_id = '';
let slot_time = '';

let selected_filiale = 0;
let selected_date = 0;

let sf_height = 0;

let public_url = '/public/storage/';
const grozs_url = $('#_desktop_cart .desktop').data('url');

let user = $('.user-info .account').data('user');
let user_role = $('.user-info .account').data('role');
let admin = false;
$.each(user_role, function(key, value) {
  if (value === 'administrators' || value === 'moderators') {
    admin = true;
  }
});

let tire_qty = '';

let total_price = parseInt($('.cart-summary-line.cart-total .value').text().trim().replace('€ ', ''));
let shipping = '';
let fitting = '';

$(document).on('mousedown', '#quick-popup', function(e){
  window.clickStartedInModal = $(e.target).is('#quick-popup *');
});

$(document).on('mouseup', '#quick-popup', function(e){
  if(!$(e.target).is('#quick-popup *') && window.clickStartedInModal) {
    window.preventModalClose = true;
  }
});

$("#quick-popup").on("hide.bs.modal", function (e) {
  if(window.preventModalClose){
    window.preventModalClose = false;
    return false;
  }
});

// $(document).on('mousedown', '.modal-dialog', function(e){
//   window.clickStartedInModal = $(e.target).is('.modal-dialog *');
// });
//
// $(document).on('mouseup', '.modal-dialog', function(e){
//   if(!$(e.target).is('.modal-dialog *') && window.clickStartedInModal) {
//     window.preventModalClose = true;
//   }
// });
//
// $(".modal-dialog").on("hide.bs.modal", function (e) {
//   if(window.preventModalClose){
//     window.preventModalClose = false;
//     return false;
//   }
// });

// Mobīlā versija pierakstam

$(document).ready(function() {

});

// !Mobīlā versija pierakstam


function formatNumber (num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}

function activeCart() {
  if ($('.cart-preview').hasClass('inactive')) {
    $('.cart-preview').removeClass('inactive').addClass('active');
    $('#_desktop_cart .desktop').wrapAll('<a rel="nofollow" href="' + grozs_url + '">');
    $('#_mobile_cart .mobile').wrapAll('<a rel="nofollow" href="' + grozs_url + '">');
  }
}

$(document).ready(function() {

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  if ($('.records').length !== 0) {
    $.ajax({
      url: '/pieraksts/fillFiliale',
      method: 'POST',
      dataType: 'JSON',
      success: function (data) {
        data.forEach(function (value, key) {
          $('<option value="' + value.office_id + '">' + value.title + '</option>').insertAfter($('#mobile-filiale select[name="filiale"] option').first());
        });
      }
    });
  }

  $('#mobile-filiale select[name="filiale"]').on('change', function () {
    if ($(this).hasClass('required-input')) {
      $(this).removeClass('required-input');
    }
    let office_id = $(this).val();
    $('section#mobile-main input[name="filiale"]').val(office_id);
    $.ajax({
      url: '/pieraksts/fillDates',
      method: 'POST',
      dataType: 'JSON',
      success: function (data) {
        $('.hidden-dates').slideDown();
        if (selected_filiale == 1) {
          $('#mobile-date select[name="reservation-date"] option').not(':first').remove();
          $('#mobile-date select[name="reservation-date"] option').first().prop('selected', true);
          $('#mobile-time select[name="reservation-time"] option').not(':first').remove();
          $('#mobile-time select[name="reservation-time"] option').first().prop('selected', true);
          $('.hidden-times').slideUp();
        }
        data.forEach(function (value, key) {
          $('<option value="' + value.date + '">' + value.date + ' - ' + value.day + '</option>').insertAfter($('#mobile-date select[name="reservation-date"] option').first());
        });
        selected_filiale = 1;
      }
    });
  });

  $('#mobile-date select[name="reservation-date"]').on('change', function () {
    if ($(this).hasClass('required')) {
      $(this).removeClass('required');
    }
    let date = $(this).val();
    $.ajax({
      url: '/pieraksts/fillSlots',
      method: 'POST',
      data: {'date': date, 'filiale': $('section#mobile-main input[name="filiale"]').val()},
      dataType: 'JSON',
      success: function (data) {
        $('.hidden-times').slideDown();
        if (selected_date == 1) {
          $('#mobile-time select[name="reservation-time"] option').not(':first').remove();
          $('#mobile-time select[name="reservation-time"] option').first().prop('selected', true);
        }
        $.each(data.times, function (key, value) {
          $('<option value="' + value.slot_id + '" data-time="' + value.time + '">' + value.time + '</option>').insertAfter($('#mobile-time select[name="reservation-time"] option').first());
        });
        selected_date = 1;
      }
    });
  });

  $('#mobile-time select[name="reservation-time"]').on('change', function () {
    if ($(this).hasClass('required-input')) {
      $(this).removeClass('required-input');
    }
    slot_id = $(this).val();
    slot_time = $('option:selected', this).data('time');
  });

  $('#mobile-service select[name="serviceOption"]').on('change', function () {
    if ($(this).hasClass('required-input')) {
      $(this).removeClass('required-input');
    }
  });

  $('#mobile-submit-reservation').on('click', function () {
    let car = $('#mobile-brand').val();
    let carModel = $('#mobile-model').val();
    let licPlate = $('#mobile-reg_nr').val();
    let filiale = $('#mobile-filiale select[name="filiale"]').val();
    let date = $('#mobile-date select[name="reservation-date"]').val();
    let purpose = $('#mobile-service select[name="serviceOption"]').val();
    let comment = $('#mobile-comment').val();
    let name = $('#mobile-name').val();
    let phone = $('#mobile-phone').val();
    let email = $('#mobile-email').val();


    $.ajax({
      url: '/pieraksts/fillSlotMobile',
      method: 'POST',
      dataType: 'JSON',
      data: {
        'car': car,
        'carModel': carModel,
        'licPlate': licPlate,
        'slot_id': slot_id,
        'purpose': purpose,
        'comment': comment,
        'name': name,
        'phone': phone,
        'email': email,
        'slot_time': slot_time,
        'filiale': filiale,
        'date': date
      },
      success: function (data) {
        if (data.error) {
          if (data.error.brand) $('#mobile-brand').attr('placeholder', data.error.brand);
          if (data.error.model) $('#mobile-model').attr('placeholder', data.error.model);
          if (data.error.reg_nr) $('#mobile-reg_nr').attr('placeholder', data.error.reg_nr);
          if (data.error.phone) $('#mobile-phone').attr('placeholder', data.error.phone);
          if (data.error.wrongPhone) {
            if ($('.phone-error').length == 0) {
              $('<div class="alert alert-danger phone-error">' + data.error.wrongPhone + '</div>').insertAfter('.mobile-body .form-group:last')
            }
          } else {
            $('.mobile-body .phone-error').remove();
          }
          if (data.error.email) $('#mobile-email').attr('placeholder', data.error.email);
          if (data.error.emptyEmail) {
            if ($('.email-error').length == 0) {
              $('<div class="alert alert-danger email-error">' + data.error.emptyEmail + '</div>').insertAfter('.mobile-body .form-group:last')
            }
          } else {
            $('.mobile-body .email-error').remove();
          }
          if (data.error.brand) {
            $('html, body').animate({
              scrollTop: $(".auto-model").offset().top
            });
          } else if (data.error.model) {
            $('html, body').animate({
              scrollTop: $(".auto-model").offset().top
            });
          } else if (data.error.filiale) {
            $('select[name="filiale"]').addClass('required-input');
            $('html, body').animate({
              scrollTop: $(".reservation-filiale").offset().top
            });
          } else if (data.error.reservationDate) {
            $('select[name="reservation-date"]').addClass('required-input');
            $('html, body').animate({
              scrollTop: $(".hidden-dates").offset().top
            });
          } else if (data.error.slotId) {
            $('select[name="reservation-time"]').addClass('required-input');
            $('html, body').animate({
              scrollTop: $(".hidden-times").offset().top
            });
          } else if (data.error.purpose) {
            $('select[name="serviceOption"]').addClass('required-input');
            $('html, body').animate({
              scrollTop: $('.purpose').offset().top
            });
          } else if (data.error.phone) {
            $('html, body').animate({
              scrollTop: $(".phone-number").offset().top
            });
          } else if (data.error.email) {
            $('html, body').animate({
              scrollTop: $(".client-email").offset().top
            });
          }
        } else if (data.success) {
          $('html, body').animate({
            scrollTop: $("section#mobile-main").offset().top
          });
          $('.mobile-reservation-modal-body .mobile-body').slideUp();
          $('.mobile-reservation-modal-body .mobile-body-success .alert').append(data.success);
          $('.mobile-reservation-modal-body .mobile-body-success').slideDown();
          $('#mobile-submit-reservation').slideToggle();
          $('#mobile-close-modal').slideToggle().on('click', function () {
            $(this).slideToggle();
            $('#mobile-submit-reservation').slideToggle();
            $('section#mobile-main form').trigger('reset');
            $('.hidden-dates').slideUp();
            $('.hidden-times').slideUp();
            $('.mobile-body-success').slideUp();
            $('.mobile-reservation-modal-body .mobile-body').slideDown();
            $('.mobile-reservation-modal-body .mobile-body-success .alert').text('');
          });
        }
      }
    });
  });
});

$('[data-toggle="tooltip"]').tooltip({
  content: function () {
    return $( $(this).attr('title') );
  }
});

sf_height = $('#search_filters').height();
// $('.show_list').click(function(){
//   document.cookie = "show_list=true; expires=Thu, 30 Jan 2100 12:00:00 UTC; path=/";
//   $('#js-product-list .product-miniature').addClass('product_show_list');
//   $('.table-top').addClass('product_show_list');
//   $('.custom_atv_name').addClass('product_show_list');
//   $('.show_list').addClass('active');
//   sortItemsInList();
// });

// $('.show_grid').click(function(){
//   document.cookie = "show_list=; expires=Thu, 30 Jan 1970 12:00:00 UTC; path=/";
//   $('#js-product-list .product-miniature').removeClass('product_show_list');
//   $('.table-top').removeClass('product_show_list');
//   $('.custom_atv_name').removeClass('product_show_list');
//   $('.show_list').removeClass('active');
//   sortItemsInBrand();
// });

function sortItemsInBrand() {
  var $brandP = $('.products').first();
  var $brands = $brandP.find('> .custom_brand_name');
  var dublicateTexts = [];
  $brands.each(function() {
    var $brand = $(this);
    var $articles = $brandP.find('article[data-brand="' + $(this).data('brand') + '"]');
    $articles.each(function () {
      var text = $(this).find('.product-title').text().trim();
      if (dublicateTexts.indexOf(text) === -1) {
        dublicateTexts.push(text);
        var inserted = false;
        $articles
          .filter(function () {
            return  $(this).find('.product-title').text().trim() === text;
          })
          .find('span.price')
          .sort(sorting)
          // .reverse()
          .map(function() {
            return $(this).parents('article').first();
          })
          .each(function () {
            if (inserted) {
              $(this).addClass('hidden');
              return;
            }
            inserted = true;

          });
      }
    });
    $articles
      .filter(function () {
        return !$(this).hasClass('hidden');
      })
      .find('span.price')
      .sort(sorting)
      .map(function() {
        return $(this).parents('article').first();
      })
      .each(function() {
        $(this).insertAfter($brand);
      });
  });
  filterTableByCodeType();
}

function sortItemsInList() {
  const $brandP = $('.products').first();
  const $atvCats = $brandP.find('> .custom_atv_name');
  if ($atvCats.length > 0) {
    $atvCats.each(function () {
      const $atvCat = $(this);
      const atv = $(this).data('atv');
      const $articles = $brandP.find('> article[data-atv="' + atv + '"]');
      $articles
        .find('span.price')
        .sort(sorting)
        .reverse()
        .map(function () {
          return $(this).parents('article').first();
        });
    });
  } else {
    var $articles = $brandP.find('> article');
    $articles
      .find('span.price')
      .sort(sorting)
      .map(function () {
        return $(this).parents('article').first();
      });
  }
  filterTableByCodeType();
}
//
$('body').off('change', '#search_filters input[data-search-url]');
$('body').on('change', '#search_filters input[data-search-url]', function (event) {
  filterTableByCodeType();
});
$('body').off('click', '#search_filters a.js-search-link');
$('body').on('click', '#search_filters a.js-search-link', function (event) {
  $(this).parent().find('input[data-search-url]').click();
});
$('#search_filters .sidebar-bottom a.js-search-link').attr('href', 'javascript:;');
$('.facet--27 input').click();
$('#category.category-id-17 li[data-label="'+encodeURIComponent('F/R')+'"]').hide();
var visible_facets = ['F', 'R'];
$('#category.category-id-17 .facet--35 li').each(function(){
  if (visible_facets.indexOf($(this).data('label')) == -1) {
    $(this).hide();
  }
})
// $('#quantity_wanted').off('change');
// if ($('#category.category-id-21').length) {
//   $('.show_grid').click();
// } else {
//   $('.show_list').click();
// }
// if ($('#category.category-id-21').length) {
//   $('#search_filters').addClass('auto');
// } else {
//   $('#search_filters .sidebar-auto').remove();
//   $('#search_filters_params').addClass('active');
// }

// $('#search_filters h4 > span').on('click', function(){
//   var span = $(this);
//   var active = span.hasClass('active');
//   $('#search_filters h4 > span').removeClass('active');
//   span.addClass('active');
//   if ($('#search_filters h4 > span:nth-of-type(1)').hasClass('active')) {
//     $('#search_filters').addClass('auto').removeClass('params');
//   } else {
//     $('#search_filters').removeClass('auto').addClass('params');
//   }
//
//   var collapsed = $('#search_filters').hasClass('collapsed');
//   $('#search_filters').removeClass('collapsed');
//   if ($('#search_filters').hasClass('mopen')) {
//     $('#search_filters').removeClass('mopen').addClass('mcollapsed')
//   } else if (collapsed) {
//     $('#search_filters').removeClass('mcollapsed').addClass('mopen');
//   } else if ($('#search_filters').hasClass('mcollapsed')) {
//     if ($(window).scrollTop() >= 330) {
//       $('#search_filters').removeClass('mcollapsed').addClass('mopen');
//     } else {
//       $('#search_filters').removeClass('mcollapsed');
//     }
//   } else {
//     $('#search_filters').addClass('mcollapsed');
//   }
//
//   var h = $('#search_filters .can-collapse').css('height');
//   $('#search_filters .can-collapse').css('height', 'unset');
//   var hh = $('#search_filters .can-collapse').height();
//   $('#search_filters .can-collapse').css('height', h);
//
//   if (active || $('#search_filters').hasClass('collapsed')) {
//     span.css('pointer-events','none');
//     var collapsed = $('#search_filters').hasClass('collapsed');
//     if (collapsed) {
//       $('#search_filters .can-collapse').show();
//     }
//     $('#search_filters .can-collapse').animate({
//       height: (collapsed  ? ($('#search_filters').hasClass('auto') ? hh+'px' : '300px') : '0px' )
//     }, 500, function() {
//       $('#search_filters').toggleClass('collapsed');
//       span.css('pointer-events','initial');
//     });
//   } else {
//     $('#search_filters .can-collapse').css('height', ($('#search_filters').hasClass('auto') ? hh+'px' : '300px'));
//   if ($('#search_filters').hasClass('auto')) {
//     var filters = allVal.indexOf($('#facet_auto-make .select-title span').text().replace(/^\s+|\s+$/g,'')) != -1 || allVal.indexOf($('#facet_auto-model .select-title span').text().replace(/^\s+|\s+$/g,'')) != -1
//       ? null : car_cat[$('#facet_auto-make .select-title span').text().replace(/^\s+|\s+$/g,'')][$('#facet_auto-model .select-title span').text().replace(/^\s+|\s+$/g,'')];
//     if (filters && allVal.indexOf($('#facet_auto-dia .select-title > span').text().replace(/^\s+|\s+$/g,'')) != 1) {
//       filters['dia'] = [];
//       filters['dia'].push($('#facet_auto-dia .select-title > span').text().replace(/^\s+|\s+$/g,''));
//     }
//     filterTableByCar(filters);
//   } else {
//     var filters = [];
//     filters['dia'] = []; filters['dia'].push($('#search_filters .facet--32 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//     filters['lug'] = []; filters['lug'].push($('#search_filters .facet--29 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//     filters['stud'] = []; filters['stud'].push($('#search_filters .facet--30 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//     filters['offset'] = []; filters['offset'].push($('#search_filters .facet--28 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//     filterTableByCar(filters);
//   }
// });
// if ($('#search_filters').length) {
//   setTimeout(function(){
//     $(window).off('scroll');
//     $(window).on('scroll', function() {
//       if ($(window).scrollTop() >= 330) {
//         if (!$('#search_filters').hasClass('collapsed') && ($('#products').hasClass('fixed') || $('#search_filters').height() > $(window).height())) {
//           if (!$('#search_filters').hasClass('mopen') && !$('#search_filters').hasClass('mcollapsed')) {
//             $('#search_filters').addClass('collapsed');
//           }
//           $('#search_filters_wrapper').addClass('fixed');
//           //console.log(1);
//         } else if ($('#js-product-list').height() > $('#search_filters').height()) {
//           //console.log($('#search_filters .wrap:nth-child(2)').height() + 100, $(window).height());
//           $('#search_filters_wrapper').addClass('fixed');
//         }
//         //$('#search_filters .can-collapse').css('height','0px').hide();
//       } else {
//         $('#search_filters').removeClass('collapsed').removeClass('collapsed').removeClass('mopen');
//         $('#search_filters_wrapper').removeClass('fixed');
//         //$('#search_filters .can-collapse').css('height','auto').show();
//       }
//     });
//   }, 500);
// }
// if (typeof car_cat != 'undefined') {
//   $('.can-collapse > span').on('click',function(){
//     if ($('#search_filters').hasClass('auto')) {
//       if (allVal.indexOf($('#facet_auto-make .select-title span').text().replace(/^\s+|\s+$/g,'')) != -1 || allVal.indexOf($('#facet_auto-model .select-title span').text().replace(/^\s+|\s+$/g,'')) != -1) {
//         filterTableByCar();
//       } else {
//         filterTableByCar(car_cat[$('#facet_auto-make .select-title span').text().replace(/^\s+|\s+$/g,'')][$('#facet_auto-model .select-title span').text().replace(/^\s+|\s+$/g,'')]);
//       }
//     } else {
//       var filters = [];
//       filters['dia'] = []; filters['dia'].push($('#search_filters .facet--32 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//       filters['lug'] = []; filters['lug'].push($('#search_filters .facet--29 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//       filters['stud'] = []; filters['stud'].push($('#search_filters .facet--30 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//       filters['offset'] = []; filters['offset'].push($('#search_filters .facet--28 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//       filterTableByCar(filters);
//     }
//   })
//   $.each(car_cat,function(make,make_data){
//     $('<a rel="nofollow" href="javascript:;" class="select-list">'+make+'</a>').on('click', function(){
//       $('#facet_auto-model').removeClass('select-make');
//       selectElement($('#facet_all_val').val(), '#facet_auto-model');
//       $('#search_filters .can-collapse').css('height','unset');
//       var text = $(this).text().replace(/^\s+|\s+$/g,'');selectElement(text, '#facet_auto-make');
//       filterTableByCar();
//       $('#facet_auto-model .select-list:not(.all)').each(function(){
//         $(this).show();
//         if ($(this).data('make') != text){
//           $(this).hide();
//         }
//       })
//     }).appendTo($('#facet_auto-make .facet-dropdown .dropdown-menu'));
//
//     $.each(make_data,function(model,rec){
//       $('<a rel="nofollow" href="javascript:;" class="select-list" data-make="'+make+'">'+model+'</a>').on('click', function(){
//         var text = $(this).text().replace(/^\s+|\s+$/g,'');
//         selectElement(text, '#facet_auto-model');
//         $('#search_filters .can-collapse').css('height','unset');
//         $('#facet_auto-dia').removeClass('select-make');
//         $('#facet_auto-dia .select-list:not(.all)').each(function(){
//           $(this).show();
//           //console.log(car_cat[make][model]['dia'], $(this).text());
//           if (car_cat[make][model]['dia'].indexOf($(this).text()) == -1){
//             $(this).hide();
//           }
//         })
//         filterTableByCar(car_cat[make][model]);
//       }).appendTo($('#facet_auto-model .facet-dropdown .dropdown-menu'));
//     });
//   });
//   $('#facet_auto-make .select-list.all').on('click', function(){
//     selectElement($('#facet_all_val').val(), '#facet_auto-make');
//     $('#facet_auto-model').addClass('select-make');
//     $('#facet_auto-dia').addClass('select-make');
//     filterTableByCar();
//   });
//   $('#facet_auto-model .select-list.all').on('click', function(){
//     selectElement($('#facet_all_val').val(), '#facet_auto-model');
//     $('#facet_auto-dia').addClass('select-make');
//     filterTableByCar();
//   });
//   $('#facet_auto-dia .select-list').on('click', function(){
//     selectElement($(this).html(), '#facet_auto-dia');
//     var filters = [];
//     filters['dia'] = [];
//     if (allVal.indexOf($(this).text().replace(/^\s+|\s+$/g,'')) != -1) {
//       filters['dia'] = car_cat[$('#facet_auto-make .select-title > span').text().replace(/^\s+|\s+$/g,'')][$('#facet_auto-model .select-title > span').text().replace(/^\s+|\s+$/g,'')]['dia'];
//     } else {
//       filters['dia'].push($(this).text().replace(/^\s+|\s+$/g,''));
//     }
//     filters['lug'] = []; filters['lug'] = car_cat[$('#facet_auto-make .select-title > span').text().replace(/^\s+|\s+$/g,'')][$('#facet_auto-model .select-title > span').text().replace(/^\s+|\s+$/g,'')]['lug'];
//     filters['stud'] = []; filters['stud'] = car_cat[$('#facet_auto-make .select-title > span').text().replace(/^\s+|\s+$/g,'')][$('#facet_auto-model .select-title > span').text().replace(/^\s+|\s+$/g,'')]['stud'];
//     filters['offset'] = []; filters['offset'] = car_cat[$('#facet_auto-make .select-title > span').text().replace(/^\s+|\s+$/g,'')][$('#facet_auto-model .select-title > span').text().replace(/^\s+|\s+$/g,'')]['offset'];
//     filterTableByCar(filters);
//   });
// }
//
$.UrlExists = function(url) {
  var http = new XMLHttpRequest();
  http.open('HEAD', url, false);
  http.send();
  return http.status!=404;
}
// OLD TABLE SHOPPING BUTTON MODAL FUNCTION

$('.tire-table-row, .tire-image-card').each(function(key, value) {
  products.push($(value).children().children().last().children().first().val());
  $(value).find('.grid-cart-btn').on('click', function() {
    if (!user) {
      const tire_id = $(this).data('info');

      $.ajax({
        url: '/' + pathParts[1] + '/ajax',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        method: 'POST',
        data: { tire_id: tire_id },
        success: function(data)
        {
          data = JSON.parse(data);
          let cart_quantity = data.quantity;
          cart_quantity = parseInt(cart_quantity);
          let total_sum = data.total_sum;
          total_sum = parseInt(total_sum);
          const image = data.cart.options.image;
          if (typeof image !== "undefined") {
            if (data.cart.options.tire.make_id){
              fetch(public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg',
                { method: 'GET' },)
                .then(res => {
                  if (res.ok) {
                    $('.modal-image-preview img').attr('src', public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg');
                  } else {
                    $('.modal-image-preview img').attr('src', 'https://www.r1-dev.area.lv/public/img/p/en-default-home_default.jpg');
                  }
                });
            }
          }
          // TIRE IMAGE INSIDE MODAL

          $('.modal-product-info .product-name').html(data.cart.name);
          $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-price').attr('data-price', parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
          $('.modal-product-info .product-height').html(data.cart.options.tire.d2);
          $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-type').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-li').html(data.cart.options.tire.li);
          $('.modal-product-info .product-si').html(data.cart.options.tire.si);
          $('.cart-content .cart-products-total').html(total_sum);
          $('.modal-product-info .product-qty').attr('data-qty', parseInt(data.quantity)).html($('.modal-product-info .product-qty').attr('data-qty'));
          $('span.cart-products-count').html('(' + cart_quantity + ')');
          $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
          $('.blockcart.cart-preview .header').empty();
          $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
        }
      });
    } else {
      const tire_data = $(this).parent().parent().parent();
      let product = $('.product-main-details h1').text();
      $('.popup input[name=qty]').val(4);
      $('.popup input[name=total]').val(parseInt($('.popup input[name=price]').val()) * parseInt($('.popup input[name=qty]').val()));
      $('.popup input[name=user]').val(user).attr('readonly', true).prop('readonly', true);
      $('.popup input[name=article]').val($('.tire-info', tire_data).data('article'));
      $('.popup input[name=prod]').val($('.tire-info', tire_data).data('content'));
      $('.popup input[name=price]').val($('#sale-price', tire_data).html().trim().replace('€ ', ''));

      calcData = {
        'article': tire_data.find('th').data('article'),
        'qty': 4,
        'user': user,
        'prod': $('.product-main-details h1').text(),
        'price': tire_data.find('.tire-price-red').text().trim().replace('€', ''),
      }

      const urlData = new URLSearchParams(calcData).toString();

      popCalc('/testing3',780,400, urlData);
    }
  });

});


$('.tire-table-row, .tire-image-card').each(function(key, value) {
  products.push($(value).children().children().last().children().first().val());
  $(value).find('.grid-buy-btn').on('click', function() {
    if (!user) {
      const tire_id = $(this).data('info');

      $.ajax({
        url: '/' + pathParts[1] + '/ajax',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        method: 'POST',
        data: { tire_id: tire_id, quantity: 4 },
        success: function(data)
        {
          data = JSON.parse(data);
          let cart_quantity = data.quantity;
          cart_quantity = parseInt(cart_quantity);
          let total_sum = data.total_sum;
          total_sum = parseInt(total_sum);
          const image = data.cart.options.image;
          if (typeof image !== "undefined") {
            if (data.cart.options.tire.make_id){
              fetch(public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg',
                  { method: 'GET' },)
                  .then(res => {
                    if (res.ok) {
                      $('.modal-image-preview img').attr('src', public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg');
                    } else {
                      $('.modal-image-preview img').attr('src', 'https://www.r1-dev.area.lv/public/img/p/en-default-home_default.jpg');
                    }
                  });
            }
          }
          // TIRE IMAGE INSIDE MODAL
          $('.modal-product-info .product-name').html(data.cart.name);
          $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-price').attr('data-price', parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
          $('.modal-product-info .product-height').html(data.cart.options.tire.d2);
          $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-type').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-li').html(data.cart.options.tire.li);
          $('.modal-product-info .product-si').html(data.cart.options.tire.si);
          $('.cart-content .cart-products-total').html(total_sum);
          $('.modal-product-info .product-qty').attr('data-qty', parseInt(data.quantity)).html($('.modal-product-info .product-qty').attr('data-qty'));
          $('span.cart-products-count').html('(' + cart_quantity + ')');
          $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
          $('.blockcart.cart-preview .header').empty();
          $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
        }
      });
    } else {
      const tire_data = $(this).parent().parent().parent();
      // console.log(tire_data);
      // console.log(tire_data.find('.card-title-text').text().trim());
      $('.popup input[name=prod]').val(tire_data.find('.card-title-text').text().trim());
      $('.popup input[name=price]').val(tire_data.find('.rim-price-red').text().trim().replace('€', ''));

      // $('.popup input[name=prod]').val($('.tire-info', tire_data).data('content'));
      // $('.popup input[name=price]').val($('#sale-price', tire_data).html().trim().replace('€ ', ''));
      $('.popup input[name=qty]').val(4);
      $('.popup input[name=total]').val(parseInt($('.popup input[name=price]').val()) * parseInt($('.popup input[name=qty]').val()));
      $('.popup input[name=user]').val(user).attr('readonly', true).prop('readonly', true);
      $('.popup input[name=article]').val($('.tire-info', tire_data).data('article'));
      // console.log($('.tire-info', tire_data).data('article'));
      // console.log('data-article: ', $('.tire-table-link').data('article'));

      calcData = {
        'article': tire_data.parent().parent().data('article'),
        'qty': 4,
        'user': user,
        'prod': tire_data.find('.card-title-text').text().trim(),
        'price': tire_data.find('.rim-price-red').text().trim().replace('€', ''),
      }

      const urlData = new URLSearchParams(calcData).toString();

      popCalc('/testing3',780,400, urlData);
    }
  });

});

$('#quantity_wanted').on('input', function() {
  let qty = $(this).val();
  if (qty <= 0) {
    return false;
  }

  $(this).attr('value', qty);
  tire_qty = qty;
});
//
$('.bootstrap-touchspin-up').on('click', function() {
  if (!$('#quantity_wanted').val()) {
    $('#quantity_wanted').val(0);
  }
  $('#quantity_wanted').val(parseInt($('#quantity_wanted').val()) + 1);
  $('#quantity_wanted').attr('value', parseInt($('#quantity_wanted').val()));
  tire_qty = parseInt($('#quantity_wanted').val());
});

$('.bootstrap-touchspin-down').on('click', function() {
  if (!$('#quantity_wanted').val()) {
    $('#quantity_wanted').val(2);
  }
  if ($('#quantity_wanted').val() <= 1) {
    return false;
  }
  $('#quantity_wanted').val(parseInt($('#quantity_wanted').val()) - 1);
  $('#quantity_wanted').attr('value', parseInt($('#quantity_wanted').val()));
  tire_qty = parseInt($('#quantity_wanted').val());
});

//
function ajaxChangeQty(tire_id, qty, price) {
  $.ajax({
    url: '/cart/ajaxChangeQty',
    method: 'POST',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: { tire_id: tire_id, qty: qty },
    success: function(data)
    {
      data = JSON.parse(data);
      $('.cart-products-count').html('(' + data.total_items + ')');
      $('#cart-subtotal-products .value').html('€ ' + data.total_sum.replace('.00', ''));
      $('#cart-subtotal-products .js-subtotal').html(data.total_items + ' Preces');
      // console.log($('#cart-subtotal-products .js-subtotal').text().replace(' Preces', ''));
      $('.cart-total .value').html('€ ' + data.total_sum.replace('.00', ''));
      $('.product-price[data-product-id=' + tire_id + ']').html('<strong>€ ' + (parseInt(price) * parseInt(qty)) + '</strong>');
      $('.js-cart-line-product-quantity[data-product-id=' + tire_id + ']').val(parseInt(qty));
      $('.cart-montage-choice .cart-delivery-options .cart-delivery-label').first().children('input').val(data.total_items);
      $('.cart-montage-choice .cart-delivery-options .cart-delivery-label').first().children('span').html(data.total_items + ' Riepām');
    }
  });
}
//
$('.js-cart-line-product-quantity').each(function(key, value) {
  $(value).on('input', function() {
    let item_id = $(this).data('product-id');
    let qty = $(this).val();
    if (qty <= 0) qty = 1;
    let price = $(this).data('item-price');
    $('#cart-subtotal-products .js-subtotal').html(qty + ' Preces');

    ajaxChangeQty(item_id, qty, price);
    let __total = parseInt($('#cart-subtotal-products .js-subtotal').html().trim().replace(' Preces', ''));
    if ($('.cart-delivery-option').is(':visible')) {
      checkShipping(__total);
    }
    if ($('.cart-montage-choice').is(':visible')) {
      checkFitting(__total);
    }
  })

  $(value).parent().children().last().children().first().on('click', function() {
    let item = $(this).parent().siblings('.js-cart-line-product-quantity');

    let item_id = item.data('product-id');
    let qty = parseInt(item.val()) + 1;
    let price = item.data('item-price');
    $('#cart-subtotal-products .js-subtotal').html(qty + ' Preces');

    ajaxChangeQty(item_id, qty, price);
    let __total = parseInt($('#cart-subtotal-products .js-subtotal').html().trim().replace(' Preces', ''));
    if ($('.cart-delivery-option').is(':visible')) {
      checkShipping(__total);
    }
    if ($('.cart-montage-choice').is(':visible')) {
      checkFitting(__total);
    }
  });

  $(value).parent().children().last().children().last().on('click', function() {
    let item = $(this).parent().siblings('.js-cart-line-product-quantity');

    let item_id = item.data('product-id');
    let qty = parseInt(item.val()) - 1;
    if (qty <= 0) qty = 1;
    let price = item.data('item-price');
    $('#cart-subtotal-products .js-subtotal').html(qty + ' Preces');

    ajaxChangeQty(item_id, qty, price);
    let __total = parseInt($('#cart-subtotal-products .js-subtotal').html().trim().replace(' Preces', ''));
    if ($('.cart-delivery-option').is(':visible')) {
      checkShipping(__total);
    }
    if ($('.cart-montage-choice').is(':visible')) {
      checkFitting(__total);
    }
  });
});
//
if (!admin) {
  $('.add-to-cart').on('click', function() {
    let tire_id = $(this).data('info');
    let quantity = $('#quantity_wanted').val();
    let tire_price = parseInt($('.current-price').children().last().attr('content')) * parseInt(quantity);
    let cart_count = $('.cart-products-count').html().replace('(', '').replace(')', '');
    $('#blockcart-modal span.cart-products-count').html('(' + (parseInt(cart_count) + parseInt(quantity)) + ')');

    activeCart();

    $.ajax({
      url: '/' + pathParts[1] + '/ajax',
      method: 'POST',
      data: { tire_id: tire_id, quantity: quantity },
      success: function(data)
      {
        data = JSON.parse(data);
        let $quantity = data.quantity;
        $quantity = parseInt($quantity);
        let total_sum = data.total_sum;
        total_sum = parseInt(total_sum);
        const image = data.cart.options.image;
        if (typeof image !== "undefined") {
          if (data.cart.options.tire.make_id){
            fetch(public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg',
              { method: 'GET' },)
              .then(res => {
                if (res.ok) {
                  $('.modal-image-preview img').attr('src', public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg');
                } else {
                  $('.modal-image-preview img').attr('src', 'https://www.r1-dev.area.lv/public/img/p/en-default-home_default.jpg');
                }
              });
          }
        }

        // TIRE IMAGE INSIDE SPECIFIC TIRE MODAL
        $('.modal-product-info .product-name').html(data.cart.name);
        $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2));
        $('.modal-product-info .product-price').attr('data-price', parseInt(data.cart.options.tire.price2));
        $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
        $('.modal-product-info .product-height').html(data.cart.options.tire.d2);
        $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
        $('.modal-product-info .product-type').html(data.cart.options.tire.d3);
        $('.modal-product-info .product-li').html(data.cart.options.tire.li);
        $('.modal-product-info .product-si').html(data.cart.options.tire.si);
        $('.cart-content .cart-products-total').html(total_sum);
        $('.modal-product-info .product-qty').attr('data-qty', parseInt(data.quantity)).html($('.modal-product-info .product-qty').attr('data-qty'));
        $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
        $('.blockcart.cart-preview .header').empty();
        $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + $quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
        $('.shopping-cart .cart-products-count').html('(' + (parseInt(cart_count) + parseInt(quantity)) + ')');
        $('span.cart-products-count').html('(' + (parseInt(cart_count) + parseInt(quantity)) + ')');
      }
    });

  });
} else {
  $('.add-to-cart').on('click', function() {
    const tire_price = $('.product-prices').find('.has-discount span[itemprop=price]').attr('content');
    const tire_title = $('.tire_title').val();
    const tire_article = $('.tire_article').val();
    $('.popup input[name=qty]').val($('.product-quantity .qty .input-group input[name=qty]').attr('value'));
    $('.popup input[name=total]').val(parseInt(tire_price) * $('.popup input[name=qty]').val());
    $('.popup input[name=prod]').val(tire_title);
    $('.popup input[name=price]').val(parseInt($('.current-price').children().last().attr('content')));
    $('.popup input[name=user]').val(user).attr('readonly', true).prop('readonly', true);
    $('.popup input[name=article]').val(tire_article);

    calcData = {
          'article': tire_article,
          'qty': $('.product-quantity .qty .input-group input[name=qty]').attr('value'),
          'user': user,
          'prod': tire_title,
          'price': tire_price,
      }

      const urlData = new URLSearchParams(calcData).toString();

      popCalc('/testing3',780,400, urlData);


})
}

$('.ct_matrix_row').each(function(key, value) {
  $(value).find('.ct_submit').on('click', function() {

    if (!admin) {
      const tire_id = $(this).data('info');
      let quantity = $('#ct_matrix_' + tire_id + '_idQty').val();
      let tire_price = parseInt($(value).children('.ctd_price').last().children().html().substring(2)) * parseInt(quantity);
      let cart_count = $('.cart-products-count').html().replace('(', '').replace(')', '');
      $('#blockcart-modal span.cart-products-count').html('(' + (parseInt(cart_count) + parseInt(quantity)) + ')');

      activeCart();

      $.ajax({
        url: '/' + pathParts[1] + '/ajax',
        method: 'POST',
        data: {tire_id: tire_id},
        success: function (data) {
          data = JSON.parse(data);
          let $quantity = data.quantity;
          $quantity = parseInt($quantity);
          let total_sum = data.total_sum;
          total_sum = parseInt(total_sum);
          const image = data.cart.options.tire.tread.image;
          // if (typeof image !== "undefined") {
          //   if (data.cart.options.tire.image){
          //     $('.modal-image-preview img').attr('src', '/storage/app/public/' + data.cart.options.image + '/tread/' + data.cart.options.tire.tread.image + '.png');
          //   } else {
          //     $('.modal-image-preview img').attr('src', 'https://www.r1-dev.area.lv/public/img/p/en-default-home_default.jpg');
          //   }
          // }
          $('.modal-product-info .product-name').html(data.cart.options.tire.title.toUpperCase());
          $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-price').attr('data-price', parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
          $('.modal-product-info .product-height').html(data.cart.options.tire.d2);
          $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-type').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-li').html(data.cart.options.tire.li);
          $('.modal-product-info .product-si').html(data.cart.options.tire.si);
          $('.cart-content .cart-products-total').html(total_sum);
          $('.modal-product-info .product-qty').attr('data-qty', parseInt(data.quantity));
          $('.modal-product-info .product-qty').html($('.modal-product-info .product-qty').attr('data-qty'));
          $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
          $('.blockcart.cart-preview .header').empty();
          $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + $quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
          $('.shopping-cart .cart-products-count').html('(' + (parseInt(cart_count) + parseInt(quantity)) + ')');
          $('span.cart-products-count').html('(' + (parseInt(cart_count) + parseInt(quantity)) + ')');
        }
      });
    } else {
      const $this = $(this).parent().parent();
      const tire_price = $this.find('td[data-label="Akcijas cena"] .strongprice').attr('data-price');
      const tire_title = $('.tire_title').val();
      const tire_article = $(this).data('article');
      $('.popup input[name=qty]').val(4);
      $('.popup input[name=total]').val(parseInt(tire_price) * $('.popup input[name=qty]').val());
      $('.popup input[name=prod]').val(tire_title);
      $('.popup input[name=price]').val(tire_price);
      $('.popup input[name=user]').val(user).attr('readonly', true).prop('readonly', true);
      $('.popup input[name=article]').val(tire_article);

      calcData = {
          'article': tire_article,
          'qty': 4,
          'user': user,
          'prod': tire_title,
          'price': tire_price,
      }

      const urlData = new URLSearchParams(calcData).toString();

      popCalc('/testing3',780,400, urlData);


     }
  });
});
//

$('#blockcart-modal .cart-content-btn button').on('click', function() {
  $('#blockcart-modal').slideToggle();
});

$('.popup-close').on('click', function(e) {
  e.preventDefault();
  $('#quick-popup').trigger('click');
});

$('#blockcart-modal .modal-header button').on('click', function() {
  $('#blockcart-modal').slideToggle();
});

// });
// ///RDP///
if( $('#search-form input[name="s"]').val() === '') {$("#search-form").hide();}

function calcQuickBuyPrice(){
  var total = parseFloat($('#quick-buy-form input[name=qty]').val()) * parseFloat($('#quick-buy-form input[name=price]').val());
  $('#quick-buy-form input[name=total]').val(isNaN(total) ? '' : total);
}

function getFormData(form){
  var paramObj = {};
  $.each(form.serializeArray(), function(_, kv) {
    //console.log(kv);
    if (paramObj.hasOwnProperty(kv.name)) {
      paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
      paramObj[kv.name].push(kv.value);
    }
    else {
      paramObj[kv.name] = kv.value;
    }
  });
  return paramObj;
}

function sendData(data){
  $('#quick-buy-form').parent().addClass('busy');
  $.ajax({
    type: 'POST',
    url: '/accrualOrder',
    data: {info: data},
    timeout: 10000,
    success: function(resp){
      $('#quick-buy-form').parent().find('.popup-close').click();
      $('.popup input[name=montage]').prop('checked', false);
      $('.popup input[name=price_montage]').attr('disabled', 'disabled');
      $('.popup input[name=price_montage]').val('');
      $('.popup input[name=safe]').prop('checked', false);
      $('.popup input[name=price_safe]').attr('disabled', 'disabled');
      $('.popup input[name=price_safe]').val('');
      $('.popup textarea[name=comments]').val('');
      Swal.fire({
        title: 'Paziņojums',
        text: 'Pasūtījums ir pieņemts!',
        icon: 'success',
        confirmButtonText: 'OK'
      });
      //$.ajax({
      //  type: 'GET',
      //  url: '/sync/accrual',
      //
      //})
    },
    error: function(jqXHR, textStatus){
      if (textStatus === 'timeout') {
        Swal.fire({
          title: 'Kļūda!',
          text: 'Pasūtījums nav pieņemts!',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        // toastr.error('Pastūtījums nav pieņemts!', 'Kļūda');
      }
    },
    complete: function(){
      $('#quick-buy-form').parent().removeClass('busy');
    }
  })
}

let montage = '';
let safe = '';

function toggleMontage(){
  if ($('#montage').prop('checked')) {
    $('#quick-buy-form input[name=price_montage]').removeAttr('disabled').focus();
  } else {
    $('#quick-buy-form input[name=price_montage]').attr('disabled','disabled').val('');
  }
  addMontagePrice();
}

function addMontagePrice(){
  if (!isNaN(parseFloat($('#quick-buy-form input[name=price]').val())) && !isNaN(parseFloat($('#quick-buy-form input[name=qty]').val()))) {
    montage = isNaN(parseFloat($('#quick-buy-form input[name=price_montage]').val())) ? 0 : parseFloat($('#quick-buy-form input[name=price_montage]').val());
    $('#quick-buy-form input[name=total]').val(montage+parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    if (montage > 0 && safe > 0) {
      $('#quick-buy-form input[name=total]').val(montage + safe + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    } else if (montage === 0 && safe > 0) {
      $('#quick-buy-form input[name=total]').val(safe + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    }
  }
}

function toggleSafe(){
  if ($('#safe').prop('checked')) {
    $('#quick-buy-form input[name=price_safe]').removeAttr('disabled').focus();
  } else {
    $('#quick-buy-form input[name=price_safe]').attr('disabled','disabled').val('');
  }
  addSafePrice();
}

function addSafePrice(){
  if (!isNaN(parseFloat($('#quick-buy-form input[name=price]').val())) && !isNaN(parseFloat($('#quick-buy-form input[name=qty]').val()))) {
    safe = isNaN(parseFloat($('#quick-buy-form input[name=price_safe]').val())) ? 0 : parseFloat($('#quick-buy-form input[name=price_safe]').val());
    $('#quick-buy-form input[name=total]').val(safe+parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    if (safe > 0 && montage > 0) {
      $('#quick-buy-form input[name=total]').val(safe + montage + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    } else if (safe === 0 && montage > 0) {
      $('#quick-buy-form input[name=total]').val(montage + parseFloat($('#quick-buy-form input[name=qty]').val())*parseFloat($('#quick-buy-form input[name=price]').val()));
    }
  }
}

function showQuickBuyForm(id) {
  $('#quick-buy-form input[name=prod_id]').val(id);
  $('#quick-buy-form input[name=price]').val($('#js-product-list article[data-id-product-attribute="'+id+'"]').find('.price').text().substr(2));
  $('#quick-buy-form input[name=prod]').val($('#js-product-list article[data-id-product-attribute="'+id+'"]').find('.product-title-hidden').text());
  calcQuickBuyPrice();
};

$(document).ready(function() {

  let $ids = [];

  $('.tire-table-row, .tire-image-card').each(function() {
    $(this).find('input[type=checkbox]').on('click', function() {
      let $id = $(this).val();
      let $product = $('input[type=checkbox][name="product_ids[]"][value="' + $id + '"]');
      $product.attr('checked', this.checked).prop('checked', this.checked);
      if ($(this).is(':checked')) {
        $product.closest('.tire-table-row').addClass('selected');
        $product.closest('.tire-image-card').addClass('selected');
      } else {
        $product.closest('.tire-table-row').removeClass('selected');
        $product.closest('.tire-image-card').removeClass('selected');
      }
      // $(this).closest('.tire-table-row').toggleClass('selected');
      $ids = $(document).find('input[type=checkbox][name="product_ids[]"]:checked').map(function() {
        return $(this).val();
      }).toArray();
      $ids = $ids.filter(function(item, i, ids) {
        return i == ids.indexOf(item);
      });
      $.each($ids, function(key, value) {
      });
      $ids = $ids.join(',');
      const baseUrl = window.location.href.split('#')[0];
      if ($ids.length) {
        window.location.replace(baseUrl + '#|' + $ids);
        $('input#show-selected-checkbox').prop( "disabled", false );
      } else {
        $('input#show-selected-checkbox').prop( "disabled", true );
        let uri = window.location.toString();

        if (uri.indexOf("#") > 0) {
          let clean_uri = uri.substring(0,
            uri.indexOf("#"));

          window.history.replaceState({},
            document.title, clean_uri);
        }
      }
    });
  });

  let $hash = window.location.hash;

  if ($hash) {
    $hash = $hash.substring(2).split(',');
    $.each($hash, function(key, value) {
      let $product = $('input[type=checkbox][name="product_ids[]"][value="' + value + '"]');
      $(document).find($product).attr('checked', true).prop('checked', true);
      $(document).find($product).closest('.tire-table-row').addClass('selected')
      $(document).find($product).closest('.tire-image-card').addClass('selected');
    });
  }

});

// $(document).on('change', 'input[type="checkbox"][name="product_ids[]"]', function(){
//
//   let ids_str = '';
//   $('.tire-table-row').removeClass('selected');
//   if ($ids.length) {
//     $ids.each(function() {
//       $(this).parents('.tire-table-row').addClass('selected');
//     });
//     ids_str = $.map($ids, function(id) {
//       return $(id).val();
//     }).join(',');
//   }
//   const baseUrl = window.location.href.split('#')[0];
//   window.location.replace(baseUrl + '#|' + ids_str);
// });
// $(document).on('change', 'input[type="checkbox"][name="product_ids2[]"]', function(){
//   const $ids = $(document).find('input[type="checkbox"][name="product_ids2[]"]:checked');
//   let ids_str = '';
//   $('#ct_matrix tr').removeClass('selected');
//   if ($ids.length) {
//     $ids.each(function() {
//       $(this).parents('#ct_matrix tr').addClass('selected');
//     });
//     ids_str = $.map($ids, function(id) {
//       return $(id).val();
//     }).join(',');
//   }
//   const baseUrl = window.location.href.split('#')[0];
//   window.location.replace(baseUrl + '#|' + ids_str);
// });
// if (window.location.hash !== '' && window.location.hash.length > 2 && window.location.hash.indexOf('#|') === 0) {
//   const ids = window.location.hash.replace('#|', '').split(',');
//   $(document).find('input[type="checkbox"][name="product_ids[]"]').each(function() {
//     if (ids.indexOf($(this).val()) !== -1) {
//       $(this).prop('checked', true).trigger('change');
//     }
//   });
//   // $(document).find('input[type="checkbox"][name="product_ids2[]"]').each(function() {
//   //   if (ids.indexOf($(this).val()) !== -1) {
//   //     $(this).prop('checked', true).trigger('change');
//   //   }
//   // });
// }
$('#top-menu > li > a').each(function() {
  if($(this).attr("data-depth") === "0") {
    $(this).attr("href", "#");
  }
});

$('.menusearch-icon').on('click', function(){
  if($('#search-form form input[name="s"]').val() !== '' && $('#search-form').css('display') === 'block')  {
    $('#search-form form').submit();
  } else {
    $("#search-form").toggle();
  }
});

$('#_mobile_top_menu').on('click','.dropdown-item', function(event){
  if($(event.target).hasClass('dropdown-item')) {
    $(this).find('span[data-toggle="collapse"]').click();
  }

});
//
/* Filter and code input */
$(document).ajaxStop(function() {
  renderInput();
  if(document.cookie.indexOf('show_list=true') === -1 && $('body').attr('id') !== 'search') {
    sortItemsInBrand();
    $('.custom_brand_name').removeClass('product_list_view');
  } else {
    sortItemsInList();
  }
});
function inputListener(event) {
  event.preventDefault();
  var code = $(this).val();
  var arr = validateCode(code);
  if(!arr) return;

  /*switch(arr.length) {
      case 2:
      case 3:
      case 4:
          selectElement(arr[1]);
      case 3:
      case 4:
          selectElement(arr[2]);
      case 4:
          selectElement(arr[3]);
  }*/
  if (arr[1]) {
    selectElement(arr[1], '#search_filters .facet-ind-1');
  }
  if (arr[2]) {
    selectElement(arr[2], '#search_filters .facet-ind-2');
  }
  if (arr[3]) {
    selectElement(arr[3], '#search_filters .facet-ind-3');
  }
}
function renderInput() {
  /*if($('#search_filters').length > 0 && $('#search_filters input').length === 0) {*/
  if($('#search_filters').length > 0 && $('#search_filters #autofind_atr').length === 0) {
    $($('#facet-template').html()).insertAfter('#search_filters .facet--3');
    $($('#facet-template').html()).insertAfter('#search_filters .facet--12');
    $($('#facet-template').html()).insertAfter('#search_filters .facet--24');
    $('#search_filters').on('input', 'input', inputListener);
    $('#search_filters').on('keydown', 'input', function(event) {
      charCode = event.keyCode || event.which;
      if (charCode != 190 && charCode != 110 && charCode != 46 && charCode > 40 && (charCode < 48 || charCode > 57) && (charCode < 96 || charCode > 105))
        return false;
      // if (event.keyCode === 13) {
      //   $('#autofind_sub').click();
      // }
    });
  }
  if(document.cookie.indexOf('show_list=true') !== -1 && $('body').attr('id') !== 'search') {
    $('.table-top').addClass('product_show_list');
    $('.custom_atv_name').addClass('product_show_list');
    $('.show_list').addClass('active');
  }
  if($('body').attr('id') === 'search') {
    $('.product_show_list').removeClass('product_show_list');
  }
}
//
function filterTableByCar(filters){
  console.log(filters);
  $('article.product-miniature:not(.hidden)').show();
  $('.custom_brand_name:not(.hidden)').show();
  $("article").removeClass("evenClass");
  $('#products').removeClass('filtered');
  $('.custom_atv_name').removeClass('first-filtered');
  $('.custom_brand_name').removeClass('first-filtered');
  if ($('#search_filters .show_list').hasClass('active')) {
    $('.custom_atv_name').show();
  } else {
    $('.custom_atv_name').hide();
  }
  var code_wild = false;

  // $('#search_filters .sidebar-bottom input[data-search-url]').each(function(){
  //   if ($(this).prop('checked')) {
  //     var type = 'Other';
  //     var parent = $(this).parents('section').first();
  //     if (parent.hasClass('facet--4') || parent.hasClass('facet--35')) {
  //       type = 'Code';
  //       if (parent.hasClass('facet--35') && $('#category.category-id-17').length) {
  //         code_wild = true;
  //       }
  //     } else if (parent.hasClass('facet--5')) {
  //       type = 'Type';
  //     } else if (parent.hasClass('facet--8')) {
  //       type = 'Fuel';
  //     } else if (parent.hasClass('facet--9')) {
  //       type = 'Wet';
  //     } else if (parent.hasClass('facet--27')) {
  //       type = 'Top40';
  //     } else if (parent.hasClass('facet--37')) {
  //       type = 'Moto';
  //     } else if (parent.hasClass('facet--availability')) {
  //       type = 'Availability';
  //     }
  //     if (!filters[type]) {
  //       filters[type] = [];
  //     }
  //     if (parent.hasClass('facet--availability')) {
  //       filters[type].push($(this).data('color'));
  //     } else {
  //       filters[type].push($(this).parent().parent().find('a').text().replace(/^\s+|\s+$/g,''));
  //     }
  //   }
  // });
  if (filters) {
    $('article.product-miniature').each(function(){
      var elem = $(this);
      for (var key in filters) {
        if (false && key == 'offset') {
          if (allVal.indexOf(filters[key][0]) == -1 && elem.attr('data-'+key)) {
            var found = false;
            $.each(filters[key],function(i,offset){
              if (offset == elem.attr('data-'+key)
                || (offset.indexOf('+') != -1 && parseInt(elem.attr('data-'+key)) >= parseInt(offset.replace('+','')))
                || (offset.indexOf('-') != -1 && parseInt(elem.attr('data-'+key)) <= parseInt(offset.replace('+','')))) {
                found = true;
              }
            });
            if (!found) {
              elem.hide();
              $('#products').addClass('filtered');
            }
          }
        } else {
          if (allVal.indexOf(filters[key][0]) == -1 && elem.attr('data-'+key) && filters[key].indexOf(elem.attr('data-'+key)) == -1) {
            console.log(allVal.indexOf(filters[key]), filters[key]);
            elem.hide();
            $('#products').addClass('filtered');
          }
        }
      }
    });
  }
  if ($('#search_filters .show_list').hasClass('active')) {
    if ($('.custom_atv_name:visible').length < 2) {
      $('.custom_atv_name').hide();
    } else {
      $('.custom_atv_name').each(function(){
        var size = $(this).data('atv');
        if ($('article.product-miniature[data-atv="'+size+'"]:visible').length == 0) {
          $(this).hide();
        }
      });
    }
  } else {
    // if ($('.custom_brand_name:visible').length < 2) {
    //   $('.custom_brand_name').hide();
    // } else {
    //   $('.custom_brand_name').each(function(){
    //     var brand = $(this).data('brand');
    //     if ($('article.product-miniature[data-brand="'+brand+'"]:visible').length == 0) {
    //       $(this).hide();
    //     }
    //   });
    // }
  }
  // if (!$('.custom_atv_name').first().is(':visible')) {
  //   $('.custom_atv_name:visible').first().addClass('first-filtered');
  // }
  // if (!$('.custom_brand_name').first().is(':visible')) {
  //   $('.custom_brand_name:visible').first().addClass('first-filtered');
  // }
  // if ($('#products').hasClass('filtered')) {
  //   $("article.product-miniature:visible:even").addClass("evenClass");
  // }
}

function filterTableByCodeType(){
  var filters = [];
  //var viewClass = $('#search_filters .show_list').hasClass('active') ? 'product_show_list' : 'product-miniature';
  // $('article.product-miniature:not(.hidden)').show();
  // $('.custom_brand_name:not(.hidden)').show();
  // $('.custom_brand_name').removeClass('blockVisible');
  // $("article").removeClass("evenClass");
  // $('#products').removeClass('filtered');
  // $('.custom_atv_name').removeClass('first-filtered');
  // $('.custom_brand_name').removeClass('first-filtered');
  // if ($('#search_filters .show_list').hasClass('active')) {
  //   $('.custom_atv_name').show();
  // } else {
  //   $('.custom_atv_name').hide();
  // }
  // var code_wild = false;



  // $('#search_filters .sidebar-bottom input[data-search-url]').each(function(){
  //   if ($(this).prop('checked')) {
  //     var type = 'Other';
  //     var parent = $(this).parents('section').first();
  //     if (parent.hasClass('facet--4') || parent.hasClass('facet--35')) {
  //       type = 'Code';
  //       if (parent.hasClass('facet--35') && $('#category.category-id-17').length) {
  //         code_wild = true;
  //       }
  //     } else if (parent.hasClass('facet--5')) {
  //       type = 'Type';
  //     } else if (parent.hasClass('facet--8')) {
  //       type = 'Fuel';
  //     } else if (parent.hasClass('facet--9')) {
  //       type = 'Wet';
  //     } else if (parent.hasClass('facet--27')) {
  //       type = 'Top40';
  //     } else if (parent.hasClass('facet--37')) {
  //       type = 'Moto';
  //     } else if (parent.hasClass('facet--availability')) {
  //       type = 'Availability';
  //     }
  //     if (!filters[type]) {
  //       filters[type] = [];
  //     }
  //     if (parent.hasClass('facet--availability')) {
  //       filters[type].push($(this).data('color'));
  //     } else {
  //       filters[type].push($(this).parent().parent().find('a').text().replace(/^\s+|\s+$/g,''));
  //     }
  //   }
  // });
  if ($('#search_filters .show_list').hasClass('active')) {
    if ($('.custom_atv_name:visible').length < 2) {
      $('.custom_atv_name').hide();
    } else {
      $('.custom_atv_name').each(function(){
        var size = $(this).data('atv');
        if ($('article.product-miniature[data-atv="'+size+'"]:visible').length == 0) {
          $(this).hide();
        }
      });
    }
  } else {
    // if ($('.custom_brand_name:visible').length < 2) {
    //   $('.custom_brand_name').hide();
    // } else {
    //   $('.custom_brand_name').each(function(){
    //     var brand = $(this).data('brand');
    //     if ($('article.product-miniature[data-brand="'+brand+'"]:visible').length == 0) {
    //       $(this).hide();
    //     }
    //   });
    // }
  }
  // if (!$('.custom_atv_name').first().is(':visible')) {
  //   $('.custom_atv_name:visible').first().addClass('first-filtered');
  // }
  // if (!$('.custom_brand_name').first().is(':visible')) {
  //   $('.custom_brand_name:visible').first().addClass('first-filtered');
  // }
  // if ($('#products').hasClass('filtered')) {
  //   $("article.product-miniature:visible:even").addClass("evenClass");
  // }
}
function validateCode(str) {
  if ($('body').hasClass('category-id-16')) {
    var re = /(\d{2})(\d{2})?(\d{2})?/;
    return str.match(re);
  } else {
    if (str.substr(0,1) == '0') {
      str = str.substr(1,7);
    }
    if (str.length == 2) {
      var re = /(\d{2})?/;
      return str.match(re);
    }
    else if (str.length == 4) {
      var re = /(\d{2})(\d{2})?/;
      return str.match(re);
    }
    else if (str.length == 6) {
      var re = /(\d{2})(\d{2})?(\d{2})?/;
      return str.match(re);
    } else {
      var re = /(\d{3})(\d{2})?(\d{2})?/;
      return str.match(re);
    }
  }
}
renderInput();
function selectElement(match, selector) {
  const $el = $((selector ? selector+' ' : '')+'.facet-dropdown .dropdown-menu a, '+(selector ? selector+' ' : '')+'.facet-dropdown > a > span[data-q]')
    .filter(function() {return $(this).text() == match || parseInt($(this).text()) == match})
    .eq(0);
  if($el.length === 0) return;
  $el.parents('.facet-dropdown').find('> a > span').data('selected', false).text($el.text());
  $el.siblings().data('selected', false);
  $el.data('selected', true);
}
$('#search_filters .sidebar-top').find('section.facet[class*="facet--"]')
  .filter(function () {
    var cl_name = '';
    $.each(this.className.split(' '), function(i, cl){
      if(cl.indexOf('facet--') !== -1) {
        cl_name = cl;
      }
    });
    if (cl_name) {
      $(this).addClass('facet-ind-'+$(this).index());
      $('#search_filters .'+cl_name+' .facet-dropdown > a > span[data-q]').each(function() {
        selectElement($(this).text(),'#search_filters .'+cl_name);
      });
    }
  });
var skip = !$('body').hasClass('category-id-21');
$('.facet-dropdown > a > span').each(function() {
  if (skip) {
    skip = false;
    return;
  }
  if ($(this).data('q') === undefined) {
    $('.products.row').addClass('hide-price');
  }
});

if ($('.custom_atv_name').length === 1) {
  $('.custom_atv_name').hide();
}

// $('#search_filters .sidebar-top').find('section.facet[class*="facet--"]')
//   .filter(function () {
//     var cl_name = '';
//     $.each(this.className.split(' '), function(i, cl){
//       if(cl.indexOf('facet--') !== -1) {
//         cl_name = cl;
//       }
//     });
//     if (cl_name) {
//       if (cl_name.indexOf('facet--28') != -1 || cl_name.indexOf('facet--29') != -1 || cl_name.indexOf('facet--30') != -1 || cl_name.indexOf('facet--32') != -1) {
//         $('<a rel="nofollow" href="javascript:;" class="select-list" data-q="wheels_diameter-all">All</a>').prependTo($('#search_filters .'+cl_name+' .facet-dropdown .dropdown-menu'));
//       }
//       $('#search_filters .'+cl_name+' .facet-dropdown .dropdown-menu a').each(function(){
//         var elem = $(this);
//         elem.attr('href','javascript:;');
//         var text = elem.text();
//         elem.on('click', function(){
//           selectElement(text, '#search_filters .'+cl_name);
//           if (cl_name.indexOf('facet--28') != -1 || cl_name.indexOf('facet--29') != -1 || cl_name.indexOf('facet--30') != -1 || cl_name.indexOf('facet--32') != -1) {
//             var filters = [];
//             filters['dia'] = []; filters['dia'].push($('#search_filters .facet--32 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//             filters['lug'] = []; filters['lug'].push($('#search_filters .facet--29 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//             filters['stud'] = []; filters['stud'].push($('#search_filters .facet--30 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//             filters['offset'] = []; filters['offset'].push($('#search_filters .facet--28 .select-title > span').text().replace(/^\s+|\s+$/g,''));
//             filterTableByCar(filters);
//           }
//         });
//       })
//     }
// });
//
// $('#autofind_sub').on('click', function(e) {
//   // e.preventDefault();
//   // var q = 'q=';
//   // $('#search_filters [data-q]').filter(function() {
//   //     return $(this).data('selected') === true;
//   // }).each(function() {
//   //     q += $(this).data('q') + '/';
//   // });
//   // if(q === 'q=') return;
//   // var query = q.slice(0, q.lastIndexOf('/'));
//   // var href = window.location.href.split('?');
//   // var hrefQ = (href[1]) ? href[1].split('&').filter(function(el) {
//   //     return el.indexOf('q') !== 0;
//   // }) : [];
//   // hrefQ.push(query);
//   // window.location.href = href[0] + '?' + hrefQ.join('&');
//
// });
$.fn.reverse = [].reverse;
$('.table-top .table-cell[data-filter]').on('click', function() {
  $('.table-top .table-cell[data-filter]').removeClass('sorted');
  $(this).addClass('sorted');
  const selector = $(this).data('filter');
  const order = $(this).data('order');
  const $elements = $(selector);
  const newOrdered = $elements.sort(function(a, b){
    const t1 = $(a).text().replace('€', '').trim();
    const t2 = $(b).text().replace('€', '').trim();
    const n1 = parseInt(t1, 10);
    const n2 = parseInt(t2, 10);
    if (!isNaN(n1) || !isNaN(n2)) {
      if(n1 < n2) return 1;
      if(n1 > n2) return -1;
      return 0;
    }
    if(t1 < t2) return 1;
    if(t1 > t2) return -1;
    return 0;
  });
  const newElements = order === 'DESC' ? newOrdered : newOrdered.reverse();
  $(this).data('order', order === 'DESC' ? 'ASC' : 'DESC').attr('data-order', $(this).data('order'));
  newElements.map(function () {
    return $(this).parents('article').first();
  }).each(function () {
    $(this).insertAfter($('.custom_brand_name.' + $(this).attr('id').replace('/', '')));
  })

});
//
// /* Filter and code input */
//
// /*Product description table top fixed*/
// if ($('.table-top').length > 0) {
//     var hT = $('.table-top').offset().top;
//     $(window).resize(function(){
//         $('#products').removeClass('fixed');
//         hT = $('.table-top').offset().top;
//         $(window).trigger('scroll');
//     });
//     $(window).scroll(function() {
//         if (document.cookie.indexOf('show_list=true') === -1) return;
//         if (hT === 0 && !$('#products').hasClass('fixed')) {
//             hT = $('.table-top').offset().top;
//         }
//         var wS = $(this).scrollTop();
//         var $F = $('.products').first();
//         var hF = $F.offset().top + $F.height();
//         if (wS > hT - 44 && wS < hF - 50){
//             $('#products').addClass('fixed');
//         } else {
//             $('#products').removeClass('fixed');
//             hT = $('.table-top').offset().top;
//         }
//     });
//     $(window).trigger('scroll');
// }
// //
if ($('.ct_matrix_head').length > 0) {
  var hT2 = $('.ct_matrix_head').offset().top;
  var $F2 = $('#ct_matrix tbody tr:last-child');
  $(window).resize(function(){
    $('#ct_matrix').removeClass('fixed');
    hT2 = $('.ct_matrix_head').offset().top;
  });
  $(window).scroll(function() {
    var hF2 = $F2.offset().top + $F2.height();
    var wS = $(this).scrollTop();
    //console.log('hF2', hF2, wS);
    if (wS > hT2 && wS < hF2 - 50){
      $('#ct_matrix').addClass('fixed');
    } else {
      $('#ct_matrix').removeClass('fixed');
      hT2 = $('.ct_matrix_head').offset().top;
    }
  });
}
//
$(window).trigger('scroll');
// /*product description table top fixed*/
//
// /*Filter fixed*/
// // if ($('#search_filters_wrapper').length > 0) {
// //     var hT2 = $('#search_filters_wrapper').offset().top;
// //     var $F2 = $('#category .main-content');
// //     $(window).scroll(function() {
// //         var hF2 = $F2.offset().top + $F2.height() - $('#search_filters_wrapper').height();
// //         var wS = $(this).scrollTop();
// //         //console.log('hF2', hF2, wS);
// //         if (wS > hT2 && wS < hF2 - 50){
// //             $('#search_filters_wrapper').addClass('fixed');
// //         } else {
// //             $('#search_filters_wrapper').removeClass('fixed');
// //             hT2 = $('#search_filters_wrapper').offset().top;
// //         }
// //     });
// // }
// $(function() {
//
//     var $sidebar   = $("#search_filters_wrapper");
//     if ($sidebar.length === 0) return;
//     var $window    = $(window),
//         offset     = $sidebar.offset(),
//         topPadding = 0,
//         $wrapper = $('#category .main-content'),
//         wrapperOf = $wrapper.offset(),
//         wrapperH = $wrapper.height(),
//         sidebarH = $sidebar.height();
//
//     $window.scroll(function() {
//         if ($window.scrollTop() > offset.top && $window.width() > 992) {
//             $sidebar.css({
//                 marginTop: Math.min($window.scrollTop() - offset.top + topPadding, wrapperH - sidebarH - 100)
//             });
//         } else {
//             $sidebar.css({
//                 marginTop: 0
//             });
//         }
//     });
//
// });
// /*filter fixed*/
function sorting(a, b){
  const t1 = $(a).text().replace('€', '').trim();
  const t2 = $(b).text().replace('€', '').trim();
  const n1 = parseInt(t1, 10);
  const n2 = parseInt(t2, 10);
  if (!isNaN(n1) || !isNaN(n2)) {
    if(n1 < n2) return 1;
    if(n1 > n2) return -1;
    return 0;
  }
  if(t1 < t2) return 1;
  if(t1 > t2) return -1;
  return 0;
}
//
function sortBrands() {
  var $brandP = $('.products').first();
  var $brands = $brandP.find('> .custom_brand_name');
  var $newBrands = $brands.sort(sorting).reverse();
  $newBrands.each(function() {
    $brandP.append($(this));
  });
  var $atvCats = $brandP.find('> .custom_atv_name');
  var $newAtvCats = $atvCats.sort(sorting).reverse();
  $newAtvCats.each(function() {
    $brandP.append($(this));
  });
}
//
//
//
// sortBrands();
if(document.cookie.indexOf('show_list=true') === -1 && $('body').attr('id') !== 'search') {
  sortItemsInBrand();
  $('.custom_brand_name').removeClass('product_list_view');
} else {
  sortItemsInList();
}

function sortProductPage() {
  var firstDelimiter = ' ';
  var secondDelimiter = '/';
  var replacement = 'R';
  if ($('body').hasClass('product-id-category-16')) {
    firstDelimiter = '-';
    secondDelimiter = 'x'
  }
  if (
    !$('body').hasClass('product-id-category-13') &&
    !$('body').hasClass('product-id-category-14') &&
    !$('body').hasClass('product-id-category-17') &&
    !$('body').hasClass('product-id-category-16')
  ) {
    return;
  }
  var $productTable = $('#ct_matrix');
  if ($productTable.length === 0) {
    return;
  }
  var $productBody = $productTable.find('tbody');
  var $products = $productBody.find('tr');
  $products.sort(function(a, b) {
    var text1 = $(a).find('td').first().text().trim().split(firstDelimiter);
    var text2 = $(b).find('td').first().text().trim().split(firstDelimiter);
    var size10 = parseInt(text1[1].replace(replacement, ''), 10);
    var size20 = parseInt(text2[1].replace(replacement, ''), 10);
    if (size10 === size20) {
      var text3 = text1[0].split(secondDelimiter).map(i => i.trim());
      var text4 = text2[0].split(secondDelimiter).map(i => i.trim());
      var size11 = parseInt(text3[0], 10);
      var size21 = parseInt(text4[0], 10);
      if (size11 === size21) {
        var size13 = parseInt(text3[1], 10);
        var size23 = parseInt(text4[1], 10);
        return size13 - size23;
      }
      return size11 - size21;
    }
    return size10 - size20;
  }).each(function() {
    $productBody.append($(this));
  });
}

sortProductPage();
//
$(document).on('keydown', function(event) {
  $('.facet-dropdown.open .dropdown-menu > a').filter(function(){
    return $(this).text().trim().toLowerCase()[0] === event.key;
  }).first().addClass('focused').focus().siblings().removeClass('focused');
});
//

//
if ($('#storage').length > 0) {
  var $storage = $('#storage');
  var holes = $('.facet--29 > ul > li a > span');
  var range = $('.facet--30 > ul > li a > span');
  var size = $('.facet--32 > ul > li a > span');
  var query = {
    holes: holes,
    range: range,
    size: size
  };
  var href = $storage.attr('href');
  Object.keys(query).forEach(function (i) {
    var $elem = query[i];
    if ($elem.data('q') !== undefined) {
      href += '&' + i + '=' + $elem.text().trim();
    }
  });
  //console.log(href);
  $storage.attr('href', href);
}

function truncateCharacters(text, limit, ellipsis = '...', strip = 0) {
  if (text.length > limit) {
    text = $.trim(text.substring(0, limit - strip)) + ellipsis;
  }
  return text;
}
//
$(document).ready(function() {

  $('.loading').fadeOut('slow');

  $('#reservation .modal-background').on('click', function() {
    $('.modal-footer #close-modal').click();
  });

  $('.slot .free-slot-link, .slot .offer-slot-link').on('click', function() {
    $('.modal-body.finish, .finish-footer').remove();
    $('.reservation-modal-body').slideDown();
    $('.reservation-modal-footer #submit-reservation').show();
    $('.reservation-modal-footer #close-modal').text('Atcelt');
    $('#reservation #modalTitle.title-finish').remove();
    $('#reservation #modalTitle').slideDown();
    date = $(this).data('date');
    queue_id = $(this).data('col');
    iorder = $(this).data('iorder');
    slot = $(this).attr('id');

    // $('input[name="date"]').val(date);
    // $('input[name="queue_id"]').val(queue_id);
    // $('input[name="slotNumber"]').val(iorder);

    $.ajax({
      url: '/pieraksts/getSlotInfo',
      method: 'POST',
      data: { 'date': date, 'queue_id': queue_id, 'slotNumber': iorder },
      dataType: "JSON",
      success: function(data) {
        $('.dayOfWeek').text(data.dayOfWeek);
        $('.dateOfDay').text(data.date);
        $('.timeOfDay').text(data.time);
        $('.officeTitle').text(data.office_title);
      }
    });
  });

  $('#reservation #service input[name="serviceOption"]').on('input', function() {
    if ($(this).data('save') == 1) {
      $('<div class="form-group row temp_save_nr"><label for="save_nr" class="col-sm-3" style="text-align:left;">Glabāšanas talona numurs:</label><div class="col-sm-9"><input type="text" class="form-control" id="save_nr"></div><div class="col-sm-3"></div><div class="col-sm-9" style="font-size: 11px; line-height: 10px;">Ja Jums pašlaik nav zināms glabāšanas talona numurs, tas nekas, atradīsim Jūsu riepas vai riteņus pēc automašīnas numura</div></div>').insertAfter('.services');
    } else {
      $('#reservation .temp_save_nr').remove();
    }
  });

  $('.modal#slotModal #service input[name="serviceOption"]').on('input', function() {
    if ($(this).data('save') == 1) {
      $('<div class="form-group row bg-light temp_save_nr"><label for="save_nr" class="col-sm-3" style="text-align:right;">Glabāšanas talona numurs:</label><div class="col-sm-9"><input type="text" class="form-control" id="save_nr"></div><div class="col-sm-3"></div><div class="col-sm-9" style="font-size: 11px; line-height: 10px;">Ja Jums pašlaik nav zināms glabāšanas talona numurs, tas nekas, atradīsim Jūsu riepas vai riteņus pēc automašīnas numura</div></div>').insertAfter('.services');
    } else {
      $('#slotModal .temp_save_nr').remove();
    }
  });

  $('.select-service-option').on('change', function() {
    console.log($(this).val());
    if ($(this).val() == 2) {
      $('<div class="form-group row bg-light temp_save_nr"><label for="save_nr" class="col-sm-3" style="text-align:right;">Glabāšanas talona numurs:</label><div class="col-sm-9"><input type="text" class="form-control" id="save_nr"></div><div class="col-sm-3"></div><div class="col-sm-9" style="font-size: 11px; line-height: 10px;">Ja Jums pašlaik nav zināms glabāšanas talona numurs, tas nekas, atradīsim Jūsu riepas vai riteņus pēc automašīnas numura</div></div>').insertAfter('.services');
    } else {
      $('div.temp_save_nr').hide();
    }
  });

  $(document).on('keypress', function(e) {
    if ($('#reservation').is(':visible')) {
      if (e.key === 'Enter') {
        e.preventDefault();
        $('#submit-reservation').click();
      }
    }
  });

  $('#reservation').on('hide.bs.modal', function () {
    $('#reservation form').trigger('reset');
    $('#reservation .alert').remove();
    $('#reservation .temp_save_nr').remove();
    $('#brand, #model, #phone, #email').removeAttr('placeholder');
  });

  $('#submit-reservation').on('click', function() {

    $(this).attr('disabled', true).prop('disabled', true);
    $('#close-modal').attr('disabled', true).prop('disabled', true);

    let recaptcha_k = $('#recaptcha_k').data('value');
    let car = $('#reservation #brand').val();
    let carModel = $('#reservation #model').val();
    let licPlate = $('#reservation #reg_nr').val();
    let purpose = $('input[name="serviceOption"]:checked').val();
    let storageBin = $('#reservation .temp_save_nr #save_nr').val()
    let comment = $('#reservation #comment').val();
    let name = $('#reservation #name').val();
    let phone = $('#reservation #phone').val();
    let email = $('#reservation #email').val();

    $.ajax({
      url: '/pieraksts/fillSlot',
      method: 'POST',
      dataType: 'JSON',
      data: {
        'car': car,
        'carModel': carModel,
        'licPlate': licPlate,
        'purpose': purpose,
        'storageBin': storageBin,
        'comment': comment,
        'name': name,
        'phone': phone,
        'email': email,
        'date': date,
        'queue_id': queue_id,
        'slotNumber': iorder,
        'token': $('#reservation input[name=grecaptcha]').val(),
        'action': $('#reservation input[name=grecaptcha_app]').val(),
      },
      success: function(data) {
        $('#submit-reservation').removeAttr('disabled');
        $('#close-modal').removeAttr('disabled');
        if (data.error) {
          if (data.error.brand) $('#brand').attr('placeholder', data.error.brand);
          if (data.error.model) $('#model').attr('placeholder', data.error.model);
          if (data.error.reg_nr) $('#reg_nr').attr('placeholder', data.error.reg_nr);
          if (data.error.purpose) {
            if ($('.purpose-error').length == 0) {
              $('<div class="alert alert-danger purpose-error">' + data.error.purpose + '</div>').insertAfter('#reservation .form-group:last');
            }
          } else {
            $('#reservation .purpose-error').remove();
          }
          if (data.error.phone) $('#phone').attr('placeholder', data.error.phone);
          if (data.error.wrongPhone) {
            if ($('.phone-error').length == 0) {
              $('<div class="alert alert-danger phone-error">' + data.error.wrongPhone + '</div>').insertAfter('.mobile-body .form-group:last');
              $('<div class="alert alert-danger phone-error">' + data.error.wrongPhone + '</div>').insertAfter('#reservation .form-group:last');
            }
          } else {
            $('.mobile-body .phone-error').remove();
          }
          if (data.error.email) $('#email').attr('placeholder', data.error.email);
          if (data.error.emptyEmail) {
            if ($('.email-error').length == 0) {
              $('<div class="alert alert-danger email-error">' + data.error.emptyEmail + '</div>').insertAfter('#reservation .form-group:last')
            }
          } else {
            $('#reservation .email-error').remove();
          }
        } else if (data.success) {

          $('#submit-reservation').removeAttr('disabled');
          $('#close-modal').removeAttr('disabled');
          let plate = phone.substr(-3);
          plate = parseInt(plate);
          plate = $.trim(plate);

          let successText = truncateCharacters($.trim(car),8,'&mldr;',1) + ' xxxxx' + plate;

          $('.reservation-modal-body').slideToggle();
          $('#modalTitle').first().slideToggle();
          $('<h5 class="modal-title title-finish" id="modalTitle">Pieraksts</h5>').insertAfter('#modalTitle');
          $('.reservation-modal-footer #submit-reservation').hide();
          $('.reservation-modal-footer #close-modal').text('Aizvērt');
          $('<div class="modal-body finish">' + data.success + '</div><div class="modal-footer finish-footer"><button type="button" class="btn btn-secondary" id="close-modal" data-dismiss="modal" style="margin-right: 10px;">Aizvērt</button></div>').insertAfter($('#modalTitle').parent()).css('display', 'none').slideDown();
          $('<td class="taken-slot slot">' + successText + '</td>').hide().fadeIn().insertAfter($('#' + slot + '[data-date="' + date + '"]').parent());
          $('#' + slot + '[data-date="' + date + '"]').parent().fadeOut().remove();
          $('#brand, #model, #phone, #email').removeAttr('placeholder');
          $('#reservation form').trigger('reset');
          $('#reservation .temp_save_nr').remove();
        } else if (data.taken) {
          $('#submit-reservation, #close-modal').removeAttr('disabled');
          $('.reservation-modal-body').slideToggle();
          $('#modalTitle').first().slideToggle();
          $('<h5 class="modal-title title-finish" id="modalTitle">Pieraksts</h5>').insertAfter('#modalTitle');
          $('.reservation-modal-footer #submit-reservation').hide();
          $('.reservation-modal-footer #close-modal').text('Aizvērt');
          $('<div class="modal-body finish">' + data.taken + '</div>').insertAfter($('#modalTitle').parent()).css('display', 'none').slideDown();
          $('#brand, #model, #phone, #email').removeAttr('placeholder');
          $('#reservation form').trigger('reset');
          $('#reservation .temp_save_nr').remove();
        }
      }
    });
  });

  $('.queueTable.records .subheader svg').on('click', function() {
    $('.modal#queueModal input[name="queue_id"]').val($(this).data('queue-id'));
    $('.modal#queueModal input[name="date"]').val($(this).data('date'));

    $.ajax({
      method: 'GET',
      url: '/admin/pieraksts/queue_ajax/' + $(this).data('queue-id') + '/' + $(this).data('date'),
      dataType: 'JSON',
      success: function(data) {
        $('.modal#queueModal .title').text(data.f_office);
        $('.modal#queueModal input#title').val(data.f_title);
        (data.f_visible == 1) ? $('.modal#queueModal .time #isActive').attr('checked', true) : $('.modal#queueModal .time #isActive').removeAttr('checked');
        $('.modal#queueModal .time #openTime').val(data.f_opentime);
        $('.modal#queueModal .time #closeTime').val(data.f_closetime);
        $('.modal#queueModal .f_day').html(data.f_day);
        if (data.f_rows === 1) {
          $('#gridRadios6').removeAttr('checked', true);
          $('#gridRadios5').attr('checked', true);
        } else {
          $('#gridRadios5').removeAttr('checked', true);
          $('#gridRadios6').attr('checked', true);
        }
      }
    })
  });

  $('.modal#queueModal .submit').on('click', function(e) {
    e.preventDefault();
    $.ajax({
      method: 'POST',
      url: '/admin/pieraksts/queue_ajax/' + $('.modal#queueModal input[name="queue_id"]').val() + '/' + $('.modal#queueModal input[name="date"]').val(),
      data: {
        'q': $('.modal#queueModal input[name="queue_id"]').val(),
        'd': $('.modal#queueModal input[name="date"]').val(),
        'title': $('.modal#queueModal #title').val(),
        'isActive': $('.modal#queueModal #isActive').is(':checked'),
        'opentime': $('.modal#queueModal #openTime').val(),
        'closetime': $('.modal#queueModal #closeTime').val(),
        'f_purpose': $('.modal#queueModal input[name=gridRadios]:checked').val(),
        'f_rows': $('.modal#queueModal input[name=rows]:checked').val(),
      },
      dataType: 'JSON',
      success: function (data) {
        if (data.status === 0) {
          this.error(data.status_text);
        }
        location.reload();
      },
      error: function(data) {
        console.log(data.responseText);
        $('<div class="alert alert-danger">' + data.status_text + '</div>').insertAfter($('.modal#queueModal input[type=hidden]').last());
      }
    });
  });

  $('.queueTable.records .buttonbar svg').on('click', function() {
    $('.modal#slotModal input[name="queue_id"]').val($(this).data('queue-id'));
    $('.modal#slotModal input[name="date"]').val($(this).data('date'));
    $('.modal#slotModal input[name="slot"]').val($(this).data('slot-id'));

    $.ajax({
      method: 'GET',
      url: '/admin/pieraksts/slot_ajax/' + $(this).data('queue-id') + '/' + $(this).data('date') + '/' + $(this).data('slot-id'),
      dataType: 'JSON',
      success: function(data) {
        $('.modal#slotModal h6.title').text(data.f_office + ', ' + data.f_date + ' ' + data.f_time);
        if (parseInt(data.f_status) === 2) {
          $('select#f_status').append($('<option>', {value:2, text:'Akcija', selected: 'selected'}));
        } else {
          $('select#f_status option').each(function() {
            $(this).removeAttr('selected');
            if ($(this).val() === data.f_status) {
              $(this).attr('selected', 'selected');
            }
          });
        }
      }
    })
  });

  // $('.modal#slotModal .submit').on('click', function(e) {
  //   e.preventDefault();
  //   $.ajax({
  //     method: 'POST',
  //     url: '/admin/pieraksts/slot_ajax/' + $('.modal#slotModal input[name="queue_id"]').val() + '/' + $('.modal#slotModal input[name="date"]').val() + '/' + $('.modal#slotModal input[name="slot"]').val(),
  //     data: {
  //       'queue_id': $('.modal#slotModal input[name="queue_id"]').val(),
  //       'date': $('.modal#slotModal input[name="date"]').val(),
  //       'slot_id': $('.modal#slotModal input[name="slot"]').val(),
  //       'f_status': $('.modal#slotModal #f_status').val(),
  //       'f_slotcomment': $('.modal#slotModal #f_slotcomment').val(),
  //     },
  //     dataType: 'JSON',
  //     success: function (data) {
  //       if (data.status === 0) {
  //         this.error(data);
  //       }
  //       location.reload();
  //     },
  //     error: function(data) {
  //       console.log(data);
  //     }
  //   });
  // });

  $('.queueTable .discount').each(function() {
    $(this).on('click', function() {

      let checked;

      if ($(this).is(':checked')) {
        checked = 1;
        $(this).parent().children('.slot-comment').html(' 30% Atlaide');
      } else {
        checked = 0;
        $(this).parent().children('.slot-comment').html('');
      }

      $.ajax({
        method: 'POST',
        url: '/admin/pieraksts/discount',
        data: {'slot_id': $(this).data('slot-id'), 'checked': checked},
      });
    });
  });

  $('.reservation_edit#slotModal').on('change', '#f_time', function() {
    let selected_time = $('option:selected', this);
    $('option', this).each(function() { $(this).removeAttr('selected'); });
    selected_time.attr('selected', 'selected').prop('selected', 'selected');
  }).change();

  $('.queueTable.reservation .buttonbar svg').on('click', function() {
    // console.log($(this).data('date'));
    // throw '';

    if ($('.last-info').length) {
	    $('.last-info').remove();
    }

    let __date = $(this).data('date');

    $('.modal#slotModal input[name="queue_id"]').val($(this).data('queue-id'));
    $('.modal#slotModal input[name="serviceOption"]').each(function() {
      $(this).removeAttr('checked').prop('checked', false);
    });

    $('.modal#slotModal #f_date option').each(function() {
      $(this).removeAttr('selected');
      if ($(this).val() == __date) {
        $(this).parent().val($(this).val());
        $(this).attr('selected', true).prop('selected', true);
      }
    });
    $('.modal#slotModal input[name="date"]').val($(this).data('date'));
    // $('.modal#slotModal #f_date option[value=' + $(this).data('date') + ']').attr('selected', 'selected').prop('selected', 'selected');
    $('.modal#slotModal input[name="slot"]').val($(this).data('slot-id'));
    $('.modal#slotModal input[name="part"]').val($(this).data('slot-part'));

    $.ajax({
      method: 'GET',
      url: '/admin/rezervacijas/slot_ajax/' + $(this).data('queue-id') + '/' + $(this).data('date') + '/' + $(this).data('slot-id') + '/' + $(this).data('slot-part'),
      dataType: 'JSON',
      success: function(data) {
        // $('.modal#slotModal #f_date').val(data.f_date);
        $('.modal#slotModal #f_time').val(data.f_time);
        $('.modal#slotModal #f_time option[value="' + data.f_time + '"]').attr('selected', true);
        $('.modal#slotModal #f_car').val(data.f_car);
        $('.modal#slotModal #f_model').val(data.f_model);
        $('.modal#slotModal #f_plate').val(data.f_plate);
        $('.modal#slotModal #f_office').append(data.options);
        $('.modal#slotModal select#f_office option[value="' + data.q + data.f_office + '"]').attr('selected','selected').prop('selected', 'selected');
        $('.modal#slotModal select.select-service-option option[name="serviceOption"]').each(function() {
          if ($(this).val() == data.f_purpose) {
            $(this).attr('selected', true).prop('selected', true);
            if ($(this).data('save') == 1) {
              $('<div class="form-group row bg-light temp_save_nr"><label for="save_nr" class="col-sm-3" style="text-align:right;">Glabāšanas talona numurs:</label><div class="col-sm-9"><input type="text" class="form-control" id="save_nr" value="' + data.f_storagebin + '"></div><div class="col-sm-3"></div><div class="col-sm-9" style="font-size: 11px; line-height: 10px;">Ja Jums pašlaik nav zināms glabāšanas talona numurs, tas nekas, atradīsim Jūsu riepas vai riteņus pēc automašīnas numura</div></div>').insertAfter('.services');
            }
          }
        });
        $('.modal#slotModal #f_comment').html(data.f_comment);
        $('.modal#slotModal #f_name').val(data.f_name);
        $('.modal#slotModal #f_phone').val(data.f_phone);
        $('.modal#slotModal #f_email').val(data.f_email);
        if (data.f_status != 0) {
          $('.modal#slotModal #f_status').val(data.f_status);
        } else {
          $('.modal#slotModal #f_status').val(1);
        }
        $('.modal#slotModal #f_slotcomment').html(data.f_slotcomment);
         if (data.p == 'a') {
           if (data.is_mobile == 1) {
             if (data.f_edittime == '') {
               $('<div class="last-info">Pieraksts izveidots no mobīlās ierīces<br>Izveidots: ' + data.f_createtime + ' (' + data.f_createuser + ')<br>Labots:</div>').insertAfter($('.modal#slotModal .form-group').last());
             } else {
               $('<div class="last-info">Pieraksts izveidots no mobīlās ierīces<br>Izveidots: ' + data.f_createtime + ' (' + data.f_createuser + ')<br>Labots: ' + data.f_edittime + ' (' + data.f_edituser + ')</div>').insertAfter($('.modal#slotModal .form-group').last());
             }
           } else {
             if (data.f_edittime == '') {
               $('<div class="last-info">Pieraksts izveidots no datora<br>Izveidots: ' + data.f_createtime + ' (' + data.f_createuser + ')<br>Labots:</div>').insertAfter($('.modal#slotModal .form-group').last());
             } else {
               $('<div class="last-info">Pieraksts izveidots no datora<br>Izveidots: ' + data.f_createtime + ' (' + data.f_createuser + ')<br>Labots: ' + data.f_edittime + ' (' + data.f_edituser + ')</div>').insertAfter($('.modal#slotModal .form-group').last());
             }
           }
        } else {
           if (data.is_mobile2 == 1) {
             if (data.f_edittime2 == '') {
               $('<div class="last-info">Pieraksts izveidots no mobīlās ierīces<br>Izveidots: ' + data.f_createtime2 + ' (' + data.f_createuser2 + ')<br>Labots:</div>').insertAfter($('.modal#slotModal .form-group').last());
             } else {
               $('<div class="last-info">Pieraksts izveidots no mobīlās ierīces<br>Izveidots: ' + data.f_createtime2 + ' (' + data.f_createuser2 + ')<br>Labots: ' + data.f_edittime2 + ' (' + data.f_edituser2 + ')</div>').insertAfter($('.modal#slotModal .form-group').last());
             }
           } else {
             if (data.f_edittime2 == '') {
               $('<div class="last-info">Pieraksts izveidots no datora<br>Izveidots: ' + data.f_createtime2 + ' (' + data.f_createuser2 + ')<br>Labots:</div>').insertAfter($('.modal#slotModal .form-group').last());
             } else {
               $('<div class="last-info">Pieraksts izveidots no datora<br>Izveidots: ' + data.f_createtime2 + ' (' + data.f_createuser2 + ')<br>Labots: ' + data.f_edittime2 + ' (' + data.f_edituser2 + ')</div>').insertAfter($('.modal#slotModal .form-group').last());
             }
           }
        }
      }
    })
  });

  $('.modal#slotModal').on('hide.bs.modal', function() {
    if ($('.temp_save_nr').is(':visible')) {
      $('.modal#slotModal .temp_save_nr').remove();
    }
  });

  $('.modal#slotModal .submit').on('click', function(e) {
    e.preventDefault();
    $.ajax({
      method: 'POST',
      url: '/admin/rezervacijas/slot_ajax/' + $('.modal#slotModal input[name="queue_id"]').val() + '/' + $('.modal#slotModal #f_date').val() + '/' + $('.modal#slotModal input[name="slot"]').val() + '/' + $('.modal#slotModal input[name="part"]').val(),
      data: {
	'f_currDate': $('.modal#slotModal input[name=date]').val(),
        'f_office': $('.modal#slotModal #f_office').val(),
        'f_date': $('.modal#slotModal #f_date').val(),
        'f_time': $('.modal#slotModal #f_time').val(),
        'f_status': $('.modal#slotModal #f_status').val(),
        'f_car': $('.modal#slotModal #f_car').val(),
        'f_model': $('.modal#slotModal #f_model').val(),
        'f_plate': $('.modal#slotModal #f_plate').val(),
        'f_purpose': $('.modal#slotModal option[name="serviceOption"]:selected').val(),
        'f_storagebin': $('.modal#slotModal .temp_save_nr #save_nr').val(),
        'f_comment': $('.modal#slotModal #f_comment').val(),
        'f_name': $('.modal#slotModal #f_name').val(),
        'f_phone': $('.modal#slotModal #f_phone').val(),
        'f_email': $('.modal#slotModal #f_email').val(),
        'f_slotcomment': $('.modal#slotModal #f_slotcomment').val(),
      },
      dataType: 'JSON',
      success: function (data) {
        if (data.status === 0) {
          this.error(data);
          return false;
        }
        location.reload();
      },
      error: function(data) {
        if (data.error_fields.f_office) {
          toastr.options.timeOut = 3000;
          toastr.error(data.error_fields.f_office, 'Kļūda');
        }
        if (data.error_fields.f_time) {
          toastr.options.timeOut = 3000;
          toastr.error(data.error_fields.f_time, 'Kļūda');
        }
      },
    });
  });

  $('.modal#slotModal .delete').on('click', function() {
    $('.modal#slotModal #f_status option[value=0]').attr('selected', 'selected').prop('selected', 'selected');
    $('.modal#slotModal .submit').click();
  });

  // $('.modal#slotModal .submit').on('click', function(e) {
  //   e.preventDefault();
  //   $.ajax({
  //     method: 'POST',
  //     url: '/admin/pieraksts/slot_ajax/' + $('.modal#slotModal input[name="queue_id"]').val() + '/' + $('.modal#slotModal input[name="date"]').val() + '/' + $('.modal#slotModal input[name="slot"]').val(),
  //     data: {
  //       'queue_id': $('.modal#slotModal input[name="queue_id"]').val(),
  //       'date': $('.modal#slotModal input[name="date"]').val(),
  //       'slot_id': $('.modal#slotModal input[name="slot"]').val(),
  //       'f_status': $('.modal#slotModal #f_status').val(),
  //       'f_slotcomment': $('.modal#slotModal #f_slotcomment').val(),
  //     },
  //     dataType: 'JSON',
  //     success: function (data) {
  //       if (data.status === 0) {
  //         this.error(data);
  //       }
  //       location.reload();
  //     },
  //     error: function(data) {
  //       console.log(data);
  //     }
  //   });
  // });

  $('.modal#slotModal .decline, .modal#slotModal button.close').on('click', function() {
    $('.modal#slotModal input[name="gridRadios"]').first().attr('checked', true).prop('checked', true);
    $('.modal#slotModal input[name="rows"]').first().attr('checked', true).prop('checked', true);
    $('.modal#slotModal #f_office').html('');
  });

  $('.modal#queueModal .decline, .modal#queueModal button.close').on('click', function() {
    $('.modal#queueModal input[name="gridRadios"]').first().attr('checked', true).prop('checked', true);
    $('.modal#queueModal input[name="rows"]').first().attr('checked', true).prop('checked', true);
  });

  $('.modal#slotModal .decline').on('click', function() {
    $('.modal#slotModal input[name="gridRadios"]').each(function() {
      $(this).attr('checked', false);
    });
    $('.modal#slotModal textarea').each(function() {
      $(this).html('');
    })
  });

  $('.modal#slotModal').on('keypress', function(e) {
    if (e.key == 'Enter' && e.target == $('textarea.specialClass')) {
      $('.modal#slotModal .submit').click();
    }
  });

});

function unique(array){
  return array.filter(function(el, index, arr) {
    return index === arr.indexOf(el);
  });
}

Array.prototype.insert = function ( index, item ) {
  this.splice( index, 0, item );
};

let $inputs = $('input[name="currBrand"], input[name="d1"], input[name="d2"], input[name="d3"]');

$inputs.prop('readonly', true);

$inputs.attr('readonly', true);

if ($('.size-dropdown').hasClass('open')) {
  $(this).on('click', function(e) {
    $(this).removeClass('open');
    e.stopPropagation();
  });
} else {
  $(this).on('click', function(e) {
    $(this).addClass('open');
    e.stopPropagation();
  });
}

$(document).on("click", function(event){
  if(!$(event.target).closest(".size-dropdown").length){
    $('.size-dropdown').removeClass('open');
  }
});

// JURIDISKA VAI FIZISKA PERSONA
const disabledFields = $(".reveal-if-active input");

disabledFields.each( function() {
  disabledFields.prop('disabled', true);
});

$('#j').on('change', function() {
  $("#status-name").html("Juridiska");
  for (let i = 0; i < disabledFields.length; i++) {
    disabledFields[i].disabled = false;
  }
});
$('#f').on('change', function() {
  $("#status-name").html("Privātpersona");
  for (let i = 0; i < disabledFields.length; i++) {
    disabledFields[i].disabled = true;
  }
});

let $person = $('input[type=hidden][name=person]').val();

if ($person == 1) {
  $('.reveal-if-active').hide();
} else {
  $('.reveal-if-active').show();
  $('#company_registration_number').removeAttr("disabled");
  $('#company_pvn_number').removeAttr("disabled");
  $('#company_name').removeAttr("disabled");
  $('#company_address').removeAttr("disabled");
}

$('.person-status-container label').on('click', function() {
  let $var = $(this).children();
  if ($var.val() == 1) {
    $('.reveal-if-active').hide();
    $('input[type=hidden][name=person]').val(1);
  } else {
    $('.reveal-if-active').show();
    $('input[type=hidden][name=person]').val(2);
  }
});

$('#company_registration_number').on('keyup', function() {

  if ($(this).val().length != 11) {
    return false;
  }

  if ($('#company_pvn_number').val().lenght != 0 && $('#company_name').val().length != 0 && $('#company_address').val().length != 0) {
    return false;
  }

  $('#company_pvn_number').attr("disabled", true);
  $('#company_name').attr("disabled", true);
  $('#company_address').attr("disabled", true);

  let $url = window.location.protocol + '//' + window.location.hostname + '/api/company/' + $('#company_registration_number').val();

  $.ajax({
    url: $url,
    method: 'GET',
    data: {
      'csrf': $('meta[name="csrf-token"]').attr('content'),
    },
    dataType: 'JSON',
    success: function(data) {
      $('#company_registration_number').parent().removeClass('has-warning');
      $('.registration_number_error').hide();
      $('#company_pvn_number').val(data.pvn_code);
      $('#company_name').val(data.name);
      $('#company_address').val(data.address);
    },
    error: function () {
      console.log("WRONG CODE");
      $('#company_registration_number').parent().addClass('has-warning');
      $('.registration_number_error').show();
    },
    complete: function() {
      $('#company_pvn_number').removeAttr("disabled");
      $('#company_name').removeAttr("disabled");
      $('#company_address').removeAttr("disabled");
    }
  });
});

const deliveryOptionDisabledFields = $(".cart-delivery-option input");

deliveryOptionDisabledFields.each( function() {
  deliveryOptionDisabledFields.prop('disabled', true);
});

function checkShipping(qty = null) {
  $('.cart-grid input[name=delivery]').val(true);
  $('.cart-grid input[name=fitting]').val(false);
  let __total = parseInt($('#cart-subtotal-products .value').html().trim().replace('€ ', '').replace(/,/g, ''));
  let shippingCity = parseInt($('.cart-delivery-option .custom-select option:selected').val());
  $.ajax({
    url: '/checkShipping',
    method: 'POST',
    data: {city: shippingCity, qty: qty},
    success: function(data) {
      data = parseInt(data);
      let __lastPrice = parseInt($('#cart-subtotal-products .value').html().trim().replace('€ ', '').replace(/,/g, ''));
      if (shippingCity === 1 || shippingCity === 2) {
        if (__total > 115) {
          $('#cart-subtotal-shipping #shipping_price').html('Bezmaksas');
          $('.cart-total .value').html('€ ' + formatNumber(__total));
          $('input[name=delivery_price]').removeAttr('value');
        } else {
          $('#cart-subtotal-shipping #shipping_price').html('€ ' + data);
          $('.cart-total .value').html('€ ' + (formatNumber(__lastPrice + data)));
          $('input[name=delivery_price]').val(data);
        }
      } else {
        $('#cart-subtotal-shipping #shipping_price').html('€ ' + data);
        $('.cart-total .value').html('€ ' + (formatNumber(__lastPrice + data)));
        $('input[name=delivery_price]').val(data);
      }
      $('input[name=fitting_price]').removeAttr('value');
    }
  });
}

function checkFitting(qty = null) {
  let __total = parseInt($('#cart-subtotal-products .value').html().trim().replace('€ ', '').replace(/,/g, ''));
  let __items = parseInt($('#cart-subtotal-products .js-subtotal').html().trim().replace(' Preces', ''));
  let needsFit = $('.cart-montage-choice .cart-delivery-options .cart-delivery-label input:checked').val();

  if (qty === null) {
    qty = __items;
  }

  $.ajax({
    url: '/checkFitting',
    method: 'POST',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: {total_items: qty, fitting: needsFit},
    dataType: 'JSON',
    success: function(data) {
      let fittingPrice = parseInt(data.cartOptions.fitting_price);
      let __lastPrice = parseInt($('#cart-subtotal-products .value').html().trim().replace('€ ', '').replace(/,/g, ''));

      $('.cart-grid input[name=delivery]').val(false);
      $('.cart-grid input[name=fitting]').val(true);
      $('#cart-subtotal-montage #shipping_price').html('€ ' + formatNumber(fittingPrice));
      $('.cart-total .value').html('€ ' + formatNumber(__lastPrice + fittingPrice));
      $('input[name=fitting_price]').val(fittingPrice);
      if (fittingPrice == 0) {
        $('.cart-grid input[name=delivery]').val(false);
        $('.cart-grid input[name=fitting]').val(false);
        $('#cart-subtotal-montage #shipping_price').html('Nav');
        $('.cart-total .value').html('€ ' + formatNumber(__lastPrice));
        $('input[name=fitting_price]').removeAttr('value');
      }
      $('input[name=delivery_price]').removeAttr('value');
    }
  });
}

$('.cart-summary .cart-delivery-option .custom-select').on('change', function() {
  checkShipping();
});

$('.cart-montage-choice .cart-delivery-options .cart-delivery-label input[name=cart-montage-radio]').each(function() {
  if ($(this).is(':checked') && $(this).val() == 1) {
    checkFitting();
  }
  $(this).on('change', function() {
    checkFitting();
  });
});

$('#email_notifications').on('input', function() {
  if ($(this).prop('checked')) {
    $(this).val(1);
  } else {
    $(this).val(2);
  }
});

if ($('#email_notifications').val() == 1) {
  $('#email_notifications').attr('checked', '');
} else {
  $('#email_notifications').removeAttr('checked');
}

// CART SUBMIT BUTTON
$('.checkout button').on('click', function(e) {
  // e.preventDefault();

});

function checkCart() {
  let $url = window.location.protocol + '//' + window.location.hostname + '/checkCart';

  $.ajax({
    url: $url,
    method: 'POST',
    data: {
      'csrf': $('meta[name="csrf-token"]').attr('content'),
    },
    dataType: 'JSON',
    success: function(data) {
      console.log(data);
    },
    error: function () {
      console.log("WRONG CODE");

    },
    complete: function() {
      console.log('complete');
    }
  });
}

let total_items = parseInt($('.label.js-subtotal').text().trim().replace(' Preces', ''));


// console.log($('input[name="product_ids[]"]:checked'));
// $('input[name="product_ids[]"]:checked').parent().parent().addClass('selected');
// $(document).on('change', 'input[type="checkbox"][name="product_ids[]"]', function(){
//   const $ids = $(document).find('input[type="checkbox"][name="product_ids[]"]:checked');
//   let ids_str = '';
//   $('.tire-table-row').removeClass('selected');
//   $('.tire-image-card').removeClass('selected');
//
//   if ($ids.length) {
//     $ids.each(function() {
//       $(this).parents('.tire-table-row').addClass('selected');
//       $(this).parents('.tire-image-card').addClass('selected');
//     });
//     ids_str = $.map($ids, function(id) {
//       return $(id).val();
//     }).join(',');
//   }
//   const baseUrl = window.location.href.split('#')[0];
//   window.location.replace(baseUrl + '#|' + ids_str);
// });

// $.UrlExists = function(url) {
//   var http = new XMLHttpRequest();
//   http.open('HEAD', url, false);
//   http.send();
//   return http.status!=404;
// }

$('.tire-table-checkbox').children().each(function(key, value){
  // PARSE TO INT
  products.push(parseInt($(value).val()));

  // ON SHOPPING CART BUTTON CLICK
  $(value).parent().parent().find('.cart-shopping-button').on('click', function() {

    if (!admin) {
      const tire_id = $(this).data('info');

      $.ajax({
        url: url + '/ajax',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        method: 'POST',
        data: { tire_id: tire_id },
        success: function(data)
        {
          data = JSON.parse(data);
          let cart_quantity = data.quantity;
          cart_quantity = parseInt(cart_quantity);
          let total_sum = data.total_sum;
          total_sum = parseInt(total_sum);
          const image = data.cart.options.tire.tread.tread_id;

          if (typeof image !== "undefined") {
            if (data.cart.options.tire.make_id){
              fetch(public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg',
                { method: 'GET' },)
                .then(res => {
                  if (res.ok) {
                    $('.modal-image-preview img').attr('src', public_url + data.cart.options.image + '/tread/' + data.cart.options.tire.make_id + '-o.jpg');
                  } else {
                    $('.modal-image-preview img').attr('src', 'https://www.r1-dev.area.lv/public/img/p/en-default-home_default.jpg');
                  }
                });
            }
          }

          // TIRE IMAGE INSIDE MODAL
          $('.modal-product-info .product-name').html(data.cart.name);
          $('.modal-product-info .product-price').html(parseInt(data.cart.options.tire.price2)).attr('data-price', parseInt(data.cart.options.tire.price2));
          $('.modal-product-info .product-width').html(data.cart.options.tire.d1);
          $('.modal-product-info .product-height').html(data.cart.options.tire.d2);
          $('.modal-product-info .product-radius').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-type').html(data.cart.options.tire.d3);
          $('.modal-product-info .product-li').html(data.cart.options.tire.li);
          $('.modal-product-info .product-si').html(data.cart.options.tire.si);
          $('.cart-content .cart-products-total').html(total_sum);
          $('.modal-product-info .product-qty').html($('.modal-product-info .product-qty').attr('data-qty')).attr('data-qty', parseInt(data.quantity));
          $('span.cart-products-count').html('(' + cart_quantity + ')');
          $('.blockcart.cart-preview').removeClass('inactive').addClass('active');
          $('.blockcart.cart-preview .header').empty();
          $('<a rel="nofollow" href="' + grozs_url + '"><i class="material-icons shopping-cart">shopping_cart</i><span class="hidden-sm-down">Grozs: </span><span class="cart-products-count">(' + cart_quantity + ')</span></a>').appendTo('.blockcart.cart-preview .header');
        }
      });
    } else {
      // IF ADMIN
      const tire_data = $(this).parent().parent().parent();
      // console.log('tire_data: ', tire_data);
      $('.popup input[name=prod]').val($('.table-tire-name-cell a', tire_data).data('content'));
      $('.popup input[name=price]').val($('.tire-price-red', tire_data).html().replace('€ ', ''));
      $('.popup input[name=qty]').val(4);
      $('.popup input[name=total]').val(parseInt($('.tire-price-red', tire_data).html().replace('€ ', '')) * $('.popup input[name=qty]').val());
      $('.popup input[name=user]').val(user).attr('readonly', true).prop('readonly', true);
      $('.popup input[name=article]').val($('.table-tire-name-cell a', tire_data).data('article'));

      calcData = {
          'article': $('.table-tire-name-cell a', tire_data).data('article'),
          'qty': 4,
          'user': user,
          'prod': $('.table-tire-name-cell a', tire_data).data('content'),
          'price': $('.tire-price-red', tire_data).html().replace('€', ''),
      }

      const urlData = new URLSearchParams(calcData).toString();

      popCalc('/testing3',780,400, urlData);


    }

  })
});

$('#facet_availability li label').on('click', function() {
  let $colors = [];
  const $checked = $('#facet_availability li label .custom-checkbox input:checked');

  if ($checked.length === 0) {
    $('.tires-table').each(function() {
      $(this).children('#tires-table-body').children('tr').each(function() {
        $(this).show();
      });
      $(this).show().prev().show();
    });

    $('.grid-view-link').each(function() {
      $(this).show();
    })

    $('.tire-brand-name.grid-t').each(function(){
      $(this).show();
    })

    return true;
  }

  $checked.each(function() {

    let $id = $(this).attr('class');
    $colors.push($id);
    switch ($id) {
      case 'green':
      case 'yellow': {
        $colors.push('half-' + $id);
        break;
      }
    }
    $colors = [...new Set($colors)];

    $('.tires-table').each(function() {
      $(this).children('#tires-table-body').children('tr').each(function() {
        $(this).parent().parent().hide().prev().hide();
        $(this).hide();
        if ($colors.includes($(this).find('.dot .sort-order').text().toLowerCase())) {
          $(this).show();
        }
      })
      let length = $(this).children('#tires-table-body').children().filter(function() {
        return $(this).css("display") !== "none";
      }).length;
      if (length !== 0) {
        $(this).show().prev().show();
      }
    });



    $('.row.grid-ex.pr-1').each(function(){
      if($(this).find('a.grid-view-link:visible').length === 0) {

      }
    })

    $('.grid-view-link').each(function() {

        $(this).hide();

        if ($colors.includes($(this).find('.grid-dot .sort-order').text().toLowerCase())) {
          $(this).show();
        }

        let length = $(this).filter(function() {
          return $(this).css("display") !== "none";
        }).length;

        if (length !== 0) {
          $(this).show();
        }

    });
  });

});

// SHOW LIST VIEW
$('div.can-collapse span.show_list').on('click', function(){
  $('#js-product-list').show();
  $('.tire-image-container').hide();
  $(this).addClass('active');
  $('span.show_grid').removeClass('active');
  localStorage.setItem("show_type", "list");
});

// SHOW GRID VIEW
$('div.can-collapse span.show_grid').on('click', function(){
  $('.tire-image-container').show();
  $('#js-product-list').hide();
  $(this).addClass('active');
  $('span.show_list').removeClass('active');
  localStorage.setItem("show_type", "grid");
});

// SHOW VIEW DEPENDING ON LOCAL STORAGE VALUE
if (localStorage.getItem('show_type') == 'grid') {
  $('div.can-collapse span.show_grid').click();
}
if (localStorage.getItem('show_type') == 'list') {
  $('div.can-collapse span.show_list').click();
}

let showTires = false;
let showDisc = false;
let showService = false;
let showInfo = false;

let showCode = false;
let showFuel = false;
let showWetSurface = false;

function showRiepasDropdown(){
  showTires = !showTires;
  if (showTires) {
    $('.dropdown-options.riepas').slideDown();
    $('span.riepas').text('keyboard_arrow_up');
  } else {
    $('.dropdown-options.riepas').slideUp();
    $('span.riepas').text('keyboard_arrow_down');
  }
}

function showDiskiDropdown(){
  showDisc = !showDisc;
  if (showDisc) {
    $('.dropdown-options.diski').slideDown();
    $('span.diski').text('keyboard_arrow_up');
  } else {
    $('.dropdown-options.diski').slideUp();
    $('span.diski').text('keyboard_arrow_down');
  }
}

function showServissDropdown(){
  showService = !showService;
  if (showService) {
    $('.dropdown-options.serviss').slideDown();
    $('span.serviss').text('keyboard_arrow_up');
  } else {
    $('.dropdown-options.serviss').slideUp();
    $('span.serviss').text('keyboard_arrow_down');
  }
}

function showInfoDropdown(){
  showInfo = !showInfo;
  if (showInfo) {
    $('.dropdown-options.info').slideDown();
    $('span.info').text('keyboard_arrow_up');
  } else {
    $('.dropdown-options.info').slideUp();
    $('span.info').text('keyboard_arrow_down');
  }
}

$('h1.facet-hover').each(function() {
  $(this).on('click', function() {
    $(this).parent().children('ul.collapse').slideToggle();
    if ($(this).children('span').text() == 'keyboard_arrow_down')
      $(this).children('span').text('keyboard_arrow_up');
    else
      $(this).children('span').text('keyboard_arrow_down');
  });
})

let __count = $('.cart-item-table').each(function() {}).length;

$('.cart-options label input').each(function() {
  let __total = parseInt($('#cart-subtotal-products .js-subtotal').html().trim().replace(' Preces', ''));
  if (parseInt($(this).filter(':checked').val()) === 3) {
    checkShipping(__total);
    deliveryOptionDisabledFields.each( function() {
      deliveryOptionDisabledFields.prop('disabled', false);
    });
    $('.cart-delivery-option').show();
    $('#cart-subtotal-shipping').show();
    $('.cart-montage-choice').hide();
    $('#cart-subtotal-montage').hide();
  } else {

    deliveryOptionDisabledFields.each( function() {
      deliveryOptionDisabledFields.prop('disabled', true);
    });
    $('.cart-montage-choice').show();
    $('#cart-subtotal-montage').show();
    $('.cart-delivery-option').hide();
    $('#cart-subtotal-shipping').hide();
  }
  $(this).click(function() {
    if(parseInt($(this).val()) === 3){
      checkShipping(__total);
      deliveryOptionDisabledFields.each( function() {
        deliveryOptionDisabledFields.prop('disabled', false);
      });
      $('.cart-delivery-option').show();
      $('#cart-subtotal-shipping').show();
      $('.cart-montage-choice').hide();
      $('#cart-subtotal-montage').hide();
    } else {

      checkFitting(__total);

      deliveryOptionDisabledFields.each( function() {
        deliveryOptionDisabledFields.prop('disabled', true);
      });
      $('.cart-montage-choice').show();
      $('#cart-subtotal-montage').show();
      $('.cart-delivery-option').hide();
      $('#cart-subtotal-shipping').hide();

      if (__count > 1) {
        $('.cart-montage-choice').hide();
        $('#cart-subtotal-montage').hide();
      }

    }
  })
  // console.log($(this));
});

if (__count > 1) {
  $('.cart-montage-choice').hide();
  $('#cart-subtotal-montage').hide();
}

$('.password-eye').on('click', function() {
  $('span i', this).toggleClass("fa-eye-slash fa-eye");
  if ($(this).siblings().attr('type') === 'password'){
    $(this).siblings().attr('type', 'text');
  } else {
    $(this).siblings().attr('type', 'password');
  }
});

function delay(callback, ms) {
  let timer = 0;
  return function() {
    let context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}


$('input[type=password].password-confirmation').keyup(delay(function(e) {
  if ($(this).val().length >= 8){
    $('.invalid-password').hide();
  }

  if ($('#password').val().length < 8){
    $('.short-password').show();
  } else {
    $('.short-password').hide();
  }

  if ($('#password').val() === $('#password-confirm').val()){
    $('.form-footer').children('button').prop('disabled', false);
    $('.password-error').hide();
    return;
  }
  $('.form-footer').children('button').prop('disabled', true);
  $('.password-error').show();
}, 500));

$(document).ready(function() {
  // $('#tires-table tbody tr').change(function() {
  //   $('#show-selected-checkbox').attr('disabled', $('th.tire-table-checkbox input:checked').length == 0);
  // });

  if (!$('.row.grid-ex.pr-1').find('.green').length){
    $('input#facet_availability_0').prop('disabled', true);
  }
  if (!$('.row.grid-ex.pr-1').find('.yellow').length){
    $('input#facet_availability_1').prop('disabled', true);
  }
  if (!$('.row.grid-ex.pr-1').find('.red').length){
    $('input#facet_availability_2').prop('disabled', true);
  }

  $(document).scroll(function () {
    var y = $(this).scrollTop();
    if (y > 100) {
      $('.back-to-top-button').fadeIn();
    } else {
      $('.back-to-top-button').fadeOut();
    }
  });

  if ($('section#products').is(':visible')) {
    if (!$('.tire-table-row').length) {
      $('section#main').append('<div class="mt-1 alert alert-danger"><b>Atvainojiet,</b> taču šāds izmērs nav pieejams.</div>');
    }
  }

  const rows = $(".tire-table-row");
  const rowsGrid = $("a.grid-view-link");

  $('.tire-table-checkbox').each(function(){
    if($(this).is(':checked')){
      $('input#show-selected-checkbox').prop( "disabled", false );
    }
  })

  $("#show-selected-checkbox").on("click",function() {
    if ($(this).is(':checked')) {
      // IF THERES NO CHECKBOX CHECKED
      if (!window.location.hash.has(',atlase')) {
        const linkHash = window.location.hash;
        window.location.replace(linkHash + ',atlase');
      }
      rows.each(function() {
        $(this).hide();
        if ($(this).hasClass('selected')) {
          $(this).show();
        }
      });
      rowsGrid.each(function() {
        $(this).hide();

        if ($(this).children().hasClass('selected')) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });

      $('.tires-table').each(function() {
        $(this).children('#tires-table-body').each(function() {
          let list_count = $(this).children('.tire-table-row').filter(function() {
            return $(this).css('display') !== 'none';
          }).length;
          if (list_count == 0) {
            $(this).parent().prev().hide();
            $(this).parent().hide();
          }
        });
      });

      $('.grid-ex').each(function() {
        let grid_count = $(this).children('.grid-view-link').filter(function() {
          return $(this).css('display') !== 'none';
        }).length;
        if (grid_count == 0) {
          $(this).prev().hide();
          $(this).hide();
        }
      });

    } else {
      // IF THERES NO CHECKBOX CHECKED
      if (window.location.hash.has(',atlase')) {
        const baseUrl = window.location.href;
        window.location.hash = window.location.hash.replace(',atlase', '');
        // window.location.replace('atlase', '');
      }

      $('.grid-ex').show();
      $('.tires-table, .tire-brand-name').show();
      rows.show();
      rowsGrid.show();
    }
  });

  // if (window.location.hash.indexOf('o') != -1){
  //   $('#show-selected-checkbox').trigger('click');
  // }

  // if (!window.location.hash) {
  //   $('input#show-selected-checkbox').prop( "disabled", true );
  //   $('.show-selected-checkbox-li').css('pointer-events', 'none');
  // } else {
  //   $('input#show-selected-checkbox').prop( "disabled", false );
  //   $('.show-selected-checkbox-li').css('pointer-events', 'auto');
  // }
  //
  // $('#products input[type=checkbox]').on('change', function(){
  //   if (!window.location.hash) {
  //     $('input#show-selected-checkbox').prop( "disabled", true );
  //   } else {
  //     $('input#show-selected-checkbox').prop( "disabled", false );
  //     $('.show-selected-checkbox-li').css('pointer-events', 'auto');
  //   }
  // });

  // IF MOBILE THEN SET LOCAL STORAGE TO DISPLAY GRID VIEW
  // if (navigator.userAgentData.mobile ) {
  //   $('.pak-table').css('overflow', 'scroll');
  // }
  if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && !localStorage.getItem('show_type')){
    $('div.can-collapse span.show_grid').click();
  }

  if (window.location.hash.has(',atlase')) {
    $('#show-selected-checkbox').click();
  }

});

// var interval;
//
// function countdown() {
//   clearInterval(interval);
//   interval = setInterval( function() {
//     var timer = $('#timeout .modal-body .time').data('start');
//     timer = timer.split(':');
//     var minutes = timer[0];
//     var seconds = timer[1];
//     seconds -= 1;
//     if (minutes < 0) return;
//     else if (seconds < 0 && minutes != 0) {
//       minutes -= 1;
//       seconds = 59;
//       minutes = '0' + minutes;
//     }
//     else if (seconds < 10 && length.seconds != 2) seconds = '0' + seconds;
//
//     $('#timeout .modal-body .time').html(minutes + ':' + seconds);
//
//     if (minutes == 0 && seconds == 0) {
//       clearInterval(interval);
//       Swal.fire({
//         title: 'Kļūda!',
//         text: 'Sessijas laiks ir beidzies',
//         icon: 'error',
//         confirmButtonText: 'OK'
//         }).then((result) => {
//         if (result.isConfirmed) {
//           window.location.href = '/logout';
//         }
//       });
//     }
//   }, 1000);
// }

// if (user) {
//
//   let sessionCheck;
//
//   function checkSession() {
//     sessionCheck = setInterval(function() {
//       $.get('/testing1', function(data) {
//         if (data == 1) {
//           $('#timeout').modal({
//             keyboard: false,
//             backdrop: false,
//           }).modal('show');
//           countdown();
//           clearInterval(sessionCheck);
//         }
//       });
//     }, 3000);
//   }
//
//   checkSession();
//
//   $('#timeout #stay').on('click', function() {
//     $.post('/testing1', function(data, status) {
//       if (data == 1) {
//         clearInterval(sessionCheck);
//         clearInterval(interval);
//         $('#timeout').modal('hide');
//         $('#timeout .modal-body .time').html($('#timeout .modal-body .time').data('start'));
//         checkSession();
//       }
//     });
//   });
// }

$('.cart-card .checkout-buttons .form-check input[name=payment]').each(function() {
  $(this).on('change', function() {
    $(this).prop('checked', true);
    if ($(this).val() == 1 || $(this).val() == 2) {
      $('.btn-checkout').last().attr('name', 'end').val('end').text('Turpināt');
    } else {
      $('.btn-checkout').last().attr('name', 'pay').val('pay').text('Apmaksāt');
    }
  });
});

// Validācija
document.addEventListener("DOMContentLoaded", function() {
  // Visi input fieldi
  let elements = $('input');
  for (let i = 0; i < elements.length; i++) {
    // Noņem popup kad hovero pāri required fieldam
    elements[i].title = '';
    // Uz invalīda fielda atgriež validācijas tekstu
    elements[i].oninvalid = function(e) {
      e.target.setCustomValidity("");
      if (!e.target.validity.valid) {
        e.target.setCustomValidity("Šis lauks nedrīkst būt tukšs!");
      }
    };
    elements[i].oninput = function(e) {
      e.target.setCustomValidity("");
    };
  }
})

// jQuery.extend(jQuery.validator.messages, {
//   required:   "This field is required.",
//   remote:     "Please fix this field.",
//   email:      "Please enter a valid email address.",
//   url:        "Please enter a valid URL.",
//   date:       "Please enter a valid date.",
//   dateISO:    "Please enter a valid date (ISO).",
//   number:     "Please enter a valid number.",
//   digits:     "Please enter only digits.",
//   creditcard: "Please enter a valid credit card number.",
//   equalTo:    "Please enter the same value again.",
//   accept:     "Please enter a value with a valid extension.",
//   maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
//   minlength: jQuery.validator.format("Please enter at least {0} characters."),
//   rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
//   range: jQuery.validator.format("Please enter a value between {0} and {1}."),
//   max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
//   min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
// });

$('.dropdown-item.sizeCalc').on('click', function(e) {
  e.preventDefault();
  let url = $(this).attr('href');
  popCalc(url,600,450);
});

function popCalc(url,popW,popH, data){

  w = screen.width;
  h = screen.height;

  let leftPos = Math.round((w-popW)/2);
  let topPos = Math.round((h-popH)/2);

  let id=Math.floor(Math.random()*10000);
  let strWindowFeatures = "toolbar=no,scrollbars=no,location=no,resizable=yes,width=" + popW + ",height=" + popH + ",top=" + topPos + ",left=" + leftPos;

  if (data) {
    pops=window.open(url + '?' +  data, id, strWindowFeatures);
  } else {
    pops=window.open(url, id, strWindowFeatures);
  }

  if (pops.opener == null)
    pops.opener = self;

}


$('button.offer-slot-link').on('click', function() {
  let discount = $(this).text().match(/\d+/)[0];
  if ($('div.alert.alert-warning.discount-alert').length === 0 ){
    $('.modal-dialog').find('.form-group.services')
      .append("<div class='alert alert-warning discount-alert' style='font-size: 14px;'><b>Šajā pieraksta laikā tiek piemērota atlaide (-" + discount + "% darbam!!!)</b></div>");
  }
})

// if ($('.code-dropdown-btn').find('span.code-dropdown').text() == 'keyboard_arrow_up') {
//   localStorage.setItem('code-dropdown', 'true');
// }
$('.code-dropdown-btn').on('click', function() {
  if ($(this).find('span.code-dropdown').text() == 'keyboard_arrow_up') {
    localStorage.setItem('code-dropdown', 'true');
  } else {
    localStorage.removeItem('code-dropdown');
  }
})

$('.fuel-eco-dropdown-btn').on('click', function() {
  if ($(this).find('span.fuel-efficiency-dropdown').text() == 'keyboard_arrow_up') {
    localStorage.setItem('fuel-eco-dropdown', 'true');
  } else {
    localStorage.removeItem('fuel-eco-dropdown');
  }
})

$('.wet-surface-dropdown-btn').on('click', function() {
  if ($(this).find('span.wet-surface-dropdown').text() == 'keyboard_arrow_up') {
    localStorage.setItem('wet-surface-dropdown', 'true');
  } else {
    localStorage.removeItem('wet-surface-dropdown');
  }
})

$('.type-dropdown-btn').on('click', function() {
  if ($(this).find('span.type-dropdown').text() == 'keyboard_arrow_up') {
    localStorage.setItem('type-dropdown', 'true');
  } else {
    localStorage.removeItem('type-dropdown');
  }
})

if (localStorage.getItem('type-dropdown') === 'true') {
  $('.type-dropdown-btn').click();
}

if (localStorage.getItem('code-dropdown') === 'true') {
  $('.code-dropdown-btn').click();
}

if (localStorage.getItem('fuel-eco-dropdown') === 'true') {
  $('.fuel-eco-dropdown-btn').click();
}

if (localStorage.getItem('wet-surface-dropdown') === 'true') {
  $('.wet-surface-dropdown-btn').click();
}

$('.popup-code-dropdown').on('click', function() {
  // console.log();
  if ($(this).parent().next('ul').is(":visible")){
    $(this).css('transform', 'rotate(0deg)');
  } else {
    $(this).css('transform', 'rotate(180deg)');
  }
  $(this).parent().next('ul').slideToggle();
});
