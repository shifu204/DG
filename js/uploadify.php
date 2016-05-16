<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/
define('IN_ECS', true);
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/lib_time.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/cls_image.php');
$image = new cls_image();


// Define a destination
$dir = '/images/shaidan/';
//按年月文件夹分类
$time = gmtime();
$time_floder = date("Ym");
$dir .=$time_floder;

$targetFolder = ROOT_PATH.$dir.'/source/'; // Relative to the root
$normalFolder = ROOT_PATH.$dir.'/normal/';
$thumbFolder = ROOT_PATH.$dir.'/thumb/';
$false_folder = 0;
if(!make_dir($targetFolder)){
    $false_folder ++;
}
if(!make_dir($normalFolder)){
    $false_folder ++;
}
if(!make_dir($thumbFolder)){
    $false_folder ++;
}
if($false_folder > 0 ){
    echo 'no_dir';
    exit;
}
$verifyToken = $_POST['token'];

// 最多上传5张图片
$sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('shaidan_img')
     . ' WHERE token = "' . $verifyToken . '"';
$img_count = $GLOBALS['db']->getOne($sql);
if ($img_count == 5) {
    echo 'five';
    exit;
}

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $targetFolder;
        //获取文件名后缀，然后重命名文件
        //$ext = pathinfo( $_FILES['Filedata']['name'], PATHINFO_EXTENSION);
        $_FILES['Filedata']['name'] = gmtime().'.jpg';
        $file_name = preg_replace('/.\./', $verifyToken . '_' . substr(time(), 6, 4) . '.', $_FILES['Filedata']['name']);
	$targetFile = rtrim($targetPath,'/') . '/' . $file_name;
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
            move_uploaded_file($tempFile,$targetFile);
            echo $dir."/normal/".$file_name;
            $source_img_path =$targetFolder . $file_name;
            // 去除后缀名
            $make_filename = preg_replace('/\..*$/U', '', $file_name);
            // 判断原图的宽高
            $image_size    = getimagesize($image_file); 
            if ($image_size[0] > 640 || $image_size[1] > 540) {
                $normal_img_width = 640;
                $normal_img_height = 540;
            } else {
                $normal_img_width = $image_size[0];
                $normal_img_height = $image_size[1];
            }
            // 生成普通图
            $image->make_thumb($source_img_path, 640, 0,
                $normalFolder, '', $make_filename);
            // 生成缩略图
            $image->make_thumb($source_img_path, 200, 0,
                $thumbFolder, '', $make_filename);
	} else {
		echo 'Invalid file type.';
	}
}
?>
