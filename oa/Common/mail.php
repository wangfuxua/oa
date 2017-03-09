<?
    /**
    * Find an icon within a given path for a given MIME type. This method first tries to
    * match a filename for the exact MIME type (e.g. message/rfc822), if not successfull,
    * for the main type (e.g. message/), then the generic icon. Naming conventions are as
    * follows:
    * - Specific icons are named <main_type>_<subtype>.<extension>
    * - Main type icons are named <main_type>_.<extension>
    * - The generic icon is named __.<extension>
    * where <extension> is one of the usual web file formats (JPG, GIF, PNG).
    * The third parameter allows to specify a scoring of the extensions. The first found is
    * returned.
    *
    * @param  string  Path to search in (WITHOUT trailing slash!)
    * @param  string  MIME type as search pattern
    * @param  array  List of allowed extensions in preferred order (e.g. array('gif', 'png', 'jpg'))
    * @return  string Path to the best fitting icon file; FALSE on failure
    * @since 1.1.2
    * @access public
    */
    function get_icon_from_type($path, $type, $order = array())
    {
        if (!$type) $type = 'no/thing';
        if (!strstr($type, '/')) {
            $maintype = $type;
            $subtype = '';
        } else {
            list ($maintype, $subtype) = explode('/', $type);
        }

        if (!isset($this->dircache[$path])) {
            if (!file_exists($path)) return false; // No files to find since the dir is not there
            $d = opendir($path);
            while (false !== ($filename = readdir($d) ) ) {
                if ('.' == $filename)            continue;
                if ('..' == $filename)           continue;
                if (is_dir($path.'/'.$filename)) continue;
                $list[] = $filename;
            }
            closedir($d);
            $this->dircache[$path] = $list;
            unset($list);
        }

        $exact   = array();
        $main    = array();
        $generic = array();
        foreach ($this->dircache[$path] as $filename) {
            if (preg_match('!^'.preg_quote(str_replace('/', '_', $type), '!').'\.!', $filename)) {
                $exact[] = $filename;
            }
            if (preg_match('!^'.$maintype.'_\.!', $filename)) {
                $main[] = $filename;
            }
            if (preg_match('!^__\.!', $filename)) {
                $generic[] = $filename;
            }
        }
        // Glue the found entries together - obey the level of detail
        $icons = array_merge($exact, $main, $generic);

        if (empty($icons)) return false; // We did not find anything
        if (empty($order)) return $icons[0]; // Choose any...

        // Try to match found files against the allowed & preferred extensions
        foreach ($icons as $filename) {
            foreach ($order as $ext) {
                if (preg_match('!\.'.preg_replace('![^a-zA-Z0-9]!', '', $ext).'$!', $filename)) {
                    return $filename;
                }
            }
        }
        // This line should only be reached, when an extension list is given, but the found
        // icons do not match any of these
        return false;
    }
    
/**
* Meant for formatting byte values (file / mail sizes and the like) to make a sensible but short output
* Capable of GB, MB, B(ytes)
* @param  int  The value to format
* @param  bool  Set to true, the spacer between the number and the "B(yte)" will be just a space else &nbsp;
* @param  bool  true for Bytes, MBytes, ...; else B, MB, ... will be output
* @since 3.0.0
*/
function size_format($size = '', $plain = false, $long = false)
{
    $n = ($plain) ? ' ' : '&nbsp;';
    $b = (!$long) ? 'B' : 'Bytes';
    if (floor($size/1073741824) > 0) {
        return number_format(($size/1073741824), 1, $GLOBALS['WP_msg']['dec'], $GLOBALS['WP_msg']['tho']) . $n.'G'.$b;
    } elseif (floor($size/1048576) > 0) {
        return number_format(($size/1048576), 1, $GLOBALS['WP_msg']['dec'], $GLOBALS['WP_msg']['tho']) . $n.'M'.$b;
    } elseif (floor($size/1024) > 0) {
        return number_format(($size/1024), 1, $GLOBALS['WP_msg']['dec'], $GLOBALS['WP_msg']['tho']) . $n.'K'.$b;
    } else return trim(floor($size)) . $n.$b;
}

