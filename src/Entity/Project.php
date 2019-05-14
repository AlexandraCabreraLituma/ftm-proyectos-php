<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="projects", indexes={@ORM\Index(name="fk_user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class Project implements \JsonSerializable
{



    /**
     * Project constructor.
     * @param string $title
     * @param string $description
     * @param string $specificObjectives
     * @param \DateTime $initialDate
     * @param \DateTime $finalDate
     * @param User $user
     */
    public function __construct(string $title,
                                string $description,
                                string $specificObjectives,
                                \DateTime $initialDate,
                                \DateTime $finalDate,
                                User $user = null)
    {
        $this->id = 0;
        $this->title = $title;
        $this->description = $description;
        $this->specificObjectives = $specificObjectives;
        $this->initialDate = $initialDate;
        $this->finalDate = $finalDate;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSpecificObjectives()
    {
        return $this->specificObjectives;
    }

    /**
     * @param string $specificObjectives
     */
    public function setSpecificObjectives($specificObjectives)
    {
        $this->specificObjectives = $specificObjectives;
    }

    /**
     * @return \DateTime
     */
    public function getInitialDate()
    {
        return $this->initialDate;
    }

    /**
     * @param \DateTime $initialDate
     */
    public function setInitialDate($initialDate)
    {
        $this->initialDate = $initialDate;
    }

    /**
     * @return \DateTime
     */
    public function getFinalDate()
    {
        return $this->finalDate;
    }

    /**
     * @param \DateTime $finalDate
     */
    public function setFinalDate($finalDate)
    {
        $this->finalDate = $finalDate;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=60, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="specific_objectives", type="text", length=65535, nullable=false)
     */
    private $specificObjectives;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="initial_date", type="datetime", nullable=false)
     */
    private $initialDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="final_date", type="datetime", nullable=false)
     */
    private $finalDate;

    /**
     * Result User
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * Specify data which should be serialized to JSON
     *
     * @link   http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since  5.4.0
     */
    public function jsonSerialize():array
    {
        return array(
            'id'                  => $this->id,
            'title'               => utf8_encode($this->title),
            'description'         => utf8_encode($this->description),
            'specific_objectives' => utf8_encode($this->specificObjectives),
            'initial_date'        => $this->initialDate->format('Y-m-d H:i:s'),
            'final_date'          => $this->finalDate->format('Y-m-d H:i:s'),
            'user'                => $this->user,
        );

    }

    /**
     * Implements __toString()
     *
     * @return string
     * @link   http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString(): string
    {
        return sprintf(
            '%3d - %3d - %22s - %3d - %s - %s ' ,
            $this->id,
            $this->title,
            $this->description,
            $this->user,
            $this->initialDate->format('Y-m-d H:i:s'),
            $this->finalDate->format('Y-m-d H:i:s')

        );
    }

}
