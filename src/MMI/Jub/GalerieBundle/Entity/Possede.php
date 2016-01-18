<?php

namespace MMI\Jub\GalerieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Possede
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MMI\Jub\GalerieBundle\Entity\PossedeRepository")
 */
class Possede
{
/*	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 *
	private $id;*/
	
    /**
     *
     *@ORM\Id
     *@ORM\ManyToOne(targetEntity="Ci")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_ci", referencedColumnName="id", onDelete="Cascade")
     * })
     */
    private $ciP;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id", onDelete="Cascade", nullable=false)
     */
    private $user;
    
    /**
     * @ORM\Column(type="boolean")
     */
	protected $masque;   
    
//	Bazar propre à la table
    
/*    /**
     * Get id
     *
     * @return integer 
     *
    public function getId()
    {
        return $this->id;
    }*/

//	Masque
    
    /**
     * Set masque
     *
     * @param boolean $masque
     * @return Possede
     */
    public function setMasque($masque){
    	$this->masque = $masque;
    	return $masque;
    }
    
    /**
     * Get masque
     *
     * @return boolean
     */
    public function getMasque(){
    	return $this->masque;
    }
//	Utilisateur
    
    /**
     * Set user
     *
     * @param \MMI\Jub\GalerieBundle\Entity\User $user
     * @return User
     */
    public function setUser(\MMI\Jub\GalerieBundle\Entity\User $user)
    
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \MMI\Jub\GalerieBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

//	Centre d'Intérêt

/*    public function __construct()
    {
    	parent::__construct();
    	$this->ciP = new ArrayCollection();
    }
*/
    /**
     * Set ciP
     *
     * @param \MMI\Jub\GalerieBundle\Entity\Ci $ciP
     * @return Possede
     */
    public function setCiP(\MMI\Jub\GalerieBundle\Entity\Ci $ciP)
    {
        $this->ciP = $ciP;
        return $this;
    }

    /**
     * Get ciP
     *
     * @return \MMI\Jub\GalerieBundle\Entity\Ci 
     */
    public function getCiP()
    {
        return $this->ciP;
    }

    public function __toString() {
    	return $this->ciP->getNomCi();
    }
}
