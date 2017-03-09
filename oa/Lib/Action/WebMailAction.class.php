<?
import("@.Util.pop3");
import("@.Util.imap");
import("@.Util.driver");
import("@.Util.mailstream");
import("@.Util.mime");
import("@.Util.smtp");
import("@.Util.idna_convert");
import("@.Util.mailparser");
include_cache(APP_PATH."/Common/mail.php");
class WebMailAction extends PublicAction {
    var $mailfile;
    var $mailtemp;
	public function _initialize(){
		$this->curtitle="邮件";
		$this->assign("curtitle",$this->curtitle);
		$this->mailfile=APP_PATH."/mailfile/";//邮件缓存目录
		$this->mailtemp=APP_PATH."/mailtemp/";//发邮件时保存的默认目录
	    
		parent::_initialize();
	}
	/**
	 * 首页
	 * 
	 * */
	public function index(){
		//print_rr($_REQUEST);
		//EXIT;
		$to=$_REQUEST[to];
		if (isset($_REQUEST[setid])) {
		    $setid=$_REQUEST[setid];
		}
		
		if (!$setid) {
			$daos=D("WebMailSet");
			$rows=$daos->where("uid='$this->_uid'")->order("setid desc")->find();
			$setid=$rows[setid];
		}
		
		$toarray=array("inbox","drafts","sent","waste","writeMail");
		$toarraynb=array("inbox","outbox","sentbox","deletebox","add");
		$toarrayset=array("index","form");
		$toarraybox=array("mailbox");
		
        if ($to&&(in_array($to,$toarray)||in_array($to,$toarrayset))) {
        	$this->assign("disp_nb","display:none");
        	$this->assign("o_cls","set".$setid);
            $this->assign("hcls","class='downlist'");        	
        }elseif(in_array($to,$toarraynb)){

            $this->assign("disp_nb","");
            $this->assign("o_cls","nb");
            $this->assign("hcls","class='uplist'");
        }else{
            $this->assign("disp_nb","");
            $this->assign("o_cls","nb");
            $this->assign("hcls","class='uplist'");
        }
        
		$dao=D("WebMailSet");
		$daof=D("WebMailEmailFolders");
		$list=$dao->where("uid='$this->_uid'")->order("listorder")->findall();
		
		foreach ($list as $key=>$row){
		   //$folder=$daof->where("uid=$this->_uid and friendly_name!=''")->findall();
		   if ($setid==$row[setid]&&$to) {
               $list[$key][disp]="display:block";
               $list[$key][hcls]="";		   	
		   }else {
		       $list[$key][disp]="display:none";	
		       $list[$key][hcls]="class='downlist'";	   	  	
		   }
		
		   $folder=$daof->where("setid='$row[setid]' and friendly_name!=''")->findall();
		   $list[$key][sub]=$folder;
		}
		//print_rr($list);
		$this->assign("folderlist",$list);//邮箱列表
		//echo "/index.php/WebMail/$to/setid/$setid";
		
		if (in_array($to,$toarray)) {
		   $daobox=D("EmailBox");
		   $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	       $this->assign("boxlist",$boxlist);
	       			
			$this->display();
			
			echo '<script>
			$(document).ready(function(){
			  $("#mail-act").html("");
			  $("#mail-act").load("/index.php/WebMail/'.$to.'/setid/'.$setid.'");})
			  </script>';
		}elseif(in_array($to,$toarrayset)){
		   $daobox=D("EmailBox");
		   $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	       $this->assign("boxlist",$boxlist);
	       			
			$this->display();
			echo '<script>
			$(document).ready(function(){
			  $("#mail-act").html("");
			  $("#mail-act").load("/index.php/WebMailSet/'.$to.'/setid/'.$setid.'");})
			  </script>';
		}elseif (in_array($to,$toarraynb)){
			
		   $daobox=D("EmailBox");
		   $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	       $this->assign("boxlist",$boxlist);
        	if ($to=="add") {
        		$toid=$_REQUEST[toid];
        		$daou=D("User");
        		$rowu=$daou->where("USER_ID='$toid'")->find();
        		$tou='/TO_ID/U_'.$rowu[uid].'/TO_NAME/'.urlencode($rowu[USER_NAME]);
        	}
			$this->display();
			echo '<script>
			$(document).ready(function(){
			  $("#mail-act").html("");
			  $("#mail-act").load("/index.php/Email/'.$to.$tou.'");})
			  </script>';
		}elseif (in_array($to,$toarraybox)){
		   $daobox=D("EmailBox");
		   $boxlist=$daobox->where("uid='$this->_uid'")->order("BOX_NO")->findall();
	       $this->assign("boxlist",$boxlist);
	    
			$this->display();
			echo '<script>
			$(document).ready(function(){
			  $("#mail-act").html("");
			  $("#mail-act").load("/index.php/Email/'.$to.'");})
			  </script>';
			
		}else {
		//默认邮件列表//内部邮件
		//$lo=0;
        EmailAction::inbox(); 
		}
		
		//$this->display();
	}
	/**
	 * 接收邮件
	 * 
	 * */
    protected function receive($setid="0"){
		//$setid=intval($_REQUEST[setid]);
		if (!$setid) {
			$this->error("接收的邮箱不存在");
			return false;
		}
		$dao=D("WebMailSet");
        $row=$dao->where("setid='$setid'")->find();
        $row[poppass]=deconfuse($row[poppass],$row['popserver'].$row['popport'].$row['popuser']);
        //echo $row[poppass];
        //exit;
        if ($row) {
        	if ($row[acctype]=="pop3") {//pop3方式
        		//echo "aaa";
			    $CONN=new pop3($row[popserver],$row[popport],0);
			    
			    //echo $CONN->check_connected()."bbb";exit;
				if ($CONN->check_connected()!="connected") {
					$this->error("服务器链接失败");
				}
			    $status=$CONN->login($row[popuser],$row[poppass],0);
			    //echo $status;	
			    //exit;
	            if (!$status['login']) { // Login failed
	                $CONN->close();
	                unset($CONN);
	                $this->error("登录失败");
	            }
			}else {
				
			}
				//	echo $setid."ddd";
		//exit;

			$maillist = $CONN->get_list();
		    $attlist = array();
		    foreach ($maillist as $num => $flags) {
		        $attlist[$num] = $flags;
		        $maillist[$num] = $flags['uidl'];
		    }
    		$CONN->close();
		    if (isset($row) && ($row['leaveonserver'] || $row['acctype'] == 'imap')) {
		        if ($row['acctype'] == 'imap') {
		        } else {
		        	$daocache=D("WebMailEmailUidlcache");
		            list ($maillist, $deletelist) = $daocache->uidlcache_match($setid, $maillist);
		            if (!empty($deletelist)) {
		            	foreach ($deletelist as $ouidl) 
		                  WebMailModel::delete_mail(false, $fileto, $ouidl);
		            }
		        }
		    }
		    $mylist = array();
		    foreach ($maillist as $num => $uidl) {
		        $mylist[] = '"'.addcslashes($num, '"').'"';
		        $_SESSION['WPs_email_fetcher']['UIDLs'][$pid][$num] = $uidl;
		    }
	    }
	    //print_rr($maillist);
	    
	    return $maillist;
    }
    /**
     * 接收邮件到本地
     * */
    protected function receiveto($mail=0,$setid=0,$folder=""){
    	//echo "aaa";exit;
	    if (isset($_REQUEST['folder'])) {
	        $num = false;
	        $uidl = stripslashes($_REQUEST['uidl']);
	        $f = $FS->get_folder_info(intval($_REQUEST['folder']));
	        list ($pid, $folder) = explode(':', $f['folder_path'], 2);
	    } else {
	        //$pid = intval($_REQUEST['pid']);
	        $pid = $setid;
	        $num = intval($mail);
	        //$num=1;
	        $uidl = false;
	        $folder = 'INBOX';
	    }
    		
		$dao=D("WebMailSet");
        $login=$dao->where("setid='$setid'")->find();//当前邮箱的配置
        $login[poppass]=deconfuse($login[poppass],$login['popserver'].$login['popport'].$login['popuser']);
        //echo $login[poppass];
        
        if ($login) {
        	if ($login[acctype]=="pop3") {//pop3方式
			    $CONN=new pop3($login[popserver],$login[popport],0);
				if ($CONN->check_connected()!="connected") {
					$this->error("服务器链接失败");
				}
			    $status=$CONN->login($login[popuser],$login[poppass],0);	
	            if (!$status['login']) { // Login failed
	                $CONN->close();
	                unset($CONN);
	                $this->error("登录失败");
	            }
    	       $uidl = $CONN->uidl($num);
    	       $login['cachetype'] = 'full';
			}else {
			}
		    if (!$num) $error = true;
		    if (!$error) {
		    	
		        if ($login['acctype'] == 'imap') {
		            //$fileto = WebMailEmailFoldersModel::get_folder_id_from_path($pid.':'.$folder);
		            //暂不支持
		        } else {
		            $fileto = "inbox";//默认inbox
		            
		           // echo $fileto;exit;
		            if (isset($login['inbox']) && $login['inbox']) {//用户自建的默认收件箱，当前未开通此功能
		                $inbox = WebMailEmailFoldersModel::get_folder_info($this->_uid,$login['inbox']);
		                if (is_array($inbox) && $inbox['folder_path']) {
		                    $fileto = $inbox['folder_path'];
		                }
		            }
		        }
		        $mailfile = uniqid($this->CUR_TIME_INT.'.', true);
		        if (!$login['leaveonserver']) $num = 1;
		        $mailprops = $CONN->get_list($num);
		        $mail_size = $mailprops['size'];
		        
		        $pathin = $this->mailfile.$login[setid]."/".$fileto."/".$mailfile.".in";//fileto是inbox，收件箱
		        $pathout = $this->mailfile.$login[setid]."/".$fileto."/".$mailfile.".out";//fileto是inbox，收件箱
		        
		        if (0) {//$login['checkspam'] && $mail_size < $spamcheck_maxsize//过滤邮件为false
		            $success = $CONN->retrieve_to_file($num, $pathin);
		            if (!$success) {
		               // sendJS('{ "error": "'.addcslashes($CONN->get_last_error(), '"').'"}', 1, 0);
		                $error = true;
		            }
		            if (!$error) {
		                $spamcomd = str_replace('$1', $pathin, $spamassassin);
		                $spamcomd = str_replace('$2', $pathout, $spamcomd);
		                //echo $spamcomd;exit;
		                exec($spamcomd, $void, $deferred_junk);
		                // Make sure, SA could be called and produced a tagged mail
		                if (file_exists($pathout)&& is_readable($pathout)&& sprintf("%u", filesize($pathout)) != 0) {
		                    // Read the mail structure
		                    @chmod($pathout,"777");
		                    $mh = fopen($pathout, 'r');
		                    list ($header, $struct) = mailparser::parse($mh);
		                    fclose($mh);
		                    unset($struct['last_line']);
		                    $header['struct'] = serialize($struct);
		                    $mail_size = filesize($pathout);
		                    $header['folder_path'] = $fileto;
		                    rename($pathout,$path.'/'.$mail_path.'/'.(($login['cachetype'] != 'full') ? $temp_name : $fileto).'/'.$mailfile);
		                    unlink($pathin);
		                } else {
		                    $deferred_junk = false;
		                    // Read the mail structure
		                    @chmod($pathin,"777");
		                    $mh = fopen($pathin, 'r');
		                    list ($header, $struct) = mailparser::parse($mh);
		                    fclose($mh);
		                    unset($struct['last_line']);
		                    $header['struct'] = serialize($struct);
		
		                    $mail_size = filesize($pathin);
		                    $header['folder_path'] = $fileto;
		                    rename($pathin,$path.'/'.$mail_path.'/'.(($login['cachetype'] != 'full') ? $temp_name : $fileto).'/'.$mailfile);
		                }
		                $old_umask = umask(0);
		                chmod($path.'/'.$mail_path.'/'.$fileto.'/'.$mailfile, $_PM_['core']['file_umask']);
		                umask($old_umask);
		            }
		            //echo $mh."aaaa";
		        } else {
                    $success = $CONN->retrieve_to_file($num, $pathin);
		            if (!$success) {
		                $this->error("获取文件失败");
		            }
		            $mail_size = filesize($pathin);
		            @chmod($pathin,"777");
		            $mh = fopen($pathin, 'r');
		            list ($header, $struct) = mailparser::parse($mh);
		            fclose($mh);
		            unset($struct['last_line']);
		            $header['struct'] = serialize($struct);
		        }
		       // print_rr($header);
		       // exit;
		        if ($header['spam_status']) $deferred_junk = true;
		        $header['ouidl'] = $uidl;
		        $header['profile'] = $pid;
		        if ($login['acctype'] == 'imap') {
		            $header['folder_id'] = $fileto;
		        } else {
		            $header['folder_path'] = $fileto;
		        }
		        $header[setid]=$pid;//当前接收的邮箱ID
		        
		        $header['size'] = $mail_size;
		        if (!isset($header['priority'])) $header['priority'] = $header['importance'];
		        $header['date_received'] = date('Y-m-d H:i:s',$this->CUR_TIME_INT);
		        if (isset($header['date']) && $header['date']) {
		            $header['date_sent'] = date('Y-m-d H:i:s', ($header['date']) ? $header['date'] : $this->CUR_TIME_INT);
		        } else {
		            $header['date_sent'] = $header['date_received'];
		        }
		        $header['filed'] = true;
		        $header['uidl'] = $mailfile;
		        $header['status'] = (!$mailprops['recent'] && $mailprops['seen'] ? 'r' : 'u').($mailprops['answered'] ? 'a' : '');
		        $header['unseen'] = (substr($header['status'], 0, 1) == 'r') ? 0 : 1;
		
		        if (isset($header['x_phm_msgtype']) && $header['x_phm_msgtype']) {
		            if ($header['x_phm_msgtype'] == 'SMS') $header['type'] = 'sms';
		            if ($header['x_phm_msgtype'] == 'EMS') $header['type'] = 'ems';
		            if ($header['x_phm_msgtype'] == 'MMS') $header['type'] = 'mms';
		            if ($header['x_phm_msgtype'] == 'SystemMail') $header['type'] = 'sysmail';
		        }
		
		        if (isset($header['content_type']) && isset($header['mime'])
		                && !preg_match('!^text/(plain|html)!i', $header['content_type'])
		                && '1.0' == trim($header['mime'])) {
		            if ('multipart/alternative' == $header['content_type']) {
		                $header['attachments'] = 0;
		                foreach ($struct['body']['part_type'] as $k => $v) {
		                    $v = strtolower($v);
		                    if (isset($struct['body']['dispo'][$k]) && $struct['body']['dispo'][$k] == 'attachment') {
		                        $header['attachments'] = 1;
		                        break;
		                    }
		                }
		            } elseif ('multipart/report' == $header['content_type']) {
		                $header['type'] = 'receipt';
		                $header['attachments'] = 1;
		            } elseif (in_array($header['content_type'], array('text/calendar', 'text/vcalendar', 'text/icalendar', 'text/x-vcal', 'text/x-vcalendar'))) {
		                $header['type'] = 'appointment';
		                $header['attachments'] = 1;
		            } else {
		                $header['attachments'] = 1;
		            }
		        } else {
		            $header['attachments'] = 0;
		        }
		        //print_rr($header);
		        //exit; 
		        //echo $login['cachetype'];
		        if ($login['cachetype'] == 'full') {
		            $header['cached'] = '1';
		            $dao=D("WebMail");
		            
		            $newmail_id = $dao->file_mail($header);
		            
		            if ($login['leaveonserver']) {
		                WebMailEmailUidlcacheModel::uidlcache_additem($pid, $uidl);
		            } else {
		                //$stat = $CONN->delete($num);
		            }
		            $CONN->close();
		        } else {
		            $header['cached'] = '0';
		            $CONN->close();
		            if (!@unlink($pathin)) {
		            	$this->error($pathin);
		            }
		            $dao=D("WebMail");
		            $newmail_id = $dao->file_mail($header);
		        }
        		          		        
		    	
		    }

    		
        }
    }
    