function wash_size_field($size = '0')
{
    $size = preg_replace('![\ \.,]!', '', $size);
    $size = preg_replace('!^([^0-9]*)([0-9]+)(m|k){0,1}!i', '\\2 \\3', $size);
    $parts = split(' ', $size, 2);
    if (!isset($parts[1])) return $parts[0];
    switch ($parts[1]) {
    case 'M':
    case 'm':
        $size = $parts[0] * 1024 * 1024;
        break;
    case 'k':
    case 'K':
        $size = $parts[0] * 1024;
        break;
    default:
        $size = $parts[0];
    }
    return $size;
}

// Encrypt a string
// Input:   confuse(string $data, string $key);
// Returns: encrypted string
function confuse($data = '', $key = '')
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
function deconfuse($data = '', $key = '')
{
    $data = base64_decode($data);
    $decoded = '';  $DataLen = strlen($data);
    if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
    for($i = 0; $i < $DataLen; ++$i) {
        $decoded .= chr((256 + ord($data{$i}) - ord($key{$i})) % 256);
    }
    return $decoded;
}

/**
* Convert a string from any of various encodings to UTF-8
*
* @param  string  String to encode
* [@param  string  Encoding; Default: ISO-8859-1]
* [@param  bool  Safe Mode: if set to TRUE, the original string is retunred on errors]
* @return  string  The encoded string or false on failure
* @since 3.0.5
*/
function encode_utf8($string = '', $encoding = 'iso-8859-1', $safe_mode = false)
{
    $safe = ($safe_mode) ? $string : false;
    if (strtoupper($encoding) == 'UTF-8' || strtoupper($encoding) == 'UTF8') {
        return $string;
    } elseif (strtoupper($encoding) == 'ISO-8859-1') {
        return utf8_encode($string);
    } elseif (strtoupper($encoding) == 'WINDOWS-1252') {
        return utf8_encode(map_w1252_iso8859_1($string));
    } elseif (strtoupper($encoding) == 'UNICODE-1-1-UTF-7') {
        $encoding = 'utf-7';
    }
    if (function_exists('mb_convert_encoding')) {
        $conv = @mb_convert_encoding($string, 'UTF-8', strtoupper($encoding));
        if ($conv) return $conv;
    }
    if (function_exists('iconv')) {
        $conv = @iconv(strtoupper($encoding), 'UTF-8', $string);
        if ($conv) return $conv;
    }
    if (function_exists('libiconv')) {
        $conv = @libiconv(strtoupper($encoding), 'UTF-8', $string);
        if ($conv) return $conv;
    }
    return $safe;
}

/**
* Convert a string from UTF-8 to any of various encodings
*
* @param  string  String to decode
* [@param  string  Encoding; Default: ISO-8859-1]
* [@param  bool  Safe Mode: if set to TRUE, the original string is retunred on errors]
* @return  string  The decoded string or false on failure
* @since 3.0.5
*/
function decode_utf8($string = '', $encoding = 'iso-8859-1', $safe_mode = false)
{
    $safe = ($safe_mode) ? $string : false;
    if (!$encoding) $encoding = 'ISO-8859-1';
    if (strtoupper($encoding) == 'UTF-8' || strtoupper($encoding) == 'UTF8') {
        return $string;
    } elseif (strtoupper($encoding) == 'ISO-8859-1') {
        return utf8_decode($string);
    } elseif (strtoupper($encoding) == 'WINDOWS-1252') {
        return map_iso8859_1_w1252(utf8_decode($string));
    } elseif (strtoupper($encoding) == 'UNICODE-1-1-UTF-7') {
        $encoding = 'utf-7';
    }
    if (function_exists('mb_convert_encoding')) {
        $conv = @mb_convert_encoding($string, strtoupper($encoding), 'UTF-8');
        if ($conv) return $conv;
    }
    if (function_exists('iconv')) {
        $conv = @iconv('UTF-8', strtoupper($encoding), $string);
        if ($conv) return $conv;
    }
    if (function_exists('libiconv')) {
        $conv = @libiconv('UTF-8', strtoupper($encoding), $string);
        if ($conv) return $conv;
    }
    return $safe;
}

