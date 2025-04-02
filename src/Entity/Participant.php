<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this pseudo')]
class Participant implements UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\'\-]+$/",
        message: "Le nom ne doit contenir que des lettres, espaces, apostrophes ou tirets"
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le prénom doit faire au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s\'\-]+$/",
        message: "Le nom ne doit contenir que des lettres, espaces, apostrophes ou tirets"
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire")]
    #[Assert\Regex(
        pattern: "/^[0-9]{10}$/",
        message: "Le numéro de téléphone doit contenir 10 chiffres"
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
    #[Assert\Length(max: 255, maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères")]
    private ?string $mail = null;

    #[ORM\Column]
    private ?bool $administrateur = null;

    #[ORM\Column]
    private ?bool $actif = true;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'organisateur', orphanRemoval: true)]
    private Collection $sorties;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Site $site = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le pseudo est obligatoire")]
    #[Assert\Length(
        min: 4,
        max: 255,
        minMessage: "Le pseudo doit faire au moins {{ limit }} caractères",
        maxMessage: "Le pseudo ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9_]+$/",
        message: "Le pseudo ne peut contenir que des lettres, chiffres et underscores"
    )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire", groups: ["registration"])]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Le mot de passe doit faire au moins {{ limit }} caractères",
        maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères",
        groups: ["registration"]
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
        message: "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial",
        groups: ["registration"]
    )]
    private ?string $password = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo):self
    {
        $this->photo = $photo;

        return $this;
    }
}
