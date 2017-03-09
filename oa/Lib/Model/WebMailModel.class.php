<?
class WebMailModel extends BaseModel {
   var $mailpath="/oa/mailpath";
   //var $_uid = Session::get(_uid); 
	
	    /**
     * Connect to the IMAP box depending on the profile stored in the folder path
     *
     * @param string $folder_path This holds the profile ID and the path within the mailbox
     *[@param unknown_type $ro  TRUE for readonly access (prevents setting of Recent flags); Default: false]
     * @param unknown_type $to_root  TRUE to open the root mailbox; Default: FALSE]
     * @return bool
     */
    private function connect_imap($folder_path, $ro = false, $to_root = false, $conn = 0)
    {
        // Ugly, fugly
        global $DB;
        $profile = explode(':', $folder_path);
        if (!isset($profile[1]) || !$profile[0]) return false;
        $imapconn = $DB->get_popconnect(0, 0, 0, $profile[0]);
        $accdata = $DB->get_accdata(false, false, false, $profile[0]);
        // The profile no longer exists, so we cannot connect to it at all - remove it from the index!
        if (!isset($imapconn['popserver']) && !isset($imapconn['popport'])) {
            foreach ($this->get_imapkids($profile[0]) as $k => $v) {
                $this->IDX->remove_folder($this->uid, $k, false, true);
            }
            return true;
        } /*
        // Should speed up IMAP operations
        if (isset($this->IPcache) && isset($this->IPcache[$imapconn['popserver']])) {
            $imapconn['popserver'] = $this->IPcache[$imapconn['popserver']];
        } elseif (function_exists('gethostbyname') && !preg_match('!^\d+\.\d+\.\d+\.\d+$!', $imapconn['popserver'])) {
            $IP = @gethostbyname($imapconn['popserver']);
            if (false !== $IP) $imapconn['popserver'] = $IP;
        } */
        $imapconn['popport'] = ($imapconn['popport']) ? $imapconn['popport'] : 143;
        $sslopts = '/imap/novalidate-cert/norsh';
        if ($imapconn['popport'] == 993) {
            $sslopts = '/imap/ssl/novalidate-cert/norsh';
        } elseif (isset($imapconn['popnoapop']) && $imapconn['popnoapop']) {
            $sslopts = '/imap/notls/norsh';
        }
        $serverstring = '{'.$imapconn['popserver'].':'.$imapconn['popport'].$sslopts.'}'.($to_root ? '' : $profile[1]);
        if (isset($accdata['cachetype'])) {
            $this->imap[$conn]['cached_headers'] = ($accdata['cachetype'] == 'struct' || $accdata['cachetype'] == 'full');
            $this->imap[$conn]['cached_mail'] = ($accdata['cachetype'] == 'full');
        } else {
            $this->imap[$conn]['cached_headers'] = $this->imap[$conn]['cached_mail'] = false;
        }
        $mbox = @imap_open($serverstring, $imapconn['popuser'], $imapconn['poppass'], (($ro) ? OP_READONLY : 0) | OP_SILENT);
        if (!$mbox || !is_resource($mbox)) {
            $errstr = imap_alerts();
            $this->set_error($errstr[0]);
            return false;
        }
        $this->imap[$conn]['handle'] = $mbox;
        $this->imap[$conn]['serverstring'] = $serverstring;
        $this->imap[$conn]['mboxname'] = $imapconn['accname'];
        $this->imap[$conn]['profnum'] = $profile[0];
        $this->imap[$conn]['mboxstring'] = $profile[1];
        $this->imap[$conn]['onlysubscribed'] = (isset($imapconn['onlysubscribed']) && $imapconn['onlysubscribed'] ? true : false);
        if ($to_root) {
            $list = imap_getmailboxes($mbox, $serverstring, '%');
            $this->imap[$conn]['delimiter'] = $list[0]->delimiter;
        }
        return true;
    }
    
	private function disconnect_imap($conn = 0, $expunge = false)
    {
        if (isset($this->imap[$conn]['handle']) && $this->imap[$conn]['handle']) {
            imap_close($this->imap[$conn]['handle'], ($expunge !== false) ? CL_EXPUNGE : null);
        }
        $this->imap[$conn]['handle'] = false;
    }
    
	/**
	 * @param 
	 * 
	 * */
	function getmymail(){
		
		
	}
	
