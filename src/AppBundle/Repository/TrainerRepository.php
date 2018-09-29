<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Trainer;
use Doctrine\ORM\EntityRepository;

class TrainerRepository extends EntityRepository
{
    public function findAllQueryBuilder($orderByArray = [], $filter = '')
    {
        $queryBuilder = $this->createQueryBuilder("trainer");

        if ($filter) {
            $filterParts = [];
            foreach (Trainer::SEARCHABLE_FIELDS as $field) {
                $filterParts[] = 'LOWER(trainer.' . $field . ') LIKE LOWER(:filter)';
            }
            $filterFull = implode(' OR ', $filterParts);
            $queryBuilder
                ->andWhere($filterFull)
                ->setParameter('filter', '%' . $filter . '%');
        }

        foreach ($orderByArray as $field => $direction) {
            if (in_array($field, Trainer::SORTABLE_FIELDS)) {
                if ($field == 'category_id') {
                    $queryBuilder->leftJoin("trainer.category", "tc");
                    $queryBuilder->addOrderBy("tc.title", $direction);
                } else {
                    $queryBuilder->addOrderBy("trainer." . $field, $direction);
                }
            }
        }

        return $queryBuilder;
    }
}