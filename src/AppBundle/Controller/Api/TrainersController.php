<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Trainer;
use AppBundle\Service\TrainerService;
use AppBundle\Util\PaginationHelper;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\NamePrefix("api_")
 */
class TrainersController extends FOSRestController
{
    use PaginationHelper;

    /**
     * @var TrainerService
     */
    private $trainerService;

    public function __construct(TrainerService $trainerService)
    {
        $this->trainerService = $trainerService;
    }

    /**
     * Список тренажеров
     *
     * Данный список используется в качестве справочной информации.
     *
     * @Rest\Get("/api/trainers")
     * @Rest\View()
     * @Rest\QueryParam(name="_page", requirements="\d+", default="1")
     * @Rest\QueryParam(name="_perPage", requirements="\d{1,3}", default="30")
     * @Rest\QueryParam(name="_sortDir", requirements="(asc|ASC|desc|DESC)", default="DESC")
     * @Rest\QueryParam(name="_sortField")
     * @Rest\QueryParam(name="q")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Список названий тренажеров",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@ApiDoc\Model(type=Trainer::class,groups={"trainer_list"}))
     *     )
     * )
     * @SWG\Tag(name="Справочник тренажеров (Trainer)")
     *
     * @param $_page
     * @param $_perPage
     * @param $_sortField
     * @param $_sortDir
     * @param $q string - Fulltext search filter
     * @return Response
     */
    public function getTrainers($_page, $_perPage, $_sortField, $_sortDir, $q)
    {
        $queryBuilder = $this->trainerService->loadTrainersQueryBuilder($q, $_sortField, $_sortDir);
        $result = $this->getPaginatedResult($queryBuilder, $_page, $_perPage);

        $context = new Context();
        $context->addGroup('trainer_list');

        $view = $this->view($result->items, 200);
        $view->setContext($context);
        $view->setHeader("X-Total-Count", $result->total);
        return $this->handleView($view);
    }


    /**
     * Просмотр информации о тренажере
     *
     * @Rest\Get("/api/trainers/{id}")
     * @Rest\View
     *
     * @SWG\Response(
     *     response=200,
     *     description="Информация о тренажере",
     *     @SWG\Schema(
     *      ref=@ApiDoc\Model(type=Trainer::class,groups={"trainer_view"})
     *      )
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Идентификатор тренажера"
     * )
     * @SWG\Tag(name="Справочник тренажеров (Trainer)")
     */
    public function getAction($id)
    {
        $object = $this->trainerService->loadTrainer($id);

        $context = new Context();
        $context->addGroup('trainer_view');

        $view = $this->view($object, 200);
        $view->setContext($context);
        return $this->handleView($view);
    }

}