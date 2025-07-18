<?php

namespace App\Controller;

use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class StatisticController extends AbstractController
{
    private const LAST_DAYS = 30;

    #[Route('/statistic', name: 'app_statistic_index')]
    public function index(
        TicketRepository $ticketRepository,
        ChartBuilderInterface $chartBuilder,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SUPPORT');
        $statusBreakdown = $ticketRepository->findStatusBreakdown();

        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => array_keys($statusBreakdown),
            'datasets' => [
                [
                    'label' => 'Tickets by status',
                    'backgroundColor' => ['#6c757d', '#ffc107', '#28a745', '#dc3545'],
                    'data' => array_values($statusBreakdown),
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => '#212529',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'padding' => 10,
                    'cornerRadius' => 6,
                    'displayColors' => false,
                ],
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ]);

        return $this->render('statistic/index.html.twig', [
            'appTitle' => $this->getParameter('app_title'),
            'pageTitle' => $this->getParameter('app_title').' – Statistics',
            'boardTitle' => 'Statistics',
            'newTicketsCount' => $ticketRepository->countNewInLastDays(self::LAST_DAYS),
            'avgCloseTimeDays' => $ticketRepository->findAverageCloseTime().' days',
            'statusChart' => $chart,
        ]);
    }
}
