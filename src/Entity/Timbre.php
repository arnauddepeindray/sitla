<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimbreRepository")
 */
class Timbre extends AbstractType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mois;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="payerTimbre")
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypePaiement", inversedBy="typeAssociationTimbre")
     * @ORM\JoinColumn(name="id_type", referencedColumnName="id")
     */
    private $typePaiement;

    public function __construct()
    {

        $this->nomEntreprise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): self
    {
        $this->mois = $mois;

        return $this;
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



    /**
     * @return $mixed
     */
    public function getTypePaiement()
    {
        return $this->typePaiement;
    }


    public function setTypePaiement(TypePaiement $typePaiement)
    {
       $this->typePaiement = $typePaiement;
        return $this;
    }


}
