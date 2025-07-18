<?php

/**
 * File: TicketClosedEvent.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Event;

use App\Entity\Ticket;
use Symfony\Contracts\EventDispatcher\Event;

class TicketClosedEvent extends Event
{
    public function __construct(
        private Ticket $ticket,
        private Ticket\TicketEvent $ticketEvent,
    ) {
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): void
    {
        $this->ticket = $ticket;
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
