<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class LogPasswordViewRepository extends EntityRepository
{
    public function countAll($idUser)
    {

        return   $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(p.id) as nb, MAX(p.dateInsert) as lastdate  FROM AppBundle:LogPasswordView p 
                WHERE p.idUser='.$idUser.'
                '
            )->getResult();
    }

    public function getLastmonths($idUser=0,$months=7)
    {
        $q = $strm = $strd = '';
        if($idUser>0) {
            $q  = ' AND p.idUser='.$idUser;
        }

        $d = date("Y-m-01",time()-3600*24*30*$months);
        //$req = $this->getEntityManager()->createQueryBuilder
        $req = $this->getEntityManager()->createQuery('
        SELECT COUNT(p.id) as cpt, SUBSTRING(p.dateInsert,1,7) as month
        FROM AppBundle:LogPasswordView p 
        WHERE p.dateInsert > :d  '.$q.' 
        GROUP BY month
        ORDER BY month ASC');
        $req->setParameter('d',$d);
        $res    = $req->execute();
        if(is_array($res)) {
            foreach ($res as $data) {

                $dateObj = \DateTime::createFromFormat('!m', substr($data['month'], -2));
                $monthName = $dateObj->format('F'); // March
                $strm[] = $monthName;
                $strd[] = $data['cpt'];
            }
        }else{
            $strm = $strd = '0';
        }

        $fnl = array($strm,$strd);


        return $fnl;

    }

    public function loadDatatableHistory($idUser,$orderColumn='',$orderDir='asc',$start='0',$length='10',$search='')
    {
        if($orderColumn==0) $order="s.label";
        elseif($orderColumn==1) $order="p.ip";
        elseif($orderColumn==2) $order="p.dateInsert";

        $searchs    = explode(' ',$search);
        $q          = '';
        foreach($searchs as $ids=>$s){
            $q  .='AND (p.ip like :search'.$ids.' OR s.label like :search'.$ids.' )';

        }

        $res        = $this->getEntityManager()
            ->createQuery(
                'SELECT p.ip , s.label, p.dateInsert FROM AppBundle:LogPasswordView p, AppBundle:Password s
                WHERE s.id=p.idPassword 
                AND p.idUser='.$idUser.'
                 '.$q.'
                ORDER BY '.$order.' '.$orderDir.'
                '
            )->setFirstResult($start)
            ->setMaxResults($length);
        foreach($searchs as $ids=>$s) {
            $res->setParameter('search'.$ids, '%'.$s.'%');
        }
        //echo $res->getSql();
        return $res->getResult();
    }
}