<?php

namespace App\Entity;

use App\Repository\FriendshipsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendshipsRepository::class)]
class Friendships
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $user1;

    #[ORM\Column(type: 'integer')]
    private $user2;

    #[ORM\Column(type: 'integer')]
    private $status;

    #[ORM\Column(type: 'datetime')]
    private $u1last;

    #[ORM\Column(type: 'datetime')]
    private $u2last;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1(): ?int
    {
        return $this->user1;
    }

    public function setUser1(int $user1): self
    {
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?int
    {
        return $this->user2;
    }

    public function setUser2(int $user2): self
    {
        $this->user2 = $user2;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getU1last(): ?\DateTimeInterface
    {
        return $this->u1last;
    }

    public function setU1last(\DateTimeInterface $u1last): self
    {
        $this->u1last = $u1last;

        return $this;
    }

    public function getU2last(): ?\DateTimeInterface
    {
        return $this->u2last;
    }

    public function setU2last(\DateTimeInterface $u2last): self
    {
        $this->u2last = $u2last;

        return $this;
    }
}
