<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $userFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $userLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $userPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $userMobile = null;

    #[ORM\Column(length: 255)]
    private ?string $userEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $userImage = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $userBio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function setUserFirstName(string $userFirstName): self
    {
        $this->userFirstName = $userFirstName;

        return $this;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function setUserLastName(string $userLastName): self
    {
        $this->userLastName = $userLastName;

        return $this;
    }

    public function getUserPassword(): ?string
    {
        return $this->userPassword;
    }

    public function setUserPassword(string $userPassword): self
    {
        $this->userPassword = $userPassword;

        return $this;
    }

    public function getUserMobile(): ?string
    {
        return $this->userMobile;
    }

    public function setUserMobile(string $userMobile): self
    {
        $this->userMobile = $userMobile;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserImage(): ?string
    {
        return $this->userImage;
    }

    public function setUserImage(string $userImage): self
    {
        $this->userImage = $userImage;

        return $this;
    }

    public function getUserBio(): ?string
    {
        return $this->userBio;
    }

    public function setUserBio(?string $userBio): self
    {
        $this->userBio = $userBio;

        return $this;
    }
}
