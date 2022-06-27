<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Comment;
use DateTime;
use DateTimeInterface;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Vous avez déjà un compte")
 *
 */
class User implements UserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const GOOGLE_OAUTH = 'Google';
    public const GITHUB_OAUTH = 'Github';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var int
     * @ORM\Column(type="string", nullable=true)
     */
    private $clientId;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     *
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string")
     */

    private $oauthType;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationCode;
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $enable;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $roles = [];
    /**
     * @var Comment[]
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     */
    private $comments;

    /**
     * @param $clientId
     * @param string $email
     * @param string $name
     * @param string $oauthType
     * @param array $roles
     */
    public function __construct
    (
        $clientId = null,
        string $email = null,
        string $name = null,
        string $oauthType = 'intern',
        array $roles = null
    )
    {
        $this->clientId = $clientId;
        $this->email = $email;
        $this->name = $name;
        $this->oauthType = $oauthType;
        $this->roles = $roles;
        $this->lastLogin = new DateTime('now');
        $this->roles = [self:: ROLE_USER];
        $this->enable = true;
        $this->comments = new ArrayCollection();
    }
    public static function fromGoogleRequest
    (
        string $clientId,
        string $email,
        string $name
    ): User
    {
        return new self(
            $clientId,
            $email,
            $name,
            self::GOOGLE_OAUTH,
            [self::ROLE_USER]
        );
    }
    public static function fromGithubRequest
    (
        string $clientId,
        string $email,
        string $name
    )
    {
        return new self(
            $clientId,
            $email,
            $name,
            self::GITHUB_OAUTH,
            [self::ROLE_USER]
        );
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }
    /**
     * @return string
     */
    public function getOauthType(): string
    {
        return $this->oauthType;
    }

    /**
     * @return DateTimeInterface
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return $this
     */
    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
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
     * @return $this
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationCode(): string
    {
        return $this->confirmationCode;
    }

    /**
     * @param string $confirmationCode
     * @return User
     */
    public function setConfirmationCode(string $confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }


    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
       return $this->email;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }


    /**
     * @return int
     */
    public function setClientId()
    {
        return $this->clientId;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
	{
        $comment->setUser($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

}
