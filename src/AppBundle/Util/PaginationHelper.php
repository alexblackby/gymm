<?php

namespace AppBundle\Util;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;


trait PaginationHelper
{
    protected function getPaginatedResult(QueryBuilder $queryBuilder, $page = 1, $perPage = 30): PaginatedCollection
    {
        if ($perPage > 100) {
            $perPage = 100;
        }
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);

        $total = $pagerfanta->getNbResults();
        $items = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $items[] = $result;
        }

        $result = new PaginatedCollection();
        $result->items = $items;
        $result->total = $total;

        return $result;
    }
}