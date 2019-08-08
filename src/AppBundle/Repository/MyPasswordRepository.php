<?php
/**
 * Created by PhpStorm.
 * User: guillaume.regairaz
 * Date: 05/08/2019
 * Time: 13:36
 */
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class MyPasswordRepository extends EntityRepository
{
    public function findAlllist($idUser,$orderColumn='',$orderDir='asc',$start='',$length='',$search='')
    {
        if($orderColumn==0) $order="label";
            else $order="description";

        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Password p 
                WHERE p.idUser='.$idUser.'
                 AND (p.label like :search OR p.description like :search )
                ORDER BY p.'.$order.' '.$orderDir.'
                '
            )->setFirstResult($start)
            ->setMaxResults($length)
            ->setParameter('search', '%'.$search.'%')
            ->getResult();
    }


}