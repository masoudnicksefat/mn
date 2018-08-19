<?php

defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Contact Module
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
jimport('joomla.application.component.helper');
jimport('joomla.utilities.date');
jimport('joomla.filesystem.file');
    
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

// Load the language files
$jlang = JFactory::getLanguage();

// Plugin
$jlang->load('mod_coalawebcontact', JPATH_SITE, 'en-GB', true);
$jlang->load('mod_coalawebcontact', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('mod_coalawebcontact', JPATH_SITE, null, true);

// Component
$jlang->load('com_coalawebcontact', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_coalawebcontact', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_coalawebcontact', JPATH_ADMINISTRATOR, null, true);

//Keeping the parameters in the component keeps things clean and tidy.
$comParams = JComponentHelper::getParams('com_coalawebcontact');

//Stop conflicts with a unique ID
$uniqueId = 'cwcmod-' . $module->id;

//Lets get help and params from our helper
$helper = new modCoalawebContactHelper($uniqueId, $params, $comParams);
$myparams = $helper->getParams($params, $comParams);

$helppath = '/components/com_coalawebcontact/helpers/';
JLoader::register('EmailHelper', JPATH_ADMINISTRATOR . $helppath . 'email.php');
$emailHelp = new EmailHelper();

//Email cloak if the module is loaded into content
$cloak = JPluginHelper::isEnabled('content', 'emailcloak');
$cloakOn = $myparams['cloakOn'];

/* Redirect Url */
$samePageUrl = JURI::getInstance()->toString();
$sucessUrl = $emailHelp->getRedirect($samePageUrl, $myparams);

$checkRecipient = ($myparams['recipient']);

//Get input data from the form
$input = JFactory::getApplication()->input;

$subject = $input->post->get('cw_mod_contact_subject' . $uniqueId, '', 'STRING');
$name = $input->post->get('cw_mod_contact_name' . $uniqueId, '', 'STRING');
$email = $input->post->get('cw_mod_contact_email' . $uniqueId, '', 'STRING');
$message = $input->post->get('cw_mod_contact_message' . $uniqueId, '', 'HTML');
$capEnter = $input->post->get('cw_mod_contact_captcha' . $uniqueId, '', 'STRING');
$capCheck = $input->post->get('cw_mod_contact_captcha_check' . $uniqueId, '', 'STRING');
$copyme = $input->post->get('cw_mod_contact_copyme' . $uniqueId, '', 'INT');
$remoteHost = '';
$cInput1 = $input->post->get('cw_mod_contact_cinput1' . $uniqueId, '', 'STRING');
$submitted = $input->post->get('cw_mod_contact_sub' . $uniqueId, '', 'BOOLEAN');

// Array of input data
$fields = [
    'subject' => $subject,
    'name' => $name,
    'email' => $email,
    'message' => $message,
    'remoteHost' => $remoteHost,
    'cInput1' => $cInput1
];

if ($submitted) {

    //Lets do some server side php validation!
    foreach ($fields as $key => $value) {

        //We don't need to check the Remote Host.
        if ($key == 'remoteHost') {
            continue;
        }

        //Here we are using some nifty variable variables.
        $funcName = 'check' . ucfirst($key);
        ${$key . 'Check'} = $emailHelp->$funcName($$key, $myparams);
        if (${$key . 'Check'}['hasError']) {
            $hasError = true;
        }
        ${$key . 'Error'} = ${$key . 'Check'}[$key . 'Error'];
    }

    switch ($myparams['whichCaptcha']) {
        case "basic":
            if ($capEnter != $myparams['bCaptchaAnswer']) {
                $captchaError = $myparams['msgCaptchaWrong'];
                $hasError = true;
            } else {
                $captcha = $capEnter;
            }
            break;

    }

    /*
     * No Errors? Nice lets start sending the mail.
     */
    if (!isset($hasError)) {
        $mail = JFactory::getMailer();

            //$mail->SMTPDebug = true;

            if ($myparams['mailFromOpt'] === '1') {
                $sender = array($email, $name);
            } else {
                $mailFrom = $myparams['mailFrom'];
                $fromName = $myparams['mailFromName'];
                $sender = array($mailFrom, $fromName);
            }

        $mail->setSender($sender);
        
        //Add our recipient
        $mail->addRecipient($myparams['recipient']);

        //Setup our subject.
        $extSubject = $subject ? $myparams['emailSubject']. ': ' . $subject : $myparams['emailSubject'];

        //Assign our Subject
        $mail->setSubject($extSubject);

        //Build the email body
        $body = $emailHelp->emailBody($fields, $samePageUrl, $myparams);

        $mail->Encoding = 'base64';
        $mail->IsHTML(false);
        $mail->setBody($body);

        //Finally send it!
        $sendAdmin = $mail->Send();

        //If copy me was selected lets send that too.
        if ($copyme && $copyme == '1' && $email != '' && $sendAdmin == true) {
            $copymeBody = $emailHelp->copymeBody($fields, $samePageUrl, $myparams);
            
            $mail->ClearAllRecipients();
            $mail->addRecipient($email);
            $mail->Encoding = 'base64';
            $mail->IsHTML(false);
            $mail->setBody($copymeBody);

            $sendCopy = $mail->send();
        }


        //If all went well.
        if ($sendAdmin == true) {
            $emailSent = true;
        } else {
            $emailSent = false;
            $msgFailed = JTEXT::_('MOD_CWCONTACT_EMAIL_FAILED_MSG');
        }
    }
} else {
    $emailSent = false;
}

require JModuleHelper::getLayoutPath('mod_coalawebcontact', $params->get('layout', 'default'));
