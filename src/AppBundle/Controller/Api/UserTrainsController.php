<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\UserTrain;
use AppBundle\Exceptions\FormValidationException;
use AppBundle\Service\UserTrainService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Rest\NamePrefix("api_")
 */
class UserTrainsController extends FOSRestController
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
     * Список тренировок пользователя
     *
     * Список содержит объекты тренировок (UserTrain) указанного пользователя.
     * Объект тренировки содержит список выполненных упражнений (UserTrainer), которые в свою очередь содержат подходы (UserTrainerSet).
     * В текущей версии есть доступ только к своим собственным тренировкам (userid должно быть равно id текущего пользователя).
     *
     * @REST\Get("/api/users/{userId}/trains")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns list of user trainings",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@ApiDoc\Model(type=UserTrain::class))
     *     )
     * )
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Идентификатор пользователя"
     * )
     * @SWG\Parameter(
     *     name="fromTime",
     *     in="query",
     *     required=false,
     *     type="integer",
     *     format="timestamp",
     *     description="Возвращает список тренировок, у которых create time меньше указанного fromTime. Формат времени timestamp."
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     required=false,
     *     type="integer",
     *     description="Количество возвращаемых записей"
     * )
     * @SWG\Tag(name="Тренировки (UserTrain)")
     * @ApiDoc\Security(name="Bearer")
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * @Rest\QueryParam(name="fromTime", requirements="\d+", default="0")
     * @Rest\QueryParam(name="limit", requirements="\d{1,2}", default="20")
     */
    public function getTrainsAction($userId, $fromTime, $limit)
    {
        $user = $this->getUser();
        if ($user->getId() != $userId) {
            throw new AccessDeniedException;
        }

        return $this->userTrainService->getUserTrains($user, $limit, $fromTime);
    }


    /**
     * Информация о тренировке
     *
     * @REST\Get("/api/users/{userId}/trains/{trainId}")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns user training",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=UserTrain::class))
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Access to this train denied"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Train not found"
     * )
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Идентификатор пользователя"
     * )
     * @SWG\Parameter(
     *     name="trainId",
     *     in="path",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     description="Идентификатор тренировки"
     * )
     * @SWG\Tag(name="Тренировки (UserTrain)")
     * @ApiDoc\Security(name="Bearer")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function getTrainAction($userId, $trainId)
    {
        $user = $this->getUser();
        if ($user->getId() != $userId) {
            throw new AccessDeniedException;
        }

        $train = $this->userTrainService->loadTrain($trainId);
        $this->userTrainService->checkAccessToTrain($train, $user);

        return $train;
    }


    /**
     * Сохранить тренировку
     *
     * @REST\Put("/api/users/{userId}/trains/{trainId}")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Train saved",
     *     @SWG\Schema(ref=@ApiDoc\Model(type=UserTrain::class))
     * )
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Идентификатор пользователя"
     * )
     * @SWG\Parameter(
     *     name="trainId",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Идентификатор тренировки"
     * )
     * @SWG\Parameter(
     *     name="train",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(ref=@ApiDoc\Model(type=UserTrain::class,groups={"train_create"}))
     * )
     * @SWG\Tag(name="Тренировки (UserTrain)")
     * @ApiDoc\Security(name="Bearer")
     * @Rest\View()
     */
    public function putTrainsAction($userId, $trainId, Request $request)
    {
        $user = $this->getUser();
        if ($user->getId() != $userId) {
            throw new AccessDeniedException;
        }

        $train = $this->userTrainService->loadOrCreateTrain($trainId, $user);

        try {
            $data = json_decode($request->getContent(), true);
            $this->userTrainService->processTrainForm($data, $train);
        } catch (FormValidationException $ex) {
            return $ex->getForm();
        }

        return $train;
    }


    /**
     * Удаление тренировки
     *
     * @REST\Delete("/api/users/{userId}/trains/{trainId}")
     *
     * @SWG\Response(
     *     response=204,
     *     description="Train deleted"
     * )
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Идентификатор пользователя"
     * )
     * @SWG\Parameter(
     *     name="trainId",
     *     in="path",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     description="Идентификатор тренировки"
     * )
     *
     * @SWG\Tag(name="Тренировки (UserTrain)")
     * @ApiDoc\Security(name="Bearer")
     * @Rest\View()
     */
    public function deleteTrainAction($userId, $trainId)
    {
        $user = $this->getUser();
        if ($user->getId() != $userId) {
            throw new AccessDeniedException;
        }

        $train = $this->userTrainService->loadTrain($trainId, false);

        if ($train) {
            $this->userTrainService->checkAccessToTrain($train, $user);
            $this->userTrainService->deleteTrain($train);
        }
    }
}