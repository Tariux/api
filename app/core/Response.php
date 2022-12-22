<?php

namespace JAPI;

class Response {

    public $message;
    public $logType;
    public $status;
    public $responseData;

    function __construct (
        array $responseData = [] ,
        string $message = "" ,
        bool $status = false ,
        string $logType = 'error' 
    ) {

        $this->message = $message;
        $this->status = $status;
        $this->logType = $logType;

        if (empty($responseData) || $responseData === null || !isset($responseData)) {
            $this->status = !isset($status) ? false : $status;
            $this->logType = !isset($logType) ? 400 : $logType;
            unset($this->responseData);
        } else {
            $this->responseData = $responseData;
        }

        print(json_encode($this));

        die();

        
    }
}