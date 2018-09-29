<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserTrainRepository extends EntityRepository
{
    public function findAllWithCursor(User $user, int $limit = 1, int $fromTime = 0)
    {
        $queryBuilder = $this->createQueryBuilder("usertrain");

        $queryBuilder
            ->andWhere("usertrain.user = :user")
            ->setParameter('user', $user);

        if ($fromTime) {
            $queryBuilder
                ->andWhere("usertrain.createTime < :fromTime")
                ->setParameter('fromTime', $fromTime);
        }

        $queryBuilder->addOrderBy("usertrain.createTime", "DESC");
        $queryBuilder->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

}