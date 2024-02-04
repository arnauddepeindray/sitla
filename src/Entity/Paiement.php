<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;

use Symfony\Component\Form\AbstractType;

/**
 * @property ArrayCollection nomEntreprise
 * @property ArrayCollection type
 * @ORM\Entity(repositoryClass="App\Repository\PaiementRepository")
 */
class Paiement extends AbstractType
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
     * @ORM\ManyToOne(targetEntity="App\Entity\TypePaiement", inversedBy="typeAssociation",cascade = {"persist","merge"})
     * @ORM\JoinColumn(name="id_typePaiment", referencedColumnName="id")
     */
    private $typePaiement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="paiments" )
     * @ORM\JoinColumn(name="id_entreprise", referencedColumnName="id")
     */
    private $entreprise;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;


    public function __construct()
    {
        $this->nomEntreprise = new ArrayCollection();
        $this->type = new ArrayCollection();
    }

    public function getId()
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

    /**
     * @return  $mixed
     */
    public function getTypePaiement()
    {
        return $this->typePaiement;
    }


    public function setTypePaiement(TypePaiement $typePaiement): self
    {
       $this->typePaiement = $typePaiement;
       return $this;
    }


    /**
     * @return  $mixed
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }


    public function setEntreprise(Entreprise $entreprise)
    {
       $this->entreprise = $entreprise;
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




   
}
