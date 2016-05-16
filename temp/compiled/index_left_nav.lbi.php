<div class="left-side-buoy">
    <div class="iln_1"></div>
    <a href="http://www.deebei.net/topic.php?topic_id=25" target="_blank">
        <div class="aa1">       
            <div class="iln_2"></div>
        </div>
    </a>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".iln_1").click(function(){
            $(".left-side-buoy").fadeOut("slow");
        });
        side_bar_fixed( $(".left-side-buoy"),'left','undefined','top',322, 0 );
        scroll_fixed($(".left-side-buoy"), "left-side-buoy-show",$("#left-side-buoy"),'undefined',322);
    }); 
</script>