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
  // const socket = new WebSocket('ws://r1riepas.lv:3500');

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

  if (localStorage.getItem('toastMessage')) {
    if ($('.reservations_page .apply-changes').is(':visible')) {
      $.toast({
        'type': 'success',
        'title': 'Paziņojums',
        'message': localStorage.getItem('toastMessage'),
      });
    }
    localStorage.removeItem('toastMessage');
  }

  if (localStorage.getItem('deleteMessage')) {
    $.toast({
      'type': 'success',
      'title': 'Paziņojums',
      'message': localStorage.getItem('deleteMessage')
    });
    localStorage.removeItem('deleteMessage');
  }

  let iorder;
  let queue_id;
  let date;
  let time;
  let newOpenTime;
  let oldOpenTime;
  let newCloseTime;
  let oldCloseTime;
  let office;
  let slot;
  let car_brand;
  let phone;
  let new_date;
  let new_time;
  let new_queue;
  let timeStep;
  let empty_slot;
  let discountSelect;
  let numNonEmptyInputs;

  let days = {
    'pirmdiena': 'pirmdienām',
    'otrdiena': 'otrdienām',
    'trešdiena': 'trešdienām',
    'ceturtdiena': 'ceturtdienām',
    'piektdiena': 'piektdienām',
    'sestdiena': 'sestdienām',
    'svētdiena': 'svētdienām'
  }

  $(document).on('click', '#slotModal .close-modal, #slotModal .decline', function() {
    $('#slotModal').modal('hide');
    $('#slotModal form').trigger('reset');
  });

  $(document).on('click', '#queueModal .decline', function() {
    $('#queueModal').modal('hide');
  });



  $('#queueModal, #slotModal').on('show.bs.modal hide.bs.modal', function () {
    $('body').toggleClass('removeScroll');
  });

  $('.reservation_edit .select-discount-option').on('change', function() {
    let selectedOption = $('option:selected', this);
    let isLastOption = selectedOption.is(':last-child');

    // $('.reservation_edit #f_status option').removeAttr('selected').prop('selected', false).first().attr('selected', true).prop('selected', true);

    discountSelect = selectedOption;

    if (isLastOption) {
      $(this).next().show();
    } else {
      $(this).next().hide();
    }
  });

  $('.reservation_edit #f_slotcomment').on('input', function() {
    $('.reservation_edit .select-discount-option option').last().attr('selected', true).prop('selected', true);
  });

  $('.schedule-table.reservations_page #save_changes').one('click', function(e) {
    e.preventDefault();

    $('.body.loader-block').show();
    $('body').addClass('removeScroll');
    setTimeout(function() {
      $.ajax({
        url: '/pieraksts/rezervacijas/saveTimeChanges',
        method: 'GET',
        dataType: 'JSON',
        success: function(data) {
          localStorage.setItem('toastMessage', 'Visas izmaiņas veiksmīgi saglabātas!');

          let resData = {};
          resData.timeChangedState = 1;

          // socket.send(JSON.stringify(resData));
          window.location.href = '/pieraksts/rezervacijas';
        }
      });
    }, 1000);
  });

  $('.schedule-table.reservations_page #cancel_changes').one('click', function(e) {
    e.preventDefault();

    $('.body.loader-block').show();
    $('body').addClass('removeScroll');
    setTimeout(function() {
      $.ajax({
        url: '/pieraksts/rezervacijas/cancelTimeChanges',
        method: 'GET',
        dataType: 'JSON',
        success: function(data) {
          localStorage.setItem('deleteMessage', 'Visas izmaiņas veiksmīgi atceltas!');
          window.location.href = '/pieraksts/rezervacijas';
        }
      });
    }, 1000);
  });

  $('#queueModal .submit').on('click', function(e) {
    e.preventDefault();

    let changeVal = $('#queueModal input[name="changeVal"]:checked').val();
    let newOpenTime = $('#queueModal select#f_opentime option:selected').val();
    let newCloseTime = $('#queueModal select#f_closetime option:selected').val();
    let timeStep = $('#queueModal #f_timeinterval option:selected').val();
    let is_half = parseInt($('#queueModal input[name="queue"]:checked').val());
    let ac_toggle = ($('#queueModal input[name="ac_toggle"]').is(':checked') === true) ? 1 : null;
    let moto_toggle = ($('#queueModal input[name="moto_toggle"]').is(':checked') === true) ? 1 : null;

    console.log(ac_toggle, moto_toggle);

    let sendData = {
      'times': {
        'changeVal': changeVal,
        'oldOpenTime': oldOpenTime,
        'newOpenTime': newOpenTime,
        'oldCloseTime': oldCloseTime,
        'newCloseTime': newCloseTime,
        'timeStep': timeStep,
        'queue_id': queue_id,
        'date': date,
        'is_half': (is_half === 2) ? 1 : null,
      }
    };

    $('#queueModal .loader-block').show();
    setTimeout(function() {
      $.ajax({
        url: '/pieraksts/rezervacijas/changeTimes',
        method: 'POST',
        data: sendData,
        dataType: 'JSON',
        error: function(jqXHR, textStatus, errorThrown) {
          $('#queueModal .loader-block').hide();
          $.toast({
            'type': 'danger',
            'title': 'Paziņojums',
            'message': 'Notika kļūda laiku izmaiņas laikā',
          })
        },
        success: function(data) {
          if (data.status === 'success') {
            localStorage.setItem('toastMessage', 'Lai <b>saglabātu</b> darba laikus, nospiediet "Saglabāt" pogu, lai atgrieztu <b>VISAS</b> izmaiņas, nospiediet "Atcelt" pogu');
            window.location.href = '/pieraksts/rezervacijas';
          } else {
            $('#queueModal .loader-block').hide();
            $.toast({
              'type': 'danger',
              'title': 'Paziņojums',
              'message': data.message,
            })
          }
          window.location.reload();
        },
        complete: function() {

        }
      });
    }, 1000);

    // socket.send(JSON.stringify(sendData));
  });

  $('.grid .table .title').on('click', function() {
    $('#queueModal .loader-block').show();
    queue_id = $(this).parent().attr('data-queue-id');
    date = $(this).parent().parent().attr('data-date');
    let day = $(this).parent().parent().parent().prev().text().split(',')[0].toLowerCase();
    day = days[day];

    $.ajax({
      url: '/pieraksts/rezervacijas/getTimes',
      method: 'POST',
      data: {queue_id: queue_id, date: date},
      beforeSend: function() {
        $('#queueModal #f_timeinterval option').each(function() {
          $(this).removeAttr('selected').prop('selected', false);
        });
        $('#queueModal #f_opentime').html('');
        $('#queueModal #f_closetime').html('');
        $('#queueModal #title').val('');
        $('#queueModal input[name="changeVal"]').removeAttr('checked').prop('checked', false);
        if ($('#queueModal #all_working_days').is(':hidden')) {
          $('#queueModal #all_working_days').parent().show();
        }
        $('#queueModal #fullQueue, #queueModal #halfQueue').removeAttr('checked').prop('checked', false);
      },
      success: function(data) {

        data = JSON.parse(data);

        if (data.weekday == 6) {
          if ($('#queueModal #all_working_days').is(':visible')) {
            $('#queueModal #all_working_days').parent().hide();
          }
        }

        setTimeout(function() {
          $('#queueModal #title').val(data.title);

          $('#queueModal #one_day').attr('checked', true).prop('checked', true);
          if (data.is_half === 1) {
            $('#queueModal').find('#halfQueue').attr('checked', true).prop('checked', true);
          } else {
            $('#queueModal').find('#fullQueue').attr('checked', true).prop('checked', true);
          }

          $('#queueModal .f_day').text(day);

          for (var hour = 0; hour <= 23; hour++) {
            // Loop through minutes from 0 to 30
            for (var minute = 0; minute < 60; minute += 30) {
              // Format the time in HH:MM format
              var time = ('0' + hour).slice(-2) + ':' + ('0' + minute).slice(-2);

              // Create an option element for each time and append it to the select element
              $('#queueModal #f_opentime').append($('<option></option>').val(time).html(time));
              $('#queueModal #f_closetime').append($('<option></option>').val(time).html(time));

              $('#queueModal #f_opentime option[value="' + data.timeopen + '"]').attr('selected', true).prop('selected', true);
              $('#queueModal #f_closetime option[value="' + data.timeclose + '"]').attr('selected', true).prop('selected', true);
            }
          }
          $('#queueModal #f_timeinterval option').each(function() {
            if ($(this).val() == data.timeStep) {
              $(this).attr('selected', true).prop('selected', true);
            }
          })

          oldOpenTime = $('#queueModal select#f_opentime option:selected').val();
          oldCloseTime = $('#queueModal select#f_closetime option:selected').val();
        }, 1000);
      },
      complete: function() {

        setTimeout(function() {
          // $('#queueModal .loader-block').hide();
        }, 1000);
      }
    });

    $('#queueModal').modal('show');
  });

  $('#times-modal button[name="editTimes"]').on('click', function(e) {
    e.preventDefault();

    let timeopen = $('#times-modal #working_hours_open option:selected').val();
    let timeclose = $('#times-modal #working_hours_close option:selected').val();
    let lastIorder = $('.grid[data-date="' + date + '"] .office[data-queue-id="' + queue_id + '"] .time-status').last().attr('data-iorder');
    let changeVal = $('#times-modal .change_options input[name="change"]:checked').val();

    $.ajax({
      url: '/pieraksts/rezervacijas/changeTimes',
      method: 'POST',
      data: {timeopen: timeopen, timeclose: timeclose, timeStep: timeStep, changeVal: changeVal, lastIorder: lastIorder, queue_id: queue_id, date: date},
      beforeSend: function() {
        $('#times-modal .times-modal-body').addClass('loading');
        $('#times-modal .loader').fadeIn();
      },
      success: function(data) {
        // data = JSON.parse(data);
        //
        // let resData = {};
        // resData.times = data;
        // resData.times.queue_id = queue_id;
        // resData.times.date = date;
        // resData.times.changeVal = changeVal;
        //
        // socket.send(JSON.stringify(resData));

        window.location.reload();
      }
    });

  });

  $(document).on('click', 'button.status', function() {

    $('#slotModal .loader-block').show();

    let today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed, so we add 1
    const day = String(today.getDate()).padStart(2, '0');

    today = `${year}-${month}-${day}`;

    slot = $(this).parent();
    iorder = slot.attr('data-iorder');
    queue_id = slot.parent().attr('data-queue-id');
    date = slot.parent().parent().attr('data-date');
    time = slot.children('.time-slot').html();
    office = slot.parent().attr('class').replace('table office_', '');

    let discount;

    let data = {
      iorder: iorder,
      queue_id: queue_id,
      date: date,
    }

    $('#slotModal').modal('show');

    $.ajax({
      url: '/pieraksts/getSlotInfo',
      data: data,
      method: 'POST',
      dataType: 'JSON',
      beforeSend: function() {
        $('#slotModal .record-notif-state ul li').each(function() {
          $(this).find('input').removeAttr('checked').prop('checked', false);
        });
        $('#slotModal ul.services li').each(function() {
          $(this).find('input').removeAttr('checked').prop('checked', false);
        });
        $('#slotModal textarea[name="user_message"]').html('');
        $('#slotModal form').trigger('reset');
      },
      success: function(data) {

        $('#slotModal #f_date option').each(function() {
          $(this).removeAttr('selected').prop('selected', false);
          if (date === $(this).val()) {
            $(this).attr('selected', true).prop('selected', true);
          }
        });
        $('#slotModal #f_time option').each(function() {
          $(this).removeAttr('selected').prop('selected', false);
          if (time === $(this).val()) {
            $(this).attr('selected', true).prop('selected', true);
          }
        });
        $('#slotModal #f_office option').each(function() {
          $(this).removeAttr('selected').prop('selected', false);
          if (queue_id === $(this).val()) {
            $(this).attr('selected', true).prop('selected', true);
          }
        });

        let takenby = JSON.parse(data.takenby);
        $('.last-info').remove();
        if (takenby === false) {
          $('#record-modal #working_days, #record-modal #working_hours, #record-modal #office_queues').attr('disabled', 'true').prop('disabled', true);
          discount = data.discount;
        } else {

          if (data.edittime == '') {
            $('<div class="last-info">Pieraksts izveidots no ' + data.is_mobile + '<br>Izveidots: ' + data.createtime + ' (' + data.createuser + ')<br>Labots:</div>').insertAfter($('.modal#slotModal .form-group').last());
          } else {
            $('<div class="last-info">Pieraksts izveidots no ' + data.is_mobile + '<br>Izveidots: ' + data.createtime + ' (' + data.createuser + ')<br>Labots: ' + data.edittime + ' (' + data.edituser + ')</div>').insertAfter($('.modal#slotModal .form-group').last());
          }



          $('#record-modal #working_days, #record-modal #working_hours, #record-modal #office_queues').removeAttr('disabled').prop('disabled', false);
          $('#slotModal #f_car').val(takenby.car_brand);
          $('#slotModal #f_model').val(takenby.car_model);
          $('#slotModal #f_plate').val(takenby.lic_plate);
          $('#slotModal #service .select-service-option option[value="' + takenby.service + '"]').attr('selected', true).prop('selected', true).trigger('change');
          $('#slotModal #save_nr').val(takenby.temp_nr);
          $('#slotModal .rims-with-select-row input[name="flexRadioDefault"][value="' + takenby.rimsWith + '"]').attr('checked', true).prop('checked', true);
          $('#slotModal .services select.select-service-option option').on('change', function() {
            $('#slotModal .rims-with-select-row input[name="flexRadioDefault"][value="' + takenby.rimsWith + '"]').attr('checked', true).prop('checked', true);
          });
          $('#slotModal textarea#f_comment').val(takenby.user_comment);
          $('#slotModal #f_name').val(takenby.name);
          $('#slotModal #f_phone').val(takenby.phone_number);
          $('#slotModal #f_email').val(takenby.email);
          discount = data.comment;
        }

        empty_slot = !!(data.office_id && data.takenby == 'false');

        if (discount) {
          let found = false;
          $('.reservation_edit .select-discount-option option').each(function() {
            if ($(this).text() === discount) {
              $(this).attr('selected', true).prop('selected', true);
              $(this).parent().next().hide();
              found = true;
              return false;
            }
          });
          if (found === false) {
            $('.reservation_edit .select-discount-option option:last-child').attr('selected', true).prop('selected', true);
            $('.reservation_edit .select-discount-option').next().html(discount).show();
          }
        } else {
          $('.reservation_edit .select-discount-option option').first().attr('selected', true).prop('selected', true);
          $('.reservation_edit .select-discount-option').next().html('').hide();
        }

        $('.reservation_edit .reservationOption').each(function() {
          if (date == today) {
            $(this).parent().show();
            $(this).on('click', function() {
              if ($(this).is(':checked')) {
                $('.reservation_edit .reservationOption').attr('disabled', true).prop('disabled', true);
                $(this).attr('disabled', false).prop('disabled', false);
              } else {
                $('.reservation_edit .reservationOption').attr('disabled', false).prop('disabled', false);
              }
            })
          } else {
            $(this).parent().hide();
          }
        });


      },
      complete: function() {
        setTimeout(function() {
          // $('#slotModal .loader-block').hide();
        }, 1000);
      }
    });
  });

  $('#slotModal').on('show.bs.modal', function () {
    $('body').addClass('removeScroll');
  }).on('hide.bs.modal', function () {
    $('body').removeClass('removeScroll');
  }).on('keypress', function(e) {
    if (e.keyCode === 13) {
      $('.submit', this).click();
    }
  });

  $(document).on('click', '#slotModal .submit', function(e) {
    e.preventDefault();

    let discount;

    if (!discountSelect) {
      discountSelect = $('.reservation_edit .select-discount-option option:selected');
    }

    switch ($(discountSelect).val()) {
      case 'empty': {
        discount = null;
        break;
      }
      case 'other': {
        discount = $('.reservation_edit #f_slotcomment').val();
        break;
      }
      default: {
        discount = $(discountSelect).text();
        break;
      }
    }

    let car_brand = $('#slotModal #f_car').val();
    let car_model = $('#slotModal #f_model').val();
    let name = $('#slotModal #f_name').val();
    let phone = $('#slotModal #f_phone').val();
    let service = $('#slotModal .services select.select-service-option option:selected').val();
    let rimsWith = $('#slotModal .rims-with-select-row input[name="flexRadioDefault"]:checked').val() || '';
    let temp_nr = $('#slotModal .temp_save_nr input#save_nr').val() || '';
    let plate = parseInt(phone.substr(-3)) || '';
    let lic_plate = isNaN(plate) ? '' : plate;
    let license_plate = $('#slotModal #f_plate').val();
    let user_comment = $('#slotModal #f_comment').val();
    let email = $('#slotModal #f_email').val();
    let new_date = $('#slotModal #f_date option:selected').val();
    let new_time = $('#slotModal #f_time option:selected').val();
    let new_queue = $('#slotModal #f_office option:selected').val();
    let status = $('#slotModal #f_status option:selected').val();
    let f_statuscase = $('.reservationOption[type=checkbox]:checked').val();

    let formData = `car_brand=${car_brand}&car_model=${car_model}&rimsWith=${rimsWith}&temp_nr=${temp_nr}&lic_plate=${license_plate}&service=${service}&user_comment=${user_comment}&name=${name}&phone_number=${phone}&email=${email}&status=${status}&slotcomment=${discount}`;

    let dopParams = {
      iorder: iorder,
      queue_id: queue_id,
      date: date,
      time: time,
      office: office,
      new_date: new_date,
      new_time: new_time,
      new_queue: new_queue,
    };

    $.ajax({
      url: '/pieraksts/rezervacijas/editSlot',
      method: 'POST',
      data: {formData: formData, dopParams: dopParams, f_statuscase: f_statuscase},
      beforeSend: function() {
        $('#slotModal .loader-block').show();
      },
      success: function(data) {
        data = JSON.parse(data);

        if (data.failed) {
          $('#toasts .toast').each(function() {
            $(this).fadeOut(function() {
              $(this).remove();
            });
          });
          $.toast({
            autoDismiss: false,
            title: 'Kļūda!',
            message: data.failed_msg,
          });
        }

        let wsParams = {
          iorder: iorder,
          queue_id: queue_id,
          date: date,
          office: office,
          car_brand: car_brand,
          car_model: car_model,
          plate: plate,
          fullNumber: phone,
          discount: discount,
        };

        let slot_admin;

        if (data.edited_slot_admin) {
          dopParams.new_iorder = data.new_iorder;
          dopParams.new_office = data.new_office;
          slot_admin = {edited_slot_admin:data.edited_slot_admin};
        } else if (data.deleted_slot_admin) {
          slot_admin = {deleted_slot_admin:data.deleted_slot_admin};
        } else if (data.moved_slot_admin) {
          if (data.edited) dopParams.edited = true;
          dopParams.new_iorder = data.new_iorder;
          dopParams.new_office = data.new_office;
          slot_admin = {moved_slot_admin:data.moved_slot_admin};
        }

        let wsData = {
          wsParams: wsParams,
          dopParams: dopParams,
          slot_admin: slot_admin,
          status: data.status,
        };

        // socket.send(JSON.stringify(wsData));

        window.location.reload();
      },
      complete: function() {

        setTimeout(function() {
          // $('#slotModal #service .form-check').each(function() {
          //   $(this).find('input').removeAttr('checked');
          // });
          // $('#slotModal .loader-block').hide();
          // $('#slotModal form').trigger('reset');
          // $('#slotModal').modal('hide');
        }, 1000);
      }
    });

  });

  $(document).on('change', '#slotModal #service .select-service-option', function() {
    switch ($('option:selected', this).val()) {
      case '1': {
        $('.rims-with-select-row').show();
        $('.temp_save_nr').hide();
        break;
      }
      case '2': {
        $('.rims-with-select-row').hide();
        $('.temp_save_nr').show();
        break;
      }
      default: {
        $('.rims-with-select-row').hide();
        $('.temp_save_nr').hide();
      }
    }
  });

  function formatTime(date) {
    let hours = ('0' + date.getHours()).slice(-2);
    let minutes = ('0' + date.getMinutes()).slice(-2);
    return hours + ':' + minutes;
  }

  // Listen for WebSocket messages
  // socket.addEventListener('message', function(event) {
  //   const data = JSON.parse(event.data);
  //
  //   if (!data.times) {
  //     let slot = $('.schedule-table.reservations_page .grid[data-date="' + data.wsParams.date + '"] .table[data-queue-id="' + data.wsParams.queue_id + '"] .time-status[data-iorder="' + data.wsParams.iorder + '"]');
  //     if (data.new_slot_client) {
  //       slot.removeClass('time-free').removeClass('time-discount').addClass('time-taken').find('button').remove();
  //       let successText = truncateCharacters($.trim(data.wsParams.car_brand) + ' ' + $.trim(data.wsParams.car_model),9,'&mldr;',1) + ' ' + data.wsParams.fullNumber;
  //       slot.append('<button class="slot status taken-slot">' + successText + '</button>').hide().fadeIn();
  //     } else if (data.slot_admin.edited_slot_admin) {
  //       if (data.dopParams.new_date !== data.dopParams.date || data.dopParams.new_queue !== data.dopParams.queue_id || data.dopParams.new_time !== data.dopParams.time) {
  //         slot = $('.schedule-table.reservations_page .grid[data-date="' + data.dopParams.new_date + '"] .table[data-queue-id="' + data.dopParams.new_queue + '"] .time-status[data-iorder="' + data.dopParams.new_iorder + '"]');
  //       }
  //       if (data.status == 3) {
  //         slot.removeClass('time-free').removeClass('time-discount').removeClass('time-taken').addClass('time-closed').find('button').remove();
  //         slot.append('<button class="slot status closed-slot">Slēgts</button>').hide().fadeIn();
  //       } else if (data.status == 0) {
  //         if (data.wsParams.discount) {
  //           slot.removeClass('time-free').removeClass('time-taken').removeClass('time-closed').addClass('time-discount');
  //           slot.find('button.slot').addClass('discount').html(data.wsParams.discount);
  //         }
  //       } else {
  //         slot.removeClass('time-free').removeClass('time-discount').addClass('time-taken').find('button').remove();
  //         let successText = truncateCharacters($.trim(data.wsParams.car_brand) + ' ' + $.trim(data.wsParams.car_model),9,'&mldr;',1) + ' ' + data.wsParams.fullNumber;
  //         if ($.trim(successText).length === 0) successText = 'xxxxx';
  //         if (slot.parent().attr('data-half') == 1) {
  //           if (slot.attr('data-iorder') % 2 == 1) {
  //             slot.append('<button class="slot status text-red taken-slot-admin">' + successText + '</button>').hide().fadeIn();
  //           } else {
  //             slot.append('<button class="slot status taken-slot-admin">' + successText + '</button>').hide().fadeIn();
  //           }
  //         } else {
  //           slot.append('<button class="slot status taken-slot-admin">' + successText + '</button>').hide().fadeIn();
  //         }
  //       }
  //     } else if (data.slot_admin.moved_slot_admin) {
  //       let new_slot = $('.schedule-table.reservations_page .grid[data-date="' + data.dopParams.new_date + '"] .table[data-queue-id="' + data.dopParams.new_queue + '"] .time-status[data-iorder="' + data.dopParams.new_iorder + '"]');
  //
  //       let old_slot_classes = slot.prop('classList');
  //       let new_slot_classes = new_slot.prop('classList');
  //
  //       let old_slot_button = slot.find('button').clone();
  //       let new_slot_button = new_slot.find('button').clone();
  //
  //       new_slot.find('button').first().remove();
  //       slot.find('button').first().remove();
  //       new_slot.append(old_slot_button).hide().fadeIn();
  //       slot.append(new_slot_button).hide().fadeIn();
  //
  //       new_slot.attr('data-old-classes', old_slot_classes);
  //       slot.attr('data-new-classes', new_slot_classes);
  //
  //       new_slot.removeAttr('class').attr('class', new_slot.attr('data-old-classes')).removeAttr('data-old-classes');
  //       slot.removeAttr('class').attr('class', slot.attr('data-new-classes')).removeAttr('data-new-classes');
  //
  //       if (data.dopParams.edited === true) {
  //         let successText = truncateCharacters($.trim(data.wsParams.car_brand) + ' ' + $.trim(data.wsParams.car_model),9,'&mldr;',1) + ' ' + data.wsParams.fullNumber;
  //
  //         new_slot.find('button').remove();
  //         new_slot.append('<button class="slot status taken-slot">' + successText + '</button>').fadeIn();
  //       }
  //
  //       //.removeAttr('class').attr('class', new_slot.attr('data-old-classes')).removeAttr('data-old-classes')
  //
  //       iorder = data.dopParams.new_iorder;
  //       queue_id = data.dopParams.new_queue;
  //       date = data.dopParams.new_date;
  //       time = data.dopParams.new_time;
  //       office = data.dopParams.new_office;
  //       slot = new_slot;
  //     } else if (data.slot_admin.deleted_slot_admin) {
  //       if (data.slot_admin.comment) {
  //         slot.removeClass('time-taken').addClass('time-discount').find('button').remove();
  //         slot.append('<button class="bg-orange-500 hover:bg-orange-200 text-black py-2 px-4 status">' + data.comment.comment + '</button>').fadeIn();
  //       } else {
  //         slot.removeClass('time-taken').removeClass('time-discount').addClass('time-free').find('button').remove();
  //         slot.append('<button class="slot status slot-free"></button>').fadeIn();
  //       }
  //     }
  //   } else {
  //     if (data.times.changeVal) {
  //
  //     }
  //   }
  //
  // });

});

