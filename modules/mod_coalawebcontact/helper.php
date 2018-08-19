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

jimport('joomla.filesystem.file');

$helppath = '/components/com_coalawebcontact/helpers/';
JLoader::register('EmailHelper', JPATH_ADMINISTRATOR . $helppath . 'email.php');

class modCoalawebContactHelper extends JObject {

    private $uniqueid;
    private $params;

    public function __construct($uniqueId, $params, $comParams) {
        $this->uniqueid = $uniqueId;
        $this->params = $this->getParams($params, $comParams);
        $this->addAssets();
        return;
   }

    public function getParams($params, $comParams) {
        $app = JFactory::getApplication();
        $emailHelp = new EmailHelper();

        //Advanced
        $arr['uniqueid'] = $this->uniqueid;
        $arr['moduleclass_sfx'] = htmlspecialchars($params->get('moduleclass_sfx'));
        $arr['cloakOn'] = $comParams->get('cloak_on', '1');

        //Labels
        $arr['nameLbl'] = $comParams->get('name_lbl', JTEXT::_('COM_CWCONTACT_LABEL_NAME'));
        $arr['nameHint'] = $comParams->get('name_hint', JTEXT::_('COM_CWCONTACT_NAME_HINT'));
        $arr['emailLbl'] = $comParams->get('email_lbl', JTEXT::_('COM_CWCONTACT_LABEL_EMAIL'));
        $arr['emailHint'] = $comParams->get('email_hint', JTEXT::_('COM_CWCONTACT_EMAIL_HINT'));
        $arr['messageLbl'] = $comParams->get("message_lbl", JTEXT::_('COM_CWCONTACT_LABEL_MESSAGE'));
        $arr['messageHint'] = $comParams->get('message_hint', JTEXT::_('COM_CWCONTACT_MESSAGE_HINT'));
        $arr['subjectLbl'] = $comParams->get('subject_lbl', JTEXT::_('COM_CWCONTACT_LABEL_SUBJECT'));
        $arr['subjectHint'] = $comParams->get('subject_hint', JTEXT::_('COM_CWCONTACT_SUBJECT_HINT'));
        $arr['copymeLbl'] = $comParams->get('copyme_lbl', JTEXT::_('COM_CWCONTACT_LABEL_COPYME'));
        $arr['sFromTitleLbl'] = $comParams->get('sfrom_title', JTEXT::_('COM_CWCONTACT_MAIL_TITLE_FROM'));
        $arr['sFromWebLbl'] = $comParams->get('sfrom_web_lbl', JTEXT::_('COM_CWCONTACT_LABEL_SFROMWEB'));
        $arr['sFromUrlLbl'] = $comParams->get('sfrom_url_lbl', JTEXT::_('COM_CWCONTACT_LABEL_SFROMURL'));
        $arr['sFromUrlText'] = $comParams->get('sfrom_url_text', JTEXT::_('COM_CWCONTACT_TEXT_URL'));
        $arr['sByTitleLbl'] = $comParams->get('sby_title', JTEXT::_('COM_CWCONTACT_MAIL_TITLE_BY'));
        $arr['reqTitleLbl'] = $comParams->get('req_title', JTEXT::_('COM_CWCONTACT_MAIL_TITLE_REQUEST'));
        $arr['sByIpLbl'] = $comParams->get('ip_lbl', JTEXT::_('COM_CWCONTACT_LABEL_IP'));
        $arr['calFromLbl'] = $comParams->get('cal_from_lbl', JTEXT::_('COM_CWCONTACT_LABEL_CAL_FROM'));
        $arr['calToLbl'] = $comParams->get('cal_to_lbl', JTEXT::_('COM_CWCONTACT_LABEL_CAL_TO'));
        $arr['cInput1Lbl'] = $comParams->get('custom1_lbl', JTEXT::_('COM_CWCONTACT_LABEL_CUSTOM1'));
        $arr['cInput1Hint'] = $comParams->get('custom1_hint', JTEXT::_('COM_CWCONTACT_CUSTOM1_HINT'));
        $arr['charcountLbl'] = $comParams->get('charcount_lbl', JTEXT::_('COM_CWCONTACT_LABEL_CHARCOUNT'));
        $arr['charcountLblEnd'] = $comParams->get('charcount_lbl_end', JTEXT::_('COM_CWCONTACT_LABEL_CHARCOUNT_END'));

        // Messages
        $arr['msgRemailMissing'] = $comParams->get('msg_remail_missing', JTEXT::_('COM_CWCONTACT_MSG_REMAIL_MISSING'));
        $arr['msgEmailSent'] = $comParams->get('msg_email_sent', JTEXT::_('COM_CWCONTACT_MSG_EMAIL_SENT'));
        $arr['msgSubjectMissing'] = $comParams->get('msg_subject_missing', JTEXT::_('COM_CWCONTACT_MSG_SUBJECT_MISSING'));
        $arr['msgNameMissing'] = $comParams->get('msg_name_missing', JTEXT::_('COM_CWCONTACT_MSG_NAME_MISSING'));
        $arr['msgDepMissing'] = $comParams->get('msg_dep_missing', JTEXT::_('COM_CWCONTACT_MSG_DEP_MISSING'));
        $arr['msgEmailMissing'] = $comParams->get('msg_email_missing', JTEXT::_('COM_CWCONTACT_MSG_EMAIL_MISSING'));
        $arr['msgEmailInvalid'] = $comParams->get('msg_email_invalid', JTEXT::_('COM_CWCONTACT_MSG_EMAIL_INVALID'));
        $arr['msgDateMissing'] = $comParams->get('msg_date_missing', JTEXT::_('COM_CWCONTACT_MSG_DATE_MISSING'));
        $arr['msgMessageMissing'] = $comParams->get('msg_message_missing', JTEXT::_('COM_CWCONTACT_MSG_MESSAGE_MISSING'));
        $arr['msgCaptchaWrong'] = $comParams->get('msg_captcha_wrong', JTEXT::_('COM_CWCONTACT_MSG_CAPTCHA_WRONG'));
        $arr['msgTosMissing'] = $comParams->get('msg_tos_missing', JTEXT::_('COM_CWCONTACT_MSG_TOS_MISSING'));
        $arr['msgCInput1Missing'] = $comParams->get('msg_custom1_missing', JTEXT::_('COM_CWCONTACT_MSG_CUSTOM1_MISSING'));
        $arr['msgAsterisk'] = $comParams->get('msg_asterisk', JTEXT::_('COM_CWCONTACT_MSG_ASTERISK'));

        // Display Fields
        $arr['displaySubject'] = $params->get('display_subject') ? $params->get('display_subject') : $comParams->get('display_subject', 'Y');
        $arr['displayName'] = $params->get('display_name') ? $params->get('display_name') : $comParams->get('display_name', 'Y');
        $arr['displayEmail'] = $params->get('display_email') ? $params->get('display_email') : $comParams->get('display_email', 'R');
        $arr['displayMessage'] = $params->get('display_message') ? $params->get('display_message') : $comParams->get('display_message', 'Y');
        $arr['displayCopyme'] = ($params->get('display_copyme') >= '0') ? $params->get('display_copyme') : $comParams->get('display_copyme', 'N');
        $arr['displayFormat'] = ($params->get('display_date_format') >= '0') ? $params->get('display_date_format') : $comParams->get('display_date_format', '0');
        $arr['displayCInput1'] = $params->get('display_c_input1') ? $params->get('display_c_input1') : $comParams->get('display_c_input1', 'N');
        $arr['typeCInput1'] = $comParams->get('type_c_input1', 'text');
        $arr['selectCInput1'] = $emailHelp->selectOptions($comParams->get('select_c_input1'));
        $arr['displayChar'] = ($params->get('display_char') >= '0') ? $params->get('display_char') : $comParams->get('display_char', '0');
        $arr['displayAsteriskMsg'] = ($params->get('display_asterisk_msg') >= '0') ? $params->get('display_asterisk_msg') : $comParams->get('display_asterisk_msg', '0');

        // General Display
        $arr['recipients'] = $params->get('recipients') ? $params->get('recipients') : $comParams->get('recipients', '1');
        $arr['recipient'] = $params->get('recipient') ? $params->get('recipient') : $comParams->get('recipient');
        $arr['redirectUrl'] = $params->get('redirect_url') ? $params->get('redirect_url') : $comParams->get('redirect_url', '2');
        $arr['customUrl'] = $params->get('custom_url') ? $params->get('custom_url') : $comParams->get('custom_url');
        $arr['sysMsg'] = $comParams->get('sys_msg', 'system-message-container');
        $arr['theme'] = $params->get('form_theme') ? $params->get('form_theme') : $comParams->get('form_theme', 'light-clean');
        $arr['tosUrl'] = JRoute::_(JURI::root()) . 'index.php?option=com_content&view=article&id=' . $comParams->get('tos_url') . '&tmpl=component&task=preview';
        $arr['charLimit'] = $params->get('char_limit') ? $params->get('char_limit') : $comParams->get('char_limit', '300');
        $arr['cwcLoadCss'] = $comParams->get('cwc_load_css', '1');
        $arr['submitBtn'] = $params->get('submit_btn') ? $params->get('submit_btn') : $comParams->get('submit_btn', JTEXT::_('COM_CWCONTACT_LABEL_SUB_BUTTON'));
        $arr['submitStyle'] = $params->get('submit_btn_style') ? $params->get('submit_btn_style') : $comParams->get('submit_btn_style', 'btn');
        $arr['submitCustom'] = $params->get('submit_btn_custom') ? $params->get('submit_btn_custom') : $comParams->get('submit_btn_custom', '');
        $arr['submitClass'] = $arr['submitStyle'] === 'custom' ? $arr['submitCustom'] : $arr['submitStyle'];
        $arr['formWidth'] = $params->get('form_width') ? $params->get('form_width') : $comParams->get('form_width', '100');
        $arr['emailSubject'] = $params->get('email_subject') ? $params->get('email_subject') : $comParams->get('email_subject', JTEXT::_('COM_CWCONTACT_LABEL_SUBJECT'));
        $arr['mailFromOpt'] = $comParams->get('mail_from', '2');
        $arr['emailFormat'] = $params->get('email_format') ? $params->get('email_format') : $comParams->get('email_format', 'nohtml');

        // Mail
        $arr['displayMailRequestA'] = $comParams->get('display_mail_request_a', '1');
        $arr['displayMailSentbyA'] = $comParams->get('display_mail_sentby_a', '1');
        $arr['displayMailSentfromA'] = $comParams->get('display_mail_sentfrom_a', '1');
        $arr['displayMailThankyou'] = $comParams->get('display_mail_thankyou', '0');
        $arr['mailThankyouTitle'] = $comParams->get('mail_thankyou_title', JTEXT::sprintf('COM_CWCONTACT_THANKYOU_TITLE', $app->getCfg('sitename')));
        $arr['mailThankyouMsg'] = $comParams->get('mail_thankyou_msg', JTEXT::_('COM_CWCONTACT_THANKYOU_MSG'));
        $arr['displayMailRequestC'] = $comParams->get('display_mail_request_c', '1');
        $arr['displayMailSentbyC'] = $comParams->get('display_mail_sentby_c', '1');
        $arr['displayMailSentfromC'] = $comParams->get('display_mail_sentfrom_c', '1');

        // Global
        $arr['sitename'] = $app->getCfg('sitename');
        $arr['mailFrom'] = $app->getCfg('mailfrom');
        $arr['mailFromName'] = $app->getCfg('fromname');

        // Captcha
        $arr['whichCaptcha'] = $params->get('which_captcha') ? $params->get('which_captcha') : $comParams->get('which_captcha', 'none');
        $arr['displayCapTitle'] = ($params->get('display_captcha_title') >= '0') ? $params->get('display_captcha_title') : $comParams->get('display_captcha_title', '1');
        $arr['captchaHeading'] = $comParams->get('captcha_heading', JTEXT::_('COM_CWCONTACT_CAPTCHA_HEADING'));
        $arr['captchaHint'] = $comParams->get('captcha_hint', JTEXT::_('COM_CWCONTACT_CAPTCHA_HINT'));
        $arr['bCaptchaQuestion'] = $params->get('bcaptcha_question') ? $params->get('bcaptcha_question') : $comParams->get('bcaptcha_question', JTEXT::_('COM_CWCONTACT_BCAPTCHA_DEFAULT_QUESTION'));
        $arr['bCaptchaAnswer'] = $params->get('bcaptcha_answer') ? $params->get('bcaptcha_answer') : $comParams->get('bcaptcha_answer', JTEXT::_('COM_CWCONTACT_BCAPTCHA_DEFAULT_ANSWER'));

        return $arr;
    }

