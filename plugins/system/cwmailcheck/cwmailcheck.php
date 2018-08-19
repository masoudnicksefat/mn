<?php

defined("_JEXEC") or die("Restricted access");
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Mail Check Plugin
 * @author              Steven Palmer
 * @author url          https://coalaweb.com/
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 *  The CoalaWeb Mail Check Plugin was inspired by JCV Thanks to Ready Bytes {@link http://www.readybytes.net}
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
jimport('joomla.plugin.plugin');

class plgSystemCwmailcheck extends JPlugin {

    public function __construct($subject, $config) {
        parent::__construct($subject, $config);

        // load the CoalaWeb Mail Check language file
        $lang = JFactory::getLanguage();

        if ($lang->getTag() != 'en-GB') {
            // Loads English language file as fallback (for undefined stuff in other language file)
            $lang->load('plg_system_cwmailcheck', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('plg_system_cwmailcheck', JPATH_ADMINISTRATOR, null, 1);

        //Load the component language strings
        if ($lang->getTag() != 'en-GB') {
            $lang->load('com_coalawebcontact', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('com_coalawebcontact', JPATH_ADMINISTRATOR, null, 1);
    }

    function onAfterRender() {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $option = $app->input->get('option');
        
        //Some of the parameters are set in the component this keeps things clean and tidy.
        $comParams = JComponentHelper::getParams('com_coalawebcontact');
        //Do we want a button?
        $loadBtn = $comParams->get('check_btn_on', '1');

        // Lets do a few checks first
        if (!$loadBtn || $app->getName() == 'site' || $doc->getType() !== 'html' || $option != 'com_config') {
            return;
        }
        //Let now load jquery
        JHtml::_('jquery.framework');
        //Now grab our script to append to the end of the page
        $html = $this->_mailcheckScripts();

        //Retrieve the body and then send it back with our script attached
        $bodyBefore = $app->getBody();
        $bodyAfter = str_replace('</body>', $html . '</body>', $bodyBefore);
        $app->setBody($bodyAfter);

        return true;
    }

    function onAfterRoute() {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $input = JFactory::getApplication()->input;
        $option = $app->input->get('option');

        
        //Some of the parameters are set in the component this keeps things clean and tidy.
        $comParams = JComponentHelper::getParams('com_coalawebcontact');
        //Do we want a button?
        $loadBtn = $comParams->get('check_btn_on', '1');
        
        // Lets do a few checks first
        if (!$loadBtn || $app->getName() == 'site' || $doc->getType() !== 'html' || $option != 'com_config') {
            return;
        }
        
        //Lets add our custom alert files
        $baseUrl = '/media/coalawebcontact/plugins/system/cwmailcheck/';
        $doc->addStyleSheet($baseUrl . "css/sweetalert.css");
        $doc->addScript($baseUrl . "js/sweetalert.min.js");

        //now check our count to keep track of what stage we are at.
        $checkcount = $input->get('plg_cwmailcheck', false);

        if ($option != 'com_config' || !$checkcount) {
            return true;
        }

        //initiate the mailer 
        $mailer = self::createMailer();

        //Now send the mail and do some basic validation
        if ($mailer->Send() !== true) {
            $this->_error();
        }

        $this->_success();
    }

    protected static function createMailer() {

        //Lets get the current inputs so we can test them
        $input = JFactory::getApplication()->input;

        $smtpauth = $input->get('smtpauth');
        $smtpuser = $input->get('smtpuser', '', 'STRING');
        $smtppass = $input->get('smtppass', '', 'RAW');
        $smtphost = $input->get('smtphost');
        $smtpsecure = $input->get('smtpsecure');
        $smtpport = $input->get('smtpport');
        $mailfrom = $input->get('from_email', '', 'STRING');
        $fromname = $input->get('from_name', '', 'STRING');
        $mailer = $input->get('mailer');
        
        //Create a JMail object
        $mail = JFactory::getMailer();

        //Sender and Recipient
        $sender = array($mailfrom, $fromname);
        $mail->setSender($sender);
        $mail->addRecipient($sender);

        //Subject
        $subject = JText::_('PLG_CWMAILCHECK_EMAIL_SUBJECT');
        $mail->setSubject($subject);
        
        //Body
        $date = JDate::getInstance()->toSql();
        $body = JText::sprintf('PLG_CWMAILCHECK_EMAIL_BODY', $date);
        $mail->Encoding = 'base64';
        $mail->IsHTML(false);
        $mail->setBody($body);

        // Default mailer is to use PHP's mail function
        switch ($mailer) {
            case 'smtp':
                $mail->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
                break;

            case 'sendmail':
                $mail->IsSendmail();
                break;
            
            case 'mail':
                $mail->IsMail();
                break;

            default:
                $mail->IsMail();
                break;
        }

        return $mail;
    }

    //Create our Json encoded error info
    protected function _error() {
        $result['status'] = 'error';
        $result['title'] = JText::_('PLG_CWMAILCHECK_TITLE_ERROR');
        $result['message'] = JText::_('PLG_CWMAILCHECK_MESSAGE_ERROR');
        $result = json_encode($result);

        echo $result;
        exit();
    }

    //Create our Json encoded success info
    protected function _success() {
        $result['status'] = 'success';
        $result['title'] = JText::_('PLG_CWMAILCHECK_TITLE_SUCCESS');
        $result['message'] = JText::_('PLG_CWMAILCHECK_MESSAGE_SUCCESS');
        $result = json_encode($result);

        echo $result;
        exit();
    }

    //Lets add our scripts to do the heavy lifting
    private function _mailcheckScripts() {
        $root = JURI::root();
        $foo = '<span style=\"margin:20px;\">'
                . '<button class=\"btn\" type=\"button\" id=\"cw-mail-check\">'
                . '<i class=\"icon-envelope-opened\"></i>&nbsp;CoalaWeb Mail Check'
                . '</button></span>';

        $html = '<script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    $("#jform_mailer-lbl").closest("fieldset").find("legend").append("' . $foo . '");   
                    $("#cw-mail-check").click(function () {
                        var smtp_auth = 1;
                        if ($("#jform_smtpauth1").prop("checked")) {
                            smtp_auth = 0;
                        }

                        var url = "' . $root . '";
                        url = url + "administrator/index.php?option=com_config&";
                        url = url + "plg_cwmailcheck=1";

                        var from_email = $("#jform_mailfrom").val();
                        var mailer = $("#jform_mailer :selected").val();
                        var from_name = $("#jform_fromname").val();
                        var send_path = $("#jform_sendmail").val();
                        var smtp_secure = $("#jform_smtpsecure :selected").val();
                        var smtp_port = $("#jform_smtpport").val();
                        var smtp_user = $("#jform_smtpuser").val();
                        var smtp_pass = $("#jform_smtppass").val();
                        var smtp_host = $("#jform_smtphost").val();

                        $.post(url,
                                {
                                    from_email: from_email,
                                    mailer: mailer,
                                    from_name: from_name,
                                    send_path: send_path,
                                    smtp_auth: smtp_auth,
                                    smtp_secure: smtp_secure,
                                    smtp_port: smtp_port,
                                    smtp_user: smtp_user,
                                    smtp_pass: smtp_pass,
                                    smtp_host: smtp_host
                                },
                        function (data, status) {
                            var record = JSON.parse(data);


                            if (record.status === "success") {
                                swal({
                                  title: record.title,
                                  text: record.message,
                                  type: record.status,
                                  html: true
                                });
                            }

                            if (record.status === "error") {
                               swal({
                                  title: record.title,
                                  text: record.message,
                                  type: record.status,
                                  html: true
                                });
                            }

                        });
                    });   
                });
            })(jQuery);
        </script>';

        return $html;
    }
}