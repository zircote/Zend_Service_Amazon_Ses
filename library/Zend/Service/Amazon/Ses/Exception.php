<?php
class Zend_Service_Amazon_Ses_Exception extends Zend_Service_Amazon_Exception
{
    protected $requestId;
    public function __construct ($message, $code = 0, Exception $previous = null, 
    $requestId = null)
    {
        $this->requestId = $requestId;
        parent::__construct($message, $code, $previous);
    }
    public function getRequestId ()
    {
        return $this->requestId;
    }
}
