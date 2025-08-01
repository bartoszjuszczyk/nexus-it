<?php

namespace App\Entity;

use App\Entity\Ticket\TicketAttachment;
use App\Entity\Ticket\TicketEvent;
use App\Entity\Ticket\TicketRating;
use App\Repository\UserRepository;
use Deprecated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfigurationInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 10)]
    private ?string $locale = null;

    #[ORM\Column(length: 255)]
    private ?string $timezone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'author')]
    private Collection $tickets;

    /**
     * @var Collection<int, TicketAttachment>
     */
    #[ORM\OneToMany(targetEntity: TicketAttachment::class, mappedBy: 'author')]
    private Collection $ticketAttachments;

    /**
     * @var Collection<int, TicketEvent>
     */
    #[ORM\OneToMany(targetEntity: TicketEvent::class, mappedBy: 'author')]
    private Collection $ticketEvents;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author')]
    private Collection $articles;

    /**
     * @var Collection<int, TicketRating>
     */
    #[ORM\OneToMany(targetEntity: TicketRating::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $ticketRatings;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\OneToMany(targetEntity: Equipment::class, mappedBy: 'assignedTo')]
    private Collection $equipment;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totpAuthenticationSecret;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->ticketAttachments = new ArrayCollection();
        $this->ticketEvents = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->ticketRatings = new ArrayCollection();
        $this->equipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setAuthor($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getAuthor() === $this) {
                $ticket->setAuthor(null);
            }
        }

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
            $ticketAttachment->setAuthor($this);
        }

        return $this;
    }

    public function removeTicketAttachment(TicketAttachment $ticketAttachment): static
    {
        if ($this->ticketAttachments->removeElement($ticketAttachment)) {
            // set the owning side to null (unless already changed)
            if ($ticketAttachment->getAuthor() === $this) {
                $ticketAttachment->setAuthor(null);
            }
        }

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
            $ticketEvent->setAuthor($this);
        }

        return $this;
    }

    public function removeTicketEvent(TicketEvent $ticketEvent): static
    {
        if ($this->ticketEvents->removeElement($ticketEvent)) {
            // set the owning side to null (unless already changed)
            if ($ticketEvent->getAuthor() === $this) {
                $ticketEvent->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketRating>
     */
    public function getTicketRatings(): Collection
    {
        return $this->ticketRatings;
    }

    public function addTicketRating(TicketRating $ticketRating): static
    {
        if (!$this->ticketRatings->contains($ticketRating)) {
            $this->ticketRatings->add($ticketRating);
            $ticketRating->setAuthor($this);
        }

        return $this;
    }

    public function removeTicketRating(TicketRating $ticketRating): static
    {
        if ($this->ticketRatings->removeElement($ticketRating)) {
            // set the owning side to null (unless already changed)
            if ($ticketRating->getAuthor() === $this) {
                $ticketRating->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->setAssignedTo($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getAssignedTo() === $this) {
                $equipment->setAssignedTo(null);
            }
        }

        return $this;
    }

    public function isTotpAuthenticationEnabled(): bool
    {
        return null !== $this->totpAuthenticationSecret;
    }

    public function getTotpAuthenticationUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getTotpAuthenticationConfiguration(): ?TotpConfigurationInterface
    {
        return new TotpConfiguration($this->totpAuthenticationSecret, TotpConfiguration::ALGORITHM_SHA1, 30, 6);
    }

    public function getTotpAuthenticationSecret(): ?string
    {
        return $this->totpAuthenticationSecret;
    }

    public function setTotpAuthenticationSecret(?string $totpSecret): static
    {
        $this->totpAuthenticationSecret = $totpSecret;

        return $this;
    }
}
