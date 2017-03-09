<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?php
include_once('./ini.php');
$uploaddir = "./uploadfiles/";//设置文件保存目录 注意包含/
   $type=array("jpg","gif","bmp","jpeg","png");//设置允许上传文件的类型

   //获取文件后缀名函数
      function fileext($filename)
    {
        return substr(strrchr($filename, '.'), 1);
    }
   //生成随机文件名函数
    function random($length)
    {
        $hash = 'CR-';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        mt_srand((double)microtime() * 1000000);
            for($i = 0; $i < $length; $i++)
            {
                $hash .= $chars[mt_rand(0, $max)];
            }
        return $hash;
    }

   $a=strtolower(fileext($_FILES['pic_forum']['name']));
   //判断文件类型
   if(!in_array(strtolower(fileext($_FILES['pic_forum']['name'])),$type))
     {
       // $text=implode(",",$type);
       // echo "您只能上传以下类型文件: ",$text,"<br>";
	   echo "<center>您没有上传图标！系统将使用默认图标！</center>";
     }
   //生成目标文件的文件名
   else{
    $filename=explode(".",$_FILES['pic_forum']['name']);
        do
        {
            $filename[0]=random(10); //设置随机数长度
            $name=implode(".",$filename);
            $uploadfile=$uploaddir.$name;
        }

		while(file_exists($uploadfile));

        if (move_uploaded_file($_FILES['pic_forum']['tmp_name'],$uploadfile)){

            if(!is_uploaded_file($_FILES['pic_forum']['tmp_name'])){
                //输出图片预览
                echo "<center>讨论组图标上传成功 图片预览: </center><br><center><img width=100 src='$uploadfile'></center>";

              }
              else{
                echo "上传失败！";
              }
        }
   }
$forum_name=isset($_POST['new_forum'])?$_POST['new_forum']:'';
$forumid=isset($_POST['forumid'])?$_POST['forumid']:'';
$forum_pic=isset($uploadfile)?$uploadfile:'';
$act=isset($_GET['act'])?$_GET['act']:'';
$fieldid=isset($_POST['fieldid'])?$_POST['fieldid']:0;
$announcement=isset($_POST['announcement'])?$_POST['announcement']:'';
$icon=isset($_POST['icon'])?$_POST['icon']:'0';
if($icon!=0){
	$icoimg="ico/".$icon.".gif";
}else{
	$icoimg=isset($_POST['ico'])?$_POST['ico']:'ico/1.gif';
}

if($forum_name!=''&&$act!='edit'&&$forum_pic!=''&&$sub_pic!=1){
	$sql="INSERT INTO `forum` ( `forumname` , `announcement` , `pic`,`fieldid` ) VALUES ( '".$forum_name."','".$announcement."','".$forum_pic."','".$fieldid."')";
	if(mysql_query($sql)){
	forum_showmessage('讨论组创建成功！', "manage.php", 1);
	}else{
	forum_showmessage('讨论组创建失败！', "manage.php", 1);
	}
}elseif($forum_name!=''&&$act!='edit'&&$forum_pic==''&&$sub_pic!=1){
	$sql="INSERT INTO `forum` (`forumname`,`announcement`,`moderator`,`fieldid`) VALUES ('".$forum_name."','".$announcement."','".$icoimg."','".$fieldid."')";
	if(mysql_query($sql)){
	forum_showmessage('讨论组创建成功！', "manage.php", 1);
	}else{
	forum_showmessage('讨论组创建失败！', "manage.php", 1);
	}
}elseif($act=='edit'&&$forum_pic!=''){
	$sql2="UPDATE `forum` SET `forumname`='".$forum_name."', `pic`='".$forum_pic."'	,`announcement`='".$announcement."',`fieldid`='".$fieldid."'  WHERE  `forumid`='".$forumid."'";
	if(mysql_query($sql2)){
	forum_showmessage('讨论组更新成功！', "manage.php", 1);
	}else{
	forum_showmessage('讨论组更新失败！', "manage.php", 1);
	}
}elseif($act=='edit'&&$forum_pic==''){
	$sql2="UPDATE `forum` SET `forumname`='".$forum_name."', `announcement`='".$announcement."',`moderator`='".$icoimg."',`fieldid`='".$fieldid."',`pic`='' WHERE  `forumid`='".$forumid."'";
	if(mysql_query($sql2)){
	forum_showmessage('讨论组更新成功！', "manage.php", 1);
	}else{
	forum_showmessage('讨论组更新失败！', "manage.php", 1);
	}
}

else{
	forum_showmessage('讨论组更新失败！', "manage.php", 1);
}
?>
