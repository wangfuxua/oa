<?
class WebMailEmailModel extends BaseModel {
	var $tableName="webmail_email";
	/*
	var $_validate=array(
	        array("WP_send[to]","require","收件人不能为空","ALL"),
	);
	*/    
	
	
	 /**
     * Retrieves a list of all UIDLs of a specific folder, mainly interesting for IMAP, when passing
     * $retfield = 'uidl' also for checking mails in the file system for their existance in the DB index
     *
     * @param int $uid  User ID for the given folder
     * @param int $id   Unique ID of the folder
     *[@param string $orderby  Order by this DB field]
     *[@param ASC|DESC $orderdir Order direction]
     * @param string  Return this DB field, by deafult "meta_ouidl" is used, it might also be 'uidl'
     * @return array
     * @since 0.5.0
     */
    public function get_folder_uidllist($uid, $id, $orderby = false, $orderdir = false, $retfield = 'meta_ouidl')
    {
        $return = array();
        $orderadd = '';
        $id = intval($id);
        $uid = intval($uid);
        if (false !== $orderby) {
            $orderadd = ' ORDER BY `'.$this->escape($orderby, null, '').'` '.('ASC' == $orderdir ? 'ASC' : 'DESC');
//            $orderadd
        }
        // Automatically drop doublettes
        $list=$this->where('folder_id='.$id.' GROUP BY CONCAT(meta_profile, ".", meta_ouidl) HAVING zahl > 1')->field("idx,meta_profile,meta_ouidl, count(*) zahl")->findAll();
        if ($list) {
        	foreach ($list as $row){
        		if (!$row[profile] || !$row[uidl]) continue; // Prevent killing entries without profile or uidl data contained
        		$this->where('CONCAT(meta_profile,".",meta_ouidl)="'.$profile.'.'.$uidl.'" AND idx!='.$idx)->delete();
                //$qid2 = $this->query('DELETE FROM '.$this->DB['tbl_mailindex'].' WHERE CONCAT(meta_profile,".",meta_ouidl)="'.$profile.'.'.$uidl.'" AND idx!='.$idx);
        	}
        }
        
        // Finally fetch the list
        if (is_array($retfield)) {
            foreach ($retfield as $k => $v) $retfield[$k] = '`'.$this->escape($v, null, '').'`';
            $retfield = join(',', $retfield);
            $retmode = 1;
        } else {
            $retfield = $this->escape($retfield, null, '');
            $retmode = 0;
        }
        
        $list=$this->where('folder_id='.$id.' AND uid='.$uid.$orderadd)->field('idx, '.$retfield)->findAll();
        //$qid = $this->query('SELECT idx, '.$retfield.' FROM '.$this->DB['tbl_mailindex'].' WHERE folder_id='.$id.' AND uid='.$uid.$orderadd);
        $wieviele = mysql_num_rows($qid);
        if (0 == $retmode) {
        	foreach ($list as $row){
        		list($idx,$ret)=$row;
        		$return[$idx] = $ret;
        	}
        } else {
        	
        	foreach ($list as $row){
        		$idx = $row['idx'];
                unset($row['idx']);
                $return[$idx] = $row;
        	}
        }
        return $return;
    }
	
    
 /**
    * Delete either a single mail or all mails within a given folder from index
    *
    * @param  int  $uid  The user ID to perform the operation for
    * @param  int  $mail  Optionally the mail to delete; if FALSE, one MUST specify the affected folder
    * @param  int  $folder  Optionally the folder to delete all mails for; if FALSE, one MUST specify the mail ID
    * @return  bool  TRUE on success, FALSE on failure
    * @since 0.0.7
    */
    public function mail_delete($uid = 0, $mail = false, $folder = false, $ouidl = false)
    {
    	$dao=D("WebMailEmail");
        if ($mail === false && $folder === false && $ouidl === false) {
            //$this->error('Please either specify the mail, its original UIDL or the folder, where all mails should be killed');
            $this->error('请选择邮件');
            return false;
        }
        if ($mail !== false) {
        	$row=$dao->where('uid='.intval($uid).' AND idx='.intval($mail))->field("folder_id,hsize,meta_read,meta_ouidl,meta_profile")->find();
            if (!$row[folder_id]) return false;
            $row[is_read] = (strstr('u', $row[is_read])) ? 1 : 0;
            $re=$dao->where('uid='.intval($uid).' AND idx='.intval($mail))->delete(); 
            if ($re) {
                $query = 'UPDATE '.'webmail_email_folders'.' SET meta_mailnum=meta_mailnum-1'
                        .', meta_mailsize=meta_mailsize-'.($row[hsize]+0).', meta_unread=meta_unread-'.$row[meta_read]
                        .' WHERE idx='.$row[folder_id];
                $dao->execute($query);
                if ($row[meta_ouidl] && $row[meta_profile]) WebMailEmailUidlcacheModel::uidlcache_markdeleted($row[meta_profile], $row[meta_ouidl]);
                return true;
            }
            return false;
        } elseif ($ouidl !== false) {
            $row=$dao->where('uid='.intval($uid).' AND folder_id='.intval($folder).' AND ouidl='.$this->escape($ouidl))->field('idx,hsize,meta_read,meta_ouidl,meta_profile')->find();
            if (!$row[idx]) return false;
            $row[is_read] = (strstr('u', $row[is_read])) ? 1 : 0;
            if ($dao->where('uid='.intval($uid).' AND idx='.intval($row[idx]))->delete()) {
                $query = 'UPDATE '.'webmail_email_folders'.' SET meta_mailnum=meta_mailnum-1'
                        .', meta_mailsize=meta_mailsize-'.($size+0).', meta_unread=meta_unread-'.$is_read
                        .' WHERE idx='.intval($folder);
                $dao->execute($query);
                if ($row[meta_ouidl] && $row[meta_profile]) WebMailEmailUidlcacheModel::uidlcache_markdeleted($row[meta_profile], $row[meta_ouidl]);
                return true;
            }
            return false;
        } elseif ($folder !== false) {
            $uidls = array();
            $list=$dao->where('uid='.intval($uid).' AND folder_id='.intval($folder))->field('meta_profile, meta_ouidl')->findAll();
            foreach ($list as $row){
                if (!$row[meta_profile] || !$row[meta_ouidl]) continue;
                WebMailEmailUidlcacheModel::uidlcache_markdeleted($row[meta_profile], $row[meta_ouidl]);            	
            }
            if ($this->where('uid='.intval($uid).' AND folder_id='.intval($folder))->delete()) {
                $query = 'UPDATE '.'webmail_email_folders'.' SET meta_mailnum=0,meta_mailsize=0,meta_unread=0 WHERE idx='.intval($folder);
                if (!$dao->execute($query)) {
                    return false;
                }
                return true;
            }
            return false;
        }
        // We fall down here, if some bogus input was given
        return false;
    }
     /**
     * 转移邮件仅支持pop3
     * 
     * */
    public function mailChange($idx="",$toid="",$docroot=""){
    	$_uid=Session::get("LOGIN_UID");
    	if (!$idx) {
    		return false;
    	}
    	
    	$dao=D("WebMailEmail");
    	$row=$dao->where("idx='$idx' AND uid='$_uid'")->find();//邮件信息
    	//print_rr($row);
    	if (!$row) {
    		return false;
    	}
    	$daof=D("WebMailEmailFolders");
    	$rowf=$daof->where("idx='$row[folder_id]'")->find();//邮件目录信息

    	$f_path=$docroot.$rowf[setid]."/".$rowf[folder_path]."/";//本地文件路径
    	$f_file=$f_path.$row[uidl].".in";
    	
    	$daom=D("WebMailSet");
    	$rowm=$daom->where("setid='$rowf[setid]'")->find();//得到当前邮箱的信息
    	
    	$dao->setField("folder_id",$toid,"idx='$idx'");//转移
    	
    	$rowto=$daof->where("idx='$toid'")->find();

    	if ($rowto[folder_path]=="waste") {
    		$daocache=D("WebMailEmailUidlcache");
    		$daocache->setField("delete","1","uidl='$row[meta_ouidl]'");
    	}else{
    		$daocache=D("WebMailEmailUidlcache");
    		$daocache->setField("delete","0","uidl='$row[meta_ouidl]'");    		
    	}
    		$to_path=$docroot."$rowto[setid]"."/".$rowto[folder_path]."/";
    		$to_file=$to_path.$row[uidl].".in";
    		###移动本地文件
    		@copy($f_file,$to_file);
    		
    		//$return="waste";

    	 return true;
    	//return true;//
    	
    	
    }
       
