<?php
/* ------------------------------------------------------------------------- */
/* drivers/mysql/driver.php - phlyMail Yokohama 3.6                          */
/* Proivdes storage functions for use with a mySQL database                  */
/* (c) 2002-2007 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v3.9.9pl3mod1                                                             */
/* ------------------------------------------------------------------------- */

class driver {
    // This holds all config options
    var $DB;

    /**
     * Constructor
     * Read the config and open the DB
     *
     * @param string $Conf Path to the config file
     * @param bool Use secure mail account passwords; if false, these are considedered plain text
     * @return object
     */
    /*
    public function __construct($Conf, $secaccpass = true)
    {
        // Initialise database driver choices
        if (file_exists($Conf)) {
            foreach(file($Conf) as $l) {
                if ($l{0} == '#') continue;
                if (substr($l, 0, 15) == '<?php die(); ?>') continue;
                list($k, $v) = explode(';;', $l); $this->DB[$k] = trim($v);
            }
            unset($l, $k, $v, $Conf);

            $this->DB['secaccpass'] = $secaccpass;

            $this->DB['db_pref'] = '`'.$this->DB['mysql_database'].'`.'.$this->DB['mysql_prefix'].'_';

            $this->DB['tbl_user']            = $this->DB['db_pref'].'user';
            $this->DB['tbl_user_accounting'] = $this->DB['db_pref'].'user_accounting';
            $this->DB['tbl_user_quota']      = $this->DB['db_pref'].'user_quota';
            $this->DB['tbl_user_smslogging'] = $this->DB['db_pref'].'user_smslogging';
            $this->DB['tbl_mailboxes']       = $this->DB['db_pref'].'mailboxes';
            $this->DB['tbl_profiles']        = $this->DB['db_pref'].'profiles';
            $this->DB['tbl_signatures']      = $this->DB['db_pref'].'signatures';
            $this->DB['tbl_sendto_handler']  = $this->DB['db_pref'].'sendto_handler';
            $this->DB['tbl_aliases']         = $this->DB['db_pref'].'profile_alias';
            $this->DB['tbl_adb_adr']         = $this->DB['db_pref'].'adb_adr';
            $this->DB['tbl_adb_books']       = $this->DB['db_pref'].'adb_books';
            $this->DB['tbl_adb_grp']         = $this->DB['db_pref'].'adb_group';

            // Open Database connection
            $dbh = mysql_connect($this->DB['mysql_host'], $this->DB['mysql_user'], $this->DB['mysql_pass']);
            if (isset($dbh)) {
                $this->dbh = $dbh;
                return true;
            }
        }
        return false;
    }
    */
    
	public function __construct(){
		return true;
	}
	
    // Close the DB connection
    // Input  : void
    // Returns: $return    TRUE on success, FALSE otherwise
    public function close()
    {
        return @mysql_close($this->dbh);
    }

    // Check whether a username:password combination matches a valid user of the system
    // Input  : authenticate(string user name)
    // Returns: $return     array data on success, FALSE otherwise
    //          $return[0] uid
    //          $return[1] MD5 hash of user's password
    public function authenticate($un = '')
    {
        return mysql_fetch_row($this->query('SELECT 1,password,externalemail FROM '.$this->DB['tbl_user'].' WHERE username="'
                .$this->escape($un).'" AND active="1"'));
    }

    // Get POP3 connection data of a certain user
    // Input  : get_popconnect(integer user id, string user name, integer account number)
    // Returns: $return    array data on success, FALSE otherwise
    //          $return['popserver']  string POP3 server
    //          $return['popport']    string POP3 port
    //          $return['popuser']    string POP3 user name
    //          $return['poppass']    string POP3 password
    //          $return['popnoapop']  use APOP, where 1 means no, 0 auto
    public function get_popconnect($uid = 0, $username = '', $accid = 0, $real_id = 0)
    {
        $q_r = ($real_id) ? '`id`='.intval($real_id) : 'uid='.intval($uid).' AND a.accid='.intval($accid);
        $return = mysql_fetch_assoc($this->query
                ('SELECT a.accname,a.popserver,a.popport,a.popuser,a.poppass,a.popnoapop,a.popsec,a.inbox,a.sent,a.drafts'
                .',a.junk,a.waste,a.templates,a.onlysubscribed, a.imapprefix FROM '
                .$this->DB['tbl_profiles'].' a WHERE '.$q_r
                ));
        if ($this->DB['secaccpass']) $return['poppass'] = $this->deconfuse($return['poppass'], $return['popserver'].$return['popport'].$return['popuser']);
        return $return;
    }

    // Get SMTP connection data of a certain user
    // Input  : get_smtpconnect(integer user id, string user name, integer account number)
    // Returns: $return    array data on success, FALSE otherwise
    //          $return['smtpserver']   string SMTP server
    //          $return['smtpport']     string SMTP port
    //          $return['smtpuser']     string SMTP user name
    //          $return['smtppass']     string SMTP password
    //          $return['smtpafterpop'] do SMTP-after-POP, where 1 means yes, 0 means no
    public function get_smtpconnect($uid = 0, $username = '', $accid = 0, $real_id = 0)
    {
        $q_r = ($real_id) ? '`id`='.intval($real_id) : 'uid='.intval($uid).' AND a.accid='.intval($accid);
        $return = mysql_fetch_assoc($this->query
                ('SELECT a.accname,a.smtpserver,a.smtpport,a.smtpuser,a.smtppass,a.smtpafterpop,a.smtpsec,a.userheaders FROM '
                .$this->DB['tbl_profiles'].' a WHERE '.$q_r
                ));
        if ($this->DB['secaccpass']) $return['smtppass'] = $this->deconfuse($return['smtppass'], $return['smtpserver'].$return['smtpport'].$return['smtpuser']);
        $return['userheaders'] = unserialize($return['userheaders']);
        return $return;
    }

    // Return the basic user data for an user ID
    // Input  : get_usrdata(integer user id)
    // Returns: $return    array data on success, FALSE otherwise
    //          $return['accname'][id]  Display name of the account(s)
    public function get_usrdata($uid = 0)
    {
        return mysql_fetch_assoc($this->query
                ('SELECT 1,username,externalemail,active,unix_timestamp(logintime) as login_time, '
                .'unix_timestamp(logouttime) as logout_time FROM '.$this->DB['tbl_user'].' LIMIT 1'));
    }

    // Returns failure count and timestamp for an user ID
    // Input  : get_usrfail(integer user id)
    // Returns: $return    array data on success, FALSE otherwise
    //          $return['fail_count']  Number of failures
    //          $return['fail_time']   Timestamp of last fail
    public function get_usrfail($uid = false)
    {
        return @mysql_fetch_assoc($this->query('SELECT fail_count,fail_time FROM '.$this->DB['tbl_user'].' LIMIT 1'));
    }

    // Set failure count of a certain user
    // Input  : set_usrfail(integer user id)
    // Returns: $return    boolean, TRUE on success, FALSE otherwise
    public function set_usrfail($uid = false)
    {
        return $this->query('UPDATE '.$this->DB['tbl_user'].' set fail_count=fail_count+1, fail_time=unix_timestamp()');
    }

    // Reset failure count ( == set to 0) of a certain user
    // Input  : reset_usrfail(integer user id)
    // Returns: $return    boolean, TRUE on success, FALSE otherwise
    public function reset_usrfail($uid = false)
    {
        return $this->query('UPDATE '.$this->DB['tbl_user'].' set fail_count=0, fail_time=0');
    }

