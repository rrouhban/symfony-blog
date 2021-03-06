<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email est déjà utilisé"
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="L'identifiant n'est pas disponible"
 * )
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, unique=true)
     * @Assert\NotBlank(
     *     message="L'identifiant est obligatoire"
     * )
     * @Assert\Length(
     *     min=3,
     *     minMessage="L'identifiant doit contenir 3 caractères minimum"
     * )
     * @Assert\Regex(
     *     pattern="/^[\pL\pM\pN_-]+$/u",
     *     match=true,
     *     message="L'identifiant contient des caractères interdits"
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var int
     *
     * @ORM\Column(name="provider_id", type="integer", nullable=true)
     */
    private $providerId;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message="Le mot de passe est obligatoire"
     * )
     * @Assert\Length(
     *     min=6,
     *     max=4096,
     *     minMessage="Le mot de passe doit contenir 6 caractères minimum"
     * )
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", unique=true)
     * @Assert\NotBlank(
     *     message="L'email est obligatoire"
     * )
     * @Assert\Email(
     *     message="Le format de l'email n'est pas valide"
     * )
     */
    private $email;

    /**
     * @var array
     * @ORM\Column(name="role", type="array")
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(name="reset_password_token", type="string", unique=true, nullable=true)
     */
    private $resetPasswordToken;

    /**
     * @var \DateTime
     * @ORM\Column(name="token_expiration_date", type="datetime", nullable=true)
     */
    private $tokenExpirationDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", cascade={"persist", "remove"})
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * Get username.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * Get password.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setRole(array $role): void
    {
        $this->role = $role;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $role = $this->role;

        if (empty($role)) {
            $role[] = 'ROLE_USER';
        }

        return array_unique($role);
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->username,
            $this->password) = unserialize($serialized);
    }

    /**
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    /**
     * @param string $resetPasswordToken
     */
    public function setResetPasswordToken(?string $resetPasswordToken = null): void
    {
        $this->resetPasswordToken = $resetPasswordToken;
    }

    /**
     * @return \DateTime
     */
    public function getTokenExpirationDate(): \DateTime
    {
        return $this->tokenExpirationDate;
    }

    public function setTokenExpirationDate(): void
    {
        $date = new \DateTime();
        $date->add(new \DateInterval('PT1H'));
        $this->tokenExpirationDate = $date;
    }

    /**
     * @return int
     */
    public function getProviderId(): int
    {
        return $this->providerId;
    }

    /**
     * @param int $providerId
     */
    public function setProviderId(int $providerId): void
    {
        $this->providerId = $providerId;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