/**
* Special treatment for our guys in Redmond
* Windows-1252 is basically ISO-8859-1 -- with some exceptions, which get accounted for here
* @param  string  Your input in Win1252
* @param  string  The resulting ISO-8859-1 string
* @since 3.0.8
*/
function map_w1252_iso8859_1($string = '')
{
    if ($string == '') return '';
    $return = '';
    for ($i = 0; $i < strlen($string); ++$i) {
        $c = ord($string{$i});
        switch ($c) {
            case 129: $return .= chr(252); break;
            case 132: $return .= chr(228); break;
            case 142: $return .= chr(196); break;
            case 148: $return .= chr(246); break;
            case 153: $return .= chr(214); break;
            case 154: $return .= chr(220); break;
            case 225: $return .= chr(223); break;
            default: $return .= chr($c); break;
        }
    }
    return $return;
}

/**
* Special treatment for our guys in Redmond
* Windows-1252 is basically ISO-8859-1 -- with some exceptions, which get accounted for here
* @param  string  Your input in ISO-8859-1
* @param  string  The resulting Win1252 string
* @since 3.0.8
*/
function map_iso8859_1_w1252($string = '')
{
    if ($string == '') return '';
    $return = '';
    for ($i = 0; $i < strlen($string); ++$i) {
        $c = ord($string{$i});
        switch ($c) {
            case 196: $return .= chr(142); break;
            case 214: $return .= chr(153); break;
            case 220: $return .= chr(154); break;
            case 223: $return .= chr(225); break;
            case 228: $return .= chr(132); break;
            case 246: $return .= chr(148); break;
            case 252: $return .= chr(129); break;
            default: $return .= chr($c); break;
        }
    }
    return $return;
}

/**
* Compares to version of a specific software with each other. Both arguments should follow the same versioning scheme
* @param string  Version of the software, which is currently in use or checked for
* @param string  Minimum required version for a certain functionality
* @return bool  TRUE, if the required version is there, FALSE if not
* @since 3.2.2
*/
function PHM_version_compare($is, $should)
{
    $is = preg_replace('![^0-9\.]!', '', $is);
    $is = explode('.', $is);

    $should = preg_replace('![^0-9\.]!', '', $should);
    $should = explode('.', $should);

    foreach ($should as $k => $v) {
        if (!isset($is[$k])) $is[$k] = 0;
        if ($should[$k] < $is[$k]) return true;
        if ($should[$k] > $is[$k]) return false;
    }
    return true;
}

/**
* Function to join _PM_ with other config sources (like user settings).
* Since array_merge canonly merge flat arrays and array_merge_recursive appends doublettes
* to the father element we have to do the merge "manually"
* @param array  inital _PM_ array
* @param array  Data to join the array with (identical structure!)
* @return array  Merged _PM_ array
* @since 3.2.4
*/
function merge_PM($_PM_, $import)
{
	if (!is_array($import) || empty($import)) return $_PM_;
    foreach ($import as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $k2 => $v2) $_PM_[$k][$k2] = $v2;
        } else $_PM_[$k] = $v;
    }
    return $_PM_;
}


/**
* Outputs Javascript to the client to allow communication between frontend and application
*
* @since 3.3.6
* @param string Javascript command / JSON string to send
* @param bool Whether this is JSON or real javascript code; Default: TRUE
* @param bool Whether to exit right after sending the output; Defautl true
* @return void
*/
function sendJS($command, $is_json = true, $exit = true)
{
    if (!headers_sent()) {
        header('ETag: PUB' . time());
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()-10) . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 5) . ' GMT');
        header('Pragma: no-cache');
        header('Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate');
        header('Content-Type: '.($is_json ? 'application/json' : 'text/javascript').'; charset=utf-8');
    }
    echo $command, LF;
    if ($exit) exit;
}


