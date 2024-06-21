<?php

namespace App\Controller\Api;

use App\Api\Manager\ChartLineManager;
use App\Api\Manager\SprintManager;
use App\Api\Preparer\CapacityDayChartPointFormPreparer;
use App\Api\Preparer\ChartLinePreparer;
use App\Api\Preparer\SprintChartLineFormPreparer;
use App\Api\Preparer\SprintDaysFormPreparer;
use App\Api\Preparer\SprintFormPreparer;
use App\Api\Preparer\SprintStoryFormPreparer;
use App\Entity\Api\Sprint;
use App\Entity\Api\SprintStory;
use App\Entity\CapacityDayChartPoint;
use App\Entity\ChartLine;
use App\Repository\Api\SprintExcludedDayRepository;
use App\Repository\Api\SprintRepository;
use App\Repository\ChartLineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/sprint")
 */
class SprintController extends AbstractController
{
    private SprintFormPreparer      $sprintFormPreparer;
    private SprintStoryFormPreparer $sprintStoryFormPreparer;
    private SprintDaysFormPreparer  $sprintDaysFormPreparer;

    private EntityManagerInterface $entityManager;
    private SprintManager          $sprintManager;

    private ChartLinePreparer                 $chartLinePreparer;
    private SprintChartLineFormPreparer       $chartLineFormPreparer;
    private ChartLineManager                  $chartLineManager;
    private ChartLineRepository               $chartLineRepository;
    private CapacityDayChartPointFormPreparer $capacityDayChartPointFormPreparer;

    public function __construct(
        SprintFormPreparer $sprintFormPreparer,
        SprintStoryFormPreparer $sprintStoryFormPreparer,
        SprintDaysFormPreparer $sprintDaysFormPreparer,
        EntityManagerInterface $entityManager,
        SprintManager $sprintManager,
        ChartLinePreparer $chartLinePreparer,
        SprintChartLineFormPreparer $chartLineFormPreparer,
        ChartLineManager $chartLineManager,
        ChartLineRepository $chartLineRepository,
        CapacityDayChartPointFormPreparer $capacityDayChartPointFormPreparer
    ) {
        $this->sprintFormPreparer                = $sprintFormPreparer;
        $this->sprintStoryFormPreparer           = $sprintStoryFormPreparer;
        $this->sprintDaysFormPreparer            = $sprintDaysFormPreparer;
        $this->entityManager                     = $entityManager;
        $this->sprintManager                     = $sprintManager;
        $this->chartLinePreparer                 = $chartLinePreparer;
        $this->chartLineFormPreparer             = $chartLineFormPreparer;
        $this->chartLineManager                  = $chartLineManager;
        $this->chartLineRepository               = $chartLineRepository;
        $this->capacityDayChartPointFormPreparer = $capacityDayChartPointFormPreparer;
    }

