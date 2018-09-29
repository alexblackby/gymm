<?php

namespace AppBundle\Repository;

use AppBundle\Entity\TrainerCategory;
use Doctrine\ORM\EntityRepository;

class TrainerCategoryRepository extends EntityRepository
{
    public function findAllQueryBuilder($orderByArray = [])
    {
        $queryBuilder = $this->createQueryBuilder("tc");

        foreach ($orderByArray as $field => $direction) {
            if (in_array($field, TrainerCategory::SORTABLE_FIELDS)) {
                $queryBuilder->addOrderBy("tc." . $field, $direction);
            }
        }

        return $queryBuilder;
    }
}