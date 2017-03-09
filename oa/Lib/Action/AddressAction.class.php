<?php
include_cache(APP_PATH."/Common/address.php");
class AddressAction extends PublicAction {
    var $name="Address";

	public 	function _initialize(){
		$this->curtitle="通讯薄";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    protected function  _grouplist(){
				$dao=D("AddressGroup");
		        $list=$dao->where("USER_ID='$this->LOGIN_USER_ID'")
		          ->order("GROUP_NAME asc")
		          ->findall();
		          return $list;
    }

	public function privateaddress(){
		$GROUP_ID=$_REQUEST[GROUP_ID];

        $this->menu();
        $this->address($GROUP_ID);
		$this->display();
	}

	/*---------私有方法--显示左边树菜单---------------*/
	protected  function menu(){
//分组
		$dao=D("AddressGroup");
		$menulist=$dao->where("USER_ID='$this->LOGIN_USER_ID'")
		          ->order("GROUP_NAME asc")
		          ->findall();

		$this->assign("menulist",$menulist);
//索引
        import("@.Util.mb");
        //import("@.Util.pinyin");
        $mb= new Mb();
        $sy=$mb->mbs();
	    for($i=ord('A'); $i<=ord('Z'); ++$i) {
			$name[chr($i)]=array();
			$nidx[chr($i)]=array();
		}
		$name['other']=array();
		$nidx['other']=array();

		$dao=D("Address");
		$listsy=$dao->where("USER_ID='$this->LOGIN_USER_ID'")
		          ->order("PSN_NAME")
		          ->findall();
		if ($listsy) {
		    foreach ($listsy as $row){
		    	$s=$row['PSN_NAME'];
				$idx='other';
				if(ord($s[0])>=128)
				{
					$FirstName=substr($s, 0, 3);
					foreach($sy as $key => $s)
					{
						if(strpos($s,$FirstName))
						{
							$idx=strtoupper($key);
							break;
						}
					}
				}
				else
				{
					$FirstName=strtoupper($s[0]);
					if($FirstName>='A' && $FirstName<='Z')
						$idx=$FirstName;
					else $idx='other';
				}
				if(!in_array($FirstName, $nidx[$idx]))
					array_push($nidx[$idx], $FirstName);

				array_push($name[$idx], $row);

		    }
            $INDEX=0;
		    foreach ($name as $key=>$r){
		    			if(count($name[$key])>0)
						{
							   if($key=='other'){
							      $names[$INDEX][TABLE_STR]="<b>其它</b>(".count($name[$key]).") - ".implode(', ', $nidx[$key]);
							      $names[$INDEX][TABLE_STR_URL]=implode(', ', $nidx[$key]);
							   }else{
							      $names[$INDEX][TABLE_STR]="<b>".$key."</b>(".count($name[$key]).") - ".implode(', ', $nidx[$key]);
							      $names[$INDEX][TABLE_STR_URL]=implode(', ', $nidx[$key]);
							   }
						       $names[$INDEX][ID_STR]="";
						       foreach($r as $ROW)
						       {
						          $names[$INDEX][ID_STR].=$ROW[ADD_ID].",";
						       }
						       $INDEX++;
						}

		    }

		}
        $this->assign("names",$names);

		//$this->display();

	}

	/*---------私有方法--显示右边列表---仅限于GROUP_ID--------------*/
	protected  function address($GROUP_ID="0"){ 
		$_GET[GROUP_ID]=intval($GROUP_ID);
		$dao=D(addressgroup);
		$group=$dao->where("GROUP_ID='$_GET[GROUP_ID]'")->find();
 		$GROUP_NAME=$group[GROUP_NAME];
		if($_GET[GROUP_ID]==0)
			$GROUP_NAME="默认";
 			$this->assign("GROUP_NAME",$GROUP_NAME);
        	$this->assign("GROUP_ID",$_GET[GROUP_ID]);
 
		$dao=D("Address"); 
		$map="USER_ID='$this->LOGIN_USER_ID' and GROUP_ID='$_GET[GROUP_ID]'";
		$count=$dao->count($map); 
        import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p= new Page($count,$listRows);
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page); 
		 $list=$dao->where($map)
		          ->order("ADD_ID desc")
		          ->limit("$p->firstRow,$p->listRows")
		          ->findall();        
		 $this->assign("list",$list);

		//$this->display();
	}



	public function search(){
		$this->menu();//显示菜单;
         $list=$this->_grouplist();
		 $this->assign("list",$list);
		 $this->display();
	}

