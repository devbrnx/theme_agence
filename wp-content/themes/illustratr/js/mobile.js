/**
 * Created by Urien on 10/02/2015.
 */

jQuery(document).ready(function ($) {


    var isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone/i);
        },
        iPad: function () {
            return navigator.userAgent.match(/iPad/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };
    if (isMobile.any()) {

        $('.clients').on('click', function(){
            $(this).find(".aligncenter").slideToggle();

        })
    }else if(isMobile.iPad()){
        $('.clients').on('click', function(){
               $(this).children().show().delay(400);
        });
    }
});
 