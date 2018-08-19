<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Contact Component
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

jimport('joomla.filesystem.path');
jimport('joomla.filesystem.file');

/**
 *  component helper.
 */
class AttachmentsHelper
{

    public static function getUploadFile($files)
    {
        //Keeping the parameters in the component keeps things clean and tidy.
        $params = JComponentHelper::getParams('com_coalawebcontact');
        $updated = array();

        $upload_folder = 'media/coalawebcontact/resources';
        $path = JPATH_ROOT . '/' . $upload_folder;

        if (isset($files)) {
            foreach ($files['cwupload'] as $result) {

                //Check for errors
                if ($result["error"] > 0) {
                    //Error was found.
                    $info = self::fileMessages($result["error"]);
                    $result['type'] = $info['type'];
                    $result['text'] = $info['msg'];
                } else {
                    //Clean file name
                    $result['name'] = JFilterInput::getInstance()->clean($result['name'], 'CMD');
                    $result['name'] = JFile::makeSafe(strtolower($result['name']));

                    //Check file type
                    if (self::isValidType($params, $result)) {
                        //Check size
                        if ($result["size"] < $params->get('max_size')) {
                            //Does file already exists?
                            if (file_exists($path . '/' . $result["name"])) {
                                JFile::delete($path . '/' . $result["name"]);
                            }
                            //Security check
                            if (self::checkSecurity($params, $result)) {
                                self::storeFile($path, $result);
                            }

                        } else {
                            //file is too large
                            $result["error"] = 99;
                            $result['type'] = 'warning';
                            $result['text'] = JText::_('COM_CWCONTACT_TOO_LARGE_ERROR');
                        }
                    } else {
                        //the file type is not permitted
                        $result["error"] = 99;
                        $result['type'] = 'warning';
                        $result['text'] = JText::_('COM_CWCONTACT_INVALID_ERROR');
                    }
                }
                //Add iteration to array
                $updated[] = $result;
            }
        }

        //return array of iterations
        return $updated;
    }

    /**
     * Check if file is of a valid type
     *
     * @param $params
     * @param $result
     * @return bool
     */
    private static function isValidType($params, $result)
    {
        $valid = false;
        $filetypes = $params->get('file_types');

        $actualMIME = self::actualMIME($result["tmp_name"]);

        if ($filetypes == "*" ||
            (stripos($filetypes, $result["type"]) !== false &&
                $actualMIME !== false &&
                stripos($filetypes, $actualMIME) !== false)
        ) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * Check for actual MIME type
     *
     * @param $file
     * @return bool|mixed|string
     */
    private static function actualMIME($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $mime = false;

        // try to use recommended functions
        if (defined('FILEINFO_MIME_TYPE') &&
            function_exists('finfo_open') && is_callable('finfo_open') &&
            function_exists('finfo_file') && is_callable('finfo_file') &&
            function_exists('finfo_close') && is_callable('finfo_close')
        ) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            if ($mime === '') {
                $mime = false;
            }
            finfo_close($finfo);
        } else if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $f = "'" . $file . "'";
            if (function_exists('escapeshellarg') && is_callable('escapeshellarg')) {
                //prefer to use escapeshellarg if it is available
                $f = escapeshellarg($file);
            }

            if (function_exists('exec') && is_callable('exec')) {
                //didn't like how 'system' flushes output to browser. replaced with 'exec'
                //note: You can change this to: shell_exec("file -b --mime-type $f"); if you get
                //"regular file" as the mime type
                $mime = exec("file -bi $f");
                //this removes the charset value if it was returned with the mime type. mime is first.
                $mime = strtok($mime, '; ');
                $mime = trim($mime); //remove any remaining whitespace
            } else if (function_exists('shell_exec') && is_callable('shell_exec')) {
                //note: You can change this to: shell_exec("file -b --mime-type $f"); if you get
                //"regular file" as the mime type
                $mime = shell_exec("file -bi $f");
                //this removes the charset value if it was returned with the mime type. mime is first.
                $mime = strtok($mime, '; ');
                $mime = trim($mime); //remove any remaining whitespace
            }
        } else if (function_exists('mime_content_type') && is_callable('mime_content_type')) {
            //test using mime_content_type last, since it sometimes detects the mime incorrectly
            $mime = mime_content_type($file);
        }

