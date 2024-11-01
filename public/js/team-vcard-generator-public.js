(function ($) {
    'use strict';
    
    $(document).ready(function () {
        $('table tr').click(function () {
            $('.ui-loader').show();
            window.location.href = $(this).attr('href');
        });
        $('#redireccion_padre a').click(function (event) {
            event.preventDefault();
            $('.ui-loader').show();
            window.location.href = $(this).attr('href');
        });
        $('#social li a').click(function (event) {
            event.preventDefault();
            $('.ui-loader').show();
            window.location.href = $(this).attr('href');
        });
        $(document).on('submit', '#contacto', function (event) {
            event.preventDefault();
            $('.ui-loader').show();
            $.ajax({
                type: "POST",
                url: '<?php echo plugin_dir_url(__FILE__) ?>/ajax.php',
                data: $("#contacto").serialize(), // serializes the form's elements.
                success: function (data)
                {
                    alert(data);
                    $('.ui-loader').hide();
                }
            });
        });
        $(document).on('submit', '#contacto', function (event) {
            var btn = $(this).find("input[type=submit]:focus");
            if ($(btn).attr("name") == "show_IOS") {
                event.preventDefault();
                //no nos ejecute el submit en caso de IOS
                if ($("#envio_IOS").length) {
                    //Si existe la id nos la cambia para que nos muestre
                    $('#envio_IOS').attr('id', 'envio_IOS_show');
                } else {
                    //Si no nos lo esconde
                    $('#envio_IOS_show').attr('id', 'envio_IOS');
                }
            }
        });
        var ultimo_scroll = 0;
        $(window).scroll(function () {
            if ($(window).scrollTop() > $(window).height()) {
                if ($(window).scrollTop() !== ultimo_scroll) {
                    ultimo_scroll = $(window).scrollTop();
                    $(".scroll-top").finish();
                    $(".scroll-top").fadeIn("slow").delay(2000).fadeOut('slow');
                }
            } else {
                $(".scroll-top").stop();
                $(".scroll-top").fadeOut("fast");
            }
        });
        $('.scroll-top').on('click', function () {
            $(this).fadeOut('fast');
            $('body,html').animate({scrollTop: 0}, 800);
        });
    });

})(jQuery);