    /**
     * 删除邮件仅支持pop3
     * 
     * */
    public function mailDelete($idx="",$folder_path="",$docroot=""){
    	$_uid=Session::get("LOGIN_UID");
    	if (!$idx) {
    		return false;
    	}
    	
    	$dao=D("WebMailEmail");
    	$row=$dao->where("idx='$idx' AND uid='$_uid'")->find();//邮件信息
    	//print_rr($row);
    	if (!$row) {
    		return false;
    	}
    	$daof=D("WebMailEmailFolders");
    	$rowf=$daof->where("idx='$row[folder_id]'")->find();//邮件目录信息
    	if (!$folder_path) {
    		$folder_path=$rowf[folder_path];
    	}
    	$f_path=$docroot.$rowf[setid]."/".$rowf[folder_path]."/";//本地文件路径
    	$f_file=$f_path.$row[uidl].".in";
    	
    	$daom=D("WebMailSet");
    	$rowm=$daom->where("setid='$rowf[setid]'")->find();//得到当前邮箱的信息
    	$rowm[poppass]=deconfuse($rowm[poppass],$rowm['popserver'].$rowm['popport'].$rowm['popuser']);
    	
    	if ($folder_path=="waste") {//从已删除邮件目录里删除===彻底删除
    		if ($rowm[localkillserver]) {//如果从本地可以直接删除服务器上的邮件
			    $CONN=new pop3($rowm[popserver],$rowm[popport],0);
			    $status=$CONN->login($rowm[popuser],$rowm[poppass],0);	
			    $re=$CONN->delete_by_uidl(array("0"=>$row[meta_ouidl]));
    		}
    		@unlink($f_file);//删除本地的文件
    		$dao->where("idx='$idx'")->delete();//从数据库删除邮件
    		//echo $dao->getlastsql();
    	}else {//从其它目录删除===转移到已删除邮件目录
    		$rowwaste=$daof->where("setid='$rowf[setid]' and folder_path='waste'")->find();
    		//echo $daof->getlastsql();
    		$dao->setField("folder_id",$rowwaste[idx],"idx='$idx'");
    		$daocache=D("WebMailEmailUidlcache");
    		$daocache->setField("delete","1","uidl='$row[meta_ouidl]'");
    		
    		//echo $dao->getlastsql();
    		$to_path=$docroot."$rowwaste[setid]"."/waste/";
    		$to_file=$to_path.$row[uidl].".in";
    		###移动本地文件
    		@copy($f_file,$to_file);
    		
    		//$return="waste";
    	}
    	 return true;
    	//return true;//
    	
    	
    }
     
