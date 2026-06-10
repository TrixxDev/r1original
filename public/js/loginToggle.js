$(document).ready(function() {

    let atributs;
    let menu_opened = 0;

    let __filterWidth = $('.select-title.tire-width').attr('autocomplete', true).prop('autocomplete', true).val();
    let __filterHeight = $('.select-title.tire-height').attr('autocomplete', false).prop('autocomplete', false).val();
    let __filterDiameter = $('.select-title.tire-radius').attr('autocomplete', false).prop('autocomplete', false).val();

    $('.login-form .toggle, .register-form .toggle').on('click', function() {
       if ($(this).data('text-show') == 'Rādīt') {
           $(this).data('text-show', 'Hide');
           $(this).parent().parent().children('input').attr('type', 'text');
           $(this).html('Slēpt');
       } else {
           $(this).data('text-show', 'Rādīt');
           $(this).parent().parent().children('input').attr('type', 'password');
           $(this).html('Rādīt');
       }
    });

    // Desktop nav bar

    // $('.category').on('mouseover', function() {
    //     if (!$(this).children().last().css('display') == 'block') {
    //         $('.category .popover').css('display', 'none');
    //         $(this).children().last().css('display', 'block');
    //     } else {
    //         $(this).children().last().css('display', 'none');
    //     }
    // })

    // Mobile nav drawer
    (function () {
        const $burger = $('#menu-icon');
        const $overlay = $('#mobile-nav-overlay');
        const $drawer = $('#mobile-nav-drawer');
        const $body = $('body');
        let mobileNavOpen = false;

        if (!$burger.length || !$drawer.length) {
            return;
        }

        function closeAccordions() {
            $drawer.find('.mobile-nav-toggle[aria-expanded="true"]').each(function () {
                const $btn = $(this);
                $btn.attr('aria-expanded', 'false');
                $btn.next('.mobile-nav-submenu').hide();
                $btn.find('.material-icons').text('keyboard_arrow_down');
            });
        }

        function setMobileNavOpen(open) {
            mobileNavOpen = open;
            $burger.attr('aria-expanded', open ? 'true' : 'false');
            $burger.attr('aria-label', open ? 'Aizvērt izvēlni' : 'Atvērt izvēlni');
            $burger.toggleClass('is-active', open);
            $overlay.attr('aria-hidden', open ? 'false' : 'true').toggleClass('is-visible', open);
            $drawer.attr('aria-hidden', open ? 'false' : 'true').toggleClass('is-open', open);
            $body.toggleClass('mobile-nav-open', open);

            if (!open) {
                closeAccordions();
            }
        }

        $burger.on('click', function () {
            setMobileNavOpen(!mobileNavOpen);
        });

        $overlay.on('click', function () {
            setMobileNavOpen(false);
        });

        $drawer.find('.mobile-nav-drawer__close').on('click', function () {
            setMobileNavOpen(false);
        });

        $drawer.on('click', '.mobile-nav-toggle', function () {
            const $btn = $(this);
            const expanded = $btn.attr('aria-expanded') === 'true';
            const $sub = $btn.next('.mobile-nav-submenu');

            $btn.closest('.mobile-nav-list').children('.mobile-nav-item').each(function () {
                const $otherBtn = $(this).children('.mobile-nav-toggle');
                if ($otherBtn.length && !$otherBtn.is($btn)) {
                    $otherBtn.attr('aria-expanded', 'false').find('.material-icons').text('keyboard_arrow_down');
                    $otherBtn.next('.mobile-nav-submenu').slideUp(200);
                }
            });

            $btn.attr('aria-expanded', expanded ? 'false' : 'true');
            $btn.find('.material-icons').text(expanded ? 'keyboard_arrow_down' : 'keyboard_arrow_up');
            $sub.slideToggle(200);
        });

        $drawer.on('click', '.mobile-nav-link', function () {
            setMobileNavOpen(false);
        });

        $(document).on('keydown', function (event) {
            if (event.key === 'Escape' && mobileNavOpen) {
                setMobileNavOpen(false);
            }
        });
    })();

    (function () {
        const $menu = $('#_desktop_top_menu');
        if (!$menu.length) {
            return;
        }

        $menu.find('.category').each(function () {
            const $item = $(this);
            const $submenu = $item.children('.sub-menu').first();
            if (!$submenu.length) {
                return;
            }

            let hideTimer = null;

            $item.on('mouseenter', function () {
                window.clearTimeout(hideTimer);
                $menu.find('.category .sub-menu').css('display', 'none');
                $submenu.css('display', 'block');
            });

            $item.on('mouseleave', function () {
                hideTimer = window.setTimeout(function () {
                    $submenu.css('display', 'none');
                }, 150);
            });
        });
    })();

    $('.facet-dropdown').on('click', function() {
        const $size = $(this).children().first().val();
        $(this).children().first().focus().val('').val($size);
        if (!$(this).hasClass('open')) {
            $('.facet-dropdown').removeClass('open');
            $(this).addClass('open');
            //$(this).children().first().removeAttr('readonly');
            $('.dropdown-menu.width').scrollTo($('.dropdown-menu.width .select-list#' + __filterWidth));
            $('.dropdown-menu.height').scrollTo($('.dropdown-menu.height .select-list#' + __filterHeight));
            $('.dropdown-menu.radius').scrollTo($('.dropdown-menu.radius .select-list#' + __filterDiameter));
            menu_opened = 1;
        } else {
            $(this).removeClass('open');
            $(this).children().first().attr('readonly', true).prop('readonly', true);
            menu_opened = 0;
        }
    });



    $('.facet-dropdown .dropdown-menu a').bind('click', function() {
      atributs = $(this).attr('id');
      if (atributs == '') { atributs = 'Visi' };
      $(this).parent().parent().children('.select-title').val(atributs);
      $(this).parent().parent().children('.select-title').prop('value', atributs);
      $(this).parent().parent().children('.select-title').attr('value', atributs);
      $(this).parent().parent().children('.select-title').trigger('change');
    });



    //$('.select-title.tire-width, .select-title.tire-height, .select-title.tire-radius').on('keyup', function(e) {
    //    if (e.which == 32) {
    //        let $number = $(this).val();
    //        $number = $number.replaceAll(" ", "");
    //        $(this).val($number);
    //    } else if (e.ctrlKey && e.shiftKey && e.which == 82) {
    //        location.reload();
    //    }
    //    if ($(this).val().length == $(this).attr('maxlength')) {
    //        $('')
    //    }
    //});


    if (menu_opened === 1) {
        $(document).on('click', function(event){
        var container = $("#_desktop_top_menu .facet-dropdown .dropdown-menu");
            if (!container.is(event.target) &&            // If the target of the click isn't the container...
                container.has(event.target).length === 0) // ... nor a descendant of the container
                {
                    container.parent().removeClass('open');
                }
        });
    }
});
