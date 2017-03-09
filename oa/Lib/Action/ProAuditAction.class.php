<?php
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");
include_cache(APP_PATH."/Common/xm.php");
class ProAuditAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目审批";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		//this->LOGIN_USER_NAME=Session::get("LOGIN_USER_NAME");
		
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){
		$dao=D("XmssWh");
		$map=" SHR='$this->LOGIN_USER_ID'";
		$map2=" A.SHR='$this->LOGIN_USER_ID'";
		$count=$dao->count($map);
		
	        if($count>0){
	            import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '20';
				}
				if (empty($_REQUEST[p]))
				    $_REQUEST[p]=1;
				$firstRows=($_REQUEST[p]-1)*$listRows;
				//echo $firstRows;
				$p          = new Page($count,$listRows);
				//分页查询数据
                $list=$dao->table("xmss_wh as A")
                          ->join("left join xmss_lb as B ON A.XM_ID = B.XM_ID left join xmss_lx as C ON A.XM_ID = C.XM_ID left join user as U ON C.XMFZR_ID=U.USER_ID left join metadata D ON A.WH_ID = D.ID")
                          ->field("A.ID,A.WH_ID,A.FJ_ID,A.FJ_NAME,A.SH,B.MC,C.XMFZR_ID,C.LXSJ,U.USER_NAME,D.value2 as WH_NAME")
                          ->order("A.ID desc")
                          ->where($map2)
                          ->limit("$p->firstRow,$p->listRows")
                          ->findall();
                  //select a.user_id,b.user_name from xmss_lc a left join user b on a.user_id = b.user_id where wh_id = $WH_ID
                          
       
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        

		$this->display();
	}
	
}
?>	