<?php
//import("@.Action.PublicAction");
include_cache(APP_PATH."/Common/file.php");
class FileAction extends PublicAction {
	var $filesort="";
	function _initialize(){
		if($_REQUEST['file_sort']==1){
		   $this->filesort="公共文件柜";
		}elseif($_REQUEST['file_sort']==2){
		   $this->filesort="个人文件柜";
		}elseif ($_REQUEST['file_sort']==3){
		   $this->filesort="共享文件柜";
		}else {
			$this->filesort="";	
		}
		$this->curtitle=$this->filesort;
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	/*---------入口-显示iframe框架--------*/
	public  function index(){		
		$sort_id=$_REQUEST['sort_id'];
		$file_sort=$_REQUEST['file_sort'];
		$rlist=$this->my_xml_tree(0,$file_sort);//生成树
		if ($sort_id){  
			$tree_parent=$this->_tree_parent($sort_id); 
			$dao=D("FileSort");
            $new_priv=$manage_priv=$down_priv=$touser_priv=0;
			$row=$dao->where("SORT_ID=$tree_parent")->find(); 
			if ($row[SORT_TYPE]!=4){
				$dao_user=D("User");
				$row_user=$dao_user->where("USER_ID='$this->LOGIN_USER_ID'")->find(); 
				  if(find_id($row[NEW_USER],"U_".$row_user[uid])||find_id($row[NEW_USER],"P_".$row_user[USER_PRIV])||find_id($row[NEW_USER],"D_".$row_user[DEPT_ID])){
			         $new_priv=1;
				  }
			      if(find_id($row[MANAGE_USER],"U_".$row_user[uid])||find_id($row[MANAGE_USER],"P_".$row_user[USER_PRIV])||find_id($row[MANAGE_USER],"D_".$row_user[DEPT_ID])){
			         $manage_priv=1; 
			      }
			      if(find_id($row[DOWN_USER],"U_".$row_user[uid])||find_id($row[DOWN_USER],"P_".$row_user[USER_PRIV])||find_id($row[DOWN_USER],"D_".$row_user[DEPT_ID])){
			         $down_priv=1;
			      }
			      if($row[DEPT_ID]){
			      	 $dao=D("Department");
			      	 $dps=$dao->where("DEPT_ID='$row[DEPT_ID]'")->find();
			      	 $dept_name=$dps[DEPT_NAME];
			      }
			}else{
               $new_priv=$manage_priv=$down_priv=$touser_priv=1; 
			} 
			switch ($row[SORT_TYPE]){
				       case "1":  	$SORT_TYPE_DESC="全体";   break;
				       case "2":  	$SORT_TYPE_DESC=$dept_name;
				                		if($row[DEPT_ID]!=$this->LOGIN_DEPT_ID)  exit; break;
				       case "3":
				                	$SORT_TYPE_DESC="指定人员";
				                		if(!find_id($row[TO_USER_ID],$this->LOGIN_USER_ID))  exit;  break;
				       case "4":  	$SORT_TYPE_DESC="个人";  break;
			} 
			$dao=D("FileSort");
            $myrow=$dao->where("SORT_ID='$sort_id'")->find();
			###################本文件夹下的文件列表
	        $dao=D("FileContent");
	        $map=array("SORT_ID"=>$sort_id);
	        $count=$dao->count($map); 
	        if($count>0){
	            import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p = new Page($count,$listRows);
				//分页查询数据
                $file=$dao->where($map)->limit("$p->firstRow,$p->listRows")->order("CONTENT_ID desc")->findall();
                //分页跳转的时候保证查询条件
				foreach($map as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";
					}
				}
			//分页显示
			$page = $p->show();
			$this->assign("page",$page);
			$this->assign('file',$file);
	        }else{
	        	$msg=msg("","该文件夹尚无文件");
	        	$this->assign('msg',$msg);
	        }

	        $this->assign("count",$count);
	        $this->assign('row',$row);
	        $this->assign('SORT_NAME',$row[SORT_NAME]);

            $mysorttree=my_sort_tree($sort_id,$file_sort,0,$this->LOGIN_DEPT_ID,$this->LOGIN_USER_ID,1);
            $this->assign('mysorttree',$mysorttree);
		}else{
			    if($file_sort==1){
				   $msg=msg("","请选择文件夹进行查阅");
				   $this->assign('msg',$msg);
			    }else{
				   $msg=msg("","请选择文件夹进行阅读<br>或创建新的个人文件夹");
				   $this->assign('msg',$msg);
			    } 
		}
		############共享功能############
		#########弹出框开始#########
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  
		$list1=$dao_d->DeptSelect();
		$dao_p=D("UserPriv");//按角色选择人员
		$list2=$dao_p->findall();
		$dao_u=D("User");//按个人选择人员
		$list3=$dao_u->findall();
		$list_d = $list_p = $list_u = array(); 
		$TO_USER_ID=explode(",",$myrow[TO_USER_ID]);
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if ($b=="D"){ 
            			$list4 = $dao_d->where("DEPT_ID='$c'")->find(); 
            			$listall[$key][id]="D_".$list4[DEPT_ID];
            			$listall[$key][name]=$list4[DEPT_NAME];
            		}
            		if ($b=="P"){ 
            			$list4 = $dao_p->where("USER_PRIV='$c'")->find();
            			$listall[$key][id]="P_".$list4[USER_PRIV];
            			$listall[$key][name]=$list4[PRIV_NAME];
 					}
 					if ($b=="U"){ 
            			$list4 = $dao_u->where("uid='$c'")->find();
            			$listall[$key][id]="U_".$list4[uid];
            			$listall[$key][name]=$list4[USER_NAME]; 
  					}   	       
        }
        ########容量提示#########
        $dao_FS=D("FileSort");
        $dao_FC=D("FileContent");
        $dao_AT=D("Attachments");
        $row_sortid=$dao_FS->where("USER_ID='$this->LOGIN_USER_ID'")->findall();
        $sort_id_array=$attid_array=$attid_in_attachments=$att_size=$att_all_size=array();
        foreach ($row_sortid as $key=>$v){
        	$row_attid_file=$dao_FC->where("SORT_ID='".$v['SORT_ID']."'")->findall();
        	foreach ($row_attid_file as $key2=>$s){
        		 $attid_array[]=$s['ATTACHMENT_ID'];
        	}
        } 
        for($i=0;$i<count($attid_array);$i++){  
        	$attid_in_array.= $attid_array[$i]; 
         	$attid_in_attachments=explode(",",$attid_in_array);
        } 
        for($j=0;$j<count($attid_in_attachments);$j++){
        	$attid_in_attachments2 = $attid_in_attachments[$j];
        	if(!empty($attid_in_attachments2)){ 
        		$row_attid_attachments=$dao_AT->where("attid='".$attid_in_attachments2."'")->findall(); 
		        foreach ($row_attid_attachments as $key3=>$vv){
        			 $att_size[]=$vv['filesize'];
        		} 
        	} 
        }
        $att_all_size=0;
        for($k=0;$k<count($att_size);$k++){
        	$att_all_size+=$att_size[$k]; 
        }
        $att_all_size/=(1024*1024); 
        $att_all_size=substr($att_all_size,0,5);
        $this->assign("att_all_size",$att_all_size); 
		$this->assign("list1",$list1); 
		$this->assign("list2",$list2);  
		$this->assign("list3",$list3); 
		$this->assign("listall",$listall);
		$this->assign("TO_USER_ID",$TO_USER_ID);
		$this->assign("myrow",$myrow);
		$this->assign("list",$rlist);
		$this->assign('SORT_TYPE_DESC',$SORT_TYPE_DESC);  
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID); 
		$this->assign("new_priv",$new_priv);
		$this->assign("manage_priv",$manage_priv);
		$this->assign("down_priv",$down_priv);
		$this->assign("touser_priv",$touser_priv);
		$this->assign("sort_id",$sort_id);
		$this->assign("file_sort",$file_sort);
		$this->display();
	}
	public  function index2(){		
		$sort_id=$_REQUEST['sort_id'];
		$file_sort=$_REQUEST['file_sort'];
		$rlist=$this->my_xml_tree(0,$file_sort);//生成树
		if ($sort_id){  
			$tree_parent=$this->_tree_parent($sort_id); 
			$dao=D("FileSort");
            $new_priv=$manage_priv=$down_priv=$touser_priv=0;
			$row=$dao->where("SORT_ID=$tree_parent")->find(); 
			if ($row[SORT_TYPE]!=4){
				$dao_user=D("User");
				$row_user=$dao_user->where("USER_ID='$this->LOGIN_USER_ID'")->find(); 
				  if(find_id($row[NEW_USER],"U_".$row_user[uid])||find_id($row[NEW_USER],"P_".$row_user[USER_PRIV])||find_id($row[NEW_USER],"D_".$row_user[DEPT_ID])){
			         $new_priv=1;
				  }
			      if(find_id($row[MANAGE_USER],"U_".$row_user[uid])||find_id($row[MANAGE_USER],"P_".$row_user[USER_PRIV])||find_id($row[MANAGE_USER],"D_".$row_user[DEPT_ID])){
			         $manage_priv=1; 
			      }
			      if(find_id($row[DOWN_USER],"U_".$row_user[uid])||find_id($row[DOWN_USER],"P_".$row_user[USER_PRIV])||find_id($row[DOWN_USER],"D_".$row_user[DEPT_ID])){
			         $down_priv=1;
			      }
			      if($row[DEPT_ID]){
			      	 $dao=D("Department");
			      	 $dps=$dao->where("DEPT_ID='$row[DEPT_ID]'")->find();
			      	 $dept_name=$dps[DEPT_NAME];
			      }
			}else{
               $new_priv=$manage_priv=$down_priv=$touser_priv=1; 
			} 
			switch ($row[SORT_TYPE]){
				       case "1":  	$SORT_TYPE_DESC="全体";   break;
				       case "2":  	$SORT_TYPE_DESC=$dept_name;
				                		if($row[DEPT_ID]!=$this->LOGIN_DEPT_ID)  exit; break;
				       case "3":
				                	$SORT_TYPE_DESC="指定人员";
				                		if(!find_id($row[TO_USER_ID],$this->LOGIN_USER_ID))  exit;  break;
				       case "4":  	$SORT_TYPE_DESC="个人";  break;
			} 
			$dao=D("FileSort");
            $myrow=$dao->where("SORT_ID='$sort_id'")->find();
			###################本文件夹下的文件列表
	        $dao=D("FileContent");
	        $map=array("SORT_ID"=>$sort_id);
	        $count=$dao->count($map); 
	        if($count>0){
	            import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p = new Page($count,$listRows);
				//分页查询数据
                $file=$dao->where($map)->limit("$p->firstRow,$p->listRows")->order("CONTENT_ID desc")->findall();
                //分页跳转的时候保证查询条件
				foreach($map as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";
					}
				}
			//分页显示
			$page = $p->show();
			$this->assign("page",$page);
			$this->assign('file',$file);
	        }else{
	        	$msg=msg("","该文件夹尚无文件");
	        	$this->assign('msg',$msg);
	        }

	        $this->assign("count",$count);
	        $this->assign('row',$row);
	        $this->assign('SORT_NAME',$row[SORT_NAME]);

            $mysorttree=my_sort_tree($sort_id,$file_sort,0,$this->LOGIN_DEPT_ID,$this->LOGIN_USER_ID,1);
            $this->assign('mysorttree',$mysorttree);
		}else{
			    if($file_sort==1){
				   $msg=msg("","请选择文件夹进行查阅");
				   $this->assign('msg',$msg);
			    }else{
				   $msg=msg("","请选择文件夹进行阅读<br>或创建新的个人文件夹");
				   $this->assign('msg',$msg);
			    } 
		}
		############共享功能############
		#########弹出框开始#########
		UserSelectAction::DeptSelect();
		$dao_d=D("Department");//按部门选择人员
		$list_d=$dao_d->DeptSelect();  //左侧部门树
		$js_list = UserAction::js_list();  //js人员信息列表
		$list_p=UserAction::left_privtree();//左侧角色树
		
        $this->assign("list_d",$list_d);
        $this->assign("list_p",$list_p); 
		$this->assign("js_list",$js_list); 
		#########弹出框结束#########  
		$list1=$dao_d->DeptSelect();
		$dao_p=D("UserPriv");//按角色选择人员
		$list2=$dao_p->findall();
		$dao_u=D("User");//按个人选择人员
		$list3=$dao_u->findall();
		$list_d = $list_p = $list_u = array(); 
		$TO_USER_ID=explode(",",$myrow[TO_USER_ID]);
        foreach ($TO_USER_ID as $key=>$a){
            		$b=substr($a,0,1);
            		$c=substr($a,2); 
            	 	if ($b=="D"){ 
            			$list4 = $dao_d->where("DEPT_ID='$c'")->find(); 
            			$listall[$key][id]="D_".$list4[DEPT_ID];
            			$listall[$key][name]=$list4[DEPT_NAME];
            		}
            		if ($b=="P"){ 
            			$list4 = $dao_p->where("USER_PRIV='$c'")->find();
            			$listall[$key][id]="P_".$list4[USER_PRIV];
            			$listall[$key][name]=$list4[PRIV_NAME];
 					}
 					if ($b=="U"){ 
            			$list4 = $dao_u->where("uid='$c'")->find();
            			$listall[$key][id]="U_".$list4[uid];
            			$listall[$key][name]=$list4[USER_NAME]; 
  					}   	       
        }
        ########容量提示#########
        $dao_FS=D("FileSort");
        $dao_FC=D("FileContent");
        $dao_AT=D("Attachments");
        $row_sortid=$dao_FS->where("USER_ID='$this->LOGIN_USER_ID'")->findall();
        $sort_id_array=$attid_array=$attid_in_attachments=$att_size=$att_all_size=array();
        foreach ($row_sortid as $key=>$v){
        	$row_attid_file=$dao_FC->where("SORT_ID='".$v['SORT_ID']."'")->findall();
        	foreach ($row_attid_file as $key2=>$s){
        		 $attid_array[]=$s['ATTACHMENT_ID'];
        	}
        } 
        for($i=0;$i<count($attid_array);$i++){  
        	$attid_in_array.= $attid_array[$i]; 
         	$attid_in_attachments=explode(",",$attid_in_array);
        } 
        for($j=0;$j<count($attid_in_attachments);$j++){
        	$attid_in_attachments2 = $attid_in_attachments[$j];
        	if(!empty($attid_in_attachments2)){ 
        		$row_attid_attachments=$dao_AT->where("attid='".$attid_in_attachments2."'")->findall(); 
		        foreach ($row_attid_attachments as $key3=>$vv){
        			 $att_size[]=$vv['filesize'];
        		} 
        	} 
        }
        $att_all_size=0;
        for($k=0;$k<count($att_size);$k++){
        	$att_all_size+=$att_size[$k]; 
        }
        $att_all_size/=(1024*1024); 
        $att_all_size=substr($att_all_size,0,5);
        $this->assign("att_all_size",$att_all_size); 
		$this->assign("list1",$list1); 
		$this->assign("list2",$list2);  
		$this->assign("list3",$list3); 
		$this->assign("listall",$listall);
		$this->assign("TO_USER_ID",$TO_USER_ID);
		$this->assign("myrow",$myrow);
		$this->assign("list",$rlist);
		$this->assign('SORT_TYPE_DESC',$SORT_TYPE_DESC);  
		$this->assign("LOGIN_USER_ID",$this->LOGIN_USER_ID); 
		$this->assign("new_priv",$new_priv);
		$this->assign("manage_priv",$manage_priv);
		$this->assign("down_priv",$down_priv);
		$this->assign("touser_priv",$touser_priv);
		$this->assign("sort_id",$sort_id);
		$this->assign("file_sort",$file_sort);
		$this->display();
	}
	public function sharesubmit(){
		$SORT_ID=$_REQUEST[SORT_ID]; 
		$dao=D("FileSort");
		if(!empty($_REQUEST[FLD_STR])){
		$FLD_STR=$_REQUEST[FLD_STR];
		}else {
			$FLD_STR=""; 
		}
		$dao->setField("TO_USER_ID",$FLD_STR,"SORT_ID=$SORT_ID"); 
		$this->success("成功提交");
	}
	public function title(){
		$this->assign("FILE_SORT",$_REQUEST['file_sort']);
		$this->assign("FILE_SORT_DESC",$this->filesort);
		$this->display();
	}
	public function orderlist(){
		$dao=D("FileSort");
		$file_sort=$_REQUEST['file_sort']; 
		if($file_sort==2){
			$sort_type=4;
		}elseif ($file_sort==1){
			$sort_type=1;
		}else{
			$sort_type=3;
		} 
		$row=$dao->where("SORT_TYPE='$sort_type'")->findall(); 
		$this->assign("row",$row);
		$this->display();
	}
	public function orderlistsubmit(){   
		$dao=D("FileSort"); 
		foreach ($_POST[order_list] as $k=>$v){   
		$query="update `file_sort` set `ORDER_LIST`=$v where `SORT_ID`=$k"; 
		$dao->execute($query);   
		}  
		$this->success("成功提交");
	}
    /*--------------------左边树形------------*/
	public function tree(){

		$rlist=$this->my_xml_tree(0,$_REQUEST['file_sort']);
		$this->assign("list",$rlist);
		$this->assign("FILE_SORT",$_REQUEST['file_sort']);
		$this->display();
	}
 
	/*-------------新建文件夹表单----------*/
	public function sortnew(){
		$tree_parent=$this->_tree_parent($_REQUEST[sort_id]);
		$dao=D("FileSort");
        $row=$dao->where("SORT_ID='$tree_parent'")->find();
        			switch ($row[SORT_TYPE]){
				       case "2":
				                if($row[DEPT_ID]!=$this->LOGIN_DEPT_ID)
				                   exit;
				                break;
				       case "3":
				                if(!find_id($row[TO_USER_ID],$this->LOGIN_USER_ID))
				                   exit;
				                break;
				       case "4":
				                if($row[USER_ID]!=$this->LOGIN_USER_ID)
				                   exit;
				                break;
			          }

	    $this->assign("des","新建子文件夹");
	    $this->assign("sort_name","");
        $this->assign("submit",'sortnewSubmit');
		$this->assign("FILE_SORT",$_REQUEST['file_sort']);
		$this->assign("SORT_ID",$_REQUEST[sort_id]);
		$this->display();
	}
	/*-------------新建文件夹插入程序----------*/
	public function sortnewSubmit(){
		$dao=D("FileSort");
		if (false===$dao->create()) {
           $this->error($dao->getError());
		}


		if($_POST[FILE_SORT]==2&&$_POST[SORT_ID]==0){
			$query="insert into file_sort (SORT_PARENT,SORT_NAME,SORT_TYPE,USER_ID) values ($_POST[SORT_ID],'$_POST[SORT_NAME]','4','$this->LOGIN_USER_ID')";
		}elseif($_POST[FILE_SORT]==2&&$_POST[SORT_ID]!=0){
			$query="insert into file_sort (SORT_PARENT,SORT_NAME,SORT_TYPE,USER_ID) values ($_POST[SORT_ID],'$_POST[SORT_NAME]','4','$this->LOGIN_USER_ID')";
		}elseif ($_POST[FILE_SORT]==1){
			$query="insert into file_sort (SORT_PARENT,SORT_NAME,SORT_TYPE,USER_ID) values ($_POST[SORT_ID],'$_POST[SORT_NAME]','1','$this->LOGIN_USER_ID')";
		}else{
			$query="insert into file_sort (SORT_PARENT,SORT_NAME,SORT_TYPE,USER_ID) values ($_POST[SORT_ID],'$_POST[SORT_NAME]','3','$this->LOGIN_USER_ID')";
		}
		if($dao->execute($query)){
			 $this->assign("jumpUrl",__URL__."/index/file_sort/$_POST[FILE_SORT]/sort_id/$_POST[SORT_ID]");
			 $this->success('成功添加');
		}else{
			$this->error('添加失败');
		}
	}
	/*-------------修改新建文件夹----------*/
	public function sortedit(){
		$tree_parent=$this->_tree_parent($_REQUEST[sort_id]);
		$dao=D("FileSort");
        $row=$dao->where("SORT_ID='$tree_parent'")->find();
        			switch ($row[SORT_TYPE]){
				       case "2":
				                if($row[DEPT_ID]!=$this->LOGIN_DEPT_ID)
				                   exit;
				                break;
				       case "3":
				                if(!find_id($row[USER_ID],$this->LOGIN_USER_ID))
				                   exit;
				                break;
				       case "4":
				                if($row[USER_ID]!=$this->LOGIN_USER_ID)
				                   exit;
				                break;
			          }
        $sort=$dao->where("SORT_ID=$_REQUEST[sort_id]")->find();
	    $this->assign("des","重命名文件夹");
	    $this->assign("sort_name",$sort[SORT_NAME]);
	    $this->assign("submit",'sorteditSubmit');
		$this->assign("FILE_SORT",$_REQUEST['file_sort']);
		$this->assign("SORT_ID",$_REQUEST[sort_id]);
		$this->display('re-named');
	}
	/*-------------修改新建文件夹更新程序----------*/
	public function sorteditSubmit(){
		$dao=new FileSortModel();
		if (false===$dao->create()) {
           $this->error($dao->getError());
		}
		$dao->setField("SORT_NAME","$_POST[SORT_NAME]","SORT_ID='$_POST[SORT_ID]'");
	    $this->assign("jumpUrl",__URL__."/index/file_sort/$_POST[FILE_SORT]/sort_id/$_POST[SORT_ID]");
		$this->success('成功修改');
	}
   /*-------------删除新建文件夹----------*/
	public function sortdelete(){
        $this->_tree_delete($_REQUEST[sort_id]);
        $this->assign("jumpUrl",__URL__."/index/file_sort/$_REQUEST[file_sort]/sort_id/0");
        $this->success('成功删除');
	}
   /*-------------移动新建文件夹----------*/
	public function changesort(){
		if($_REQUEST[SORT]==0){
			$dao=D("FileContent");
		   	$TOK=strtok($_REQUEST[FILE_STR],",");
		   	while($TOK!=""){
		     $query="update file_content set SORT_ID=$_REQUEST[SORT_PARENT] where CONTENT_ID=$TOK";
		     $dao->execute($query); 
		     $TOK=strtok(",");
		     $this->assign("jumpUrl",__URL__."/index/file_sort/$_REQUEST[file_sort]/sort_id/$_REQUEST[sort_id]");
				$this->success('成功移动文件');
		   	}
		}
		if($_REQUEST[SORT]==1){
			$dao=D("FileSort");
		   if($_REQUEST[file_sort]==2&&$_REQUEST[SORT_PARENT]==0) 
		      	$SET_STR=",SORT_TYPE=4,USER_ID='$this->LOGIN_USER_ID'";
		       	$query="update `file_sort` set `SORT_PARENT`=$_REQUEST[SORT_PARENT]".$SET_STR." where `SORT_ID`=$_REQUEST[sort_id]";
		       	$dao->execute($query);
		      	$this->assign("jumpUrl",__URL__."/index/file_sort/$_REQUEST[file_sort]/sort_id/$_REQUEST[sort_id]");
				$this->success('成功移动文件夹'); 
		} 
	}
	/*--------------------文件操作-------------*/
	/*-------------新建或修改文件----------*/
	public function filenew(){
		  $file_sort=$_REQUEST[file_sort];
		  $sort_id=$_REQUEST[sort_id];

		 if ($_REQUEST[CONTENT_ID]) {
		    $dao=D("FileContent");
		    $row = $dao->where("CONTENT_ID='$_REQUEST[CONTENT_ID]'")->find();
			if ($row[ATTACHMENT_ID]){
				$daoatt=D("Attachments");
				$listatt=$daoatt->where("attid in (0".$row[ATTACHMENT_ID]."0)")->findall();
			}
			$this->assign("listatt",$listatt);
		    $this->assign("row",$row);
		    $desc="修改文件";
		 }else {
		 	$row=array();
		 	$desc="新建文件";
		    $this->assign("att","无附件");
		    $this->assign("row",$row);
		 }
		 $this->assign("desc",$desc);
		 $this->assign("file_sort",$file_sort);
		 $this->assign("sort_id",$sort_id);
		 $this->assign("upload_max_filesize",ini_get("upload_max_filesize"));
		 $this->display();
	}
   /*-------------新建文件插入程序----------*/
	public function fileSubmit(){
		//print_r($_POST); 
		$a = implode(",",$_POST[ATTACHMENT_ID]);
		$a_old =  implode(",",$_POST[oldattid]);
		if($_POST[oldattid]&&$_POST[ATTACHMENT_ID]){
		$_POST[ATTACHMENT_ID]=$a_old.",".$a.",";
		}elseif($_POST[ATTACHMENT_ID]){
		$_POST[ATTACHMENT_ID]=$a.",";
		}else{
		$_POST[ATTACHMENT_ID]=$a_old.",";
		}

		$b = implode("*",$_POST[ATTACHMENT_NAME]);
		$b_old =  implode("*",$_POST[oldattname]);
		if($_POST[oldattname]&&$_POST[ATTACHMENT_NAME]){
		$_POST[ATTACHMENT_NAME]=$b_old."*".$b."*";
		}elseif($_POST[ATTACHMENT_NAME]){
		$_POST[ATTACHMENT_NAME]=$b."*";
		}else{
		$_POST[ATTACHMENT_NAME]=$b_old."*";
		}
		
		$_POST[SEND_TIME]=date("Y-m-d H:i:s",time());

		$dao=D("FileContent");
		if (!$_POST[CONTENT_ID]){
			if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        } 
	        $id = $dao->add();
		}else {
			if(false === $dao->create()) {
        	   $this->error($dao->getError());
            }
            $dao->where("CONTENT_ID='$_POST[CONTENT_ID]'")->save();
            $id=$_POST[CONTENT_ID];
		} 
			$this->assign("jumpUrl",__URL__.'/index/file_sort/'.$_POST[FILE_SORT].'/sort_id/'.$_POST[SORT_ID]);
			$this->success("成功添加"); 
	}
	/*-----------删除附件----------*/
	public function deleteattach(){
         $CONTENT_ID=$_REQUEST[CONTENT_ID];
         $ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID]; 
         ##删除附件
         $this->_deleteattach($ATTACHMENT_ID);//删除 
         ##更新附件
         if ($CONTENT_ID) {
	         $dao=D("FileContent");
	         $row=$dao->where("CONTENT_ID='$CONTENT_ID'")->find();
	
	         $ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
			 $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
	         $ATTACHMENT_ID_NEW=$ATTACHMENT_NAME_NEW="";
	         foreach ($ATTACHMENT_ID_ARRAY as $key=>$attid){
	         	  if ($attid!=$ATTACHMENT_ID&&$attid) {
	         	   	  $ATTACHMENT_ID_NEW.=$attid.",";
	         	   	  $ATTACHMENT_NAME_NEW.=$ATTACHMENT_NAME_ARRAY[$key]."*";
	         	   }
	         }
	         $dao->setField("ATTACHMENT_ID",$ATTACHMENT_ID_NEW,"CONTENT_ID='$CONTENT_ID'");
	         $dao->setField("ATTACHMENT_NAME",$ATTACHMENT_NAME_NEW,"CONTENT_ID='$CONTENT_ID'"); 
         } 
    //     header('location:'.__URL__.'/filenew/file_sort/'.$_REQUEST[file_sort].'/sort_id/'.$_REQUEST[sort_id].'/CONTENT_ID/'.$_REQUEST[CONTENT_ID]);
         //$this->redirect("add/EMAIL_ID/$EMAIL_ID/BOX_ID/$BOX_ID","email");
	}
	/*------------删除文件-----------*/
	public function filedelete(){//删除文件
		 $CONTENT_ID=$_REQUEST[CONTENT_ID];
         $ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];

		$dao=D("FileContent");
		$file=$dao->where('CONTENT_ID=$_REQUEST[CONTENT_ID]')->find();
		$ATTACHMENT_ID_OLD=$file["ATTACHMENT_ID"];
        $ATTACHMENT_NAME_OLD=$file["ATTACHMENT_NAME"];
        $ATTACHMENT_ID_ARRAY=explode(",",$ATTACHMENT_ID_OLD);
        $ATTACHMENT_NAME_ARRAY=explode("*",$ATTACHMENT_NAME_OLD);
		foreach ($ATTACHMENT_ID_ARRAY as $attid){
			 $this->_deleteattach($attid);
		}
		$query="delete from file_content where CONTENT_ID=$_REQUEST[CONTENT_ID]";
		$dao->execute($query);
		header('location:'.__URL__.'/index/file_sort/'.$_REQUEST[file_sort].'/sort_id/'.$_REQUEST[sort_id]);
	}
		/*-------------文件查询----------*/
	public function filequery(){
		$sort_id=$_REQUEST[sort_id];
		$file_sort=$_REQUEST['file_sort'];

		$tree_parent=$this->_tree_parent($sort_id);

		$dao=D("FileSort");
        $row=$dao->where("SORT_ID='$tree_parent'")->find();
        $SORT_TYPE=$row["SORT_TYPE"];
			switch ($SORT_TYPE){
				       case "1":
				                $SORT_TYPE_DESC="全体";
				                break;
				       case "2":
				                //$SORT_TYPE_DESC=$dept_name;
				                $SORT_TYPE_DESC=getDeptname($row[DEPT_ID]);
				                break;
				       case "3":
				                $SORT_TYPE_DESC="指定人员";
				                break;
				       case "4":
				                $SORT_TYPE_DESC="个人";
				                break;
			}
		$this->assign("file_sort",$file_sort);
		$this->assign("sort_id",$sort_id);
		if ($sort_id) {
        $row=$dao->where("SORT_ID='$sort_id'")->find();
		$this->assign("SORT_NAME",$row[SORT_NAME]);
		}else {
		$this->assign("SORT_NAME","根目录");
		}
		$this->assign("SORT_TYPE_DESC",$SORT_TYPE_DESC);
		$this->display();
	}
  protected function getSubSorts($sort_id,$SORT_TYPE,$MANAGE_USERS){
	  $dao =D("FileSort");
	  $map="SORT_PARENT='$sort_id'";
	  $list=$dao->where($map)->field("SORT_ID")->findall();

	  	  foreach ($list as $row){
	  	  	if ($SORT_TYPE==4) {
					  	$subdept.=$row[SORT_ID].",";
				 	    $subdept.=$this->getSubSorts($row[SORT_ID],$SORT_TYPE,$MANAGE_USERS);
	  	  	}else {
			  	  	if(find_id($MANAGE_USERS,$this->LOGIN_USER_ID)){
					  	$subdept.=$row[SORT_ID].",";
				 	    $subdept.=$this->getSubSorts($row[SORT_ID],$SORT_TYPE,$MANAGE_USERS);
			  	   }
	  	  	}
		  }
		return $subdept;
  }
	/*-------------文件搜索结果------------*/
	public function filesearch(){
		$sort_id=intval($_REQUEST[sort_id]);
		$file_sort=$_REQUEST['file_sort'];

          ############目录属性，文件夹下的文件操作的权限
           // if ($sort_id) {
           //(InStr(USER_ID,',$this->LOGIN_USER_ID,')>0 or InStr(USER_ID,'$this->LOGIN_USER_ID,')=1)
           if ($sort_id==0) {
           	   if ($file_sort==1) {//公共文件柜
           	   	  $dao=D("FileSort");
	              $map = "(SORT_TYPE='1' or (SORT_TYPE='2' and DEPT_ID=$this->LOGIN_DEPT_ID) or (SORT_TYPE='3' and (InStr(USER_ID,',$this->LOGIN_USER_ID,')>0 or InStr(USER_ID,'$this->LOGIN_USER_ID,')=1))) and SORT_PARENT=0";
           	   	  $list=$dao->where($map)
		                    ->findall();
		                $sort_ids="";
		          foreach ($list as $rows){
						$sort_ids.=$this->getSubSorts($rows[SORT_ID],$rows[SORT_TYPE],$rows[MANAGE_USERS]);
						$sort_ids.=$rows[SORT_ID].",";
		          }
		          $condition[]="SORT_ID in (".$sort_ids."0)";

           	   	  $this->assign('SORT_TYPE_DESC',"搜索");
			      $this->assign("SORT_NAME","公共文件柜");

           	   }elseif ($file_sort==2){//个人文件柜

           	   	  $dao=D("FileSort");
           	   	  $list=$dao->where("SORT_TYPE='4' and USER_ID='$this->LOGIN_USER_ID'")->findall();//
           	   	  foreach ($list as $rows){
           	   	  	$sort_ids=$rows[SORT_ID].",";
           	   	  }
           	     // $condition[]="SORT_ID in ($sort_ids$sort_id)";

           	   	  $this->assign('SORT_TYPE_DESC',"搜索");
			      $this->assign("SORT_NAME","个人文件柜");
           	   }
           }elseif ($file_sort==3){//共享文件柜

           	   	  $dao=D("FileSort");
           	   	  $list=$dao->where("SORT_TYPE='4' and TO_USER_ID='$this->LOGIN_USER_ID'")->findall();//
           	   	  foreach ($list as $rows){
           	   	  	$sort_ids=$rows[SORT_ID].",";
           	   	  }
           	     // $condition[]="SORT_ID in ($sort_ids$sort_id)";

           	   	  $this->assign('SORT_TYPE_DESC',"搜索");
			      $this->assign("SORT_NAME","共享文件柜");
           	   }
           else {
			$tree_parent=$this->_tree_parent($sort_id);
			$dao=D("FileSort");
            $new_priv=$manage_priv=$down_priv=$touser_priv=0;
			$row=$dao->where("SORT_ID=$tree_parent")->find();
		    $SORT_TYPE=$row["SORT_TYPE"];
		    $MANAGE_USERS=$row[MANAGE_USER];

            $myrow=$dao->where("SORT_ID='$sort_id'")->find();
			//ECHO $dao->getlastsql();
			if ($SORT_TYPE==4){
                 $new_priv=$manage_priv=$down_priv=$touser_priv=1;
                 $MANAGE_USERS="";
                 //$INDEX_PAGE="index2.php";
			}else{
				  if(find_id($row[NEW_USER],$this->LOGIN_USER_ID))
			         $new_priv=1;
			      if(find_id($row[MANAGE_USER],$this->LOGIN_USER_ID))
			         $manage_priv=1;
			      if(find_id($row[DOWN_USER],$this->LOGIN_USER_ID))
			         $down_priv=1;
			      if(find_id($row[TO_USER_ID],$this->LOGIN_USER_ID))
			         $touser_priv=1;

			      if($row[DEPT_ID]){
			      	 $dao=D("Department");
			      	 $dps=$dao->where("DEPT_ID='$row[DEPT_ID]'")->find();
			      	 $dept_name=$dps[DEPT_NAME];
			      }
			}
			//echo $manager_priv;
			//echo $row[SORT_TYPE];
			switch ($SORT_TYPE){
				       case "1":
				                $SORT_TYPE_DESC="全体";
				                break;
				       case "2":
				                $SORT_TYPE_DESC=$dept_name;
				                break;
				       case "3":
				                $SORT_TYPE_DESC="指定人员";
				                break;
				       case "4":
				                $SORT_TYPE_DESC="个人";
				                break;
			}
			$this->assign('SORT_TYPE_DESC',$SORT_TYPE_DESC);
			$this->assign("SORT_NAME",$myrow[SORT_NAME]);
			$sort_ids=$this->getSubSorts($sort_id,$SORT_TYPE,$MANAGE_USERS);
			$condition[]="SORT_ID in ($sort_ids$sort_id)";
			}
		##############搜索结果
		if ($_REQUEST[SUBJECT]) {
            $condition[]=" SUBJECT like '%".$_REQUEST[SUBJECT]."%'";
		}
		if ($_REQUEST[KEY1]) {
            $condition[]=" CONTENT like '%".$_REQUEST[KEY1]."%'";
		}
		if ($_REQUEST[KEY2]) {
            $condition[]=" CONTENT like '%".$_REQUEST[KEY2]."%'";
		}
		if ($_REQUEST[KEY3]) {
            $condition[]=" CONTENT like '%".$_REQUEST[KEY3]."%'";
		}
		if ($_REQUEST[ATTACHMENT_NAME]) {
            $condition[]=" ATTACHMENT_NAME like '%".$_REQUEST[ATTACHMENT_NAME]."%'";
		}
		if ($_REQUEST[ATTACHMENT_DESC]) {
            $condition[]=" ATTACHMENT_DESC like '%".$_REQUEST[ATTACHMENT_DESC]."%'";
		}
        if (is_array($condition))
                $conditions = implode("and", $condition);
        //ECHO $conditions;
        $dao=D("FileContent");
        $count=$dao->count($conditions);

        if ($count>0) {
        	import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '20';
				}
        	 $p= new Page($count,$listRows);
        	 $list=$dao->where($conditions)
			           ->order("CONTENT_ID DESC")
			           ->limit($p->firstRow.','.$p->listRows)
			           ->findall();

			  foreach($condition as $key=>$val) {
					if(is_array($val)) {
						foreach ($val as $t){
							$p->parameter	.= $key.'[]='.urlencode($t)."&";
						}
					}else{
						$p->parameter   .=   "$key=".urlencode($val)."&";
					}
				}

			$page = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);

        }
		$this->assign("file_sort",$file_sort);
		$this->assign("sort_id",$sort_id);
		$this->display();

	}
      /*-----------------文件阅读-------------*/
    public function fileread(){
    	$sort_id=$_REQUEST[sort_id];
    	$CONTENT_ID=$_REQUEST[CONTENT_ID];
    	$close=$_REQUEST['close'];
         	############根目录属性，文件夹下的文件操作的权限
			$tree_parent=$this->_tree_parent($sort_id);
			$dao=D("FileSort");
            $new_priv=$manage_priv=$down_priv=$touser_priv=0;
			$row=$dao->where("SORT_ID=$tree_parent")->find();
			//ECHO $dao->getlastsql();
			if ($row[SORT_TYPE]==4){
                 $new_priv=$manage_priv=$down_priv=$touser_priv=1;
                 $INDEX_PAGE="index2.php";
			}else{
				  if(find_id($row[NEW_USER],$this->LOGIN_USER_ID))
			         $new_priv=1;
			      if(find_id($row[MANAGE_USER],$this->LOGIN_USER_ID))
			         $manage_priv=1;
			      if(find_id($row[DOWN_USER],$this->LOGIN_USER_ID))
			         $down_priv=1;
			      if(find_id($row[TO_USER_ID],$this->LOGIN_USER_ID))
			         $touser_priv=1;

			      if($row[DEPT_ID]){
			      	 $dao=D("Department");
			      	 $dps=$dao->where("DEPT_ID='$row[DEPT_ID]'")->find();
			      	 $dept_name=$dps[DEPT_NAME];
			      }
			}
			//echo $row[SORT_TYPE];
			switch ($row[SORT_TYPE]){
				       case "1":
				                $SORT_TYPE_DESC="全体";
				                break;
				       case "2":
				                $SORT_TYPE_DESC=$dept_name;
				                if($row[DEPT_ID]!=$this->LOGIN_DEPT_ID)
				                   exit;
				                break;
				       case "3":
				                $SORT_TYPE_DESC="指定人员";
				                if(!find_id($row[USER_ID],$this->LOGIN_USER_ID))
				                   exit;
				                break;
				       case "4":
				                $SORT_TYPE_DESC="个人"; 
				                break;
			}
			$this->assign('SORT_TYPE_DESC',$SORT_TYPE_DESC);
			$this->assign("SORT_NAME",$row[SORT_NAME]);

    	$dao=D("FileContent");
    	$rows=$dao->where("CONTENT_ID='$CONTENT_ID'")->find();
    	$this->assign("rows",$rows);
    	$this->assign("close",$close);
    	$this->assign("down_priv",$down_priv);
    	$this->assign("manage_priv",0);
    	$this->display();
    }

	public function _tree_delete($SORT_ID){
		  $dao=D("FileSort");
		  $dao->where("SORT_ID=$SORT_ID")->delete();
		  //$query="delete from FILE_SORT where SORT_ID=$SORT_ID";
		  //$dao->execute($query);
		  $dao=D("FileContent");
		  //-- 删除附件文件 --
		  $list=$dao->where("SORT_ID=$SORT_ID")->findall();
		  if ($list) {
		  	  foreach ($list as $ROW){
				    $ATTACHMENT_ID=$ROW["ATTACHMENT_ID"];
				    $ATTACHMENT_NAME=$ROW["ATTACHMENT_NAME"];
				    //15965873291
				    $ATTACHMENT_ID_ARRAY=explode(",",$ATTACHMENT_ID);
				    $ATTACHMENT_NAME_ARRAY=explode("*",$ATTACHMENT_NAME);

				    $ARRAY_COUNT=sizeof($ATTACHMENT_ID_ARRAY);
				    for($I=0;$I<$ARRAY_COUNT;$I++)
				    {
				       if($ATTACHMENT_ID_ARRAY[$I]=="")
				          break;
				       if($ATTACHMENT_NAME_ARRAY[$I]!="")
				          $this->_deleteattach($ATTACHMENT_ID_ARRAY[$I]);
				    }
		  	  }
		  }
			  $query="delete from FILE_CONTENT where SORT_ID=$SORT_ID";
			  $dao->execute($query);

			  //-- 递归删除子目录 --
			  $dao=D("FileSort");
			  $list=$dao->where("SORT_PARENT=$SORT_ID")->findall();
			  if ($list) {
			  	  foreach ($list as $ROW){
			         $this->_tree_delete($ROW["SORT_ID"]);
			  	  }
			  }
  	return ;
	}
	protected function _tree_parent($SORT_ID){
		$dao=D("FileSort");
		$row=$dao->where("SORT_ID='$SORT_ID'")->find();
		if($row[SORT_PARENT]==0)
		     return $SORT_ID;
		else
		     return $this->_tree_parent($row[SORT_PARENT]);
	}

	/*-----------------递归生成-个人-jstree数组 -------------*/
	protected function my_xml_tree($PARENT_ID=0,$FILE_SORT){
	   if (!$FILE_SORT) {
	      $FILE_SORT=$_REQUEST['file_sort'];
		}
	  $dao=D("FileSort");
	  $dao_uid=D("User");
	  $list_uid=$dao_uid->where("USER_ID='$this->LOGIN_USER_ID'")->find();
	  $list_userid="U_".$list_uid['uid'];
	  $list_deptid="D_".$this->LOGIN_DEPT_ID;
	  $list_privid="P_".$this->LOGIN_USER_PRIV;
	  if($PARENT_ID==0){
	        if($FILE_SORT==1){//公共文件柜
	        	$map = "(SORT_TYPE='1' or (SORT_TYPE='2' and DEPT_ID=$this->LOGIN_DEPT_ID)) and SORT_PARENT=$PARENT_ID";
	        }elseif($FILE_SORT==3){//共享文件柜
				$map = "(InStr(TO_USER_ID,'$list_userid')>0 or InStr(TO_USER_ID,'$list_deptid')>0 or InStr(TO_USER_ID,'$list_privid')>0) and SORT_PARENT=$PARENT_ID";
	        }else{
	        	$map = "SORT_TYPE='4' and USER_ID='$this->LOGIN_USER_ID' and SORT_PARENT=$PARENT_ID";
	        }
	  }else{
	          $map = "SORT_PARENT=$PARENT_ID";
	  }
	  $list=$dao->where($map)->order("ORDER_LIST DESC,SORT_NAME ASC")->findall();
      if($list){
      	   foreach ($list as $key=>$ROW){
      	   	  $listchild=$this->my_xml_tree($ROW['SORT_ID'],$FILE_SORT);
      	   	  if (!$listchild) {
      	   	     $rlist[]=$ROW;
      	   	  }else{
      	   	  	 $rlist[]=$ROW;
      	   	  	 foreach ($listchild as $keys=>$rows){
      	   	  	    $rlist[]=$rows;
      	   	  	 }
      	   	  }
     	   }
      }
	   return $rlist;
	}
}
?>