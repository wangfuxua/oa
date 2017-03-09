<?php
/**
* phlyMail's mail parser, offers methods to streamingly read in a mail and decode it.
* This is a static class, so using it differs from class behaviour of PHP4!
* @author Matthias Sommerfeld, phlyLabs Berlin
* @copyright 2004-2008 phlyLabs Berlin, http://phlylabs.de
* @package phlyMail "Yokohama2" 4.x
* @version 1.0.6 2008-12-15
* @todo  Improve implementation of RFC2231!
*/
class mailparser
{
    /**
    * Parse a mail and return the structure as an array
    * @param  resource  File handle of an open stream to read the mail from
    *[@param  string  Optional embracing boundary, used for reentrancy when parsing inline mails]
    *[@param  string  Optional starting IMAP part number, used for reentrancy when parsing inline mails]
    * @return  mixed  Array data on success, false on failure
    * @since 0.0.1
    */
    static public function parse(&$filehandle, $ext_boundary = false, $startpart = '')
    {
        if (!is_resource($filehandle)) return false;

        // Read and parse the mail header
        $mail_header = '';
        while (true) {
            $line = fgets($filehandle, 1024);
            if (!$line) break;
            $mail_header .= $line;
            if (trim($line) == '') break;
        }
        // Parse den Header der Mail
        $mail_header = self::parse_mail_header($mail_header);
        $decode_mime = ('1.0' == trim($mail_header['mime'])) ? 1 : 0;
        if (strtolower($mail_header['content_type']) == 'text/plain') {
            $parts_text = 'true';
        } elseif (strtolower($mail_header['content_type']) == 'text/html') {
            $parts_html = 'true';
        } elseif (strtolower($mail_header['content_type']) == 'text') { // As seen in real life
            $parts_text = 'true';
        } elseif (preg_match('!^multipart/!i', $mail_header['content_type'])) {
            if (!$decode_mime) $x_robust_warning = 'This mail is not standards conformant!';
            $decode_mime = 1;
            $boundary_stack = array();
        } elseif (trim($mail_header['content_type']) == '') {
            $mail_header['content_type'] = 'text/plain';
            $parts_text = 'true';
        }
        $boundary = isset($mail_header['boundary']) ? $mail_header['boundary'] : false;
        if (!$boundary && (isset($parts_text) || isset($parts_html))) $decode_mime = 0;
        if (isset($mail_header['content_encoding'])) {
            $mail_header['content_encoding'] = trim(strtolower($mail_header['content_encoding']));
        }
        if (isset($mail_header['mailer'])) {
            $mail_header['mailer'] = $mail_header['mailer'];
        }
        if (isset($mail_header['comment'])) {
            $mail_header['comment'] = $mail_header['comment'];
        }
        // End Parsing Header

        // Parsing the body
        $proc_mode = 'none';
        $next_mode = false;
        $next_id = false;
        $id = 0;
        $parent = 0;
        $end_reached = 0;
        $imap_part = ($startpart) ? $startpart : '';
        $imap_layernum = 1;
        $bytes_read = 0;
        $bytes_block = 8192;
        // Exception One Attachment only Email
        if (!$boundary && 1 == $decode_mime) {
            $struct['part_type'][0] = $mail_header['content_type'];
            $struct['part_detail'][0] = $mail_header['content_type_pad'];
            $struct['part_encoding'][0] = $mail_header['content_encoding'];
            $struct['childof'][0] = 0;
            $struct['has_childs'][0] = false;
            $struct['imap_part'][0] = ($imap_part == '') ? $imap_layernum : $imap_part.'.'.$imap_layernum;
            $proc_mode = 'addbody';
        }
        while ($end_reached == 0) {
            if ('finalise' != $proc_mode) {
                $curr_offset = ftell($filehandle);
                $line = fgets($filehandle, 1024);
                if (!$line) $proc_mode = 'finalise';
                if (feof($filehandle) !== false) $proc_mode = 'finalise';
                if ($ext_boundary && ('--'.$ext_boundary == trim($line) || '--'.$ext_boundary.'--' == trim($line))) $proc_mode = 'finalise';
            }
            if (1 == $decode_mime) {
                if ('leaveout' == $proc_mode) $proc_mode = $next_mode;
                if ('noop' == $proc_mode) continue;
                if ('none' == $proc_mode) {
                    if ('--'.$boundary.'--' == rtrim($line)) {
                        if (!empty($boundary_stack)) {
                            $boundary = array_pop($boundary_stack);
                            // Reset current parent setting
                            $parent = $struct['childof'][$parent];
                            // Reset IMAP layernum counting
                            $imap_part = array_pop($imap_part_stack);
                            $imap_layernum = array_pop($imap_layernum_stack);
                        }
                        continue;
                    } elseif ('--'.$boundary == trim($line)) {
                        $proc_mode = 'addhead';
                    }
                }
                if ('parsehead' == $proc_mode) {
                    if (isset($head) && trim($head) != '') {
                        $head = self::parse_mime_header(CRLF.$head.CRLF);
                        $struct['part_type'][$id] = strtolower($head['content_type']);
                        if (isset($head['content_type_pad'])) $struct['part_detail'][$id] = $head['content_type_pad'];
                        if (isset($head['content_disposition'])) $struct['dispo'][$id] = $head['content_disposition'];
                        if (isset($head['content_dispo_pad'])) $struct['dispo_pad'][$id] = $head['content_dispo_pad'];
                        $struct['part_encoding'][$id] = isset($head['content_encoding']) ? strtolower($head['content_encoding']) : false;
                        if (isset($head['content_description'])) $struct['part_description'][$id] = $head['content_description'];
                        if (isset($head['content_id'])) $struct['content_id'][$id] = $head['content_id'];
                        $struct['childof'][$id] = $parent;
                        if (preg_match('/^multipart/i', $struct['part_type'][$id])) {
                            $struct['has_childs'][$id] = true;
                            $boundary_stack[] = $boundary;
                            $imap_part_stack[] = $imap_part;
                            $imap_part = ($imap_part == '') ? $imap_layernum : $imap_part.'.'.$imap_layernum;
                            $imap_layernum_stack[] = $imap_layernum;
                            $imap_layernum = 0;
                            $boundary = $head['boundary'];
                            // The following MIME parts are children of the current one
                            $parent = $id;
                        } elseif (preg_match('/^message/i', $struct['part_type'][$id])) {
                            list ($inlineheader, $inlinebody) = self::parse($filehandle, $boundary, ($imap_part == '') ? $imap_layernum : $imap_part.'.'.$imap_layernum);
                            $struct['has_childs'][$id] = true;
                            $struct['inlineheader'][$id] = $inlinebody['header'];
                            $line = $inlinebody['last_line'];
                            unset($inlinebody['last_line']);
                            foreach ($inlinebody['body'] as $key => $x) {
                                foreach ($x as $k => $v) {
                                    $struct[$key][($id + $k + 1)] = ('childof' == $key) ? ($id+$v) : $v;
                                }
                            }
                            $next_id = $id + sizeof($inlinebody['body']['imap_part']);
                        } else {
                            $struct['has_childs'][$id] = false;
                        }
                        unset($head);
                        $struct['imap_part'][$id] = ($imap_part == '') ? $imap_layernum : $imap_part.'.'.$imap_layernum;
                        $proc_mode = 'addbody';
                    } else {
                        $proc_mode = 'none';
                        continue;
                    }
                }
                if ('finalise' == $proc_mode) {
                    if (isset($struct['part_type'][$id])) {
                        if (!isset($struct['length'][$id])) $struct['length'][$id] = $curr_offset - $struct['offset'][$id];
                        $proc_mode = 'parsebody';
                    }
                    $end_reached = 1;
                }
                if ('addhead' == $proc_mode) {
                    if (CRLF == $line) {
                        $proc_mode = 'parsehead';
                        continue;
                    } else {
                        $head = (isset($head)) ? $head.$line : $line;
                    }
                }
                if ('addbody' == $proc_mode) {
                    if (!isset($struct['offset'][$id])) $struct['offset'][$id] = $curr_offset;
                    if ('--'.$boundary.'--' == rtrim($line)) {
                        if (!empty($boundary_stack)) {
                            $boundary = array_pop($boundary_stack);
                            // Reset current parent setting
                            $parent = $struct['childof'][$parent];
                            // Reset IMAP layernum counting
                            $imap_part = array_pop($imap_part_stack);
                            $imap_layernum = array_pop($imap_layernum_stack);
                        }
                        $struct['length'][$id] = $curr_offset - $struct['offset'][$id];
                        $proc_mode = 'parsebody';
                        $next_mode = 'none';
                    } elseif ('--'.$boundary == rtrim($line)) {
                        $struct['length'][$id] = $curr_offset - $struct['offset'][$id];
                        $proc_mode = 'parsebody';
                    }
                }
                if ('parsebody' == $proc_mode) {
                    if (false !== $next_id) {
                        $id = $next_id;
                        $next_id = false;
                    } else {
                        ++$id;
                    }
                    ++$imap_layernum;
                    if ('none' == $next_mode) {
                        $proc_mode = $next_mode;
                        $next_mode = 'addhead';
                    } else $proc_mode = 'addhead';
                }
            } else {
                if ('finalise' == $proc_mode) {
                    $end_reached = 1;
                    $struct['imap_part'][0] = ($imap_part == '') ? $imap_layernum : $imap_part.'.'.$imap_layernum;
                } else {
                    if (!isset($struct['offset'][0])) $struct['offset'][0] = $curr_offset;
                    if (!isset($struct['length'][0])) $struct['length'][0] = 0;
                    $struct['length'][0] += strlen($line);
                }
            }
        }
        return array
                (isset($mail_header) ? $mail_header : false
                ,array
                        ('header' => array
                                ('content_type' => $mail_header['content_type']
                                ,'content_type_pad' => $mail_header['content_type_pad']
                                ,'mime' => $mail_header['mime']
                                ,'content_encoding' => $mail_header['content_encoding']
                                ,'boundary' => $mail_header['boundary']
                                )
                        ,'body' => isset($struct) ? $struct : false
                        ,'last_line' => trim($line)
                        )
                );
    }

