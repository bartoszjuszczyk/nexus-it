<?php

/**
 * File: NewTicketEvent.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Entity\Ticket\TicketEvent;

use App\Entity\Ticket\TicketEvent;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class NewTicketEvent extends TicketEvent
{
}
