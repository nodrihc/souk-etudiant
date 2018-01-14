<?php


namespace OC\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use OC\PlatformBundle\Entity\Annonce;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @var annonces
     *
     * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Annonce", mappedBy="user", cascade={"persist"})
     *
     */
    private $annonces;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add annonce
     *
     * @param \OC\PlatformBundle\Entity\Annonce $annonce
     *
     * @return User
     */
    public function addAnnonce(\OC\PlatformBundle\Entity\Annonce $annonce)
    {
        $this->annonces[] = $annonce;

        return $this;
    }

    /**
     * Remove annonce
     *
     * @param \OC\PlatformBundle\Entity\Annonce $annonce
     */
    public function removeAnnonce(\OC\PlatformBundle\Entity\Annonce $annonce)
    {
        $this->annonces->removeElement($annonce);
    }

    /**
     * Get annonces
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnnonces()
    {
        return $this->annonces;
    }
}
