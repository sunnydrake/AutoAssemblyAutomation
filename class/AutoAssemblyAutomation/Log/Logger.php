<?php

namespace AutoAssemblyAutomation\Log;

use DateTime;

/**
 * Logger Class
 */
class Logger
{
    //Date Setup
    private string $timeFormat = DateTime::W3C;
    //Mail Setup
    /**
     * @var string email to send mail
     */
    private string $to = "admin@localhost";
    /**
     * @var string Mail Subject
     */
    private string $subject = "AutoAssemblyAutomation Logger Write";
    //File setup
    /**
     * @var string File name to write log
     */
    private string $fileName = 'AutoAssemblyAutomation.log';
    /**
     * @var array Array of Logger Functions to perform message storage
     */
    private array $refWrite;//: array of reference to FileWrite or MailWrite functions;

    /**
     * @param array $options Array of options for logger initialization (names of message storage functions ['mailWrite','fileWrite'])
     */
    public function __construct(array $options = ['mailWrite', 'fileWrite'])
    {
        foreach ($options as $opt)
            if (method_exists($this, $opt)) $this->refWrite[] = $opt;
    }

    /**
     * Delivery Message to Log
     * @param string $message
     * @return bool
     */
    public function message(string $message): bool
    {
        $allOk = true;
        $message = "[" . (new DateTime())->format($this->timeFormat) . "] " . $message;
        foreach ($this->refWrite as $opt) if ($this->$opt($message) === false) $allOk = false;
        return $allOk;
    }

    /**
     * Write Message to Mail
     * @param string $message
     * @return bool
     */
    private function mailWrite(string $message): bool
    {
        return mail($this->to, $this->subject, $message);
    }

    /**
     * Write Message to File
     * @param string $message
     * @return bool
     */
    private function fileWrite(string $message): bool
    {
        return file_put_contents($this->fileName, $message, FILE_APPEND);
    }
}