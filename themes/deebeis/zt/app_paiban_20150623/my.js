$(function(){
//    var hhh=window.screen.width;
//    alert(hhh);
//    $('.wrapper').width(hhh);
    var hh=$('.exchange-table').width();
    var hh2=hh*0.2;
    $(".exchange-table tr").each(function(){
        $(this).find("td").eq(1).find('.name').css({'width':hh2+'px'});
    });
    var h=$(".praise").width();
    $(".praise").css({'height':h+'px','line-height':h+'px'});

    var h3=$(".app-down").height();
    $(".app-down .down").css({'line-height':h3+'px'});
    
    
});