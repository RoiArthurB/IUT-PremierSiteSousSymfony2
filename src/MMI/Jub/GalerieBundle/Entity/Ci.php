<?php
namespace MMI\Jub\GalerieBundle\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ci
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $nomCi;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
    	$this->id = $id;
    	return $this;
    }

    /**
     * Set nomCi
     *
     * @param string $nomCi
     * @return Ci
     */
    public function setNomCi($nomCi)
    {
        $this->nomCi = $nomCi;

        return $this;
    }

    /**
     * Get nomCi
     *
     * @return string 
     */
    public function getNomCi()
    {
        return $this->nomCi;
    }
    
  
}
