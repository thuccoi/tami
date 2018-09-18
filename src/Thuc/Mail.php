<?php

namespace Thuc;

use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class Mail {

    protected $username;
    protected $password;
    protected $smtpOptions;
    protected $subject;
    protected $body;
    protected $to;
    protected $arrto = [];

    public function __construct($username, $password, $subject = null, $body = null, $to = null) {
        $this->username = $username;
        $this->password = $password;
        
        if ($subject) {
            $this->subject = $subject;
        }
        if ($body) {
            $this->body = $body;
        }
        if ($to) {
            $this->arrto[] = $to;
        }

        $this->smtpOptions = new \Zend\Mail\Transport\SmtpOptions();
        $this->smtpOptions->setHost('smtp.gmail.com')
                ->setConnectionClass('login')
                ->setName('smtp.gmail.com')
                ->setConnectionConfig(array(
                    'username' => $username,
                    'password' => $password,
                    'ssl' => 'tls'
        ));
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function addTo($to) {

        $this->arrto[] = $to;

        return $this;
    }

    public function send($subject = null, $body = null, $to = null) {

        if ($subject) {
            $this->subject = $subject;
        }
        if ($body) {
            $this->body = $body;
        }
        if ($to) {
            $this->arrto[] = $to;
        }

        $this->arrto = array_unique($this->arrto);

        $message = new \Zend\Mail\Message();
        $message->setBody($this->body);
        $message->setFrom($this->username);

        foreach ($this->arrto as $to) {
            $message->addTo($to);
        }

        $message->setSubject($this->subject);

        $html = new MimePart($this->body);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $message->setBody($body);

        $transport = new \Zend\Mail\Transport\Smtp($this->smtpOptions);

        $transport->send($message);
        return $this;
    }

}
