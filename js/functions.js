$(document).ready(function(){

    $('input').on('focus',function(event){
        this.setSelectionRange(0, 0);
    });
/*    $(".nav-dots > span").click(function(){
        $("html, body").animate({ scrollTop: 0 }, "slow");
    })*/
    $(".go-form").click(function(){
        $('html, body').animate({
            scrollTop: $(".form-content").offset().top
        }, 600);
    })
    $(".nav-dots > span").click(function(){
        $('html, body').animate({
            scrollTop: $("#slider").offset().top
        }, 600);
    })
    $(".cc-selector input#llamada").on( "click", function() {
        $('input[name="email"]').hide();
        $('input[name="fono"]').fadeIn(300);
    });
    $(".cc-selector input#correo").on( "click", function() {
        $('input[name="fono"]').hide();
        $('input[name="email"]').fadeIn(300);
    });

    $('body').on('submit', '#contact-form', function(e){

        var errors  = 0,
            email   = $('input[name=email]').val(),
            nombre  = $('input[name=nombre]').val(),
            cargo   = $('input[name=cargo]').val(),
            empresa = $('input[name=empresa]').val(),
            telefono = $('input[name=fono]').val(),
            medio   = $('input[name=por-medio]:checked').val();

        if(email == '' && medio == 'correo'){
            errors += 1;
        }
        if(telefono == '' && medio == 'llamada'){
	        errors += 1;
        }
        if(nombre == ''){
            errors += 1;
        }
        if(cargo == ''){
            errors += 1;
        }
        if(empresa == ''){
            errors += 1;
        }
        if(errors == 0){
            var datos = $('#contact-form').serialize();
            $.post('/empresas/php/proceso.php', datos, function(respuesta){
                console.log(respuesta);
                if(respuesta.exito == 1){
                    $('#contact-form').hide(10, function(){
                        $('#contact-form')[0].reset();
                        //llega a otra pagina
                        location.replace("gracias.html");
                        //$('.rosa-gracias').show();
                    });
                }else{
                    $('.texto-alerta').text('Ha ocurrido un error al guardar el formulario');
                    $('.texto-alerta').show(0, function(){
                        setTimeout(function(){$('.texto-alerta').hide();}, 4000);
                    });
                }
            });
        }else{
            $('.texto-alerta').text('Debes llenar todos los campos del formulario para continuar');
            $('.texto-alerta').show(0, function(){
                setTimeout(function(){$('.texto-alerta').hide();}, 4000);
            });
        }
        e.preventDefault();
    });
});