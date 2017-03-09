<?php
/* ------------------------------------------------------------------------- */
/* POP3 Driver with transparent SSL support - phlyMail 3.4.0+                */
/* Proivdes connect and retrieve functions for the POP3 protocol             */
/* (c) 2003-2008 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v3.6.7                                                                    */
/* ------------------------------------------------------------------------- */
/*
// if sha1() is not available, we use the PHP implementation
if (!function_exists('sha1')) {
    if (function_exists('hash_algos') && in_array('sha1', hash_algos())) {
        function sha1($str) { return hash('sha1', $str); }
    } else {
        require_once(dirname(__FILE__).'/sha1.inc.php');
        function sha1($str) { return sha1::compute($str); }
    }
}
// Likewise for sha256
if (!function_exists('sha256')) {
    if (function_exists('hash_algos') && in_array('sha256', hash_algos())) {
        function sha256($str) { return hash('sha256', $str); }
    } else {
        require_once(dirname(__FILE__).'/sha256.inc.php');
        function sha256($str) { return SHA256::hash($str, 'hex'); }
    }
}
*/

class pop3 {
    public $append_errors = true;
    public $timestamp_errors = false;
    private $error = false;
    protected $is_ssl = false;
    protected $has_tls = false;
    protected $is_tls = false;
    protected $may_tls = true;
    protected $_diag_session = false; 
    // 我们支持的SASL机制名单
    protected $SASL = array('cram_md5', 'login', 'plain', 'cram_sha256', 'cram_sha1');

    public function __construct($server, $port = 110, $recon_slp = 0, $allowtls = true)
    {
    	
        if ($this->_diag_session) $this->diag = fopen(dirname(__FILE__).'/pop3_diag.txt', 'w');
        $this->may_tls = $allowtls;
        if (!$port) $port = 110;
        if ($this->connect($server, $port)) {
        	
            $this->server = $server;
            $this->port = $port;
            $this->reconnect_sleep = isset($recon_slp) ? $recon_slp : 0;
            $this->connected = 'connected';
        } else $this->connected = 'unconnected';
        
        return true;
    }

    /**
     * Sole aim is, to know, whether we are connected or not
     * since we cannot return something useful on construction
     * of the object
     */
    public function check_connected()
    {
        return $this->connected;
    }

    public function get_last_error()
    {
        $return = ($this->error) ? $this->error : '';
        unset ($this->error);
        return $return;
    }

    /**
     * Log in to POP3 server
     * @param string $username
     * @param string $password
     * @param int $apop  Set to 1 to allow APOP, set to 0 to disallow even if advertised by server
     */
    public function login($username = '', $password = '', $apop = 1)
    {
        $return = array('type' => false, 'login' => false);
        // Issue empty command - some POP3 server constantly drop the first command sent to them
        // This is somewhat violating RFC1939 but who is mistaking here?
        // Mainly to blame is qmail at this point ...
        if (!$this->is_ssl) $this->noop();
        // Try to find out about POP3 AUTH and use it on success
        $capa = $this->capa(false);
        
        if ($this->has_tls && false !== $capa && isset($capa['stls']) && $capa['stls']) {
        //echo "aa";exit;	
            $this->talk('STLS');
            $res = stream_socket_enable_crypto($this->fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$res) {
                $this->close();
                $this->set_error('Cannot enable TLS, although server advertises it');
            } else {
                $this->is_tls = true;
                $return['type'] = 'secure';
            }
            
            // Now check, whether the connections has not been closed
            if (!$this->alive()) {
                $this->set_error($response);
                $this->close();
                sleep($this->reconnect_sleep);
                $this->connect($this->server, $this->port);
                if (!$this->alive()) return $return;
                if (!$this->is_ssl) $this->noop();
            }
            
        }
        // Server supports POP3 AUTH... try the supported mechanisms to authenticate
        
        if (!$this->is_ssl && !$this->is_tls && false !== $capa && $capa['sasl'] != false) {
        	//echo "bb";exit;
            // Find the mechanisms supported on both sides
            $SASL = array_intersect($this->SASL, $capa['sasl_internal']);
            $this->is_auth = false;
            foreach ($SASL as $v) {
                $function_name = '_auth_'.$v;
                if ($this->{$function_name}($username, $password)) {
                    $return['login'] = 1;
                    $return['type'] = 'secure';
                    return $return;
                }
            }
            // Now check, whether the connections has not been closed
            if (!$this->alive()) {
                $this->set_error($response);
                $this->close();
                sleep($this->reconnect_sleep);
                $this->connect($this->server, $this->port);
                if (!$this->alive()) return $return;
                if (!$this->is_ssl) $this->noop();
            }
            
        }
        // APOP
        if (!$this->is_ssl && !$this->is_tls && 1 != $return['login']
                && preg_match('/(<.+@.+>)$/', $this->greeting, $token) && $apop == 1) {
                	//echo "cc";exit;
            $response = $this->talk('APOP '.$username.' '.md5($token[1].$password));
            if (strtolower(substr($response, 0, 3)) == '+ok') {
                $return['login'] = 1;
                $return['type'] = 'secure';
                return $return;
            }
            // APOP failed due to bogus server advertising
            // Now check, whether the connections has not been closed
            if (!$this->alive()) {
                $this->set_error($response);
                $this->close();
                sleep($this->reconnect_sleep);
                $this->connect($this->server, $this->port);
                if (!$this->alive()) return $return;
                if (!$this->is_ssl) $this->noop();
            }
            
        }
        // USER/PASS
        if (1 != $return['login']) {
        	//echo "----x";
        	//echo "ee";
        	//echo 'USER ';
        	//exit;
            $response = $this->talk('USER '.$username);
            if (strtolower(substr($response, 0, 4)) == '-err') {
                $this->set_error($response);
                if (!$this->alive()) return $return;
            }
            $response = $this->talk('PASS '.$password);
            //echo $response;exit;
            if (strtolower(substr($response, 0, 3)) == '+ok') {
            	//echo "aa";
                $return['login'] = 1;
                $return['type'] = 'normal';
            } else {
                $this->set_error($response);
                if (!$this->alive()) return $return;
            }
            
        }
        //print_r($return);
        
        return $return;
    }

