<?php

namespace App\Entity;

use App\Repository\UsersTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserTokenRepository::class)
 */
class UserToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="users_token_id")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $users_token_access;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsersTokenAccess(): ?string
    {
        return $this->users_token_access;
    }

    public function setUsersTokenAccess(string $users_token_access): self
    {
        $this->users_token_access = $users_token_access;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
