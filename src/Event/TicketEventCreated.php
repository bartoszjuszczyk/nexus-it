<?php

/**
 * File: TicketEventCreated.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Event;

use App\Entity\Ticket;
use Symfony\Contracts\EventDispatcher\Event;

class TicketEventCreated extends Event
{
    public function __construct(
        private Ticket\TicketEvent $ticketEvent,
    ) {
    }

    public function getTicketEvent(): Ticket\TicketEvent
    {
        return $this->ticketEvent;
    }

    public function setTicketEvent(Ticket\TicketEvent $ticketEvent): void
    {
        $this->ticketEvent = $ticketEvent;
    }
}