    /**
    * Decode a string encoded by RFC1522
    * @param string String to deocde
    * @param string  Encoding mode, 'b' for base64, 'q' for quoted-printable
    * @return string Decoded string
    */
    public static function decode_1522_line($coded = '', $mode)
    {
        if (!$coded) return '';
        return ('q' == $mode) ? quoted_printable_decode(str_replace('_', '=20', $coded)) : base64_decode($coded);
    }

    /**
     * Parses header lines of an email.
     * @param string  The original mail header raw and directly from the mail source
     * @param bool  Whether to fill predefined fields, which do not exist in the mail
     * @param bool TRUE to get the raw mail header just unfolded,
     *     FALSE to get the complete mail header unfolded, decoded and as array
     * @return array Many predefined offsets holding specific header lines, either offset "prepared" or offset "complete"
     */
    public static function parse_mail_header($mail_head = '', $fill_empty_fields = 1, $return_prepared = 0)
    {
        // Unfolding long header lines
        $mail_head = preg_replace('/(\r\n|\n)([\ \t]+)/', '', $mail_head);
        // Special case: We also need the optinal additional information
        if (preg_match('/\r\nContent-Type: ([-\/\.0-9a-z]+)(;\s?([^\r\n\t]+))*/i', $mail_head, $found)) {
            $return['content_type'] = trim($found[1]);
            $return['content_type_pad'] = (isset($found[3])) ? trim($found[3]) : false;
        } else {
            $return['content_type'] = $return['content_type_pad'] = false;
        }
        // Find the various header fields, if not matched, initialise at least the array offset
        foreach (array
                        /* Suchbegriff, Interne ID, Wo in $found, Comments wegwerfen? */
                        (array('!^MIME-Version:\ (.+)$!mi',                   'mime',             1, 1)
                        ,array('!^Content-Transfer-Encoding:(\ )?(.+)$!mi',   'content_encoding', 2, 1)
                        ,array('/boundary=("?)([^\r^\n^\"\;]+)\1/i',          'boundary',         2, 0)
                        ,array('!^Subject:(\ )?(.+)$!mi',                     'subject',          2, 0)
                        ,array('!^From:(\ )?(.+)$!mi',                        'from',             2, 0)
                        ,array('!^Reply-To:(\ )?(.+)$!mi',                    'replyto',          2, 0)
                        ,array('!^To:(\ )?(.+)$!mi',                          'to',               2, 0)
                        ,array('!^Date:(\ )?(.+)$!mi',                        'date',             2, 1)
                        ,array('!^Delivery-Date:(\ )?(.+)$!mi',               'delivery_date',    2, 1)
                        ,array('!^X-Mailer:(\ )?(.+)$!mi',                    'mailer',           2, 0)
                        ,array('!^X-Spam-Status:(\ )?(Yes)!mi',               'spam_status',      2, 0)
                        ,array('!^X-Spam-Flag:(\ )?(Yes)!mi',                 'spam_status',      2, 0)
                        ,array('!^Comment:(\ )?(.+)$!mi',                     'comment',          2, 0)
                        ,array('!^Message-ID:(\ )?(.+)$!mi',                  'message_id',       2, 0)
                        /* Mainly of interest in IMAP folders */
                        ,array('!^X-phlyMail-Message-Type:\ (.+)$!mi',        'x_phm_msgtype',    1, 0)
                        /* Both the following fields are scanned, but the "better" one takes precedence */
                        ,array('!^Return-Receipt-To:(\ )?(.+)$!mi',           'send_mdn_to',      2, 0)
                        ,array('!^Disposition-Notification-To:(\ )?(.+)$!mi', 'send_mdn_to',      2, 0)
                        /* By parsing the common priority fields in this order, the more
                        standardized ones take precedence, but none is left out */
                        ,array('!^X-MSMail-Priority:(\ )?(.+)$!mi',           'importance',       2, 1)
                        ,array('!^Importance:(\ )?(.+)$!mi',                  'importance',       2, 1)
                        ,array('!^X-Priority:(\ )?(.+)$!mi',                  'importance',       2, 1)
                        /* Formerly a .Mac only feature, we support displaying those */
                        ,array('!^Face-URL:(\ )?(.+)$!mi',                    'x_image_url',      2, 0)
                        ,array('!^X-Face-URL:(\ )?(.+)$!mi',                  'x_image_url',      2, 0)
                        ,array('!^Image-URL:(\ )?(.+)$!mi',                   'x_image_url',      2, 0)
                        ,array('!^X-Image-URL:(\ )?(.+)$!mi',                 'x_image_url',      2, 0)
                        /* These might get parsed once, but currently are not yet */
                        ,array('!^X-Face:(\ )?(.+)$!mi',                      'x_face',           2, 0)
                        ,array('!^Face:(\ )?(.+)$!mi',                        'face',             2, 0)
                        /* Support building discussion threads */
                        ,array('!^In-Reply-To:(\ )?(.+)$!mi',                 'inreplyto',        2, 0)
                        ,array('!^References:(\ )?(.+)$!mi',                  'references',       2, 0)
                ) as $needle) {
            if (preg_match($needle[0], $mail_head, $found) && isset($found[$needle[2]])) {
                if ($needle[3]) $found[$needle[2]] = self::headerline_removecomment($found[$needle[2]]);
                $return[$needle[1]] = trim($found[$needle[2]]);
            } else {
                $return[$needle[1]] = false;
            }
        }
        // Try to fix bogus header lines (unencoded 8bit data)
        if (false !== $return['content_type_pad'] && preg_match('!charset="?([^";]+)("|$|;)!i', $return['content_type_pad'], $found)) {
            $charset = (isset($found[1])) ? $found[1] : $GLOBALS['WP_msg']['iso_encoding'];
        } else {
            $charset = $GLOBALS['WP_msg']['iso_encoding'];
        }

        foreach ($return as $k => $v) {
            if (false === $v) continue;
            if ($k == 'content_type' || $k == 'content_tye_pad') continue;
            if (preg_match('![^\x00-\x7f]!', $v)) {
               $return[$k] = encode_utf8($v, $charset, true);
            } else {
                // Pay attention to RFC 1522 (MIME-extended mail header lines)
                $return[$k] = preg_replace
                        (array('/\=\?([^\s]+)\?q\?([^\r\n]*)\?\=/Uie', '/\=\?([^\s]+)\?b\?([^\r\n]*)\?\=/Uie')
                        ,array
                                ("encode_utf8(self::decode_1522_line('\\2', 'q'), '\\1', true)"
                                ,"encode_utf8(self::decode_1522_line('\\2', 'b'), '\\1', true)"
                                )
                        ,$v
                        );
            }
        }
        // Any comments in the Date: field will just confuse the parsers
        $return['date'] = ($return['date']) ? self::parse_rfc2822_date($return['date']) : false;
        // Mark the SPAM status
        $return['spam_status'] = ($return['spam_status']);
        // Priority settings should be integer values between 1 and 5
        switch ($return['importance']) {
            case 'High':   $return['importance'] = 1; break;
            case 'Normal': $return['importance'] = 3; break;
            case 'Low':    $return['importance'] = 5; break;
        }
        if (!isset($return['importance'])) $return['importance'] = 3;
        // For raw views -> complete, but decoded and unfolded header
        if ($return_prepared == 0) {
            // making up for key => value pairs, unfolded
            preg_match_all('!^([-a-z0-9]+):\ ?(.+)$!mi', $mail_head, $return['complete']);
            // These header lines got to get MIME decoded, too!
            foreach ($return['complete'][2] as $k => $v) {
                if (preg_match('![^\x00-\x7f]!', $v)) {
                    $return['complete'][2][$k] = encode_utf8($v, $charset, true);
                } else {
                    // Pay attention to RFC 1522 (MIME-extended mail header lines)
                    $return['complete'][2][$k] = preg_replace
                            (array('/\=\?([^\s]+)\?q\?([^\r\n]*)\?\=/Uie', '/\=\?([^\s]+)\?b\?([^\r\n]*)\?\=/Uie')
                            ,array
                                    ("encode_utf8(self::decode_1522_line('\\2', 'q'), '\\1', true)"
                                    ,"encode_utf8(self::decode_1522_line('\\2', 'b'), '\\1', true)"
                                    )
                            ,$v
                            );
                }
                $return['complete'][0][$k] = $return['complete'][1][$k].': '.$return['complete'][2][$k];
            }
        } else {
            // Just unfolded and decoded
            $return['prepared'] = $mail_head;
        }
        return $return;
    }

