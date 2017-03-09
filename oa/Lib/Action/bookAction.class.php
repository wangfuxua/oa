<?php
/**
 * bookAction.class.php
 * 功能：图书管理模块
 *********/
include_cache(APP_PATH."/Common/book.php");
class bookAction extends PublicAction {
/*--------图书类别入口程序---------*/	
	public function typeIndex(){
		$bookType = new bookTypeModel();
		$bookList = $bookType->order('TYPE_ID')->findall();
		$this->assign('bookList',$bookList);
		
		$this->display();
	}
/*------------图书类别添加程序----------*/
	public function typeAdd(){
		$bookType = new bookTypeModel();
		
		if (false==$bookType->create()) {
			$this->error($bookType->getError());
		}
			$bookType->add();
			$this->assign('jumpUrl',__URL__."/typeIndex"); 
        	$this->success('添加成功!');
	}
/*-----------图书类别删除程序---------------*/
	public function typeDel(){
		$bookType = new bookTypeModel();
		//$bookRow = $bookType->where("TYPE_ID=$_GET[TYPE_ID]")->find();
			$bookInfo = new bookInfoModel();
			$info = $bookInfo->where("TYPE_ID=$_GET[TYPE_ID]")->find();
			if($info){
			//$this->success("错误，类别$bookRow[TYPE_NAME]下面尚有图书，不能删除");
		
				$this->assign('jumpUrl',__URL__."/typeIndex"); 
        		$this->error('无法删除，类别下面尚有图书!');
			}else{
				$bookType->delete("TYPE_ID=$_GET[TYPE_ID]");
				$this->assign('jumpUrl',__URL__."/typeIndex"); 
        		$this->success('删除操作成功!');
			}
			
	}
/*--------------图书类别更新程序-----------*/
	public function typeUpdate(){
		$bookType = new bookTypeModel();
		
		if (false==$bookType->create()) {
			$this->error($bookType->getError());
		}
		
		if($_POST['TYPE_NAME']){
			$book = $bookType->where("TYPE_NAME='$_POST[TYPE_NAME]' and TYPE_ID<>'$_POST[TYPE_ID]'")->find();
			if($book){
				$this->assign('jumpUrl',__URL__."/typeIndex"); 
        		$this->success('类别名称已存在!');
			}else{
				//$bookType->find($_POST['TYPE_ID']);
				$bookType->TYPE_NAME = $_POST['TYPE_NAME'];
				$bookType->save();
				$this->assign('jumpUrl',__URL__."/typeIndex"); 
        		$this->success('更新成功!');
			}		
		}
	}
/*-----------------图书类别编辑程序-----------------*/
	public function typeEdit(){
		$bookType = new bookTypeModel();
		$bookRow = $bookType->where("TYPE_ID=$_GET[TYPE_ID]")->find();
		$this->assign('bookRow',$bookRow);
		$this->display();
	}
/*------------图书信息录入入口程序----------------*/
	public function manageIndex(){
        $map=array();	
        $dao=D("bookInfo");
		$count=$dao->count($map);
		     import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
			$p= new Page($count,$listRows);
				//分页查询数据
			$list=$dao->where($map)
			          ->order("BOOK_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
		$this->display();
	}
	
	/*--------------图书管理列表------------*/
    public function manageList(){
		$dao=D("bookInfo");
        $map=$this->_search();	
		$count=$dao->count($map);
		if ($count>0) {
			import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
					            	
			$p= new Page($count,$listRows);
				//分页查询数据
			$list=$dao->where($map)
			          ->order($_REQUEST[ORDER_FIELD])
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
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
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
		}
		$this->display();
    }
    /*---------------------删除图书信息---------------*/
    public function bookDelete(){
    	$BOOK_ID=$_REQUEST[BOOK_ID];
    	$dao=D("bookInfo");
    	$dao->where("BOOK_ID='$BOOK_ID'")->delete();
    	$this->assign("jumpUrl",__URL__."/manageIndex");
    	$this->success("成功删除");
    }
    
/*-------------图书信息录入和修改创建程序-------------*/
	public function manageNew(){
		$BOOK_ID=$_REQUEST[BOOK_ID];
		if ($BOOK_ID) {//修改
			$dao=D("bookInfo");
			$row=$dao->where("BOOK_ID='$BOOK_ID'")->find();
		    $choose_dept_id=$row[DEPT_ID];	
		    $desc="修改图书";
			if ($row[ATTACHMENT_ID]){
				$daoatt=D("Attachments");
				$listatt=$daoatt->where("attid in (0".$row[ATTACHMENT_ID]."0)")->findall();
			}
			$this->assign("listatt",$listatt);
					    
		    
		}else{//添加
			$row=array();
			$choose_dept_id=$this->LOGIN_DEPT_ID;
			$desc="添加图书";
		}
		
		$select = $this->my_dept_tree(0,$choose_dept_id,0);
		$this->assign('deptlist',$select);
		
		$bookType = new bookTypeModel();
		$bookList = $bookType->order('TYPE_ID')->findAll();
		$this->assign('typelist',$bookList);
		$this->assign("upload_max_filesize",ini_get("upload_max_filesize"));
		$this->assign("desc",$desc);
		$this->assign("row",$row);
		$this->display();
	}
	
	/*---------------图书信息添加或修改提交处理程序-----------*/
	public function manageSubmit(){
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
		
		$BOOK_ID=$_REQUEST[BOOK_ID];
		$dao=D("bookInfo");
        $count = $dao->count("TYPE_ID='$_REQUEST[TYPE_ID]' and DEPT_ID='$_REQUEST[DEPT_ID]' and BOOK_NAME='$_REQUEST[BOOK_NAME]' AND BOOK_ID!='$BOOK_ID'");
        
		if ($count>0) {
			$this->error("图书已经存在");
		}
		if (false===$dao->create()) {
			$this->error($dao->getError());
		}
		if ($BOOK_ID) {
			$dao->where("BOOK_ID='$BOOK_ID'")->save();
			//echo $dao->getlastsql();exit;
			$this->assign("jumpUrl",__URL__."/manageIndex");
			$this->success("成功修改");
		}else {
			$dao->add();
			$this->assign("jumpUrl",__URL__."/manageIndex");
			$this->success("成功添加");
		}
		
	}

	/*----------------------图书详细信息-------------*/

	public function bookList(){  // added 20090313 显示图书列表
        $map=array();	
        $dao=D("bookInfo");
		$count=$dao->count($map);
		     import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
			$p= new Page($count,$listRows);
				//分页查询数据
			$list=$dao->where($map)->order($_REQUEST[ORDER_FIELD])
			          ->order("DEPT_ID desc")
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
		$this->display();
	}

	public function bookDetail(){
 	    $BOOK_ID=$_REQUEST[BOOK_ID];
		$dao=D("bookInfo");
        $row=$dao->where("BOOK_ID='$BOOK_ID'")->find();  
			if ($row[ATTACHMENT_ID]){
				$daoatt=D("Attachments");
				$listatt=$daoatt->where("attid in (0".$row[ATTACHMENT_ID]."0)")->findall();
			}
			$this->assign("listatt",$listatt);
			         
        $this->assign("row",$row);
		
		$this->display();
		
	}
	public function queryIndex(){
		$select = $this->my_dept_tree(0,$this->LOGIN_DEPT_ID,0);
		$this->assign('select',$select);
		$bookType = new bookTypeModel();
		$bookList = $bookType->order('TYPE_ID')->findAll();
		$this->assign('bookList',$bookList);
		$this->display();
	}
		
/*---------图书信息查询程序----------*/
	public function manageQuery(){
		$select = $this->my_dept_tree(0,$this->LOGIN_DEPT_ID,0);
		$this->assign('select',$select);
		$bookType = new bookTypeModel();
		$bookList = $bookType->order('TYPE_ID')->findAll();
		$this->assign('bookList',$bookList);
		$this->display();
	}
    /*-----------------图书查询结果-----------*/
	public function queryList(){
		$dao=D("bookInfo");
        $map=$this->_search();	
		$count=$dao->count($map);
		if ($count == 0) {
			$this->error('没有你要找的书！请重新输入搜索条件！');		
		}
		elseif ($count>0) {
			import("ORG.Util.Page");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
					            	
			$p= new Page($count,$listRows);
				//分页查询数据
			$list=$dao->where($map)
			          ->order($_REQUEST[ORDER_FIELD])
			          ->limit($p->firstRow.','.$p->listRows)
			          ->findall();
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
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
		}
		$this->display();
	}
	/*------------自动生成查询条件-----------------*/
	protected function _search() 
    {
		$model	=	D("bookInfo");
        foreach($model->getDbFields() as $key=>$val) {
            if(isset($_REQUEST[$val]) && $_REQUEST[$val]!='') {
            	if($val=="DEPT_ID"&&$_REQUEST[$val]=='all')
                     continue;
            	if($val=="TYPE_ID"&&$_REQUEST[$val]=='all')
                     continue;
            	if($val=="LEND"&&$_REQUEST[$val]=='-1')
                     continue;
                     
                if($val=="DEPT_ID"||$val=="TYPE_ID"||$val=="LEND")
                    $map[$val]	=	$_REQUEST[$val];
                else
                   $map[$val]	=	array('like','%'.trim($_REQUEST[$val]).'%');
            }
        }
        return $map;
    }
    

    	/*-----------删除附件----------*/
	public function deleteattach(){
         $CONTENT_ID=$_REQUEST[CONTENT_ID];
         $ATTACHMENT_ID=$_REQUEST[ATTACHMENT_ID];
         $BOOK_ID=$_REQUEST[BOOK_ID];
         ##删除附件
         $this->_deleteattach($ATTACHMENT_ID);//删除
         ##更新附件
         if ($BOOK_ID) {
	         $dao=D("bookInfo");
	         $row=$dao->where("BOOK_ID='$BOOK_ID'")->find();
	         $ATTACHMENT_ID_ARRAY=explode(",",$row[ATTACHMENT_ID]);
			 $ATTACHMENT_NAME_ARRAY=explode("*",$row[ATTACHMENT_NAME]);
	         $ATTACHMENT_ID_NEW=$ATTACHMENT_NAME_NEW="";
	         foreach ($ATTACHMENT_ID_ARRAY as $key=>$attid){
	         	  if ($attid!=$ATTACHMENT_ID&&$attid) {
	         	   	  $ATTACHMENT_ID_NEW.=$attid.",";
	         	   	  $ATTACHMENT_NAME_NEW.=$ATTACHMENT_NAME_ARRAY[$key]."*";
	         	   }
	         }
	         $dao->setField("ATTACHMENT_ID",$ATTACHMENT_ID_NEW,"BOOK_ID='$BOOK_ID'");
	         $dao->setField("ATTACHMENT_NAME",$ATTACHMENT_NAME_NEW,"BOOK_ID='$BOOK_ID'");
         }

//         header('location:'.__URL__.'/filenew/file_sort/'.$_REQUEST[file_sort].'/sort_id/'.$_REQUEST[sort_id].'/CONTENT_ID/'.$_REQUEST[CONTENT_ID]);
	}
    	
		
}
?>