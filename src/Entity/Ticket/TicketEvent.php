<?php

namespace App\Entity\Ticket;

use App\Config\Event\EventType;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\Ticket\TicketEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TicketEventRepository::class)]
#[ORM\Table(name: 'ticket_event')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'integer')]
#[ORM\DiscriminatorMap([
    EventType::COMMENT->value => TicketEvent\CommentEvent::class,
    EventType::INTERNAL_COMMENT->value => TicketEvent\InternalCommentEvent::class,
    EventType::SUPPORT_COMMENT->value => TicketEvent\SupportCommentEvent::class,
    EventType::STATUS_CHANGE->value => TicketEvent\StatusChangeEvent::class,
    EventType::ASSIGN->value => TicketEvent\AssignEvent::class,
    EventType::ATTACHMENT->value => TicketEvent\AttachmentEvent::class,
])]
abstract class TicketEvent
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'ticketEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?EventType
    {
        return $this->type;
    }

    public function setType(EventType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }
}