	protected function _search($name='')
    {
        //生成查询条件
		if(empty($name)) {
			$name	=	$this->name;
		}
		$model	=	D($name);
		//$map['USER_ID']=$this->LOGIN_USER_ID;
		$map="USER_ID='$this->LOGIN_USER_ID'";
        foreach($model->getDbFields() as $key=>$val) {
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
            	if($val=="SEX"&&$_REQUEST[$val]=='ALL')
                     continue;
            	if($val=="GROUP_ID"&&$_REQUEST[$val]=='-1')
                     continue;

                if($val=="GROUP_ID"||$val=="SEX"||$val=="USER_ID"){
                    //$map[$val]	=	$_REQUEST[$val];
                    $map.=" and $val='$_REQUEST[$val]'";
                }else{
                    //$map[$val]	=	array('like','%'.$_REQUEST[$val].'%');
                    $map.=" and $val like '%$_REQUEST[$val]%'";
                }
            }
        }

        return $map;
    }

    public function SearchSubmit(){ 
    	$this->menu();
    	if ($_REQUEST[ID_STR]){
    		 $ID_STR=$_REQUEST[ID_STR];
    		 $this->assign("desc","索引结果【".$_REQUEST[TABLE_STR]."】"); 
    		 
             $dao=D("Address");
    		 $map="ADD_ID in ($ID_STR"."0)";
	         $count=$dao->count($map); 
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
			 $p  = new Page($count,$listRows);	
		     $list=$dao->where($map)->limit("$p->firstRow,$p->listRows")->findall();		    		 
		     $page = $p->show();
		     $this->assign("page",$page); 
			 $this->assign("list",$list);

    	}else {
	        $map=$this->_search("Address");
	        $dao=D("Address");
	        if(!empty($dao)) {
	        	$count=$dao->count($map); 
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p  = new Page($count,$listRows);

	        	$list=$dao->where($map)
	        	          ->order("ADD_ID desc")
	        	          ->limit("$p->firstRow,$p->listRows")
	        	          ->findall();
			    //print_r($map);
			    ////$_REQUEST
				foreach($_REQUEST as $key=>$val) {
		                if ($key=='a'||$key=='m'){
		                   continue;
		                }
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.htmlentities(urlencode($t))."&";
						}
					}else{
						$p->parameter   .=   "$key=".htmlentities(urlencode($val))."&";
					}
				}
	        	$page       = $p->show();
		        $this->assign("page",$page);
	        }
	        //echo $dao->getlastsql();
			 $this->assign("list",$list);
			 $this->assign("desc","查询结果");
    	}
		 $this->display();

    }
	/*-----------添加联系人-------------*/
	public function add(){
				$_GET[GROUP_ID]=intval($_GET[GROUP_ID]);
				$dao=D("AddressGroup");
				$group=$dao->where("GROUP_ID='$_GET[GROUP_ID]'")
				           ->find();
				   $GROUP_NAME=$group[GROUP_NAME];
				if($_GET[GROUP_ID]==0)
				   $GROUP_NAME="默认";
				$this->assign("GROUP_NAME",$GROUP_NAME);
		        $this->assign("GROUP_ID",$_GET[GROUP_ID]);

                $list=$this->_grouplist();
		        $this->assign("list",$list);
		        $this->menu();
		        $this->display();
	}

	public function insert(){
		$_POST[USER_ID]=$this->LOGIN_USER_ID;
		if($_POST[BIRTHDAY]!="")
		{
		  if (!is_date($_POST[BIRTHDAY])){
		  	$this->error('日期格式不对,应如：应形如 1999-1-2');
		  }
		}
		$dao=D("Address");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }
        //保存当前数据对象
        $id = $dao->add();
        if($id) { //保存成功
            //成功提示
            $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$_POST[GROUP_ID]");
            $this->success('成功添加');
        }else {
            //失败提示
            $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$_POST[GROUP_ID]");
            $this->error('添加失败');
        }

	}

	public function delete(){
		$GROUP_ID=intval($_REQUEST[GROUP_ID]);
	    if(!$_GET[ADD_ID]){
			$this->error('联系人不存在');
		}
		$dao=D("Address");
		if ($dao->where("ADD_ID='$_GET[ADD_ID]' AND USER_ID='$this->LOGIN_USER_ID'")
		     ->delete()){
		     	  $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$GROUP_ID");
		          $this->success('删除成功');
		          //$this->redirect('address/ADD_ID/$_GET[ADD_ID]','address','','','','1','删除成功');
		}else {
			      $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$GROUP_ID");
			      $this->error('删除失败');
			//      $this->redirect('diary','index');

		}

	}

	/*---------删除多个---------*/
	public function deleteall(){
		$DELETE_STR=$_REQUEST[DELETE_STR];
		$GROUP_ID=intval($_REQUEST[GROUP_ID]);

		$array=explode(",",$DELETE_STR);
		$dao=D("Address");
		foreach ($array as $ADD_ID){
		   if ($ADD_ID) {

		   	 $dao->where("ADD_ID='$ADD_ID' AND USER_ID='$this->LOGIN_USER_ID'")
		         ->delete();
		   	}
		}
         $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$GROUP_ID");
         $this->success('删除成功');
	}
	/*---------删除全部---------*/
	public function deleteall2(){
  	    $GROUP_ID=intval($_REQUEST[GROUP_ID]);
		$dao=D("Address");
     	$dao->where("GROUP_ID='$GROUP_ID' AND USER_ID='$this->LOGIN_USER_ID'")
		    ->delete();
         $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$GROUP_ID");
         $this->success('删除成功');
	}

	public function edit(){
			    if(!$_GET[ADD_ID]){
					$this->error('联系人不存在');
				}
                $list=$this->_grouplist();
		        $this->assign("GROUP_ID",$_GET[GROUP_ID]);
		        $this->assign("list",$list);

		        $dao=D("Address");
		        $user=$dao->where("ADD_ID='$_GET[ADD_ID]'")
		                  ->find();
		        if($user[BIRTHDAY]=="0000-00-00")
		           $user[BIRTHDAY]="";
				$this->assign("user",$user);

		        $dao=D("AddressGroup");
				$group=$dao->where("GROUP_ID='$user[GROUP_ID]'")
				           ->find();
				   $GROUP_NAME=$group[GROUP_NAME];
				if($user[GROUP_ID]==0)
				   $GROUP_NAME="默认";
				$this->assign("GROUP_NAME",$GROUP_NAME);
		        $this->assign("GROUP_ID",$user[GROUP_ID]);


                $this->menu();
		        $this->display();
	}

	public function update(){
		$_POST[USER_ID]=$this->LOGIN_USER_ID;
		if($_POST[BIRTHDAY]!="")
		{
		  if (!is_date($_POST[BIRTHDAY])){
		  	$this->error('日期格式不对,应如：应形如 1999-1-2');
		  }
		}
		$dao=D("Address");
        if(false === $dao->create()) {
        	$this->error($dao->getError());
        }

       //保存当前数据对象
        if($result = $dao->where("ADD_ID='$_POST[ADD_ID]'")->save()) { //保存成功
        //if($result = $dao->save($_POST,"ADD_ID='$_POST[ADD_ID]'")) { //保存成功
            //成功提示
            $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$_POST[GROUP_ID]");
            $this->success('成功修改');
        }else {
            //失败提示
            $this->assign("jumpUrl",__URL__."/privateaddress/GROUP_ID/$_POST[GROUP_ID]");
            $this->error('修改失败');
        }

	}
	public function detail(){
		$ADD_ID=$_REQUEST[ADD_ID];
		$dao=D("Address");
		$row=$dao->where("ADD_ID='$ADD_ID'")->find();
		if($row[SEX]==0){
			$row[SEX]="男";
		}else{
			$row[SEX]="女";
		}
		$this->assign("row",$row);
		$this->menu();
		$this->display();
	}


	/*------------管理分组 -------------*/
	public function group(){
        $dao=D("AddressGroup");
        $list=$dao->where("USER_ID='$this->LOGIN_USER_ID'")->order("GROUP_NAME asc")->findall();
        //echo $dao->getlastsql();
        $this->assign("list",$list);
        $this->menu();
		$this->display();
	}

	public function groupform(){
		$GROUP_ID=$_REQUEST[GROUP_ID];

		if ($GROUP_ID) {
			$desc="编辑分组";
			$dao=D("AddressGroup");
			$row=$dao->where("GROUP_ID='$GROUP_ID'")->find();
			$this->assign("row",$row);

		}else {
			$desc="添加分组";
		}
		$this->assign("desc",$desc);
		$this->menu();
		$this->display();
	}
	public function groupsubmit(){
		$GROUP_ID=$_REQUEST[GROUP_ID];
		$dao=D("AddressGroup");

		if (false===$dao->create()) {
			$this->error("操作失败");
		}
		if ($GROUP_ID) {//修改
			$dao->where("GROUP_ID='$GROUP_ID'")->save();
		    $this->success("成功修改");
		}else {//添加
			$_POST[USER_ID]=$this->LOGIN_USER_ID;
			$dao->add($_POST);
			$this->success("成功添加");
		}

	}
	public function groupdelete(){
		$GROUP_ID=$_REQUEST[GROUP_ID];
		$daoa=D("Address");
		$daoa->setField("group_id","0","GROUP_ID=$GROUP_ID");


		$dao=D("AddressGroup");
		$dao->where("group_id=$GROUP_ID")->delete();
		//echo $daoa->getlastsql();
		$this->redirect("group","Address");
		//$this->success("成功删除");
	}

	public function groupexport(){
		$GROUP_ID=$_REQUEST[GROUP_ID];
		$TYPE=$_REQUEST[TYPE];

		if ($GROUP_ID==0) {
			$GROUP_NAME="默认";
		}else {
			$dao=D("AddressGroup");
			$row=$dao->where("GROUP_ID='$GROUP_ID'")->find();
			$GROUP_NAME=$row[GROUP_NAME];
		}

		$GROUP_NAME = preg_replace("/[[:space:]]/","",$GROUP_NAME);
		$GROUP_NAME = ereg_replace("[[:space:]]","",$GROUP_NAME);

		clearstatcache();//清除文件状态缓存
		ob_end_clean();
		Header("Cache-control: private");
		Header("Accept-Ranges: bytes");
		Header("Content-type: application/octet-stream");
        Header("Content-Disposition: attachment; filename=" . urlencode($GROUP_NAME).".csv");

        //echo "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\" />";
        //Header("Content-Disposition: attachment; filename=aa.csv");
		if ($TYPE==0) {//Foxmail
		$str="姓名,职位,昵称,电子邮件地址,手机,传呼机,QQ,ICQ,性别,生日,配偶,子女,家庭所在地邮政编码,家庭所在街道,家庭电话1,公司,公司所在地邮政编码,公司所在街道,办公电话1,公司传真,附注\n";
		$str=iconv("UTF-8","GB2312",$str);
		echo $str;
		}else {
		$str="姓名,职务,昵称,电子邮件地址,移动电话,寻呼机,QQ,ICQ,性别,生日,配偶,子女,住宅所在地的邮政编码,住宅所在街道,住宅电话,公司,公司所在地的邮政编码,公司所在街道,业务电话,业务传真,附注\n";
		$str=iconv("UTF-8","GB2312",$str);
		echo $str;
		}
		$dao=D("Address");
		$list=$dao->where("USER_ID='$this->LOGIN_USER_ID' and GROUP_ID=$GROUP_ID")->order("ADD_ID desc")->findall();
	    if ($list) {
	        foreach ($list as $ROW){
	        	    $PSN_NAME=$this->_format($ROW["PSN_NAME"]);
				    $SEX=$this->_format($ROW["SEX"]);
				    if($SEX==0)
				       $SEX="男";
				    else
				       $SEX="女";
                    $SEX=iconv("UTF-8","GB2312",$SEX);
				    $BIRTHDAY=$this->_format($ROW["BIRTHDAY"]);

				    $NICK_NAME=$this->_format($ROW["NICK_NAME"]);
				    $MINISTRATION=$this->_format($ROW["MINISTRATION"]);
				    $MATE=$this->_format($ROW["MATE"]);
				    $CHILD=$this->_format($ROW["CHILD"]);

				    $DEPT_NAME=$this->_format($ROW["DEPT_NAME"]);
				    $ADD_DEPT=$this->_format($ROW["ADD_DEPT"]);
				    $POST_NO_DEPT=$this->_format($ROW["POST_NO_DEPT"]);
				    $TEL_NO_DEPT=$this->_format($ROW["TEL_NO_DEPT"]);
				    $FAX_NO_DEPT=$this->_format($ROW["FAX_NO_DEPT"]);

				    $ADD_HOME=$this->_format($ROW["ADD_HOME"]);
				    $POST_NO_HOME=$this->_format($ROW["POST_NO_HOME"]);
				    $TEL_NO_HOME=$this->_format($ROW["TEL_NO_HOME"]);
				    $MOBIL_NO=$this->_format($ROW["MOBIL_NO"]);
				    $BP_NO=$this->_format($ROW["BP_NO"]);
				    $EMAIL=$this->_format($ROW["EMAIL"]);
				    $OICQ_NO=$this->_format($ROW["OICQ_NO"]);
				    $ICQ_NO=$this->_format($ROW["ICQ_NO"]);
				    $NOTES=$this->_format($ROW["NOTES"]);

                    echo "$PSN_NAME,$MINISTRATION,$NICK_NAME,$EMAIL,$MOBIL_NO,$BP_NO,$OICQ_NO,$ICQ_NO,$SEX,$BIRTHDAY,$MATE,$CHILD,$POST_NO_HOME,$ADD_HOME,$TEL_NO_HOME,$DEPT_NAME,$POST_NO_DEPT,$ADD_DEPT,$TEL_NO_DEPT,$FAX_NO_DEPT,$NOTES\n";
	        }
	    }

	}
	/*-------------------导入分组---------------*/
	public function groupimport(){
		$GROUP_ID=$_REQUEST[GROUP_ID];
        $this->menu();
        $this->assign("GROUP_ID",$GROUP_ID);
		$this->display();
	}
	/*---------------导入分组处理程序------------*/
	public function groupimportsubmit(){
		//echo "<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\" />";
		//header('content-type:text/html;charset=utf-8');
		$GROUP_ID=$_REQUEST[GROUP_ID];
		$FILE_NAME=$_FILES[CSV_FILE][name];//或者$FILE_NAME=$_POST[FILE_NAME];
		$CSV_FILE=$_FILES[CSV_FILE][tmp_name];
		if(strtolower(substr($FILE_NAME,-3))!="csv"){
			$this->error("只能导入CSV文件");
		}
		/*
		$info	=	$this->_upload($this->uploadpath);
		$data = $info[0];
		$CSV_FILE=$this->uploadpath.$data[savename];
		*/
		$ID_STR="PSN_NAME,SEX,NICK_NAME,BIRTHDAY,MINISTRATION,MATE,CHILD,DEPT_NAME,ADD_DEPT,POST_NO_DEPT,TEL_NO_DEPT,FAX_NO_DEPT,ADD_HOME,POST_NO_HOME,TEL_NO_HOME,MOBIL_NO,BP_NO,EMAIL,OICQ_NO,ICQ_NO,NOTES";
		$ROW_COUNT = 0;
		//$f=file($CSV_FILE);
		//print_r($f);

		$handle = fopen ($CSV_FILE,"r");
		$line = fgets($handle);
		$TITLE=explode(",",$line);
//		$TITLE = fgetcsv ($handle, 10000, ",");
		/*
		while (!feof($handle)) {
		   $line = fgets($handle);
		     echo $line;
		}
		*/
		if(!$handle || !$TITLE){
			$this->error("打开文件错误");
		}

		$dao=D("Address");

		while (!feof($handle)){
			$ADDRESS=array();
			$line = fgets($handle);
			if ($line){
			$DATA=explode(",",$line);
		    $ROW_COUNT++;
		    foreach ($DATA as $key=>$value){
		        $ADDRESS[USER_ID]=$this->LOGIN_USER_ID;
		        $ADDRESS[GROUP_ID]=$GROUP_ID;
		        if ($this->phpdigVerifyUTF8($TITLE[$key])) {
		        	$TITLE_KEY=$TITLE[$key];
		        }else {
		           $TITLE_KEY=iconv("GB2312","UTF-8",$TITLE[$key]);
		           $value=iconv("GB2312","UTF-8",$value);
		        }

		    	$ID=match($TITLE_KEY);
		        $ADDRESS[$ID]=$value;
		           if($ID=="SEX")
		              if($value=="女")
		                 $ADDRESS[$ID]="1";
		              else
		                 $ADDRESS[$ID]="0";
		    }
		    $dao->create();
		    $dao->add($ADDRESS);
		   }
		}

		//exit();
		fclose ($handle);
		if(file_exists($CSV_FILE))
		   unlink($CSV_FILE);

		$this->success("共".$ROW_COUNT."条数据导入!");

	}

	protected function _format($STR){
		   $STR=str_replace("\"","'",$STR);
		   $STR=iconv("UTF-8","GB2312",$STR);
		   if(strpos($STR,","))
		      $STR="\"".$STR."\"";
		   return $STR;
	}

	/*-------------是不是utf8---------*/
	protected 	function phpdigVerifyUTF8($str) {
	  // verify if a given string is encoded in valid utf-8
	  if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
	    return true;
	  }
	  else {
	    return false;
	  }
	}

	protected function is_utf8($str){ //是UTF8,则回穿1。不是，回传0
		return preg_match('%^(?:
		[\x09\x0A\x0D\x20-\x7E] # ASCII
		│ [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
		│ \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
		│ [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
		│ \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
		│ \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
		│ [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
		│ \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
		)*$%xs', $str);

	}


}
?>