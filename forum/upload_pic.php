<?php
   $result = 0;
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
   $a=strtolower(fileext($_FILES['myfile']['name']));
   //判断文件类型
   if(!in_array(strtolower(fileext($_FILES['myfile']['name'])),$type))
     {
      $text=implode(",",$type);
      echo "您只能上传以下类型文件: ",$text,"<br>";
	}
   //生成目标文件的文件名
   else{
    $filename=explode(".",$_FILES['myfile']['name']);
        do
        {
            $filename[0]=random(10); //设置随机数长度
            $name=implode(".",$filename);
            $uploadfile=$uploaddir.$name;
        }
		while(file_exists($uploadfile));

        if (move_uploaded_file($_FILES['myfile']['tmp_name'],$uploadfile)){
            if(!is_uploaded_file($_FILES['myfile']['tmp_name'])){
                 	$result = 1;
					$r_name = $name;
              }
        }
   }
sleep(1);

?>

<script language="javascript" type="text/javascript">window.parent.window.stopUpload(<?=$result?>,'<?=$r_name?>');</script>
