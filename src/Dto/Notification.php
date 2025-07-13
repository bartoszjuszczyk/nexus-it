<?php

/**
 * File: Notification.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Dto;

class Notification
{
    private string $subject;
    private array $recipients;
    private string $template;
    private array $context;

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): Notification
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Notification
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): Notification
    {
        $this->template = $template;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): Notification
    {
        $this->context = $context;

        return $this;
    }
}
