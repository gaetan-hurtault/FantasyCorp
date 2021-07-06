<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string 
     */
    private $title;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @var integer 
     */
    private $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $online;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="product",cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=CommandLine::class, mappedBy="product")
     */
    private $commandLines;

    /**
     * @ORM\OneToMany(targetEntity=Promotion::class, mappedBy="product")
     */
    private $promotions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productCondition;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $excluWeb;

    /**
     * @ORM\Column(type="float")
     */
    private $pricePurchasing;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $length;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $ageMin;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 1})
     */
    private $playerNumberMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerNumberMax;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timePlayingMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timePlayingMax;

    /**
     * @ORM\ManyToMany(targetEntity=Theme::class, inversedBy="products")
     */
    private $themes;

    /**
     * @ORM\ManyToMany(targetEntity=Languages::class, inversedBy="products")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity=Editor::class, inversedBy="products")
     */
    private $editor;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="product", cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @ORM\Column(type="text", nullable=true,length=400)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $weight;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="products")
     */
    private $authors;

    /**
     * @ORM\Column(type="text")
     */
    private $descriptionFast;

    /**
     * @ORM\OneToOne(targetEntity=Picture::class, cascade={"persist", "remove"})
     */
    private $pictureFirst;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sellerParticular;

    /**
     * @ORM\Column(type="float")
     */
    private $rating;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->commandLines = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->language = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setProduct($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getProduct() === $this) {
                $picture->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommandLine[]
     */
    public function getCommandLines(): Collection
    {
        return $this->commandLines;
    }

    public function addCommandLine(CommandLine $commandLine): self
    {
        if (!$this->commandLines->contains($commandLine)) {
            $this->commandLines[] = $commandLine;
            $commandLine->setProduct($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): self
    {
        if ($this->commandLines->contains($commandLine)) {
            $this->commandLines->removeElement($commandLine);
            // set the owning side to null (unless already changed)
            if ($commandLine->getProduct() === $this) {
                $commandLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Promotion[]
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setProduct($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->contains($promotion)) {
            $this->promotions->removeElement($promotion);
            // set the owning side to null (unless already changed)
            if ($promotion->getProduct() === $this) {
                $promotion->setProduct(null);
            }
        }

        return $this;
    }
    public function getSlug()
    {
        return(new Slugify())->slugify($this->title);
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getProductCondition(): ?string
    {
        return $this->productCondition;
    }

    public function setProductCondition(string $productCondition): self
    {
        $this->productCondition = $productCondition;

        return $this;
    }

    public function getExcluWeb(): ?bool
    {
        return $this->excluWeb;
    }

    public function setExcluWeb(bool $excluWeb): self
    {
        $this->excluWeb = $excluWeb;

        return $this;
    }

    public function getPricePurchasing(): ?float
    {
        return $this->pricePurchasing;
    }

    public function setPricePurchasing(float $pricePurchasing): self
    {
        $this->pricePurchasing = $pricePurchasing;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(?int $ageMin): self
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    public function getPlayerNumberMin(): ?int
    {
        return $this->playerNumberMin;
    }

    public function setPlayerNumberMin(?int $playerNumberMin): self
    {
        $this->playerNumberMin = $playerNumberMin;

        return $this;
    }

    public function getPlayerNumberMax(): ?int
    {
        return $this->playerNumberMax;
    }

    public function setPlayerNumberMax(?int $playerNumberMax): self
    {
        $this->playerNumberMax = $playerNumberMax;

        return $this;
    }

    public function getTimePlayingMin(): ?int
    {
        return $this->timePlayingMin;
    }

    public function setTimePlayingMin(?int $timePlayingMin): self
    {
        $this->timePlayingMin = $timePlayingMin;

        return $this;
    }

    public function getTimePlayingMax(): ?int
    {
        return $this->timePlayingMax;
    }

    public function setTimePlayingMax(?int $timePlayingMax): self
    {
        $this->timePlayingMax = $timePlayingMax;

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
    public function getLanguage(): Collection
    {
        return $this->language;
    }

    public function addLanguage(languages $language): self
    {
        if (!$this->language->contains($language)) {
            $this->language[] = $language;
        }

        return $this;
    }

    public function removeLanguage(languages $language): self
    {
        if ($this->language->contains($language)) {
            $this->language->removeElement($language);
        }

        return $this;
    }

    public function getEditor(): ?editor
    {
        return $this->editor;
    }

    public function setEditor(?editor $editor): self
    {
        $this->editor = $editor;

        return $this;
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
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
        }

        return $this;
    }

    public function getDescriptionFast(): ?string
    {
        return $this->descriptionFast;
    }

    public function setDescriptionFast(string $descriptionFast): self
    {
        $this->descriptionFast = $descriptionFast;

        return $this;
    }

    public function getPictureFirst(): ?Picture
    {
        return $this->pictureFirst;
    }

    public function setPictureFirst(?Picture $pictureFirst): self
    {
        $this->pictureFirst = $pictureFirst;

        return $this;
    }

    public function getSellerParticular(): ?bool
    {
        return $this->sellerParticular;
    }

    public function setSellerParticular(bool $sellerParticular): self
    {
        $this->sellerParticular = $sellerParticular;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
