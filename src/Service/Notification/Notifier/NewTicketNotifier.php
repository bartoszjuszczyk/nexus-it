<?php

/**
 * File: NewSupportCommentNotifier.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Service\Notification\Notifier;

use App\Dto\Notification;
use App\Entity\Ticket\TicketEvent;
use App\Repository\UserRepository;

class NewTicketNotifier implements NotifierInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function supports(TicketEvent $event): bool
    {
        return $event instanceof TicketEvent\NewTicketEvent;
    }

    public function createNotification(TicketEvent $event): Notification
    {
        $ticket = $event->getTicket();
        $ticketAuthor = $ticket->getAuthor();

        $adminRecipients = $this->getAdminRecipients();

        return (new Notification())
            ->setSubject(sprintf('New ticket: %s (#%s)', $ticket->getTitle(), $ticket->getId()))
            ->setRecipients($adminRecipients)
            ->setTemplate('emails/ticket-events/new-comment.html.twig')
            ->setContext([
                'ticketId' => $ticket->getId(),
                'ticketTitle' => $ticket->getTitle(),
                'eventAuthorName' => $ticketAuthor->getFullname(),
            ]);
    }

    private function getAdminRecipients(): array
    {
        $result = [];
        $administrators = $this->userRepository->findAdministrators();

        foreach ($administrators as $administrator) {
            $result[] = [
                'email' => $administrator->getEmail(),
                'name' => $administrator->getFullname(),
            ];
        }

        return $result;
    }
}
