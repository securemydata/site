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

        $searchs    = explode(' ',$search);
        $q          = '';
        foreach($searchs as $ids=>$s){
            $q  .='AND (p.label like :search'.$ids.' OR p.description like :search'.$ids.' OR p.login like :search'.$ids.' OR p.url like :search'.$ids.')';

        }

        $res        = $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Password p 
                WHERE p.idUser='.$idUser.'
                 '.$q.'
                ORDER BY p.'.$order.' '.$orderDir.'
                '
            )->setFirstResult($start)
            ->setMaxResults($length);
        foreach($searchs as $ids=>$s) {
            $res->setParameter('search'.$ids, '%'.$s.'%');
        }
        return $res->getResult();
    }

    public function countAll($idUser)
    {

        return   $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(p.id) as nb, MAX(p.dateInsert) as lastdate  FROM AppBundle:Password p 
                WHERE p.idUser='.$idUser.'
                '
            )->getResult();
    }


}