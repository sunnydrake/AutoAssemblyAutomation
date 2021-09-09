<?php

namespace AutoAssemblyAutomation\Notify;

use DateTime;

class ClientMail
{
    //Date Setup
    /**
     * @var string email to send mail
     */
    public string $clientMail = "admin@localhost";
    //Mail Setup
    private string $timeFormat = DateTime::W3C;
    /**
     * @var string Mail Subject
     */
    private string $subject = "AutoAssemblyAutomation Notify";
    /**
     * @var array Array of Notify Functions to perform message delivery
     */
    private array $refWrite;//: array of reference to FileWrite or MailWrite functions;

    /**
     * @param string $email Client eMail Mandatory!
     * @param array $options Array of options for Client initialization ['mailWrite']
     */
    public function __construct(string $email = "admin@localhost", array $options = ['mailWrite'])
    {
        if (empty($email)) return;
        $this->clientMail = $email;
        foreach ($options as $opt)
            if (method_exists($this, $opt)) $this->refWrite[] = $opt;
    }

    /**
     * Delivery Message to Client
     * @param string $message
     * @return bool
     */
    public function message(string $message): bool
    {
        if (empty($this->clientMail)) return false;
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
        return mail($this->clientMail, $this->subject, $message);
    }
    /**
     * Write Message to console
     * @param string $message
     * @return bool
     */
    private function consoleWrite(string $message): bool
    {
        return print("\nMAIL\nTo:$this->clientMail \nSubject:\n$this->subject \nMessage:\n$message");
    }
    /**
     * Write Message to console Only
     * @param mixed $param for compat reasons
     */
    public function setConsoleOnly(mixed $param)
    {
        if (isset($this->consoleWrite)) {
            $this->refWrite=[$this->consoleWrite];
        }
    }
}