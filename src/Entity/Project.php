<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


/**
 * Project
 *
 * @ORM\Table(name="projects", indexes={@ORM\Index(name="fk_user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class Project implements \JsonSerializable
{
    /**
     * constructor.
     *
     * @param string $title title
     * @param string $description description
     * @param string $keyWords keyWords
     * @param \DateTime $initialDate initialDate
     * @param \DateTime $finalDate finalDate
     * @param bool $enabled enabled
     * @param User $user user
     */
    public function __construct(
        string $title,
        string $description,string $keyWords,
        \DateTime $initialDate, \DateTime $finalDate,
        bool $enabled,
        User $user
    ) {
        $this->id = 0;
        $this->title = $title;
        $this->description = $description;
        $this->keyWords = $keyWords;
        $this->initialDate = $initialDate;
        $this->finalDate = $finalDate;
        $this->enabled = $enabled;
        $this->user = $user;
    }



    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKeyWords(): string
    {
        return $this->keyWords;
    }

    /**
     * @param string $keyWords
     */
    public function setKeyWords(string $keyWords): void
    {
        $this->keyWords = $keyWords;
    }

    /**
     * @return \DateTime
     */
    public function getInitialDate(): \DateTime
    {
        return $this->initialDate;
    }

    /**
     * @param \DateTime $initialDate
     */
    public function setInitialDate(\DateTime $initialDate): void
    {
        $this->initialDate = $initialDate;
    }

    /**
     * @return \DateTime
     */
    public function getFinalDate(): \DateTime
    {
        return $this->finalDate;
    }

    /**
     * @param \DateTime $finalDate
     */
    public function setFinalDate(\DateTime $finalDate): void
    {
        $this->finalDate = $finalDate;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
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
     * @ORM\Column(name="key_words", type="text", length=65535, nullable=false)
     */
    private $keyWords;

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
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;


    /**
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
            'title'               => $this->title,
            'description'         => $this->description,
            'key_words'           => $this->keyWords,
            'initial_date'        => $this->initialDate->format('Y-m-d H:i:s'),
            'final_date'          => $this->finalDate->format('Y-m-d H:i:s'),
            'enabled'             => $this->enabled,
            'user'                => $this->user,
        );

    }
}
