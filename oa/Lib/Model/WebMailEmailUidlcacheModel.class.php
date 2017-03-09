<?
class WebMailEmailUidlcacheModel extends BaseModel {
	var $tableName="webmail_email_uidlcache";
	
	/**
     * 匹配当前所收取的邮件和本地缓存的邮件 
     * @param int $setid  邮件id
     * @param array 要匹配的邮件列表
     * [@param array 本地邮件列表]
     * @return array 匹配后的结果:
     * [0] -> new elements
     * [1] -> no longer existant elements
     * @since 0.7.2
     */
    public function uidlcache_match($setid, $maillist, $dblist = false)
    {
    	//echo "e";
        $setid = intval($setid);
        $nlist = array();
        $olist = array();
        $cached = array();
        $q_r = '';
        $dao=D("WebMailEmailUidlcache");
        if (false !== $dblist) {
            $cached = &$dblist;
        } else {
           //$dao->create();
        	$list=$dao->where("setid=$setid")->field("uidl")->findAll();
        	//echo $dao->getlastsql();
        	//print_rr($list);
        	foreach ($list as $key=>$row){
        		$cached[] = get_magic_quotes_runtime() ? stripslashes($row[uidl]) : $row[uidl];
        	}
        }
        $olist = array_diff($cached, $maillist);
        $nlist = array_diff($maillist, $cached);
        // Drop items from the cache table, which do no longer exist on the mailserver
        if (false === $dblist && !empty($olist)) {
            foreach ($olist as $uidl) {
                if ($q_r) $q_r .= ',';
                $q_r .= $uidl;
            }
            $dao->where("setid=$setid and uidl in (".$q_r.")")->delete();
            //echo $dao->getlastsql();
            //$this->query('DELETE FROM '.$this->DB['tbl_uidlcache'].' WHERE setid='.$setid.' AND uidl in ('.$q_r.')');
        }
        return array($nlist, $olist);
    }
    
	
    /**
     * Used for profile, where mails deleted locally shall get deleted on the POP3 server, too.
     *
     * @param int $setid
     * @param string $item
     * @return true
     * @since 0.7.3
     */
    public function uidlcache_markdeleted($setid, $item)
    {   
    	$dao=D("WebMailSet");
    	$row=$dao->where('setid='.intval($setid))->field('localkillserver')->find();
    	if (!$row[localkillserver]) {
            return true;     		
    	}
        $dao=D("WebMailEmailUidlcache");
        //$dao->create();
    	return $dao->setField("deleted","1","setid=$setid and uidl=".$this->escape($item));
    	
        //list ($delonserver) = $this->fetchrow($this->query('SELECT localkillserver FROM '.$this->DB['tbl_profiles'].' WHERE id='.intval($setid)));
        //if (!$delonserver) return true;
        //return $this->query('UPDATE '.$this->DB['tbl_uidlcache'].' SET deleted="1" WHERE profile='.intval($setid).' AND uidl='.$this->escape($item));
    }
    
    /**
     * Adds an item to the UIDL cache
     *
     * @param int $profile
     * @param string $item
     * @return bool
     * @since 0.7.2
     */
    public function uidlcache_additem($profile, $item)
    {
        $profile = intval($profile);
        $data[setid]=$profile;
        $data[uidl]=$item;
        //print_r($data);
        $dao=D("WebMailEmailUidlcache");
        $dao->create();
        $id=$dao->add($data);
        //echo $dao->getLastSql();
        return $id;
        
        
        //return $this->query('INSERT '.$this->DB['tbl_uidlcache'].' SET profile='.$profile.', uidl='.$this->escape($item));
    }
        
        
    
}
?>