function parse_email_address($address="",$shorten_to = 0){
	$address = str_replace('"', '', $address);
    if (preg_match('!^(.+)<(.+)>$!', trim($address), $found)) {
            // Real Name <Em@il>
            if ($shorten_to && strlen($found[1]) > $shorten_to) {
                $found[1] = substr($found[1], 0, ($shorten_to - 3)) . '...';
            }
            //$address= str_replace("<","",$address);
            //$address= str_replace(">","",$address);
            return $address;
    }elseif (preg_match('!(.+)\((.+?)\)!U', trim($address), $found)){
    	
    }else {
            $address= str_replace("<","",$address);
            $address= str_replace(">","",$address);
            return $address;    	
    }
        	
	
}


function un_html($return = '')
{
    return preg_replace
           (array('!&gt;!i', '!&lt;!i', '!&quot;!i', '!&amp;!i', '!&nbsp;!i', '!&copy;!i')
           ,array('>', '<', '"', '&', ' ', '(c)')
           ,$return
           );
}

/**
* Converts plain text to HTML, underlines links
* @param  string  Text to convert
* @return  string  HTMLized text
* @since 3.1.7
*/
function text2html($text)
{
	$text = nl2br(htmlspecialchars($text));
    // Emailadressen
    $text = preg_replace
            ('!(mailto:)([^\s<>"?]+)(\?subject=([^\s<>"]+))?!i'
            ,'<a href="\1\2\3">\2</a>'
            ,$text
            );
    // Internet-Protokolle:
    $text = preg_replace
            ('!(http://|https:|ftp://|gopher://|news:|www\.)(.+)(?=<|>|\W[\s\n\r]|[\s\r\n]|$)!Umi'
            ,'<a href="\\1\\2">\\1\\2</a>'
            ,$text.LF
            );
    return $text;
}




/* ------------------------------------------------------------------------- */
/* lib/message.encode.php - phlyMail 3.0.0+                                  */
/* Routines related to encoding emails                                       */
/* (c) 2001-2006 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v3.4.5                                                                    */
/* ------------------------------------------------------------------------- */

/**
 * Creates a complete mail header based on the input
 *
 * @param array $in standard headers to, cc, bcc, subject
 * @param string $additional  application created header lines (already standards compliant!)
 * @param string $encoding  Encoding for RFC1522
 * @param array $userheaders  Header lines created by the user, cleaned by this function
 * @param bool $uh_before  insert userland headers before or after the standard headers
 * @return string
 */
function create_messageheader($in, $additional = '', $encoding = 'iso-8859-1', $userheaders = array(), $uh_before = false)
{
    $return = '';
    // Instantiate IDNA class
    $IDN = new idna_convert();
    // Clean and encode standard headers
    foreach (array('from', 'to', 'cc', 'bcc', 'subject') as $key) {
        if (!isset($in[$key])) {
            $in[$key] = '';
            continue;
        }
        $value = $in[$key];
        if (!$value) continue;
        if (trim($value) && !$value) continue;
        // Suche nach QP-Teilen
        if (preg_match('/[\x80-\xff]/', $value)) {
            switch ($key) {
            case 'subject': $cmode = 'g'; break;
            case 'from': case 'to': case 'cc': case 'bcc':
                $cmode = '@';
                // Replace IDNs
                $address = preg_replace('!\(.+\)!U', '', $value);
                $address = split(',', str_replace(' ', '', $address));
                foreach ($address as $v) {
                    $value = str_replace($v, $IDN->encode($v), $value);
                }
            break;
            default: $cmode = 'n'; break;
            }
            $in[$key] = encode_1522_line_q(decode_utf8(rtrim($value), $encoding), $cmode, $encoding);
        }
    }
    // Clean and encode user header lines
    $userheader = '';
    foreach ($userheaders as $key => $value) {
        $value = preg_replace('!\r|\n!', '', $value);
        if (!$value) continue;
        $key = preg_replace('![^\x21-\x39\x3B-\x7e]!', '', $key);
        $userheader .= $key.': '.encode_1522_line_q(decode_utf8(rtrim($value), $encoding), 'n', $encoding).CRLF;
    }
    // Create Message ID
    if (!strstr($additional, 'Message-ID: <')) {
        $return .= create_msgid(isset($in['from']) ? $in['from'] : false);
    }

    $return .= 'Date: '.date('r').CRLF;
    if ($additional) {
        $return .= rtrim(preg_replace('!('.CRLF.')+!', CRLF, $additional)).CRLF;
    }
    // Default header lines
    if ($in['from']) {
        $return .= 'From: '.$in['from'].CRLF
                .'Return-Path: <'.mailparser::parse_email_address($in['from'], 0, false, true).'>'.CRLF;
    }
    if ($in['to']) $return .= 'To: '.$in['to'].CRLF;
    if ($in['cc']) $return .= 'Cc: '.$in['cc'].CRLF;
    if ($in['bcc']) $return .= 'Bcc: '.$in['bcc'].CRLF;
    if ($in['subject']) $return .= 'Subject: '.$in['subject'].CRLF;
    $return .= 'X-Mailer: kdoaMail (http://mail.7e73.com)'.CRLF;
    // Put userspace header lines into mail header
    if ($userheader) {
        if ($uh_before) {
            $return = $userheader.$return;
        } else {
            $return .= $userheader;
        }
    }
    return $return;
}

