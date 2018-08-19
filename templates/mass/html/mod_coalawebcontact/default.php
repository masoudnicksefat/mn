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

?>

<div class="custom<?php echo $myparams['moduleclass_sfx']; ?>">
    <div class="cw-mod-contact-<?php echo $myparams['theme'] . $myparams['formWidth']; ?>" id="<?php echo $uniqueId ?>">
        <?php if (!$checkRecipient || !JFactory::getConfig()->get('mailonline')) : ?>
            <div id="cw-mod-contact-<?php echo $myparams['theme']; ?>">
                <div class="cwc-msg">
                    <?php if (!$checkRecipient) : ?>
                        <span class="error">
                            <?php echo $myparams['msgRemailMissing']; ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!JFactory::getConfig()->get('mailonline')) : ?>
                        <span class="error">
                        <?php echo JText::_('JLIB_MAIL_FUNCTION_OFFLINE'); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php else : ?>
            <div id="cw-mod-contact-<?php echo $myparams['theme']; ?>">

                <?php if (isset($emailSent) && $emailSent == true) : ?>
                    <?php $app->redirect($sucessUrl, $myparams['msgEmailSent']); ?>
                <?php else : ?>

                    <?php if (isset($msgFailed)) : ?>
                        <div class="cwc-msg">
                            <span class="error">
                                <?php echo $msgFailed; ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <form 
                        id="cw-mod-contact-<?php echo $myparams['theme'].'-fm'.$uniqueId ?>" 
                        action="<?php echo $samePageUrl . '#cw-mod-contact-'.$myparams['theme'].'-fm'.$uniqueId ?>" 
                        method="post" 
                        enctype="multipart/form-data" 
                        class="form-validate">
                        
                        <?php if ($myparams['displayAsteriskMsg']) : ?>
                            <div class="cwc-msg">
                                <span class="success">
                                    <?php echo $myparams['msgAsterisk']; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($myparams['displayName'] == 'Y' || $myparams['displayName'] == 'R') : ?>
                        <!-- Start Name -->
                            <div class="input form-group">
                                <label for="cw_mod_contact_name<?php echo $uniqueId ?>">
                                    <?php echo $myparams['nameLbl'] . (($myparams['displayName'] == 'R') ? JTEXT::_('COM_CWCONTACT_ASTERISK') : ''); ?>
                                </label>
                                <input 
                                    type="text" 
                                    name="cw_mod_contact_name<?php echo $uniqueId ?>" 
                                    id="cw_mod_contact_name<?php echo $uniqueId ?>" 
                                    value="<?php echo (isset($name) ? $name : null )?>" 
                                    class="form-control <?php echo (($myparams['displayName'] === 'R') ? 'required' : null) ?>" 
                                    <?php echo (($myparams['displayName'] === 'R') ? 'required' : null) ?>
                                    data-error-value-missing="<?php echo $myparams['msgNameMissing']; ?>" 
                                    placeholder="<?php echo $myparams['nameHint']; ?>" />
                                <?php if (isset($nameError)) : ?>
                                <div class="cwc-msg">
                                    <span class="error">
                                        <?php echo $nameError; ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        <!-- End Name -->
                        <?php endif; ?>

                        <?php if ($myparams['displayEmail'] == 'Y' || $myparams['displayEmail'] == 'R') : ?>
                        
                        <?php if ($cloak && $cloakOn) : ?>
                           <?php echo '<!-- {emailcloak=off} -->'; ?>
                        <?php endif; ?>
                            
                        <!-- Start Email -->
                        <div class="input form-group">
                            <label for="cw_mod_contact_email<?php echo $uniqueId ?>">
                                <?php echo $myparams['emailLbl'] . (($myparams['displayEmail'] == 'R') ? JTEXT::_('COM_CWCONTACT_ASTERISK') : ''); ?>
                            </label>
                            <input 
                                type="email" 
                                name="cw_mod_contact_email<?php echo $uniqueId ?>" 
                                id="cw_mod_contact_email<?php echo $uniqueId ?>" 
                                value="<?php echo (isset($email) ? $email : null )?>" 
                                class="form-control <?php echo (($myparams['displayEmail'] === 'R') ? 'required validate-email' : 'validate-email') ?>" 
                                <?php echo (($myparams['displayEmail'] === 'R') ? 'required' : null) ?>
                                data-error-type-mismatch="<?php echo $myparams['msgEmailInvalid']; ?>" 
                                data-error-value-missing="<?php echo $myparams['msgEmailMissing']; ?>" 
                                placeholder="<?php echo $myparams['emailHint']; ?>" />
                            <?php if (isset($emailError)) : ?>
                            <div class="cwc-msg">
                                <span class="error">
                                    <?php echo $emailError; ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <!-- End Email -->
                        <?php endif; ?>
                        
                        <?php if ($myparams['displaySubject'] == 'Y' || $myparams['displaySubject'] == 'R') : ?>
                        <!-- Start Subject -->
                            <div class="input form-group">
                                <label for="cw_mod_contact_subject<?php echo $uniqueId ?>">
                                    <?php echo $myparams['subjectLbl'] . (($myparams['displaySubject'] == 'R') ? JTEXT::_('COM_CWCONTACT_ASTERISK') : ''); ?>
                                </label>
                                <input 
                                    type="text" 
                                    name="cw_mod_contact_subject<?php echo $uniqueId ?>" 
                                    id="cw_mod_contact_subject<?php echo $uniqueId ?>" 
                                    value="<?php echo (isset($subject) ? $subject : null )?>" 
                                    class="form-control <?php echo (($myparams['displaySubject'] === 'R') ? 'required' : null) ?>" 
                                    <?php echo (($myparams['displaySubject'] === 'R') ? 'required' : null) ?>
                                    data-error-value-missing="<?php echo $myparams['msgSubjectMissing']; ?>" 
                                    placeholder="<?php echo $myparams['subjectHint']; ?>" />
                                <?php if (isset($subjectError)) : ?>
                                <div class="cwc-msg">
                                    <span class="error">
                                        <?php echo $subjectError; ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        <!-- End Subject -->
                        <?php endif; ?>
                        
                        <?php if ($myparams['displayCInput1'] == 'Y' || $myparams['displayCInput1'] == 'R') : ?>
                        <!-- Start Custom Input 1 -->
                            <div class="inpu form-groupt">
                                <label for="cw_mod_contact_cinput1<?php echo $uniqueId ?>">
                                    <?php echo $myparams['cInput1Lbl'] . (($myparams['displayCInput1'] == 'R') ? JTEXT::_('COM_CWCONTACT_ASTERISK') : ''); ?>
                                </label>
                                <?php if ($myparams['typeCInput1'] == 'text' ) : ?>
                                    <input 
                                        type="text" 
                                        name="cw_mod_contact_cinput1<?php echo $uniqueId ?>" 
                                        id="cw_mod_contact_cinput1<?php echo $uniqueId ?>" 
                                        value="<?php echo (isset($cInput1) ? $cInput1 : null )?>" 
                                        class="form-control <?php echo (($myparams['displayCInput1'] === 'R') ? 'required' : null) ?>" 
                                        <?php echo (($myparams['displayCInput1'] === 'R') ? 'required' : null) ?>
                                        data-error-value-missing="<?php echo $myparams['msgCInput1Missing']; ?>" 
                                        placeholder="<?php echo $myparams['cInput1Hint']; ?>" />
                                <?php elseif ($myparams['typeCInput1'] == 'textarea' ) : ?>
                                    <textarea 
                                        name="cw_mod_contact_cinput1<?php echo $uniqueId ?>" 
                                        id="cw_mod_contact_cinput1<?php echo $uniqueId ?>" 
                                        class="<?php echo (($myparams['displayCInput1'] === 'R') ? 'required' : null) ?>" 
                                        <?php echo (($myparams['displayCInput1'] === 'R') ? 'required' : null) ?>
                                        rows="6" 
                                        maxlength="<?php echo $myparams['charLimit']; ?>" 
                                        data-error-value-missing="<?php echo $myparams['msgCInput1Missing']; ?>" 
                                        placeholder="<?php echo $myparams['cInput1Hint']; ?>"><?php echo (isset($cInput1) ? $cInput1 : null )?></textarea>
                                <?php elseif ($myparams['typeCInput1'] == 'select' ) : ?> 
                                    <select 
                                        name="cw_mod_contact_cinput1<?php echo $uniqueId ?>" 
                                        id="cw_mod_contact_cinput1<?php echo $uniqueId ?>" 
                                        class="<?php echo (($myparams['displayCInput1'] === 'R') ? 'required' : null) ?>"
                                        <?php echo (($myparams['displayCInput1'] === 'R') ? 'required' : null) ?>
                                        data-error-value-missing="<?php echo $myparams['msgCInput1Missing']; ?>">
                                        <option value="" selected><?php echo $myparams['cInput1Hint']; ?></option>
                                    <?php echo $myparams['selectCInput1']; ?>
                                    </select>
                                <?php endif; ?>
                                <?php if (isset($cInput1Error)) : ?>
                                <div class="cwc-msg">
                                    <span class="error">
                                        <?php echo $cInput1Error; ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>  
                        <!-- End Custom Input 1 -->
                        <?php endif; ?>
                        
                        <?php if ($myparams['displayMessage'] == 'Y' || $myparams['displayMessage'] == 'R') : ?>
                            <!-- Start Message -->
                            <div class="input">
                                <label for="cw_mod_contact_message<?php echo $uniqueId ?>">
                                    <?php echo $myparams['messageLbl'] . (($myparams['displayMessage'] == 'R') ? JTEXT::_('COM_CWCONTACT_ASTERISK') : ''); ?>
                                </label>
                                <textarea 
                                    name="cw_mod_contact_message<?php echo $uniqueId ?>" 
                                    id="cw_mod_contact_message<?php echo $uniqueId ?>" 
                                    class="form-control <?php echo (($myparams['displayMessage'] === 'R') ? 'required' : null) ?>"
                                    <?php echo (($myparams['displayMessage'] === 'R') ? 'required' : null) ?>
                                    rows="6" 
                                    maxlength="<?php echo $myparams['charLimit']; ?>" 
                                    data-error-value-missing="<?php echo $myparams['msgMessageMissing']; ?>" 
                                    placeholder="<?php echo $myparams['messageHint']; ?>"><?php echo (isset($message) ? $message : null )?></textarea>
                                <?php if (isset($msgError)) : ?>
                                    <div class="cwc-msg">
                                        <span class="error">
                                            <?php echo $msgError; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- End Message -->
                        <?php endif; ?>
                        
                        <?php if ($myparams['whichCaptcha'] == 'basic') : ?>
                            <?php if ($myparams['displayCapTitle']) : ?>
                            <div class="cwc-msg">
                                <span class="success">
                                    <?php echo $myparams['captchaHeading']; ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <div class="input">
                                <label for="cw_mod_contact_captcha<?php echo $uniqueId; ?>">
                                <?php echo $myparams['bCaptchaQuestion']; ?>
                                </label> 
                                <input 
                                    type="text" 
                                    class="required" 
                                    required
                                    name="cw_mod_contact_captcha<?php echo $uniqueId ?>" 
                                    id="cw_mod_contact_captcha<?php echo $uniqueId ?>" 
                                    value="<?php echo (isset($capEnter) ? $capEnter : null )?>" 
                                    data-error-value-missing="<?php echo $myparams['msgCaptchaWrong']; ?>" 
                                    placeholder="<?php echo $myparams['captchaHint']; ?>"/>
                                <?php if (isset($captchaError)) : ?>
                                    <div class="cwc-msg">
                                        <span class="error">
                                            <?php echo $captchaError; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($myparams['displayCopyme']) : ?>
                            <div class="copyme">
                                <input 
                                    type="checkbox" 
                                    name="cw_mod_contact_copyme<?php echo $uniqueId ?>" 
                                    id="cw_mod_contact_copyme<?php echo $uniqueId ?>" 
                                    value="1" <?php echo ($copyme == "1" ? 'checked="checked"' : null )?> 
                                    class="copyme" />
                                <label for="cw_mod_contact_copyme<?php echo $uniqueId ?>">
                                    <?php echo $myparams['copymeLbl']; ?>
                                </label>
                            </div>
                        <?php endif; ?>

                        <div class="cw-mod-contact-<?php echo $myparams['theme']; ?>-buttons">
                            <div class="btn-submit">
                                <button 
                                    name="submit<?php echo $uniqueId ?>" 
                                    type="submit" 
                                    id="submit<?php echo $uniqueId ?>" 
                                    class="<?php echo $myparams['submitClass']; ?> btn-success"><?php echo $myparams['submitBtn']; ?>
                                </button>
                                <input 
                                    type="hidden" 
                                    name="cw_mod_contact_sub<?php echo $uniqueId ?>" 
                                    id="cw_mod_contact_sub<?php echo $uniqueId ?>" 
                                    value="true" />
                                <?php echo JHTML::_( 'form.token' ); ?>
                            </div>
                        </div>
                    </form>			
                <?php endif; ?>
                
            </div>
<?php endif; ?>
    </div>
</div>