    /**
    * Returns header fields of a given mail ID. This is fetched from the index data created by
    * $this->init_mails()
    * @param  int  ID of mail to fetch data of
    *[@param  bool  Set to true, if info should be fetched from the indexer directly
    *[@param int  Set this to a specific folder id to retrieve the complete list for that folder]]
    * @return  mixed  false on error; empty array if impossible to retrieve data;
    *                array with header fields on success
    */
    public function get_mail_info($id, $direct = false, $folder = false)
    {
        if ($direct) {
        	
            $return = $this->IDX->get_mail_list($this->uid, $folder, false, false, false, false, $id);
            return ($folder) ? $return : $return[0];
        }
        if (!isset($this->midx) && !$this->init_mails()) return false;
        return isset($this->midx[$id]) ? $this->midx[$id] : array();
    }

    /**
    * Retrieve the mail index of a given folder and return as array
    * @param    int    ID of the affected user, give 0 for global folders
    * @param    int    ID of the folder
    * [@param    mixed    Skimming option, pass a FALSE value for no skimming, nonngeative integer for an offset]
    * [@param    mixed    Skimming option, pass a FALSE value for no skimming, nonngeative integer for a pagesize]
    * [@param  string  name of the DB field for ordering by; Default: hdate_sent]
    * [@param  'ASC'|'DESC'  Direction to order; Default: ASC]
    * [@param  int  ID of a mail for getting only this mail's information, omit everything else, pass folder ID 0 then]
    * [@param  string  Search criteria to match mails against, also pass pattern then]
    * [@param  string  Search pattern to match mails against, also pass criteria]
    */
    public function get_mail_list($uid = 0, $folder = 0, $offset = false, $pagesize = false, $ordby = false, $orddir = 'ASC', $idx = false
            ,$criteria = false, $pattern = false)
    {
        if (false === $uid) return false;
        if (false === $folder && false === $idx) return false;
        $return = array();
        $q_r = '';
        $valid_criteria = array
                ('from' => 'hfrom'
                ,'to' => array('hto', 'hcc', 'hbcc')
                ,'cc' => 'hcc'
                ,'bcc' => 'hbcc'
                ,'subject' => 'hsubject'
                ,'allheaders' => array('hfrom', 'hto', 'hcc', 'hbcc', 'hsubject')
                //, 'date_recv' <-- Das muss auch mit Patterns wie "<date_add(now(), 7 day)" usw. tun
                );
        if (!$ordby) {
            $ordby = 'hdate_sent';
        } elseif ($ordby == 'meta_status') {
            $ordby = 'meta_read';
        }

        // Limit result set to mails matching search criteria and search pattern
        if ($criteria !== false && $pattern !== false && isset($valid_criteria[$criteria])) {
            $pattern = $this->escape($pattern, null, '');
            if (is_array($valid_criteria[$criteria])) {
                $searches = array();
                foreach ($valid_criteria[$criteria] as $crit) {
                    $searches[] = $crit.' LIKE "%'.str_replace('%', '\%', str_replace('_', '\_', $pattern)).'%"';
                }
                $q_r .= ' AND ('.join(' OR ', $searches).')';
            } else {
                $q_r .= ' AND '.$valid_criteria[$criteria].' LIKE "%'.str_replace('%', '\%', str_replace('_', '\_', $pattern)).'%"';
            }
        }

        // Either select data for a single mail or optionally ordered, skimmable data from a folder
        if ($idx) {
            $q_r .= ' AND idx='.intval($idx);
        } else {
            $q_r .= ' AND folder_id='.intval($folder).' ORDER BY `'.$this->escape($ordby, null, '').'` '.($orddir == 'ASC' ? 'ASC' : 'DESC');
        }
        $query = 'SELECT idx, uidl, folder_id, hfrom, hto, hcc, hbcc, hsubject, hdate_recv, hdate_sent, hsize, hpriority'
                .', attachments, meta_read, meta_type, meta_ouidl, meta_profile, meta_dsn_sent, meta_cached, if(meta_colour IS NULL, "", meta_colour) `colour`'
                .', meta_htmlunblocked FROM '.$this->DB['tbl_mailindex'].' WHERE uid='.$uid.$q_r;
        if ($offset && $idx === false) {
            $query .= ' LIMIT '.(($pagesize) ? intval($offset).', '.intval($pagesize) : intval($offset));
        } elseif ($idx === false) {
            $query .= (($pagesize) ? ' LIMIT 0, '.intval($pagesize) : '');
        }
        $qh = $this->query($query);
        $mailcounter = ($offset) ? $offset : 0;
        while ($line = $this->fetchassoc($qh)) {
            $return[$mailcounter] = array
                    ('id' => $line['idx']
                    ,'uidl' => $line['uidl']
                    ,'folder_id' => $line['folder_id']
                    ,'from' => $line['hfrom']
                    ,'to' => $line['hto']
                    ,'cc' => $line['hcc']
                    ,'bcc' => $line['hbcc']
                    ,'subject' => $line['hsubject']
                    ,'date_received' => $line['hdate_recv']
                    ,'date_sent' => $line['hdate_sent']
                    ,'size' => $line['hsize']
                    ,'priority' => $line['hpriority']
                    ,'attachments' => $line['attachments']
                    ,'status' => $line['meta_read']
                    ,'type' => $line['meta_type']
                    ,'ouidl' => $line['meta_ouidl']
                    ,'profile' => $line['meta_profile']
                    ,'dsn_sent' => $line['meta_dsn_sent']
                    ,'cached' => $line['meta_cached']
                    ,'colour' => $line['colour']
                    ,'htmlunblocked' => $line['meta_htmlunblocked']
                    );
            ++$mailcounter;
        }
        return $return;
    }