        return $mime;
    }

    /**
     * Attempt to upload the file
     *
     * @param $path
     * @param $result
     */
    private static function storeFile($path, &$result)
    {
        // Make sure that the full file path is safe.
        $filepath = JPath::clean( $path.'/'.strtolower( $result["name"] ));

        try {
            JFile::upload($result["tmp_name"], $filepath);
        } catch (RuntimeException $e) {
            $result["error"] = 99;
            $result['type'] = 'error';
            $result['text'] = JText::_('COM_CWCONTACT_UPLOAD_UNSUCCESSFUL');
            return;
        }
        $result['type'] = 'success';
        $result['text'] = JText::_('COM_CWCONTACT_UPLOAD_SUCCESSFUL');
    }

    /**
     * Get message text based on error code
     *
     * @param $error_code
     * @return array
     */
    private static function fileMessages($error_code)
    {
        switch ($error_code) {
            case 1:
                $message = JText::_('COM_CWCONTACT_INI_SIZE_ERROR');
                $type = 'error';
                break;
            case 2:
                $message = JText::_('COM_CWCONTACT_FORM_SIZE_ERROR');
                $type = 'error';
                break;
            case 3:
                $message = JText::_('COM_CWCONTACT_PARTIAL_ERROR');
                $type = 'error';
                break;
            case 4:
                $message = JText::_('COM_CWCONTACT_NO_FILE_ERROR');
                $type = 'error';
                $type = 'warning';
                break;
            case 6:
                $message = JText::_('COM_CWCONTACT_NO_TMP_DIR_ERROR');
                $type = 'error';
                break;
            case 7:
                $message = JText::_('COM_CWCONTACT_CANT_WRITE_ERROR');
                $type = 'error';
                break;
            case 8:
                $message = JText::_('COM_CWCONTACT_EXTENSION_ERROR');
                $type = 'error';
                break;
            default:
                $message = JText::_('COM_CWCONTACT_UNKNOWN_ERROR');
                $type = 'error';
                break;
        }
        $info = [
            'msg' => $message,
            'type' => $type
        ];
        return $info;
    }


    /**
     * Check for suspicious naming and or potential PHP contents
     *
     * @param $params
     * @param $result
     * @return bool
     */
    private static function checkSecurity($params, &$result)
    {
        $safe = true;

        $forbidden = array('php', 'phps', 'php5', 'php3', 'php4', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py');

        // Prevent buffer overflow attack by checking for null byte in the file name
        $null_byte = "\x00";
        if (stripos($result["name"], $null_byte) !== false) {
            $result['type'] = 'error';
            $result['text'] = JText::_('COM_CWCONTACT_NULL_BYTE_FOUND');

            return false;
        }

        //Prevent uploading forbidden script files (based on file extension)
        $filename = $result["name"];
        $split = explode('.', $filename);
        array_shift($split);
        $only_extensions = array_map('strtolower', $split);

        foreach ($forbidden as $script) {
            if (in_array($script, $only_extensions)) {
                $result['type'] = 'error';
                $result['text'] = JText::_('COM_CWCONTACT_SCRIPT_FOUND');

                return false;
            }
        }

        //Check for php tags and or script if not allowed
        $buffer = 1024 * 8;
        $fp = @fopen($result["tmp_name"], 'r');
        if ($fp !== false) {
            $data = '';

            while (!feof($fp) && $safe === true) {
                $data .= @fread($fp, $buffer);

                //Check for PHP long tag
                if (stripos($data, '<?php') !== false) {
                    $result['type'] = 'error';
                    $result['text'] = JText::_('COM_CWCONTACT_PHP_TAG_FOUND');

                    $safe = false;
                    continue;
                }

                //Check for PHP short tag but only if file is a script
                $script_files = array('php', 'phps', 'php3', 'php4', 'php5', 'class', 'inc', 'txt', 'dat', 'tpl', 'tmpl');
                $is_script = false;
                foreach ($script_files as $script) {
                    //Is a script?
                    if (in_array($script, $only_extensions)) {
                        $is_script = true;
                    }
                }

                if ($is_script) {
                    //Search for the short tag
                    if (stripos($data, '<?') !== false) {
                        $result['type'] = 'error';
                        $result['text'] = JText::_('COM_CWCONTACT_PHP_TAG_FOUND');

                        $safe = false;
                        continue;
                    }
                }

                //Check for script if not allowed
                $allow_scripts_in_archive = $params->get('allow_scripts');
                if (!$allow_scripts_in_archive) {
                    $archive_exts = array('zip', '7z', 'jar', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa');
                    $is_archive = false;
                    foreach ($archive_exts as $archive) {
                        //Is an archive?
                        if (in_array($archive, $only_extensions)) {
                            $is_archive = true;
                        }
                    }

                    if ($is_archive) {
                        foreach ($forbidden as $ext) {
                            //Search for the short tag
                            if (stripos($data, '.' . $ext) !== false) {
                                $result['type'] = 'error';
                                $result['text'] = JText::_('COM_CWCONTACT_FORBIDDEN_FOUND');

                                $safe = false;
                                continue;
                            }
                        }
                    }
                }
                //start the next loop with the last 10 bytes just in case the PHP tag was split up
                $data = substr($data, -10);
            }
            //close the file handle
            fclose($fp);
        }

        return $safe;
    }

}
