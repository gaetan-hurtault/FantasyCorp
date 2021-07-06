<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class SearchProduct 
{
    /**
     *
     * @var string
     */
    private $search;
    /**
     *
     * @var string
     */
    private $tri;
    /**
     *
     * @var int
     */
    private $ageMin;
    /**
     *
     * @var Editor
     */
    private $editor;
    private $categories;
    /**
     *
     * @var int
     */
    private $prixMin;
    /**
     *
     * @var int
     */
    private $prixMax;
    /**
     *
     * @var string
     */
    private $condition;
    /**
     *
     * @var bool
     */
    private $online;
    
    private $languages;
  
    private $themes;
    /**
     *
     * @var int
     */
    private $nbrPlayerMin;
    /**
     *
     * @var int
     */
    private $nbrPlayerMax;
    /**
     *
     * @var int
     */
    private $timeMin;
    /**
     *
     * @var int
     */
    private $timeMax;
    /**
     *
     * @var string
     */
    private $tag;

    public function __construct()
    {
    $this->themes = new \Doctrine\Common\Collections\ArrayCollection();
    $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
    $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }
    public function getTri(): ?string
    {
        return $this->tri;
    }

    public function setTri(string $tri): self
    {
        $this->tri = $tri;

        return $this;
    }
    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(int $ageMin): self
    {
        $this->ageMin = $ageMin;

        return $this;
    }
    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }
    public function getPrixMin(): ?int
    {
        return $this->prixMin;
    }

    public function setPrixMin(int $prixMin): self
    {
        $this->prixMin = $prixMin;

        return $this;
    }
    public function getPrixMax(): ?int
    {
        return $this->prixMax;
    }

    public function setPrixMax(int $prixMax): self
    {
        $this->prixMax = $prixMax;

        return $this;
    }
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(string $condition): self
    {
        $this->condition = $condition;

        return $this;
    }
/**
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addThemes(Theme $themes): self
    {
        if (!$this->themes->contains($themes)) {
            $this->themes[] = $themes;
        }

        return $this;
    }

    public function removeThemes(Theme $themes): self
    {
        if ($this->themes->contains($themes)) {
            $this->themes->removeElement($themes);
        }

        return $this;
    }

    /**
     * @return Collection|languages[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguages(languages $languages): self
    {
        if (!$this->languages->contains($languages)) {
            $this->languages[] = $languages;
        }

        return $this;
    }

    public function removeLanguages(languages $languages): self
    {
        if ($this->languages->contains($languages)) {
            $this->languages->removeElement($languages);
        }

        return $this;
    }
    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategories(Category $categories): self
    {
        if (!$this->categories->contains($categories)) {
            $this->categories[] = $categories;
        }

        return $this;
    }

    public function removeCategories(Category $categories): self
    {
        if ($this->categories->contains($categories)) {
            $this->categories->removeElement($categories);
        }

        return $this;
    }
    public function getNbrPlayerMin(): ?int
    {
        return $this->nbrPlayerMin;
    }

    public function setNbrPlayerMin(int $nbrPlayerMin): self
    {
        $this->nbrPlayerMin = $nbrPlayerMin;

        return $this;
    }
    public function getNbrPlayerMax(): ?int
    {
        return $this->nbrPlayerMax;
    }

    public function setNbrPlayerMax(int $nbrPlayerMax): self
    {
        $this->nbrPlayerMax = $nbrPlayerMax;

        return $this;
    }
    public function getTimeMin(): ?int
    {
        return $this->timeMin;
    }

    public function setTimeMin(int $timeMin): self
    {
        $this->timeMin = $timeMin;

        return $this;
    }
    public function getTimeMax(): ?int
    {
        return $this->timeMax;
    }

    public function setTimeMax(int $timeMax): self
    {
        $this->timeMax = $timeMax;

        return $this;
    }
    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}