    /**
     * 批量收信
     * 
     * */
     public function receivelist(){
     	//echo "aaa";
     	//exit;
     	$setid=intval($_REQUEST[setid]);
     	
     	$setids=$_REQUEST[setids];
     	if ($setids) {
			$setid=substr($setids,3);
			$setid=intval($setid);     		
     	}

        $dao=D("WebMailSet");
        $row=$dao->where("setid=$setid")->find();
        if (!$row) {
        	$this->error("未设置接收邮件的地址");
        }
       // echo $setid;
     	$mails=$this->receive($setid);
     	$k=0;
     	foreach ($mails as $key=>$uidl){
     		
     		$this->receiveto($key,$setid);
     		$k++;
     	}
     	//echo "共接收".$k."封邮件";
     	
     	header("location:".__URL__."/inbox/setid/".$setid);
     	
     	//$this->assign("msgmail","共接收".$k."封邮件");
     	/*
     	$this->display();
			echo '<script>
			$(document).ready(function(){
			  $("#mail-act").html("");
			  $("#mail-act").load("/index.php/WebMail/writeMail");
})
			  </script>';
		*/	
     	//$this->success("共接收".$k."封邮件");
     }
      
     /**
      * 显示邮件内容
      * */
     public function view(){
     	$idx=$_REQUEST[idx];
     	$from=$_REQUEST['from'];
     	//echo $from;
     	
     	$setid=$_REQUEST[setid];
     	//echo $setid;
     	$dao=D("WebMailEmail");
     	$dao->setField("meta_read","r","idx=$idx");
     	
     	$row=$dao->where("idx='$idx'")->find();
     	
        $daomy=D("WebMail");
        //$this->mailfile=$this->mailtemp;
        if (!$setid) {
           $setid=$row[meta_profile];//这个是邮箱id setid	
        }
        $folderid=$row[folder_id];
        //echo $folderid;
        $daof=D("WebMailEmailFolders");
        //echo 
        $path=$daof->get_folder_path($setid,$folderid,$folderpath="",$this->mailfile);
        //echo $path;
     	$return_m=$daomy->getMailBody($idx,$path);
     	
     	$struct=$return_m[struct];
        $return = mailparser::get_visible_attachments($struct['body'], 'links', APP_PATH.'/Tpl/default/Public/images/mime');
        $attlist=array();
        foreach ($return[img] as $key=>$value){
	        $attlist[$key][name]=$return[name][$key];
	        $attlist[$key][linkkey]=$key;
        }
        $attcount=count($attlist);
    	$this->assign("mailbody",$return_m[mailbody]);
     	$this->assign("attlist",$attlist);    	
     	$this->assign("attcount",$attcount);    	
     	$this->assign("row",$row);
     	$this->assign("from",$from);
     	$this->assign("setid",$setid);
     	
     	
		##上下封
		   	///index.php/WebMail/view/?setid={$setid}&from=inbox&idx=
		   $str="";	
		   //$from=$_REQUEST['from'];   
		   //$dao=D("WebMailEmail");
		if ($from=="inbox"||$from=="sent"||$from=="waste") {
			
		   $mappre="uid='$this->_uid' AND folder_id='$folderid' and idx>$idx";
		   $row=$dao->where($mappre)->field("idx")->order("hdate_sent asc")->find();
		   //echo $dao->getlastsql()."<br>";
		   if ($row[idx]) {
		   $str.="<a href='#' onclick='javascript:$(\"#mail-act\").load(\"/index.php/WebMail/view/idx/$row[idx]/setid/$setid/from/$from\")'>上一封</a>";
		   }
		   $mapnext="uid='$this->_uid' AND folder_id='$folderid' and idx<$idx";
		   $row=$dao->where($mapnext)->field("idx")->order("hdate_sent desc")->find();
		   //echo $dao->getlastsql();
		   if ($row[idx]) {
		   $str.="<a href='#' onclick='javascript:$(\"#mail-act\").load(\"/index.php/WebMail/view/idx/$row[idx]/setid/$setid/from/$from\")'>下一封</a>";		   
		   }
		   		
		}else {
		   $mappre="uid='$this->_uid' AND folder_id='$folderid' and idx>$idx";
		   $row=$dao->where($mappre)->field("idx")->order("hdate_sent asc")->find();
		   if ($row[idx]) {
		   $str.="<a href='#' onclick='javascript:$(\"#mail-act\").load(\"/index.php/WebMail/view/idx/$row[idx]/setid/$setid/from/$from\")'>上一封</a>";
		   }
		   $mapnext="FROM_ID='$this->_uid' AND SEND_FLAG='1' and DELETE_FLAG='0' and idx<$idx";
		   $row=$dao->where($mapnext)->field("idx")->order("hdate_sent desc")->find();
		   if ($row[idx]) {
		   $str.="<a href='#' onclick='javascript:$(\"#mail-act\").load(\"/index.php/WebMail/view/idx/$row[idx]/setid/$setid/from/$from\")'>下一封</a>";		   
		   }
				
		}
	
		$this->assign("nextpre",$str);
		     	
     	
     	
     	
     	$this->display();
     }
     /**
      * 显示或下载附件
      * */
     public function viewAtt(){
     	$idx=$_REQUEST[idx];
     	$num=$_REQUEST[part];
     	$part=$num;
     	$_REQUEST[mail]=$idx;
     	$dao=D("WebMailEmail");
     	$row=$dao->where("idx='$idx'")->find();
     	
     	$struct=unserialize($row[meta_struct]);     	
     	$encoding = (isset($struct['body']['part_encoding'][$num]) && $struct['body']['part_encoding'][$num])
			        ? $struct['body']['part_encoding'][$num]
			        : ((isset($struct['header']['content_encoding'])) ? $struct['header']['content_encoding'] : '7bit' );

        $setid=$row[meta_profile];//这个是邮箱id
        $folderid=$row[folder_id];
        $daof=D("WebMailEmailFolders");
        $path=$daof->get_folder_path($setid,$folderid,$folderpath="",$this->mailfile);

     	$STOR=new mailstream($path);
     	$STOR->mail_open_stream($idx,"r");
     	$STOR->mail_seek_stream($struct['body']['offset'][$num]);
	    $save_as = 'noname';
	    if (isset($struct['body']['part_detail'][$num])
	            && preg_match('!name=("?)(.*)(\1)!i', $struct['body']['part_detail'][$num], $found)) {
	        $save_as = $found[2];
	    } elseif (isset($struct['body']['dispo_pad'][$num])
	            && preg_match('/name=("?)(.*)(\1)/i', $struct['body']['dispo_pad'][$num], $found)) {
	        $save_as = $found[2];
	    }
	    if ($content_type == 'message/delivery-status') {
	       $content_type = 'text/plain';
	       $save_as = 'delivery_status.txt';
	    }
        header('Cache-Control: post-check=0, pre-check=0');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.basename($save_as).'"');
	    header('Content-Transfer-Encoding: binary');
	    if ($row['meta_cached']) {
	    	//echo $encoding;exit;
	        $length = $struct['body']['length'][$num];
	        $read = 0;
	        while (true) {
	            $line = $STOR->mail_read_stream(0);
	            //echo $line;exit;
	            if (!$line) exit;
	            $read += strlen($line);
	            if (strtolower($encoding) == 'quoted-printable') {
	                echo quoted_printable_decode(str_replace('='.CRLF, '', $line));
	            } elseif(strtolower($encoding) == 'base64') {
	                echo base64_decode($line);
	            } else {
	                echo $line;
	            }
	            if ($read >= $length) exit;
	        }
	    } else {
	        if (strtolower($encoding) == 'quoted-printable') {
	            echo quoted_printable_decode(str_replace('='.CRLF, '', $STOR->get_imap_part($_REQUEST['mail'], $part)));
	        } elseif(strtolower($encoding) == 'base64') {
	            echo base64_decode($STOR->get_imap_part($_REQUEST['mail'], $part));
	        } else {
	            echo $STOR->get_imap_part($_REQUEST['mail'], $part);
	        }
	        exit;
	    } 
     }

     /**
      * 写邮件
      * */
     public function writeMail(){
     	$idx=$_REQUEST[idx];
		
     	$setid=$_REQUEST[setid];//=set1,set2...
     	if ($setid) {
     	   $setid=substr($setid,3);	
     	}else {
           $setid=$_REQUEST[setid_id];//=1,2... 		
     	}
		$setid=intval($setid);
		$dao=D("WebMailSet");
        $set=$dao->where("setid='$setid'")->find();
        $this->assign("set",$set);
        //print_rr($set);
		if ($idx) {
			$dao=D("WebMailEmail");
			$row=$dao->where("idx='$idx'")
			         ->find();
			  //       echo $dao->getlastsql();
			//print_rr($row);  
			$daof=D("WebMailEmailFolders");
			//echo $setid."<br>"
			$path=$daof->get_folder_path($setid,$row[folder_id],$folderpath="",$this->mailfile);
			//echo $path;       
			if ($_REQUEST[fw]) {//转发
		   		$daomy=D("WebMail");
		   		$mailbody.="<BR><BR><BR>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>发件人：</STRONG> ".$row[hfrom]." </FONT></DIV>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>发送时间：</STRONG> ".$row[hdate_sent]." </FONT></DIV>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>收件人：</STRONG> ".$row[hto]." </FONT></DIV>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>主题：</STRONG> ".$row[hsubject]." </FONT></DIV>";
		   		$return=$daomy->getMailBody($idx,$path);
		   		$mailbody.=$return[mailbody];
		   		$row[hto]="";
		   		$row[hsubject]="Fw: ".$row[hsubject];
			    $listatt=array();
			}elseif ($_REQUEST[reply]) {//回复
				
		   		$daomy=D("WebMail");
		   		$mailbody.="<BR><BR><BR>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>发件人：</STRONG> ".$row[hfrom]." </FONT></DIV>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>发送时间：</STRONG> ".$row[hdate_sent]." </FONT></DIV>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>收件人：</STRONG> ".$row[hto]." </FONT></DIV>";
		   		$mailbody.="<DIV><FONT face=Verdana size=2><STRONG>主题：</STRONG> ".$row[hsubject]." </FONT></DIV>";
		   		$return=$daomy->getMailBody($idx,$path);
		   		$mailbody.=$return[mailbody];
		   		
		   		$row[hsubject]="Re: ".$row[hsubject];
				$listatt=array();
			}else {//编辑
				
				$daomy=D("WebMail");
				
				
		   		$return=$daomy->getMailBody($idx,$path);
		   		
		   		$mailbody=$return[mailbody];
		   						
				//$mailbody=$daomy->getMailBody($idx,$this->mailfile);
				
			}
		    $this->assign("CONTENT",$mailbody);
			$this->assign("row",$row);
		}

        
     	$this->display();
     }
     /**
      * 删除邮件
      * 目前仅支持pop3
      * */
     public function delete(){
     	 $setid=$_REQUEST[setid];
         $idxs=$_REQUEST[idxs]; 
     	 $folder_path=$_REQUEST[folder];//可以为空
         $dao=D("WebMailEmail");      	      	 
     	 $idxarr=explode(",",$idxs);
     	 foreach ($idxarr as $idx){
           $dao->mailDelete($idx,$folder_path,$this->mailfile);       	      	 	
     	 }
     	 
     	 //header("location:".__URL__."/waste/setid/".$setid);
     }
     
     /**
      * 删除某邮件箱所有邮件
      * 目前仅支持pop3
      * */
     public function deleteall(){
     	 $folderid=$_REQUEST[folderid];
         $dao=D("WebMailEmail");
         $list=$dao->where("folder_id='$folderid'")->findall();
     	 foreach ($list as $row){
           $dao->mailDelete($row[idx],$folder_path,$this->mailfile);       	      	 	
     	 }
     	 //header("location:".__URL__."/waste/setid/".$setid);
     }
     
     /**
      * 转移
      * */
     public function change(){
         $idxs=$_REQUEST[idxs]; 
         
         $folderid=$_REQUEST[folderid];//转向到的邮箱;
         
         //echo "测试后再开放";exit;
         $daof=D("WebMailEmailFolders");
         $rowf=$daof->where("idx='$folderid'")->find();
     	 $folder_path=$rowf[folder_path];
     	 
     	 $dao=D("WebMailEmail"); 
     	 $idxarr=explode(",",$idxs);
     	 
     	 foreach ($idxarr as $idx){
           $dao->mailChange($idx,$folderid,$this->mailfile);       	      	 	
     	 }
     	 
     	 header("location:".__URL__."/".$folder_path."/setid/".$rowf[setid]);     	         
     	//print_rr($_REQUEST);     	
     	//EXIT;
     }

     /**
      * 发送邮件
      * */
     public function sendMail(){
     	//print_rr($_REQUEST);
     	//EXIT;
     	$savemail=$_REQUEST[savemail];
     	$setid=$_REQUEST[setid];
     	
        $tmp_path=$this->mailtemp;
     	$msg_id = uniqid($this->CUR_TIME_INT.'.');
	    $WP_send = array();
	    foreach (array
	            ('from', 'to', 'cc', 'bcc', 'subj', 'body', 'bodytype', 'return_receipt', 'prio', 'inreply'
	            ,'from_profile', 'replymode', 'orig', 'references'
	            ) as $k) {
	       if (!isset($_REQUEST['WP_send'][$k])) continue;
	       $WP_send[$k] = stripslashes(un_html($_REQUEST['WP_send'][$k]));
	    }
	    
	    if (!$WP_send[to]) {
             $this->error("收件人不能为空");   	    	
	    }

	    //echo $tmp_path.$msg_id.'.ser';
    	$state = file_put_contents($tmp_path.$msg_id.'.ser', serialize($WP_send));

	    if ($state) {
	    	//echo "dds";

	        if (isset($WP_send['bodytype']) && 'text/html' == $WP_send['bodytype']) {//无
	            $WP_send['body'] = str_replace(CRLF, LF, $WP_send['body']);
	        } else {
	            $WP_send['body'] = str_replace(CRLF, LF, $WP_send['body']);
	            $WP_send['bodytype'] = 'text/plain';
	        }
	        
	        if (function_exists('wordwrap')) {
	            $WP_send['body'] = wordwrap($WP_send['body']);
	        }

	        //echo "dds";
	        // On answering mails, refer to the original message ID
	        if (isset($WP_send['inreply']) && $WP_send['inreply']) {
	            $WP_send['inreply'] = trim($WP_send['inreply']);
	            $WP_send['inreply'] .= CRLF.'References: '
	                    .((isset($WP_send['references']) && $WP_send['references']) ? trim($WP_send['references']).' ' : '')
	                    .$WP_send['inreply'];
	            $WP_send['additional'] .= 'In-Reply-To: '.$WP_send['inreply'].CRLF;
	        }

	       // echo "dds";
	        $WP_send['additional'] .= set_prio_headers($WP_send['prio']);
		     
	        $mime_boundary = '_---_WebMail_--_'.$this->CUR_TIME_INT.'==_';
	        if (isset($WP_send['attach']) && is_array($WP_send['attach']) && !empty($WP_send['attach'])) {
	            $mime_encoding = true;
	            $attachments = true;
	        }
	        if (preg_match('/[\x80-\xff]/', $WP_send['body'])) {
	            $mime_encoding = true;
	            $bodylines = explode(LF, $WP_send['body']);
	            $WP_send['body'] = '';
	            foreach ($bodylines as $value) {
	                $WP_send['body'] .= quoted_printable_encode(decode_utf8($value, 'utf-8'));
	            }
	            unset($bodylines);
	            $body_qp = true;
	        }
	
	        if (isset($WP_send['return_receipt']) && $WP_send['return_receipt']) {
	            $WP_send['additional'] .= 'Return-Receipt-To: '.$receipt.CRLF.'Disposition-Notification-To: '.$receipt.CRLF;
	        }

	        // Create Message ID
	        if (isset($WP_send['from']) && $WP_send['from']) {
	            $addi = mailparser::parse_email_address($WP_send['from'], 0, false);
	            $dom = strstr($addi[0], '@');
	        } elseif (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
	            $dom = '@'.$_SERVER['SERVER_NAME'];
	        } else { // This is failsafe only
	            $dom = '@kaidaoa.local';
	        }
	        $WP_send['additional'] .= 'Message-ID: <'.$msg_id.$dom.'>'.CRLF;
	        // Create the message header
	        $header = create_messageheader
	                (array
	                        ('from' => $WP_send['from']
	                        ,'to' => $WP_send['to']
	                        ,'cc' => $WP_send['cc']
	                        ,'bcc' => $WP_send['bcc']
	                        ,'subject' => $WP_send['subj']
	                        )
	                ,$WP_send['additional']
	                ,'utf-8'
	                ,$connect['userheaders']
	                ,(isset($uh_before)) ? $uh_before : true
	                );
	        // Start composing the file
	        //echo $tmp_path.$msg_id;
	        //exit;
	        @chmod($tmp_path.$msg_id,"777");
	        $tmpout = fopen($tmp_path.$msg_id, 'w');
	        fwrite($tmpout, $header);
	        //echo $WP_send['body'];
	        if (isset($_REQUEST[ATTACHMENT_ID]) && $_REQUEST[ATTACHMENT_ID])  {
	        	$attidarr=$_REQUEST[ATTACHMENT_ID];
	            fwrite($tmpout, 'MIME-Version: 1.0'.CRLF);
	            fwrite($tmpout, 'Content-Type: multipart/mixed; boundary="'.$mime_boundary.'"'.CRLF);
	            fwrite($tmpout, CRLF);
	            fwrite($tmpout, 'This is a multipart message in MIME format.'.CRLF);
	            fwrite($tmpout, CRLF.'--'.$mime_boundary.CRLF);
	            if (isset($body_qp) && 'true' == $body_qp) {
	                fwrite($tmpout, 'Content-Type: '.$WP_send['bodytype'].'; charset='.'utf-8'.CRLF);
	                fwrite($tmpout, 'Content-Transfer-Encoding: quoted-printable'.CRLF.CRLF);
	            } else {
	                fwrite($tmpout, 'Content-Type: '.$WP_send['bodytype'].'; charset='.'utf-8'.CRLF.CRLF);
	            }
	            fwrite($tmpout, $WP_send['body']);
	            
	            foreach ($attidarr as $key=>$attid){
	               fwrite($tmpout, CRLF.'--'.$mime_boundary.CRLF);
	               $daoatt=D("Attachments");
	               $att=$daoatt->where("attid='$attid'")->find();
                   put_attach_file($tmpout, $this->uploadpath.$att['attachment'], $att['filetype'], $att['filename'], CRLF);
	               @unlink($tmp_path.$v['filename']);
	            }
	            fwrite($tmpout, CRLF.'--'.$mime_boundary.'--'.CRLF);
	        } else {
	        	
	            if (isset($body_qp) && 'true' == $body_qp) {
	                fwrite($tmpout, 'MIME-Version: 1.0'.CRLF);
	                fwrite($tmpout, 'Content-Type: '.$WP_send['bodytype'].'; charset='.'utf-8'.CRLF);
	                fwrite($tmpout, 'Content-Transfer-Encoding: quoted-printable'.CRLF);
	                fwrite($tmpout, CRLF);
	                fwrite($tmpout, rtrim($WP_send['body']).LF);
	            } elseif (isset($WP_send['bodytype']) && 'text/html' == $WP_send['bodytype']) {
	            	//echo $WP_send['body'];exit;
	                fwrite($tmpout, 'MIME-Version: 1.0'.CRLF);
	                fwrite($tmpout, 'Content-Type: '.$WP_send['bodytype'].'; charset='.'utf-8'.CRLF);
	                fwrite($tmpout, 'Content-Transfer-Encoding: 8bit'.CRLF);
	                fwrite($tmpout, CRLF);
	                fwrite($tmpout, '<html>'.CRLF.'<head>'.CRLF.' <title>HTML genereated by FCKeditor</title>'.CRLF.'</head>'.CRLF.'<body>'.CRLF);
	                fwrite($tmpout, trim($WP_send['body']).LF);
	                fwrite($tmpout, '</body>'.CRLF.'</html>'.CRLF);
	            } else {
	            	
	                fwrite($tmpout, CRLF.rtrim($WP_send['body']).LF);
	            }
	        }
	        fclose($tmpout);
	        /*
	        if (isset($_REQUEST['draft']) && $_REQUEST['draft']) {
	            $do = 'save&draft=1';
	            $statusmessage = $WP_msg['EmailSavingMail'];
	        } elseif (isset($_REQUEST['template']) && $_REQUEST['template']) {
	            $do = 'save&template=1';
	            $statusmessage = $WP_msg['EmailSavingMail'];
	        } else {
	            $do = 'send';
	            $statusmessage = $WP_msg['EmailSendingMail'];
	        }
	        */
	        /*
	        sendJS('{"statusmessage":"'.$statusmessage.'", "url": "'.PHP_SELF.'?WP_do='.$do.'&'.give_passthrough(1)
	                .'&load=send_email&handler=core&msg_id='.$msg_id.'"}');
	                */
	    }     	
//exit;
     	//$WP_send['from_profile'] = 1;
     	
     	if ($savemail) {
			        $daof=D("WebMailEmailFolders");
			        
			        $pathto=$daof->get_folder_path($setid,$folderid="",$folderpath="drafts",$this->mailfile);//copy到drafts
     		        $topath=$pathto.$msg_id;
     		        
				    $mytmp_ser = $tmp_path.$msg_id.'.ser';
				    $mytmpfile = $tmp_path.$msg_id;
				    $daoweb=D("WebMail");
				    $newmail_id=$daoweb->saveMail($mytmp_ser,$mytmpfile,$setid,$save=1,$dctime=$this->CUR_TIME_INT);
				    
		            copy($mytmpfile,$topath.".in");
		            unlink($mytmpfile);
                    unlink($mytmp_ser);
                    
		            if ($newmail_id) {
		            	$this->assign("jumpUrl",__URL__."/index/to/drafts/setid/".$setid);
		            	$this->success("成功保存");
		            }else {
		            	$this->error("保存失败");
		            }
			        //$state = $this->STOR->file_mail($header, $mh);

     	}else {
     		        $frompath=$tmp_path.$msg_id;  
		            $unread=false;
				    $mytmp_ser = $tmp_path.$msg_id.'.ser';
				    $mytmpfile = $tmp_path.$msg_id;
				         		
     	     	$WP_send = unserialize(file_get_contents($tmp_path.$msg_id.'.ser'));
     	     	//print_rr($WP_send);
     	     	//exit;
		        $to = explode(', ', gather_addresses(array(trim($WP_send['to']), trim($WP_send['cc']), trim($WP_send['bcc']))));
		        $from = mailparser::parse_email_address($WP_send['from'], 0, true);
		        //$setid=intval($_REQUEST[setid]);
				$dao=D("WebMailSet");
		        $row=$dao->where("setid='$setid'")->find();
		        //echo $dao->getlastsql();
		        //echo $row[smtppass]."aaa";
		        $row[poppass]=deconfuse($row[poppass],$row['popserver'].$row['popport'].$row['popuser']);
		        $row[smtppass]=deconfuse($row[smtppass],$row['smtpserver'].$row['smtpport'].$row['smtpuser']);
		        //echo $row[smtppass];
		        
		        $smtp_host = $row['smtpserver'];
		        $smtp_port = ($row['smtpport']) ? $row['smtpport'] : 25;
		        $smtp_user = $row['smtpuser'];
		        $smtp_pass = $row['smtppass'];
		        $smtpallowtls = ($row['smtpsec'] == 'auto');
		        if ($smtp_host&&$smtp_user&&$smtp_pass&&$smtp_port) {
		           $sm = new smtp($smtp_host, $smtp_port, $smtp_user, $smtp_pass, $smtpallowtls);
		        }else {
		        	$this->error("发送邮件前请设置完整SMTP服务");
		        }
		        $server_open = $sm->open_server($from[0], $to, filesize($tmp_path.$msg_id));
		        if (!$server_open) {
		            $WP_return .= str_replace('<br />', '\n', str_replace(LF, '', $sm->get_last_error())).'\n';
		            $this->error($WP_return);
		            $sm = false;
		        }
			    if ($sm) {
			    	
			    	@chmod($tmp_path.$msg_id,"777"); 
			        $tmpout = fopen($tmp_path.$msg_id, 'r');
			        while (!feof($tmpout) && $line = fgets($tmpout)){
			        	  $sm->put_data_to_stream($line); 
			        }
			        $sm->finish_transfer();
			        if (0) {//$_PM_['core']['send_method'] == 'sendmail'
			            if (!$sm->close()) {
			                $WP_return .= $WP_msg['nomailsent'].' ('.$sm->get_last_error().')\n';
			                $success = false;
			            } else {
			                $success = true;
			            }
			        }
			        if (1) {//$_PM_['core']['send_method'] == 'smtp'
			            if ($sm->check_success()) {
			                $success = true;
			            } else {
			                $WP_return .= $WP_msg['nomailsent'].' ('.$sm->get_last_error().')\n';
			                $success = false;
			            }
			            $sm->close();
			        }
			        
			    	
			        if ($success) {//$success
			        	###写入已发送库
				        $daof=D("WebMailEmailFolders");
				        $pathto=$daof->get_folder_path($setid,$folderid="",$folderpath="sent",$this->mailfile);//copy到sent
	     		        $topath=$pathto.$msg_id;
					    $mytmp_ser = $tmp_path.$msg_id.'.ser';
					    $mytmpfile = $tmp_path.$msg_id;
					    $daoweb=D("WebMail");
					    $newmail_id=$daoweb->saveMail($mytmp_ser,$mytmpfile,$setid,$save=0,$dctime=$this->CUR_TIME_INT);
					    
					   // exit();
			            copy($mytmpfile,$topath.".in");
			            unlink($mytmpfile);
	                    unlink($mytmp_ser);
	                   // echo __URL__."/index/to/inbox/setid".$setid;exit;
	                    $this->assign("jumpUrl",__URL__."/index/to/inbox/setid/".$setid);
			        	$this->success("成功发送");
			        } else {
			            unlink($mytmpfile);
	                    unlink($mytmp_ser);
			        	$this->error("发送失败");
			        }
			    } else {
			    	 $this->error("邮件服务配置错误");
			    }
     	}
     }
     /**
      * 显示收件箱内容列表
      * */
     public function inbox(){
     	$dao=D("WebMailEmail");
     	$setids=$_REQUEST[setids];
     	$setid=intval($_REQUEST[setid]);
     	
     	$daof=D("WebMailEmailFolders");
     	$rowf=$daof->where("setid='$setid' and folder_path='inbox'")->find();
     	$this->assign("folderid",$rowf[idx]);
     	
     	$boxlist=$daof->where("setid='$setid'")->findall();
     	$this->assign("boxlist",$boxlist);
     	
		//$map="meta_profile='$rowf[idx]' and uid='$this->_uid'";
		$map="folder_id='$rowf[idx]' and uid='$this->_uid'";
		$mapnew="folder_id='$rowf[idx]' and uid='$this->_uid' and meta_read = 'u'";
		$countnew=$dao->count($mapnew);
		
	    $count=$dao->count($map);//总数
	        if($count>0){
	        	import("@.Util.mailpage");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p          = new mailpage($count,$listRows);
				//分页查询数据
                $list=$dao->limit("$p->firstRow,$p->listRows")
                          ->order("hdate_sent desc")
                          ->where($map)
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	    $this->assign("curtitle","收件箱");
        $this->assign("COUNT",$count);
        $this->assign("countnew",$countnew);
        $this->assign("setid",$setid);
		$this->display();
     }
           
	  /**
      * 显示已发送邮件列表
      * */
     public function sent(){
     	$dao=D("WebMailEmail");
		$setid=intval($_REQUEST[setid]);
     	$daof=D("WebMailEmailFolders");
     	$rowf=$daof->where("setid='$setid' and folder_path='sent'")->find();
     	$this->assign("folderid",$rowf[idx]);
     	
     	$boxlist=$daof->where("setid='$setid'")->findall();
     	$this->assign("boxlist",$boxlist);
     	     	
		$map="folder_id='$rowf[idx]'  and uid='$this->_uid'";
		
		$mapnew="folder_id='$rowf[idx]' and uid='$this->_uid' and meta_read = 'u'";
		$countnew=$dao->count($mapnew);
		
	    $count=$dao->count($map);//总数
	        if($count>0){
	        	import("@.Util.mailpage");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p          = new mailpage($count,$listRows);
				//分页查询数据
                $list=$dao->limit("$p->firstRow,$p->listRows")
                          ->where($map)
                          ->order("hdate_sent desc")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	    $this->assign("curtitle","已发送邮件");
        $this->assign("COUNT",$count);
        $this->assign("countnew",$countnew);
        $this->assign("setid",$setid);
		$this->display();
     }
	  /**
      * 显示发件箱邮件列表
      * */
     public function drafts(){
     	$dao=D("WebMailEmail");
		$setid=intval($_REQUEST[setid]);
     	$daof=D("WebMailEmailFolders");
     	$rowf=$daof->where("setid='$setid' and folder_path='drafts'")->find();
     	$this->assign("folderid",$rowf[idx]);
     	
     	$boxlist=$daof->where("setid='$setid'")->findall();
     	$this->assign("boxlist",$boxlist);
     	     	
		$map="folder_id='$rowf[idx]' and uid='$this->_uid'";
		
		$mapnew="folder_id='$rowf[idx]' and uid='$this->_uid' and meta_read = 'u'";
		$countnew=$dao->count($mapnew);
				
	    $count=$dao->count($map);//总数
	        if($count>0){
	        	import("@.Util.mailpage");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p          = new mailpage($count,$listRows);
				//分页查询数据
                $list=$dao->limit("$p->firstRow,$p->listRows")
                          ->where($map)
                          ->order("hdate_sent desc")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	    $this->assign("curtitle","草稿箱");
        $this->assign("COUNT",$count);
        $this->assign("countnew",$countnew);
        $this->assign("setid",$setid);
		$this->display();
     }

	  /**
      * 显示已删除邮件列表
      * */
     public function waste(){
     	$dao=D("WebMailEmail");
		$setid=intval($_REQUEST[setid]);
     	$daof=D("WebMailEmailFolders");
     	$rowf=$daof->where("setid='$setid' and folder_path='waste'")->find();
     	$this->assign("folderid",$rowf[idx]);
     	
     	$boxlist=$daof->where("setid='$setid'")->findall();
     	$this->assign("boxlist",$boxlist);
     	     	
		$map="folder_id='$rowf[idx]' and uid='$this->_uid'";
		
		$mapnew="folder_id='$rowf[idx]' and uid='$this->_uid' and meta_read = 'u'";
		$countnew=$dao->count($mapnew);
				
	    $count=$dao->count($map);//总数
	    
	        if($count>0){
	        	import("@.Util.mailpage");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p          = new mailpage($count,$listRows);
				//分页查询数据
                $list=$dao->limit("$p->firstRow,$p->listRows")
                          ->where($map)
                          ->order("hdate_sent desc")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	    $this->assign("curtitle","已删除邮件");
        $this->assign("COUNT",$count);
        $this->assign("countnew",$countnew);
        $this->assign("setid",$setid);
		$this->display();
     }
     /**
      * 邮件搜索
      * 
      * */
     
     public function search(){
     	$keyword=$_REQUEST[keyword];
     	
     	$dao=D("WebMailEmail");
		$setid=intval($_REQUEST[setid]);
     	
		$daof=D("WebMailEmailFolders");
     	//$rowf=$daof->where("setid='$setid' and folder_path='waste'")->find();
     	
     	$boxlist=$daof->where("setid='$setid'")->findall();
     	$this->assign("boxlist",$boxlist);
     	     	
		//$map="folder_id='$rowf[idx]' and uid='$this->_uid'";
		$map="(hfrom like '%$keyword%' or hto like '%$keyword%' or hsubject like '%$keyword%') and uid='$this->_uid'";
	    $count=$dao->count($map);//总数
	    
	        if($count>0){
	        	import("@.Util.mailpage");
				if(!empty($_REQUEST['listRows'])) {
					$listRows  =  $_REQUEST['listRows'];
				}else {
					$listRows  =  '15';
				}
				$p          = new mailpage($count,$listRows);
				//分页查询数据
                $list=$dao->limit("$p->firstRow,$p->listRows")
                          ->where($map)
                          ->order("hdate_sent desc")
                          ->findall();
			//分页显示
			$page       = $p->show();
			$this->assign("page",$page);
			$this->assign('list',$list);
	        }
	        
	    $this->assign("curtitle","邮件搜索");
        $this->assign("COUNT",$count);
        $this->assign("BOX_ID",$BOX_ID);
		$this->display();
		     	
     }    
     
}

?>