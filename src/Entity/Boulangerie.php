<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\ToBeOrNoToBe;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoulangerieRepository")
 */
class Boulangerie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $nomBL;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     */
    private $adresse;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer")
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbOperateurs;

    /**
     * @ToBeOrNoToBe()
     * @Assert\LessThanOrEqual(9999 , message="Cette valeur doit être composée de 4 chiffres")
     * @Assert\GreaterThanOrEqual(1000 , message="Cette valeur doit être composée de 4 chiffres")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matricule;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBL(): ?string
    {
        return $this->nomBL;
    }

    public function setNomBL(string $nomBL): self
    {
        $this->nomBL = $nomBL;

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

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNbOperateurs(): ?int
    {
        return $this->nbOperateurs;
    }

    public function setNbOperateurs(?int $nbOperateurs): self
    {
        $this->nbOperateurs = $nbOperateurs;

        return $this;
    }

    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(?int $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

}