    // Set login timestamp of a specific user
    // Input : set_logintime(integer user id)
    // Return: void
    public function set_logintime($uid = false)
    {
        return $this->query('UPDATE '.$this->DB['tbl_user'].' set logintime=NOW()');
    }

    // Set logout timestamp of a specific user
    // Input : set_logouttime(integer user id)
    // Return: void
    public function set_logouttime($uid = false)
    {
        return $this->query('UPDATE '.$this->DB['tbl_user'].' set logouttime=NOW()');
    }

    // Set login timestamp of a specific account of a user
    // Input : set_poplogintime(integer user id, integer account id)
    // Return: void
    public function set_poplogintime($uid = false, $accid = false, $pid = false)
    {
        if (!$accid && !$pid) return;
        $q_r = ($accid) ? ' AND accid='.intval($accid) : ' AND `id`='.intval($pid);
        return $this->query('UPDATE '.$this->DB['tbl_profiles'].' set logintime=NOW()'.$q_r);
    }

    // Get index for all accounts of a certain user
    // Input  : get_accidx(integer user id, string user name)
    // Returns: $return      array data on success, FALSE otherwise
    //          $return[id]  Display name of the account(s)
    public function get_accidx($uid = 0, $username = '', $extended = false, $protocol = false)
    {
        $return = array();
        $q_r = ($protocol) ? ' AND acctype="'.$this->escape($protocol).'"' : '';
        $qid = mysql_query('SELECT `id`, accid, accname, acctype FROM '.$this->DB['tbl_profiles'].' WHERE 1'.$q_r);
        if ($qid) {
            while(list($id, $accid, $accname, $acctype) = mysql_fetch_row($qid)) {
            	if ($extended) {
                	$return[$id] = array('accid' => $accid, 'accname' => $accname, 'acctype' => $acctype);
            	} else {
            		$return[$accid] = $accname;
            	}
            }
        }
        return $return;
    }


    /**
    * Returns the ID of the profile a certain email address matches against
    * @param  int  User ID to query the DB for
    * @param  string  Email address to find the profile for
    * @return  int  ID of the profile or FALSE on no match
    * @since 3.6.2
    */
    public function get_profile_from_email($uid = 0, $email = '')
    {
        if (!$email) return array(0, 0);
        $query = 'SELECT accid, 0 FROM '.$this->DB['tbl_profiles'].' WHERE uid='.intval($uid).' AND address="'.$this->escape($email).'"';
        list ($return[0], $return[1]) = mysql_fetch_row(mysql_query($query));
        if (!$return[0]) {
            $query = 'SELECT profile, aid FROM '.$this->DB['tbl_aliases'].' WHERE uid='.intval($uid).' AND email="'.$this->escape($email).'"';
            list ($return[0], $return[1]) = mysql_fetch_row(mysql_query($query));
        }
        return $return;
    }

    /**
     * Returns the real DB index for a given "account id" for the given user
     *
     * @param int $uid
     * @param int $accid
     * @return int
     * @since 3.7.1
     */
    public function get_profile_from_accid($uid, $accid)
    {
        list ($return) = mysql_fetch_row($this->query('SELECT `id` FROM '.$this->DB['tbl_profiles'].' WHERE uid='.intval($uid).' AND accid='.intval($accid)));
        return $return;
    }

    /**
    * Tries to determine a default email address for a user, if none is defined, any email address is returned
    * @param  int  user ID to query the DB for
    * @param  ref  _PM_ structure
    * @return string  Email address on success, false on failure (aka: no profiles yet)
    * @since  3.6.4
    */
    public function get_default_email($uid, &$settings)
    {
        $return = false;
        if (isset($settings['core']['default_profile']) && $settings['core']['default_profile']) {
            $query = 'SELECT address FROM '.$this->DB['tbl_profiles'].' WHERE AND accid='.intval($settings['core']['default_profile']);
            list ($return) = mysql_fetch_row(mysql_query($query));
        }
        if (!$return) {
            $query = 'SELECT address FROM '.$this->DB['tbl_profiles'].' LIMIT 1';
            list ($return) = mysql_fetch_row(mysql_query($query));
        }
        return $return;
    }

    // Get the highest account id in use for a specific user
    // Input  : get_maxaccid(integer user id)
    // Returns: integer next possible profile id
    public function get_maxaccid($uid = 0)
    {
        if (!$uid) return 1;
        list ($curr) = mysql_fetch_row($this->query('SELECT max(accid) FROM '.$this->DB['tbl_profiles']));
        return ($curr+1);
    }

    /**
    * Get personal data of a certain user
    * @param integer user id
    * @param string user name
    * @param integer account number
    * @return array data on success, FALSE otherwise; The erray contains
    * - sig_on     integer is the signature active?
    * - real_name  string real name of the user
    * - address    string email address to use for sending
    * - signature  blob signature
    * - aliases  array (aid => int, real_name => string, email => string)
    */
    public function get_accdata($uid = 0, $username = '', $accid = 0, $real_id = 0)
    {
        $q_r = ($real_id) ? '`id`='.intval($real_id) : 'a.accid='.intval($accid);
        $return = mysql_fetch_assoc($this->query('SELECT acctype,accid,1 uid,sig_on,real_name,checkevery,be_checkevery,leaveonserver,localkillserver,cachetype'
                .',checkspam,inbox,sent,drafts,waste,junk,templates,unix_timestamp(logintime) logintime,address,signature,onlysubscribed'
                .',imapprefix,userheaders FROM '.$this->DB['tbl_profiles'].' a WHERE '.$q_r));
        $return['userheaders'] = ($return['userheaders']) ? unserialize($return['userheaders']) : array();
        $return['aliases'] = array();
        if (!isset($return['uid'])) return $return;
        $qid = $this->query('SELECT aid, real_name, email FROM '.$this->DB['tbl_aliases'].' WHERE profile='.$return['accid']);
        while (list ($aid, $rname, $mail) = mysql_fetch_row($qid)) {
            $return['aliases'][$aid] = array('real_name' => $rname, 'email' => $mail);
        }
        return $return;
    }

    // Check, if a given username (already) exists in the database
    // Input  : checkfor_username(string username)
    // Returns: TRUE if exists, FALSE otherwise
    public function checkfor_username($username = '', $giveuid = false)
    {
        $qid = $this->query('SELECT 1 FROM '.$this->DB['tbl_user'].' WHERE username="'.mysql_escape_string($username).'"');
        if (!mysql_num_rows($qid)) return false;
        return (!$giveuid) ? true : 1;
    }

    // Returns the mailboxes, a certain user has defined in the MySQL DB
    //@param  int  user id
    //@return  array  Array data; key is the ID of the mailbox, value is an array with the type and the name of the mailbox
    //@since 0.4.7
    public function get_user_configured_mailboxes($uid)
    {
        $return = array();
        $qid = $this->query('SELECT idx, type, name FROM '.$this->DB['tbl_mailboxes']);
        while ($line = mysql_fetch_assoc($qid)) {
            $return[$line['idx']] = array('type' => $line['type'], 'name' => $line['name']);
        }
        return $return;
    }

