<?php
/* ------------------------------------------------------------------------- */
/* IMAP Driver - phlyMail 3.4.0+                                             */
/* Proivdes connect and retrieve functions for the IMAP protocol             */
/* (c) 2005-2008 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v0.3.6                                                                    */
/* ------------------------------------------------------------------------- */

// Simple login and mail retrieval interface as counterpart of the pop3 lib,
// just extending it a bit

class imap {
    private $error           = false;
    public $append_errors    = false;
    public $timestamp_errors = false;
    public $reconnect_sleep  = 1;

    public function __construct($server = '', $port = 143, $recon_slp = 0)
    {
        if (!$port) $port = 143;
        if ($this->connect($server, $port)) {
            $this->server = $server;
            $this->port = $port;
            $this->reconnect_sleep = isset($recon_slp) ? $recon_slp : 0;
            $this->connected = true;
        } else $this->connected = false;
        return true;
    }

    // Sole aim is to know, whether we are connected or not
    // since we cannot return something useful on construction
    // of the object
    public function check_connected()
    {
        return $this->connected;
    }

    public function get_last_error()
    {
        $return = ($this->error) ? $this->error : '';
        unset($this->error);
        return $return;
    }

    // Log in to IMAP server
    public function login($username = '', $password = '', $mailbox = 'INBOX', $ro = false, $allowssl = true)
    {
        $sslopts = '/imap/novalidate-cert/norsh';
        if ($this->port == 993) {
            $sslopts = '/imap/ssl/novalidate-cert/norsh';
        } elseif (!$allowssl) {
            $sslopts = '/imap/notls/norsh';
        }
        $serverstring = '{'.$this->server.':'.$this->port.$sslopts.'}'.$mailbox;
        $mbox = imap_open($serverstring, $username, $password, (($ro) ? OP_READONLY : 0) | OP_SILENT);
        if (!$mbox || !is_resource($mbox)) {
            $this->error = 'Could no connect to '.$this->server.':'.$this->port;
            return false;
        }
        $this->mbox = $mbox;
        $this->folder = $mailbox;
        $this->serverstring = $serverstring;
        return true;
    }

    // Return LIST, if mail given of this one, else complete
    public function get_list($mail = false, $listonly = false)
    {
        if ($mail) {
            $info = array_shift(imap_fetch_overview($this->mbox, $mail));
            if (isset($info)) {
                return array
                        ('size' => $info->size, 'recent' => $info->recent
                        ,'flagged' => $info->flagged, 'answered' => $info->answered
                        ,'seen' => $info->seen, 'draft' => $info->draft
                        ,'uidl' => $info->uid
                        );
            } else {
                $this->set_error('IMAP listing failed');
                return false;
            }
        } else {
            $return = array();
            // Mailbox is empty?
            if (imap_num_msg($this->mbox) == 0) {
                return $return;
            }
            if ($listonly) {
                foreach (imap_search($this->mbox, 'ALL', SE_UID) as $k => $uid) {
                    $return[$k] = $uid;
                }
            } else  {
                foreach (imap_fetch_overview($this->mbox, '1:*') as $info) {
                    if (isset($info->deleted) && $info->deleted) continue;
                    $return[$info->msgno] = array
                            ('size' => $info->size, 'recent' => $info->recent
                            ,'flagged' => $info->flagged, 'answered' => $info->answered
                            ,'seen' => $info->seen, 'draft' => $info->draft
                            ,'uidl' => $info->uid
                            );
                }
            }
            return $return;
        }
    }

    // Get the header lines of a mail
    public function top($mail = false)
    {
        if (!$mail) {
            $this->set_error('No mail given');
            return false;
        }
        return imap_fetchheader($this->mbox, $mail);
    }

    // Get the Unique ID of a mail
    public function uidl($mail = false)
    {
        if (!$mail) {
            $this->set_error('No mail given');
            return false;
        }
        return imap_uid($this->mbox, $mail);
    }

    public function msgno($uidl = false)
    {
        if (!$uidl) {
            $this->set_error('No UID given');
            return false;
        }
        return imap_msgno($this->mbox, $uidl);
    }

    // Get stats of a IMAP box
    // WARNING: The returned data is incompatible to that of the POP3 driver
    public function stat()
    {
        $info = imap_status($this->mbox, $this->serverstring, SA_ALL);
        if (isset($info) && !empty($info)) {
            return $info;
        } else {
            return false;
        }
    }

