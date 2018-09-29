<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\UserTrain;
use AppBundle\Entity\UserTrainer;
use AppBundle\Exceptions\FormValidationException;
use AppBundle\Exceptions\ObjectNotFoundException;
use AppBundle\Form\UserTrainerForm;
use AppBundle\Form\UserTrainForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class UserTrainService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function loadOrCreateTrainer($trainerId, UserTrain $train): UserTrainer
    {
        $trainer = $this->loadTrainer($trainerId, false);
        if ($trainer) {
            $this->checkIsTrainerInTrain($trainer, $train);
            $this->clearTrainerSets($trainer);
        } else {
            $trainer = $this->createNewTrainer($trainerId, $train);
        }
        return $trainer;
    }

    public function loadTrainer($trainerId, $exceptionIfNotExists = true): ?UserTrainer
    {
        /** @var UserTrainer $trainer */
        $trainer = $this->entityManager->getRepository(UserTrainer::class)->find($trainerId);

        if ($exceptionIfNotExists && !$trainer) {
            throw new ObjectNotFoundException("Trainer {$trainerId} not found");
        }

        return $trainer;
    }

    public function checkIsTrainerInTrain(UserTrainer $trainer, UserTrain $train)
    {
        if ($trainer->getTrain()->getId() !== $train->getId()) {
            throw new ObjectNotFoundException("Trainer {$trainer->getId()} not found in the train {$train->getId()}");
        }
    }

    public function clearTrainerSets(UserTrainer $trainer)
    {
        $trainer->getSets()->clear();
        $this->entityManager->flush();
    }

    public function createNewTrainer($trainerId, UserTrain $train): UserTrainer
    {
        $trainer = new UserTrainer();
        $trainer->setId($trainerId);
        $trainer->setUser($train->getUser());
        $trainer->setTrain($train);
        $this->entityManager->persist($trainer);

        return $trainer;
    }

    public function loadOrCreateTrain($trainId, User $user): UserTrain
    {
        $train = $this->loadTrain($trainId, false);
        if ($train) {
            $this->checkAccessToTrain($train, $user);
        } else {
            $train = new UserTrain();
            $train->setId($trainId);
            $train->setUser($user);
            $train->setCreateTime(time());
        }
        return $train;
    }

    public function loadTrain($trainId, $exceptionIfNotExists = true): ?UserTrain
    {
        /** @var UserTrain $train */
        $train = $this->entityManager->getRepository(UserTrain::class)->find($trainId);

        if ($exceptionIfNotExists && !$train) {
            throw new ObjectNotFoundException("Train {$trainId} not found");
        }

        return $train;
    }

    public function checkAccessToTrain(UserTrain $train, User $user)
    {
        if ($train->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedException("You don't have access to the train " . $train->getId());
        }
    }

    public function getUserTrains(User $user, $limit, $fromTime)
    {
        return $this->entityManager->getRepository(UserTrain::class)->findAllWithCursor($user, $limit, $fromTime);
    }

    /**
     * @param $data
     * @param UserTrainer $trainer
     * @throws FormValidationException
     */
    public function processTrainerForm($data, UserTrainer $trainer)
    {
        $form = $this->formFactory->create(UserTrainerForm::class, $trainer);
        $form->submit($data);

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $this->entityManager->persist($trainer);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     * @param UserTrain $train
     * @throws FormValidationException
     */
    public function processTrainForm($data, UserTrain $train)
    {
        $form = $this->formFactory->create(UserTrainForm::class, $train);
        $form->submit($data);

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $this->entityManager->persist($train);
        $this->entityManager->flush();
    }

    public function deleteTrainer(UserTrainer $trainer)
    {
        $this->entityManager->remove($trainer);
        $this->entityManager->flush();

    }

    public function deleteTrain(UserTrain $train)
    {
        $this->entityManager->remove($train);
        $this->entityManager->flush();
    }
}