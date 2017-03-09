<?php
class AttachAction extends PublicAction {
	public function view(){
		$attid=$_REQUEST[attid];
		$ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];
		$OP=$_REQUEST[OP];
		if ($ATTACHMENT_ID) {
			$attid=$ATTACHMENT_ID;
		}
		$dao=D("Attachments");
		$row=$dao->where("attid='$attid'")->find();
		$url=$this->uploadpath.$row[attachment];
		/*
		
		$row[filename] = str_replace(" ","",$row[filename]);   //去掉英文空格 
		$row[filename]=str_replace(chr(32),"",$row[filename]);  //去掉中文空格
		$row[filename]=str_replace(chr(161),"",$row[filename]);  //去掉中文空格
		$row[filename] = str_replace(chr(227),"",$row[filename]);   //去掉utf-8空格
		*/
		
		$row[filename] = preg_replace("/[[:space:]]/","",$row[filename]);
		$row[filename] = ereg_replace("[[:space:]]","",$row[filename]);

		if (!file_exists($url)){
			$this->error("找不到文件，位于服务器：".$url);
		}
		clearstatcache();//清除文件状态缓存
		$fp = fopen($url,"rb");
		$file_ext=strtolower(substr($row[attachment],strpos($row[attachment],".")));
		switch ($file_ext) {
			default:
               $content_type=1;
               $content_type_desc="application/octet-stream";				
				break;
		}
		
		if($OP==1||$content_type==1){
		   ob_end_clean();
		   Header("Cache-control: private");
		   Header("Content-type: $content_type_desc");
		   Header("Accept-Ranges: bytes");
		   Header("Accept-Length: ".filesize($url));
		
		   if($content_type==1){
		      Header("Content-Disposition: attachment; filename=" . urlencode($row[filename]));
		      //Header("Content-Disposition: attachment; filename=" . str_replace("+","%20",urlencode($row[filename])));
		   }else{
		      Header("Content-Disposition: ; filename=" . urlencode($row[filename]));
		      //Header("Content-Disposition: ; filename=" . str_replace("+","%20",urlencode($row[filename])));   
		   }
		}
		