       /**
    * Add a mail to the index
    *
    * @param  int  ID of the user to add the mail to
    * @param  int  Folder ID to add the mail to
    * @param  array  detailed header and meta information about the mail
    * @return  mixed  new ID of the mail on success, FALSE otherwise
    * @since 0.0.9
    */
    public function mail_add($uid = 0, $folder, $data)
    {
    	//echo "aaa";exit;
    	$daofolder=D("WebMailEmailFolders");
    	$map='uid='.intval($uid).' AND idx='.intval($folder);
    	$count=$daofolder->count($map);
    	//echo $daofolder->getlastsql();
    	if (!$count) {
    		$this->error;
    	}
    	/*
        $query = 'SELECT 1 FROM '.$this->DB['tbl_folders'].' WHERE uid='.intval($uid).' AND idx='.intval($folder);
        list ($fold_exists) = $this->fetchrow($this->query($query));
        if (!$fold_exists) {
            $this->set_error('Folder not owned by this user');
            return false;
        }
        */
    	//print_rr($data);
        if (!isset($data['status'])) $data['status'] = 'u';
        if (!isset($data['type'])) $data['type'] = 'mail';
        
        if (!isset($data['priority']) || !is_numeric($data['priority'])) $data['priority'] = 3;
        $fields = '';
        foreach (array
                ('uidl' => 'uidl'
                ,'from' => 'hfrom'
                ,'to' => 'hto'
                ,'cc' => 'hcc'
                ,'bcc' => 'hbcc'
                ,'subject' => 'hsubject'
                ,'date_received' => 'hdate_recv'
                ,'date_sent' => 'hdate_sent'
                ,'size' => 'hsize'
                ,'priority' => 'hpriority'
                ,'attachments' => 'attachments'
                ,'type' => 'meta_type'
                ,'status' => 'meta_read'
                ,'struct' => 'meta_struct'
                ,'ouidl' => 'meta_ouidl'
                ,'profile' => 'meta_profile'
                ,'cached' => 'meta_cached'
                ,'colour' => 'meta_colour'
                ) as $k => $v) {
                $adds[$v]=(isset($data[$k]) ? $data[$k] : '');
                
            //$fields .= ', `'.$v.'`='.$this->escape(isset($data[$k]) ? $data[$k] : '');
        }
        $adds[uid]=$uid;
        $adds[folder_id]=$folder;
        $adds[setid]=$data[profile];
       // print_rr($adds);
       
        $dao=D("WebMailEmail");
        $dao->create();
        $newID=$dao->add($adds);
        //echo $dao->getlastsql();
       // exit;
        //echo $dao->getlastsql();exit;
        $is_read = (isset($data['status']) && !strstr('u', $data['status'])) ? 0 : 1;
        $unseen = (isset($data['unseen']) && !$data['unseen']) ? 0 : 1;

        //$query = 'INSERT '.$this->DB['tbl_mailindex'].' SET uid='.$uid.', folder_id='.intval($folder).$fields;
        
        //$res = $this->query($query);
        if ($newID) {
            //list ($newID) = $this->fetchrow($this->query('SELECT LAST_INSERT_ID()'));
            
            $query = 'UPDATE webmail_email_folders'
                    .' SET meta_mailnum=meta_mailnum+1, meta_mailsize=meta_mailsize+'.intval($data['size'])
                    .',meta_unread=meta_unread+'.$is_read.',meta_unseen="'.$unseen.'"'
                    .' WHERE idx='.intval($folder);
            $this->execute($query);
            
            return $newID;
        }
        return false;
    }
        
	
}


?>