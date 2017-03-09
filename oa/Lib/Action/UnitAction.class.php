<?php
/**********************************
 *       单位信息管理模块
 * author:廖秋虎  time:08/11/18
 **********************************/
class UnitAction extends PublicAction {
/**************入口******************/
	public function index(){
		$unit = new UnitModel();
		$row = $unit->find();
		$this->assign('row',$row);
		$this->display();
	}
/*************更新******************/
	public function update(){
		$unit = new UnitModel();
		$unit->create();
		$unit->save();
		$this->assign('jumpUrl',__URL__."/index"); 
        $this->success('单位信息更新成功!');
	}
}
?>
