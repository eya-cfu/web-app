<?php

namespace App\Entity;

use App\Validator\TrueLivreur;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivreurRepository")
 */
class Livreur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @TrueLivreur()
     * @Assert\LessThanOrEqual(9999 , message="Cette valeur doit être composée de 4 chiffres")
     * @Assert\GreaterThanOrEqual(1000 , message="Cette valeur doit être composée de 4 chiffres")
     * @ORM\Column(type="integer")
     */
    private $matricule;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $teleLivreur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numVehicule;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(int $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getTeleLivreur(): ?int
    {
        return $this->teleLivreur;
    }

    public function setTeleLivreur(?int $teleLivreur): self
    {
        $this->teleLivreur = $teleLivreur;

        return $this;
    }

    public function getNumVehicule(): ?string
    {
        return $this->numVehicule;
    }

    public function setNumVehicule(string $numVehicule): self
    {
        $this->numVehicule = $numVehicule;

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