    private function addAssets() {
        $doc = JFactory::getDocument();
        JHtml::_('jquery.framework');
        JHtml::_('behavior.formvalidator');

        $emailHelp = new EmailHelper();

        $urlModMedia = JURI::base(true) . '/media/coalawebcontact/components/contact/themes/' . $this->params['theme'] . '/css/';
        $urlModJs = JURI::base(true) . '/media/coalawebcontact/components/contact/js/';

        if ($this->params['cwcLoadCss']) {
            $doc->addStyleSheet($urlModMedia . 'cw-mod-contact-' . $this->params['theme'] . '.css');
        }

        if ($this->params['cwcLoadCss'] && ($this->params['submitStyle'] != 'btn' || $this->params['submitStyle'] != 'custom' )) {
            $doc->addStyleSheet(JURI::base(true) . '/media/coalawebcontact/components/contact/css/cwc-buttons.css');
        }

        //Custom HTML5 Messages
        $doc->addScript($urlModJs . 'jquery.html5cvm.min.js');

        //Character count
        $doc->addScript($urlModJs . 'jquery.simplyCountable.js');

        //CoalaWeb JS Tools
        $jsTools = $this->getJSTools();
        $jsCode = $emailHelp->cleanCode($jsTools);
        $doc->addScriptDeclaration($jsCode);


        return;
    }

