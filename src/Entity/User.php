<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="orcid", columns={"orcid"}), @ORM\UniqueConstraint(name="email", columns={"email"}), @ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity
 * @codeCoverageIgnore
 */
class User implements \JsonSerializable
{
    /**
     * User constructor.
     * @param string $username username
     * @param string $password password
     * @param string $email email
     * @param string $orcid orcid
     * @param string $firstname firstname
     * @param string $lastname lastname
     * @param int $phone phone
     * @param string $address address
     */
    public function __construct(
        string $username,
        string $password,
        string $email,
        string $orcid,
        string $firstname,
        string $lastname,
        int $phone,
        string $address = ''
    )
    {
        $this->id = 0;
        $this->username = $username;
        $this->setPassword($password);
        $this->email = $email;
        $this->orcid = $orcid;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->phone = $phone;
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getId() :int
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * Verifies that the given hash matches the user password.
     *
     * @param string $password password
     *
     * @return boolean
     */
    public function validatePassword($password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @return string
     */
    public function getEmail():string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getOrcid():string
    {
        return $this->orcid;
    }

    /**
     * @param string $orcid
     */
    public function setOrcid($orcid)
    {
        $this->orcid = $orcid;
    }

    /**
     * @return string
     */
    public function getFirstname():string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname():string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return int
     */
    public function getPhone():int
    {
        return $this->phone;
    }

    /**
     * @param int $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getAddress():string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @ORM\Column(name="username", type="string", length=40, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="orcid", type="string", length=60, nullable=false)
     */
    private $orcid;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=60, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=60, nullable=false)
     */
    private $lastname;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="integer", nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=60, nullable=false)
     */
    private $address;

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
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'orcid'         => $this->orcid,
            'firstname'     => $this->firstname,
            'lastname'      => $this->lastname,
            'phone'         => $this->phone,
            'address'       => $this->address
        );

    }
    /**
     * Representation of User as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->username;
    }

}
