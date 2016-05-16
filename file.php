<?php
//echo phpinfo();
//print_r($_SERVER);
$fp = fopen('D:\wwwroot\web\test.txt', 'r+');
fwrite($fp, $_SERVER['REQUEST_URI']);
//if (fwrite($fp, 'haha')) echo 'ok';
//else echo 'no';
fclose($fp);

//echo file_put_contents("test.txt","Hello World. Testing!");