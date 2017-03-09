<?php
/* ------------------------------------------------------------------------- */
/* smtp.php -> Class to send mails through SMTP                */
/* supports transparent SSL connection, if openSSL extension is installed    */
/* (c) 2003-2008 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* phlyMail Common Branch                                                    */
/* v1.0.3                                                                    */
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

// Possible improvements:
// - more tolerant against bogus servers (some use the optional initial-response
//   argument for AUTH PLAIN, others do not)
// - Implement DIGEST-MD5 SASL mechanism

class smtp {
    /**
     * @access public
     */
    public $CRLF = "\r\n"; // Define standard line endings (CRLF and LF)
    public $LF   = "\n";
    public $error = false; // Init Error (query with $this->get_last_error()
    public $error_nl = 'LF'; // Multiple errors can be returned in either HTML linebreaks or plain LF
    public $authonly = false; // If set to yes, only valid SMTP AUTH connections will be used
    public $_diag_session = false; // Switch this to true for writing the session to a diagnosis file
    /**
     * @access private
     */
    public $server = false;
    public $port = false;
    public $smtp = false;
    public $def_port = 25; // Default port number to use, if not specified
    public $_SASL = array('cram_md5', 'login', 'plain', 'cram_sha256', 'cram_sha1'); // List of SASL mechanisms we support
    private $SrvMaxSize = 0;
    private $SrvAuthMech = array();
    protected $has_tls = false;
    protected $is_tls = false;
    protected $may_tls = true;

