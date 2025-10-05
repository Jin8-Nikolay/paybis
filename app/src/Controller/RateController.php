<?php

namespace App\Controller;

use App\Enum\CryptoPair;
use App\Repository\RateRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use ValueError;

#[Route('/api/rates')]
class RateController extends AbstractController
{
    private RateRepository $rateRepository;

    public function __construct(RateRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;
    }

    #[Route('/last-24h', name: 'rates_last_24h', methods: ['GET'])]
    public function last24h(Request $request): JsonResponse
    {
        try {
            $pairEnum = CryptoPair::from(strtoupper($request->query->get('pair', CryptoPair::BTCEUR->value)));
        } catch (ValueError) {
            return $this->json(['error' => 'Invalid pair'], 400);
        }

        $end = new DateTimeImmutable();
        $start = $end->modify('-24 hours');

        return $this->json(
            $this->rateRepository->findRates($pairEnum, $start, $end),
            200,
            [],
            ['groups' => 'api', 'datetime_format' => 'Y-m-d H:i:s']
        );
    }

    #[Route('/day', name: 'rates_day', methods: ['GET'])]
    public function byDay(Request $request): JsonResponse
    {
        try {
            $pairEnum = CryptoPair::from(strtoupper($request->query->get('pair', CryptoPair::BTCEUR->value)));
        } catch (ValueError) {
            return $this->json(['error' => 'Invalid pair'], 400);
        }

        $dateStr = $request->query->get('date', date('Y-m-d'));

        try {
            $day = new DateTimeImmutable($dateStr);
        } catch (Exception) {
            return $this->json(['error' => 'Invalid date format. Use YYYY-MM-DD'], 400);
        }

        $start = (clone $day)->setTime(0, 0);
        $end = (clone $day)->setTime(23, 59, 59);

        return $this->json(
            $this->rateRepository->findRates($pairEnum, $start, $end),
            200,
            [],
            ['groups' => 'api', 'datetime_format' => 'Y-m-d H:i:s']
        );
    }
}