// Create Message ID header
function create_msgid($from = false)
{
    if ($from) {
        $addi = mailparser::parse_email_address($from, 0, false, true);
        $dom = strstr($addi, '@');
    } elseif (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
        $dom = '@'.$_SERVER['SERVER_NAME'];
    } else { // This is failsafe only
        $dom = '@phlymail.local';
    }
    return 'Message-ID: <'.uniqid(time().'.').$dom.'>'.CRLF;
}

/**
 * This function encodes hader line values, which may contain non 7bit characters
 * As of version 3.4.5 of this file base64 or QP encoding is automatically chosen by
 * lookin for an amount of characters to encode which is more than about 1/3 of the
 * value length.
 *
 * @param string $coded The string to encode
 * @param string $cmode Pass 'g' for the whole string to encode, '@' for email addresses, otherwise
 *     the returned string will be encoded word by word (QP only)
 * @param string $encoding The character encoding the string is in
 * @return string
 */
function encode_1522_line_q($coded = '', $cmode = 'g', $encoding = 'iso-8859-1')
{
    preg_match_all('![^\t\x20\x2E\041-\074\076-\176]!', $coded, $found, PREG_SET_ORDER);
    if (sizeof($found) > strlen($coded)/3) {
        $chunklen = (isset($GLOBALS['_PM_']['core']['longsubjects'])) ? 988 : 76;
        $coded = chunk_split(base64_encode(rtrim($coded)), ($chunklen - strlen('=?'.$encoding.'?B??=')));
        $coded = str_replace('='.CRLF, '?='.CRLF.' =?'.$encoding.'?B?', trim($coded));
        $coded = '=?'.$encoding.'?B?'.$coded.'?='.CRLF;
        return rtrim($coded);
    }
    if ('g' == $cmode) {
        $chunklen = (isset($GLOBALS['_PM_']['core']['longsubjects'])) ? 988 : 76;
        $coded = str_replace('?', '=3F', quoted_printable_encode(str_replace(' ', '_', rtrim($coded)), ($chunklen - strlen('=?'.$encoding.'?Q??='))));
        $coded = str_replace('='.CRLF, '?='.CRLF.' =?'.$encoding.'?Q?', trim($coded));
        $coded = '=?'.$encoding.'?Q?'.$coded.'?='.CRLF;
        return rtrim($coded);
    } elseif ('@' == $cmode) {
        $zeilen = explode(CRLF, $coded);
        $coded = '';
        foreach ($zeilen as $key => $value) {
            if (!$value) continue;
            if ($key > 0) $coded .= "\t";
            unset ($words);
            $words = explode(' ', $value, 2);
            foreach ($words as $k => $word) {
                if (preg_match('/[\x80-\xff]/', $word) && preg_match('/\(|\)/', $word)) {
                    $words[$k] = preg_replace
                            ('/^(\()?([^\)]+)(\))?$/ie'
                            ,"'(=?".$encoding."?Q?'.rtrim(str_replace('?', '=3F', quoted_printable_encode(str_replace(' ', '_', '\\2')))).'?=)'"
                            ,$word
                            );
                }
            }
            $coded .= join(' ', $words).CRLF;
        }
        return rtrim($coded);
    } else {
        $zeilen = explode(CRLF, $coded);
        $coded = '';
        foreach ($zeilen as $key => $value) {
            if (!$value) continue;
            if ($key > 0) $coded .= "\t";
            unset ($words);
            $words = explode(' ', $value);
            foreach ($words as $k => $word) {
                if (preg_match('/[\x80-\xff]/', $word)) {
                    $words[$k] = '=?'.$encoding.'?Q?'.rtrim(str_replace('?', '=3F', quoted_printable_encode(str_replace(' ', '_', $word)))).'?=';
                }
            }
            $coded .= join(' ', $words).CRLF;
        }
        return rtrim($coded);
    }
}

