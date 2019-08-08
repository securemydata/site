<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var string
     * @Assert\Email
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="passphrase", type="string", length=255, nullable=true)
     */
    private $passphrase;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @ORM\Column(name="date_passphrase", type="datetime", nullable=true)
     */
    private $datePassphrase;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_bdd", type="datetime", nullable=false)
     */
    private $dateBdd = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    public function __construct()
    {
        $this->setDateBdd(new \DateTime());
        $this->setDatePassphrase(new \DateTime());
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set passphrase
     *
     * @param string $passphrase
     *
     * @return User
     */
    public function setPassphrase($passphrase)
    {
        $this->passphrase = $passphrase;

        return $this;
    }

    /**
     * Get passphrase
     *
     * @return string
     */
    public function getPassphrase()
    {
        return $this->passphrase;
    }

    /**
     * Set datePassphrase
     *
     * @param \DateTime $datePassphrase
     *
     * @return User
     */
    public function setDatePassphrase($datePassphrase)
    {
        $this->datePassphrase = $datePassphrase;

        return $this;
    }

    /**
     * Get datePassphrase
     *
     * @return \DateTime
     */
    public function getDatePassphrase()
    {
        return $this->datePassphrase;
    }

    /**
     * Set dateBdd
     *
     * @param \DateTime $dateBdd
     *
     * @return User
     */
    public function setDateBdd($dateBdd)
    {
        $this->dateBdd = $dateBdd;

        return $this;
    }

    /**
     * Get dateBdd
     *
     * @return \DateTime
     */
    public function getDateBdd()
    {
        return $this->dateBdd;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    public static function encryptPassword($password, $salt  ){

        $c  = openssl_encrypt ( $password , "AES-128-CBC" , $salt,0,'123fdqs123456789');
        //echo $c." [".strlen($c)."]";
        //echo openssl_decrypt( $c , "AES-128-CBC" , 'test',0,'123fdqs123456789');
        return $c;
    }


    public static function decryptPassword($password, $salt  ){

        $c  = openssl_decrypt( $password , "AES-128-CBC" , $salt,0,'123fdqs123456789');
        return $c;
    }
}
