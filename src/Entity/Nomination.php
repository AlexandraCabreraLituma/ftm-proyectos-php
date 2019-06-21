<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nomination
 *
 * @ORM\Table(name="nominations", indexes={@ORM\Index(name="project_profile_id", columns={"project_profile_id"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class Nomination implements \JsonSerializable
{
    public const NOMINATION_POSTULATED = 'postulated';
    public const NOMINATION_ACCEPTED = 'accepted';
    public const NOMINATION_REJECTED = 'rejected';

    public const NOMINATION_STATES = [ self::NOMINATION_POSTULATED, self::NOMINATION_ACCEPTED,self::NOMINATION_REJECTED ];
    /**
     * constructor.
     *
     * @param string $state state
     * @param Projectprofile $projectProfile projectProfile
     * @param User $user user
     */
    public function __construct(
        Projectprofile $projectProfile,
        User $user,
        string $state
    ) {
        $this->id = 0;
        $this->projectProfile = $projectProfile;
        $this->user = $user;
        $this->state = $state;
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
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return Projectprofile
     */
    public function getProjectProfile(): Projectprofile
    {
        return $this->projectProfile;
    }

    /**
     * @param Projectprofile $projectProfile
     */
    public function setProjectProfile(Projectprofile $projectProfile): void
    {
        $this->projectProfile = $projectProfile;
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
     * @ORM\Column(name="state", type="string", length=60, nullable=false)
     */
    private $state;

    /**
     * @var Projectprofile
     *
     * @ORM\ManyToOne(targetEntity="Projectprofile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_profile_id", referencedColumnName="id")
     * })
     */
    private $projectProfile;

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
            'projectprofile'      => $this->projectProfile,
            'user'                => $this->user,
            'state'               => $this->state
        );

    }

}
