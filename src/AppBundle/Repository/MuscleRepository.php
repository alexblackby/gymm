<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Muscle;
use Doctrine\ORM\EntityRepository;

class MuscleRepository extends EntityRepository
{
    public function findAllQueryBuilder($orderByArray = [])
    {
        $queryBuilder = $this->createQueryBuilder("m");

        foreach ($orderByArray as $field => $direction) {
            if (in_array($field, Muscle::SORTABLE_FIELDS)) {
                $queryBuilder->addOrderBy("m." . $field, $direction);
            }
        }

        return $queryBuilder;
    }
}