<?php

namespace CG\PortfolioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Skill
 *
 * @ORM\Table(name="skill")
 * @ORM\Entity(repositoryClass="CG\PortfolioBundle\Repository\SkillRepository")
 */
class Skill
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="logo_src", type="string", length=255)
     */
    private $logoSrc;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     * 
     * @Assert\Range(
     *     min = 0,
     *     max = 100,
     *     minMessage = "Vous devez entrer une valeur supérieure à {{ limit }}",
     *     maxMessage = "Vous devez entrer une valeur inférieure à {{ limit }}"
     * )
     */
    private $level;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="is_software", type="boolean")
     */
    private $isSoftware;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Skill
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Skill
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set logoSrc
     *
     * @param string $logoSrc
     *
     * @return Skill
     */
    public function setLogoSrc($logoSrc)
    {
        $this->logoSrc = $logoSrc;

        return $this;
    }

    /**
     * Get logoSrc
     *
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->logoSrc;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Skill
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set isSoftware
     *
     * @param boolean $isSoftware
     *
     * @return Skill
     */
    public function setIsSoftware($isSoftware)
    {
        $this->isSoftware = $isSoftware;

        return $this;
    }

    /**
     * Get isSoftware
     *
     * @return boolean
     */
    public function getIsSoftware()
    {
        return $this->isSoftware;
    }
}
