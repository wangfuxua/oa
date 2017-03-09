<?

class WebMailEmailFoldersModel extends BaseModel {
	var $tableName="webmail_email_folders";

	
	/**
	 * 通过folder_path 得到 folder 的id
	 * */
	public function get_folder_id_from_path($uid = 0, $path, $roles = false,$setid=""){
		$dao=D("WebMailEmailFolders");
        if (!$roles) {
        	$map="uid=$uid AND folder_path='".$path."'";
        	if ($setid) {
        		$map.=" and setid='$setid'";
        	}
        }else {
        	$map="uid=$uid AND att_icon=':".$path."'";
        	if ($setid) {
        		$map.=" and setid='$setid'";
        	}
        }
		$row = $dao->where($map)->field("idx")->find();
		//echo $dao->getlastsql();
		return $row[idx];
	}
	/**
	 * 得到文件夹信息
	 * */
	public function get_folder_info($uid = 0, $folder = 0)
    {
    	$dao=D("WebMailEmailFolders");
        if (false === $uid) return false;
        if (false === $folder) return false;
        $folder = intval($folder);
        $uid = intval($uid);
        $row=$dao->where("uid=".$uid." AND idx=".$folder)->field('friendly_name foldername, childof, folder_path, att_type `type`, att_icon icon'
                .', att_big_icon big_icon, att_has_folders has_folders, att_has_items has_items'
                .', att_filter filter, att_settings settings, meta_mailnum mailnum'
                .', meta_mailsize mailsize, meta_unread unread, meta_unseen unseen')->find();
        //echo $dao->getLastSql();
        return $row;
        
        /*$query = 'SELECT friendly_name foldername, childof, folder_path, att_type `type`, att_icon icon'
                .', att_big_icon big_icon, att_has_folders has_folders, att_has_items has_items'
                .', att_filter filter, att_settings settings, meta_mailnum mailnum'
                .', meta_mailsize mailsize, meta_unread unread, meta_unseen unseen FROM '
                .$this->DB['tbl_folders'].' WHERE uid='.$uid.' AND idx='.$folder;
                
        return $this->fetchassoc($this->query($query));
        */
    }
    
    /**
     * 得到邮件大小
     * Qutoa related: Returns the overall size of all mails this user has stored in his
     * local folders (including the system folders, of course).
     * @param int $uid  User ID
     * @return int $size Size of all mails in bytes
     * @since 0.7.1
     */
    public function quota_getmailsize($uid = 0, $stats = false){
        if (false == $stats) {
        	$row=$this->where('uid='.intval($uid).' AND att_type<10')->field("sum(meta_mailsize) as size")->find();
        	return $row['size'];
            //$query = 'SELECT sum(meta_mailsize) FROM '.$this->DB['tbl_folders'].' WHERE uid='.intval($uid).' AND att_type<10';
            //list ($size) = $this->fetchrow($this->query($query));
            //return $size;
        }
        $row=$this->where('att_type<10')->field('count(distinct uid) as cnt, sum(meta_mailsize) as size')->find();
        if ($row[cnt]) {
            $rows=$this->where('att_type<10 GROUP BY uid ORDER BY moep DESC LIMIT 1')->field('uid, sum(meta_mailsize) moep')->find();                   	        $max_uid=$rows[uid];
            $max_cnt=$rows[moep];   
        }
        /*
        $query = 'SELECT count(distinct uid), sum(meta_mailsize) FROM '.$this->DB['tbl_folders'].' WHERE att_type<10';
        list ($cnt, $sum) = $this->fetchrow($this->query($query));
        if ($cnt) {
            $query = 'SELECT uid, sum(meta_mailsize) moep FROM '.$this->DB['tbl_folders'].' WHERE att_type<10 GROUP BY uid ORDER BY moep DESC LIMIT 1';
            list ($max_uid, $max_cnt) = $this->fetchrow($this->query($query));
        }
        */
        return array
                ('count' => isset($cnt) ? $cnt : 0
                ,'sum' => isset($sum) ? $sum : 0
                ,'max_uid' => isset($max_uid) ? $max_uid : 0
                ,'max_count' => isset($max_cnt) ? $max_cnt : 0
                );
    }
	/**
	 * 得到邮件的本地路径folder_path
	 * */
    public function get_folder_path($setid="",$idx="",$folderpath="",$docroot=""){
    	//echo "<br>";
    	//echo $setid;
    	if (!$setid) {
    		return false;
    	}
    	//echo $idx;
    	if (!$idx&&!$folderpath) {
    		
    		return false;
    	}
    	//echo $idx;
        if ($idx) {
        	
        	$daof=D("WebMailEmailFolders");
            $rowf=$daof->where("idx='$idx'")->field("folder_path")->find();
            $path=$docroot.$setid."/".$rowf[folder_path]."/";
        }elseif ($folderpath){
        	$path=$docroot.$setid."/".$folderpath."/";
        }
        //echo $path;
        return $path;
    	
    }
	
    public function mail_get_real_location($uid = 0, $mail = 0,$docroot="")
    {
    	
    	$_uid = Session::get("LOGIN_UID");
        $dao=D("WebMailEmail");
        $map='m.uid='.intval($_uid).' and m.idx='.intval($mail).' AND m.folder_id=f.idx';
        $row=$dao->where($map)->table("webmail_email m ,webmail_email_folders f")->field("f.folder_path, m.uidl")->find();
        //echo $dao->getlastsql();
        $path = $this->docroot.$row['uidl'].".in";
        return $path;
        
        /*  	
        $return = false;
        $qid = $this->query('SELECT f.folder_path, m.uidl FROM '.$this->DB['tbl_mailindex'].' m, '.$this->DB['tbl_folders'].' f WHERE m.uid='.intval($uid).' and m.idx='.intval($mail).' AND m.folder_id=f.idx');
        $return = $this->fetchrow($qid);
        return $return;
        */
    }
        
}


?>