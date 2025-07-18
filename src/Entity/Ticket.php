<?php

namespace App\Entity;

use App\Entity\Ticket\TicketAttachment;
use App\Entity\Ticket\TicketEvent;
use App\Entity\Ticket\TicketStatus;
use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $last_replied_at = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, TicketAttachment>
     */
    #[ORM\OneToMany(targetEntity: TicketAttachment::class, mappedBy: 'ticket', cascade: ['persist'])]
    private Collection $ticketAttachments;

    #[ORM\ManyToOne]
    private ?TicketStatus $status = null;

    /**
     * @var Collection<int, TicketEvent>
     */
    #[ORM\OneToMany(targetEntity: TicketEvent::class, mappedBy: 'ticket', orphanRemoval: true)]
    private Collection $ticketEvents;

    #[ORM\ManyToOne]
    private ?User $assigned_to = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    public function __construct()
    {
        $this->ticketAttachments = new ArrayCollection();
        $this->ticketEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLastRepliedAt(): ?\DateTimeImmutable
    {
        return $this->last_replied_at;
    }

    public function setLastRepliedAt(?\DateTimeImmutable $last_replied_at): static
    {
        $this->last_replied_at = $last_replied_at;

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

    /**
     * @return Collection<int, TicketAttachment>
     */
    public function getTicketAttachments(): Collection
    {
        return $this->ticketAttachments;
    }

    public function addTicketAttachment(TicketAttachment $ticketAttachment): static
    {
        if (!$this->ticketAttachments->contains($ticketAttachment)) {
            $this->ticketAttachments->add($ticketAttachment);
            $ticketAttachment->setTicket($this);
        }

        return $this;
    }

    public function removeTicketAttachment(TicketAttachment $ticketAttachment): static
    {
        if ($this->ticketAttachments->removeElement($ticketAttachment)) {
            // set the owning side to null (unless already changed)
            if ($ticketAttachment->getTicket() === $this) {
                $ticketAttachment->setTicket(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?TicketStatus
    {
        return $this->status;
    }

    public function setStatus(?TicketStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, TicketEvent>
     */
    public function getTicketEvents(): Collection
    {
        return $this->ticketEvents;
    }

    public function addTicketEvent(TicketEvent $ticketEvent): static
    {
        if (!$this->ticketEvents->contains($ticketEvent)) {
            $this->ticketEvents->add($ticketEvent);
            $ticketEvent->setTicket($this);
        }

        return $this;
    }

    public function removeTicketEvent(TicketEvent $ticketEvent): static
    {
        if ($this->ticketEvents->removeElement($ticketEvent)) {
            // set the owning side to null (unless already changed)
            if ($ticketEvent->getTicket() === $this) {
                $ticketEvent->setTicket(null);
            }
        }

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assigned_to;
    }

    public function setAssignedTo(?User $assigned_to): static
    {
        $this->assigned_to = $assigned_to;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): Ticket
    {
        $this->closedAt = $closedAt;

        return $this;
    }
}
