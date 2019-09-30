<?php
namespace AppBundle\Service;

use AppBundle\Entity\LogPasswordView;
use AppBundle\Service\LogPassword;

class LogPassword
{
    protected $em;



    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function log($idUser,$IdPassword,$ip)
    {
        $l   = new LogPasswordView();
        $l->setIdUser($idUser);
        $l->setIdPassword($IdPassword);
        $l->setDateInsert(new \DateTime());
        $l->setIp($ip);
        //$entityManager = $this->getDoctrine()->getManager();
        $this->em->persist($l);
        $this->em->flush();
        //var_export($l);
        //echo "Yes ! $idUser $IdPassword ";
        return true;
    }

}