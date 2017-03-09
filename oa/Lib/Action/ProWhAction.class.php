<?php
import("@.Action.PublicAction");
import("@.Util.Program");
include_cache(APP_PATH."/Conf/config_xm.php");
class ProWhAction extends PublicAction {
    var $curtitle;
	function _initialize(){
		$this->curtitle="项目维护";
		$this->LOGIN_DEPT_ID=Session::get('LOGIN_DEPT_ID');
		$this->assign("curtitle",$this->curtitle);
		parent::_initialize();
	}
	
    public function index(){
        $dao=D("XmssLx");
    	$map="XMFZR_ID = '$this->LOGIN_USER_ID' AND SP = '0'";
    	$count=$dao->count($map);
    		if($count>0){
	            import("ORG.Util.Page");	
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '';
				}
				$p          = new Page($count,$listRows);
				//分页查询数据
				$list=$dao->table("xmss_lx as A")
				          ->join("xmsb_lb as B ON A.XM_ID = B.XM_ID")
                          ->where("A.XMFZR_ID = '".$this->LOGIN_USER_ID."' AND SP = '0'")
                          ->limit("$p->firstRow,$p->listRows")
				          ->field('A.XM_ID,B.MC,A.LXSJ')
				          ->findAll();
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
				$daouser=D("user");
				$user=$daouser->where("USER_ID='$this->LOGIN_USER_ID'")->find();
				$LOGIN_USER_NAME=$user[USER_NAME];
			//分页显示
			$page       = $p->show();
	      }
	        $this->assign("ZLPH",ZLPH);
	        $this->assign("XMCG",XMCG);
    	    $this->assign("LOGIN_USER_NAME",$LOGIN_USER_NAME);
			$this->assign("page",$page);
			$this->assign('list',$list);
			$this->display();    
    }
    /*-------项目维护管理----*/
    public function maintenance(){//项目维护管理
        $XM_ID=$_REQUEST[XM_ID];
        $MC=$_REQUEST[MC];
        $FL=$_REQUEST[FL];
        $WH_ID=$_REQUEST[WH_ID];
        $this->assign("XM_ID",$XM_ID);    	
        $this->assign("WH_ID",$WH_ID); 
        $this->assign("MC",$MC);
        $this->assign("FL",$FL);
        $this->assign("XMZT_WH",XMZT_WH);
        
        $daometa=D("metadata");
        $list1=$daometa->where("name='XMWH' AND value3 = '$FL'")->order("value1")->findall();//维护类型
        $this->assign("list1",$list1);
        $dao = new Model();
        $listuser=$dao->table("xmss_lc as a")
                      ->join("user as b on a.USER_ID = b.USER_ID")
                      ->field("a.USER_ID,b.USER_NAME")
                      ->where("a.WH_ID = '$WH_ID'")
                      ->findall();
        $this->assign("listuser",$listuser);
        
        /*项目维护文件;*/
        //附件列表
        $daotpl=D("templates");
        $tpl=$daotpl->table("templates as t")
                    ->join("attachments as a on t.attid=a.attid") 
                    ->where("t.LB='XMWH'")
                    ->findall();
           $tpls=array();         
        if ($tpl) {
        	foreach ($tpl as $row){
        		$tpls[$row[attid]]=$row[filename];
        	}
        }
        //维护列表
        $daowh=D("XmssWh");
        $list=array();
        if ($list1) {
        	foreach ($list1 as $key=>$row){
        		$list[$key][WH_ID]=$row[id];
        		$list[$key][WH_NAME]=$row[value1];
        		foreach ($tpls as $attid=>$fjname){
        			if (stristr($fjname,$row[value1])!==false) {
        			   $list[$key][WH_NAME] .= "<a herf=/index.php/attach/view/ATTACHMENT_ID/$attid>".$fjname."</a>";
        			   break;
        			}
        		}
        		$list2=array();
        		$list2=$daowh->where("XM_ID = $XM_ID AND WH_ID = $row[id]")->findall();
        		if($list2){
        			foreach ($list2 as $key2=>$value){
        				if($value=="_Y_"){
        					$list2[$key2][SH]="审核通过";
        				}elseif ($value=="_N_"){
        					$list2[$key2][SH]="未通过审核";
        				}else{
        					$list2[$key2][SH]="正在审核";
        				}
        				
        			}
        			$list[$key][rowspan]=count($list2);
        		}else{
        			$list[$key][rowspan]=0;
        		}
        		$list[$key][sub]=$list2;
        		
        	}
        }
        //print_r($list);
        $this->assign("list",$list);
        
    	$this->display();
    }
 /*---------------上传或删除维护文件---------------*/   
    public function attachsubmit(){//上传或删除维护文件
    	$WH_ID=$_POST[WH_ID];
    	$OPERATION=$_POST[OPERATION];
    	if ($OPERATION=="add") {//上传维护文件
		    	$dao=D("XmssLc");
		    	$map="WH_ID = '$WH_ID'";
		    	$count=$dao->count($map);
		    	if (empty($count)) {
		    		$this->error("目前设定改维护类别的审核流程,不能进行该类别的项目维护。");
		    	}

		    	$dao=D("metadata");
		    	$map="name='XMWH' AND id = '$WH_ID'";
		    	$row=$dao->where($map)->find();
				$TIME=date("Ymds",$this->CUR_TIME_INT);

				if ($_FILES){
					$path	=	$this->uploadpath;
					$info	=	$this->_upload($path);
					$data =$info[0];
					if($data){
						$data[addtime]=$this->CUR_TIME_INT;
						$data[filename]=$data[name];
						$data[attachment]=$data[savename];
						$data[filesize]=$data[size];
						$data[filetype]=$data[type];
						$dao = D('attachments');
						$dao->create();
						$attid=$dao->add($data);
						
			        $_POST[FJ_ID]=$attid;
					$_POST[FJ_NAME]=$row["value2"].$TIME.$_FILES[ATTACHMENT][name];
					}
				}
				$dao=D("XmssWh");
				$dao->create();
				$dao->add();
    		
    	}
		//XM_ID=$XM_ID&MC=$MC&FL=$FL&WH_ID=$WH_ID
		$this->redirect("maintenance/XM_ID/$_POST[XM_ID]/MC/$_POST[MC]/FL/".urlencode($_POST[FL])."/WH_ID/$_POST[WH_ID]","prowh");
    	//echo $dao->getlastsql();exit;
    	
    	
    	
    }	
    
}

?>    