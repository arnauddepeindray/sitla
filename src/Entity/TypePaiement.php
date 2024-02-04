<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TypePaiementRepository")
 */
class TypePaiement
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
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Don", mappedBy="type")
     */
    private $don;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Paiement", mappedBy="typePaiement")
     */
    private $typeAssociation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Timbre", mappedBy="typePaiement")
     */
    private $typeAssociationTimbre;

    public function __construct()
    {
        $this->typeAssociation = new ArrayCollection();
        $this->typeAssociationTimbre = new ArrayCollection();
        $this->paiements = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getType();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getTypeAssociation()
    {
        return $this->typeAssociation;
    }

    public function addTypeAssociation(Paiement $paiement): self
    {
        $this->typeAssociation = $paiement;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDon()
    {
        return $this->don;
    }

    public function addDon(Don $don): self
    {
        $this->don = $don;
        return $this;
    }


    /**
     * @return $mixed
     */
    public function getTypeAssociationTimbre()
    {
        return $this->typeAssociationTimbre;
    }

    public function addTypeAssociationTimbre(Timbre $timbre): self
    {
        $this ->typeAssociationTimbre = $timbre;
        return $this;
    }



}