function put_attach_stream(&$stream, $filename = '', $type = 'application/octet-stream', $name = 'unknown', $LE = CRLF)
{
    $type = trim($type);
    $name = basename(trim($name));
    $bytes_block = 57;
    $mine=new mime(); 
    // This should use the filename for finding the correct MIME type
    if ('application/octet-stream' == $type && 'unknown' != $name) {
        $ntype = $mine->get_type_from_name($name, false);
        if ($ntype[0]) $type = $ntype[0];
    }
    $encoding = $mine->get_encoding_from_type($type);
    $fh_src = fopen($filename, 'r');
    if ($fh_src) {
        $stream->put_data_to_stream('Content-Type: '.$type.'; name="'.$name.'"'.$LE);
        $stream->put_data_to_stream('Content-Disposition: attachment; filename="'.$name.'"'.$LE);
        switch ($encoding) {
        case 'q':
            $stream->put_data_to_stream('Content-Transfer-Encoding: quoted-printable'.$LE.$LE);
            while ($line = fgets($fh_src)) $stream->put_data_to_stream(quoted_printable_encode($line));
            break;
        case '8':
        case '7':
            $stream->put_data_to_stream('Content-Transfer-Encoding: '.$encoding.'bit'.$LE.$LE);
            while ($line = fgets($fh_src)) $stream->put_data_to_stream($line);
            break;
        default:
            $stream->put_data_to_stream('Content-Transfer-Encoding: base64'.$LE.$LE);
            while ($line = fread($fh_src, $bytes_block)) $stream->put_data_to_stream(base64_encode($line).$LE);
            break;
        }
        fclose($fh_src);
        return true;
    } else return false;
}

function put_attach_file(&$file, $filename = '', $type = 'application/octet-stream', $name = 'unknown', $LE = CRLF)
{
    $type = trim($type);
    $name = basename(trim($name));
    $bytes_block = 57;
    $mine=new mime();
    // This should use the filename for finding the correct MIME type
    if ('application/octet-stream' == $type && 'unknown' != $name) {
        $ntype = $mine->get_type_from_name($name, false);
        if ($ntype[0]) $type = $ntype[0];
    }
    $encoding = $mine->get_encoding_from_type($type);
    $fh_src = fopen($filename, 'r');
    if ($fh_src) {
        fwrite($file, 'Content-Type: '.$type.'; name="'.$name.'"'.$LE);
        fwrite($file, 'Content-Disposition: attachment; filename="'.$name.'"'.$LE);
        switch ($encoding) {
        case 'q':
            fwrite($file, 'Content-Transfer-Encoding: quoted-printable'.$LE.$LE);
            while ($line = fgets($fh_src)) fwrite($file, quoted_printable_encode($line));
            break;
        case '8':
        case '7':
            fwrite($file, 'Content-Transfer-Encoding: '.$encoding.'bit'.$LE.$LE);
            while ($line = fgets($fh_src)) fwrite($file, $line);
            break;
        default:
            fwrite($file, 'Content-Transfer-Encoding: base64'.$LE.$LE);
            while ($line = fread($fh_src, $bytes_block)) fwrite($file, base64_encode($line).$LE);
            break;
        }
        fclose($fh_src);
        return true;
    } else return false;
}