	 /**
    * 删除邮件（包括文件和数据库）
    *
    * [@param  int  邮件 id]
    * [@param  int  清空邮件文件夹，支持已删除邮件]
    * [@param  string  通过 uidl 删除邮件; Currently used by the folder syncer on opening an IMAP folder]
    * [@param  bool  是否彻底删除一个邮件]
    * @return  bool  返回值
    * @since 0.1.1
    */
    public function delete_mail($id = false, $folder = false, $ouidl = false, $forced = false,$froot="")
    {
    	$_uid=Session::get("LOGIN_UID"); 
    	if (false !== $folder) {
    		//$daofolder=D("WebMailEmailFolders");
    		//$info = $this->IDX->get_folder_info($this->uid, $folder);
    		$info = WebMailEmailFoldersModel::get_folder_info($_uid,$folder);
   		    if (':waste' == $info['att_icon'] || ':junk' == $info['att_icon']) {
   		        if (11 == $info['type'] || 10 == $info['type']) {
   		        	$drop = WebMailEmailModel::get_folder_uidllist($_uid,$folder);
   		            //$drop = $this->get_folder_uidllist($this->_uid, $folder);
   		            if (empty($drop)) return true; // Nothing to delete
   		            if (isset($this->imap[0]['handle']) && $this->imap[0]['handle']) {
   		                $this->disconnect_imap();
   		            }
   		            $this->connect_imap($info['folder_path'], false, false, 0);
   		            // How dumb is this, imap_delete allows to pass exactly one msg_no / uid
   		            imap_setflag_full($this->imap[0]['handle'], join(',', $drop), '\Deleted', ST_UID);
   		            imap_expunge($this->imap[0]['handle']);
                    $this->disconnect_imap(0, true);
                    return true;
   		        } else {
   		        	  WebMailEmailModel::mail_delete($_uid, false, $folder);
    		        //$this->IDX->mail_delete($this->uid, false, $folder);
    		        // Deleting the dir depends on the OS we are running on
    		        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || ini_get('safe_mode') || !is_callable('system')) {
    		            $d = opendir($froot.$info['folder_path']);
    		            while (false != ($file = readdir($d))) {
    		                if ('.' == $file) continue;
    		                if ('..' == $file) continue;
    		                @unlink($froot.$info['folder_path'].'/'.$file);
    		            }
    		            closedir($d);
    		        } else {
    		            @system('rm '.$froot.$info['folder_path'].'/*');
    		        }
    		        return true;
   		        }
    	    } else {
    		    // $this->set_error('Kann Ordner *'.$info['folder_path'].'/'.$folder.' nicht leeren');
    		    // Currently we don't even try to empty folders other than junk or waste
    		    return true;
    		}
    	} elseif (false !== $ouidl) {
    		//$d
    		$ret = WebMailEmailModel::mail_delete($_uid, false, $folder, $ouidl);
    		//if (!$ret) $this->set_error();
    		return $ret;
    	} else {
    		
    		$daomail=D("WebMailEmail");
    		$mail = $daomail->where("idx='$id'")->find();
    	    //$mail = $this->get_mail_info($id, true);
    	    $daofolder=D("WebMailEmailFolders");
    	    $info=$daofolder->where("idx='$mail[folder_id]'")->find();

    		//$info = $this->IDX->get_folder_info($this->uid, $mail['folder_id']);
   		    if (11 == $info['type'] || 10 == $info['type']) {
   		        // User held shift on deleting the mail, just kill it
   		        if ($forced) {
                    if (isset($this->imap[0]['handle']) && $this->imap[0]['handle']) {
                        $this->disconnect_imap();
                    }
                    $this->connect_imap($info['folder_path'], false, false, 0);
                    imap_setflag_full($this->imap[0]['handle'], $mail['ouidl'], '\Deleted', ST_UID);
                    imap_expunge($this->imap[0]['handle']);
                    $status = imap_errors();
                    $this->disconnect_imap(0, true);
                    return $this->IDX->mail_delete($this->uid, $id, false);
   		        }
                // IMAP folder -> Find the wastebin of this respective IMAP mailbox
                $profile = split(':', $info['folder_path'], 2);
                $accdata = $GLOBALS['DB']->get_accdata($this->uid, false, false, $profile[0]);
                $waste = $accdata['waste'];
                if (0 != $waste) { // The user defined a Junk folder for that profiel -> try to use it
                   $folderInfo = $this->get_folder_info($waste);
                    if (false === $folderInfo || empty($folderInfo)) $waste = false;
                } else { // Otherwise try using the system folder for that account, this will only work with IMAP
                    $waste = $this->IDX->get_system_folder($this->uid, 'waste', $profile[0]);
                    $folderInfo = $this->get_folder_info($waste);
                    if (false === $folderInfo || empty($folderInfo)) $waste = false;
                }
                if ($waste && $waste != $mail['folder_id']) {
                    return $this->move_mail($id, $waste);
                } elseif ($waste && $waste == $mail['folder_id']) {
                    if (isset($this->imap[0]['handle']) && $this->imap[0]['handle']) {
                        $this->disconnect_imap();
                    }
                    $this->connect_imap($info['folder_path'], false, false, 0);
                    imap_setflag_full($this->imap[0]['handle'], $mail['ouidl'], '\Deleted', ST_UID);
                    imap_expunge($this->imap[0]['handle']);
                    $status = imap_errors();
                    $this->disconnect_imap(0, true);
                    return $this->IDX->mail_delete($this->uid, $id, false);
                } else {
                    return $this->move_mail($id, $this->IDX->get_system_folder($this->uid, 'waste', 0));
                }

   		    }
   		    //以上程序不运行
   		    
    		//list ($path, $filename) = $this->IDX->mail_get_real_location($this->uid, $id);
    		//if (!$path || !$filename) return true;
    		//$from_path = $this->docroot.'/'.$path.'/'.$filename;
    		$from_path=$froot.$mail[folder_id]."/".$info[folder_path]."/";
    		// File might be missing -> just remove it from the index
    		if (!file_exists($from_path) || !is_readable($from_path) || !is_writable($from_path)) {
    			return WebMailEmailModel::mail_delete($_uid, $id, false);
    		}
    		// Mails are not deleted directly, instead they get moved to the trash
    		// Exception: User held shift, meaning: drop that mail, don't trash it first
    		if ($path != 'waste' && !$forced) {
    			return $this->move_mail($id, $this->get_folder_id_from_path('waste'));
    		}
    		$ret = $this->IDX->mail_delete($this->uid, $id, false);
    		if (!$ret) $this->set_error($this->IDX->get_errors());
    		unlink($from_path);
    		return $ret;
    	}
    }
	
    /**
     * 保存邮件  
     * 发送时保存到本地的已发送邮件
     * @param 
     * */
    public function saveMail($mytmp_ser,$mytmpfile,$setid="",$save=0,$dctime=""){
    	            $_uid=Session::get("LOGIN_UID");
    	            
    	            if (!$setid) {
    	            	return false;
    	            }

    	            $unread=false;
				    $WP_send = unserialize(file_get_contents($mytmp_ser));
				    
    		        @chmod($mytmpfile,"777");
		            $mh = fopen($mytmpfile, 'r');
		            list ($header, $struct) = mailparser::parse($mh);
		            $header['struct'] = serialize($struct);
		            fclose($mh);
		            unset($struct['last_line']);
			        $mail_size = filesize($mytmpfile);
			        $header['size'] = $mail_size;
			        $header['date_sent'] = date('Y-m-d H:i:s',$dctime);
			        if (isset($header['date']) && $header['date']) {
			           $header['date_sent'] = date('Y-m-d H:i:s', ($header['date']) ? $header['date'] : $dctime);
			        } else {
			          $header['date_sent'] = date('Y-m-d H:i:s',$dctime);
			        }
			        
    	            $daof=D("WebMailEmailFolders");
    	            $map=" setid='$setid' and uid='$_uid'";
    	            if ($save) {//如果是保存
    	               $map.=" and folder_path='drafts'";
    	               $rowf=$daof->where($map)->find();   	
    	               $header['folder_id'] = $rowf[idx];//保存到发件箱;
    	               
    	            }else{//发送邮件时保存到本地
    	               $map.=" and folder_path='sent'";
    	               $rowf=$daof->where($map)->find();   	
    	               $header['folder_id'] = $rowf[idx];//保存到发件箱;    	            	
    	            } 
    	            			        
			        $header['filed'] = false;
			        $header['status'] = ($unread) ? 'u' : 'r';
			        $header['uidl'] = basename($mytmpfile);
			        $header['type'] = $type;
			        $header['priority'] = $header['importance'];
			        $header['unseen'] = false;
			        $header['cached'] = true;
			        foreach (array('from', 'to', 'cc', 'bcc') as $k) {
			        	$header[$k]=$WP_send[$k];
			            //if (!isset($header[$k])) $header[$k] = '';
			        }
			        $header['subject']=$WP_send[subj];
			        //print_rr($WP_send);

				    
			        if (isset($header['content_type']) && isset($header['mime'])
			                && !preg_match('!^text/(plain|html)!i', $header['content_type'])
			                && '1.0' == trim($header['mime'])) {
			            $header['attachments'] = 1;
			        } else {
			            $header['attachments'] = 0;
			        }
			        rewind($mh);
			        
		            $daoweb=D("WebMail");
		            //print_rr($header);
				    //exit;
		            $newmail_id = $daoweb->file_mail($header);
		            fclose($mh);
    	            return $newmail_id;
    	
    }

    /**
    * Add a mail to the storage and indexer
    * @param  array Mail data
    * [- folder_path  string  Path of the folder within docroot to save the mail to]
    * [- folder_id  int  ID of the folder, either this or the folder_path MUST be given;
    *          if both are given, the ID takes precedence]
    * - uidl  string  Filename of the mail
    * - subject  string  Subject: header field
    * - from  string  From: header field
    * - to  string  To: header field
    * - cc  string  CC: header field
    * - bcc  string  Bcc: header field
    * - date_recveived  string  Date of reception of that mail
    * - date_sent  string  Date: header field of the mail
    * - size  string  Size of the mail in octets (bytes)
    * - prioritiy  int  Priority setting of the mail (1...5)
    * - attachments  boolean  Does this mail have attachments?
    * [- type  string  Type of the item to file ('mail', 'sms', 'ems', 'mms', 'appointment,'
    *          'receipt', 'away'); Default: mail]
    * - filed  boolean TRUE, if the mail is already saved in the specified location, FALSE if not;
    *          if FALSE is specified, one should pass an open file handle as the second parameter,
    *          where the mail will be read from
    * - struct  serialized mail structure data
    *[@param  resource  Open stream to read the mail data from]
    *[@param  string  Path to the source file, ONLY valid for IMAP folders as the destination!; Default: false]
    * @return int  Mail ID in the index
    * @since 0.1.0
    */
    public function file_mail($data, $res = false, $from_path = false)
    {
    	//print_rr($_SESSION);
    	$_uid=Session::get("LOGIN_UID");
    	//echo $data['folder_id'];
    	//exit;
        if (!isset($data['folder_path']) && !isset($data['folder_id'])) {
            $this->error='请选择邮件文件夹';
            return false;
        }
        if (isset($data['folder_path']) && !isset($data['folder_id'])) {
            $data['folder_id'] = WebMailEmailFoldersModel::get_folder_id_from_path($_uid, $data['folder_path'],false,$data[setid]);
        }
        // Check, whether the destination folder is an IMAP folder, if so, append the mail file to it.
        
        /*  这儿在邮件里没有什么用
        $info = WebMailEmailFoldersModel::get_folder_info($_uid, $data['folder_id']);
        if ((11 == $info['type'] || 10 == $info['type']) && $from_path) {
            if (isset($this->imap[0]['handle']) && $this->imap[0]['handle']) {
                $this->disconnect_imap();
            }
            $this->connect_imap($info['folder_path'], false, false, 0);
            imap_append
                    ($this->imap[0]['handle']
                    ,$this->imap[0]['serverstring']
                    ,file_get_contents($from_path)
                    ,(isset($data['status']) && !strstr('u', $data['status'])) ? '\Seen' : null
                    );
            $this->disconnect_imap();
            return true;
        }
        */
        
       // print_rr($data);
        //exit;
        $ID = WebMailEmailModel::mail_add($_uid, $data['folder_id'], $data);
        /*
        $this->IDX->resync_folder($this->uid, $data['folder_id']);
        if (!isset($data['filed']) || !$data['filed']) {
            if ($res && is_resource($res)) {
                list ($folderpath, $filename) = $this->IDX->mail_get_real_location($this->uid, $ID);
                $path = $this->docroot.'/'.$folderpath.'/'.$filename;
                if (!file_exists(dirname($path)) || !is_dir(dirname($path))) {
                    $this->create_dirtree(dirname($path));
                }
                $fh = fopen($path, 'w');
                while (false === feof($res) && $line = fgets($res, 1024)) {
                    fputs($fh, $line);
                }
                fclose($fh);
        		$old_umask = umask(0);
        		chmod($path, $GLOBALS['_PM_']['core']['file_umask']);
        		umask($old_umask);
            }
        }
        */
        return $ID;
    }
    
    
    /**
     * 得到邮件内容正文
     * @param 邮件路径
     * */
    public function getMailBody($idx="",$filepath=""){

	     	$dao=D("WebMailEmail");
	     	$row=$dao->where("idx='$idx'")->find();
	     	//echo $dao->getlastsql();
		  	$struct=unserialize($row[meta_struct]);
	     	###分析内容类型
			$part_text = $part_enriched = $part_html = -1;
			// Determine, which of the mail part is the mail body
			if (isset($struct['body']['part_type']) && is_array($struct['body']['part_type'])) {
			    $mode = 'mixed';
			    if (isset($struct['header']['content_type'])
			            && 'multipart/' == substr(strtolower($struct['header']['content_type']), 0, 10)) {
			        preg_match('!multipart/(\w+)!', strtolower($struct['header']['content_type']), $found);
			        $mode = (!empty($found) && isset($found[1])) ? $found[1] : 'mixed';
			    }
			    ksort($struct['body']['imap_part']); // Ensure the real structure is iterated upon
			    foreach ($struct['body']['imap_part'] as $k => $v) {
			        if (isset($old_mode) && substr($v, 0, strlen($parent)) != $parent) {
			            $mode = $old_mode;
			        } elseif ($mode == 'inlinemail') {
			            continue;
			        }
			        if (!isset($struct['body']['part_type'][$k])) $struct['body']['part_type'][$k] = 'text/plain';
			        $pType = strtolower($struct['body']['part_type'][$k]);
			        if ('multipart/' == substr($pType, 0, 10)) {
			            preg_match('!multipart/(\w+)!', $pType, $found);
			            if (!empty($found) && isset($found[1])) $mode = $found[1];
			        } elseif ('message/' == substr($pType, 0, 8) && 'message/delivery-status' != $pType) {
			            $parent = $v;
			            $old_mode = $mode;
			            $mode = 'inlinemail';
			            $parts_attach = true;
			            $struct['body']['part_attached'][$k] = true;
			        } elseif (isset($struct['body']['dispo'][$k]) && $struct['body']['dispo'][$k] == 'attachment') {
			            $parts_attach = true;
			            $struct['body']['part_attached'][$k] = true;
			            continue;
			        } elseif ('text/html' == $pType) {
			            if (('mixed' == $mode && (-1 != $part_html || -1 != $part_enriched || -1 != $part_text))
			                    || ('alternative' == $mode &&  -1 != $part_html)) {
			                $parts_attach = true;
			                $struct['body']['part_attached'][$k] = true;
			                continue;
			            }
			            $part_html = $k;
			            $num = $part_html;
			        } elseif ('text/enriched' == $pType) {
			            if (('mixed' == $mode && (-1 != $part_html || -1 != $part_enriched || -1 != $part_text))
			                    || ('alternative' == $mode &&  -1 != $part_enriched)) {
			                $parts_attach = true;
			                $struct['body']['part_attached'][$k] = true;
			                continue;
			            }
			            $part_enriched = $k;
			            $num = $part_enriched;
			        } elseif ('text/plain' == $pType || 'text' == $pType || 'message/delivery-status' == $pType) {
			            if (('mixed' == $mode && (-1 != $part_html || -1 != $part_enriched || -1 != $part_text))
			                    || ('alternative' == $mode && -1 != $part_text)) {
			                $parts_attach = true;
			                $struct['body']['part_attached'][$k] = true;
			                continue;
			            }
			            $part_text = $k;
			            $num = $part_text;
			        } else {
			            if (-1 != $part_html && $struct['body']['childof'][$part_html] != 0 && $mode == 'related'
			                    && $struct['body']['childof'][$k] == $struct['body']['childof'][$part_html]) {
			                continue;
			            }
			            $parts_attach = true;
			            $struct['body']['part_attached'][$k] = true;
			        }
			    }
			} elseif (isset($struct['header']['content_type'])) {
			    $struct['header']['content_type'] = strtolower($struct['header']['content_type']);
			    if ('text/plain' == $struct['header']['content_type'] || 'text' == $struct['header']['content_type']) {
			        $part_text = 0;
			        $num = $part_text;
			    } elseif ('text/enriched' == $struct['header']['content_type']) {
			        $part_enriched = 0;
			        $num = $part_enriched;
			    } elseif ('text/html' == $struct['header']['content_type']) {
			        $part_html = 0;
			        $num = $part_html;
			    }
			} else {
			    $part_text = 0;
			    $num = $part_text;
			}
			
	     	//$STOR=new mailstream();
	     	
			$content_type = (isset($struct['body']['part_type'][$num]) && $struct['body']['part_type'][$num])
			        ? $struct['body']['part_type'][$num]
			        : ((isset($struct['header']['content_type'])) ? $struct['header']['content_type'] : 'text/plain' );
			$encoding = (isset($struct['body']['part_encoding'][$num]) && $struct['body']['part_encoding'][$num])
			        ? $struct['body']['part_encoding'][$num]
			        : ((isset($struct['header']['content_encoding'])) ? $struct['header']['content_encoding'] : '7bit' );
			$ctype_pad = (isset($struct['body']['part_detail'][$num]) && $struct['body']['part_detail'][$num])
			        ? $struct['body']['part_detail'][$num]
			        : ((isset($struct['header']['content_type_pad'])) ? $struct['header']['content_type_pad'] : '' );
			        //echo $filepath;exit;
			        
	     	$STOR=new mailstream($filepath);
	     	
			//if ($row[meta_cached]) {
			    $STOR->mail_open_stream($idx, 'r');
			    $STOR->mail_seek_stream($struct['body']['offset'][$num]);
			//}else {//imap方式
	           //$part = $struct['body']['imap_part'][$num];
	        //}
	        
		    $mailbody = $STOR->mail_read_stream($struct['body']['length'][$num]);
		    if (strtolower($encoding) == 'quoted-printable') {
		        $mailbody = quoted_printable_decode(str_replace('='.CRLF, '', $mailbody));
		    } elseif(strtolower($encoding) == 'base64') {
		        $mailbody = base64_decode($mailbody);
		    }
		    if ($ctype_pad) {
		        preg_match('!charset="?([^";]+)("|$|;)!i', $ctype_pad, $found);
		    }
		    $charset = (isset($found[1])) ? $found[1] : 'iso-8859-1';
		    if (strtolower($content_type) == 'text/html') {
		        // Clean Up HTML
		        $mailbody = preg_replace
		                (array('!<script.*?>.+</script>!si', '!<iframe.*?>.*?</iframe>!si')
		                ,array('', '')
		                ,$mailbody
		                );
		        $mailbody=encode_utf8($mailbody, $charset, true);
		        header('Content-Type: '.$content_type.';charset='.$charset);
		    } elseif (strtolower($content_type) == 'text/enriched') {
		        // Convert Richtext to HTML
		        $mailbody = str_replace('<<', '&lt;', $mailbody);
		        $mailbody = nl2br(enriched_correct_newlines($mailbody));
		        $mailbody = str_replace
		                (array('<bold>', '</bold>', '<italic>', '</italic>', '<underline>', '</underline>', '<fixed>', '</fixed>', '<smaller>', '</smaller>', '<bigger>', '</bigger>', '<center>', '</center>')
		                ,array('<strong>', '</strong>', '<i>', '</i>', '<span style="text-decoration:underline;">', '</span>', '<tt>', '</tt>', '<font size="-1">', '</font>', '<font size="+1">', '</font>', '<div style="text-align:center;">', '</div>')
		                ,$mailbody
		                );
		        $mailbody = enriched_correct_colour($mailbody);
		        $mailbody = enriched_remove_unsupported($mailbody);
		        $mailbody=encode_utf8($mailbody, $charset, true);
		        header('Content-Type: text/html; charset='.$charset);
		    }else {
		    	    $mailbody=encode_utf8($mailbody, $charset, true);
		    }
	     	 $return[mailbody]=$mailbody; 
	     	 $return[struct]=$struct;
	     	 return $return;
		     //return $mailbody;
    }
    
	
}


?>