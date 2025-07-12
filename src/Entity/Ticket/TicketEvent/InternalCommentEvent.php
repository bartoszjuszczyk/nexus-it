<?php

/**
 * File: InternalCommentEvent.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Entity\Ticket\TicketEvent;

use App\Entity\Ticket\TicketEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class InternalCommentEvent extends TicketEvent
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotNull(message: 'Comment cannot be empty.')]
    private ?string $comment = null;

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
