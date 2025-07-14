<?php

/**
 * File: NewSupportCommentNotifier.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Notifier;

use App\Dto\Notification;
use App\Entity\Ticket\TicketEvent;

class NewSupportCommentNotifier implements NotifierInterface
{
    public function supports(TicketEvent $event): bool
    {
        return $event instanceof TicketEvent\SupportCommentEvent;
    }

    public function createNotification(TicketEvent $event): Notification
    {
        $ticket = $event->getTicket();
        $ticketAuthor = $ticket->getAuthor();
        $eventAuthor = $event->getAuthor();

        return (new Notification())
            ->setSubject(sprintf('Ticket #%s: New support comment in ticket.', $ticket->getId()))
            ->setRecipients([
                [
                    'email' => $ticketAuthor->getEmail(),
                    'name' => $ticketAuthor->getFullname(),
                ],
            ]
            )
            ->setTemplate('emails/ticket-events/new-support-comment.html.twig')
            ->setContext([
                'ticketId' => $ticket->getId(),
                'ticketTitle' => $ticket->getTitle(),
                'eventAuthorName' => $eventAuthor->getFullname(),
                'comment' => $event->getComment(),
            ]);
    }
}
