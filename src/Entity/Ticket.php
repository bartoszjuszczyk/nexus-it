<?php

namespace App\Entity;

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
    #[ORM\OneToMany(targetEntity: TicketAttachment::class, mappedBy: 'ticket')]
    private Collection $ticketAttachments;

    public function __construct()
    {
        $this->ticketAttachments = new ArrayCollection();
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
}
