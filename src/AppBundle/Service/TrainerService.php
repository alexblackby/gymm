<?php

namespace AppBundle\Service;

use AppBundle\Entity\Trainer;
use AppBundle\Exceptions\ObjectNotFoundException;
use Doctrine\ORM\EntityManagerInterface;


class TrainerService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadTrainersQueryBuilder($q, $_sortField, $_sortDir)
    {
        $queryBuilder = $this
            ->entityManager
            ->getRepository(Trainer::class)
            ->findAllQueryBuilder([$_sortField => $_sortDir], $q);

        return $queryBuilder;
    }

    public function loadTrainer($trainerId)
    {
        $object = $this
            ->entityManager
            ->getRepository(Trainer::class)
            ->find($trainerId);

        if (!$object) {
            throw new ObjectNotFoundException();
        }

        return $object;
    }
}