		while (!feof ($fp)){
		   echo fread($fp,5000);
		}
		fclose($fp);
		
	}
	
	/*------------------阅读或编辑文件-------------*/
	public function read(){
		$attid=$_REQUEST[attid];
		$ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];
		$OP=$_REQUEST[OP];
		if ($ATTACHMENT_ID) {
			$attid=$ATTACHMENT_ID;
		}
		$SIGN_KEY=$ATTACHMENT_ID;
		$dao=D("Attachments");
		$row=$dao->where("attid='$attid'")->find();
		$url=$this->uploadpath.$row[attachment];
		
		if (!file_exists($url)){
			$this->error("找不到文件，位于服务器：".$url);
		}
				
        $dao=D("User");
        $user=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->find();
		$USER_NAME=$user["USER_NAME"];
		
		if($OP==4)
		   $IE_TITLE="Office 文档在线编辑";
		else
		   $IE_TITLE="Office 文档在线阅读";		
		
		
		$this->assign("curtitle",$IE_TITLE);   
		$this->assign("USER_NAME",$USER_NAME);
		$this->assign("OP",$OP);
		$this->assign("SIGN_KEY",$SIGN_KEY);
		$this->assign("ATTACHMENT_ID",$ATTACHMENT_ID);
		
		$this->assign("ATTACHMENT_NAME",$row[filename]);
		//$this->display();
		$this->display("read2");
	}
	/*--------------显示word,excel,ppt等文件内容------------*/
	public function attach(){
		
		$attid=$_REQUEST[attid];
		$ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];
		$OP=$_REQUEST[OP];
		if ($ATTACHMENT_ID) {
			$attid=$ATTACHMENT_ID;
		}
		$dao=D("Attachments");
		$row=$dao->where("attid='$attid'")->find();
		$url=$this->uploadpath.$row[attachment];

		if (!file_exists($url)){
			$this->error("找不到文件，位于服务器：".$url);
		}
			clearstatcache();
			$fp = fopen($url,"rb");
			if(!$fp)
			{
			 echo "找不到文件：".$url;
			 exit; 
			}
			
			ob_end_clean();
			header("Cache-control: private");
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
			Header("Accept-Length: ".filesize($url));
			Header("Content-Disposition: attachment; filename=".$row[filename]);
			
			while (!feof ($fp))
			{
			  echo fread($fp,50000);
			}
			
			fclose($fp);
		
		
		
	}
	
	
	/*-----------------------运行windows程序-------cab文件有问题---*/
	public function winexe(){
		$NAME=$_REQUEST[NAME];
		$PROG=$_REQUEST[PROG];
		if(strstr($PROG,"format")){
			$this->error("非法程序");
         }
        //$PROG=str_replace("/","\\\\",$PROG);
        //$PROG="E:\Program Files\CaihongIP\CaiHong.exe";
        //ECHO $PROG;EXIT;
        $this->assign("NAME",$NAME);
        $this->assign("PROG",$PROG);
        $this->display();
	}
	
	/**
	 * kindeditor编辑器上传附件
	 * 
	 * */
	public function kindeditorUpload(){
		$dao=D("Attachments");
		$arr=$dao->fileinfo_save(array('maxSize' => 1024*1024*2, 
							           'allowExts' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'), 
							           'allowTypes' => array('image/pjpeg', 'image/gif', 'image/x-png', 'image/bmp')));
        $file_url=$this->imgpath.$arr[attachment];
        
    //插入图片，关闭层
    echo '<html>';
    echo '<head>';
    echo '<title>Insert Image</title>';
    echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
    echo '</head>';
    echo '<body>';
    echo '<script type="text/javascript">parent.KE.plugin["image"].insert("' . $_POST['id'] . '", "' . $file_url . '","' . $_POST['imgTitle'] . '","' . $_POST['imgWidth'] . '","' . $_POST['imgHeight'] . '","' . $_POST['imgBorder'] . '");</script>';
    echo '</body>';
    echo '</html>';
	}
	
	/*--------------新浪编辑器上传附件-----------------*/
	public function editorUpload(){
		    $loadtype=$_REQUEST[loadtype];
			if (!empty($_FILES)){
			$path	=	$this->uploadpath;
			$info	=	$this->_upload($path);
			$data =$info[0];
            //echo "<script>alert('aa')</script>";
		   // exit;			
			if($data){
				$data[addtime]=$this->CUR_TIME_INT;
				$data[filename]=$data[name];
				$data[attachment]=$data[savename];
				$data[filesize]=$data[size];
				$data[filetype]=$data[type];
				$dao = D("Attachments");
				$dao->create();
				$attid=$dao->add($data);
				
				$message = "<script language='javascript'>";
				if ($loadtype=="attach") {
				    $imgname=$data[filename];
				    $path="/oa/Uploads/".$data[attachment];	
				    $message .= "window.parent.LoadIMG('".$path."','".$imgname."');";
				}else {
				    $path="/oa/Uploads/".$data[attachment];	
				    $message .= "window.parent.LoadIMG('".$path."');";	
				}

				
				
				//$message .="window.close();";
				//$message .= "window.parent.ext_gif = '".substr($upload->saveto,-3).".gif';";
				//$message .= "window.parent.phpcms_path = '".PHPCMS_PATH."';";
				//$message .= "window.parent.".$uploadtext.".value='".PHPCMS_PATH.$upload->saveto."';";
				$message .= "</script>";
				echo $message;		
			    /*
				if($attid){	
			        $_POST[ATTACHMENT_ID]=$_POST[ATTACHMENT_ID_OLD].$attid.",";
					$_POST[ATTACHMENT_NAME]=$_POST[ATTACHMENT_NAME_OLD].$data[name]."*";
			    }
			    */
			    
			}
		}
		
		
	}
	
	
	
}


?>