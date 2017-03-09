<?
import("@.Util.pop3");
import("@.Util.mailparser");
include_cache(APP_PATH."/Common/mail.php");

class WebMailSetAction extends PublicAction {

    var $mailfile;
    var $mailtemp;
    
	public function _initialize(){
		$this->curtitle="邮件设置";
		$this->assign("curtitle",$this->curtitle);
		
		$this->mailfile=APP_PATH."/mailfile/";//邮件缓存目录
		$this->mailtemp=APP_PATH."/mailtemp/";//发邮件时保存的默认目录
		
	    //$this->mailfile="/oa/mailfile/";
	    
		parent::_initialize();
	}
	
	public function index(){
		$dao=D("WebMailSet");
		$list=$dao->where("uid=$this->_uid")->order("listorder")->findall();
		//echo $dao->getlastsql();
		$this->assign("list",$list);
		$this->display();
	}
	
	public function form(){
		$setid=$_REQUEST[setid];
		
		if ($setid) {
			$desc="编辑邮箱";
			$dao=D("WebMailSet");
			$row=$dao->where("setid='$setid'")->find();
			$row[poppass]=deconfuse($row[poppass],$row['popserver'].$row['popport'].$row['popuser']);
			//echo $row[poppass];
		}else {
			$desc="添加邮箱";
			$row[listorder]=0;
			$row[popport]=110;
			$row[smtpport]=25;
			$row[acctype]="pop3";
		}
		$this->assign("row",$row);
		$this->assign("desc",$desc);
		
		$this->display();
		
	}
		
	public function submit(){
//		print_rr($_REQUEST);
		//EXIT;
		$setid=$_REQUEST[setid];
		$_POST[uid]=$this->_uid;
		$dao=D("WebMailSet");
		if (false===$dao->create()) {
			$this->error("请填写完整资料");
		}
		//这儿就用这一个密码;
		$poppass=confuse($_POST[poppass],$_POST['popserver'].$_POST['popport'].$_POST['popuser']);
		if ($setid) {//修改
			$_POST[smtpserver]=$_POST[popserver];
			$_POST[smtpuser]=$_POST[popuser];
			$_POST[smtpport]=25;
			$_POST[smtppass]=confuse($_POST[poppass],$_POST['smtpserver'].$_POST['smtpport'].$_POST['smtpuser']);
			$_POST[accname]=$_POST[email];
			$_POST[poppass]=$poppass;
			$dao->where("setid='$setid'")->save($_POST);
			//ECHO $dao->getlastsql();exit;
            $this->assign("jumpUrl",__APP__."/WebMail/index/to/index/setid/".$setid);
		    $this->success("成功修改");
		    
		    	
		}else {//添加
			$_POST[smtpserver]=$_POST[popserver];
			$_POST[smtpuser]=$_POST[popuser];
			$_POST[smtpport]=25;
			$_POST[smtppass]=confuse($_POST[poppass],$_POST['smtpserver'].$_POST['smtpport'].$_POST['smtpuser']);
			$_POST[accname]=$_POST[email];
			$_POST[poppass]=$poppass;			
			$id=$dao->add($_POST);
			###生成用户目录
			$folderarr=array("inbox"=>"收件箱","drafts"=>"草稿箱","sent"=>"已发送","waste"=>"已删除");
			$mydir=$this->mailfile.$id;//根据邮箱id生成目录
			$daofolder=D("WebMailEmailFolders");
			foreach ($folderarr as $k=>$v){
			   	$data[setid]=$id;
			   	$data[uid]=$this->_uid;
			   	$data[folder_path]=$k;
			   	$data[friendly_name]=$v;
			   	$data[att_icon]=":".$k;
			   	$data[childof]="1";
			   	$data[att_has_folders]="1";
			   	$data[att_has_items]="1";
			   	$folderid=$daofolder->add($data);
			   	$mks=false;
			   	if(!file_exists($mydir)){
			   	   $mks=@mkdir($mydir);
			   	}else{
			   		$mks=true;
			   	}
			   	@chmod($mydir,0777);
			   	
			   	if (!file_exists($mydir."/".$k)) {
			   	   $mks=@mkdir($mydir."/".$k);
			   	}else{
			   		$mks=true;
			   	} 
			   	@chmod($mydir."/".$k,0777);
			   	
				if (!$mks) {
					$this->error("生成用户目录错误");
				}
				
			}
			
			$this->assign("jumpUrl",__APP__."/WebMail/index/to/index/setid/".$id);
			$this->success("成功添加");
			
				
		}
		
	}

	public function delete(){
		$setid=$_REQUEST[setid];
		$dao=D("WebMailSet");
		$dao->where("setid=$setid")->delete();
		
		//这个未完成
		$mydir=$this->mailfile.$setid;//根据邮箱id生成目录
		@unlink($mydir);
		
		$this->assign("jumpUrl",__APP__."/WebMail/index/to/index/");
		
		$this->success("成功删除");
		//$this->redirect("index");
	}
	
	protected function testlink(){
		$setid=intval($_REQUEST[setid]);
		$dao=D("WebMailSet");
        $row=$dao->where("setid='$setid'")->find();
        $row[poppass]=deconfuse($row[poppass],$row['popserver'].$row['popport'].$row['popuser']);
        if ($row) {
        	if ($row[acctype]=="pop3") {//pop3方式
			    $CONN=new pop3($row[popserver],$row[popport],0);
				if ($CONN->check_connected()!="connected") {
					$this->error("服务器链接失败");
				}
			    $status=$CONN->login($row[popuser],$row[poppass],0);	
	            if (!$status['login']) { // Login failed
	                $CONN->close();
	                unset($CONN);
	                $this->error("登录失败");
	            }
	            
			}else {
				
			}
	    
			$tofile = uniqid(time().'.', true);
		    $num=2;
		    $success = $CONN->retrieve_to_file($num, $this->mailfile.$tofile.".in");
		    $uidl = $CONN->uidl($num);
		    
		    //$num = $CONN->msgno($uidl);
		    //echo $num."<br>";
		    
		    //echo $uidl."<br>";
		    //print_r($success);		
		    @chmod($success,"777"); 
            $mh = fopen($success, 'r');
            list ($header, $struct) = mailparser::parse($mh);
            
           // print_r($header);
                
					
	    }


		//echo $success;
        //print_r($success);		
		
		//echo $re;
		
		
	}
	
	
}

?>