    /**
     * Allows to specifically query the server for a capabilites list. As defined
     * in RFC2449, only two server reactions are possible:
     * +OK <Multiline capabilites reponse> and -ERR, where the latter means, that
     * this server does not support this command. The multiline response can
     * reveal some useful information about the type of mailserver, the retention
     * policy and supported SASL mechanisms, if any.
     *
     * @param bool Set to true to receive the unparsed response, Default: false,
     *     which will give you a nice little array of recognized capabilities
     * @return mixed  String on $raw = true, array of recognized features otherwise
     * @since 3.6.0
     */
    public function capa($raw = false)
    {
        $return = array('top' => false, 'user' => false, 'uidl' => false, 'stls' => false, 'sasl' => false, 'login-delay' => 0
                ,'expire' => 'never', 'implementation' => 'unknown', 'resp-codes' => false, 'pipelining' => false);
        $response = $this->talk('CAPA');
        if ('+ok' == strtolower(substr($response, 0, 3))) {
            if ($raw) {
                $return = $response;
                while ($line = $this->talk_ml()) {
                    $return .= $line;
                }
                return $return;
            }
            while ($line = $this->talk_ml()) {
                $capa = explode(' ', trim($line), 2);
                $capa[0] = strtolower($capa[0]);
                switch ($capa[0]) {
                case 'top':
                case 'user':
                case 'uidl':
                case 'stls':
                case 'resp-codes':
                case 'pipelining':
                    $return[$capa[0]] = true;
                    break;
                case 'implementation':
                case 'login-delay':
                case 'expire':
                    $return[$capa[0]] = $capa[1];
                    break;
                case 'sasl':
                    $return[$capa[0]] = explode(' ', $capa[1]);
                    $return['sasl_internal'] = explode(' ', strtolower(str_replace('-', '_', $capa[1])));
                    break;
                }
            }
            return $return;
        } else {
            $response = trim($response);
            if (!$response) {
                return $return;
            }
            $this->set_error('邮件服务器提示： '.$response);
            return false;
        }
    }

