<?
/**
 * 
 * 
 * */
class BgsbTypeAction extends PublicAction {
	
    public 	function _initialize(){
		$this->curtitle="设备类别管理";
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){
		$dao=D("BgsbType");
		$list=$dao->order("listorder")->findall();
		$this->assign("list",$list);
		
		$this->display();
	}
	
	public function form(){
		$typeid=$_REQUEST[typeid];
		
		if ($typeid) {
			$desc="编辑设备类别";
			$dao=D("BgsbType");
			$row=$dao->where("typeid='$typeid'")->find();
			
			
		}else {
			$desc="添加设备类别";
			$row[listorder]=0;
		}
		$this->assign("row",$row);
		$this->assign("desc",$desc);
		
		$this->display();
		
	}
		
	public function submit(){
		$typeid=$_REQUEST[typeid];
		$dao=D("BgsbType");
		if (false===$dao->create()) {
			$this->error("操作失败");
		}
		if ($typeid) {//修改
			$dao->where("typeid='$typeid'")->save();
		    $this->success("成功修改");	
		}else {//添加
			$dao->add($_POST);
			$this->success("成功添加");	
		}
		
	}

	public function delete(){
		$typeid=$_REQUEST[typeid];
		$daouser=D("Bgsb");
		$user=$daouser->where("typeid='$typeid'")->find();
		if($user){
			$this->error("该设备类别下存在设备，不能删除！");
		}
		$dao=D("BgsbType");
		$dao->where("typeid=$typeid")->delete();
		$this->redirect("index");
	}

	
	
	
}


?>