    /**
     * @Route("/", name="app_sprint_index", methods={"GET"})
     */
    public function index(SprintRepository $sprintRepository): Response
    {
        return $this->render('sprint/index.html.twig', [
            'sprints' => $sprintRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="api_sprint_new", methods={"POST"})
     */
    public function new(): Response
    {
        $form = $this->sprintFormPreparer->prepareForm($sprint = new Sprint(), ['csrf_protection' => false]);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->chartLineManager->updateCurrentChartLine($sprint);
            $this->chartLineManager->updatePerfectChartLine($sprint);

            return $this->json($sprint->toArray());
        }

        return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @Route("/{id}", name="app_sprint_show", methods={"GET"})
     */
    public function show(Sprint $sprint): Response
    {
        return $this->render('sprint/show.html.twig', [
            'sprint' => $sprint,
        ]);
    }

    /**
     * @Route("/{id}/update", name="api_sprint_update", methods={"POST"})
     */
    public function edit(Sprint $sprint): Response
    {
        $form = $this->sprintFormPreparer->prepareForm($sprint, ['csrf_protection' => false]);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->chartLineManager->updateCurrentChartLine($sprint);
            $this->chartLineManager->updatePerfectChartLine($sprint);
            $this->chartLineManager->updateAdditionalChartLines($sprint);

            return $this->json($sprint->toArray());
        }

        return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @Route("/{sprint}/remove", name="api_sprint_delete", methods={"POST"})
     */
    public function delete(Sprint $sprint): Response
    {
        $chartLines = $sprint->getChartLines()->toArray();

        $sprint->setChartLines(new ArrayCollection());
        $this->entityManager->persist($sprint);

        foreach ($chartLines as $chartLine) {
            $chartLine->setSprint(null);

            $this->entityManager->persist($chartLine);
        }

        $this->entityManager->flush();

        $this->entityManager->remove($sprint);

        foreach ($chartLines as $chartLine) {
            $this->entityManager->remove($chartLine);
        }

        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{sprint}/chart-point/add", name="add_chart_point", methods={"POST"},
     *                                              requirements={"sprintId"="\d+"})
     */
    public function newChartPoint(?Sprint $sprint): Response
    {
        $sprint->addSprintStory($sprintStory = new SprintStory());

        $form = $this->sprintStoryFormPreparer->prepareForm($sprintStory, ['csrf_protection' => false]);

        if ($form->isSubmitted() && $form->isValid()) {
            $chartLine = $sprint->getCurrentChartLine() ?? new ChartLine();

            $sprint->setCurrentChartLine($this->chartLinePreparer->prepareChartLine($sprint, $chartLine));

            $this->entityManager->persist($sprint);
            $this->entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @Route("/{sprintId}/exclude-days", name="exclude_days", methods={"POST"},
     *                                              requirements={"sprintId"="\d+"})
     */
    public function excludeDays(?int $sprintId): Response
    {
        $sprint = $this->sprintManager->getSprintByIdOrCurrent($sprintId);

        $form = $this->sprintDaysFormPreparer->prepareForm($sprint, ['csrf_protection' => false]);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->chartLineManager->updatePerfectChartLine($sprint);
            $this->chartLineManager->updateAdditionalChartLines($sprint);

            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{sprintId}/exclude-days/remove", name="delete_excluded_days", methods={"POST"},
     *                                              requirements={"sprintId"="\d+"})
     */
    public function deleteExcludedDays(?int $sprintId, SprintExcludedDayRepository $excludedDayRepository): Response
    {
        $sprint = $this->sprintManager->getSprintByIdOrCurrent($sprintId);

        $excludedDays = $excludedDayRepository->findBy(['sprint' => $sprintId]);

        foreach ($excludedDays as $excludedDay) {
            $this->entityManager->remove($excludedDay);
        }
        $this->entityManager->flush();

        $this->chartLineManager->updatePerfectChartLine($sprint);
        $this->chartLineManager->updateAdditionalChartLines($sprint);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{sprintId}/chart-line/new", name="api_sprint_chart-line_new", methods={"POST"})
     */
    public function newChartLine(?int $sprintId): Response
    {
        $sprint    = $this->sprintManager->getSprintByIdOrCurrent($sprintId);
        $chartLine = new ChartLine();
        $chartLine->setSprint($sprint);
        $form = $this->chartLineFormPreparer->prepareForm($chartLine, ['csrf_protection' => false]);

        if ($form->isSubmitted() && $form->isValid()) {
            $chartLine = $this->chartLinePreparer->preparePerfectChartLine($sprint, $chartLine);

            $sprint->addChartLine($chartLine);

            $this->entityManager->persist($sprint);
            $this->entityManager->persist($chartLine);
            $this->entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @Route("/chart-line/{id}/remove", name="api_sprint_chart-line_delete", methods={"POST"})
     */
    public function deleteChartLine(?int $id, ChartLineRepository $chartLineRepository): Response
    {
        $this->entityManager->remove($chartLineRepository->find($id));
        $this->entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{sprintId}/capacity/{chartLineId}/add", name="update_capacity", methods={"POST"},
     *                                              requirements={"sprintId"="\d+"})
     */
    public function addCapacityDay(?int $sprintId, ?int $chartLineId): Response
    {
        $chartLine = $this->chartLineRepository->find($chartLineId);

        $capacityDay = new CapacityDayChartPoint();
        $capacityDay->setChartLine($chartLine);

        $form = $this->capacityDayChartPointFormPreparer->prepareForm($capacityDay, ['csrf_protection' => false]);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($existingDay = $chartLine->isDayUpdated($capacityDay->getDate())) {
                $existingDay->setValue($capacityDay->getValue());
                $capacityDay = $existingDay;
            }

            $this->entityManager->persist($capacityDay);
            $this->entityManager->flush();

            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
