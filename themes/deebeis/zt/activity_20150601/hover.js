$(function(){
    $(".goodbox").hover(
        function(){
            $(this).find("a").append("<div class='hover'></div>");
        },
        function(){
            $(this).find(".hover").remove();
        }
    );
});