    public static function parse_mime_header($mime_head = '')
    {
        // Beachte RFC 1522 (MIME-extended Mail header lines)
        $mime_head = preg_replace
                (array('/\=\?([^\s]+)\?q\?([^\r\n]*)\?\=/Uie', '/\=\?([^\s]+)\?b\?([^\r\n]*)\?\=/Uie')
                ,array
                        ("encode_utf8(self::decode_1522_line('\\2', 'q'), '\\1', true)"
                        ,"encode_utf8(self::decode_1522_line('\\2', 'b'), '\\1', true)"
                        )
                ,$mime_head
                );
        // Unfolding of long lines
        $mime_head = preg_replace('/(\r\n|\n)([\ \t]+)/', '', $mime_head);

        if (preg_match('/^Content-Type:(\ )?([-\/\.0-9a-z]+)(;\s?([^\r\n\t]+))?/mi', $mime_head, $found)) {
            $return['content_type'] = $found[2];
            $return['content_type_pad'] = (isset($found[4]) && $found[4]) ? trim($found[4]) : false;
        } else {
            $return['content_type'] = $return['content_type_pad'] = false;
        }
        if (preg_match('/^Content-Disposition:(\ )?([-\/\.0-9a-z]+)(;\s?([^\t\r\n]+))?/mi', $mime_head, $found)) {
            $return['content_disposition'] = trim($found[2]);
            $return['content_dispo_pad'] = (isset($found[4]) && $found[3]) ? trim($found[4]) : false;
        } else {
            $return['content_disposition'] = $return['content_dispo_pad'] = false;
        }
        foreach (array
                        (array('!^Content-Description:(\ )?(.+)$!mi',       'content_description', 2)
                        ,array('!^Content-Transfer-Encoding:(\ )?(.+)$!mi', 'content_encoding',    2)
                        ,array('!^Content-ID:(\ )?(.+)$!mi',                'content_id',          2)
                        ,array('/boundary=("?)([^\r^\n^\"\;]+)\1/i',        'boundary',            2)
                        ,array('!^Comment:(\ )?(.+)$!mi',                   'comment',             2)
                ) as $needle) {
            if (preg_match($needle[0], $mime_head, $found)) {
                $return[$needle[1]] = trim($found[$needle[2]]);
            } else {
                $return[$needle[1]] = false;
            }
        }
        // Für Rohansichten -> kompletter, aber dekodierter und unfolded Header
        $return['complete'] = $mime_head;
        return $return;
    }

