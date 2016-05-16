<?php
/**
 * 获取已上传的文件列表
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";

/* 判断类型 */
switch ($_GET['action']) {
    /* 列出文件 */
    case 'listfile':
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];
        break;
    /* 列出图片 */
    case 'listimage':
    default:
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

/* 获取参数 */
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
$end = $start + $size;
$filedir = isset($_GET['filedir'])?htmlspecialchars($_GET['filedir']):'';
if(!empty($filedir)){
    $path = $filedir;
}

/* 获取文件列表 */
$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
$files = getfiles($path, $allowFiles);
//if (!count($files)) {
//    return json_encode(array(
//        "state" => "no match file",
//        "list" => array(),
//        "start" => $start,
//        "total" => count($files)
//    ));
//}

$list = array();
/*获取上一层目录*/
if(!empty($filedir) && $start == 0){
    //主目录
    $tmp_path = $CONFIG['imageManagerListPath'];
    if(substr($tmp_path, strlen($tmp_path) - 1) != '/'){
        $tmp_path .= '/';
    }
    //要浏览的目录
    if(substr($filedir, strlen($filedir) - 1) != '/'){
        $filedir .='/';
    }
    if($filedir != $tmp_path){
        $tmp_upper = substr($filedir, 0,-1);
        $last_dir = strrchr($tmp_upper,'/');
        $upper_dir = substr($tmp_upper, 0, 0 - strlen($last_dir));
        $list[] = array(
            'url'=>$upper_dir.'/',
            'filename'=>'上一层目录',
            'is_dir'=>1
        );
    }
}
/* 获取指定范围的列表 */
$len = count($files);
for ($i = min($end, $len) - 1; $i < $len && $i >= 0 && $i >= $start; $i--){
    $list[] = $files[$i];
}
//倒序
//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

/* 返回数据 */
$result = json_encode(array(
    "state" => "SUCCESS",
    "list" => $list,
    "start" => $start,
    "total" => count($files)
));

return $result;


/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
//function getfiles($path, $allowFiles, &$files = array())
//{
//    if (!is_dir($path)) return null;
//    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
//    $handle = opendir($path);
//    while (false !== ($file = readdir($handle))) {
//        if ($file != '.' && $file != '..') {
//            $path2 = $path . $file;
//            if (is_dir($path2)) {
//                getfiles($path2, $allowFiles, $files);
//            } else {
//                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
//                    $files[] = array(
//                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
//                        'filename'=>basename($path2),
//                        'mtime'=> filemtime($path2)
//                    );
//                }
//            }
//        }
//    }
//    return $files;
//}

function getfiles($path, $allowFiles, &$files = array()){
    $path=iconv('utf-8','gb2312',$path);
    if (!is_dir($path)){
        return null;
    }
    if(substr($path, strlen($path) - 1) != '/') 
    {
        $path .= '/';
    }
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        //$file = '.'代表本目录，$file = '..'代表上一个目录        
        if($file != '.' && $file != '..'){           
            $path2 = $path . $file;
            if(is_dir($path2)){
                $path2=iconv('gb2312','utf-8',$path2);
                $last_dir = str_replace('/','',strrchr($path2,'/'));
                 $files[] = array(
                     'url'=>substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])).'/',
                     'filename'=>$last_dir,
                     'mtime'=> filemtime($path2),
                     'is_dir'=>1
                 );
            }
            else {
                $path2=iconv('gb2312','utf-8',$path2);
                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                    $files[] = array(
                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'filename'=>basename($path2),
                        'mtime'=> filemtime($path2)
                    );
                }
            }
        }
    }
    return $files;
}