    /**
     * Return LIST, if mail given of this one, else complete
     *
     *[@param int $mail  Number of the mail in the list to get info about]
     * @return array|false  Array data on succes, false on failure
     */
    public function get_list($mail = false)
    {
        if ($mail) {
            $line = explode(' ', $this->talk('LIST '.$mail));
            if ('+ok' == strtolower($line[0])) {
                return array
                        ('size' => $line[2], 'recent' => true
                        ,'flagged' => false, 'answered' => false
                        ,'seen' => false, 'draft' => false
                        );
            } else {
                $this->set_error('邮件服务器提示： '.join(' ', $line));
                return false;
            }
        } else {
            $line = explode(' ', $this->talk('LIST'));
            if ('+ok' == strtolower($line[0])) {
                $return = array();
                while ($line = $this->talk_ml()) {
                    list($nummer, $bytes) = explode(' ', trim($line), 2);
                    $return[$nummer] = array
                            ('size' => $bytes, 'recent' => true
                            ,'flagged' => false, 'answered' => false
                            ,'seen' => false, 'draft' => false
                            );
                }
                foreach ($return as $num => $flags) {
                    $return[$num]['uidl'] = $this->uidl($num);
                }
                
                return $return;
            } else {
                $this->set_error('邮件服务器提示： '.join(' ', $line));
                return false;
            }
        }
    }

    // Get the header lines of a mail
    public function top($mail)
    {
        $return = '';
        $response = explode(' ', $this->talk('TOP '.$mail.' 0'));
        if ('+ok' == strtolower($response[0])) {
            while ($line = $this->talk_ml()) $return .= $line;
            return $return;
        } else {
            $this->set_error('邮件服务器提示： '.join(' ', $response));
            return false;
        }
    }

    // Get the Unique ID of a mail
    public function uidl($mail)
    {
        $response = explode(' ', $this->talk('UIDL '.$mail));
        if ('+ok' == strtolower($response[0])) {
            return $response[2];
        } else {
            $this->set_error('邮件服务器提示： '.join(' ', $response));
            return false;
        }
    }

    // Get stats of a POP3 box
    public function stat()
    {
        $return = array('mails' => false, 'size' => false);
        $response = explode(' ', $this->talk('STAT'));
        if ('+ok' == strtolower($response[0])) {
            return array('mails' => $response[1], 'size' => $response[2]);
        } else {
            $this->set_error('邮件服务器提示： '.join(' ', $response));
            return false;
        }
    }

    // Delete a selected Email from POP3 server
    public function delete($mail)
    {
        $response = explode(' ', $this->talk('DELE '.$mail));
        if ('+ok' == strtolower($response[0])) {
            return true;
        } else {
            $this->set_error('邮件服务器提示： '.join(' ', $response));
            return false;
        }
    }

    /**
     * Takes a list of UIDLs, which are to delete from the server's mailbox
     * @param  array  values are the UIDLs
     * @return  mixed  TRUE on success, FALSE on failures, array of unknown UIDLs
     * @since 3.1.9
     */
    public function delete_by_uidl($uidls)
    {
        if (!$uidls || empty($uidls)) return true;
        $response = explode(' ', $this->talk('UIDL'));
        if ('-err' == strtolower($response[0])) {
            return $uidls;
        }
        $check = array();
        while ($line = $this->talk_ml()) {
            $serv = explode(' ', trim($line));
            $check[$serv[0]] = $serv[1];
        }
        if (empty($check)) return $uidls;
        $return = array();
        foreach ($uidls as $uidl) {
            $hit = array_search($uidl, $check);
            if ($hit) {
                $this->delete($hit);
            } else {
                $return[] = $uidl;
            }
        }
        return (empty($return)) ? true : $return;
    }

    // Do nothing.
    // Since RFC1939 requires a positive response, we don't care about errors yet
    public function noop()
    {
        $this->talk('NOOP');
        return true;
    }

    // Unmark any mails marked as deleted.
    // Since RFC1939 requires a positive response, we don't care about errors yet
    public function reset()
    {
        $this->talk('RSET');
        return true;
    }

    // Send RETR command to POP3 server
    // Get subsequent server responses via talk_ml()
    public function retrieve($mail)
    {
        $response = explode(' ', $this->talk('RETR '.$mail));
        if ('+ok' == strtolower($response[0])) {
            return true;
        } else {
            $this->set_error('邮件服务器提示： '.join(' ', $response));
            return false;
        }
    }

