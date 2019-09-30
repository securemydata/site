<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogPasswordView
 *
 * @ORM\Table(name="log_password_view")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogPasswordViewRepository")
 */
class LogPasswordView
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer", nullable=true)
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_password", type="integer", nullable=true)
     */
    private $idPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_insert", type="datetime", nullable=true)
     */
    private $dateInsert = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20, nullable=true)
     */
    private $ip;

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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return LogPasswordView
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idPassword
     *
     * @param integer $idPassword
     *
     * @return LogPasswordView
     */
    public function setIdPassword($idPassword)
    {
        $this->idPassword = $idPassword;

        return $this;
    }


    /**
     * Set ip
     *
     * @param integer $ip
     *
     * @return LogPasswordView
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get idPassword
     *
     * @return integer
     */
    public function getIdPassword()
    {
        return $this->idPassword;
    }

    /**
     * Set dateInsert
     *
     * @param \DateTime $dateInsert
     *
     * @return LogPasswordView
     */
    public function setDateInsert($dateInsert)
    {
        $this->dateInsert = $dateInsert;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get dateInsert
     *
     * @return \DateTime
     */
    public function getDateInsert()
    {
        return $this->dateInsert;
    }
}
