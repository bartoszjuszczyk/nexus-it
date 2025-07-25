<?php

/**
 * File: TicketEventSubscriber.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\EventListener;

use App\Entity\Ticket\TicketEvent;
use App\Event\TicketEventCreated;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Twig\Environment;

#[AsEventListener(event: TicketEventCreated::class)]
class TicketEventSubscriber
{
    public function __construct(
        private readonly HubInterface $hub,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(TicketEventCreated $event): void
    {
        $ticketEvent = $event->getTicketEvent();
        $ticket = $ticketEvent->getTicket();
        $topic = sprintf('/tickets/%d', $ticket->getId());

        $data = match (true) {
            $ticketEvent instanceof TicketEvent\StatusChangeEvent => [
                'type' => 'status_change',
                'timelineHtml' => $this->twig->render('ticket/events/_status_change.html.twig', ['event' => $ticketEvent]),
                'newStatusBadgeHtml' => $this->twig->render('ticket/_status_badge.html.twig', ['status' => $ticketEvent->getNewStatus()]),
            ],
            $ticketEvent instanceof TicketEvent\CommentEvent => [
                'type' => 'new_comment',
                'timelineHtml' => $this->twig->render('ticket/events/_comment.html.twig', ['event' => $ticketEvent]),
                'authorId' => $ticketEvent->getAuthor()->getId(),
            ],
            $ticketEvent instanceof TicketEvent\SupportCommentEvent => [
                'type' => 'new_support_comment',
                'timelineHtml' => $this->twig->render('ticket/events/_support_comment.html.twig', ['event' => $ticketEvent]),
                'authorId' => $ticketEvent->getAuthor()->getId(),
            ],
            $ticketEvent instanceof TicketEvent\InternalCommentEvent => [
                'type' => 'new_internal_comment',
                'timelineHtml' => $this->twig->render('ticket/events/_internal_comment.html.twig', ['event' => $ticketEvent]),
                'authorId' => $ticketEvent->getAuthor()->getId(),
            ],
            $ticketEvent instanceof TicketEvent\AttachmentEvent => [
                'type' => 'new_attachment',
                'timelineHtml' => $this->twig->render('ticket/events/_attachment.html.twig', ['event' => $ticketEvent]),
                'authorId' => $ticketEvent->getAuthor()->getId(),
                'attachmentListHtml' => $this->twig->render('ticket/events/_attachment_list.html.twig', ['attachment' => $ticketEvent->getAttachment()]),
            ],
            $ticketEvent instanceof TicketEvent\AssignEvent => [
                'type' => 'assign',
                'timelineHtml' => $this->twig->render('ticket/events/_assign.html.twig', ['event' => $ticketEvent]),
                'authorId' => $ticketEvent->getAuthor()->getId(),
            ],
            default => [],
        };

        if (!empty($data)) {
            $update = new Update($topic, json_encode($data));
            $this->hub->publish($update);
        }
    }

    private function renderEventFragment(TicketEvent $event): string
    {
        $template = match (true) {
            $event instanceof StatusChangeEvent => 'ticket/events/_status_change.html.twig',
            default => 'ticket/events/_comment.html.twig',
        };

        return $this->twig->render($template, ['event' => $event]);
    }

    private function getEventTypeSlug(TicketEvent $event): string
    {
        return match (true) {
            $event instanceof StatusChangeEvent => 'status_change',
            default => 'new_comment',
        };
    }
}
