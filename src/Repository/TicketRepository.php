<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findByUser(User $user): array
    {
        $authorId = $user->getId();

        return $this->createQueryBuilder('t')
            ->andWhere('t.author = :authorId')
            ->setParameter('authorId', $authorId)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithFilters(array $filters = [], ?User $user = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'a')
            ->addSelect('a');

        if ($user) {
            $qb->andWhere('t.author = :currentUser')
                ->setParameter('currentUser', $user);
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $filters['status']);
        }

        if (!empty($filters['author'])) {
            $qb->andWhere('t.author = :author')
                ->setParameter('author', $filters['author']);
        }

        $qb->orderBy('t.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function countNewInLastDays(int $days): int
    {
        $dateCondition = (new \DateTime())->sub(new \DateInterval('P'.$days.'D'));
        $tickets = $this->createQueryBuilder('t')
            ->andWhere('t.createdAt >= :dateCondition')
            ->setParameter('dateCondition', $dateCondition)
            ->getQuery()
            ->getResult();

        return count($tickets);
    }

    public function findAverageCloseTime(): int
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('t.createdAt', 't.closedAt')
            ->where('t.closedAt IS NOT NULL');

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return 0;
        }

        $totalSeconds = 0;
        foreach ($results as $result) {
            $diffInSeconds = $result['closedAt']->getTimestamp() - $result['createdAt']->getTimestamp();
            $totalSeconds += $diffInSeconds;
        }

        $averageSeconds = (int) ($totalSeconds / count($results));

        if (0 === $averageSeconds) {
            return 0;
        }

        return 0 === (int) floor($averageSeconds / 86400) ? 1 : floor($averageSeconds / 86400);
    }

    public function findStatusBreakdown(): array
    {
        $result = [];
        $tickets = $this->findAll();
        foreach ($tickets as $ticket) {
            $statusLabel = $ticket->getStatus()->getLabel();
            if (isset($result[$statusLabel])) {
                ++$result[$statusLabel];
                continue;
            }
            $result[$statusLabel] = 1;
        }

        return $result;
    }
}
