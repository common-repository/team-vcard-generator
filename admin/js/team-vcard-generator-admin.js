(function ($) {
    'use strict';

    $(document).ready(function () {
        
        $(function () {
            $("#tabs").tabs();
        });

        //Si seleccionamos que no queremos padre nos desactiva ls opciones de coger los datos del padre
        if ($('select[name=tipo_miembro_padre]').val() == 'none') {
            $(".cambiar_como_padre").attr("disabled", true);
        }
        $('select[name=tipo_miembro_padre]').change(function () {
            if ($('select[name=tipo_miembro_padre]').val() == 'none') {
                $(".cambiar_como_padre").attr("disabled", true);
            } else {
                $(".cambiar_como_padre").removeAttr("disabled");
            }
        });

        //selector de contenido de padre
        $('.cambiar_como_padre').change(function (e) {
            if (this.checked) {
                $(this).parent().parent().find('.como_padre').addClass('true');
            } else {
                $(this).parent().parent().find('.como_padre').removeClass('true');
            }
        });

        //selectores de colores
        var colpick = $('.colores').each(function () {
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                inline: $(this).attr('data-inline') === 'true',
                letterCase: 'lowercase',
                opacity: false,
                change: function (hex, opacity) {
                    if (!hex)
                        return;
                    if (opacity)
                        hex += ', ' + opacity;
                    try {
                    } catch (e) {
                    }
                    $(this).select();
                },
                theme: 'bootstrap'
            });
        });
        $('#color1').bind("change paste keyup", function () {
            $('#preview_colores').css({
                'background': ' linear-gradient(272deg,' + $(this).val() + ', ' + $('#color2').val() + ')',
                'background-size': '400% 400%',
                '-webkit-animation': 'fondocolores 12s ease infinite',
                '-moz-animation': 'fondocolores 12s ease infinite',
                '-o-animation': 'fondocolores 12s ease infinite',
                'animation': 'fondocolores 12s ease infinite'
            });
            $('#layer_color').css('background-color', $(this).val());
            $('.scroll-top').css('background-color', $(this).val());
        });
        $('#color2').bind("change paste keyup", function () {
            $('#preview_colores').css({
                'background': ' linear-gradient(272deg,' + $('#color1').val() + ',' + $(this).val() + ')',
                'background-size': '400% 400%',
                '-webkit-animation': 'fondocolores 12s ease infinite',
                '-moz-animation': 'fondocolores 12s ease infinite',
                '-o-animation': 'fondocolores 12s ease infinite',
                'animation': 'fondocolores 12s ease infinite'
            });
        });
        //Seleccion de imagen tanto de #insert-my-media que seria la imagen de usuario como el de la compañia
        $('#insert-my-media').click(open_media_window);
        $('#insert-my-media_company').click(open_media_window);
        $('body').on('click', '.add_imagen_textos', open_media_window_textos);
        $('body').on('click', '.add_imagen_ofertas', open_media_window_ofertas);
        $('#morelinks').click(function (event) {
            event.preventDefault();
            var numero;
            var numero_posibles = 10;
            var numero_visibles = $('p.inputlink:not([style*="display: none"])').length;
            if (numero_visibles < numero_posibles) {
                numero = Math.floor((Math.random() * 10000) + 20);
                $("<p class='inputlink'><label><select name='links[" + numero + "][sitio]'><option value='no'>Choose</option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[" + numero + "][direccion]'><span class='dashicons dashicons-no removelink'></span></p>").insertBefore("#morelinks");
            }
        });
        $('#moretexts').click(function (event) {
            event.preventDefault();
            var numero;
            var numero_posibles = 5;
            var numero_visibles = $('p.inputtexto:not([style*="display: none"])').length;
            if (numero_visibles < numero_posibles) {
                numero = Math.floor((Math.random() * 10000) + 20);
                $("<p class='inputtexto'><label class='titulo_texto'><input name='textos[" + numero + "][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='textos_fotos'><input type='text' name='textos[" + numero + "][imagen]' class='imagen_textos_url' readonly/><a href='#' class='button add_imagen_textos'>Select</a></span><textarea name='textos[" + numero + "][texto]' wrap='soft' cols='30' rows='10'></textarea><img class='fotos_textos_preview'></span></p>").insertBefore("p #moretexts");
            }
        });
        $('#moreofertas').click(function (event) {
            event.preventDefault();
            var numero;
            var numero_posibles = 5;
            var numero_visibles = $('p.inputofertas:not([style*="display: none"])').length;
            if (numero_visibles < numero_posibles) {
                numero = Math.floor((Math.random() * 10000) + 20);
                $("<p class='inputofertas'><label class='titulo_ofertas'><input name='ofertas[" + numero + "][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><span class='ofertas_fotos'><input type='text' name='ofertas[" + numero + "][imagen]' class='imagen_ofertas_url' readonly/><a href='#' class='button add_imagen_ofertas'>Select</a></span><textarea name='ofertas[" + numero + "][texto]' wrap='soft' cols='30' rows='10'></textarea><img class='fotos_textos_preview'></span></p>").insertBefore("p #moreofertas");
            }
        });
        $('body').on('click', '.removelink', function (event) {
            var numero_visibles = $('p.inputlink:not([style*="display: none"])').length;
            event.preventDefault();
            $(this).closest(".inputlink").remove();
            $(this).remove();
            if (numero_visibles == 1) {
                var numero = Math.floor((Math.random() * 10000) + 20);
                $("<p class='inputlink'><label><select name='links[" + numero + "][sitio]'><option value='no'>Choose</option><option value='Facebook'>Facebook</option><option value='Linkedin'>Linkedin</option><option value='Twitter'>Twitter</option><option value='Google+'>Google+</option><option value='Instagram'>Instagram</option><option value='Tumblr'>Tumblr</option><option value='VK'>VK</option><option value='Web Page'>Web Page</option><option value='Other..'>Other..</option></select></label><input type='text' name='links[" + numero + "][direccion]'><span class='dashicons dashicons-no removelink'></span></p>").insertBefore("#morelinks");
            }
        });
        $('body').on('click', '.removetext', function (event) {
            var numero_visibles = $('p.inputtexto:not([style*="display: none"])').length;
            event.preventDefault();
            $(this).closest(".inputtexto").remove();
            $(this).remove();
            if (numero_visibles == 1) {
                var numero = Math.floor((Math.random() * 10000) + 20);
                $("<p class='inputtexto'><label class='titulo_texto'><input name='textos[" + numero + "][titulo]' type='text'></label><span class='dashicons dashicons-no removetext'></span><textarea name='textos[" + numero + "][texto]' wrap='soft' cols='30' rows='10'></textarea><img class='fotos_textos_preview'></p></p>").insertBefore("#moretexts");
            }
        });
        $('.submit_formulario').click(function (event) {
            var valid = false;
            var firstname = $("input[name='firstname']").val().replace(/ /g, '').length;
            var mail_profesional = $("input[name='mail_profesional']").val().replace(/ /g, '').length;

            if (firstname > 0 && mail_profesional > 0) {
                valid = true;
            }
            if (valid) {
                $("form").submit();
            } else {
                if (!firstname > 0) {
                    $("input[name='firstname']").css("border-color", "red");
                    $("#ui-id-1").css("color","red");
                } else {
                    $("input[name='firstname']").css("border-color", "#ddd");
                }
                if (!mail_profesional > 0) {
                    $("input[name='mail_profesional']").css("border-color", "red");
                    $("#ui-id-1").css("color","red");
                } else {
                    $("input[name='mail_profesional']").css("border-color", "#ddd");
                }
                event.preventDefault();
                return false;
            }
        });
        $('.submit_formulario_admin').click(function (event) {
            var valid = false;
            var name = $("input[name='firstname']").val().replace(/ /g, '').length;
            if (name > 0) {
                $("form").submit();
            } else {
                $("input[name='firstname']").css("border-color", "red");
                $("#ui-id-1").css("color","red");
            }
        });
        $("form input[type='radio']").change(cambiar_campos);
        $(document).ready(cambiar_campos);
        function cambiar_campos(){
            var check = $("form input[type='radio']:checked").val();
            if (check == "individual") {
                $("#lista_padres").show();
                $("#lastname").parent("p").show();
                $("#cargo").parent("p").show();
                $("input[name='telefono_personal']").parent("p").show();
                $("input[name='mail_personal']").parent("p").show();
            } else {
                $("#lista_padres").hide();
                $("#lastname").val('');
                $("#lastname").parent("p").hide();
                $("#cargo").val('');
                $("#cargo").parent("p").hide();
                $("input[name='telefono_personal']").val('');
                $("input[name='telefono_personal']").parent("p").hide();
                $("input[name='mail_personal']").val('');
                $("input[name='mail_personal']").parent("p").hide();
            }
        }
        var selections = [];
        $('.selectpicker').on('changed.bs.select', function (event, clickedIndex, newValue, oldValue) {
            if (oldValue == true) {
                //quita check
                $('.bootstrap-select').each(function () {
                    $(this).find('select.selectpicker option:eq(' + clickedIndex + ')').each(function () {
                        $(this).attr('disabled', false);
                        $('.selectpicker').selectpicker('render');
                    });
                });
            } else {
                $(this).children('select.selectpicker option:eq(' + clickedIndex + ')').addClass("hola");
                $('.bootstrap-select').each(function () {
                    $(this).find('select.selectpicker option:eq(' + clickedIndex + ')').not(".hola").each(function () {
                        $(this).attr('disabled', true);
                        $('.selectpicker').selectpicker('render');
                    });
                });
                $(this).children('select.selectpicker option:eq(' + clickedIndex + ')').removeClass("hola");
            }
        });
        $('.selectpicker').on('loaded.bs.select', function (event) {
            $('.bootstrap-select').each(function () {
                $(this).find('select.selectpicker option:selected').each(function () {
                    var posicion = $(this).index();
                    $('.bootstrap-select').each(function () {
                        $(this).find('select.selectpicker option:eq(' + posicion + ')').each(function () {
                            $(this).attr('disabled', true);
                            $('.selectpicker').selectpicker('render');
                        });
                    });
                    $(this).attr('disabled', false);
                    $('.selectpicker').selectpicker('render');
                });
            });
        });
        $("#morecalendar").click(function (event) {
            event.preventDefault();
            $('.lista_dia:hidden').first().show();
            $("#lesscalendar").show();
        });
        $(".lesscalendar").click(function (event) {
            event.preventDefault();
            $(this).parent().hide();
            $(this).parent().find("li").each(function () {
                if ($(this).hasClass("selected")) {
                    var numero = $(this).index();
                    $('.bootstrap-select').each(function () {
                        $(this).find('select.selectpicker option:eq(' + numero + ')').each(function () {
                            console.log($(this).val());
                            $(this).attr('disabled', false);
                            $('.selectpicker').selectpicker('render');
                        });
                    });
                }
            });
            $(this).siblings('div.bootstrap-select').find('select.selectpicker').selectpicker('deselectAll');
            $(this).parent().insertBefore("#morecalendar");
            $(this).siblings(".calendar_franja").not(':first').remove();
            $(this).siblings(".calendar_franja .calendar_inicio").val("00:00");
            $(this).siblings(".calendar_franja .calendar_final").val("00:00");
        });
        $('.lesshour').each(function(){
           if($(this).siblings('span.separa_franjas').size()>0){
               $(this).show();
           }
        });
        
        $(".morehour").click(function (event) {
            event.preventDefault();
            $("<span class='separa_franjas'> // </span>").insertBefore($(this).siblings(".lesshour"));
            $(this).siblings(".calendar_franja").last().clone().insertBefore($(this).siblings(".lesshour"));
            $(this).siblings(".lesshour").show();
        });
        $(".lesshour").click(function (event) {
            event.preventDefault();
            $(this).siblings(".separa_franjas").last().remove();
            $(this).siblings(".calendar_franja").last().remove();
            if ($(this).siblings(".calendar_franja").length == 1) {
                $(this).hide();
            }
        });
    });


    function open_media_window() {
        if (this.window === undefined) {
            this.window = wp.media({
                //opciones de la galeria
                title: 'Insert a media',
                library: {type: 'image'},
                multiple: false,
                displaySettings: true,
                displayUserSettings: false,
                button: {text: 'Insert'}
            });
            //nos recoge si el elemento que queremos coger es empresa o imagen de usuario
            var es_logo_empresa = $(this).attr("id") == "insert-my-media_company";
            var self = this; // Needed to retrieve our variable in the anonymous function below
            this.window.on('select', function () {
                var first = self.window.state().get('selection').first().toJSON();
                wp.media.editor.insert('[myshortcode id="' + first.id + '"]');
                if (es_logo_empresa) {
                    //en caso de que sea el logo de la empresa solo cambiamos el input del logo de la empresa
                    if (first.hasOwnProperty('medium')) {
                        $("#photo_company").val(first.sizes.medium.url);
                        $("#preview_company").attr("src", first.sizes.medium.url);
                    } else {
                        $("#photo_company").val(first.sizes.full.url);
                        $("#preview_company").attr("src", first.sizes.full.url);
                    }
                } else {
                    //si no nos lo hace con el de usuario
                    if (first.hasOwnProperty('medium')) {
                        $("#photo").val(first.sizes.medium.url);
                        $("#preview").attr("src", first.sizes.medium.url);
                    } else {
                        $("#photo").val(first.sizes.full.url);
                        $("#preview").attr("src", first.sizes.full.url);
                    }
                }
            });
        }
        this.window.open();
        return false;
    }
    function open_media_window_textos() {
        var este = this;
        if (this.window === undefined) {
            this.window = wp.media({
                //opciones de la galeria
                title: 'Insert a media',
                library: {type: 'image'},
                multiple: false,
                displaySettings: true,
                displayUserSettings: false,
                button: {text: 'Insert'}
            });
            var self = this; // Needed to retrieve our variable in the anonymous function below
            this.window.on('select', function () {
                var first = self.window.state().get('selection').first().toJSON();
                wp.media.editor.insert('[myshortcode id="' + first.id + '"]');
                if (first.hasOwnProperty('large')) {
                    $(este).prev(".imagen_textos_url").val(first.sizes.large.url);
                    $(este).parent().parent().find(".fotos_textos_preview").first().attr('src', first.sizes.large.url);
                } else {
                    $(este).prev(".imagen_textos_url").val(first.sizes.full.url);
                    $(este).parent().parent().find(".fotos_textos_preview").first().attr('src', first.sizes.full.url);
                }
                // @todo
                //$(este).parent().siblings('.fotos_textos_preview').css('background-image','url(' + first.sizes.medium.url + ') no-repeat');
            });
        }
        this.window.open();
        return false;
    }
    function open_media_window_ofertas() {
        var este = this;
        if (this.window === undefined) {
            this.window = wp.media({
                //opciones de la galeria
                title: 'Insert a media',
                library: {type: 'image'},
                multiple: false,
                displaySettings: true,
                displayUserSettings: false,
                button: {text: 'Insert'}
            });
            var self = this; // Needed to retrieve our variable in the anonymous function below
            this.window.on('select', function () {
                var first = self.window.state().get('selection').first().toJSON();
                wp.media.editor.insert('[myshortcode id="' + first.id + '"]');
                if (first.hasOwnProperty('large')) {
                    $(este).prev(".imagen_ofertas_url").val(first.sizes.large.url);
                    $(este).parent().parent().find(".fotos_ofertas_preview").first().attr('src', first.sizes.large.url);
                } else {
                    $(este).prev(".imagen_ofertas_url").val(first.sizes.full.url);
                    $(este).parent().parent().find(".fotos_ofertas_preview").first().attr('src', first.sizes.full.url);
                }
            });
        }
        this.window.open();
        return false;
    }
})(jQuery);

