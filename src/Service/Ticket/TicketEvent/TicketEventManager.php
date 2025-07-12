<?php

/**
 * File: TicketEventManager.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Ticket\TicketEvent;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TicketEventManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createCommentEvent(Ticket $ticket, User $author, string $comment): Ticket\TicketEvent\CommentEvent
    {
        $commentEvent = new Ticket\TicketEvent\CommentEvent();
        $commentEvent->setComment($comment);

        return $this->prepareAndPersistEvent($commentEvent, $ticket, $author);
    }

    public function createInternalCommentEvent(
        Ticket $ticket,
        User $author,
        string $comment,
    ): Ticket\TicketEvent\InternalCommentEvent {
        $commentEvent = new Ticket\TicketEvent\InternalCommentEvent();
        $commentEvent->setComment($comment);

        return $this->prepareAndPersistEvent($commentEvent, $ticket, $author);
    }

    public function createSupportCommentEvent(
        Ticket $ticket,
        User $author,
        string $comment,
    ): Ticket\TicketEvent\SupportCommentEvent {
        $commentEvent = new Ticket\TicketEvent\SupportCommentEvent();
        $commentEvent->setComment($comment);

        return $this->prepareAndPersistEvent($commentEvent, $ticket, $author);
    }

    public function createAssignEvent(Ticket $ticket, User $author, User $assignedWorker): Ticket\TicketEvent\AssignEvent
    {
        $assignEvent = new Ticket\TicketEvent\AssignEvent();
        $assignEvent->setAssignedWorker($assignedWorker);

        return $this->prepareAndPersistEvent($assignEvent, $ticket, $author);
    }

    public function createStatusChangeEvent(
        Ticket $ticket,
        User $author, Ticket\TicketStatus $oldStatus,
        Ticket\TicketStatus $newStatus,
    ): Ticket\TicketEvent\StatusChangeEvent {
        $statusChangeEvent = new Ticket\TicketEvent\StatusChangeEvent();
        $statusChangeEvent->setOldStatus($oldStatus);
        $statusChangeEvent->setNewStatus($newStatus);

        return $this->prepareAndPersistEvent($statusChangeEvent, $ticket, $author);
    }

    public function createAttachmentEvent(
        Ticket $ticket,
        User $author,
        Ticket\TicketAttachment $attachment,
    ): Ticket\TicketEvent\AttachmentEvent {
        $attachmentEvent = new Ticket\TicketEvent\AttachmentEvent();
        $attachmentEvent->setAttachment($attachment);

        return $this->prepareAndPersistEvent($attachmentEvent, $ticket, $author);
    }

    private function prepareAndPersistEvent(
        Ticket\TicketEvent $ticketEvent,
        Ticket $ticket,
        User $author,
    ): Ticket\TicketEvent {
        $ticketEvent->setAuthor($author);
        $ticketEvent->setTicket($ticket);

        $this->entityManager->persist($ticketEvent);

        return $ticketEvent;
    }
}
