<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2017 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die('Restricted access');

/**
 *  Novarain Framework Emailer
 */
class Email
{
    /**
     *  Indicates the last error
     *
     *  @var  string
     */
    public $error;

    /**
     *  Email Object
     *
     *  @var  email data to be sent
     */
    private $email;

    /**
     *  Required elements for a valid email object
     *
     *  @var  array
     */
    private $requiredKeys = array(
        'from_email',
        'from_name',
        'recipient',
        'subject',
        'body'
    );

    /**
     *  Class constructor
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     *  Validates Email Object
     *
     *  @param   array  $email  The email object
     *
     *  @return  boolean        Returns true if the email object is valid
     */
    public function validate()
    {
        // Validate email object
        if (!$this->email || !is_array($this->email) || !count($this->email))
        {
            $this->setError('Invalid email object.');
            return;
        }

        // Check for missing properties
        foreach ($this->requiredKeys as $key)
        {
            if (!isset($this->email[$key]) || empty($this->email[$key]))
            {
                $this->setError("The $key field is either missing or invalid.");
                return;
            }
        }

        // Validate recipient email addresses. Pass multiple recipients separated by comma.
        $recipients = explode(',', $this->email['recipient']);

        foreach ($recipients as $key => $recipient)
        {
            if (!$this->validateEmailAddress($recipient))
            {
                $this->setError("Invalid recipient email address: $recipient");
                return;
            }
        }

        $this->email['recipient'] = $recipients;

        // Validate sender email address
        if (!$this->validateEmailAddress($this->email['from_email']))
        {
            $this->setError('Invalid sender email address: ' . $this->email['from_email']);
            return;  
        }

        return true;
    }

    /**
     *  Sending emails
     *
     *  @param   array  $email  The mail objecta
     *
     *  @return  mixed          Returns true on success. Throws exeption on fail.
     */
    public function send()
    {
        // Validate first the email object
        if (!$this->validate($this->email))
        {
            return;
        }

        $email  = $this->email;
        $mailer = \JFactory::getMailer();
        $mailer->CharSet = 'UTF-8';

        // Email Sender
        $mailer->setSender(
            array(
                $email['from_email'],
                $email['from_name']
            )
        );

        // Reply-to
        if (isset($email['reply_to']))
        {
            $mailer->addReplyTo($email['reply_to']);
        }

        $mailer
            ->addRecipient($email['recipient'])
            ->isHTML(true)
            ->setSubject($email['subject'])
            ->setBody($email['body']);
        
        $mailer->AltBody = strip_tags($email['body']);

        // Send mail
        $send = $mailer->Send();
        
        if ($send !== true)
        {
            $this->setError($send->__toString());
            return;
        }

        return true;
    }

    /**
     *  Set Class Error
     *
     *  @param  string   $error   The error message
     */
    private function setError($error)
    {
        $this->error = 'Error sending email: ' . $error;
        Functions::log($error);
    }

    /**
     *  Removes all illegal characters and validates an email address
     *
     *  @param   string  $email  Email address string
     *
     *  @return  bool
     */
    private function validateEmailAddress($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

?>