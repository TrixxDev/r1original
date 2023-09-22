$(document).ready(function() {

  $.fn.classChange = function(cb) {
    return $(this).each((_, el) => {
      new MutationObserver(mutations => {
        mutations.forEach(mutation => cb && cb(mutation.target, $(mutation.target).prop(mutation.attributeName)));
      }).observe(el, {
        attributes: true,
        attributeFilter: ['class'] // only listen for class attribute changes
      });
    });
  }

  $(".pace").classChange((el, newClass) => {
      if (newClass) {
        $('body .loading').first().fadeOut(function() { $(this).remove(); });
      }
    }
  );

  // Connect to the WebSocket server
  const socket = new WebSocket('ws://r1riepas.lv:3500');

  function truncateCharacters(text, limit, ellipsis = '...', strip = 0) {
    if (text.length > limit) {
      text = $.trim(text.substring(0, limit - strip)) + ellipsis;
    }
    return text;
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  let iorder;
  let queue_id;
  let date;
  let time;
  let office;
  let slot;
  let car_brand;
  let phone;
  let rimsWith;
  let temp_nr;
  let selected__office;

  // set the modal menu element
  // const $targetEl = document.getElementById('reservation');

  // // options with default values
  // const options = {
  //     backdrop: 'dynamic',
  //     backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
  //     closable: true,
  //     onHide: () => {
  //         $('body').removeClass('removeScroll');
  //     },
  //     onShow: () => {
  //         $('body').addClass('removeScroll');
  //     },
  //     onToggle: () => {
  //         console.log('modal has been toggled');
  //     }
  // };
  //
  // const modal = new Modal($targetEl, options);

  $(document).on('click', '#reservation #close-modal', function() {
    $('#reservation').modal('hide');
  });

  $(document).on('click', '#reservation .finish-footer #close-modal', function() {
    $('#reservation .rims_with, #reservation .temp_save_nr').hide();
    $('.modal-body.finish, .finish-footer').remove();
    $('.reservation-modal-body').slideDown();
    $('.reservation-modal-footer #submit-reservation').show();
    $('.reservation-modal-footer #close-modal').text('Atcelt');
    $('#reservation #modalTitle.title-finish').remove();
    $('#reservation #modalTitle').slideDown();
  });

  $(document).on('click', '.time-status.discount', function() {
    let discount_text = $('button.discount-slot', this).text();
    if ($('div.alert.alert-warning.discount-alert').length === 0 ){
      $('.modal-dialog').find('.form-group.services')
        .prepend("<div class='alert alert-warning discount-alert' style='font-size: 14px;'><b>Šajā pieraksta laikā tiek piemērota atlaide (" + discount_text + ")</b></div>");
    }
  });

  $(document).on('click', '.time-status', function() {

    $('#reservation .loader-block').show();

    iorder = $(this).attr('data-iorder');
    queue_id = $(this).parent().attr('data-queue-id');
    date = $(this).parent().parent().attr('data-date');
    time = $(this).children('.time-slot').html();
    office = $(this).parent().attr('class').replace('table office_', '');
    slot = $(this);

    if ($(this).hasClass('time-free') || $(this).hasClass('time-offer')) {
      $('#reservation').modal('show');
    }

    setTimeout(function() {
      $('.datedayOfWeek').text(slot.parent().parent().parent().prev().text());
      $('.timeOfDay').text($('.time-slot', slot).text());
      $('.officeTitle').text(slot.parent().children().first().text());

      $.each($('#record-modal .services li'), function(index, value) {
        $(value).find('input').attr('disabled', true).prop('disabled', true).attr('checked', false).prop('checked', false);
      });

      $('#reservation #service .form-check').each(function() {
        $(this).find('input').on('click', function() {
          switch ($(this).val()) {
            case '1': {
              $('#reservation .rims_with').show();
              $('#reservation .temp_save_nr').hide().val('');
              break;
            }
            case '2': {
              $('#reservation .temp_save_nr').show();
              $('#reservation .rims_with').hide();
              $('#reservation .rims_with input[name="rims_with_input"]').each(function () {
                $(this).attr('selected', false).prop('selected', false);
              });
              break;
            }
            default: {
              $('#reservation .rims_with, #reservation .temp_save_nr').hide();
            }
          }
        })
      });


      if ($(slot).attr('data-moto') === 'true') {
        $.each($('#reservation #service .form-check'), function(index, value) {
          $(value).find('input').attr('disabled', true).prop('disabled', true).attr('checked', false).prop('checked', false);
        });
        $('#reservation').find('input[data-moto]').removeAttr('disabled').prop('disabled', false).attr('checked', true).prop('checked', true);
      } else if ($(slot).attr('data-ac') === 'true') {
        $.each($('#reservation #service .form-check'), function(index, value) {
          $(value).find('input').attr('disabled', true).prop('disabled', true).attr('checked', false).prop('checked', false);
        });
        $('#reservation').find('input[data-ac]').removeAttr('disabled').prop('disabled', false).attr('checked', true).prop('checked', true);
      } else {
        let __timeSlots = $(slot).parent().parent();
        $.each($('#reservation #service .form-check'), function(index, value) {
          $(value).find('input').attr('disabled', false).prop('disabled', false).attr('checked', false).prop('checked', false);
        });
        if ($(__timeSlots).find('.time-status[data-moto]').first().length > 0) {
          $('#reservation #service').find('input[data-moto]').attr('disabled', true).prop('disabled', true);
        }
        if ($(__timeSlots).find('.time-status[data-ac]').first().length > 0) {
          $('#reservation #service').find('input[data-ac]').attr('disabled', true).prop('disabled', true);
        }
      }

      $('#reservation .loader-block').hide();
    }, 1000);

  });

  $('#reservation').on('hide.bs.modal', function () {
    $('#reservation form').trigger('reset');
    $('#reservation .alert').remove();
    $('#reservation .rims_with, #reservation .temp_save_nr').hide();
    $('#brand, #model, #phone, #email').removeAttr('placeholder');
    $('body').removeClass('removeScroll');
  }).on('show.bs.modal', function() {
    $('body').addClass('removeScroll');
  });

  $('#reservation button#submit-reservation').on('click', function(e) {
    e.preventDefault();

    car_brand = $('#reservation input#brand').val();
    car_model = $('#reservation input#model').val();
    rimsWith = $('#reservation .rims_with input[name="rims_with_input"]:checked').val();
    temp_nr = $('#reservation .temp_save_nr input#save_nr').val();
    phone = $('#reservation input#phone').val();
    let lic_plate = $('#reservation input#reg_nr').val();
    let plate = phone.substr(-3);
    plate = parseInt(plate);
    plate = $.trim(plate);
    let service = $('#service').find('input[name="serviceOption"]:checked').val();
    let user_comment = $('#reservation textarea#comment').val();
    let name = $('#reservation input#name').val();
    let email = $('#reservation input#email').val();

    let formData = 'car_brand=' + car_brand + '&car_model=' + car_model + '&rimsWith=' + rimsWith + '&temp_nr=' + temp_nr + '&lic_plate=' + lic_plate + '&service=' + service + '&user_comment=' + user_comment + '&name=' + name + '&phone_number=' + phone + '&email=' + email;

    let dopParams = {
      iorder: iorder,
      queue_id: queue_id,
      date: date,
      time: time,
      office: office,
    };

    $.ajax({
      url: '/pieraksts/fillSlot',
      method: 'POST',
      data: {formData: formData, dopParams: dopParams},
      beforeSend: function() {
        $('#reservation .loader-block').show();
      },
      success: function(data) {
        setTimeout(function() {
          data = JSON.parse(data);
          let text = '';
          $.each(data, function(index, value) {
            if (index === 'errors') {
              $.each(value, function(index, item) {
                if (index == 'car_brand') {
                  $('#brand').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'car_model') {
                  $('#model').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'lic_plate') {
                  $('#reg_nr').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'lic_plate') {
                  $('#lic_plate').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'phone_number') {
                  $('#phone').attr('placeholder', 'Jābūt aizpildītam!');
                }
              });
              $('#submit-reservation').removeAttr('disabled');
              $('#close-modal').removeAttr('disabled');
            }
            if (index === 'message') {
              $('#submit-reservation').removeAttr('disabled');
              $('#close-modal').removeAttr('disabled');

              let successText = truncateCharacters($.trim(car_brand),8,'&mldr;',1) + ' xxxxx' + plate;

              $('.reservation-modal-body').slideUp();
              $('#modalTitle').first().slideUp();
              $('<h5 class="modal-title title-finish" id="modalTitle">Pieraksts</h5>').insertAfter('#modalTitle');
              $('.reservation-modal-footer #submit-reservation').hide();
              $('.reservation-modal-footer #close-modal').text('Aizvērt');
              $('<div class="modal-body finish">' + data.message + '</div><div class="modal-footer finish-footer"><button type="button" class="btn btn-secondary" id="close-modal" style="margin-right: 10px;">Aizvērt</button></div>').insertAfter($('#modalTitle').parent()).css('display', 'none').slideDown();
              slot.removeClass('time-free').removeClass('time-offer').addClass('time-taken');
              slot.find('button').fadeOut().remove();
              $('#brand, #model, #phone, #email').removeAttr('placeholder');
              $('#reservation form').trigger('reset');
              $('#reservation .rims_with, #reservation .temp_save_nr').hide();

              let wsParams = {
                iorder: iorder,
                queue_id: queue_id,
                date: date,
                office: office,
                car_brand: car_brand,
                car_model: car_model,
                plate: plate,
                fullNumber: phone,
              };

              let wsData = {
                wsParams: wsParams,
                new_slot_client: data.new_slot_client,
              };

              socket.send(JSON.stringify(wsData));
              //
              // slot.find('button').fadeOut().remove();
              // let successText = truncateCharacters($.trim(car_brand),8,'&mldr;',1) + ' xxxxx' + plate;
              // slot.append('<span class="bg-gray-300 text-gray py-2 px-4 status" style="cursor: default;">' + successText + '</span>').fadeIn();
            }
            if (index === 'alertMessage') {
              let form = $('#reservation form');
              $('#reservation .error-list').html('').fadeOut();
              form.slideUp();
              $('#reservation #warning-alert').slideDown();
              $('#reservation #warning-alert .text-message').html(value);
            }
          });
        }, 1000);
      },
      complete: function() {
        setTimeout(function() {
          $('#reservation .loader-block').hide();
        }, 1000);
      }
    });

  });




  $(document).on('change', '#mobile-filiale .filiale_grid .filiale_card', function() {
    selected__office = $('input', this).val();
    let __selected;

    if ($('#mobile-main #mobile-slots-choice').text().trim().length > 0) {
      __selected = 1;
    } else {
      __selected = 0;
    }

    $.ajax({
      url: '/pieraksts/showMobileQueues',
      data: {office_id: selected__office},
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      beforeSend() {
        $('#mobile-filiale .filiale_grid .filiale_card').attr('disabled', true).prop('disabled', true);
        (__selected === 1) ? $('#mobile-slots-choice').slideUp() : '';
      },
      success: function(data) {
        $('#mobile-main #mobile-slots-choice .reservation').html(data);
        $('.reservation .time-list').each(function() {
          let __motoCount = $(this).find('.moto').first().length;
          let __acCount = $(this).find('.conditioner').first().length;

          if (__motoCount > 0) {
            $(this).attr('data-moto', 1);
          }
          if (__acCount > 0) {
            $(this).attr('data-ac', 1);
          }
        });
      },
      complete() {
        $('#mobile-filiale .filiale_grid .filiale_card').removeAttr('disabled').prop('disabled', false);
        $('#mobile-slots-choice').slideDown();
      }
    });
  });

  $(document).on('click', '.reservation .time-list .time-slot .active', function() {
    $('.reservation .time-list .time-slot .slot').removeClass('selected');
    $(this).addClass('selected');

    iorder = $(this).parent().attr('data-iorder');
    queue_id = $(this).parent().attr('data-queue-id');
    date = $(this).parent().parent().attr('data-date');
    time = $(this).children('.time-span').html();
    office = selected__office;
    slot = $(this);

    $('#mobile-reservation-form').show();
    $([document.documentElement, document.body]).animate({
      scrollTop: $('#mobile-reservation-form').offset().top - 30});

    $('#mobile-reservation-form .purpose #mobile-service select[name="serviceOption"]').on('change', function() {
      switch ($('option:selected', this).val()) {
        case '1': {
          $('#mobile-reservation-form .rims-with-mobile').show();
          $('#mobile-reservation-form .rims-storageBin').hide();
          break;
        }
        case '2': {
          $('#mobile-reservation-form .rims-with-mobile').hide();
          $('#mobile-reservation-form .rims-storageBin').show();
          break;
        }
        default: {
          $('#mobile-reservation-form .rims-with-mobile, #mobile-reservation-form .rims-storageBin').hide();
          break;
        }
      }
    });
  });

  $(document).on('click', '.available.slot', function() {
    let __timeSlots = $(this).parent().parent();
    $('#mobile-service select[name=serviceOption] option').each(function() {
      $(this).removeAttr('selected');
      $(this).attr('disabled', false).prop('disabled', false);
      if ($(__timeSlots).attr('data-moto')) {
        if ($(this).attr('data-moto')) {
          $(this).attr('disabled', true).prop('disabled', true);
        }
      }
      if ($(__timeSlots).attr('data-ac')) {
        if ($(this).attr('data-ac')) {
          $(this).attr('disabled', true).prop('disabled', true);
        }
      }
    });
    $('#mobile-service select[name=serviceOption] option.disabled').attr('disabled', true).prop('disabled', true);
    $('#mobile-service select[name=serviceOption]').prop('selectedIndex',0);
  });
  $(document).on('click', '.slot.conditioner', function() {
    $('#mobile-service select[name=serviceOption] option').each(function() {
      $(this).removeAttr('disabled').removeProp('disabled');
      if (!$(this).attr('data-ac')) {
        $(this).attr('disabled', true).prop('disabled', true);
      } else {
        $(this).attr('selected', true).prop('selected', true);
      }
    });
  });
  $(document).on('click', '.slot.moto', function() {
    $('#mobile-service select[name=serviceOption] option').each(function() {
      $(this).removeAttr('disabled').removeProp('disabled');
      if (!$(this).attr('data-moto')) {
        $(this).attr('disabled', true).prop('disabled', true);
      } else {
        $(this).attr('selected', true).prop('selected', true);
      }
    });
  });


  $(document).on('click', '#mobile-submit-reservation', function(e) {
    e.preventDefault();

    car_brand = $('#mobile-reservation-form input#mobile-brand').val();
    car_model = $('#mobile-reservation-form input#mobile-model').val();
    rimsWith = $('#mobile-reservation-form .rims-with-mobile input[name="rims_with_input"]:checked').val();
    temp_nr = $('#mobile-reservation-form .rims-storageBin input#mobile_storage_bin').val();
    phone = $('#mobile-reservation-form .phone-number input#mobile-phone').val();
    let lic_plate = $('#mobile-reservation-form input#mobile-reg_nr').val();
    let plate = phone.substr(-3);
    plate = parseInt(plate);
    plate = $.trim(plate);
    let service = $('#mobile-reservation-form .purpose #mobile-service select[name="serviceOption"] option:selected').val();
    let user_comment = $('#mobile-reservation-form textarea#mobile-comment').val();
    let name = $('#mobile-reservation-form input#mobile-name').val();
    let email = $('#mobile-reservation-form input#mobile-email').val();

    let formData = 'car_brand=' + car_brand + '&car_model=' + car_model + '&rimsWith=' + rimsWith + '&temp_nr=' + temp_nr + '&lic_plate=' + lic_plate + '&service=' + service + '&user_comment=' + user_comment + '&name=' + name + '&phone_number=' + phone + '&email=' + email;

    let dopParams = {
      iorder: iorder,
      queue_id: queue_id,
      date: date,
      time: time,
      office: office,
    };

    $.ajax({
      url: '/fillSlot',
      method: 'POST',
      data: {formData: formData, dopParams: dopParams},
      beforeSend: function () {
        $('#reservation .loader-block').show();
      },
      success: function (data) {
        setTimeout(function() {
          data = JSON.parse(data);
          let text = '';
          $.each(data, function(index, value) {
            if (index === 'errors') {
              $.each(value, function(index, item) {
                if (index == 'car_brand') {
                  $('#mobile-brand').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'car_model') {
                  $('#mobile-model').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'lic_plate') {
                  $('#mobile-reg_nr').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'lic_plate') {
                  $('#mobile-reg_nr').attr('placeholder', 'Jābūt aizpildītam!');
                }
                if (index == 'phone_number') {
                  $('#mobile-phone').attr('placeholder', 'Jābūt aizpildītam!');
                }
              });
              $('#submit-reservation').removeAttr('disabled');
              $('#close-modal').removeAttr('disabled');
            }
            if (index === 'message') {
              $('html, body').animate({
                scrollTop: $("section#mobile-main").offset().top
              });
              $('.mobile-reservation-modal-body .mobile-body').slideUp();
              $('.mobile-reservation-modal-body .mobile-body-success .alert').append(data.message);
              $('.mobile-reservation-modal-body .mobile-body-success').slideDown();
              $('#mobile-submit-reservation').slideToggle();
              $('#mobile-close-modal').slideToggle().on('click', function () {
                $(this).slideToggle();
                $('#mobile-submit-reservation').slideToggle();
                $('section#mobile-main form').trigger('reset');
                $('.mobile-body-success').slideUp();
                $('.mobile-reservation-modal-body .mobile-body').slideDown();
                $('.mobile-reservation-modal-body .mobile-body-success .alert').text('');
              });

              let wsParams = {
                iorder: iorder,
                queue_id: queue_id,
                date: date,
                office: office,
                car_brand: car_brand,
                car_model: car_model,
                plate: plate,
                fullNumber: phone,
              };

              let wsData = {
                wsParams: wsParams,
                new_slot_client: data.new_slot_client,
              };

              socket.send(JSON.stringify(wsData));
              //
              // slot.find('button').fadeOut().remove();
              // let successText = truncateCharacters($.trim(car_brand),8,'&mldr;',1) + ' xxxxx' + plate;
              // slot.append('<span class="bg-gray-300 text-gray py-2 px-4 status" style="cursor: default;">' + successText + '</span>').fadeIn();
            }
            if (index === 'alertMessage') {
              $('html, body').animate({
                scrollTop: $("section#mobile-main").offset().top
              });
              $('.mobile-reservation-modal-body .mobile-body').slideUp();
              $('.mobile-reservation-modal-body .mobile-body-success .alert').append(data.alertMessage);
              $('.mobile-reservation-modal-body .mobile-body-success').slideDown();
            }
          });
        }, 1000);
      },
      complete() {

      }
    });

  });


  let plate;

  function formatTime(date) {
    let hours = ('0' + date.getHours()).slice(-2);
    let minutes = ('0' + date.getMinutes()).slice(-2);
    return hours + ':' + minutes;
  }

  // Listen for WebSocket messages
  // socket.addEventListener('message', function(event) {
  //   const data = JSON.parse(event.data);
  //
  //   if (!data.times && !data.timeChangedState) {
  //     let slot = $('.schedule-table.dashboard .grid[data-date="' + data.wsParams.date + '"] .table[data-queue-id="' + data.wsParams.queue_id + '"] .time-status[data-iorder="' + data.wsParams.iorder + '"]');
  //     let mobile_slot = $('#mobile-slots-choice .time-list[data-date="' + data.wsParams.date + '"] .time-slot[data-queue-id="' + data.wsParams.queue_id + '"][data-iorder="' + data.wsParams.iorder + '"]');
  //
  //     if (!isNaN(data.wsParams.plate) || data.wsParams.plate != null) {
  //       plate = data.wsParams.plate;
  //     } else {
  //       plate = '';
  //     }
  //
  //     if (data.new_slot_client) {
  //       slot.removeClass('time-free').addClass('time-taken').find('button').fadeOut().remove();
  //       let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //       slot.append('<div class="slot taken-slot">' + successText + '</div>').hide().fadeIn();
  //
  //       mobile_slot.find('.slot').removeClass('active').removeClass('available').addClass('unavailable');
  //       mobile_slot.find('.slot-text').html('Aizņemts');
  //
  //     } else if (data.slot_admin.edited_slot_admin) {
  //
  //       if (data.dopParams.new_date !== data.dopParams.date || data.dopParams.new_queue !== data.dopParams.queue_id || data.dopParams.new_time !== data.dopParams.time) {
  //         slot = $('.schedule-table.dashboard .grid[data-date="' + data.dopParams.new_date + '"] .table[data-queue-id="' + data.dopParams.new_queue + '"] .time-status[data-iorder="' + data.dopParams.new_iorder + '"]');
  //         mobile_slot = $('#mobile-slots-choice .time-list[data-date="' + data.wsParams.new_date + '"] .time-slot[data-queue-id="' + data.wsParams.new_queue + '"][data-iorder="' + data.wsParams.new_iorder + '"]');
  //       }
  //
  //       if (data.status == 0) {
  //         if (data.wsParams.discount !== null) {
  //           slot.addClass('discount');
  //           slot.find('button.status').addClass('discount-slot').text(data.wsParams.discount);
  //         }
  //       } else if (data.status == 1) {
  //         if (plate === null) plate = '';
  //         if (slot.hasClass('time-taken')) {
  //           slot.find('div.slot').fadeOut().remove();
  //           let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //           slot.append('<div class="slot taken-slot">' + successText + '</div>').hide().fadeIn();
  //         } else if (slot.hasClass('time-free')) {
  //           slot.removeClass('time-free').addClass('time-taken').find('button').fadeOut().remove();
  //           let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //           slot.append('<div class="slot taken-slot">' + successText + '</div>').hide().fadeIn();
  //         } else if (slot.hasClass('time-offer')) {
  //           slot.removeClass('time-offer').addClass('time-taken').find('button').fadeOut().remove();
  //           let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //           slot.append('<div class="slot taken-slot">' + successText + '</div>').hide().fadeIn();
  //         } else if (slot.hasClass('time-closed')) {
  //           slot.removeClass('time-closed').addClass('time-taken').find('span').fadeOut().remove();
  //           let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //           slot.append('<div class="slot taken-slot">' + successText + '</div>').hide().fadeIn();
  //         } else if (slot.hasClass('time-gray')) {
  //           slot.removeClass('time-gray').addClass('time-taken').find('span').fadeOut().remove();
  //           let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //           slot.append('<div class="slot taken-slot">' + successText + '</div>').hide().fadeIn();
  //         }
  //       } else if (data.status == 3) {
  //         // Desktop version
  //         if (slot.hasClass('time-free')) {
  //           slot.removeClass('time-free').addClass('time-closed').find('button').fadeOut().remove();
  //           slot.append('<span class="slot closed-slot">Slēgts</span>').hide().fadeIn();
  //         } else if (slot.hasClass('time-taken')) {
  //           slot.removeClass('time-taken').addClass('time-closed').find('div.slot').fadeOut().remove();
  //           slot.append('<span class="slot closed-slot">Slēgts</span>').hide().fadeIn();
  //         } else if (slot.hasClass('time-offer')) {
  //           slot.removeClass('time-offer').addClass('time-closed').find('div.slot').fadeOut().remove();
  //           slot.append('<span class="slot closed-slot">Slēgts</span>').hide().fadeIn();
  //         } else if (slot.hasClass('time-gray')) {
  //           slot.removeClass('time-gray').addClass('time-closed').find('div.slot').fadeOut().remove();
  //           slot.append('<span class="slot closed-slot">Slēgts</span>').hide().fadeIn();
  //         }
  //
  //         // Mobile version
  //
  //       }
  //     } else if (data.slot_admin.moved_slot_admin) {
  //       if (data.status == 2) {
  //         console.log('status - 2, data - ' + data.dopParams.new_iorder);
  //       } else if (data.status == 1) {
  //         let new_slot = $('.schedule-table.dashboard .grid[data-date="' + data.dopParams.new_date + '"] .table[data-queue-id="' + data.dopParams.new_queue + '"] .time-status[data-iorder="' + data.dopParams.new_iorder + '"]');
  //
  //         let old_slot_classes = slot.prop('classList');
  //         let new_slot_classes = new_slot.prop('classList');
  //
  //         let old_slot_button = slot.find('div.slot').clone();
  //         let new_slot_button = new_slot.find('button.status, div.slot').clone();
  //         console.log(new_slot_button);
  //
  //         new_slot.find('.slot').first().remove();
  //         slot.find('div.slot').first().remove();
  //         new_slot.append(old_slot_button).hide().fadeIn();
  //         slot.append(new_slot_button).hide().fadeIn();
  //
  //         new_slot.attr('data-old-classes', old_slot_classes);
  //         slot.attr('data-new-classes', new_slot_classes);
  //         new_slot.removeAttr('class').attr('class', new_slot.attr('data-old-classes')).removeAttr('data-old-classes');
  //         slot.removeAttr('class').attr('class', slot.attr('data-new-classes')).removeAttr('data-new-classes');
  //
  //         // let successText = truncateCharacters($.trim(data.wsParams.car_brand), 8, '&mldr;', 1) + ' xxxxx' + plate;
  //         // if (data.dopParams.edited === true) {
  //         //     if (slot.find('span').length > 0) {
  //         //         slot.find('span').first().remove();
  //         //         new_slot.attr('class', 'time-status time-taken inline-flex').append('<span class="bg-gray-300 text-sm text-gray py-2 px-4 status" style="cursor: default;">' + successText + '</span>').hide().fadeIn();
  //         //     } else if (slot.find('button').length > 0) {
  //         //         // slot.find('button').first().remove();
  //         //     }
  //         // } else {
  //         //     if (slot.find('span').length > 0) {
  //         //         slot.find('span').first().remove();
  //         //         new_slot.attr('class', 'time-status time-taken inline-flex').append('<span class="bg-gray-300 text-sm text-gray py-2 px-4 status" style="cursor: default;">' + successText + '</span>').hide().fadeIn();
  //         //     } else if (slot.find('button').length > 0) {
  //         //         // slot.find('button').first().remove();
  //         //     }
  //         // }
  //
  //         iorder = data.dopParams.new_iorder;
  //         queue_id = data.dopParams.new_queue;
  //         date = data.dopParams.new_date;
  //         time = data.dopParams.new_time;
  //         office = data.dopParams.new_office;
  //         slot = new_slot;
  //       } else {
  //
  //       }
  //     } else if (data.slot_admin.deleted_slot_admin) {
  //       if (data.status == 2) {
  //         slot.removeClass('time-taken').addClass('time-offer');
  //         slot.find('span').remove();
  //         slot.append('<button class="bg-orange-500 hover:bg-orange-200 text-black py-2 px-4 status">' + data.comment.comment + '</button>').hide().fadeIn();
  //       } else {
  //         if (slot.hasClass('time-taken')) {
  //           slot.removeClass('time-taken').addClass('time-free');
  //         } else if (slot.hasClass('time-closed')) {
  //           slot.removeClass('time-closed').addClass('time-free');
  //         } else if (slot.hasClass('time-gray')) {
  //           slot.removeClass('time-gray').addClass('time-free');
  //         } else if (slot.hasClass('discount')) {
  //           slot.removeClass('discount');
  //         }
  //         if (slot.find('div.slot').length > 0) {
  //           slot.find('div.slot').remove();
  //         } else if (slot.find('span.slot').length > 0) {
  //           slot.find('span.slot').remove();
  //         } else {
  //           slot.find('button.status').remove();
  //         }
  //         slot.append('<button class="status free-slot-link available-slot">Brīvs</button>').hide().fadeIn();
  //       }
  //     }
  //   } else if (data.timeChangedState === 1) {
  //     $('#toasts .toast').each(function() {
  //       $(this).fadeOut(function() {
  //         $(this).remove();
  //       });
  //     });
  //     $.toast({
  //       autoDismiss: false,
  //       title: 'Paziņojums',
  //       message: 'Notika izmaiņas darba laikos, atjaunojiet lapu<br><button onclick="location.reload()" class="btn btn-success" style="margin-top: 5px;">Pārlādēt</button>'
  //     });
  //   } else {
  //     if (data.times.changeVal) {
  //
  //       let action;
  //       let closedTime = false;
  //       if (data.times.newCloseTime > data.times.oldCloseTime) {
  //         action = 'add';
  //       } else if (data.times.newCloseTime === data.times.oldCloseTime) {
  //         action = null;
  //       } else {
  //         action = 'remove';
  //       }
  //
  //       let newCloseTime = data.times.newCloseTime;
  //       let oldCloseTime = data.times.oldCloseTime;
  //
  //       // Convert the time strings to Date objects for easier manipulation
  //       let startTime = new Date('2000-01-01T' + newCloseTime + ':00');
  //       let endTime = new Date('2000-01-01T' + oldCloseTime + ':00');
  //
  //       // Define the time step in milliseconds (15 minutes)
  //       let timeStep = data.times.timeStep * 60 * 1000;
  //
  //       let times = [];
  //
  //       // Start the loop from the start time and increment by the time step
  //       for (let currentTime = startTime; currentTime < endTime; currentTime.setTime(currentTime.getTime() + timeStep)) {
  //         // Get the current time in the desired format (e.g., HH:mm)
  //         let formattedTime = currentTime.getHours() + ':' + ('0' + currentTime.getMinutes()).slice(-2);
  //
  //         // Add the formatted time to the array
  //         times.push(formattedTime);
  //       }
  //
  //       // $('.grid[data-date="' + data.times.date + '"] .table[data-queue-id="' + data.times.queue_id + '"] .time-status .time-slot').each(function() {
  //       //     if (times.includes($(this).html())) {
  //       //         $(this).parent().fadeOut(function() {
  //       //             $(this).remove();
  //       //         })
  //       //     }
  //       // });
  //
  //       if (times.includes($('.modal#reservation .timeOfDay').html()) && $('.modal#reservation').is(':visible')) {
  //         closedTime = true;
  //         $('.modal#reservation #submit-reservation').remove();
  //         $('.modal#reservation #close-modal').text('Aizvērt');
  //         $('.modal#reservation .reservation-modal-body .container-fluid').slideUp();
  //
  //         let alertMessage = '<div class="container-fluid"><div class="row"><div class="col-md-12">' +
  //           '<div class="alert alert-warning">Atvainojamies, darba laiks saīsinājās, lūgums izvēlēties citu pieraksta laiku</div>' +
  //           '</div></div></div>';
  //
  //         $(alertMessage).insertAfter($('.modal#reservation .reservation-modal-body .container-fluid'));
  //
  //         $('.modal#reservation #close-modal').one('click', function() {
  //           location.reload();
  //         });
  //       } else {
  //         // if (!window.Notification) {
  //         //   console.log('Browser does not support notifications.');
  //         // } else {
  //         //   // check if permission is already granted
  //         //   if (Notification.permission === 'granted') {
  //         //     // show notification here
  //         //     var notify = new Notification('Pasūtījumi', {
  //         //       body: 'Ir izveidots jauns pasūtījums',
  //         //       icon: 'https://r1riepas.lv/img/r1-riepas-logo-1515661637.jpg',
  //         //     });
  //         //   } else {
  //         //     // request permission from user
  //         //     Notification.requestPermission().then(function (p) {
  //         //       if (p === 'granted') {
  //         //         // show notification here
  //         //         var notify = new Notification('Pasūtījumi', {
  //         //           body: 'Ir izveidots jauns pasūtījums',
  //         //           icon: 'https://r1riepas.lv/img/r1-riepas-logo-1515661637.jpg',
  //         //         });
  //         //       } else {
  //         //         console.log('User blocked notifications.');
  //         //       }
  //         //     }).catch(function (err) {
  //         //       console.error(err);
  //         //     });
  //         //   }
  //         // }
  //       }
  //
  //     }
  //   }
  // });

});

