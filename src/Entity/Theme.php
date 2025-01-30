<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true, name:"parentId")]
    private ?int $parentId = null;


    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_section = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }


    public function setParentId(?int $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
    }


    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getIs_section(): ?bool
    {
        return $this->is_section;
    }

    public function setIs_section(?bool $is_section): static
    {
        $this->is_section = $is_section;

        return $this;
    }


}
