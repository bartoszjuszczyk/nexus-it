<?php

/**
 * File: AssignEvent.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Entity\Ticket\TicketEvent;

use App\Entity\Ticket\TicketEvent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class AssignEvent extends TicketEvent
{
    #[ORM\ManyToOne]
    #[Assert\NotNull(message: 'Assigned worker cannot be empty.')]
    private ?User $assignedWorker = null;

    public function getAssignedWorker(): ?User
    {
        return $this->assignedWorker;
    }

    public function setAssignedWorker(?User $assignedWorker): static
    {
        $this->assignedWorker = $assignedWorker;

        return $this;
    }
}