    /**
    * Distinguish email address, real name within a given raw email address string
    *
    * @param  string  Raw email address
    *[@param  int  Optionally returned real names can be shortened for displaying purposes]
    *[@param  bool IDN name conversion direction: false for decoding, true for encoding]
    *[@param  bool  If set to true, only the email address is returned as a string]
    * @return  array  [0] email address, [1] Real name, [2] complete string, but IDNed; if no real name
    *     is found, offset [1] is populated with the email address
    */
    public static function parse_email_address ($address = '', $shorten_to = 0, $encode = false, $mailonly = false)
    {
        $idn_method = ($encode) ? 'encode' : 'decode';
        // Instantiate IDNA class
        $IDN = new idna_convert();

        $address = str_replace('"', '', $address);
        if (preg_match('!^(.+)<(.+)>$!', trim($address), $found)) {
            // Real Name <Em@il>
            if ($shorten_to && strlen($found[1]) > $shorten_to) {
                $found[1] = substr($found[1], 0, ($shorten_to - 3)) . '...';
            }
            return ($mailonly)
                    ? ($encode) ? trim($IDN->{$idn_method}($found[2])) : trim($found[2])
                    : array
                            (0 => ($encode) ? trim($IDN->{$idn_method}($found[2])) : trim($found[2])
                            ,1 => trim($found[1])
                            ,2 => trim($found[1]).' <'.$IDN->{$idn_method}($found[2]).'>'
                            );
        } elseif (preg_match('!(.+)\((.+?)\)!U', trim($address), $found)) {
            // Em@il (Real Name)
            if ($shorten_to && strlen($found[2]) > $shorten_to) {
                $found[2] = substr($found[2], 0, ($shorten_to - 3)) . '...';
            }
            return ($mailonly)
                    ? ($encode) ? trim($IDN->{$idn_method}($found[1])) : trim($found[1])
                    : array
                            (0 => ($encode) ? trim($IDN->{$idn_method}($found[1])) : trim($found[1])
                            ,1 => trim($found[2])
                            ,2 => trim($found[2]).' <'.$IDN->{$idn_method}($found[1]).'>'
                            );
        } else {
            $address = preg_replace('![<>]!', '', trim($address));
            $return[0] = $return[1] = $return[2] = $IDN->{$idn_method}($address);
            if ($shorten_to && strlen($return[1]) > $shorten_to) {
                $return[1] = substr($return[1], 0, ($shorten_to-3)) . '...';
            }
            return ($mailonly) ? $return[0] : $return;
        }
    }

