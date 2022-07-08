$(document).ready(function() {

    let edit = false;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let main_url = window.location.protocol;
    let pathParts = window.location.pathname.split('/');
    let model = $('#brand_select').data('model');
    let brand_id = $('#brand_select').val();
    let tread_id;
    let current_tread = pathParts[4];

    function changeBrands() {
        $.ajax({
            url: main_url + '/admin/' + model + '/tread/' + brand_id + '/ajaxUpdateTreads',
            method: 'POST',
            dataType: 'JSON',
            data: { brand_id: brand_id },
            success: function(data) {
                let html = '<select name="tread" class="form-control col-md-3" id="tread_select"><option></option>';
                data.forEach(function(value, key) {
                    if (current_tread == value.tread_id) {
                        html += '<option value="' + value.tread_id + '" selected>' + value.title + '</option>';
                    } else {
                        html += '<option value="' + value.tread_id + '">' + value.title + '</option>';
                    }
                });
                html += '</select>';
                $('#tread_select').html(html);
            }
        });
    }

    function changeTread() {
        $.ajax({
            url: main_url + '/admin/' + model + '/tread/' + brand_id + '/ajaxUpdateTires',
            method: 'POST',
            dataType: 'JSON',
            data: { tread_id: tread_id },
            success: function() {
                window.location.href = main_url + '/admin/' + model + '/tread/' + tread_id;
            }
        });
    }

    $('#brand_select').on('change', function() {
        brand_id = $(this).val();

        changeBrands();
    });

    $('#tread_select').on('change', function() {
        tread_id = $(this).val();

        if (tread_id) {
            changeTread();
        } else {
            window.location.href = main_url + '/admin/' + model;
        }
    });

    changeBrands();

    // $(document).on('click', '.service-edit', function(e) {
    //   e.preventDefault();
    //   edit = true;
    //   let $id = $(this).parent().parent().attr('id');
    //   $('input[name="service"]').attr('data-service-id', $id);
    // });

    $(document).on('click', '.service_stop_edit', function(e) {
      e.preventDefault();
      edit = false;
      $('input[name="service_id"]').val('');
      $('input[name="service"]').val('');
      $('.services_form .card-footer button').removeClass('service_edit_button').addClass('service_add_button').first().text('Izveidot');
      $(this).remove();
    });


    $(document).on('click', '.service_add_button', function(e) {
        e.preventDefault();
        let title = $('input[name="service"]').val();
        if (title === '') {
          return false;
        }
        if (edit === false) {
          $.ajax({
            url: '/admin/settings/services/add',
            method: 'POST',
            data: { 'title': title },
            dataType: 'JSON',
            success: function(data) {
              if (data.success) {
                let $el = '';
                $el = ($('.no-services').length === 1) ? $('.no-services').first() : $('.services').first();
                $($el).before('<li id="service_' + data.service_id + '" class="services list-group-item d-flex justify-content-between align-items-center">' +
                  '<span class="service_title">' + data.service_title + '</span>' +
                  '<div class="options">' +
                  '<a href="/admin/settings/services/' + data.service_id + '/edit" class="badge bg-primary rounded-pill service-edit">Labot</a> ' +
                  '<a href="/admin/settings/services/' + data.service_id + '/delete" class="badge bg-primary rounded-pill service-delete">Dzēst</a>' +
                  '</div>' +
                  '</li>');
                $('input[name="service"]').val('');
                if ($('.no-services').length === 1) {
                  $('.no-services').remove();
                }
              }
            }
          });
        }
    });

    $(document).on('click', '.service_edit_button', function(e) {
      e.preventDefault();
      edit = false;
      let $value = $('input[name="service"]').val();
      let $id = $('input[name="service"]').attr('data-service-id');
      $('li#' + $id + ' .service_title').html($value);
      $('input[name="service_id"]').val('');
      $('input[name="service"]').val('').removeAttr('data-service-id');
      $('.services_form .card-footer button').first().text('Izveidot');
      $('.services_form .card-footer button').first().attr('type', 'submit').removeClass('service_edit_button').addClass('service_add_button');
      $('.services_form .card-footer button').last().remove();
    });

    $(document).on('click', '.services .options a.edit', function(e) {
        e.preventDefault();
        edit = true;
        if (!$('.service_stop_edit').is(':visible')) {
            $('.services_form .card-footer button').first().after('<button class="btn btn-sm btn-primary service_stop_edit" style="margin-left: 5px;"> Atcelt</button>');
        }
        let service_id = $(this).parent().parent().attr('id');
        let service_title = $(this).parent().parent().children().first().text();
        $('input[name="service"]').attr('data-service-id', service_id);
        $('input[name="service_id"]').val(service_id);
        $('input[name="service"]').val(service_title);
        $('.services_form .card-footer button').first().text('Labot');
        $('.services_form .card-footer button').first().removeAttr('type').removeClass('service_add_button').addClass('service_edit_button');
    });

    $('.card-body.btn').on('click', function() {
      let $btn_id = $(this).attr('id');
      $(this).attr('disabled', true).text('Sinhronizējās...').css('cursor', 'default');

      let $date = new Date();
      const $year = $date.getFullYear();
      const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
      const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
      const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
      const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
      const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

      const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

      let $sync = '';

      switch ($btn_id) {
        case 'i3-auto': // Lattako Auto riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/i3-auto',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Lattako - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Lattako Auto<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          });
          $sync = 'Lattako Auto';
          break;
        case 'i3-moto': // Lattako Moto riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/i3-moto',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Lattako - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Lattako Moto<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          });
          $sync = 'Lattako Moto';
          break;
        case 'i3-quadr': // Lattako Kvadraciklu riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/i3-quadr',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Lattako - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Lattako Kvadraciklu<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          });
          $sync = 'Lattako Kvadracikli';
          break;
        case 'duell-moto': // Duell Moto riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/duell-moto',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Duell - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Duell Moto<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          });
          $sync = 'Duell Moto';
          break;
        case 'duell-quadr': // Duell Kvadraciklu riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/duell-quadr',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Duell - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Duell Kvadraciklu<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          });
          $sync = 'Duell Kvadracikli';
          break;
        case 'i3-big': // Lattako Lielo riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/i3-big',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Lattako - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Lattako Lielās riepas<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          });
          $sync = 'Lattako Lielās riepas';
          break;
        case 'gy-auto': // GoodYear Auto riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/goodyear',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">GoodYear - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">GoodYear Auto<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          })
          $sync = 'GoodYear Auto';
          break;

        case 'rz-auto':
          $.ajax({
            url: '/sync/rz-auto',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Riepu zona - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Riepu zona auto riepas<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          })
          $sync = 'Riepu zona auto riepas';
          break;

        case 'starco-big': // GoodYear Auto riepu sinhronizācija - AJAX
          $.ajax({
            url: '/sync/starco',
            method: 'GET',
            success: function(data) {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('<p style="border-bottom: 1px solid #d8dbe0;">Bohnenkamp - ' + data + '<br>' + $time + '</p>').prependTo($('.logs'));
            },
            error: function() {

              let $date = new Date();
              const $year = $date.getFullYear();
              const $month = ($date.getMonth() < 10) ? '0' + $date.getMonth() : $date.getMonth();
              const $day = ($date.getDay() < 10) ? '0' + $date.getDay() : $date.getDay();
              const $hours = ($date.getHours() < 10) ? '0' + $date.getHours() : $date.getHours();
              const $mins = ($date.getMinutes() < 10) ? '0' + $date.getMinutes() : $date.getMinutes();
              const $secs = ($date.getSeconds() < 10) ? '0' + $date.getSeconds() : $date.getSeconds();

              const $time = '<b>' + $day + '.' + $month + '.' + $year + ' ' + $hours + ':' + $mins + ':' + $secs + '</b>';

              $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0">Bohnenkamp lielās riepas<br>Sinhronizācijas kļūda!<br>' + $time + '</p>');
            },
            complete: function() {
              $('#' + $btn_id).attr('disabled', false).text('Sinhronizēt').css('cursor', 'pointer');
            }
          })
          $sync = 'Bohnenkamp lielās riepas';
          break;
      }

      $('.logs').prepend('<p style="border-bottom: 1px solid #d8dbe0;">Uzsākta sinhronizācija - ' + $sync + '<br>' + $time + '</p>');

    });



    $('tr.odd td').on('mouseover', function() {
      $(this).parent().css('background', '#d8dbe0');
    }).on('mouseout', function() {
      $(this).parent().removeAttr('style');
    });

});

