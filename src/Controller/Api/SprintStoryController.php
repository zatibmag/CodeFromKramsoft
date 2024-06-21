<?php

namespace App\Controller\Api;

use App\Api\Manager\SprintManager;
use App\Api\Preparer\ChartLinePreparer;
use App\Entity\Api\Sprint;
use App\Entity\ChartLine;
use App\Entity\ChartPoint;
use App\Repository\Api\SprintRepository;
use App\Repository\ChartLineRepository;
use App\Serializer\Api\ArrayConvertibleDataSerializer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/sprint")
 */
class SprintStoryController extends AbstractController
{
    private SprintRepository               $sprintRepository;
    private ArrayConvertibleDataSerializer $serializer;
    private SprintManager                  $sprintManager;
    private ChartLinePreparer              $preparer;

    public function __construct(
        SprintRepository $sprintRepository,
        ChartLinePreparer $preparer,
        ArrayConvertibleDataSerializer $serializer,
        SprintManager $sprintManager
    ) {
        $this->sprintRepository = $sprintRepository;
        $this->preparer         = $preparer;
        $this->serializer       = $serializer;
        $this->sprintManager    = $sprintManager;
    }

    /**
     * @Route("/list", name="get_sprints", methods={"POST"})
     */
    public function index(Request $request): JsonResponse
    {
        $limit  = $request->query->getInt('limit', 5);
        $offset = $request->query->getInt('offset', 0);

        $sprints = $this->sprintRepository->findBy([], ['id' => 'DESC'], $limit, $offset);
        $sprints = array_map(fn($sprint) => $sprint->toArray(), $sprints);

        $sprintsNumber = $this->sprintRepository->count([]);

        return $this->json([
            'sprints'       => $sprints,
            'sprintsNumber' => $sprintsNumber,
        ]);
    }

    /**
     * @Route("/current/chart-line/perfect", name="current_perfect_chart_line", methods={"POST"})
     * @Route("/{sprintId}/chart-line/perfect", name="perfect_chart_line", methods={"POST"},
     *                                                 requirements={"sprintId"="\d+"})
     */
    public function perfectChartLine(?int $sprintId): Response
    {
        $sprint           = $this->sprintManager->getSprintByIdOrCurrent($sprintId);
        $perfectChartLine = $sprint->getPerfectChartLine();

        if (!!$perfectChartLine->getCapacityDayChartPoints()->count()) {
            $perfectChartLine = $this->preparer->prepareCustomChartLine($sprint, $perfectChartLine);
        }
        $perfectChartLine->sortChartLine();

        // Serializer already returns JSON
        return new Response($this->serializer->serialize($perfectChartLine));
    }

    /**
     * @Route("/current/chart-line/current", name="current_chart_line", methods={"POST"})
     * @Route("/{sprintId}/chart-line/current", name="chart_line", methods={"POST"},
     *                                                 requirements={"sprintId"="\d+"})
     */
    public function currentChartLine(?int $sprintId): Response
    {
        $sprint = $this->sprintManager->getSprintByIdOrCurrent($sprintId);

        $currentLine = $sprint->getCurrentChartLine();

        // Serializer already returns JSON
        return new Response($this->serializer->serialize($currentLine));
    }

    /**
     * @Route("/current/data", name="sprint_current_data", methods={"POST"})
     * @Route("/{sprintId}/data", name="sprint_data", methods={"POST"}, requirements={"sprintId"="\d+"})
     */
    public function sprintData(?int $sprintId): Response
    {
        $sprint = $this->sprintManager->getSprintByIdOrCurrent($sprintId);

        // Serializer already returns JSON
        return new Response($this->serializer->serialize($sprint));
    }

    /**
     * @Route("/{sprint}/chart-lines", name="chart_lines", methods={"POST"},
     *                                                 requirements={"sprintId"="\d+"})
     */
    public function chartLines(?Sprint $sprint): Response
    {
        if (!$sprint) {
            throw new NotFoundHttpException();
        }

        $preparedChartLines = array_map(function (ChartLine $chartLine) use ($sprint) {
            if (!!$chartLine->getCapacityDayChartPoints()->count()) {
                $chartLine = $this->preparer->prepareCustomChartLine($sprint, $chartLine);

                return $chartLine->getSortedArrayByDate();
            }

            return $chartLine->getSortedArrayByDate();
        }, $sprint->getChartLines()->toArray());

        return $this->json($preparedChartLines);
    }
}
