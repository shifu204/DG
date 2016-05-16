$(function(){
    
    $('.WeChat').hover(function(){
        $(this).find('img').show();
    },function(){
        $(this).find('img').hide();
    });
    
    $('.advertisement-img .close').click(function(){
        $('.advertisement-bg,.advertisement').hide(); 
    });
    
    $('.advertisement-img .btn').click(function(){
        $('.advertisement-bg,.advertisement').hide(); 
    });
});
window.onload=function(){
    $('.advertisement-bg,.advertisement').css({'height':$(document).height(),'width':$(document).width()});
};