$(document).ready(function () {
  $('.alloy_rims .link').each(function() {
    $(this).on('click', function() {
      window.location.href = ($(this).attr('href') !== '') ? $(this).attr('href') : '/admin/rims/';
    });
  });

  $('.queueTable .subheader svg').on('click', function() {
    $('.modal#queueModal input[name="queue_id"]').val($(this).data('queue-id'));
    $('.modal#queueModal input[name="date"]').val($(this).data('date'));

    $.ajax({
      method: 'GET',
      url: '/admin/pieraksts/queue_ajax/' + $(this).data('queue-id') + '/' + $(this).data('date'),
      dataType: 'JSON',
      success: function(data) {
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
          this.error(data);
        }
        location.reload();
      },
      error: function(data) {
        console.log(data);
      }
    });
  });

  $('.queueTable .buttonbar svg').on('click', function() {
    $('.modal#slotModal input[name="queue_id"]').val($(this).data('queue-id'));
    $('.modal#slotModal input[name="date"]').val($(this).data('date'));
    $('.modal#slotModal input[name="slot"]').val($(this).data('slot-id'));

    $.ajax({
      method: 'GET',
      url: '/admin/pieraksts/slot_ajax/' + $(this).data('queue-id') + '/' + $(this).data('date') + '/' + $(this).data('slot-id'),
      dataType: 'JSON',
      success: function(data) {
        console.log(data);
      }
    })
  });
});
