<?php

/**
 * File: TicketEventSubscriber.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\EventListener;

use App\Entity\Ticket\TicketEvent;
use App\Entity\Ticket\TicketEvent\StatusChangeEvent;
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

        $data = [];

        if ($ticketEvent instanceof StatusChangeEvent) {
            $data = [
                'type' => 'status_change',
                'timelineHtml' => $this->twig->render('ticket/events/_status_change.html.twig', ['event' => $ticketEvent]),
                'newStatusBadgeHtml' => $this->twig->render('ticket/_status_badge.html.twig', ['status' => $ticketEvent->getNewStatus()]),
            ];
        } else {
            //            $data = [
            //                'type' => 'new_comment',
            //                'timelineHtml' => $this->twig->render('ticket/events/_comment.html.twig', ['event' => $ticketEvent]),
            //            ];
        }

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
