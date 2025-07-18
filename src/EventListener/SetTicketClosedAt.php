<?php

/**
 * File: SetTicketClosedAt.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\EventListener;

use App\Event\TicketClosedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: TicketClosedEvent::class, priority: 10)]
class SetTicketClosedAt
{
    public function __invoke(TicketClosedEvent $event): void
    {
        $ticket = $event->getTicket();

        $ticket->setClosedAt(new \DateTimeImmutable());
    }
}
