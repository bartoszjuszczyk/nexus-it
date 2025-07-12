<?php

/**
 * File: AttachmentEvent.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Entity\Ticket\TicketEvent;

use App\Entity\Ticket\TicketAttachment;
use App\Entity\Ticket\TicketEvent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class AttachmentEvent extends TicketEvent
{
    #[ORM\ManyToOne]
    #[Assert\NotNull(message: 'Attachment cannot be empty.')]
    private ?TicketAttachment $attachment = null;

    public function getAttachment(): ?TicketAttachment
    {
        return $this->attachment;
    }

    public function setAttachment(?TicketAttachment $attachment): static
    {
        $this->attachment = $attachment;

        return $this;
    }
}