    // Insert a new user into the database
    // Input  : $input  array containing user data
    //          $input['username']       Login name
    //          $input['password']       Password
    //          $input['externalemail']  Email address for notifications
    //          $input['active']         '0' for no, '1' for yes
    // Returns: $return  UserID of created user on success, FALSE otherwise
    public function add_user($input)
    {
 	    list ($is_drin) = mysql_fetch_row($this->query('SELECT uid FROM '.$this->DB['tbl_user']));
 	    if ($is_drin) return false;
        if ($this->query('INSERT '.$this->DB['tbl_user'].' (username,password,externalemail,active,choices) values ("'
                .$this->escape($input['username']).'",md5("'.$this->escape($input['password'])
                .'"),"'.$this->escape($input['externalemail']).'","'.$this->escape($input['active']).'", "")')) {
            return mysql_insert_id($this->dbh);
        }
        return false;
    }

    /**
    * Adds a "mailbox" - a handler anchor, that is to the DB, thus activating such an anchor for a user
    * @param  int  user id
    * @param   string  type of the mailbox
    * @param  string  Userfriendly name of the handler anchor
    * @return   boolean  TRUE on success, FALSE on failure
    */
    public function user_add_mailbox($uid, $type, $name)
    {
        if ($this->query('INSERT '.$this->DB['tbl_mailboxes'].' (uid, type, name) values (1, "'.$this->escape($type).'", "'.$this->escape($name).'")')) {
           return mysql_insert_id($this->dbh);
        }
        return false;
    }

    /**
    * Remove a certain "mailbox" (handler anchor) again.
    * @param int id ID of the mailbox
    * @return boolean  TRUE on success, FALSE otherwise
    */
    public function user_remove_mailbox($id)
    {
        return ($this->query('DELETE FROM '.$this->DB['tbl_mailboxes'].' WHERE idx='.intval($id))
                && $this->query('ALTER TABLE '.$this->DB['tbl_mailboxes'].' ORDER BY idx'));
    }

    /**
    * Get the ID(s) of a mailbox of a certain type for a certain user id
    * @param  int  ID of the user
    * @param  string  type of the mailbox
    * @return  array  flat list of the IDs found for that user and mailbox type
    * @since 3.5.8
    */
    public function user_get_mailboxid($uid, $type)
    {
        $return = array();
        $query = 'SELECT idx FROM '.$this->DB['tbl_mailboxes'].' WHERE type="'.$this->escape($type).'"';
        $qid = $this->query($query);
        while (list ($id) = mysql_fetch_row($qid)) { $return[] = $id; }
        return $return;
    }

    // Insert new account for an user into the database
    // Input  : $input  array containing user data
    //          $input['uid']          ID of the user
    //          $input['accid']        ID of the account
    //          $input['accname']      display name of the account
    //          $input['sig_on']       '0' for inactive, '1' for active
    //          $input['popserver']    name or IP of the POP3 server
    //          $input['popport']      port number of the POP3 server
    //          $input['popuser']      user name for the POP3 account
    //          $input['poppass']      password for the POP3 account
    //          $input['popnoapop']    APOP: '0' for usee, '1' for do not use it
    //          $input['smtpserver']   name or IP of the SMTP server
    //          $input['smtpport']     port number of the SMTP server
    //          $input['smtpuser']     user name for the SMTP account
    //          $input['smtppass']     password for the SMTP account
    //          $input['smtpafterpop'] do SMTP-after-POP, where 1 means yes, 0 means no
    //          $input['real_name']    real name of the POP3 user for sending mails
    //          $input['address']      email address of the POP3 user for sending mails
    //          $input['signature']    signature of the POP3 user for sending mails
    // Returns: $return  Record ID of created account on success, FALSE otherwise
    public function add_account($input)
    {
        list($input['accid']) = mysql_fetch_row($this->query('SELECT max(accid)+1 FROM '.$this->DB['tbl_profiles'].' WHERE uid=1'));
        if ($input['accid'] == 0) $input['accid'] = 1;

        if ($this->DB['secaccpass']) $input['poppass'] = $this->confuse($input['poppass'], $input['popserver'].$input['popport'].$input['popuser']);
        if ($this->DB['secaccpass']) $input['smtppass'] = $this->confuse($input['smtppass'], $input['smtpserver'].$input['smtpport'].$input['smtpuser']);
        $input['userheaders'] = isset($input['userheaders']) ? serialize($input['userheaders']) : '';

        if ($this->query('INSERT '.$this->DB['tbl_profiles']
                .' (uid,accid,acctype,accname,sig_on,popserver,popport,popuser,poppass,popnoapop,popsec'
                .',smtpserver,smtpport,smtpuser,smtppass,smtpafterpop,smtpsec,real_name,address,signature'
                .',leaveonserver,localkillserver,cachetype,checkspam,checkevery,be_checkevery,inbox,sent,drafts,waste,junk,templates'
                .',onlysubscribed,imapprefix,userheaders) values ("'
                .intval($input['uid']).'","'.intval($input['accid']).'","'.$this->escape($input['acctype']).'","'
                .$this->escape($input['accname']).'","'
                .$this->escape($input['sig_on']).'","'.$this->escape($input['popserver']).'","'
                .intval($input['popport']).'","'.$this->escape($input['popuser']).'","'
                .$this->escape($input['poppass']).'","'.$this->escape($input['popnoapop']).'","'.$this->escape($input['popsec']).'","'
                .$this->escape($input['smtpserver']).'","'.intval($input['smtpport']).'","'
                .$this->escape($input['smtpuser']).'","'.$this->escape($input['smtppass']).'","'
                .$this->escape($input['smtpafterpop']).'","'.$this->escape($input['smtpsec']).'","'.$this->escape($input['real_name']).'","'
                .$this->escape($input['address']).'","'.intval($input['signature']).'","'
                .$this->escape($input['leaveonserver']).'","'.$this->escape($input['localkillserver']).'","'
                .(isset($input['cachetype']) ? $this->escape($input['cachetype']) : 'full').'","'
                .$this->escape($input['checkspam']).'","'.intval($input['checkevery']).'","'
                .intval($input['be_checkevery']).'",'.intval($input['inbox']).','
                .(isset($input['sent']) ? intval($input['sent']) : '0').','
                .(isset($input['drafts']) ? intval($input['drafts']) : '0').','
                .(isset($input['waste']) ? intval($input['waste']) : '0').','
                .(isset($input['junk']) ? intval($input['junk']) : '0').','
                .(isset($input['templates']) ? intval($input['templates']) : '0').','
                .(isset($input['onlysubscribed']) ? intval($input['onlysubscribed']) : '"0"').',"'
                .(isset($input['imapprefix']) ? $this->escape($input['imapprefix']) : '').'", "'
                .$this->escape($input['userheaders']).'")'
                )) {
            return $input['accid'];
        }
        return false;
    }

    // Update the record of a user in the database
    // Input  : $input  array containing user data
    //          $input['username']       Login name
    //          $input['password']       Password (Omit if unchanged)
    //          $input['externalemail']  Email address for notifications
    //          $input['active']         '0' for no, '1' for yes (Omit if unchanged)
    // Returns: $return  TRUE on success, FALSE otherwise
    public function upd_user($input)
    {
        $query = 'UPDATE '.$this->DB['tbl_user'].' SET username="'.$this->escape($input['username'])
                .'",externalemail="'.$this->escape($input['externalemail']).'"';
        if (isset($input['password']) && $input['password']) $query .= ',password=md5("'.$this->escape($input['password']).'")';
        if (isset($input['active']) && $input['active'] != '') $query .= ',active="'.$this->escape($input['active']).'"';
        return $this->query($query);
    }