function quoted_printable_encode1($return = '', $maxlen = 75)
{
    $schachtel = '';
    $return = trim($return);
    // Ersetzen der lt. RFC 1521 nötigen Zeichen
    $return = preg_replace('/([^\t\x20\x2E\041-\074\076-\176])/ie', "sprintf('=%2X',ord('\\1'))", $return);
    $return = preg_replace('!=\ ([A-F0-9])!', '=0\\1', $return);
    // Einfügen von QP-Breaks (=\r\n)
    if ($maxlen && strlen($return) > $maxlen) {
        $length = strlen($return); $offset = 0;
        do {
            $step = 76;
            $add_mode = (($offset+$step) < $length) ? 1 : 0;
            $auszug = substr($return, $offset, $step);
            if (preg_match('!\=$!', $auszug))   $step = 75;
            if (preg_match('!\=.$!', $auszug))  $step = 74;
            if (preg_match('!\=..$!', $auszug)) $step = 73;
            $auszug = substr($return, $offset, $step);
            $offset += $step;
            $schachtel .= $auszug;
            if (1 == $add_mode) $schachtel.= '='.CRLF;
        } while ($offset < $length);
        $return = $schachtel;
    }
    $return = preg_replace('!\.$!', '. ', $return);
    return preg_replace('!(\r\n|\r|\n)$!', '', $return).CRLF;
}

function set_prio_headers($return = '')
{
    switch ($return) {
    case '1':
        return 'X-Priority: 1'.CRLF.'Importance: High'.CRLF;
        break;
    case '3':
        return 'X-Priority: 3'.CRLF.'Importance: Normal'.CRLF;
        break;
    case '5':
        return 'X-Priority: 5'.CRLF.'Importance: Low'.CRLF;
        break;
    }
    return $return;
}

function gather_addresses($addresses)
{
    $address = join(',', $addresses);
    $address = preg_replace('/,$/', '', preg_replace('/,+/', ',', $address));
    $duration = strlen($address);
    $mode = '';
    $j = 1;
    for ($i = 0; $i <= $duration; ++$i) {
        $test = substr($address, $i, 1);
        if ('comment' == $mode) {
            if (')' == $test)  {
                $mode = '';
                continue;
            }
        }
        if ('string' == $mode) {
            if ('"' == $test) {
                $mode = '';
                continue;
            }
        }
        if ('' == $mode) {
            if ('(' == $test) {
                $mode = 'comment';
                continue;
            }
            if ('"' == $test) {
                $mode = 'string';
                continue;
            }
            if (',' == $test) {
                $found[$j] = $i;
                $j++;
            }
        }
    }
    $found[0] = 0;
    $found[$j] = $duration;
    $return = '';
    for ($k = 0; $k < $j; ++$k) {
        $l = $k + 1;
        if (0 != $k) ++$found[$k];
        $build = substr($address, $found[$k], ($found[$l]-$found[$k]));
        $build = mailparser::parse_email_address($build, 0, true);
        if (0 != $k) $return .= ', ';
        $return .= $build[0];
    }
    return $return;
}

function email_check_validity($addresses)
{
    if (is_array($addresses) && !empty($addresses)) {
        $addresses = explode(', ', gather_addresses($addresses));
        foreach ($addresses as $k => $val) {
            if (!preg_match('!^[a-z0-9_\.]+\@[a-z0-9-\.]+\.[a-z]+$!i', $val)) {
                $return[] = $val;
                continue;
            }
            list(, $domain) = explode('@', $val, 2);
            if (getmxrr($domain, $mx, $weight) == 0) $return[] = $val;
        }
        return $return;
    }
}


?>