    public static function get_visible_attachments($mimebody, $do_link = 'links', $icon_path)
    {
        if (!is_array($mimebody['part_attached'])) return false;
        $mime = new mime();   
        $parent = 0;
        foreach ($mimebody['part_attached'] as $num => $name) {
            if (0 != $parent) {
                if ($mimebody['childof'][$num] < $parent) {
                    $parent = 0;
                } else {
                    continue;
                }
            }

            if (isset($mime_readable)) unset($mime_readable);
            if (isset($mimebody['part_detail'][$num]) && preg_match('!name=("?)(.*)\1!i', $mimebody['part_detail'][$num], $found)) {
                $leaf = $found[2];
            } elseif (isset($mimebody['part_detail'][$num]) && preg_match_all('!name\*(\d+)\*?\=("?)([^;]+)\2(;|$)!i', $mimebody['part_detail'][$num], $found, PREG_SET_ORDER)) {
                /** Applay decoding as per RFC2231, where any MIME header might be encoded like so:
                 * <token>*0*='<encoding>'<lang>'<payload>
                 * <token>*1*=<payload continued>
                 * and so on. This parser only partially implements the RFC, since we do not care about the optional language flag nor do we
                 * care about strings, which do not have the asterisk in front
                 */
                $leaf = array();
                foreach ($found as $names) $leaf[$names[1]] = $names[3];
                $leaf = join('', $leaf);
                list ($encoding, $language, $leaf) = split("'", $leaf, 3);
                if (!isset($leaf) || !strlen($leaf)) $leaf = $encoding;
                $leaf = encode_utf8(urldecode($leaf), $encoding, true);
            } elseif (isset($mimebody['dispo_pad'][$num]) && preg_match('/name=("?)(.*)\1/i', $mimebody['dispo_pad'][$num], $found)) {
                $leaf = $found[2];
            } elseif (isset($mimebody['dispo_pad'][$num]) && preg_match_all('!name\*(\d+)\*?\=("?)(.*)\2(;|$)!i', $mimebody['dispo_pad'][$num], $found, PREG_SET_ORDER)) {
                $leaf = array();
                foreach ($found as $names) $leaf[$names[1]] = $names[3];
                $leaf = join('', $leaf);
                list ($encoding, $leaf) = split("''", $leaf, 2);
                if (!isset($leaf) || !strlen($leaf)) $leaf = $encoding;
                $leaf = encode_utf8(urldecode($leaf), $encoding, true);
            } elseif (isset($mimebody['part_description'][$num]) && strlen($mimebody['part_description'][$num]) > 0) {
                $leaf = $mimebody['part_description'][$num];
            } elseif ((count($mimebody['part_attached']) == 1)
                    && preg_match('!name=("?)(.*)\1!i', isset($GLOBALS['decode_detail']) ? $GLOBALS['decode_detail'] : '', $found)) {
                $leaf = $found[2];
            } elseif ('message/' == substr(strtolower($mimebody['part_type'][$num]), 0, 8)) {
                $leaf = $GLOBALS['WP_msg']['inlinemail'];
                $parent = $num;
            } elseif ('text/html' == strtolower($mimebody['part_type'][$num])) {
                $leaf = $GLOBALS['WP_msg']['htmledit'];
            } elseif ('multipart/' == substr(strtolower($mimebody['part_type'][$num]), 0, 10)) {
                continue;
            } else {
                $leaf = $GLOBALS['WP_msg']['undeffile'];
            }
            // Involve MIME handler
            $mime_rewritten = trim($mimebody['part_type'][$num]);
            if (($mime_rewritten == '' || preg_match('/^application.+$/i', $mime_rewritten))
                    && $leaf != $GLOBALS['WP_msg']['undeffile']) {
                list ($mime_rewritten, $mime_readable) = $mime->get_type_from_name($leaf);
            }
            $return['img'][$num] = $mime->get_icon_from_type($icon_path, $mime_rewritten, array('gif', 'png', 'jpg', 'jpeg'));
            $return['img_alt'][$num] = ($mime_rewritten) ? $mime_rewritten : $mimebody['part_type'][$num];
            // Beachte RFC 1522 (MIME-extended Mail header lines)
            $return['name'][$num] = $leaf;
            // MIME handler involvement, part two :)
            if (!isset($mime_readable)) {
                $mime_readable = $mime->get_typename_from_type($mimebody['part_type'][$num]);
            }
            $return['size'][$num] = isset($mimebody['length'][$num]) ? size_format($mimebody['length'][$num]) : 0;
            $return['filetype'][$num] = $mime_readable;
            $return['attid'][$num] = $num;
        }
        return $return;
    }

