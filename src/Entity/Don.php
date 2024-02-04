<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DonRepository")
 */
class Don
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypePaiement", inversedBy="don")
     * @ORM\JoinColumn(name="id_type", referencedColumnName="id")
     */
    private $type;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="donation")
     * @ORM\JoinColumn(name="id_entreprise", referencedColumnName="id")
     */
    private $entreprise;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?TypePaiement
    {
        return $this->type;
    }

    public function setType(TypePaiement $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return $mixed
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }


    public function setEntreprise(Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;
        return $this;
    }

}
