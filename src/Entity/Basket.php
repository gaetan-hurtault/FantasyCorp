<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BasketRepository::class)
 */
class Basket
{
    const STATE = [
        0 => 'Créé',
        1 => 'Enregistré',
        2 => 'Validé',
        3 => 'Payé',
        4 => 'Envoyé'
    ];

    const METHODSHIPP = [
        0 => "mondialrelay",
        1 => "colissimo",
        2 => "magasin"
    ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeStamp;

    /**
     * @ORM\Column(type="integer", options={"default" : 0},nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=CommandLine::class, mappedBy="basket", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $commandLines;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="baskets")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Bill::class, mappedBy="basket")
     */
    private $bills;

    /**
     * @ORM\ManyToOne(targetEntity=Adress::class, cascade={"persist"})
     */
    private $adress;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateSending;

    /**
     * @ORM\Column(type="integer", length=255, nullable=false, options={"default": 0})
     */
    private $methodShipp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idRelay;

    /**
     * @ORM\Column(type="float")
     */
    private $shippingCost;

    /**
     * @ORM\OneToOne(targetEntity=Payment::class, mappedBy="basket", cascade={"persist", "remove"})
     */
    private $payment;

    public function __construct()
    {
        $this->commandLines = new ArrayCollection();
        $this->bills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getdateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setdateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getTimeStamp(): ?\DateTimeInterface
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(?\DateTimeInterface $timeStamp): self
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $commandLine->setBasket($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): self
    {
        if ($this->commandLines->contains($commandLine)) {
            $this->commandLines->removeElement($commandLine);
            // set the owning side to null (unless already changed)
            if ($commandLine->getBasket() === $this) {
                $commandLine->setBasket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bill[]
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bill $bill): self
    {
        if (!$this->bills->contains($bill)) {
            $this->bills[] = $bill;
            $bill->setBasket($this);
        }

        return $this;
    }

    public function removeBill(Bill $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getBasket() === $this) {
                $bill->setBasket(null);
            }
        }

        return $this;
    }

    public function getAdress(): ?Adress
    {
        return $this->adress;
    }

    public function setAdress(?Adress $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getDateSending(): ?\DateTimeInterface
    {
        return $this->dateSending;
    }

    public function setDateSending(?\DateTimeInterface $dateSending): self
    {
        $this->dateSending = $dateSending;

        return $this;
    }

    public function getMethodShipp(): ?string
    {
        return $this->methodShipp;
    }

    public function setMethodShipp(?string $methodShipp): self
    {
        $this->methodShipp = $methodShipp;

        return $this;
    }

    public function getIdRelay(): ?string
    {
        return $this->idRelay;
    }

    public function setIdRelay(?string $idRelay): self
    {
        $this->idRelay = $idRelay;

        return $this;
    }

    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }

    public function setShippingCost(float $shippingCost): self
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        // set (or unset) the owning side of the relation if necessary
        $newBasket = null === $payment ? null : $this;
        if ($payment->getBasket() !== $newBasket) {
            $payment->setBasket($newBasket);
        }

        return $this;
    }
}