    /**
    * Apply filtering rules upon a given mailheader and return, whether the rules hit or not
    * @param  string  Mail header, decoded and unfolded, but otherwise intact
    * @param  'any'|'all'  Whether all of the given rules should match or any is sufficient
    * @param  array  all filtering rules to apply; structure of the array:
    * - field  string  Coded name of the mal header field to search in
    * - operator  string  Cooded matching method
    * - search  string  The actual search string
    * @return  bool  TRUE, if the rules hit, FALSE if not
    * @since 0.1.3
    */
    public static function apply_filter($mail_head, $match = 'any', $rules)
    {
    	if (!is_array($mail_head) || empty($mail_head)) return false;
    	if (!is_array($rules) || empty($rules)) return false;
    	$mail_head = join(LF, $mail_head[0]);
    	$match = ('all' == $match) ? 'all' : 'any';
    	$field = array
    			('from' => 'From:(\s){0,1}'
				,'to' => 'To:(\s){0,1}'
				,'cc' => 'CC:(\s){0,1}'
				,'to_cc' => '(To|Cc):(\s){0,1}'
				,'subject' => 'Subject:(\s){0,1}'
				,'date' => 'Date:(\s){0,1}'
				,'priority' => '(X-MSMail-Priority|Importance|X-Priority):(\s){0,1}'
				,'other_header' => ''
    			);
    	$operator = array
    			('contains' => array('.*$search$', 1)
				,'n_contains' => array('.*$search$', 0)
				,'is' => array('$search$$', 1)
				,'n_is' => array('$search$$', 0)
				,'begins' => array('$search$', 1)
				,'ends' => array('.*$search$$', 1)
				,'regex' => array('$search$', 1)
    			);

    	foreach ($rules as $rule) {
    		$pattern = '!^'.$field[$rule['field']]
    				.str_replace
    						('$search$'
    						,str_replace(' ', '\s', ($rule['operator'] == 'regex') ? str_replace('!', '\!', $rule['search']) : preg_quote($rule['search'], '!'))
    						,$operator[$rule['operator']][0]
    						).'!Umi';
    		$state = preg_match($pattern, $mail_head);
    		if (preg_match($pattern, $mail_head)) {
    			$state = '1';
    		} else {
    			$state = '0';
    		}
    		if (!($operator[$rule['operator']][1] - $state)) {
    			if ('any' == $match) return true;
    		} else {
    			if ('all' == $match) return false;
    		}
    	}
    	// The script can only go here, if:
    	// "match all" matched everything or "match any" matched nothing
    	return ('all' == $match);
    }

