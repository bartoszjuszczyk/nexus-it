<?php

/**
 * File: EmailChannel.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Channel;

use App\Dto\Notification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailChannel implements ChannelInterface
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function send(Notification $notification): void
    {
        foreach ($notification->getRecipients() as $recipient) {
            $email = (new TemplatedEmail())
                ->to(new Address($recipient['email'], $recipient['name']))
                ->subject($notification->getSubject())
                ->htmlTemplate($notification->getTemplate())
                ->context($notification->getContext());
            $this->mailer->send($email);
        }
    }
}