    // Retrieve a mail from server and put into given file
    public function retrieve_to_file($mail = false, $path = false)
    {
    	//echo $path;
        $old_umask = umask(0);
        if (!$mail || !$path) {
        	//echo "a";
            $this->set_error('Usage: retrieve_to_file(integer mail, string path)');
            return false;
        }
        if (!file_exists(dirname($path)) || !is_dir(dirname($path))) {
        	//echo "b";
            $this->set_error('目录不存在 '.dirname($path));
            return false;
        }
        @chmod($path,"777"); 
        $out = fopen($path, 'w');
        //echo $out;
        if (!$out) {
            $this->set_error('文件打开失败 '.$path);
            return false;
        }
        $response = explode(' ', $this->talk('RETR '.$mail));
        if ('+ok' == strtolower($response[0])) {
            while (true) {
                $line = $this->talk_ml();
                if (false === $line) break;
                fputs($out, $line);
                //exit;
            }
            fclose($out);
            chmod($path, $GLOBALS['_PM_']['core']['file_umask']);
            umask($old_umask);
            return $path;
        } else {
            $this->set_error('邮件服务器提示： '.join(' ', $response));
            return false;
        }
    }

    // Send command to POP3 server and return first line of response
    public function talk($input = false)
    {
        if (!$input) return false;
        if ($this->_diag_session) 
           fputs($this->diag, 'C: '.$input.CRLF);
           
        fputs($this->fp, $input.CRLF);
        
        $line = fgets($this->fp, 4096);
        //echo $line."<br>";
        
        if ($this->_diag_session) 
           fputs($this->diag, 'S: '.$line);
        
        return trim($line);
    }

    // Return a line of multiline POP3 responses, return false on last line
    public function talk_ml()
    {
        $line = fgets($this->fp, 4096);
        //echo $this->fp;
        if ($this->_diag_session) fputs($this->diag, 'S: '.$line);
        if (isset($line{0}) && $line{0} == '.') {
            $line = substr($line, 1);
            if (CRLF == $line) return false;
            return $line;
        }
        return $line;
    }

    // Close POP3 connection
    public function close()
    {
        $this->talk('QUIT');
        fclose($this->fp);
        $this->fp = false;
        return true;
    }

    //
    // internal functions
    //

    // Do the actual connect to the chosen server
    protected function connect($server = '', $port = 110)
    {
        $ERRNO = $ERRSTR = false;
        $ssl_capable = function_exists('extension_loaded') && extension_loaded('openssl');
        $this->has_tls = $this->may_tls ? (function_exists('stream_socket_enable_crypto')) : false;
        if ($port == 995) {
            if (!$ssl_capable) {
                $port = 110;
            } else {
                $server = (substr($server, 0, 6) == 'ssl://') ? $server : 'ssl://'.$server;
            }
        } elseif (substr($server, 0, 6) == 'ssl://') {
            if (!$ssl_capable) $server = str_replace('ssl://', '', $server);
        }
        $this->is_ssl = (substr($server, 0, 6) == 'ssl://') ? true : false;
        $fp = @fsockopen($server, $port, $ERRNO, $ERRSTR, 1);
        if (!$fp) {
            $this->set_error('连接失败：'.$ERRSTR.' ('.$ERRNO.')');
            return false;
        }
        $this->fp = $fp;
        $response = trim(fgets($fp, 1024));
        if (strtolower(substr($response, 0, 3)) != '+ok') {
            $this->set_error($response ? '邮件服务器提示 '.$response : 'Bogus POP3 server behaviour!');
            return false;
        }
        $this->greeting = $response;
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
        // Invalid or non-existent handler
        if (!$this->fp || !is_resource($this->fp)) return false;
        $response = @socket_get_status($this->fp);
        if (!$response || $response['timed_out']) return false;
        return true;
    }

