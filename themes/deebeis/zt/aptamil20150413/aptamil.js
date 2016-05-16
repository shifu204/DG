$(function(){
    $(".introduce_wrap > .wrap").hover(function(){
        $(this).find("div").stop(true, true).slideDown();
    },
    function(){
        $(this).find("div").stop(true, true).slideUp();   
    });
});