    // Update the record of an account for an user into the database
    // Input  : $input  array containing user data
    // Returns: $return  TRUE on success, FALSE otherwise
    public function upd_account($input)
    {
        if (isset($input['userheaders'])) $input['userheaders'] = serialize($input['userheaders']);
        $query = 'UPDATE '.$this->DB['tbl_profiles'].' SET ';
        foreach (array
                ('accname', 'acctype', 'sig_on', 'popserver', 'popport', 'popuser', 'popnoapop', 'popsec'
                ,'smtpserver', 'smtpport', 'smtpuser', 'smtpafterpop', 'smtpsec'
                ,'real_name', 'address', 'signature', 'leaveonserver', 'localkillserver', 'cachetype'
                ,'checkspam', 'checkevery', 'be_checkevery', 'inbox', 'sent', 'drafts', 'waste', 'junk', 'templates'
                ,'onlysubscribed' ,'imapprefix', 'userheaders') as $field) {
            if (!isset($input[$field])) continue;
            $query.= $field.'="'.$this->escape($input[$field]).'", ';
        }
        if ($input['poppass'] != false) {
            if ($this->DB['secaccpass']) $input['poppass'] = $this->confuse($input['poppass'], $input['popserver'].$input['popport'].$input['popuser']);
            $query .= 'poppass="'.$this->escape($input['poppass']).'", ';
        }
        if ($input['smtppass'] != false) {
            if ($this->DB['secaccpass']) $input['smtppass'] = $this->confuse($input['smtppass'],
                                                $input['smtpserver'].$input['smtpport'].$input['smtpuser']);
            $query .= 'smtppass="'.$this->escape($input['smtppass']).'", ';
        }
        $query .= 'logintime=logintime WHERE uid=1 AND accid='.intval($input['accid']);
        return ($this->query($query)) ? true : false;
    }

    // Delete a user and his/her accounts from the database
    // Input  : $username  username of the user to be deleted
    // Returns: $return    TRUE on success, FALSE otherwise
    public function delete_user($un)
    {
        if ($this->query('TRUNCATE TABLE '.$this->DB['tbl_user'])) {
            $this->query('TRUNCATE TABLE '.$this->DB['tbl_profiles']);
            $this->query('TRUNCATE TABLE '.$this->DB['tbl_signtaures']);
            $this->query('TRUNCATE TABLE '.$this->DB['tbl_aliases']);
            return true;
        }
        return false;
    }

    // Delete an account of a given user from database
    // Input:  delete_account(string username, integer accountID)
    // Return: TRUE on success, FALSE otherwise
    public function delete_account($un = '', $accountnumber = '')
    {
        if ($this->query('DELETE FROM '.$this->DB['tbl_profiles'].' WHERE accid='.intval($accountnumber))) {
            $this->query('ALTER TABLE '.$this->DB['tbl_profiles'].' AUTO_INCREMENT=0');
            return true;
        }
        return false;
    }

    /**
    * Add an alias (alternative email address plus real name) for a user and profile
    * @param  int  User ID to add the alias for
    * @param  int  profile ID to add the alias to
    * @param  string  Email address
    * @param  string  Real name (might be empty)
    * @return  bool  TRUE if successfull, FALSE on failures
    * @since 3.6.2
    */
    public function add_alias($uid = 0, $pid = 0, $email = '', $realname = '')
    {
        if (!$email) return false;
        $query = 'INSERT '.$this->DB['tbl_aliases'].' SET uid=1, profile='.intval($pid)
                .', email="'.$this->escape($email).'", real_name="'.$this->escape($realname).'"';
        return mysql_query($query);
    }

    /**
    * Update an alias
    * @param  int  User ID to update the alias for
    * @param  int  alias ID to update
    * @param  string  Email address
    * @param  string  Real name (might be empty)
    * @return  bool  TRUE if successfull, FALSE on failures
    * @since 3.6.2
    */
    public function update_alias($uid = 0, $aid = 0, $email = '', $realname = '')
    {
        if (!$email) return;
        $query = 'UPDATE '.$this->DB['tbl_aliases'].' SET email="'.$this->escape($email)
                .'", real_name="'.$this->escape($realname).'" WHERE uid=1 AND aid='.intval($aid);
        return mysql_query($query);
    }

    /**
    * Delete an alias again
    * @param  int  User ID to delete the alias for
    * @param  int  alias ID to delete
    * @return  bool  TRUE if successfull, FALSE on failures
    * @since 3.6.2
    */
    public function delete_alias($uid, $aid)
    {
        return mysql_query('DELETE FROM '.$this->DB['tbl_aliases'].' WHERE uid=1 AND aid='.intval($aid));
    }

    /**
    * Add an userheader (user defined mail header) for a user and profile
    * @param  int  User ID to add the userheader for
    * @param  int  profile ID to add the userheader to
    * @param  string  Header name
    * @param  string  Header value
    * @return  bool  TRUE if successfull, FALSE on failures
    * @since 3.8.2
    */
    public function add_uhead($uid = 0, $pid = 0, $hkey = '', $hval = '')
    {
        if (!$hkey) return false;
        list ($uheads) = mysql_fetch_row($this->query('SELECT userheaders FROM '.$this->DB['tbl_profiles'].' WHERE uid=1 AND accid='.intval($pid)));
        $uheads = ($uheads) ? unserialize($uheads) : array();
        $uheads[$hkey] = $hval;
        $uheads = serialize($uheads);
        $query = 'UPDATE '.$this->DB['tbl_profiles'].' SET userheaders="'.$this->escape($uheads).'" WHERE uid=1 AND accid='.intval($pid);
        return mysql_query($query);
    }

    /**
    * Update an userheader
    * @param  int  User ID to update the userheader for
    * @param  int  userheader ID to update
    * @param  string  Header name to replace
    * @param  string  Header name (new value)
    * @param  string  Header value
    * @return  bool  TRUE if successfull, FALSE on failures
    * @since 3.8.2
    */
    public function update_uhead($uid = 0, $pid = 0, $oldkey = '', $hkey = '', $hval = '')
    {
        if (!$hkey) return;
        list ($uheads) = mysql_fetch_row($this->query('SELECT userheaders FROM '.$this->DB['tbl_profiles'].' WHERE uid=1 AND accid='.intval($pid)));
        $uheads = ($uheads) ? unserialize($uheads) : array();
        if ($hkey != $oldkey) unset($uheads[$oldkey]);
        $uheads[$hkey] = $hval;
        $uheads = serialize($uheads);
        $query = 'UPDATE '.$this->DB['tbl_profiles'].' SET userheaders="'.$this->escape($uheads).'" WHERE uid=1 AND accid='.intval($pid);
        return mysql_query($query);
    }