    /**
     * Parse the date found in an email - and throw away stuff, which might confuse strtotime()
     *
     * @param unknown_type $date
     * @return unknown
     */
    public static function parse_rfc2822_date($date)
    {
        // So, some people even screw up defining a time compliant with the standards
        $date = str_replace('.', ':', $date);
        // What we wish to find is the following format:
        if (preg_match('!^(\w+,)?(\ *)(\d+)(\ *)(\w+)(\ *)(\d+)(\ *)(\d+\:\d+(\:\d+)?)(\ *)([^-+0-9]+)?((\+|\-|)(\d+))?!', $date, $found)) {
            $date = @strtotime($found[3].' '.$found[5].' '.$found[7].' '.$found[9].(isset($found[13]) ? ' '.$found[13] : ''));
        // sth. like 02/14/2006 16:13:00 (bogus date definition, but we can read it)
        } elseif (preg_match('!^(\d+)/(\d+)/(\d+)(\ *)(\d+\:\d+(\:\d+)?)!', $date, $found)) {
            $date = @strtotime($found[3].'-'.$found[1].'-'.$found[2].' '.$found[5]);
        } else {
            $date = false;
        }
        return $date;
    }

    /**
     * This method removes comments from RFC2822 header lines.
     * These comments are in the form "payload (comment)" and the like
     *
     * @param string $string  The original header line (unfolded and decoded please)
     * @return string $string The header line without comments
     */
    public static function headerline_removecomment($string)
    {
        while (preg_match('!\([^\(]*\)!U', $string, $found)) {
            $string = str_replace($found[0], '', $string);
        }
        return $string;
    }
}
?>