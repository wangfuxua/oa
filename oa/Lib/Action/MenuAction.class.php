<?

class MenuAction extends PublicAction {
	var $curtitle;
	function _initialize(){
		$this->curtitle="菜单设置";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
    
	/*-------------入口-显示菜单列表------*/
	public function index(){
		$dao=D("Menu");
		$map=array();
		$count=$dao->count($map);
		//echo $count;
	        if($count>0){
	            import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new Page($count,$listRows);
				//分页查询数据
                $list=$dao->where($map)
                          ->order("listorder desc")
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        $this->assign("COUNT",$count);
	        
		    $this->display();
		
	}
	
	public function form(){
		$menuid=intval($_REQUEST['menuid']);
		
		$parentid=intval($_REQUEST['parentid']);
		
		$dao=D("Menu");
		$list=$dao->where("parentid='$parentid'")->findall();
		
		$this->assign("list",$list);
		
        if ($menuid) {//修改
           	$row=$dao->where("menuid='$menuid'")->find();
           	$parentid=$row[parentid];
           			
        }else {
        	$row=array();
        	
        }
        
        $this->assign("parentid",$parentid);
        
        $this->assign("row",$row);
		$this->display();
	}

	public function submit(){
		$menuid=intval($_REQUEST['menuid']);
		$parentid=intval($_REQUEST['parentid']);
		$_POST[listorder]=intval($_POST[listorder]);
		$dao=D("Menu");	
        if ($menuid) {//修改
        	
	        if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
	        if($dao->where("menuid='$menuid'")->save()){
			   $this->assign("jumpUrl",__URL__."/index");
			   $this->success("成功修改");
			}else{ 
			   $this->assign("jumpUrl",__URL__."/index");	
			   $this->error("修改失败");   
			}
        }else {//添加
	
	        if(false === $dao->create()) {
	        	$this->error($dao->getError());
	        }
            $id = $dao->add();
            
	        if($id){
			   $this->assign("jumpUrl",__URL__."/index");
			   $this->success("成功添加");
			}else{ 
			   $this->assign("jumpUrl",__URL__."/index");	
			   $this->error("添加失败");   
			}
		                	
        }  				
		
        
		
	}
	
	public function delete(){//删除单个记录
		$menuid=intval($_REQUEST['menuid']);
		$map=array(
		  "parentid"=>$menuid
		);
		$dao=D("Menu");
		$count=$dao->count($map);
		if ($count) {
			$this->assign("jumpUrl",__URL__."/index");	
			$this->error("请先删除子菜单");
		}
		
		$dao->where("menuid='$menuid'")->delete();
		
		$this->assign("jumpUrl",__URL__."/index");	
		$this->success("成功删除");
		
	}
		
		
	

}

?>