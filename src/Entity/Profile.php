<?php

namespace App\Entity;

use App\Validator\ProfileExist;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfileRepository")
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ProfileExist()
     * @Assert\NotBlank
     * @Assert\LessThanOrEqual(9999 , message="Cette valeur doit être composée de 4 chiffres")
     * @Assert\GreaterThanOrEqual(1000 , message="Cette valeur doit être composée de 4 chiffres")
     * @ORM\Column(type="integer")
     *
     */
    private $matricule;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $affectation;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=50)
     */
    private $login;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=20)
     */
    private $password;

    /**
     * @var string
     * @Assert\EqualTo(propertyPath="password" , message="Vous n'avez pas tapé le même mot de passe! ")
     */
    public $confirm_password;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAffectation(): ?string
    {
        return $this->affectation;
    }

    public function setAffectation(string $affectation): self
    {
        $this->affectation = $affectation;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
