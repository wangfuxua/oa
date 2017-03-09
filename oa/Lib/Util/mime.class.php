<?php
/* ------------------------------------------------------------------------- */
/* mime.php -> Map extension to MIME type and vice versa         */
/* (c) 2001-2007 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* phlyMail Yokohama Default branch                                          */
/* v3.1.5                                                                    */
/* ------------------------------------------------------------------------- */
// Only valid within phlyMail
//if (!defined('_IN_PHM_')) die();
/**
* This is an internal class for phlyMail, converting various aspects of MIME
* types, filenames and encoding between each other.
* It requires an external file, holding the translation table.
*/
class mime {

    // To speed up subsequent searches for icons within the same icon directory
    // the class will build a directory cache
    public $dircache = array();

    /**
    * The mime constructor method
    * @access    public
    * @param    string    Filing system location of the MIME table file
    * [@param    boolean    Global setting for Safe Mode; if set, all operations
    *                       will return something, even if no entry is found]
    */
    function mime($mimetable = false, $safemode = false)
    {
        $this->safemode = (isset($safemode) && $safemode) ? true : false;

        if (!is_readable($mimetable)) {
            $this->error = 'File name passed to me is not readable. Exitting';
            return false;
        } else {
            $this->_LoadTable($mimetable);
            return true;
        }
    }

    /**
    * Get MIME type for a given filename (DOS style - name.type)
    * @access    public
    * @param    string    file name to get MIME type for
    * [@param    boolean    SafeMode?, @see mime]
    * @return    array    0 => MIME type, 1 => human readable, English description
    */
    function get_type_from_name($filename = '', $safemode = -1)
    {
        if ($safemode == -1 && $this->safemode) $safemode = true;
        preg_match('/\.([^\.]+)$/i', $filename, $found);
        if (isset($found) && isset($found[1])) {
            $suff = $found[1];
            foreach ($this->WP_MIME as $buffer) {
                if (strtolower($buffer['ext']) == strtolower($suff)) {
                    return array($buffer['type'], $buffer['name']);
                    break;
                }
            }
        }
        return ($safemode) ? array('application/octet-stream', false) : array(false, false);
    }

    /**
    * Get extensiion (DOS style - name.type) for given MIME type
    * @access    public
    * @param    string    MIME type to get extension for
    * [@param    boolean    SafeMode?, @see mime; IGNORED]
    * @return    string    extension, if found, false otherwise
    */
    function get_extension_from_type($mimetype = '', $safemode = false)
    {
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['type']) == strtolower($mimetype)) {
                return $buffer['ext'];
            }
        }
        return false;
    }

    /**
    * Get typical mail encoding for given MIME type
    * @access    public
    * @param    string    MIME type to get encoding for
    * [@param    boolean    SafeMode?, @see mime]
    * @return    string    'q' for quoted-printable, 'b' for base64, false if not found;
    *                       will return 'b', if SafeMode is set and nothing found
    */
    function get_encoding_from_type($mimetype = '', $safemode = -1)
    {
        if ($safemode == -1 && $this->safemode) $safemode = true;
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['type']) == strtolower($mimetype)) {
                return $buffer['encoding'];
            }
        }
        return ($safemode) ? 'b' : false;
    }

    /**
    * Get human readable, English description for a given MIME type
    * @access    public
    * @param    string    MIME type to get description for
    * [@param    boolean    SafeMode?, @see mime]
    * @return    string    Description, if none found, false is returned; if
    *                      SafeMode is active, '' is returned
    */
    function get_typename_from_type($mimetype = '', $safemode = -1)
    {
        if ($safemode == -1 && $this->safemode) $safemode = true;
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['type']) == strtolower($mimetype)) {
                return $buffer['name'];
            }
        }
        return ($safemode) ? '' : false;
    }

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
    	//echo $path.$type;
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
        //print_rr($icons);
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
    * When searching for suitable icons a dir cache is built. To cater for changing dir contents
    * you might want to flush the cache. Use this method therefor.
    *
    * @param  void
    * @return void
    * @since 1.1.2
    */
    function flush_dircache()
    {
        $this->dircache = array();
    }

    /**
    * Initialise the MIME translation table
    * @access    private
    * @param    string    filing system path to the MIME table file
    * @return    void
    */
    function _LoadTable($mimetable)
    {
        foreach (file($mimetable) as $buffer) {
            $buffer = trim($buffer);
            if (!$buffer) continue;
            if ($buffer{0} == '#') continue;
            $parts = explode(';;', $buffer);
            $this->WP_MIME[] = array
                   ('ext' => $parts[0]
                   ,'type' => $parts[1]
                   ,'encoding' => isset($parts[2]) ? $parts[2] : 'b'
                   ,'name' => (isset($parts[3])) ? $parts[3] : false
                   );
        }
    }
}
?>