    /**
     * Implementation of SASL mechanism CRAM-MD5
     *
     * @param string  Username
     * @param string  Password
     * @return boolean  TRUE on successful authentication, FALSE otherwise
     * @access private
     */
    protected function _auth_cram_md5($user = '', $pass = '')
    {
        // See RFC2104 (HMAC, also known as Keyed-MD5)
        $response = $this->talk('AUTH CRAM-MD5');
        if (strtoupper(substr($response, 0, 2)) == '+ ') {
            // Get the challenge from the server
            $challenge = base64_decode(substr(trim($response), 2));
            // Secret to use
            $secret = $pass;
            // Rightpad with NUL bytes to have 64 chars
            if (strlen($secret) < 64) $secret = $secret.str_repeat(chr(0x00), 64 - strlen($secret));
            // In case, the secret is longer than 64 chars, md5() it
            if (strlen($secret) > 64) $secret = md5($secret);
            $ipad = str_repeat(chr(0x36), 64);
            $opad = str_repeat(chr(0x5c), 64);
            $shared = bin2hex(pack('H*', md5(($secret ^ $opad).pack('H*', md5(($secret ^ $ipad).$challenge)))));
            $response = $this->talk(base64_encode($user.' '.$shared));
            if (strtoupper(substr($response, 0, 3)) != '+OK') {
                $this->error .= 'AUTH CRAM-MD5 failed: '.trim($response).LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH CRAM-MD5 rejected: '.trim($response).LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism CRAM-SHA1 // EXPERIMENTAL
     *
     * @param  string  Username
     * @param  string  Password
     * @return  boolean  TRUE on successful authentication, FALSE otherwise
     * @access  private
     */
    protected function _auth_cram_sha1($user = '', $pass = '')
    {
        $response = $this->talk('AUTH CRAM-SHA1');
        if (strtoupper(substr($response, 0, 2)) == '+ ') {
            // Get the challenge from the server
            $challenge = base64_decode(substr(trim($response), 2));
            // Secret to use
            $secret = $pass;
            // Rightpad with NUL bytes to have 64 chars
            if (strlen($secret) < 64) $secret = $secret.str_repeat(chr(0x00), 64 - strlen($secret));
            // In case, the secret is longer than 64 chars, md5() it
            if (strlen($secret) > 64) $secret = sha1($secret);
            $ipad = str_repeat(chr(0x36), 64);
            $opad = str_repeat(chr(0x5c), 64);
            $shared = bin2hex(pack('H*', sha1(($secret ^ $opad).pack('H*', sha1(($secret ^ $ipad).$challenge)))));
            $response = $this->talk(base64_encode($user.' '.$shared));
            if (strtoupper(substr($response, 0, 3)) != '+OK') {
                $this->error .= 'AUTH CRAM-SHA1 失败: '.trim($response).LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH CRAM-SHA1 拒绝: '.trim($response).LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism CRAM-SHA256 // EXPERIMENTAL
     *
     * @param  string  Username
     * @param  string  Password
     * @return  boolean  TRUE on successful authentication, FALSE otherwise
     * @access  private
     */
    protected function _auth_cram_sh2a56($user = '', $pass = '')
    {
        $response = $this->talk('AUTH CRAM-SHA256');
        if (strtoupper(substr($response, 0, 2)) == '+ ') {
            // Get the challenge from the server
            $challenge = base64_decode(substr(trim($response), 2));
            // Secret to use
            $secret = $pass;
            // Rightpad with NUL bytes to have 64 chars
            if (strlen($secret) < 64) $secret = $secret.str_repeat(chr(0x00), 64 - strlen($secret));
            // In case, the secret is longer than 64 chars, md5() it
            if (strlen($secret) > 64) $secret = sha256($secret);
            $ipad = str_repeat(chr(0x36), 64);
            $opad = str_repeat(chr(0x5c), 64);
            $shared = bin2hex(pack('H*', sha256(($secret ^ $opad).pack('H*', sha256(($secret ^ $ipad).$challenge)))));
            $response = $this->talk(base64_encode($user.' '.$shared));
            if (strtoupper(substr($response, 0, 3)) != '+OK') {
                $this->error .= 'AUTH CRAM-SHA1 失败: '.trim($response).LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH CRAM-SHA1 拒绝: '.trim($response).LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism LOGIN
     *
     * @param  string  Username
     * @param  string  Password
     * @return  boolean  TRUE on successful authentication, FALSE otherwise
     * @access  private
     */
    protected function _auth_login($user = '', $pass = '')
    {
        $response = $this->talk('AUTH LOGIN');
        if (substr($response, 0, 2) == '+ ') {
            $response = $this->talk(base64_encode($user));
            if (substr($response, 0, 1) != '+') {
                $this->error .= 'AUTH LOGIN failed, wrong username? Aborting authentication.'.LF;
                return false;
            }
            $response = $this->talk(base64_encode($pass));
            if (strtoupper(substr($response, 0, 3)) != '+OK') {
                $this->error .= 'AUTH LOGIN failed, wrong password? Aborting authentication.'.LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH LOGIN rejected: '.trim($response).LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism PLAIN
     *
     * @param  string  Username
     * @param  string  Password
     * @return  boolean  TRUE on successful authentication, FALSE otherwise
     * @access  private
     */
    protected function _auth_plain($user = '', $pass = '')
    {
        $response = $this->talk('AUTH PLAIN '.base64_encode(chr(0).$user.chr(0).$pass));
        if (substr($response, 0, 3) != '+OK') {
            $this->error .= 'AUTH PLAIN failed: '.$response.LF;
            return false;
        }
        return true;
    }
}
?>