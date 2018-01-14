<?php

namespace OC\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etudiant
 *
 * @ORM\Table(name="etudiant")
 * @ORM\Entity(repositoryClass="OC\UserBundle\Repository\EtudiantRepository")
 */
class Etudiant extends User
{

    /**
     * @var string
     *
     * @ORM\Column(name="universite", type="string", length=255)
     */
    private $universite;

    /**
     * @var string
     *
     * @ORM\Column(name="addresse", type="string", length=255)
     */
    private $addresse;



    /**
     * Set universite
     *
     * @param string $universite
     *
     * @return Etudiant
     */
    public function setUniversite($universite)
    {
        $this->universite = $universite;

        return $this;
    }

    /**
     * Get universite
     *
     * @return string
     */
    public function getUniversite()
    {
        return $this->universite;
    }

    /**
     * Set addresse
     *
     * @param string $addresse
     *
     * @return Etudiant
     */
    public function setAddresse($addresse)
    {
        $this->addresse = $addresse;

        return $this;
    }

    /**
     * Get addresse
     *
     * @return string
     */
    public function getAddresse()
    {
        return $this->addresse;
    }
}

