<?
class mailstream{
	var $docroot;
	
 public function __construct($docroot="")
    {
    	$this->docroot=$docroot;
    }
    	
	/**
    * Open a stream to given mail for file system operations
    * @param  int  ID of the mail
    * @param  string  Mode flag, see PHP function fopen() for possible values
    * @return  resource  Resource ID of the opened stream, false on failure
    */
	
    public function mail_open_stream($mail = 0, $flag = 'r')
    {
//        list ($folderpath, $filename) = $this->IDX->mail_get_real_location($this->uid, $mail);
//        $path = $this->docroot.'/'.$folderpath.'/'.$filename;
      	$_uid = Session::get("LOGIN_UID"); 
        $dao=D("WebMailEmail");
        $map='m.uid='.intval($_uid).' and m.idx='.intval($mail).' AND m.folder_id=f.idx';
        $row=$dao->where($map)->table("webmail_email m ,webmail_email_folders f")->field("f.folder_path,f.setid, m.uidl,m.folder_id")->find();
        
        // $daos=D("WebMailset");
        // $rows=$daos->where("f")
        //echo $dao->getlastsql();
        $path = $this->docroot.$row[setid]."/".$row[folder_path]."/".$row['uidl'].".in";
        $path = $this->docroot."/".$row['uidl'].".in";
        //echo "<br>";
        //echo $path."<br>";
        if (!file_exists($path)) return false;
        
        //$return = false;
        //$qid = $this->query('SELECT f.folder_path, m.uidl FROM '.$this->DB['tbl_mailindex'].' m, '.$this->DB['tbl_folders'].' f WHERE m.uid='.intval($uid).' and m.idx='.intval($mail).' AND m.folder_id=f.idx');
        //$return = $this->fetchrow($qid);
        //echo $path; 
        @chmod($path,"777");        
        $this->fh = fopen($path, $flag);
        $return=(!is_resource($this->fh) || !$this->fh) ? false : $this;
        //echo $return;
        return  $return;
    }

    /**
    * Set the file pointer of the current stream to a certain position to continue reading
    * from there
    * @param  int  Offset in bytes to point to afterwards
    * @return  bool  TRUE on success, FALSE otherwise
    * @since 0.1.6
    */
    public function mail_seek_stream($offset = 0)
    {
    	//echo $this->fh;
        if (!isset($this->fh) || !is_resource($this->fh)) return false;
        $ret = fseek($this->fh, $offset);
        return ($ret) ? true : false;
    }

    /**
    * Reads data from a mail file from the current position up to the number of bytes specified
    * Use mail_seek_stream() to set the file pointer to your desired start position
    * @param  int  Number of bytes to read, if set to 0, one line from the stream is returned
    * @return  mixed  String data read from mail stream on success, FALSE on EOF or failure
    * @since 0.1.6
    */
    public function mail_read_stream($length = 0)
    {
    
        if (!isset($this->fh) || !is_resource($this->fh)) return false;
        @set_magic_quotes_runtime(0);
        //exit;
        if (!$length) {
            if (false !== feof($this->fh)) {
                fclose($this->fh);
                unset($this->fh);
                return false;
            }
            $line = fgets($this->fh, 1024);
            //echo $line;
            //if ($line) {
              return $line;	
            //}
            
        }
        return fread($this->fh, $length);
    }

    /**
    * Closes a previously opened stream again
    * @param  void
    * @return  void
    * @since 0.2.2
    */
    public function mail_close_stream()
    {
        if (isset($this->fh) && is_resource($this->fh)) {
            fclose($this->fh);
            unset($this->fh);
        }
    }
    /**
     * Returns a given part form a given IMAP mail
     *
     * @param int $id
     * @param int $part
     * @return string
     */    
    public function get_imap_part($id, $part)
    {
        $mail = $this->get_mail_info($id, true);
        
        $folderdata = $this->IDX->get_folder_info($this->uid, $mail['folder_id']);
        $this->connect_imap($folderdata['folder_path'], true);
        return imap_fetchbody($this->imap[0]['handle'], $mail['ouidl'], $part, FT_UID | FT_PEEK);
    }
        
    
}

?>