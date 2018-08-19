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
class EmailHelper
{
    /**
     * @param $samePageUrl
     * @param $params
     * @return string
     */
    function getRedirect($samePageUrl, $params) {
        $homeUrl = JRoute::_(JURI::root());
        switch ($params['redirectUrl']) {
            case 1: $sucessUrl = ($samePageUrl . '#' . $params['sysMsg']);
                break;
            case 2: $sucessUrl = ($homeUrl . '#' . $params['sysMsg']);
                break;
            case 3: $sucessUrl = ($params['customUrl'] . '#' . $params['sysMsg']);
                break;
            default: $sucessUrl = JRoute::_(JURI::root());
                break;
        }

        return $sucessUrl;
    }

    /**
     * @param $subject
     * @param $params
     * @return mixed
     */
    function checkSubject($subject, $params) {
        if ($params['displaySubject'] == 'R') {
            if (!$subject) {
                $arr['subjectError'] = $params['msgSubjectMissing'];
                $arr['hasError'] = true;
            } else {
                $arr['subjectError'] = null;
                $arr['hasError'] = false;
            }
            return $arr;
        }
    }

    /**
     * @param $name
     * @param $params
     * @return mixed
     */
    function checkName($name, $params) {
        if ($params['displayName'] == 'R') {
            if (!$name) {
                $arr['nameError'] = $params['msgNameMissing'];
                $arr['hasError'] = true;
            } else {
                $arr['nameError'] = null;
                $arr['hasError'] = false;
            }
            return $arr;
        }
    }

    /**
     * @param $message
     * @param $params
     * @return mixed
     */
    function checkMessage($message, $params) {
        if ($params['displayMessage'] == 'R') {
            if (!$message) {
                $arr['messageError'] = $params['msgMessageMissing'];
                $arr['hasError'] = true;
            } else {
                $arr['messageError'] = null;
                $arr['hasError'] = false;
            }
            return $arr;
        }
    }


