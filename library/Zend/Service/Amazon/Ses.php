<?php
class Zend_Service_Amazon_Ses extends Zend_Service_Amazon_Abstract
{
    private $endpoint;
    private $httpClient;
    public function __construct ($key, $secret)
    {
        parent::__construct($key, $secret);
    }
    /**
     * Sets an alternative endpoint to the default
     *
     * @param  Zend_Uri_Http $endpoint
     * @return Zend_Service_Amazon_Ses
     * @throws InvalidArgumentException If the provided endpoint url is not valid
     */
    public function setEndpoint ($endpoint)
    {
        if (! ($endpoint instanceof Zend_Uri_Http)){
            $endpoint = Zend_Uri::factory($endpoint);
        }
        $this->endpoint = $endpoint;
        return $this;
    }
    /**
     * Gets the provided endpoint
     *
     * @return Zend_Uri_Http
     */
    public function getEndpoint ()
    {
        return $this->endpoint;
    }

    public function getMyHttpClient() {
    	if($this->httpClient === null) {
    		$this->httpClient = new Zend_Http_Client($this->endpoint);
    	}
    	return $this->httpClient;
    }

    public function setMyHttpClient(Zend_Http_Client $h) {
    	$this->httpClient = $h;
    	return $this;
    }

    /**
     * Verifies an email address.
     * This action causes a confirmation email message to be sent to the
     * specified address.
     *
     * @param  RFC-822 Compliant Email Address $email
     * @return void
     */
    public function verifyEmailAddress ($email)
    {
        $options['EmailAddress'] = $email;
        $this->makeApiCall('VerifyEmailAddress', $options);
        return;
    }
    /**
     * Sends an email message, with header and content specified by the client.
     *
     * The SendRawEmail action is useful for sending multipart MIME emails.
     * The raw text of the message must comply with Internet email standards;
     * otherwise, the message cannot be sent.
     *
     * Raw email must:
     * + Message must contain a header and a body, separated by a blank line.
     * + All required header fields must be present.
     * + Each part of a multipart MIME message must be formatted properly.
     * + MIME content types must be among those supported by Amazon SES.
     * Refer to the Amazon SES Developer Guide for more details.
     * + Content must be base64-encoded, if MIME requires it.
     *
     * @param  string $message    The raw text of the message.
     * @param  string $from       (Optional) From email address, if not included in the raq email's headers.
     * @param  array  $recipients (Optional) Additional receipients to what's provided in the raw email's headers.
     * @return string AWS Message Id
     * @throws RuntimeException   If the AWS repsonse is missing XML elements
     */
    public function sendRawEmail ($message, $from = null,
    array $recipients = array())
    {
        $options = array();
        $options['RawMessage.Data'] = base64_encode($message);
        if ($from !== null)
            $options['Source'] = $from;
        $i = 1;
        foreach ($recipients as $add => $name) {
            $options['Destination.member.' . $i] = $add;
            $i ++;
        }
        $messageDom = new DOMDocument();
        $messageDom->loadXML($this->makeApiCall('SendRawEmail', $options));
        $responseMessage = $messageDom->getElementsByTagName('MessageId');
        return $responseMessage->item(0)->nodeValue;
    }
    /**
     * Returns a list containing all of the email addresses that have been verified.
     *
     * @return array
     */
    public function listVerifiedEmailAddresses ()
    {
        $emails = array();
        $apiResult = $this->makeApiCall('ListVerifiedEmailAddresses');
        $result = new DOMDocument();
        $result->loadXML($apiResult);
        $emailList = $result->getElementsByTagName('VerifiedEmailAddresses');
        $emailList = $emailList->item(0)->getElementsByTagName('member');
        foreach ($emailList as $e) {
            $emails[] = $e->nodeValue;
        }
        return $emails;
    }
    /**
     * Composes an email message based on input data and then immediately queues the message for sending.
     *
     * @param  Zend_Service_Amazon_Ses_Email $email
     * @return string AWS Message Id
     * @throws RuntimeException If the AWS request did not return the properly formatted XML response.
     */
    public function sendEmail (Zend_Service_Amazon_Ses_Email $email)
    {
        $options = array();
        // build the option for the destination addresses
        // will have to add something for a case of more than 50 email
        // addresses?
        $addresses = $email->getTo();
        $i = 1;
        foreach ($addresses as $add => $name) {
            $options['Destination.ToAddresses.member.' . $i] = "\"" . $name . "\" <" . $add . ">";
            $i ++;
        }
        $addresses = $email->getCC();
        $i = 1;
        foreach ($addresses as $add => $name) {
            $options['Destination.CcAddresses.member.' . $i] = "\"" . $name . "\" <" . $add . ">";
            $i ++;
        }
        $addresses = $email->getBcc();
        $i = 1;
        foreach ($addresses as $add => $name) {
            $options['Destination.BccAddresses.member.' . $i] = "\"" . $name . "\" <" . $add . ">";
            $i ++;
        }
        $addresses = $email->getReplyTo();
        $i = 1;
        foreach ($addresses as $add => $name) {
            $options['ReplyToAddresses.member.' . $i] =  "\"" . $name . "\" <" . $add . ">";
            $i ++;
        }
        if ($email->getReturnPath() !== null)
            $options['ReturnPath'] = $email->getReturnPath();
        $options['Source'] = $email->getFrom();
        $options['Message.Subject.Data'] = $email->getSubject();
        if ($email->getBodyHtml() !== null) {
            $options['Message.Body.Html.Data'] = $email->getBodyHtml();
            $options['Message.Body.Html.Charset'] = $email->getCharset();
        }
        if ($email->getBodyText() !== null)
            $options['Message.Body.Text.Data'] = $email->getBodyText();
        $messageDom = new DOMDocument();
        $responseMessage = $messageDom->loadXML(
        $this->makeApiCall('SendEmail', $options));
        $responseMessage = $messageDom->getElementsByTagName('MessageId');
        return $responseMessage->item(0)->nodeValue;
    }
    /**
     * Returns the user's current activity limits.
     *
     * The following array keys are returned:
     *
     * max24HourSend: The maximum number of emails the user is
     * allowed to send in a 24-hour interval.
     *
     * maxSendRate: The maximum number of emails the
     * user is allowed to send per second.
     *
     * sentLast24Hours: The number of emails sent during
     * the previous 24 hours.
     *
     * @return array
     * @throws RuntimeException If the AWS request did not return the properly formatted XML response.
     */
    public function getSendQuota ()
    {
        $apiResult = $this->makeApiCall('GetSendQuota');
        $result = new DOMDocument();
        $result->loadXML($apiResult);
        $quotas = array();
        $quotas['max24HourSend'] = $result->getElementsByTagName(
        'Max24HourSend')->item(0)->nodeValue;
        $quotas['maxSendRate'] = $result->getElementsByTagName('MaxSendRate')->item(
        0)->nodeValue;
        $quotas['sentLast24Hours'] = $result->getElementsByTagName(
        'SentLast24Hours')->item(0)->nodeValue;
        return $quotas;
    }
    /**
     * Deletes the specified email address from the list of verified addresses.
     *
     * @param string $email RFC-822 Compliant Email Address
     * @return void
     */
    public function deleteVerifiedEmailAddress ($email)
    {
        $options = array();
        $options['EmailAddress'] = $email;
        $this->makeApiCall('DeleteVerifiedEmailAddress', $options);
    }
    private function makeApiCall ($service, $options = null)
    {
        $httpClient = $this->getMyHttpClient();
        $options['Action'] = $service;
        if ($options !== null) {
            $httpClient->setParameterPost($options);
        }
        // Create the signature headers
        $date = gmdate('D, d M Y H:i:s e');
        $authString = 'AWS3-HTTPS AWSAccessKeyId=' . $this->_getAccessKey() .
         ', Algorithm=HmacSHA256, Signature=' .
         base64_encode(hash_hmac('sha256', $date, $this->_getSecretKey(), true)) .
         '';
        $httpClient->setHeaders('Date', $date);
        $httpClient->setHeaders('X-Amzn-Authorization', $authString);
        $httpClient->request('POST');
//        var_dump($httpClient->getLastRequest());
        //var_dump($httpClient->getLastResponse());
        $response = $httpClient->getLastResponse()->getBody();
        // check for errors
//        print_r($response); exit;
        $errorDom = new DOMDocument();
        try {
        	$errorDom->loadXML($response);
        } catch(Exception $e) {
        	throw (new Zend_Service_Amazon_Ses_Exception('Amazon did not return a valid XML response'));
        }
        $errors = $errorDom->getElementsByTagName('ErrorResponse');
        if ($errors->length > 0) {
            $errorMessage = $errors->item(0)
                ->getElementsByTagName('Message')
                ->item(0)->nodeValue;
            $errorRequestId = $errors->item(0)
                ->getElementsByTagName('RequestId')
                ->item(0)->nodeValue;
            throw (new Zend_Service_Amazon_Ses_Exception($errorMessage, 0, null,
            $errorRequestId));
        }
        return $response;
    }
}