    /**
    * Delete an user defined mail header again
    * @param  int  User ID to delete the userheader for
    * @param  string  Header name
    * @return  bool  TRUE if successfull, FALSE on failures
    * @since 3.8.2
    */
    public function delete_uhead($uid = 0, $pid = 0, $hkey)
    {
        if (!$hkey) return;
        list ($uheads) = mysql_fetch_row($this->query('SELECT userheaders FROM '.$this->DB['tbl_profiles'].' WHERE uid=1 AND accid='.intval($pid)));
        $uheads = ($uheads) ? unserialize($uheads) : array();
        unset($uheads[$hkey]);
        $uheads = serialize($uheads);
        $query = 'UPDATE '.$this->DB['tbl_profiles'].' SET userheaders="'.$this->escape($uheads).'" WHERE uid=1 AND accid='.intval($pid);
        return mysql_query($query);
    }

    /**
     * Add a signature
     *
     * @param integer $uid
     * @param string $title
     * @param string $signature
     * @return bool
     */
    public function add_signature($uid = 0, $title = '', $signature = '')
    {
        if (!$uid || !$signature) return;
        return $this->query('INSERT '.$this->DB['tbl_signatures'].' SET uid=1, title="'.$this->escape($title).'", signature="'.$this->escape($signature).'"');
    }

    /**
     * Update a signature
     *
     * @param integer $uid
     * @param integer $id
     * @param string $title
     * @param string $signature
     * @return bool
     */
    public function update_signature($uid = 0, $id = 0, $title = '', $signature = '')
    {
        if (!$uid || ! $id || !$signature) return;
        return $this->query('UPDATE '.$this->DB['tbl_signatures'].' SET title="'.$this->escape($title).'", signature="'.$this->escape($signature).'" WHERE id='.intval($id).' AND uid=1');
    }

    /**
     * Delete a signature
     *
     * @param int $uid
     * @param int $id
     * @return bool
     */
    public function delete_signature($uid = 0, $id = 0)
    {
        return ($this->query('DELETE FROM '.$this->DB['tbl_signatures'].' WHERE id='.intval($id).' AND uid=1')
                && $this->query('ALTER TABLE '.$this->DB['tbl_signatures'].' ORDER BY id ASC'));
    }

    /**
     * Return list of signatures defined for a specific user id
     *
     * @param int $uid
     * @return array
     */
    public function get_signature_list($uid)
    {
        $return = array();
        $qid = $this->query('SELECT id, title, signature FROM '.$this->DB['tbl_signatures']);
        while ($sig = mysql_fetch_assoc($qid)) {
            $return[$sig['id']] = array('title' => $sig['title'], 'signature' => $sig['signature']);
        }
        return $return;
    }

    /**
     * Get a specific signature
     *
     * @param int $uid
     * @param int $id
     * @return array
     */
    public function get_signature($uid, $id)
    {
        list ($title, $signature) = mysql_fetch_row($this->query('SELECT title, signature FROM '.$this->DB['tbl_signatures'].' WHERE id='.intval($id).' AND uid=1'));
        return array('title' => $title, 'signature' => $signature);
    }

    // Switch activity status of a user
    // Input:  onoff_user(string username, integer status) status[0|1]
    // Return: TRUE on success, FALSE otherwise
    public function onoff_user($username, $active)
    {
        if ($this->query('UPDATE '.$this->DB['tbl_user'].' SET active="'.mysql_escape_string($active).'"')) {
            return true;
        }
        return false;
    }

    // Check, if a given username (already) exists in the database
    // Input  : checkfor_username(string username)
    // Returns: Account ID if exists, FALSE otherwise
    public function checkfor_accname($un, $accname = '')
    {
        list ($exists) = mysql_fetch_row($this->query('SELECT accid FROM '.$this->DB['tbl_profiles'].' WHERE accname="'.mysql_escape_string($accname).'"'));
        return ($exists) ? $exists : false;
    }

    // Get index for all users
    // Input  : get_usridx(integer user id, [string pattern [,string criteria [,integer num [,integer start]]]])
    // Returns: $return    array data on success, FALSE otherwise
    // If a search pattern is given, only usernames containing it will be returned;
    // the pattern may contain '*' or '%' as wildcards
    // If the num (number of users) and optionally the start values are given, only the search results
    // within this range are returned
    public function get_usridx($uid = 0, $pattern = '', $criteria = '', $num = 0, $start = 0)
    {
        $return = array();
        $q_l = 'SELECT uid,username FROM '.$this->DB['tbl_user'].' WHERE 1';
        $pattern = addslashes($pattern);
        if (strlen($pattern) > 0) {
            $pattern = str_replace('*', '%', mysql_escape_string($pattern)); $q_l.=' AND username LIKE "'.$pattern.'"';
        }
        switch($criteria) {
            case 'inactive': $q_l .= ' AND active="0"';  break;
            case 'active':   $q_l .= ' AND active="1"';  break;
            case 'locked':   $q_l .= ' AND fail_count>='.intval($GLOBALS['_PM_']['auth']['countonfail']);  break;
        }
        $q_r = ' LIMIT 1';
        $qid = $this->query($q_l.$q_r);
        while (list ($uid, $username) = mysql_fetch_row($qid)) $return[$uid] = $username;
        return $return;
    }

    // Get numbers of users, acitve users, inactive users, locked users
    // Input  : get_usroverview(integer $failcount)
    //          where $failcount is the number of failed logins to be considered as 'locked'
    // Returns: $return              array data on Succes, empty array on failure
    //          $return['all']       All users
    //          $return['active']    active
    //          $return['inactive']  inactive
    //          $return['locked']    locked
    public function get_usroverview($failcount)
    {
        $qid = $this->query('SELECT count(*), active FROM '.$this->DB['tbl_user'].' GROUP by active LIMIT 1');
        while(list($number, $active) = mysql_fetch_row($qid)) {
            $num[$active] = $number;
        }
        list ($locked) = mysql_fetch_row($this->query('SELECT count(*) FROM '.$this->DB['tbl_user'].' where fail_count >= '.intval($failcount)));
        $return = array
                ('inactive' => isset($num['0']) ? $num['0'] : 0
                ,'active'   => isset($num['1']) ? $num['1'] : 0
                ,'locked'   => isset($locked)   ? $locked   : 0
                );
        $return['all'] = $return['active'] + $return['inactive'] + $return['locked'];
        return $return;
    }

    // Get user's personal setup from the DB
    // Input  : get_usr_choices(integer user id)
    // Returns: $return    string data on success, FALSE otherwise
    public function get_usr_choices($uid)
    {
        if ($choices = $this->query('SELECT choices FROM '.$this->DB['tbl_user'])) {
            list ($choices) = mysql_fetch_row($choices);
            if (strstr($choices, ';;')) {
                $return = array();
                foreach (explode(LF, $choices) as $l) {
                    if (strlen(trim($l)) == 0) continue;
                    if ($l{0} == '#') continue;
                    $parts = explode(';;', trim($l));
                    if (!isset($parts[1])) $parts[1] = false;
                    $return['core'][$parts[0]] = $parts[1];
                }
                return $return;
            } else {
                $choices = @unserialize($choices);
                return $choices;
            }
        }
        return false;
    }

    // Input  : set_usr_choices(integer user id, string choices)
    // Returns: $return    TRUE on success, FALSE otherwise
    public function set_usr_choices($uid, $choices)
    {
        $choices = serialize($choices);
        $query = 'UPDATE '.$this->DB['tbl_user'].' SET choices="'.mysql_escape_string($choices).'"';
        return $this->query($query);
    }

