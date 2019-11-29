/*
 |--------------------------------------------------------------------------
 | Website Scripts
 | i.e. Bootstrap, jQuery .. etc
 |--------------------------------------------------------------------------
*/

window.$ = window.jQuery = require('jquery');
require('bootstrap');

require('./request-demo');


/*/ 
| Header/Footer navbar scripts
*/

$(window).scroll(function (event) {
	navbarPos();
});

$(document).ready(function(){
	navbarPos();
});

function navbarPos() {
    var scroll = $(window).scrollTop();
    if(scroll <= 50 ) {
        $('header#header').removeClass('scrolled');
        $('header#header').addClass('top');
    } else {
        $('header#header').removeClass('top');
        $('header#header').addClass('scrolled');
    }
}

$('.navbar-toggler').on('click', function() {
    $('header').toggleClass('mobile-open');
});

$(".footer-block h5").click(function(){
    $(this).parent().toggleClass("open"); 
});


/*/ 
| Inner navbars scripts
*/

// $(".faq li").click(function(){
//     $(this).toggleClass("open"); 
// });

$('.tabs-nav a').on('click', function(){
    var sec = $(this).attr('section');

    $('.tabs-nav li').removeClass('active');
    $(this).parent().addClass('active');

    $('.tabs-wrapper section').css('opacity', '0');

    if(sec != 'all') {
        $('.tabs-wrapper section').css('display', 'none');
        $('.tabs-wrapper section.'+sec).css('display', 'block');

        setTimeout(function(){
            $('.tabs-wrapper section.'+sec).animate({"opacity":1});
        }, 200);

    } else {
        $('.tabs-wrapper section').css('display', 'block');
        setTimeout(function(){
            $('.tabs-wrapper section').animate({"opacity":1});
        }, 200)
    }
});

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}
window.findGetParameter = findGetParameter;