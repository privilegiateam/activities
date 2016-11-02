<?php

namespace Acme\UserBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SettingRepository extends \Doctrine\ORM\EntityRepository
{
    public function mySetting($user){
        
       return $this->createQueryBuilder('w')
                        ->innerJoin('w.user', 'u')
                        ->where('u =  :user')
                        ->setParameter('user', $user)
                        ->getQuery()
                        ->getOneOrNullResult();
    }
}