<?php

namespace App\Entity;

use App\Repository\DonateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: DonateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Donateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 10)]
    private ?string $groupeSanguin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $derniereDateDon = null;

    // ⚠️ AJOUTER CES RELATIONS ⚠️
    #[ORM\OneToMany(mappedBy: 'donateur', targetEntity: Don::class)]
    private Collection $dons;

    #[ORM\OneToMany(mappedBy: 'donateur', targetEntity: RendezVous::class)]
    private Collection $rendezVouses;

    public function __construct()
    {
        // ⚠️ IMPORTANT : Initialiser les collections
        $this->dons = new ArrayCollection();
        $this->rendezVouses = new ArrayCollection();
        $this->roles = ['ROLE_DONATEUR']; // Role par défaut
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
     * ⚠️ MÉTHODE OBLIGATOIRE pour UserInterface
     * Un identifiant visuel qui représente cet utilisateur.
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * ⚠️ MÉTHODE OBLIGATOIRE pour UserInterface
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantir que chaque utilisateur a au moins ROLE_DONATEUR
        $roles[] = 'ROLE_DONATEUR';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * ⚠️ MÉTHODE OBLIGATOIRE pour UserInterface
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * ⚠️ MÉTHODE OBLIGATOIRE pour UserInterface
     * Effacer les données sensibles temporaires
     */
    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires sensibles, effacez-les ici
        // $this->plainPassword = null;
    }

    // ⚠️ AJOUTER CETTE MÉTHODE pour UserInterface (optionnelle mais recommandée)
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
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

    public function getGroupeSanguin(): ?string
    {
        return $this->groupeSanguin;
    }

    public function setGroupeSanguin(string $groupeSanguin): static
    {
        $this->groupeSanguin = $groupeSanguin;

        return $this;
    }

    // ⚠️ MODIFIER pour permettre null
    public function getDerniereDateDon(): ?\DateTime
    {
        return $this->derniereDateDon;
    }

    public function setDerniereDateDon(?\DateTime $derniereDateDon): static
    {
        $this->derniereDateDon = $derniereDateDon;

        return $this;
    }

    // ⚠️ AJOUTER LES MÉTHODES POUR LES RELATIONS

    /**
     * @return Collection<int, Don>
     */
    public function getDons(): Collection
    {
        return $this->dons;
    }

    public function addDon(Don $don): static
    {
        if (!$this->dons->contains($don)) {
            $this->dons->add($don);
            $don->setDonateur($this);
        }

        return $this;
    }

    public function removeDon(Don $don): static
    {
        if ($this->dons->removeElement($don)) {
            // set the owning side to null (unless already changed)
            if ($don->getDonateur() === $this) {
                $don->setDonateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): static
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setDonateur($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): static
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getDonateur() === $this) {
                $rendezVouse->setDonateur(null);
            }
        }

        return $this;
    }

    // ⚠️ MÉTHODE UTILE POUR AFFICHAGE
    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}