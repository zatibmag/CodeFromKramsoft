<?php

namespace App\Entity\Api;

use App\Repository\Api\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, ArrayConvertibleInterface
{
    private const ROLE_SUPER_ADMIN  = 'ROLE_SUPER_ADMIN';
    private const ROLE_ADMIN        = 'ROLE_ADMIN';
    private const ROLE_SCRUM_MASTER = 'ROLE_SCRUM_MASTER';
    private const ROLE_TEAM_MEMBER  = 'ROLE_TEAM_MEMBER';

    public const  ROLE_SUPER_ADMIN_NAME  = 'ROLE_SUPER_ADMIN';
    public const  ROLE_ADMIN_NAME        = 'ROLE_ADMIN';
    public const  ROLE_SCRUM_MASTER_NAME = 'ROLE_SCRUM_MASTER';
    public const  ROLE_TEAM_MEMBER_NAME  = 'ROLE_TEAM_MEMBER';

    public const AVAILABLE_ROLES = [
        self::ROLE_SUPER_ADMIN  => self::ROLE_SUPER_ADMIN_NAME,
        self::ROLE_ADMIN        => self::ROLE_ADMIN_NAME,
        self::ROLE_SCRUM_MASTER => self::ROLE_SCRUM_MASTER_NAME,
        self::ROLE_TEAM_MEMBER  => self::ROLE_TEAM_MEMBER_NAME,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->getId(),
            'username' => $this->getUsername(),
            'roles'    => $this->getRoles(),
        ];
    }

    public static function fromArray(array $params): User
    {
        $user = new User();

        $user->setUsername($params['username']);
        $user->setPassword($params['password']);
        $user->setRoles($params['roles']);

        return $user;
    }
}
