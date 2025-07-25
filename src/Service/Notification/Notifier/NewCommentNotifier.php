<?php

/**
 * File: NewSupportCommentNotifier.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Notifier;

use App\Dto\Notification;
use App\Entity\Ticket\TicketEvent;

class NewCommentNotifier implements NotifierInterface
{
    public function supports(TicketEvent $event): bool
    {
        return $event instanceof TicketEvent\CommentEvent;
    }

    public function createNotification(TicketEvent $event): Notification
    {
        $ticket = $event->getTicket();
        $ticketAuthor = $ticket->getAuthor();
        $workerAssigned = $ticket->getAssignedTo();

        return (new Notification())
            ->setSubject(sprintf('Ticket #%s: New comment in ticket.', $ticket->getId()))
            ->setRecipients([
                [
                    'email' => $workerAssigned->getEmail(),
                    'name' => $workerAssigned->getFullname(),
                ],
            ]
            )
            ->setTemplate('emails/ticket-events/new-comment.html.twig')
            ->setContext([
                'ticketId' => $ticket->getId(),
                'ticketTitle' => $ticket->getTitle(),
                'eventAuthorName' => $ticketAuthor->getFullname(),
                'comment' => $event->getComment(),
            ]);
    }
}
