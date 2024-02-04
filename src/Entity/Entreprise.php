<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\EntrepriseRepository")
 */
class Entreprise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomEntreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomAdherent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomAdherent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdhesion;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Timbre", mappedBy="entreprise")
     */
    private $payerTimbre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Paiement", mappedBy="entreprise")
     */
    private $paiments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Don", mappedBy="entreprise")
     */
    private $donation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="childrenCustomers")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    public function __toString()
    {
        return $this->nomAdherent . " " . $this->prenomAdherent;
    }



    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel): void
    {
        $this->tel = $tel;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(?string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getNomAdherent(): ?string
    {
        return $this->nomAdherent;
    }

    public function setNomAdherent(string $nomAdherent): self
    {
        $this->nomAdherent = $nomAdherent;

        return $this;
    }

    public function getPrenomAdherent(): ?string
    {
        return $this->prenomAdherent;
    }

    public function setPrenomAdherent(string $prenomAdherent): self
    {
        $this->prenomAdherent = $prenomAdherent;

        return $this;
    }

    public function getDateAdhesion(): ?\DateTimeInterface
    {
        return $this->dateAdhesion;
    }

    public function setDateAdhesion(\DateTimeInterface $dateAdhesion): self
    {
        $this->dateAdhesion = $dateAdhesion;

        return $this;
    }



    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }


    /**
     * @return $mixed
     */
    public function getTimbre()
    {
        return $this->payerTimbre;
    }

    public function addPayerTimbre(Timbre $payerTimbre): self
    {
       $this->payerTimbre = $payerTimbre;
        return $this;
    }


    /**
     * @return $mixed
     */
    public function getDonation()
    {
        return $this->donation;
    }

    public function addDonation(Don $don): self
    {
       $this->donation = $don;
        return $this;
    }



    /**
     * @return $mixed
     */
    public function getPaiments()
    {
        return $this->paiments;
    }

    public function addPaiements(Paiement $paiement): self
    {
        $this->paiments =$paiement;
        return $this;
    }


    public function getParent() {
        return $this->parent;
    }

    public function setParent(Users $parent) {
        $this->parent = $parent;
    }


}
