<?
import("@.Util.pop3");
import("@.Util.mailparser");
include_cache(APP_PATH."/Common/mail.php");

class WebMailFetcherAction extends PublicAction {
    var $mailfile;
	public function _initialize(){
		$this->mailfile=APP_PATH."/mailfile/";//邮件缓存目录
		parent::_initialize();
	}
	public function testlink(){
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
	            
			}else {//imap方式
				
			}
	    
			$tofile = uniqid(time().'.', true);
		    $num=2;
		    $success = $CONN->retrieve_to_file($num, $this->mailfile.$tofile.".in");
		    $uidl = $CONN->uidl($num);
		    
		    //echo $uidl."<br>";
		    //print_r($success);		
		    @chmod($success,"777"); 
            $mh = fopen($success, 'r');
            list ($header, $struct) = mailparser::parse($mh);
            
           // print_r($header);
                
					
	    }
		
	}
	
	
}

?>