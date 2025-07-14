<?php

/**
 * File: NewSupportCommentNotifier.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Notifier;

use App\Dto\Notification;
use App\Entity\Ticket\TicketEvent;

class AssignNotifier implements NotifierInterface
{
    public function supports(TicketEvent $event): bool
    {
        return $event instanceof TicketEvent\AssignEvent;
    }

    public function createNotification(TicketEvent $event): Notification
    {
        $ticket = $event->getTicket();
        $eventAuthor = $event->getAuthor();

        return (new Notification())
            ->setSubject(sprintf('Ticket #%s: New support comment in ticket.', $ticket->getId()))
            ->setRecipients([
                [
                    'email' => $ticket->getAssignedTo()->getEmail(),
                    'name' => $ticket->getAssignedTo()->getFullname(),
                ],
            ]
            )
            ->setTemplate('emails/ticket-events/assign.html.twig')
            ->setContext([
                'ticketId' => $ticket->getId(),
                'ticketTitle' => $ticket->getTitle(),
                'eventAuthorName' => $eventAuthor->getFullname(),
                'assigneeName' => $ticket->getAssignedTo()->getFullname(),
            ]);
    }
}