    /**
     * @param $email
     * @param $params
     * @return mixed
     */
    function checkEmail($email, $params) {
        if ($params['displayEmail'] == 'R') {
            if (!$email) {
                $arr['emailError'] = $params['msgEmailMissing'];
                $arr['hasError'] = true;
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $arr['emailError'] = $params['msgEmailInvalid'];
                $arr['hasError'] = true;
            } else {
                $arr['emailError'] = null;
                $arr['hasError'] = false;
            }
            return $arr;
        } else if ($params['displayEmail'] == 'Y' && $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $arr['emailError'] = $params['msgEmailInvalid'];
                $arr['hasError'] = true;
            } else {
                $arr['emailError'] = null;
                $arr['hasError'] = false;
            }
            return $arr;
        }
    }

    /**
     * @param $cInput1
     * @param $params
     * @return mixed
     */
    function checkCInput1($cInput1, $params) {
        if ($params['displayCInput1'] == 'R') {
            if (!$cInput1) {
                $arr['cInput1Error'] = $params['msgCInput1Missing'];
                $arr['hasError'] = true;
            } else {
                $arr['cInput1Error'] = null;
                $arr['hasError'] = false;
            }
            return $arr;
        }
    }

    /**
     * Create our custom select lists based on CSV list
     *
     * @param $selectCInput
     * @return string
     */
    function selectOptions($selectCInput) {
        $comParams = JComponentHelper::getParams('com_coalawebcontact');
        $delimiter = $comParams->get('custom_delimiter', ',');

        $arr = explode($delimiter, $selectCInput);
        foreach ($arr as $key => $opt) {
            $txt = $opt;
            $opt = preg_replace('/\s+/', '_', $opt);
            $arr[$key] = '<option value=' . $opt . '>' . $txt . '</option>';
        }
        $opts = implode(PHP_EOL, $arr);
        return $opts;
    }

    /**
     * Clean and tidy text
     *
     * @param $text
     * @param bool $stripHtml
     * @param $limit
     * @return mixed|Tidy
     */
    public static function textClean($text, $stripHtml = true, $limit)
    {
        // Now decoded the text
        $decoded = html_entity_decode($text);

        // Remove any HTML based on module settings
        $notags = $stripHtml ? strip_tags($decoded) : $decoded;

        // Remove brackets such as plugin code
        $nobrackets = preg_replace("/\{[^}]+\}/", " ", $notags);

        //Now reduce the text length if needed
        $chars = strlen($notags);
        if ($chars <= $limit) {
            $description = $nobrackets;
        } else {
            $description = JString::substr($nobrackets, 0, $limit) . "...";
        }

        // One last little clean up
        $cleanText = preg_replace("/\s+/", " ", $description);

        // Lastly repair any HTML that got cut off if Tidy is installed
        if (extension_loaded('tidy') && !$stripHtml) {
            $tidy = new Tidy();
            $config = array(
                'output-xml' => true,
                'input-xml' => true,
                'clean' => false
            );
            $tidy->parseString($cleanText, $config, 'utf8');
            $tidy->cleanRepair();
            $cleanText = $tidy;
        }

        return $cleanText;
    }

    /**
     * Clean and minimize code
     *
     * @param type $code
     * @return string
     */
    public static function cleanCode($code) {

        // Remove comments.
        $pass1 = preg_replace('~//<!\[CDATA\[\s*|\s*//\]\]>~', '', $code);
        $pass2 = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\)\/\/[^"\'].*))/', '', $pass1);

        // Minimize.
        $pass3 = str_replace(array("\r\n", "\r", "\n", "\t"), '', $pass2);
        $pass4 = preg_replace('/ +/', ' ', $pass3); // Replace multiple spaces with single space.
        $codeClean = trim($pass4);  // Trim the string of leading and trailing space.

        return $codeClean;
    }

    function emailBody($fields, $fromUrl, $params) {

        // Setup email body text 
        switch ($params['emailFormat']) {
            case "nohtml":
                $body = '';

                if ($params['displayMailRequestA']) {
                    $body .= $params['reqTitleLbl'] . "\r\n";
                    $body .= $fields['subject'] ? $params['subjectLbl'] . ": " . $fields['subject'] . "\r\n" : "";
                    $body .= $fields['message'] ? $params['messageLbl'] . ": " . $fields['message'] . "\r\n" : "";
                    $body .= "\r\n";
                }

                if ($params['displayMailSentbyA']) {
                    $body .= $params['sByTitleLbl'] . "\r\n";
                    $body .= $fields['name'] ? $params['nameLbl'] . ": " . $fields['name'] . "\r\n" : "";
                    $body .= $fields['email'] ? $params['emailLbl'] . ": " . $fields['email'] . "\r\n" : "";
                    $body .= (isset($fields['cInput1']) && $fields['cInput1']) ? $params['cInput1Lbl'] . ": " . str_replace('_', ' ', $fields['cInput1']) . "\r\n" : "";
                    $body .= "\r\n";
                }

                if ($params['displayMailSentfromA']) {
                    $body .= $params['sFromTitleLbl'] . "\r\n";
                    $body .= $fields['remoteHost'] ? $params['sByIpLbl'] . ": " . $fields['remoteHost'] . "\r\n" : "";
                    $body .= $params['sFromWebLbl'] . ": " . $params['sitename'] . "\r\n";
                    $body .= $fromUrl ? $params['sFromUrlLbl'] . ": " . $fromUrl : "";
                    $body .= "\r\n";
                }
                break;

        }

        return $body;
    }

    /**
     * @param $cfields
     * @param $fields
     * @param $fromUrl
     * @param $tosAnswer
     * @return string
     */
    function copymeBody($fields, $fromUrl, $params) {

        // Setup email body text 
        switch ($params['emailFormat']) {
            case "nohtml":
                $body = '';

                if ($params['displayMailRequestC']) {
                    $body .= $params['reqTitleLbl'] . "\r\n";
                    $body .= $fields['subject'] ? $params['subjectLbl'] . ": " . $fields['subject'] . "\r\n" : "";
                    $body .= $fields['message'] ? $params['messageLbl'] . ": " . $fields['message'] . "\r\n" : "";
                    $body .= "\r\n";
                }

                if ($params['displayMailSentbyC']) {
                    $body .= $params['sByTitleLbl'] . "\r\n";
                    $body .= $fields['name'] ? $params['nameLbl'] . ": " . $fields['name'] . "\r\n" : "";
                    $body .= $fields['email'] ? $params['emailLbl'] . ": " . $fields['email'] . "\r\n" : "";
                    $body .= (isset($fields['cInput1']) && $fields['cInput1']) ? $params['cInput1Lbl'] . ": " . str_replace('_', ' ', $fields['cInput1']). "\r\n" : "";
                    $body .= "\r\n";
                }

                if ($params['displayMailSentfromC']) {
                    $body .= $params['sFromTitleLbl'] . "\r\n";
                    $body .= $fields['remoteHost'] ? $params['sByIpLbl'] . ": " . $fields['remoteHost'] . "\r\n" : "";
                    $body .= $params['sFromWebLbl'] . ": " . $params['sitename'] . "\r\n";
                    $body .= $fromUrl ? $params['sFromUrlLbl'] . ": " . $fromUrl : "";
                    $body .= "\r\n";
                }
                break;

        }

        return $body;
    }

    /**
     * Check dependencies are meet
     *
     * @return boolean
     */
    public static function checkDependencies() {
        $checkOk = false;
        $minVersion = '0.1.5';

        // Load the version.php file for the CW Gears plugin
        $version_php = JPATH_SITE . '/plugins/system/cwgears/version.php';
        if (!defined('PLG_CWGEARS_VERSION') && JFile::exists($version_php)) {
            include_once $version_php;
        }

        $mobiledetect_php = JPATH_SITE . '/plugins/system/cwgears/helpers/cwmobiledetect.php';
        $loadcount_php = JPATH_SITE . '/plugins/system/cwgears/helpers/loadcount.php';

        if (
            JPluginHelper::isEnabled('system', 'cwgears', true) == true &&
            JFile::exists($version_php) &&
            version_compare(PLG_CWGEARS_VERSION, $minVersion, 'ge') &&
            JFile::exists($mobiledetect_php) &&
            JFile::exists($loadcount_php) ){

            if (!class_exists('Cwmobiledetect')) {
                JLoader::register('Cwmobiledetect', $mobiledetect_php);
            }

            if (!class_exists('CwGearsHelperLoadcount')) {
                JLoader::register('CwGearsHelperLoadcount', $loadcount_php);
            }

            $checkOk = true;
        }

        return $checkOk;
    }


}
