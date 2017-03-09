<?php
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
    $mine= new mime();
    
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

function quoted_printable_encode($return = '', $maxlen = 75)
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