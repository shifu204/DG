<?php
$params = array();
if (isset($_GET['year']) && isset($_GET['month'])) {
    $params = array(
        'year' => $_GET['year'],
        'month' => $_GET['month'],
    );
}
$params['url']  = 'demo.php';
require_once 'calendar.class.php';
?>

<html>
    <head>
        <title>艾宾浩斯遗忘曲线日历 | Calendar of Ebbinghaus Forgetting Curve</title>
        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
        <style type="text/css">
            body { font-family: Arial, '微软雅黑'; font-size:12px; }
            li { list-style-type: none; }
            table.calendar { border: 1px solid #050; width:100%; height:100%; border-collapse:collapse; border:0; }
            .calendar th, .calendar td { width:14.2%; text-align:center; padding:5px; }            
            .calendar th { background-color:#CCC; }
            .head, .week_head { height:30px; line-height:30px; }
            .week td { text-align:left; vertical-align:top; border:1px solid #DDD; }
            .week td b { float:left; font-weight:normal; }
            .week td ul { float:left; margin:0; font-size:13px; }
            .today{ background: #EAEAEA; border-left: 1px solid #666666; }
        </style>
    </head>
    <body>
        <div style="align:center">
            <?php
                $cal = new Calendar($params);
                $cal->display();
            ?>    
        </div>
    </body>
</html>
