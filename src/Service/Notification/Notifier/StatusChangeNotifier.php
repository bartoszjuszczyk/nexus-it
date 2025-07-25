<?php

/**
 * File: NewSupportCommentNotifier.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Notifier;

use App\Dto\Notification;
use App\Entity\Ticket\TicketEvent;

class StatusChangeNotifier implements NotifierInterface
{
    public function supports(TicketEvent $event): bool
    {
        return $event instanceof TicketEvent\StatusChangeEvent;
    }

    public function createNotification(TicketEvent $event): Notification
    {
        $ticket = $event->getTicket();
        $ticketAuthor = $ticket->getAuthor();
        $eventAuthor = $event->getAuthor();

        return (new Notification())
            ->setSubject(sprintf('Ticket #%s: Ticket status change.', $ticket->getId()))
            ->setRecipients([
                [
                    'email' => $ticketAuthor->getEmail(),
                    'name' => $ticketAuthor->getFullname(),
                ],
            ]
            )
            ->setTemplate('emails/ticket-events/status-change.html.twig')
            ->setContext([
                'ticketId' => $ticket->getId(),
                'ticketTitle' => $ticket->getTitle(),
                'eventAuthorName' => $eventAuthor->getFullname(),
                'ticketAuthorName' => $ticketAuthor->getFullname(),
                'oldStatusLabel' => $event->getOldStatus()->getLabel(),
                'newStatusLabel' => $event->getNewStatus()->getLabel(),
            ]);
    }
}