    // Activate user (Register now)
    public function activate($uid = '', $un = '', $pw = '')
    {
        $uid = intval($uid);
        list ($return) = mysql_fetch_row($this->query('SELECT 1 FROM '.$this->DB['tbl_user'].' WHERE username="'.mysql_escape_string($un).'" AND password=md5("'.mysql_escape_string($pw).'")'));
        if (1 == $return) $this->query('UPDATE '.$this->DB['tbl_user'].' SET active="1"');
        return $return;
    }

    // Tell, how many users there are in the database
    public function get_usercount()
    {
        list ($return) = mysql_fetch_row($this->query('SELECT count(*) FROM '.$this->DB['tbl_user']));
        return $return;
    }

    // Encrypt a string
    // Input:   confuse(string $data, string $key);
    // Returns: encrypted string
    public function confuse($data = '', $key = '')
    {
        $encoded = ''; $DataLen = strlen($data);
        if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
        for ($i = 0; $i < $DataLen; ++$i) {
            $encoded .= chr((ord($data{$i}) + ord($key{$i})) % 256);
        }
        return base64_encode($encoded);
    }

    // Decrypt a string
    // Input:   deconfuse(string $data, string $key);
    // Returns: decrypted String
    public function deconfuse($data = '', $key = '')
    {
        $data = base64_decode($data);
        $decoded = '';  $DataLen = strlen($data);
        if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
        for ($i = 0; $i < $DataLen; ++$i) {
            $decoded .= chr((256 + ord($data{$i}) - ord($key{$i})) % 256);
        }
        return $decoded;
    }

    // MOD section - Path: Community

    // Get amount of sent SMS for a certain user in a given month
    public function get_sms_sent($month = '197001', $uid = false)
    {
        $query = 'SELECT sum(setting) FROM '.$this->DB['tbl_user_accounting'].' WHERE `what`="sms" AND `when`="'.intval($month).'"';
        list ($sum) = mysql_fetch_row($this->query($query));
        return $sum;
    }

    public function set_sms_sent($month = '197001', $uid = false, $amount = 1)
    {
        if (!$uid) return false;
        list($exists) = mysql_fetch_row($this->query('SELECT 1 FROM '.$this->DB['tbl_user_accounting']
                        .' WHERE `what`="sms" AND `when`="'.intval($month).'"'));
        if ($exists) {
            $query = 'UPDATE '.$this->DB['tbl_user_accounting'].' SET setting=setting+'.intval($amount)
                    .' WHERE `what`="sms" AND `when`="'.intval($month).'"';
        } else {
            $query = 'INSERT '.$this->DB['tbl_user_accounting'].' (`what`,`when`,uid,setting) '
                    .'VALUES ("sms", "'.intval($month).'", '.intval($uid).','.intval($amount).')';
        }
        $this->query($query);
        return true;
    }

    // Log a sent SMS to MySQL
    public function log_sms_sent($pass)
    {
        if (!isset($pass['when'])) $pass['when'] = time();
        if (!isset($pass['uid']))  $pass['uid'] = 0;
        if (!isset($pass['type'])) $pass['type'] = 0;
        if (!isset($pass['text'])) $pass['text'] = '';
        if (!isset($pass['receiver']) || !isset($pass['size'])) return;

        $query = 'INSERT '.$this->DB['tbl_user_smslogging'].' (uid, moment, target_number, size, type, content) VALUES ('
                 .intval($pass['uid']).',"'.date('Y-m-d H:i:s', $pass['when']).'","'.mysql_escape_string($pass['receiver']).'",'
                 .intval($pass['size']).','.mysql_escape_string($pass['type']).',"'.mysql_escape_string($pass['text']).'")';
        $this->query($query);
    }

     // Set current deposit of the system
    public function get_sms_global_deposit()
    {
        $query = 'SELECT setting FROM '.$this->DB['tbl_user_accounting'].' WHERE `what`="sms" AND uid=0';
        list ($sum) = mysql_fetch_row($this->query($query));
        return $sum;
    }

    // Set current deposit of the system
    function set_sms_global_deposit($deposit)
    {
        list($exists) = mysql_fetch_row($this->query('SELECT 1 FROM '.$this->DB['tbl_user_accounting'].' WHERE `what`="sms" AND uid=0'));
        if ($exists) {
            $query = 'UPDATE '.$this->DB['tbl_user_accounting'].' SET setting='.intval($deposit).' WHERE `what`="sms" AND uid=0';
        } else {
            $query = 'INSERT '.$this->DB['tbl_user_accounting'].' (setting, `what`, uid) VALUES ('.intval($deposit).',"sms",0)';
        }
        $this->query($query);
    }

     // Decrease the global deposit by one
    public function decrease_sms_global_deposit($amount = 1)
    {
        if (!$amount) $amount = 1;
        $query = 'UPDATE '.$this->DB['tbl_user_accounting'].' SET setting=setting-'.intval($amount).' WHERE `what`="sms" AND uid=0';
        $this->query($query);
    }

     // Get sent SMS statistics for a specific month
    // Input:  get_sms_stats(int month); Format: YYYYMM
    // Return: array
    //         (int sum of sent SMS
    //         ,int max sent SMS by single user
    //         )
    public function get_sms_stats($month = '197001', $uid = false)
    {
        $add = (!$uid) ? 'uid!=0' : 'uid=1';
        $query = 'SELECT sum(setting), min(setting), max(setting), count(*) FROM '.$this->DB['tbl_user_accounting']
                .' WHERE `what`="sms" AND `when`="'.intval($month).'" AND '.$add;
        list ($sum, $min, $max, $cnt) = mysql_fetch_row($this->query($query));
        // If we have no events, we don't need to try to get those:
        // Same applies on fetching stats for a specific user
        if (isset($sum) && $sum && !$uid) {
            $query = 'SELECT u.uid, u.username FROM '.$this->DB['tbl_user_accounting'].' a,'.$this->DB['tbl_user']
                    .' u WHERE a.`what`="sms" AND a.`when`="'.intval($month).'" AND a.'.$add
                    .' AND a.uid=u.uid ORDER by a.setting ASC LIMIT 1';
            list ($min_uid, $min_usr) = mysql_fetch_row($this->query($query));
            $query = 'SELECT u.uid, u.username FROM '.$this->DB['tbl_user_accounting'].' a,'.$this->DB['tbl_user']
                    .' u WHERE a.`what`="sms" AND a.`when`="'.intval($month).'" AND a.'.$add
                    .' AND a.uid=u.uid ORDER by a.setting DESC LIMIT 1';
            list ($max_uid, $max_usr) = mysql_fetch_row($this->query($query));
        }
        return array
                (isset($sum) ? $sum : 0
                ,isset($min) ? $min : 0
                ,isset($max) ? $max : 0
                ,isset($cnt) ? $cnt : 0
                ,isset($min_uid) ? array
                                  ('min_usr' => $min_usr
                                  ,'min_uid' => $min_uid
                                  ,'max_usr' => $max_usr
                                  ,'max_uid' => $max_uid
                                  )
                                : false
                );
    }

