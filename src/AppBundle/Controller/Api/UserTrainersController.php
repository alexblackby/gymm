<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\UserTrainer;
use AppBundle\Exceptions\FormValidationException;
use AppBundle\Service\UserTrainService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\NamePrefix("api_")
 */
class UserTrainersController extends FOSRestController
{
    /**
     * @var UserTrainService
     */
    private $userTrainService;

    public function __construct(UserTrainService $userTrainService)
    {
        $this->userTrainService = $userTrainService;
    }

    /**
     * Сохранить в тренировке (train) информацию об упражнении (trainer)
     *
     * @REST\Put("/api/trains/{trainId}/trainers/{trainerId}")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Trainer updated",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=UserTrainer::class))
     * )
     * @SWG\Parameter(
     *     name="trainer",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(ref=@ApiDoc\Model(type=UserTrainer::class))
     * )
     * @SWG\Parameter(
     *     name="trainId",
     *     in="path",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     description="Идентификатор тренировки"
     * )
     * @SWG\Parameter(
     *     name="trainerId",
     *     in="path",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     description="Идентификатор упражнения в тренировке"
     * )
     * @SWG\Tag(name="Упражнения в тренировке (UserTrainer)")
     * @ApiDoc\Security(name="Bearer")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function putTrainersAction($trainId, $trainerId, Request $request)
    {
        $user = $this->getUser();

        $train = $this->userTrainService->loadTrain($trainId);
        $this->userTrainService->checkAccessToTrain($train, $user);

        $trainer = $this->userTrainService->loadOrCreateTrainer($trainerId, $train);

        try {
            $data = json_decode($request->getContent(), true);
            $this->userTrainService->processTrainerForm($data, $trainer);
        } catch (FormValidationException $ex) {
            return $ex->getForm();
        }

        return $trainer;
    }


    /**
     * Удаление упражнения (trainer) из тренировки (train)
     *
     * @REST\Delete("/api/trains/{trainId}/trainers/{trainerId}")
     *
     * @SWG\Response(
     *     response=204,
     *     description="Train deleted"
     * )
     * @SWG\Parameter(
     *     name="trainId",
     *     in="path",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     description="Идентификатор тренировки"
     * )
     * @SWG\Parameter(
     *     name="trainerId",
     *     in="path",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     description="Идентификатор упражнения в тренировке"
     * )
     * @SWG\Tag(name="Упражнения в тренировке (UserTrainer)")
     * @ApiDoc\Security(name="Bearer")
     * @Rest\View()
     */
    public function deleteTrainerAction($trainId, $trainerId)
    {
        $user = $this->getUser();

        $train = $this->userTrainService->loadTrain($trainId);
        $this->userTrainService->checkAccessToTrain($train, $user);

        $trainer = $this->userTrainService->loadTrainer($trainerId, false);

        if ($trainer) {
            $this->userTrainService->checkIsTrainerInTrain($trainer, $train);
            $this->userTrainService->deleteTrainer($trainer);
        }
    }


}