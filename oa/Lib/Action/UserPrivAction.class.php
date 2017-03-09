<?php
class UserPrivAction extends PublicAction {
	
	public 	function _initialize(){
		$this->curtitle="角色与权限设置";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){
		$dao=D("UserPriv");
		$list=$dao->order("PRIV_NO")->findall();
		$this->assign("list",$list);
		
		$this->display();
	}
	
	public function form(){
		$USER_PRIV=$_REQUEST[USER_PRIV];
		
		if ($USER_PRIV) {
			$desc="编辑角色";
			$dao=D("UserPriv");
			$row=$dao->where("USER_PRIV='$USER_PRIV'")->find();
			$this->assign("row",$row);
			
		}else {
			$desc="添加角色";
		}
		$this->assign("desc",$desc);
		
		$this->display();
		
	}
		
	public function submit(){
		$USER_PRIV=$_REQUEST[USER_PRIV];
		$dao=D("UserPriv");
		if (false===$dao->create()) {
			$this->error("操作失败");
		}
		if ($USER_PRIV) {//修改
			$dao->where("USER_PRIV='$USER_PRIV'")->save();
		    $this->success("成功修改");	
		}else {//添加
			$dao->add($_POST);
			$this->success("成功添加");	
		}
		
	}

	public function delete(){
		$USER_PRIV=$_REQUEST[USER_PRIV];
		$daouser=D("User");
		$user=$daouser->where("USER_PRIV='$USER_PRIV'")->find();
		if($user){
			$this->error("该角色下存在用户，不能删除！");
		}
		$dao=D("UserPriv");
		$dao->where("USER_PRIV=$USER_PRIV")->delete();
		$this->redirect("index");
	}

	public function editpriv(){
		$USER_PRIV=$_REQUEST[USER_PRIV];
		//--------------menu_id_array menu数组----------
		$daomenu=D("SysMenu");
		$list=$daomenu->where("flag=1")->findall();
		$i=0;
		foreach ($list as $row){
			$menu_id_array.='MENU_ID_ARRAY['.$i.']="'.$row[MENU_ID].'";';
			$i++;
		}
		$menu_id_count=$i;
		
		$this->assign("menu_id_count",$menu_id_count);
		$this->assign("MENU_ID_ARRAY",$menu_id_array);
		//--------------user_priv 信息---------
		$dao=D("UserPriv");
		$ROW=$dao->where("USER_PRIV='$USER_PRIV'")->find();
		$PRIV_NO=$ROW["PRIV_NO"];
        $PRIV_NAME=$ROW["PRIV_NAME"];
        $USER_FUNC_ID_STR=$ROW["FUNC_ID_STR"];
        
        $this->assign("ROW",$ROW);
        //$this->assign();
		//---------------循环输出-----------
        $daomenu=D("SysMenu");
        $daofunc=D("SysFunction");
		$list=$daomenu->where("flag=1")->order("MENU_ID")->findall();
		$i=0;
		foreach ($list as $row){
			$listfunc=$daofunc->where("MENU_ID like '$row[MENU_ID]%' and length(MENU_ID)=4 and flag=1")->order("MENU_ID")->findall();
			     $k=0; 
			foreach ($listfunc as $rows){
				if(substr($rows[FUNC_CODE],0,1)=="@"){//是不是子菜单
					$listfunc2=$daofunc->where("MENU_ID like '$rows[MENU_ID]%' and length(MENU_ID)=6 and flag=1")->order("MENU_ID")->findall();
					$listfunc[$k][sub2]=$listfunc2;
				}
				$k++;
			}
			$list[$i][sub]=$listfunc;
			$i++;
		}
        //print_r($list);
       // EXIT;
		$this->assign("list",$list);
        
        $this->assign("USER_PRIV",$USER_PRIV);
		$this->display();
	}
	
	public function updatepriv(){
		$dao=D("UserPriv");
		$dao->setField("FUNC_ID_STR",$_REQUEST[FUNC_ID_STR],"USER_PRIV='$_REQUEST[USER_PRIV]'");
		
		$this->assign("jumpUrl",__URL__.'/index');
		$this->success("成功提交");
			
	}
	
	
	
	
	/*---------------desktop ---------*/
	
	public function userprivmenu(){
		$dao=D("SysMenu");
		$list=$dao->order("MENU_ID")->findall();
		$i=0; 
		foreach ($list as $row){
			$rows[MENU_ID]=$row[MENU_ID];
			$rows[MENU_NAME]=$row[MENU_NAME];
			$list[$i]=$rows;
			$i++;
		}
		print json_encode($list);
		
	}
}
?>