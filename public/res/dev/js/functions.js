
/*
* Verifica se é mobile
* return: true || false
* */

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};


/*
 * Verifica largura da tela
 * return: width
 * */

function screenWidth() {
    var width = window.outerWidth;

    return width;
}


/*
 * Verifica altura da tela
 * return: height
 * */

function screenHeight() {
    var height = window.innerHeight;

    return height;
}


if(isMobile.any() || screenWidth() <= 768) {

    /*
     * Abre o menu
     * */
    (function openMenu(){
        var btnOpenCategories = document.querySelector('.btn-menu');

        btnOpenCategories.addEventListener('click', function(){
            this.classList.toggle('open');
            document.querySelector('.menu').classList.toggle('open');
        })
    }());
}


/*
 * Seta a altura da tela no banner principal
 * */

$('.banner-main').height($(window).height() + 'px');

window.onresize = function() {
    $('.banner-main').height($(window).height() + 'px');
};



/*
 * Adiciona/remove classe do body no scroll
 * */

$(document).ready(function() {
    var $header = $(".header");

    $(window).on("scroll", function() {
        var fromTop = $("body").scrollTop();
        $('body').toggleClass("down", (fromTop > $(window).height()));
    });
});


/*
 * Slider de clientes
 * */

$('.slider-clientes').bxSlider({
    slideWidth: 50000,
    minSlides: 1,
    maxSlides: 1,
    slideMargin: 0,
    controls: false
});


/*
 * Filtros de trabalhos
 * */

$('.list-trabalhos').mixItUp();



/*
 * Abas para os formulários de contato
 * */

$(".nav-tabs a").click(function(event) {
    event.preventDefault();
    $(this).parent().addClass("current");
    $(this).parent().siblings().removeClass("current");
    var tab = $(this).attr("href");
    $(".contato-inside").not(tab).css("display", "none");
    $(tab).fadeIn();
});


/*
 * Âncora
 * */

$('.menu-item a').click(function(){
    $('html, body').animate({
        scrollTop: $( $.attr(this, 'href') ).offset().top + 2
    }, 1000);
    return false;
});




/*
 * Modal
 * */

// Abre

$('.open-modal').click(function(){
    $('body').addClass('modal-open');
    $('.modal').addClass('open');
});

$('.close-modal').click(function(){
    $('body').removeClass('modal-open');
    $('.modal').removeClass('open');
    $('.modal-content').remove();
});