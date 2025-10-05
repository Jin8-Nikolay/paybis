<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rate;
use App\Enum\CryptoPair;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function findRates(CryptoPair $pair, ?DateTimeInterface $start = null, ?DateTimeInterface $end = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.pair = :pair')
            ->setParameter('pair', $pair->value)
            ->orderBy('r.createdAt', 'ASC');

        if ($start) $qb->andWhere('r.createdAt >= :start')->setParameter('start', $start);
        if ($end) $qb->andWhere('r.createdAt <= :end')->setParameter('end', $end);

        return $qb->getQuery()->getResult();
    }
}