    /**
     * Tries to find out the specific quota setting effective for the given user ID.
     * If no setting was found, false will be returned. The same applies, if the setting
     * has been explicitly set to false. False values mean: no limit set.
     *
     * @param int $uid Either a value > 0 for a specific user or == 0 for the global definition
     * @param string $handler The handler this quota setting applies to
     * @param string $what The quota setting to query
     * @return mixed Either FALSE for no setting / unlimited or the specific value defined for the setting
     * @since 3.9.1
     */
    public function quota_get($uid, $handler, $what)
    {
        if ($uid !== 0) {
            $res = $this->query('SELECT `setting` FROM '.$this->DB['tbl_user_quota'].' WHERE `uid`=1'
                    .' AND `handler`="'.$this->escape($handler).'" AND `what`="'
                    .$this->escape($what).'"');
            // At least one result for that query
            if (mysql_num_rows($res)) {
                list($setting) = mysql_fetch_row($res);
                return $setting;
            }
        }
        $res = $this->query('SELECT `setting` FROM '.$this->DB['tbl_user_quota'].' WHERE `uid`=0'
                .' AND `handler`="'.$this->escape($handler).'" AND `what`="'
                .$this->escape($what).'"');
        // At least one result for that query
        if (mysql_num_rows($res)) {
            list($setting) = mysql_fetch_row($res);
            return $setting;
        }
        return false;
    }

    /**
     * Sets a quota value effective for a specific user ID and handler name. Passing a
     * user ID of 0 you define the global setting, which will take effect, whenever there's
     * no specific value defined for a specific user.
     *
     * @param int $uid Either a value > 0 for a specific user or == 0 for the global definition
     * @param string $handler The handler this quota setting applies to
     * @param string $what The quota setting
     * @param string $setting Its value
     * @return bool
     * @since 3.9.1
     */
    public function quota_set($uid, $handler, $what, $setting)
    {
        $res = $this->query('SELECT 1 FROM '.$this->DB['tbl_user_quota'].' WHERE `uid`=1'
                .' AND `handler`="'.$this->escape($handler).'" AND `what`="'
                .$this->escape($what).'"');
        // Determine, whether to update the column or insert it
        $query = (mysql_num_rows($res))
                ? 'UPDATE '.$this->DB['tbl_user_quota'].' SET `setting`="'.$this->escape($setting)
                        .'" WHERE `uid`=1 AND `handler`="'.$this->escape($handler)
                        .'" AND `what`="'.$this->escape($what).'"'
                : 'INSERT '.$this->DB['tbl_user_quota'].' SET `uid`=1'
                        .', `handler`="'.$this->escape($handler).'", `what`="'
                        .$this->escape($what).'", `setting`="'.$this->escape($setting).'"'
                ;
        return $this->query($query);
    }

    /**
     * Explicitly removes a quota definition.
     *
     * @param int $uid Either a value > 0 for a specific user or == 0 for the global definition
     * @param string $handler The handler this quota setting applies to
     * @param string $what The quota setting
     * @return bool
     * @since 3.9.1
     */
    public function quota_drop($uid, $handler, $what)
    {
        if ($this->query('DELETE FROM '.$this->DB['tbl_user_quota'].' WHERE `uid`=1 AND `handler`="'.$this->escape($handler).'" AND `what`="'.$this->escape($what).'"')) {
            $this->query('ALTER TABLE '.$this->DB['tbl_user_quota'].' ORDER BY `qid`');
            return true;
        }
        return false;
    }

    /**
     * This is used by a handler, to add capabilities for certain mimetypes to the global
     * SendTo management table.
     * To signal, that your handler basically handles everything, just pass array('%' => 'accept').
     * To tell, you will handle all text files but HTML, pass array('text/%' => 'accept', 'text/html' => 'ignore').
     *
     * @param array $mimetypes Pass all MIME types here. Keys: MIME type, Values 'accept' or 'ignore'.
     * @param string $handler Handler's internal name, e.g. calendar or files
     * @param bool $on_context Whether to show a context menu entry, Default: true
     * @param bool $on_fetch Whether to be included in mail fetching filters; Default: false
     * @return bool
     * @since 3.9.7
     */
    public function sendto_add_mimehandler($mimetypes, $handler, $on_context = 1, $on_fetch = 0)
    {
        if (!$handler) return false;
        $q_l = 'INSERT INTO '.$this->DB['tbl_sendto_handler'].' (`behaviour`, `mimetype`, `handler`, `on_context`, `on_fetch`) ';
        $q_r = array();
        foreach ($mimetypes as $type => $behave) {
            $q_r[] = 'VALUES ("'.($behave == 'accept' ? 'accept' : 'ignore').'", "'.$this->escape($type).'"'
                    .', "'.$this->escape($handler).'", "'.($on_context).'", "'.($on_fetch).'")';
        }
        return $this->query($q_l.join(',', $q_r));
    }

    /**
     * Removes all SendTo entries associated with a certain handler
     *
     * @param string $handler
     * @return bool
     * @since 3.9.7
     */
    public function sendto_remove_mimehandler($handler)
    {
        return $this->query('DELETE FROM '.$this->DB['tbl_sendto_handler'].' WHERE `handler`="'.$this->escape($handler).'"');
    }

    /**
     * Returns a list of known handlers, which can handle a given MIME type
     *
     * @param string $mimetype
     * @return array  A list of handlers, which can handle the given MIME type
     * @since 3.9.7
     */
    public function sendto_get_mimehandlers($type)
    {
        $type = $this->escape($type);
        $return = array();
        $res = $this->query('SELECT `handler` FROM '.$this->DB['tbl_sendto_handler']
                .' WHERE (`behaviour`="accept" AND "'.$type.'" LIKE `mimetype`) AND NOT (`behaviour`="ignore" AND "'.$type.'" LIKE `mimetype`) GROUP BY `handler`');
        while (list($handler) = mysql_fetch_row($res)) {
            $return[] = $handler;
        }
        return $return;
    }

    /**
     * This returns a list of all supported (or ignored) MIME types for a specific handler.
     * Useful for checks on updates.
     *
     * @param string $handler
     * @return array Keys: MIME types, values: 'accept' or 'ignore'
     * @since 3.9.7
     */
    public function sendto_handler_supports($handler)
    {
        $res = $this->query('SELECT `behaviour`, `mimetype` FROM '.$this->DB['tbl_sendto_handler'].' WHERE `handler`="'.$this->escape($handler).'"');
        $return = array();
        while (list($behave, $type) = mysql_fetch_row($res)) {
            $return[$type] = $behave;
        }
        return $return;
    }

    /**
     * Management method for checking, whether the SendTo DB needs updates. Simply
     * returns all entries of the DB
     *
     * @return array
     * @since 3.9.8
     */
    public function sendto_listall()
    {
        $res = $this->query('SELECT `handler`, `behaviour`, `mimetype` FROM '.$this->DB['tbl_sendto_handler'].' ORDER BY `handler`');
        $return = array();
        while ($res && $line = mysql_fetch_assoc($res)) {
            $return[$line['handler']][$line['mimetype']] = $line['behaviour'];
        }
        return $return;
    }

