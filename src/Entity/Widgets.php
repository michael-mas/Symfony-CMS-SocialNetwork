<?php

namespace App\Entity;

use App\Repository\WidgetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetsRepository::class)]
class Widgets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nom;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $content;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $content2;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'widgets')]
    private $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private $widgets;

    #[ORM\OneToMany(mappedBy: 'widgets', targetEntity: Pages::class)]
    private $pages;

    public function __toString()
    {
        return $this->nom;
    }

    public function __construct()
    {
        $this->widgets = new ArrayCollection();
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
        
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent2(): ?string
    {
        return $this->content2;
    }

    public function setContent2(?string $content2): self
    {
        $this->content2 = $content2;

        return $this;
    }


    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getWidgets(): Collection
    {
        return $this->widgets;
    }

    public function addWidget(self $widget): self
    {
        if (!$this->widgets->contains($widget)) {
            $this->widgets[] = $widget;
            $widget->setParent($this);
        }

        return $this;
    }

    public function removeWidget(self $widget): self
    {
        if ($this->widgets->removeElement($widget)) {
            // set the owning side to null (unless already changed)
            if ($widget->getParent() === $this) {
                $widget->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pages>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Pages $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setCategories($this);
        }

        return $this;
    }

    public function removePage(Pages $page): self
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getCategories() === $this) {
                $page->setCategories(null);
            }
        }

        return $this;
    }
}
