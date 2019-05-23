<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table(name="profiles", indexes={@ORM\Index(name="fk_perfilUser", columns={"user_id"})})
 * @ORM\Entity
 */
class Profile implements \JsonSerializable
{
    /**
     * constructor.
     *
     * @param string $name name
     * @param string $description description
     * @param string $workingDay workingDay
     * @param string $nivel nivel
     * @param string $category category
     * @param User $user user
     */
    public function __construct(
        string $name,
        string $description,string $workingDay,
        string $nivel,
        string $category,
        User $user
    ) {
        $this->id = 0;
        $this->name = $name;
        $this->description = $description;
        $this->workingDay = $workingDay;
        $this->nivel = $nivel;
        $this->category = $category;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
    public function getWorkingDay(): string
    {
        return $this->workingDay;
    }

    /**
     * @param string $workingDay
     */
    public function setWorkingDay(string $workingDay): void
    {
        $this->workingDay = $workingDay;
    }

    /**
     * @return string
     */
    public function getNivel(): string
    {
        return $this->nivel;
    }

    /**
     * @param string $nivel
     */
    public function setNivel(string $nivel): void
    {
        $this->nivel = $nivel;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
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
     * @ORM\Column(name="name", type="string", length=60, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=60, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="working_day", type="string", length=60, nullable=false)
     */
    private $workingDay;

    /**
     * @var string
     *
     * @ORM\Column(name="nivel", type="string", length=60, nullable=false)
     */
    private $nivel;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=60, nullable=false)
     */
    private $category;

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
            'name'                => utf8_encode($this->name),
            'description'         => utf8_encode($this->description),
            'nivel'               => utf8_encode($this->nivel),
            'category'            => utf8_encode($this->category),
            'user'                => $this->user,
        );

    }
}
