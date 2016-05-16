var target=[] 
var time_id=[]
var leftTime = [];
var jsTimeSpan;
var ServerTime;

function SystemTimeSpan() {

    // var xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    //xmlhttp.open("GET", "http://www.xiangshe.com", false);
    //xmlhttp.setRequestHeader("Range", "bytes=-1");
    //xmlhttp.send();
    //var server_time = new Date(xmlhttp.getResponseHeader("Date"));

    //var TimeSpan = server_time.getTime() - new Date().getTime(); //当前时间与服务时间相差多少毫秒数
    //return TimeSpan;

    // var ServerTime = "FormatHelper.NowDateTimeForJsTime();"; 
    //var leftTime_NowDateTime = new Date(ServerTime);

    //var leftTime_NowDateTime = new Date(FormatHelper.NowDateTimeForJsTime());
    jsTimeSpan = new Date(ServerTime).getTime() - new Date().getTime(); //当前时间与服务时间相差多少毫秒数

}
function SystemDateTime() {

    var nowDate = new Date();

    nowDate.setTime(nowDate.getTime()+jsTimeSpan);
    
    return nowDate;
}
/*
  原理:每次和当前时间比较,得到天、小时、分、秒
*/
function show_date_time_0()
{
try
{
    setTimeout("show_date_time_0()", 1000); 
    for (var i=0,j=target.length;i<j;i++)
    {
        //today = new Date();
        //today.setTime(today.getTime() + TimeSpan);
        //today.setTime(today.getTime() + SystemTimeSpan());
        today = SystemDateTime();

        //alert(today);
        
        //计算目标时间与当前时间间隔(毫秒数)
        var timeold = target[i] - today; //getTime 方法返回一个整数值，这个整数代表了从 1970 年 1 月 1 日开始计算到 Date 对象中的时间之间的毫秒数。
        
        //计算目标时间与当前时间的秒数
        var sectimeold=timeold/1000; 
        
        //计算目标时间与当前时间的秒数(整数)
        var secondsold=Math.floor(sectimeold);
        
        //计算一天的秒数 
        var msPerDay=24*60*60*1000; 
       

        //得到剩余天数
        var e_daysold=timeold/msPerDay; 
         //得到剩余天数(整数)
        var daysold=Math.floor(e_daysold); 
        
        //得到剩余天数以外的小时数
        var e_hrsold=(e_daysold-daysold)*24; 
         //得到剩余天数以外的小时数(整数)
        var hrsold=Math.floor(e_hrsold); 
        
        //得到尾剩余分数
        var e_minsold=(e_hrsold-hrsold)*60; 
        //得到尾剩余分数(整数)
        minsold=Math.floor((e_hrsold-hrsold)*60); 
        
        //得到尾剩余秒数(整数)
        seconds=Math.floor((e_minsold-minsold)*60); 
        try
        {
            document.getElementById(leftTime[i]).innerHTML=timeold; 
        }
        catch(e){}
        if (daysold<0) 
        { 
            //document.getElementById(time_id[i]).innerHTML="本次特价抢购已结束，敬请期待下期。";
            document.getElementById(time_id[i]).innerHTML="<b>"+0+"</b><i></i><span>小时</span><b>"+0+"</b><i></i><span>分</span><b>"+0+"</b><span>秒</span>"; 
 
        } 
        else 
        { 
            
            //天数取三位,不足时前边补0
            if (daysold<10) { daysold=""+daysold } 
            //天数取三位,不足时前边补0
            if (daysold<100) { daysold=""+daysold } 
            
            //小时取两位,不足补0
            if (hrsold<10) { hrsold=""+hrsold } 
            //分数取两位,不足补0
            if (minsold<10) {minsold=""+minsold} 
            //秒数取两位,不足补0
            if (seconds<10) {seconds=""+seconds} 
           
            //小于100天时,字体为红色
            if (daysold<1) { 
                
                var zongHrSold=parseInt(daysold)*24+parseInt(hrsold);
                 
                $("#"+time_id[i]).html("<b>"+zongHrSold+"</b><i></i><span>小时</span><b>"+minsold+"</b><i></i><span>分</span><b>"+seconds+"</b><span>秒</span>"); 
               
            } 
            else 
            { 
                
                 $("#"+time_id[i]).html("<b>"+daysold+"</b><strong>天</strong><b>"+hrsold+"</b><i></i><span>小时</span><b>"+minsold+"</b><i></i><span>分</span><b>"+seconds+"</b><span>秒</span>"); 
            } 
        }
         
    } 
 }catch(e)
 {
    //alert(e);
 }
}
setTimeout("SystemTimeSpan()", 100); 
//SystemDateTime();
setTimeout("show_date_time_0()", 100);

