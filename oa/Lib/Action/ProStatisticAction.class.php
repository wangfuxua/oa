
<?php
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");
class ProStatisticAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目统计";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		//this->LOGIN_USER_NAME=Session::get("LOGIN_USER_NAME");
		
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
	public function index(){
		
		
		$this->display();
	}
	public function search(){
		$XMMC=$_REQUEST[XMMC];
		$BM_ID=$_REQUEST[BM_ID];
		$SXMZT=$_REQUEST[SXMZT];
		$SJ1=$_REQUEST[SJ1];
		$SJ2=$_REQUEST[SJ2];
		$WCSJ1=$_REQUEST[WCSJ1];
		$WCSJ2=$_REQUEST[WCSJ2];
		$LXSJ1=$_REQUEST[LXSJ1];
		$LXSJ2=$_REQUEST[LXSJ2];
		
		 $WHERE_STR = " D.MC LIKE '%$XMMC%'AND";
		 if(!empty($BM_ID)){
		 	$WHERE_STR .= " A.LXBM_ID = $BM_ID AND";
		 }
		 if(!empty($SXMZT)){
		 	$arXMZT = explode("*",$SXMZT);
		 	$WHERE_STR .= " D.XMZT IN $arXMZT[0] AND";
		 }
		 if($SJ1!="" && $SJ2!="")$WHERE_STR .= " D.SJ between '$SJ1' AND '$SJ2' AND";
		 else if($SJ1 != "")$WHERE_STR .= " D.SJ >= '$SJ1' AND";
		 else if($SJ2 != "")$WHERE_STR .= " D.SJ <= '$SJ2' AND";
		 if($WCSJ1!="" && $WCSJ2!="")$WHERE_STR .= " D.WCSJ between '$WCSJ1' AND '$WCSJ2' AND";
		 else if($WCSJ1 != "")$WHERE_STR .= " D.WCSJ >= '$WCSJ1' AND";
		 else if($WCSJ2 != "")$WHERE_STR .= " D.WCSJ <= '$WCSJ2' AND";
		 if($LXSJ1!="" && $LXSJ2!="")$WHERE_STR .= " LXSJ between '$LXSJ1' AND '$LXSJ2' AND";
		 else if($LXSJ1 != "")$WHERE_STR .= " LXSJ >= '$LXSJ1' AND";
		 else if($LXSJ2 != "")$WHERE_STR .= " LXSJ <= '$LXSJ2' AND";
		 
		 $map=substr($WHERE_STR,0,strlen($WHERE_STR)-3);
		 
         $dao=new Model();
         $list=$dao->table("xmsblb D")
                   ->join("xmss_lx A ON A.XM_ID = D.XM_ID")
                   ->field("LXSJ,D.XM_ID,D.MC,A.LXBM_ID,A.XMFZR_ID,A.XMZJ,D.SJ,D.XMZT,D.WCSJ,D.SJSJ")
                   ->where($map)
                   ->findAll();
                   
         
		 $this->assign("list",$list);
		 
		
	}
}
?>	