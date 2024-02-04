<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Entreprise", mappedBy="parent", cascade={"persist"})
     */
    protected $childrenCustomers;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function getChildrenCustomers() {
        return $this->childrenCustomers;
    }

    // ...

    // always use this to setup a new parent/child relationship
    public function addChildCustomers(Entreprise $child) {
        $this->childrenCustomers = $child;
        $child->setParent($this);
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
