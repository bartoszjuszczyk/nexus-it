<?php

/**
 * File: SendTicketReviewMail.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\EventListener;

use App\Event\TicketClosedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsEventListener(event: TicketClosedEvent::class, priority: 20)]
class SendTicketReviewMail
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function __invoke(TicketClosedEvent $event): void
    {
        $ticket = $event->getTicket();
        $author = $ticket->getAuthor();

        $email = (new TemplatedEmail())
            ->to(new Address($author->getEmail(), $author->getFullname()))
            ->subject('Rate the quality of service of the ticket: '.$ticket->getTitle())
            ->htmlTemplate('emails/quality_survey.html.twig')
            ->context([
                'userName' => $author->getFullname(),
                'ticketTitle' => $ticket->getTitle(),
                'ticketId' => $ticket->getId(),
            ]);
        $this->mailer->send($email);
    }
}
