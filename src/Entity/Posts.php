<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $userEmail = null;

    #[ORM\Column(length: 500)]
    private ?string $postComment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postFile = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPostComment(): ?string
    {
        return $this->postComment;
    }

    public function setPostComment(string $postComment): self
    {
        $this->postComment = $postComment;

        return $this;
    }

    public function getPostFile(): ?string
    {
        return $this->postFile;
    }

    public function setPostFile(?string $postFile): self
    {
        $this->postFile = $postFile;

        return $this;
    }
}