    /**
     * The smtp constructor method
     *
     * If called with a server name [and optionally a port number], it tries to
     * connect to that specific server immediately. If called without arguments,
     * you can connect by actually calling the method open_server() and let this
     * negotiate the correct server by itself.
     * If you pass a username and a password here, these will be used for
     * SMTP AUTH (if supported by server).
     *
     * @param string  Servername or IP address
     *[@param integer Port number, Default: 25]
     *[@param string  Username for SMTP AUTH]
     *[@param string  Password for SMTP AUTH]
     *[@param bool  Set to false, to disallow the use of TLS]
     */
    public function __construct($server = '', $port = 25, $username = false, $password = false, $allowtls = true)
    {
        if ($this->_diag_session) $this->diag = fopen(dirname(__FILE__).'/smtpdiag.txt', 'w');
        $this->may_tls = $allowtls;
        if ($server != '') {
            if ($this->_connect($server, $port)) {
                $this->server = $server;
                $this->port = $port;
                if ($username) $this->username = $username;
                if ($password) $this->password = $password;
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Sets a new option value. Available options and values:
     * [authonly - use SMTP AUTH only ('yes', 'no')]
     * [error_nl - use HTML linebreaks ('HTML') or plain LF ('LF')]
     *
     * @param string Parameter to set
     * @param string Value to use
     * @return boolean TRUE on success, FALSE otherwise
     * @access public
     */
    public function set_parameter($option, $value = false)
    {
        switch ($option) {
        case 'authonly':
            $this->authonly = (true === $value) ? true : false;
            break;
        case 'error_nl':
            $this->error_nl = ('HTML' == $value) ? 'HTML' : 'LF';
            break;
        default:
            $this->error .= 'Unknown option '.$option.$this->LF;
            return false;
        }
        return true;
    }

    /**
     * Read out the last error that occured
     *
     * @param void
     * @return string Returns the last error, if one exists, else an emtpy string
     * @access public
     */
    public function get_last_error()
    {
        $error = ($this->error) ? $this->error : '';
        $this->error = false;
        return ($this->error_nl == 'HTML') ? nl2br($error) : $error;
    }

    /**
     * Open a server connection
     *
     * If you've specified username and password on construction, these will be used here,
     * if you specified no server and port on construction, this method will negotiate
     * the server to be used by querying the MX root record for the first TO: address
     * passed.
     * Be aware, that using multiple TO: addresses with a negotiated SMTP server might
     * result in TO: addresses rejected due to server's No-Relay policy
     * This method makes use of the "authonly" setting
     *
     * @param string  FROM: address
     * @param array  TO: address(es)
     * @param int  Size of the message to be transferred in octets
     * @return boolean Returns TRUE on success, FALSE otherwise
     * @access public
     */
    public function open_server($from = false, $to = false, $size = 0)
    {
        if (!$from) {
            $this->error .= 'You must specify a from address'.$this->LF;
            return false;
        }
        if (!$to) {
            $this->error .= 'You must specify at least one recipient address'.$this->LF;
            return false;
        }
        if (!is_array($to)) $to = array($to);
         
        list(,$this->helodomain) = explode('@', $from);
        //echo $this->server;
        if ($this->server) {
            // We either use the global setting for the server to use (if given)...
            $mx[0] = &$this->server;
            $port[0] = isset($this->port) ? $this->port : $this->def_port;
            $user[0] = isset($this->username) ? $this->username : false;
            $pass[0] = isset($this->password) ? $this->password : false;
        } else {
            // ... or try to negotiate on our own
            list(,$querydomain) = explode('@', $to[0], 2);
            // On Windows systems this function is not available
            if (!function_exists('getmxrr')) {
                $this->error .= 'No SMTP servers for '.$querydomain.' found'.$this->LF;
                //echo $this->error;
                return false;
            }
             
            if (getmxrr($querydomain, $mx, $weight) == 0) {
                $this->error .= 'No SMTP servers for '.$querydomain.' found'.$this->LF;
                return false;
            }
            array_multisort($mx, $weight);
        }
        //print_rr($mx);
        // Now trying to find one server to talk to... first come, first serve
        foreach ($mx as $id => $host) {
            if (!isset($port[$id])) $port[$id] = $this->def_port;
            // If we can't connect, try next server in list
            if (!$this->smtp && !$this->_connect($host, $port[$id])) continue;
            /**
             * Some servers, namely the qmail ones reject the first line, so we simply try to cycle through
             * the first handshake twice. In case of normal servers we break out of this loop once the
             * handshake was successfull. Additionally this loop is used to try starting a TLS connection,
             * which requires us to run through the EHLO/HELO dialogue twice - once to find out, whether the server
             * has TLS enabled, the second time directly after sending STARTTLS.
             */
            for ($i = 0; $i < 2; ++$i) {
                // Handshake with the server, identify ourselves, get capabilities
                $response = $this->talk('EHLO '.$this->helodomain);
                if (substr($response, 0, 3) == '250') {
                    // Server supports SMTP AUTH... try the supported mechanisms to authenticate
                    $supported = $this->get_supported_sasl_mechanisms($response);
                    // Find the mechanisms supported on both sides
                    $SASL = array_intersect($this->_SASL, $supported);
                    // RFC1870
                    $this->SrvMaxSize = $this->get_server_maxsize($response);
                    // Server supports TLS, PHP has it, too -> try switching over to it
                    if (!$this->is_tls && $this->has_tls && $this->get_server_has_tls($response)) {
                        $response = $this->talk('STARTTLS');
                        if (!stream_socket_enable_crypto($this->smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                            $this->close();
                            $this->set_error('Cannot enable TLS, although server advertises it');
                            $this->has_tls = false;
                            continue;
                        } else {
                            $this->is_tls = true;
                            $i--; // After establishing TLS we have to restart the EHLO / HELO dialogue
                            continue;
                        }
                    }
                    break;
                } else {
                    if ($i == 0) continue;
                    $response = $this->talk('HELO '.$this->helodomain);
                    if (substr($response, 0, 3) != '250') {
                        $this->close();
                        $this->error .= 'HELO '.$this->helodomain.' failed. Aborting connection'.$this->LF;
                        continue 2;
                    }
                    $SASL = array();
                    $this->SrvMaxSize = 0;
                }
            }

            // Server supports RFC1870 (SIZE extension) and mail size has been given
            if ($size && $this->SrvMaxSize && $size > $this->SrvMaxSize) {
                $this->close();
                $this->error .= 'Message size exceeds server\'s known upper limit'.$this->LF;
                continue;
            }

            // We've got credentials and try to use SMTP AUTH
            if (isset($user[$id]) && $user[$id]) {
                $this->is_auth = false;
                foreach ($SASL as $v) {
                    $function_name = '_auth_'.$v;
                    if ($this->{$function_name}($user[$id], $pass[$id])) {
                        $this->error = false;
                        $this->is_auth = true;
                        break;
                    }
                }
                if (!$this->is_auth && $this->authonly) {
                    $this->close();
                    $this->error .= 'SMTP-AUTH failed. Aborting connection'.$this->LF;
                    return false;
                }
            }
            if ($this->init_mail_transfer($from, $to, $size)) return true;
        }
        return false;
    }

    protected function init_mail_transfer($from = false, $to = false, $size = 0)
    {
        if (!$from) {
            $this->error .= 'You must specify a from address'.$this->LF;
            return false;
        }
        if (!$to) {
            $this->error .= 'You must specify at least one recipient address'.$this->LF;
            return false;
        }
        if (!is_array($to)) $to = array($to);

        $response = $this->talk('MAIL FROM: <'.$from.'>'.($size && $this->SrvMaxSize ? ' SIZE='.intval($size) : ''));
        if (substr($response, 0, 3) != '250') {
            $this->close();
            if (substr($response, 0, 3) == '452') {
                $this->error .= $response.$this->LF;
            } elseif (substr($response, 0, 3) == '552') {
                $this->error .= $response.$this->LF;
            } else {
                $this->error .= 'FROM address '.$from.' rejected by server: '.$response.$this->LF;
            }
            return false;
        }
        $accepted = 0;
        foreach ($to as $k => $val) {
            $response = $this->talk('RCPT TO: <'.$val.'>');
            //echo $response;exit;
            // All return codes of 25* mean, that the address is accepted
            if (substr($response, 0, 2) == '25') $accepted = 1;
            else $failed[] = $this->LF.'- '.$val.': '.trim($response);
        }
        if (0 == $accepted) {
            $this->close();
            $this->error .= 'None of the TO addresses were accepted: '.join(',', $failed).$this->LF;
            return false;
        }
        $response = $this->talk('DATA');
        if (substr($response, 0, 3) != '354') {
            $this->close();
            $this->error .= 'Server rejected the DATA command: '.trim($response).$this->LF;
            return false;
        } else {
            if (isset($failed)) {
                $this->error .= 'Some of the TO addresses were rejected: '.join(',', $failed).$this->LF;
            }
            return true;
        }
    }

    /**
     * Write to the SMTP stream opened before by open_server()
     *
     * @param string Line of data to put to the stream
     * @return boolean Returns TRUE on success, FALSE otherwise
     * @access public
     */
    public function put_data_to_stream($line = false)
    {
        if (!is_resource($this->smtp)) return false;
        if (!$line) return false;
        fwrite($this->smtp, rtrim($line).$this->CRLF);
        if ($this->_diag_session) fwrite($this->diag, 'C:'.$line);
        return true;
    }

    /**
     * Finishing a mail transfer to the server
     * Use this method, if your application doesn't automatically
     * put the final CRLF.CRLF to the SMTP stream after
     * putting al the mail data to it.
     * This method implicitly calls check_success().
     *
     * @param  void
     * @return boolean Return state of check_success()
     * @access public
     */
    public function finish_transfer()
    {
        fwrite($this->smtp, $this->CRLF.'.'.$this->CRLF);
        if ($this->_diag_session) fwrite($this->diag, 'C: '.$this->CRLF.'C: .'.$this->CRLF);
    }

    /**
     * Call this method after putting your last mail line to the server
     *
     * @param void
     * @return boolean Returns TRUE on success, FALSE otherwise
     * @access public
     */
    public function check_success()
    {
        if (!is_resource($this->smtp) || feof($this->smtp)) {
            $line = '999 SMTP server connection already died';
        } else {
            $line = fgets($this->smtp, 4096);
            if ($this->_diag_session) fwrite($this->diag, 'S: '.$line);
        }
        if (substr($line, 0, 3) != '250') {
            $this->error .= 'Wrong DATA: '.trim($line).$this->LF;
            return false;
        }
        return true;
    }

    /**
     * Talk to the SMTP server directly (for things not covered by this class)
     *
     * @param  string Command to pass to the server
     * @return string Answer of the server
     * @access public
     */
    public function talk($input, $oneliner = false)
    {
        if ($this->_diag_session) fputs($this->diag, 'C: '.$input.$this->CRLF);
        $output = false;
        fputs($this->smtp, $input.$this->CRLF);
        $end = 0;
        while (0 == $end && is_resource($this->smtp)) {
            $line = fgets($this->smtp, 4096);
            if ($this->_diag_session) fputs($this->diag, 'S: '.$line);
            if ($oneliner) $end = 1;
            if (' ' == substr($line, 3, 1)) $end = 1;
            $output .= $line;
        }
        return $output;
    }

    /**
     * Close a previously opened connection
     * Although it doesn't return you something, you can query the state by using
     * get_last_error()
     *
     * @param  void
     * @return void
     * @access public
     */
    public function close()
    {
        if ($this->_diag_session) fclose($this->diag);
        if (is_resource($this->smtp)) {
            $this->talk('QUIT');
            fclose($this->smtp);
            $this->smtp = false;
            $this->error .= 'Connection closed'.$this->LF;
        } else {
            $this->error .= 'No connection to close. Did nothing.'.$this->LF;
        }
    }

    /**
     * Open socket to an SMTP server
     *
     * @param    string    Server name or IP address
     * @param    integer   Port number
     * @return   boolean   TRUE on success, FALSE otherwise
     * @access   private
     */
    private function _connect($server, $port)
    {
        $response = false;
        $ssl_capable = function_exists('extension_loaded') && extension_loaded('openssl');
        $this->has_tls = $this->may_tls ? (function_exists('stream_socket_enable_crypto')) : false;
        if ($port == 465) {
            if (!$ssl_capable) {
                $port = 25;
            } else {
                $server = (substr($server, 0, 6) == 'ssl://') ? $server : 'ssl://'.$server;
            }
        } elseif (substr($server, 0, 6) == 'ssl://') {
            if (!$ssl_capable) $server = str_replace('ssl://', '', $server);
        }
        $smtp = @fsockopen($server, $port, $errno, $errstr, 15);
        //echo $smtp.$server;
        if (!$smtp) {
            $this->error .= 'No connect to '.$server.':'.$port.' ('.$errno.' '.$errstr.')'.$this->LF;
            //echo $port;exit;
            //echo $this->error.$port;
            return false;
        }
        $this->smtp = $smtp;

        $end = 0;
        while (0 == $end) {
            $line = fgets($this->smtp, 4096);
            if (' ' == substr($line, 3, 1)) $end = 1;
            $response .= $line;
        }
        if (!$response || substr($response, 0, 3) != '220') {
            $this->close();
            $this->error .= 'Connecting to '.$server.':'.$port.' failed ('.$response.')'.$this->LF;
            return false;
        }
        return true;
    }

    /**
     * Find out about SASL mechanisms a specific SMTP server supports
     *
     * @param    string    Server answer to EHLO command
     * @return   array     list of supported SASL mechanisms
     * @access   private
     */
    private function get_supported_sasl_mechanisms($response)
    {
        if (preg_match('!^250(\ |\-)AUTH(\ |\=)([\w\s-_]+)$!Umi', $response, $found)) {
            $found[3] = strtolower(str_replace('-',  '_',  trim($found[3])));
            return explode(' ', $found[3]);
        }
        return array();
    }

    /**
     * Negotiate, whether this server supports RFC1870 (Message Size Declaration)
     *
     * @param    string    Server answer to EHLO command
     * @return   int    maximum size, defaults to 0 if not known or not supported
     * @access   private
     */
    private function get_server_maxsize($response)
    {
        if (preg_match('!^250(\ |\-)SIZE(\ |\=)([0-9]+)$!Umi', $response, $found)) {
            return $found[3];
        }
        return 0;
    }

    private function get_server_has_tls($response)
    {
        return preg_match('!^250(\ |\-)STARTTLS!Umi', $response);
    }

    /**
     * Implementation of SASL mechanism CRAM-MD5
     *
     * @param    string    Username
     * @param    string    Password
     * @return   boolean   TRUE on successful authentication, FALSE otherwise
     * @access   private
     */
    private function _auth_cram_md5($user = '', $pass = '')
    {
        // See RFC2104 (HMAC, also known as Keyed-MD5)
        $response = $this->talk('AUTH CRAM-MD5');
        if (substr($response, 0, 3) == '334') {
            // Get the challenge from the server
            $challenge = base64_decode(substr(trim($response), 4));
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
            if (substr($response, 0, 3) != '334' && substr($response, 0, 3) != '235') {
                $this->error .= 'AUTH CRAM-MD5 failed:'.trim($response).$this->LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH CRAM-MD5 rejected: '.trim($response).$this->LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism CRAM-SHA1 // EXPERIMENTAL
     *
     * @param    string    Username
     * @param    string    Password
     * @return   boolean   TRUE on successful authentication, FALSE otherwise
     * @access   private
     */
    private function _auth_cram_sha1($user = '', $pass = '')
    {
        $response = $this->talk('AUTH CRAM-SHA1');
        if (substr($response, 0, 3) == '334') {
            // Get the challenge from the server
            $challenge = base64_decode(substr(trim($response), 4));
            // Secret to use
            $secret = $pass;
            // Rightpad with NUL bytes to have 64 chars
            if (strlen($secret) < 64) $secret = $secret.str_repeat(chr(0x00), 64 - strlen($secret));
            // In case, the secret is longer than 64 chars, sha1() it
            if (strlen($secret) > 64) $secret = sha1($secret);

            $ipad = str_repeat(chr(0x36), 64);
            $opad = str_repeat(chr(0x5c), 64);
            $shared = bin2hex(pack('H*', sha1(($secret ^ $opad).pack('H*', sha1(($secret ^ $ipad).$challenge)))));

            $response = $this->talk(base64_encode($user.' '.$shared));
            if (substr($response, 0, 3) != '334' && substr($response, 0, 3) != '235') {
                $this->error .= 'AUTH CRAM-SHA1 failed:'.trim($response).$this->LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH CRAM-SHA1 rejected: '.trim($response).$this->LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism CRAM-SHA256 // EXPERIMENTAL
     *
     * @param    string    Username
     * @param    string    Password
     * @return   boolean   TRUE on successful authentication, FALSE otherwise
     * @access   private
     */
    private function _auth_cram_sha256($user = '', $pass = '')
    {
        // See RFC2104 (HMAC, also known as Keyed-MD5)
        $response = $this->talk('AUTH CRAM-SHA256');
        if (substr($response, 0, 3) == '334') {
            // Get the challenge from the server
            $challenge = base64_decode(substr(trim($response), 4));
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
            if (substr($response, 0, 3) != '334' && substr($response, 0, 3) != '235') {
                $this->error .= 'AUTH CRAM-SHA256 failed:'.trim($response).$this->LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH CRAM-SHA256 rejected: '.trim($response).$this->LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism LOGIN
     *
     * @param    string    Username
     * @param    string    Password
     * @return   boolean   TRUE on successful authentication, FALSE otherwise
     * @access   private
     */
    private function _auth_login($user = '', $pass = '')
    {
        $response = $this->talk('AUTH LOGIN');
        
        if (substr($response, 0, 3) == '334') {
            $response = $this->talk(base64_encode($user));
            //echo $user."------";
           // echo $response."<br>";
            if (substr($response, 0, 3) != '334') {
            	//echo "dddddddddddddddddddddd";exit;
                $this->error .= 'AUTH LOGIN failed, wrong username? Aborting authentication.'.$this->LF;
                return false;
            }
            $response = $this->talk(base64_encode($pass));
            //$response = $this->talk($pass);
            //echo $response."<br>";
            //echo $pass."<br>";
            //exit;
            if (substr($response, 0, 3) != '235') {
            //	echo "cccccccccdddddddddddddddddddddd";
            	//exit;
                $this->error .= 'AUTH LOGIN failed, wrong password? Aborting authentication.'.$this->LF;
                return false;
            }
            return true;
        } else {
            $this->error .= 'AUTH LOGIN rejected: '.trim($response).$this->LF;
            return false;
        }
    }

    /**
     * Implementation of SASL mechanism PLAIN
     *
     * @param    string    Username
     * @param    string    Password
     * @return   boolean   TRUE on successful authentication, FALSE otherwise
     * @access   private
     */
    private function _auth_plain($user = '', $pass = '')
    {
        $response = $this->talk('AUTH PLAIN '.base64_encode(chr(0).$user.chr(0).$pass));
        if (substr($response, 0, 3) != '235') {
            $this->error .= 'AUTH PLAIN failed. Aborting authentication.'.$this->LF;
            return false;
        }
        return true;
    }
}
?>