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
}
