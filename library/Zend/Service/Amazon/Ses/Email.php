<?php
class Zend_Service_Amazon_Ses_Email
{
    private $bodyHtml;
    private $bodyText;
    private $returnPath;
    private $fromAddress = array();
    private $toAddresses = array();
    private $replyToAddresses = array();
    private $ccAddresses = array();
    private $bccAddresses = array();
    private $charset;
    private $subject;
    /**
     * Gets the HTML part of the message body
     * @return string
     */
    public function getBodyHtml ()
    {
        return $this->bodyHtml;
    }
    public function getCharset ()
    {
        return $this->charset;
    }
    
    /**
     * Sets the HTML part of the message body
     * @param  string $bodyHtml
     * @param  string $charset (Optional)
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setBodyHtml ($bodyHtml, $charset = 'utf-8')
    {
        $this->bodyHtml = (string) $bodyHtml;
        $this->charset = $charset;
        return $this;
    }
    /**
     * Gets the Return Path
     * @return string
     */
    public function getReturnPath ()
    {
        return $this->returnPath;
    }
    /**
     * Sets the Return Path
     * @param  string $returnPath Email Address
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setReturnPath ($returnPath)
    {
        $this->returnPath = (string) $returnPath;
        return $this;
    }
    /**
     * Gets the Source (AWS Version of From Email Address)
     * @return string
     */
    public function getFrom ()
    {
        return $this->fromAddress;
    } 
    /**
     * Sets the From Address
     * @param  string $from RFC-822 Compliant Email Address
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setFrom ($from, $name = null)
    {
    	if($name !== null) {
    		 $this->fromAddress = "\"" . $name . "\" <" . $from . ">";
    	}
    	else {
        	$this->fromAddress = $from;
    	}
    }
    /**
     * Returns all the TO recipients
     * @return array
     */
    public function getTo ()
    {
        return $this->toAddresses;
    }
    /**
     * Add a TO address
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function addTo ($email, $name = null)
    {
        $this->toAddresses[$email] = $name;
        return $this;
    }
    /**
     * Clears all TO addresses
     * @return void
     */
    public function clearTo ()
    {
        $this->toAddresses = array();
    }
    /**
     * Sets the reply-to email address(es) for the message.
     * If the recipient replies to the message, each reply-to address will
     * receive the reply.
     *
     * @param  string $email Email Address
     * @param  string $name (Optional)
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function addReplyTo ($email, $name = null)
    {
        $this->replyToAddresses[$email] = $name;
        return $this;
    }
    /**
     * Gets the reply-to email address(es) for the message.
     *
     * @return array
     */
    public function getReplyTo ()
    {
        return $this->replyToAddresses;
    }
    /**
     * Clears the reply to addresses.
     * @return void
     */
    public function clearReplyTo ()
    {
        $this->replyToAddresses = array();
    }
    /**
     * Gets registered CC addresses
     * @return array
     */
    public function getCc ()
    {
        return $this->ccAddresses;
    }
    /**
     * Clears CC Addresses
     * @return void
     */
    public function clearCc ()
    {
        $this->ccAddresses = array();
    }
    /**
     * Adds the CC address
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses
     */
    public function addCc ($email, $name = null)
    {
        $this->ccAddresses[$email] = $name;
    }
    /**
     * Gets the BCC email addresses
     * @return array
     */
    public function getBcc ()
    {
        return $this->bccAddresses;
    }
    /**
     * Adds a BCC address
     *
     * @param  string $email
     * @param  string $name
     * @return Zend_Service_Amazon_Ses
     */
    public function addBcc ($email, $name = null)
    {
        $this->bccAddresses[$email] = $name;
        return $this;
    }
    /**
     * Clears BCC Addresses
     * @return void
     */
    public function clearBcc ()
    {
        $this->bccAddresses = array();
    }
    /**
     * Clears all recipients
     *
     * @return void
     */
    public function clearRecipients ()
    {
        $this->clearBcc();
        $this->clearCc();
        $this->clearTo();
    }
    /**
     * Gets the message subject
     *
     * @return string
     */
    public function getSubject ()
    {
        return $this->subject;
    }
    /**
     * Sets the message subject
     *
     * @param string $subject
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setSubject ($subject)
    {
        $this->subject = (string) $subject;
    }
    /**
     * Gets the text part of the mail message
     *
     * @return string
     */
    public function getBodyText ()
    {
        return $this->bodyText;
    }
    /**
     * Sets the text part of the mail message
     *
     * @param  string $bodyText
     * @return Zend_Service_Amazon_Ses_Email
     */
    public function setBodyText ($bodyText, $charset = 'utf-8')
    {
        $this->bodyText = $bodyText;
        $this->charset = $charset;
    }
    /**
     * Returns the parameters needed to make a SendEmail request to SES
     *
     * @return Zend_Service_Amazon_Ses_Response_SendEmail
     */
    public function getParams ()
    {}
}