    /**
     * A speical method pair to switch between confused and clear text passwords for SMTP / POP3 / IMAP
     */
    public function confused_cleartext()
    {
        $r = array();
        $qid = $this->query('SELECT id, poppass, popserver, popport, popuser, smtppass, smtpserver, smtpport, smtpuser FROM '.$this->DB['tbl_profiles']);
        while ($line = mysql_fetch_assoc($qid)) {
            $r[] = array
                    ('i' => $line['id']
                    ,'p' => $this->deconfuse($line['poppass'], $line['popserver'].$line['popport'].$line['popuser'])
                    ,'s' => $this->deconfuse($line['smtppass'], $line['smtpserver'].$line['smtpport'].$line['smtpuser'])
                    );
        }
        foreach ($r as $v) {
            $this->query('UPDATE '.$this->DB['tbl_profiles'].' SET `poppass`="'.$this->escape($v['p']).'", `smtppass`="'.$this->escape($v['s']).'" WHERE id='.$v['i']);
        }
    }
    public function cleartext_confused()
    {
        $r = array();
        $qid = $this->query('SELECT id, poppass, popserver, popport, popuser, smtppass, smtpserver, smtpport, smtpuser FROM '.$this->DB['tbl_profiles']);
        while ($line = mysql_fetch_assoc($qid)) {
            $r[] = array
                    ('i' => $line['id']
                    ,'p' => $this->confuse($line['poppass'], $line['popserver'].$line['popport'].$line['popuser'])
                    ,'s' => $this->confuse($line['smtppass'], $line['smtpserver'].$line['smtpport'].$line['smtpuser'])
                    );
        }
        foreach ($r as $v) {
            $this->query('UPDATE '.$this->DB['tbl_profiles'].' SET `poppass`="'.$this->escape($v['p']).'", `smtppass`="'.$this->escape($v['s']).'" WHERE id='.$v['i']);
        }
    }

    /**
    * Reads the structure of the currently used Database and returns it as an array structure
    * @param  void
    * @return  array  keys: table names, values: arrays with column names as keys and column definitions as values
    * @since  3.1.5
    */
    public function get_structure()
    {
        $qid = $this->query('SHOW TABLES FROM `'.$this->DB['mysql_database'].'` LIKE "'.$this->DB['mysql_prefix'].'_%"');
        $return = array();
        while (list($table) = $this->fetchrow($qid)) {
            // I need the plain table name without the prefix and the DB name and stuff
            $tbl = preg_replace('!^'.preg_quote($this->DB['mysql_prefix'].'_', '!').'!', '', $table);
            $return[$tbl] = array();
        }
        foreach ($return as $table => $v) {
            $return[$table] = array('fields' => array(), 'index' => array());
            $qid = $this->query('SHOW COLUMNS FROM '.$this->DB['db_pref'].$table);
            while ($line = $this->fetchassoc($qid)) {
                $return[$table]['fields'][$line['Field']] = array
                        ('type' => $line['Type']
                        ,'null' => ($line['Null'] == 'NO') ? 0 : 1
                        ,'key' => $line['Key']
                        ,'default' => (is_null($line['Default'])) ? 'NULL' : $line['Default']
                        ,'extra' => $line['Extra']
                        );
            }
            $qid = $this->query('SHOW INDEX FROM '.$this->DB['db_pref'].$table);
            while ($line = $this->fetchassoc($qid)) {
                if ($line['Key_name'] == 'PRIMARY') continue;
                $return[$table]['index'][$line['Key_name']] = $line['Column_name'];
            }
        }
        return $return;
    }

    /**
    * This method updates the DB structure. Takes two arguments, the first is the tables to add, the second the tables
    * to update. Be aware, that dropping either tables or fields is not possible, since this could (and probably would)
    * interfer with the idea of the owner of the licence also owning the data stored in the tables. Dropping unknown
    * or no longer necessary fields would by chance destroy data the client probably still needs
    *
    * @param  array  Tables to add; keys: table names, values: 2 arrays with key == 'fields' for the field definitions (keys
    *   are the field names, values
    * @param  array  keys: table names, values: arrays with column names as keys and column definitions as values, additionally
    *         the flag 'action' tells whether to add a field (value 'add') or alter it (value 'alter')
    * @param  array  list of statements to run (for more complex update tasks like rewriting values, if necessary)
    * @return  bool  true on success, false on failure
    * @since  3.1.5
    */
    public function update_structure($add, $alter, $statement)
    {
        // Add new tables
        foreach ($add as $table => $def) {
            $query = 'CREATE TABLE '.$this->DB['db_pref'].$table.' (';
            $i = 0;
            foreach ($def['fields'] as $field => $fdef) {
                if ($i) $query .= ', ';
                $query .= '`'.$field.'` '.$fdef['type'].' '.($fdef['null'] ? 'NULL' : 'NOT NULL')
                        .$this->update_structure_defval($fdef['default'], $fdef['type'])
                        .($fdef['key'] ? ' PRIMARY KEY' : '').($fdef['extra'] ? ' auto_increment' : '');
                $i++;
            }
            foreach ($def['index'] as $field => $fdef) {
                if ($i) $query .= ', ';
                $query .= ' INDEX `'.$field.'` (`'.$fdef.'`) ';
                $i++;
            }
            $query .= ')';
            $this->query($query);
            $e = $this->error();
            if ($e) echo $e.LF.$query.LF.LF;
        }
        // Add new fields if necessary
        foreach ($alter as $table => $def) {
            $query = 'ALTER TABLE '.$this->DB['db_pref'].$table.' ';
            $i = 0;
            foreach ($def['fields'] as $field => $fdef) {
                if ($i) $query .= ', ';
                $query .= 'ADD `'.$field.'` '.$fdef['type'].' '.($fdef['null'] ? 'NULL' : 'NOT NULL')
                        .$this->update_structure_defval($fdef['default'], $fdef['type'])
                        .($fdef['key'] ? ' PRIMARY KEY' : '').($fdef['extra'] ? ' auto_increment' : '');
                $i++;
            }
            foreach ($def['index'] as $field => $fdef) {
                if ($i) $query .= ', ';
                $query .= 'ADD INDEX `'.$field.'` (`'.$fdef.'`) ';
                $i++;
            }
            $this->query($query);
            $e = $this->error();
            if ($e) echo $e.LF.$query.LF.LF;
        }
        foreach ($statement as $query) {
            $query = str_replace('{prefix}', $this->DB['db_pref'], $query);
            $this->query($query);
            $e = $this->error();
            if ($e) echo $e.LF.$query.LF.LF;
        }
    }

    /**
     * Takes the setting for a default value and returns the right SQL portion for it
     *
     * @param mixed $val
     * @return string
     * @since 4.0.5
     */
    public function update_structure_defval($val, $type)
    {
        if ($type == 'blob' || $type == 'text') return '';
        return (false !== $val ? ($val === 'NULL' ? ' DEFAULT NULL' : (is_int($val) ? ' DEFAULT '.$val : ' DEFAULT "'.$val.'"')) : '');
    }

    //
    // Private / protected methods
    //

    /**
    * Actually issues the mysql_query()
    * @param  string  Query to perform
    * @return  res  Resource ID of the issued query
    * @since 3.6.3
    */
    protected function query($query)
    {
        return mysql_query($query, $this->dbh);
    }

    /**
     * Currently just an alias to mysql_escape_string(), but could add some
     * bells and whistles, if necessary
     *
     * @param unknown_type $string
     * @return unknown
     * @since 3.9.2
     */
    protected function escape($string)
    {
        return mysql_escape_string($string);
    }

    /**
     * Intermediate until generation 4, where the DB layer is different
     *
     * @return string
     */
    protected function error()
    {
        return mysql_error($this->dbh);
    }

    protected function fetchrow($qid) { return mysql_fetch_row($qid); }
    protected function fetchassoc($qid) { return mysql_fetch_assoc($qid); }
}
?>
