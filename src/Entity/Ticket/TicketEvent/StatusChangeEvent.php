<?php

/**
 * File: StatusChangeEvent.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Entity\Ticket\TicketEvent;

use App\Entity\Ticket\TicketEvent;
use App\Entity\Ticket\TicketStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class StatusChangeEvent extends TicketEvent
{
    #[ORM\ManyToOne]
    #[Assert\NotNull(message: 'Old status cannot be empty.')]
    private ?TicketStatus $oldStatus = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull(message: 'New status cannot be empty.')]
    private ?TicketStatus $newStatus = null;

    public function getOldStatus(): ?TicketStatus
    {
        return $this->oldStatus;
    }

    public function setOldStatus(?TicketStatus $oldStatus): static
    {
        $this->oldStatus = $oldStatus;

        return $this;
    }

    public function getNewStatus(): ?TicketStatus
    {
        return $this->newStatus;
    }

    public function setNewStatus(?TicketStatus $newStatus): static
    {
        $this->newStatus = $newStatus;

        return $this;
    }
}