    // Delete a selected Email from IMAP server
    public function delete($mail = false)
    {
        if (!$mail) {
            $this->set_error('No mail given');
            return false;
        }
        imap_delete($this->mbox, $mail);
        imap_expunge($this->mbox);
        return true;
    }

    /**
    * Takes a list of UIDLs, which are to delete from the server's mailbox
    * @param  array  values are the UIDLs
    * @return  mixed  TRUE on success, FALSE on failure
    * @since 3.1.9
    */
    public function delete_by_uidl($uidls)
    {
        if (!$uidls || empty($uidls)) return true;
        foreach ($udils as $uidl) {
            $this->delete(imap_msgno($this->mbox, $uidl));
        }
        return true;
    }


    /**
     * In the POP§ counterpart of this method, noop() actually does nothing.
     * Since RFC1939 requires a positive response, we don't care about errors yet
     * Here this method actually issues a ping to keep the connection open
     *
     * @return true
     */
    public function noop()
    {
        imap_ping($this->mbox);
        return true;
    }

    // For compliance with the POP3 class only
    public function reset()
    {
        return true;
    }

    // Send RETR command to IMAP server
    // Get subsequent server responses via talk_ml()
    public function retrieve($mail = false, $uidl = false)
    {
        if ($uidl) $mail = $this->msgno($uidl);
        if (!$mail) {
            $this->set_error('No mail given');
            return false;
        }
        return imap_fetchheader($this->mbox, $mail, FT_PREFETCHTEXT).imap_body($this->mbox, $mail, FT_PEEK);
    }

    // Retrieve a mail from server and put into given file
    public function retrieve_to_file($mail = false, $path = false)
    {
        $old_umask = umask(0);
        if (!$mail || !$path) {
            $this->set_error('Usage: retrieve_to_file(integer mail, string path)');
            return false;
        }
        if (!file_exists(dirname($path)) || !is_dir(dirname($path))) {
            $this->set_error('Non existent directory '.dirname($path));
            return false;
        }
        @chmod($path,"777"); 
        $out = fopen($path, 'w');
        if (!$out) {
            $this->set_error('Could not open file '.$path);
            return false;
        }
        fwrite($out, imap_fetchheader($this->mbox, $mail, FT_PREFETCHTEXT).imap_body($this->mbox, $mail, FT_PEEK));
        fclose($out);
        chmod($path, $GLOBALS['_PM_']['core']['file_umask']);
        umask($old_umask);
        return $path;
    }

    /**
     * Stores a mail in the imap folder (a.k.a. mailbox) specified
     *
     * @param string  Mailbox spec, e.g. INBOX.Sent
     * @param string  Message to store (make sure, it is RFC822 conformant)
     * @param string  phlyMail compatible denotion of the message status (read/unread, ...)
     * @return bool  Whether the operation was successfull
     * @since 0.1.1
     */
    public function store($mailbox, $message, $status = false)
    {
        $flags = null;
        if (false !== $status) {
            if (strstr($status, 'a') != -1) $flags[] = '\Answered';
            if (strstr($status, 'u') == -1) $flags[] = '\Seen';
            $flags = join(' ', $flags);
        }
        return imap_append($this->mbox, $this->server.$mailbox, $message, $flags);
    }

    // Close IMAP connection
    public function close()
    {
        return @imap_close($this->mbox);
    }

    /**
     * Tries to get the quota for the currently open connection
     *
     * @param string $root  Where to start fetching the quota, usually INBOX
     * @return mixed $quota Array (see php.net/imap_get_quotaroot) on success or FALSE on failure
     * @since 0.2.3
     */
    public function get_quota($root = 'INBOX')
    {
        $quota = @imap_get_quotaroot($this->mbox, $root);
        return (is_array($quota) ? $quota : false);
    }

    //
    // Private / protected methods
    //

    // Do the actual connect to the chosen server
    protected function connect($server = '', $port = 143)
    {
        $this->server = $server;
        $this->port = $port;
        return true;
    }

    // Add or set an (timestamped error), that can be requested via get_last_error()
    protected function set_error($error)
    {
        $vorn = ($this->timestamp_errors) ? time().' ' : '';
        if ($this->append_errors) {
            $this->error .= $vorn.$error.LF;
        } else {
            $this->error  = $vorn.$error;
        }
    }

    // Try to find out, whether the connection is still alive
    protected function alive()
    {
        return @imap_ping($this->mbox);
    }

}
?>