    /**
     * Get all the needed JS tools together for use
     * 
     * @return js
     */
    private function getJSTools() {
        $charLimit = $this->params['charLimit'];

        $javascript[] = '//CoalaWeb Contact Module Tools ';
        $javascript[] = 'var $cw = jQuery.noConflict();';
        $javascript[] = '$cw(document).ready(function (){';
        
        //Custom messages
        $javascript[] = '$cw("#cw-mod-contact-' . $this->params['theme'] . '-fm' . $this->uniqueid . '").html5cvm();';

        if ($this->params['displayChar']) {
            $javascript[] = '   //Character Count ';
            $javascript[] = '   $cw("#cw_mod_contact_message' . $this->uniqueid . '").simplyCountable({';
            $javascript[] = '       maxCount:' . $charLimit . ',';
            $javascript[] = '       counter: "#cw_mod_contact_counter' . $this->uniqueid . '",';
            $javascript[] = '   });';  
        }


        if ($this->params['displayCInput1'] == 'Y' || $this->params['displayCInput1'] == 'R' && $this->params['typeCInput1'] === 'select') {
            $javascript[] = '   //Keep Custom 1 selection on page reload ';
            $javascript[] = '   var item = window.localStorage.getItem("cw_mod_contact_cinput1' . $this->uniqueid . '");';
            $javascript[] = '   $cw("select[name=cw_mod_contact_cinput1' . $this->uniqueid . ']").val(item);';
            $javascript[] = '   $cw("select[name=cw_mod_contact_cinput1' . $this->uniqueid . ']").change(function() {';
            $javascript[] = '   window.localStorage.setItem("cw_mod_contact_cinput1' . $this->uniqueid . '", $cw(this).val());';
            $javascript[] = '   });';
        }

        $javascript[] = '});';
        return implode("\n", $javascript);
    }

}
